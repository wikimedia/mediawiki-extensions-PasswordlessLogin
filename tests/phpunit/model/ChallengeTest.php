<?php

namespace PasswordlessLogin\adapter;

use MediaWiki\User\User;
use MediaWikiIntegrationTestCase;
use PasswordlessLogin\model\Challenge;

class ChallengeTest extends MediaWikiIntegrationTestCase {
	/**
	 * @covers \PasswordlessLogin\model\Challenge::forUser
	 */
	public function testForUser() {
		$user = User::newFromName( 'UTSysop' );
		$user->setId( 1 );

		$challenge = Challenge::forUser( $user );

		$this->assertSame( 1, $challenge->getUserId() );
		$this->assertNotNull( $challenge->getChallenge() );
	}

	/**
	 * @covers \PasswordlessLogin\model\Challenge::forUser
	 */
	public function testForUserUnique() {
		$user = User::newFromName( 'UTSysop' );
		$user->setId( 1 );

		$challenge = Challenge::forUser( $user );
		$secondChallenge = Challenge::forUser( $user );

		$this->assertNotEquals( $challenge->getChallenge(), $secondChallenge->getChallenge() );
	}
}
