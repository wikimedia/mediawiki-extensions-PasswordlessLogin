<?php

namespace PasswordlessLogin\adapter;

use MediaWiki\MediaWikiServices;
use MediaWikiTestCase;
use PasswordlessLogin\model\Challenge;
use PasswordlessLogin\model\ChallengesRepository;
use PasswordlessLogin\model\Device;
use User;

/**
 * @group Database
 */
class DatabaseChallengesRepositoryTest extends MediaWikiTestCase {
	/**
	 * @var DatabaseChallengesRepository
	 */
	private $repository;

	protected function setUp() {
		parent::setUp();
		$this->tablesUsed[] = 'passwordlesslogin_challenges';

		$this->repository =
			new DatabaseChallengesRepository( MediaWikiServices::getInstance()->getDBLoadBalancer() );
		$this->db->delete('passwordlesslogin_challenges', '*');
	}

	public function testPersistsData() {
		$user = User::newFromName( 'UTSysop' );
		$challenge = Challenge::forUser( $user );

		$this->repository->save( $challenge );
		$result = $this->repository->findByChallenge( $challenge->getChallenge() );

		$this->assertEquals( $challenge, $result );
	}

	public function testRemovesEntries() {
		$user = User::newFromName( 'UTSysop' );
		$challenge = Challenge::forUser( $user );
		$this->repository->save( $challenge );

		$this->repository->remove( $user );

		$this->assertEquals( null, $this->repository->findByChallenge( $challenge->getChallenge() ) );
	}

	public function testUpdatesExistingEntry() {
		$challenge = Challenge::forUser( User::newFromName( 'UTSysop' ) );
		$this->repository->save( $challenge );
		$challenge->setSuccess(true);

		$this->repository->save( $challenge );
		$result = $this->repository->findByChallenge( $challenge->getChallenge() );

		$this->assertEquals( $challenge, $result );
	}
}
