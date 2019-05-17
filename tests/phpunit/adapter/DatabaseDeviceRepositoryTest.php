<?php

namespace PasswordlessLogin\adapter;

use MediaWiki\MediaWikiServices;
use MediaWikiTestCase;
use PasswordlessLogin\model\Device;
use User;

/**
 * @group Database
 */
class DatabaseDeviceRepositoryTest extends MediaWikiTestCase {
	/**
	 * @var DatabaseDeviceRepository
	 */
	private $repository;

	protected function setUp() {
		parent::setUp();
		$this->tablesUsed[] = 'passwordlesslogin_devices';

		$this->repository =
			new DatabaseDeviceRepository( MediaWikiServices::getInstance()->getDBLoadBalancer() );
	}

	/**
	 * @covers \PasswordlessLogin\adapter\DatabaseDeviceRepository::save
	 * @covers \PasswordlessLogin\adapter\DatabaseDeviceRepository::findByUserId
	 */
	public function testPersistsData() {
		$user = User::newFromName('UTSysOp');
		$device = new Device( $user->getId(), 'A_DEVICE_ID' );
		$this->repository->save( $device );

		$result = $this->repository->findByUserId($user->getId());

		$this->assertEquals($device->getDeviceId(), $result->getDeviceId());
		$this->assertEquals($device->getDeviceUserId(), $result->getDeviceUserId());
	}

	public function testNoEntry() {
		$user = User::newFromName('UTSysOp');

		$result = $this->repository->findByUserId($user->getId());

		$this->assertEquals(null, $result);
	}


	/**
	 * @covers \PasswordlessLogin\adapter\DatabaseDeviceRepository::save
	 * @covers \PasswordlessLogin\adapter\DatabaseDeviceRepository::remove
	 */
	public function testRemovesEntries() {
		$user = User::newFromName('UTSysOp');
		$device = new Device( $user->getId(), 'A_DEVICE_ID' );
		$this->repository->save( $device );

		$this->repository->remove($user);

		$this->assertEquals(null, $this->repository->findByUserId($user->getId()));
	}
}
