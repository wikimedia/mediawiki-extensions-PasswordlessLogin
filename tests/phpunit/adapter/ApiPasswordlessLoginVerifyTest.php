<?php

namespace PasswordlessLogin\adapter;

use ApiTestCase;
use MediaWiki\MediaWikiServices;
use PasswordlessLogin\model\Challenge;
use PasswordlessLogin\model\ChallengesRepository;
use PasswordlessLogin\model\Device;
use PasswordlessLogin\model\DevicesRepository;
use User;

/**
 * @group API
 * @group Database
 * @group medium
 *
 * @covers \PasswordlessLogin\adapter\ApiPasswordlessLogin
 */
class ApiPasswordlessLoginVerifyTest extends ApiTestCase {
	/** @var ChallengesRepository */
	private $challengesRepository;
	/** @var DevicesRepository */
	private $devicesRepository;

	protected function setUp() {
		parent::setUp();
		$this->challengesRepository =
			MediaWikiServices::getInstance()->getService( ChallengesRepository::SERVICE_NAME );
		$this->devicesRepository =
			MediaWikiServices::getInstance()->getService( DevicesRepository::SERVICE_NAME );

		$this->db->delete( 'passwordlesslogin_devices', '*' );
		$this->db->delete( 'passwordlesslogin_challenges', '*' );
	}

	public function testNoChallenge() {
		$result = $this->doApiRequest( [
			'action' => 'passwordlesslogin-verify',
			'challenge' => 'invalid',
			'response' => 'A_DEVICE_ID',
		] );

		$this->assertEquals( 'Failed', $result[0]['verify']['result'] );
	}

	public function testNoSecret() {
		$user = User::newFromName( 'UTSysop' );
		$challenge = Challenge::forUser( $user );
		$this->challengesRepository->save( $challenge );
		$device = Device::forUser( $user );
		$this->devicesRepository->save( $device );

		$result = $this->doApiRequest( [
			'action' => 'passwordlesslogin-verify',
			'challenge' => $challenge->getChallenge(),
			'response' => 'A_RESPONSE',
		] );

		$this->assertEquals( 'Failed', $result[0]['verify']['result'] );
	}

	public function testInvalidResponse() {
		$user = User::newFromName( 'UTSysop' );
		$challenge = Challenge::forUser( $user );
		$this->challengesRepository->save( $challenge );
		$device = Device::forUser( $user );
		$device->setSecret( "A_SECRET" );
		$device->confirm();
		$this->devicesRepository->save( $device );

		$result = $this->doApiRequest( [
			'action' => 'passwordlesslogin-verify',
			'challenge' => $challenge->getChallenge(),
			'response' => 'invalid',
		] );

		$this->assertEquals( 'Failed', $result[0]['verify']['result'] );
	}

	public function testValidResponse() {
		$user = User::newFromName( 'UTSysop' );
		$challenge = Challenge::forUser( $user );
		$this->challengesRepository->save( $challenge );
		$device = Device::forUser( $user );
		$device->setSecret( "A_SECRET" );
		$device->confirm();
		$this->devicesRepository->save( $device );

		$result = $this->doApiRequest( [
			'action' => 'passwordlesslogin-verify',
			'challenge' => $challenge->getChallenge(),
			'response' => hash_hmac( "sha512", $challenge->getChallenge(), $device->getSecret() ),
		] );

		$this->assertEquals( 'Success', $result[0]['verify']['result'] );
	}
}
