<?php

namespace PasswordlessLogin\adapter;

use MediaWiki\Auth\AbstractPrimaryAuthenticationProvider;
use MediaWiki\Auth\AuthenticationRequest;
use MediaWiki\Auth\AuthenticationResponse;
use MediaWiki\Auth\AuthManager;
use MediaWiki\MediaWikiServices;
use PasswordlessLogin\model\DevicesRepository;
use PasswordlessLogin\model\LinkRequest;
use PasswordlessLogin\model\RemoveRequest;
use StatusValue;
use User;

class AuthenticationProvider extends AbstractPrimaryAuthenticationProvider {
	private $devicesRepository;

	public function __construct( DevicesRepository $devicesRepository = null ) {
		if ( $devicesRepository === null ) {
			$devicesRepository =
				MediaWikiServices::getInstance()->getService( DevicesRepository::SERVICE_NAME );
		}
		$this->devicesRepository = $devicesRepository;
	}

	public function getAuthenticationRequests( $action, array $options ) {
		if ( $action === AuthManager::ACTION_REMOVE ) {
			return [ new RemoveRequest() ];
		}
		if ( $action === AuthManager::ACTION_LINK ) {
			return [ new LinkRequest() ];
		}

		return [];
	}

	public function beginPrimaryAuthentication( array $reqs ) {
		return AuthenticationResponse::newAbstain();
	}

	public function testUserExists( $username, $flags = User::READ_NORMAL ) {
		return false;
	}

	public function providerAllowsAuthenticationDataChange(
		AuthenticationRequest $req, $checkData = true
	) {
		if ( get_class( $req ) == RemoveRequest::class ) {
			if ( $req->action !== AuthManager::ACTION_REMOVE ) {
				return StatusValue::newFatal( 'passwordlesslogin-error' );
			}
			$user = User::newFromName( $req->username );
			if ( $user === false ||
				$this->devicesRepository->findByUserId( $user->getId() ) == null ) {
				return StatusValue::newFatal( 'passwordlesslogin-no-data' );
			}

			return StatusValue::newGood();
		}

		return StatusValue::newGood( 'ignored' );
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
