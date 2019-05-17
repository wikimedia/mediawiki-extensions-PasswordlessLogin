<?php

namespace PasswordlessLogin\adapter;

use MediaWikiTestCase;
use PasswordlessLogin\model\Device;
use User;

class DeviceTest extends MediaWikiTestCase {
	/**
	 * @covers \PasswordlessLogin\model\Device::forUser
	 */
	public function testForUser() {
		$user = User::newFromName( 'UTSysOp' );
		$user->setId( 1 );

		$device = Device::forUser( $user );

		$this->assertEquals( 1, $device->getUserId() );
		$this->assertNotNull( $device->getPairToken() );
	}

	public function testConstructorEmptyPairToken() {
		$device = new Device( 1 );

		$this->assertNull( $device->getPairToken() );
	}

	public function testForUserUnique() {
		$user = User::newFromName( 'UTSysOp' );
		$user->setId( 1 );

		$device = Device::forUser( $user );
		$secondDevice = Device::forUser( $user );

		$this->assertNotEquals($device->getPairToken(), $secondDevice->getPairToken());
	}
}
