<?php

namespace PasswordlessLogin\adapter;

use MediaWiki\Auth\AbstractPrimaryAuthenticationProvider;
use MediaWiki\Auth\AuthenticationRequest;
use MediaWiki\Auth\AuthenticationResponse;
use MediaWiki\Auth\AuthManager;
use MediaWiki\MediaWikiServices;
use PasswordlessLogin\model\Challenge;
use PasswordlessLogin\model\ChallengesRepository;
use PasswordlessLogin\model\Device;
use PasswordlessLogin\model\DevicesRepository;
use PasswordlessLogin\model\LinkRequest;
use PasswordlessLogin\model\LoginRequest;
use PasswordlessLogin\model\QRCodeRequest;
use PasswordlessLogin\model\RemoveRequest;
use PasswordlessLogin\model\VerifyRequest;
use RawMessage;
use StatusValue;
use User;

class AuthenticationProvider extends AbstractPrimaryAuthenticationProvider {
	/** @var DevicesRepository */
	private $devicesRepository;
	/** @var ChallengesRepository */
	private $challengesRepository;
	/** @var FirebaseMessageSender */
	private $firebaseMessageSender;

	public function __construct() {
		$mediaWikiServices = MediaWikiServices::getInstance();
		$this->devicesRepository =
			$mediaWikiServices->getService( DevicesRepository::SERVICE_NAME );
		$this->challengesRepository =
			$mediaWikiServices->getService( ChallengesRepository::SERVICE_NAME );
		$this->firebaseMessageSender =
			$mediaWikiServices->getService( FirebaseMessageSender::SERVICE_NAME );
	}

	public function getAuthenticationRequests( $action, array $options ) {
		if ( $action === AuthManager::ACTION_REMOVE ) {
			return [ new RemoveRequest() ];
		}
		if ( $action === AuthManager::ACTION_LINK ) {
			return [ new LinkRequest() ];
		}
		if ( $action === AuthManager::ACTION_LOGIN ) {
			return [ new LoginRequest() ];
		}

		return [];
	}

	public function beginPrimaryAuthentication( array $reqs ) {
		$request = AuthenticationRequest::getRequestByClass( $reqs, LoginRequest::class );
		if ( $request === null ) {
			return AuthenticationResponse::newAbstain();
		}

		$user = User::newFromName( $request->username );
		$device = $this->devicesRepository->findByUserId( $user->getId() );
		if ( $device == null ) {
			return AuthenticationResponse::newAbstain();
		}
		$this->challengesRepository->remove( $user );
		$challenge = Challenge::forUser( $user );
		$this->challengesRepository->save( $challenge );
		$this->firebaseMessageSender->send( $device, $challenge );

		return AuthenticationResponse::newUI( [ new VerifyRequest() ],
			new RawMessage( 'Please verify your login on your phone.' ) );
	}

	public function beginPrimaryAccountLink( $user, array $reqs ) {
		$device = Device::forUser( $user );
		$this->devicesRepository->save( $device );

		return AuthenticationResponse::newUI( [ new QRCodeRequest( $device->getPairToken() ) ],
			new RawMessage( 'Pair device' ) );
	}

	public function continuePrimaryAuthentication( array $reqs ) {
		/** @var VerifyRequest $request */
		$request = AuthenticationRequest::getRequestByClass( $reqs, VerifyRequest::class );
		if ( $request === null ) {
			return AuthenticationResponse::newFail( wfMessage( 'passwordlesslogin-error-no-authentication-workflow' ) );
		}
		$user = User::newFromName( $request->username );
		$challenge = $this->challengesRepository->findByUser( $user );
		if ( $challenge === null ) {
			return AuthenticationResponse::newFail( wfMessage( 'passwordlesslogin-no-challenge' ) );
		}
		if ( $challenge->getSuccess() === false ) {
			return AuthenticationResponse::newUI( [ new VerifyRequest() ],
				new RawMessage( 'Login not verified, yet.' ) );
		}

		$this->challengesRepository->remove( $user );

		return AuthenticationResponse::newPass( $user->getName() );
	}

	public function testUserExists( $username, $flags = User::READ_NORMAL ) {
		return false;
	}

	public function providerAllowsAuthenticationDataChange(
		AuthenticationRequest $req, $checkData = true
	) {
		if ( get_class( $req ) !== RemoveRequest::class ) {
			return StatusValue::newGood( 'ignored' );
		}

		if ( $req->action !== AuthManager::ACTION_REMOVE ) {
			return StatusValue::newFatal( 'passwordlesslogin-error' );
		}

		$user = User::newFromName( $req->username );
		if ( $user === false || $this->devicesRepository->findByUserId( $user->getId() ) == null ) {
			return StatusValue::newFatal( 'passwordlesslogin-no-data' );
		}

		return StatusValue::newGood();
	}

	public function providerChangeAuthenticationData( AuthenticationRequest $req ) {
		if ( get_class( $req ) !== RemoveRequest::class ) {
			return;
		}
		if ( $req->action !== AuthManager::ACTION_REMOVE ) {
			return;
		}
		$user = User::newFromName( $req->username );
		if ( $user === false ) {
			return;
		}
		$this->devicesRepository->remove( $user );
	}

	public function accountCreationType() {
		return self::TYPE_LINK;
	}

	public function beginPrimaryAccountCreation( $user, $creator, array $reqs ) {
		return AuthenticationResponse::newAbstain();
	}
}
