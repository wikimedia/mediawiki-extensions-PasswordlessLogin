<?php

namespace PasswordlessLogin\adapter;

use MediaWikiTestCase;
use PasswordlessLogin\model\Challenge;
use User;

class ChallengeTest extends MediaWikiTestCase {
	public function testForUser() {
		$user = User::newFromName( 'UTSysop' );
		$user->setId( 1 );

		$challenge = Challenge::forUser( $user );

		$this->assertEquals( 1, $challenge->getUserId() );
		$this->assertNotNull( $challenge->getChallenge() );
	}

	public function testForUserUnique() {
		$user = User::newFromName( 'UTSysop' );
		$user->setId( 1 );

		$challenge = Challenge::forUser( $user );
		$secondChallenge = Challenge::forUser( $user );

		$this->assertNotEquals( $challenge->getChallenge(), $secondChallenge->getChallenge() );
	}
}
