<?php

namespace PasswordlessLogin\adapter\api;

use ApiTestCase;
use PasswordlessLogin\adapter\AuthenticationProvider;
use PasswordlessLogin\model\Challenge;
use PasswordlessLogin\model\ChallengesRepository;
use User;

/**
 * @group API
 * @group Database
 * @group medium
 *
 * @covers \PasswordlessLogin\adapter\api\LoginVerification
 */
class LoginVerificationTest extends ApiTestCase {
	/** @var ChallengesRepository */
	private $challengesRepository;

	protected function setUp(): void {
		parent::setUp();
		$this->challengesRepository =
			$this->getServiceContainer()->getService( ChallengesRepository::SERVICE_NAME );

		$this->getDB()->delete( 'passwordlesslogin_challenges', '*' );
	}

	public function testNoChallenge() {
		$result = $this->doApiRequest( [
			'action' => 'passwordlesslogin-verify-login',
		] );

		$this->assertEquals( 'Failed', $result[0]['login-verification']['result'] );
	}

	public function testSolvedChallenge() {
		$user = User::newFromName( 'UTSysop' );
		$challenge = Challenge::forUser( $user );
		$challenge->setSuccess( true );
		$this->challengesRepository->save( $challenge );

		$result = $this->doApiRequest( [
			'action' => 'passwordlesslogin-verify-login',
		], [
			AuthenticationProvider::CHALLENGE_SESSION_KEY => $challenge->getChallenge()
		] );

		$this->assertEquals( 'Success', $result[0]['login-verification']['result'] );
	}

	public function testOpenChallenge() {
		$authManager = $this->getServiceContainer()->getAuthManager();
		$authManager
			->setAuthenticationSessionData( AuthenticationProvider::CHALLENGE_SESSION_KEY,
				'UTSysop' );
		$user = User::newFromName( 'UTSysop' );
		$challenge = Challenge::forUser( $user );
		$this->challengesRepository->save( $challenge );

		$result = $this->doApiRequest( [
			'action' => 'passwordlesslogin-verify-login',
		] );

		$this->assertEquals( 'Failed', $result[0]['login-verification']['result'] );
	}
}
