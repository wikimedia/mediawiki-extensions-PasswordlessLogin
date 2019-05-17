<?php

namespace PasswordlessLogin\model;

use User;

class Device {
	private $id;
	private $deviceId;
	private $deviceUserId;
	private $devicePairToken;

	public function getDeviceId() {
		return $this->deviceId;
	}

	public function setDeviceId( $deviceId ) {
		$this->deviceId = $deviceId;
	}

	public function getUserId() {
		return $this->deviceUserId;
	}

	public function __construct( $deviceUserId, $deviceId = null, $id = null ) {
		$this->id = $id;
		$this->deviceId = $deviceId;
		$this->deviceUserId = $deviceUserId;
	}

	static function forUser( User $user ) {
		$device = new Device( $user->getId() );
		$device->devicePairToken = bin2hex( random_bytes( 16 ) );

		return $device;
	}

	public function getPairToken() {
		return $this->devicePairToken;
	}
}
