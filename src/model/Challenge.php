<?php

namespace PasswordlessLogin\model;

use User;

class Challenge {
	private $challenge;
	private $userId;
	private $success = false;

	public function getSuccess() {
		return $this->success;
	}

	public function setSuccess( $success ) {
		$this->success = $success;
	}

	public function __construct( $userId, $challenge, $success = false ) {
		$this->challenge = $challenge;
		$this->userId = $userId;
		$this->success = $success;
	}

	public static function forUser( User $user ) {
		return new Challenge( $user->getId(), bin2hex( random_bytes( 8 ) ) );
	}

	public function getChallenge() {
		return $this->challenge;
	}

	public function getUserId() {
		return $this->userId;
	}

	public function verify( Device $device, $response ) {
		$calculatedMac = hash_hmac( "sha512", $this->getChallenge(), $device->getSecret() );

		$this->success = $calculatedMac === $response;

		return $this->success;
	}
}
