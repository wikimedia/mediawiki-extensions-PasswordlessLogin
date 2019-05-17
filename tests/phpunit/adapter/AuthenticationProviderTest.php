<?php

namespace PasswordlessLogin\adapter;

use MediaWiki\Auth\AuthenticationRequest;
use MediaWiki\Auth\AuthenticationResponse;
use MediaWiki\Auth\AuthManager;
use MediaWiki\Auth\PrimaryAuthenticationProvider;
use MediaWikiTestCase;
use PasswordlessLogin\model\Device;
use PasswordlessLogin\model\DevicesRepository;
use PasswordlessLogin\model\LinkRequest;
use PasswordlessLogin\model\QRCodeRequest;
use PasswordlessLogin\model\RemoveRequest;
use RawMessage;
use StatusValue;
use User;

class PasswordlessLoginPrimaryAuthenticationProviderTest extends MediaWikiTestCase {
	/**
	 * @var FakeDevicesRepository
	 */
	private $devicesRepository;

	protected function setUp() {
		parent::setUp();

		$this->devicesRepository = new FakeDevicesRepository();
		$this->setService( DevicesRepository::SERVICE_NAME, $this->devicesRepository );
	}

	/**
	 * @covers \PasswordlessLogin\adapter\AuthenticationProvider::accountCreationType
	 */
	public function testAccountCreationType() {
		$provider = new AuthenticationProvider();

		$this->assertEquals( PrimaryAuthenticationProvider::TYPE_LINK,
			$provider->accountCreationType() );
	}

	/**
	 * @covers \PasswordlessLogin\adapter\AuthenticationProvider::getAuthenticationRequests
	 */
	public function testGetAuthenticationRequestsLink() {
		$provider = new AuthenticationProvider();

		$this->assertEquals( [ new LinkRequest() ],
			$provider->getAuthenticationRequests( AuthManager::ACTION_LINK, [] ) );
	}

	/**
	 * @covers \PasswordlessLogin\adapter\AuthenticationProvider::getAuthenticationRequests
	 */
	public function testGetAuthenticationRequestsRemove() {
		$provider = new AuthenticationProvider();

		$this->assertEquals( [ new RemoveRequest() ],
			$provider->getAuthenticationRequests( AuthManager::ACTION_REMOVE, [] ) );
	}

	/**
	 * @covers \PasswordlessLogin\adapter\AuthenticationProvider::beginPrimaryAuthentication
	 */
	public function testBeginPrimaryAuthentication() {
		$provider = new AuthenticationProvider();

		$this->assertEquals( AuthenticationResponse::newAbstain(),
			$provider->beginPrimaryAuthentication( [] ) );
	}

	/**
	 * @covers \PasswordlessLogin\adapter\AuthenticationProvider::testUserExists
	 */
	public function testTestUserExists() {
		$provider = new AuthenticationProvider();

		$this->assertEquals( false, $provider->testUserExists( 'A_USERNAME' ) );
	}

	/**
	 * @covers \PasswordlessLogin\adapter\AuthenticationProvider::providerAllowsAuthenticationDataChange
	 */
	public function testProviderAllowsAuthenticationDataChangeNonPasswordlessLogin() {
		$provider = new AuthenticationProvider();

		$result =
			$provider->providerAllowsAuthenticationDataChange( $this->createMock( AuthenticationRequest::class ) );

		$this->assertEquals( StatusValue::newGood( 'ignored' ), $result );
	}

	/**
	 * @covers \PasswordlessLogin\adapter\AuthenticationProvider::testUserExists
	 */
	public function testProviderAllowsAuthenticationDataChangeRemoveRequest() {
		$provider = new AuthenticationProvider();
		$this->devicesRepository->byUserId = new Device( '1' );
		$removeRequest = new RemoveRequest();
		$removeRequest->action = AuthManager::ACTION_REMOVE;
		$removeRequest->username = 'UTSysop';

		$result = $provider->providerAllowsAuthenticationDataChange( $removeRequest );

		$this->assertEquals( StatusValue::newGood(), $result );
	}

	/**
	 * @covers \PasswordlessLogin\adapter\AuthenticationProvider::testUserExists
	 */
	public function testProviderAllowsAuthenticationDataChangeLinkRemoveRequest() {
		$provider = new AuthenticationProvider();
		$removeRequest = new RemoveRequest();
		$removeRequest->action = AuthManager::ACTION_LINK;

		$result = $provider->providerAllowsAuthenticationDataChange( $removeRequest );

		$this->assertEquals( StatusValue::newFatal( 'passwordlesslogin-error' ), $result );
	}

	/**
	 * @covers \PasswordlessLogin\adapter\AuthenticationProvider::testUserExists
	 */
	public function testProviderAllowsAuthenticationDataChangeNoData() {
		$provider = new AuthenticationProvider();
		$this->devicesRepository->byUserId = null;
		$removeRequest = new RemoveRequest();
		$removeRequest->action = AuthManager::ACTION_REMOVE;
		$removeRequest->username = 'UTSysop';

		$result = $provider->providerAllowsAuthenticationDataChange( $removeRequest );

		$this->assertEquals( StatusValue::newFatal( 'passwordlesslogin-no-data' ), $result );
	}

	/**
	 * @covers \PasswordlessLogin\adapter\AuthenticationProvider::providerChangeAuthenticationData
	 */
	public function testProviderChangeAuthenticationDataNoRemoveRequest() {
		$provider = new AuthenticationProvider();
		$request = new RemoveRequest();
		$request->action = AuthManager::ACTION_LINK;
		$request->username = 'UTSysop';

		$provider->providerChangeAuthenticationData( $request );

		$this->assertEquals( null, $this->devicesRepository->removedFor );
	}

	/**
	 * @covers \PasswordlessLogin\adapter\AuthenticationProvider::providerChangeAuthenticationData
	 */
	public function testProviderChangeAuthenticationDataRemoveRequest() {
		$provider = new AuthenticationProvider();
		$request = new RemoveRequest();
		$request->action = AuthManager::ACTION_REMOVE;
		$request->username = 'UTSysop';

		$provider->providerChangeAuthenticationData( $request );

		$this->assertEquals( User::newFromName( 'UTSysop' ), $this->devicesRepository->removedFor );
	}

	/**
	 * @covers \PasswordlessLogin\adapter\AuthenticationProvider::providerChangeAuthenticationData
	 */
	public function testProviderChangeAuthenticationDataNoRemove() {
		$provider = new AuthenticationProvider();

		$provider->providerAllowsAuthenticationDataChange( $this->createMock( AuthenticationRequest::class ) );

		$this->assertEquals( null, $this->devicesRepository->removedFor );
	}

	/**
	 * @covers \PasswordlessLogin\adapter\AuthenticationProvider::beginPrimaryAccountLink
	 */
	public function testBeginPrimaryAccountLinkReturnsUI() {
		$provider = new AuthenticationProvider();

		$result =
			$provider->beginPrimaryAccountLink( User::newFromName( 'UTSysop' ),
				[ new LinkRequest() ] );

		$authenticationResponse =
			AuthenticationResponse::newUI( [ new QRCodeRequest( '' ) ],
				new RawMessage( 'Pair device' ) );
		$this->assertEquals( $authenticationResponse->status, $result->status );
		$this->assertInstanceOf( QRCodeRequest::class, $result->neededRequests[0] );
		$this->assertNotNull( $this->devicesRepository->savedDevice );
	}
}

class FakeDevicesRepository implements DevicesRepository {
	public $byUserId = null;
	public $removedFor = null;
	public $savedDevice = null;

	function findByUserId( $userId ) {
		return $this->byUserId;
	}

	function remove( User $user ) {
		$this->removedFor = $user;
	}

	function save( Device $device ) {
		$this->savedDevice = $device;
	}
}
