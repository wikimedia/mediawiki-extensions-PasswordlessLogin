<?php

namespace PasswordlessLogin\model;

class Device {
	private $id;
	private $deviceId;
	private $deviceUserId;

	public function getDeviceId() {
		return $this->deviceId;
	}

	public function getDeviceUserId() {
		return $this->deviceUserId;
	}

	public function __construct($deviceUserId, $deviceId = null, $id = null) {
		$this->id = $id;
		$this->deviceId = $deviceId;
		$this->deviceUserId = $deviceUserId;
	}
}
