<?php

namespace PasswordlessLogin\model;

use MediaWiki\Auth\AuthenticationRequest;

class QRCodeRequest extends AuthenticationRequest {
	public $pairToken;

	public function __construct($pairToken) {
		$this->pairToken = $pairToken;
	}

	public function getFieldInfo() {
		return [
			'firstStep' => [
				'type' => 'null',
				'value' => '1. Download the MediaWiki PasswordlessLogin app',
			],
			'secondStep' => [
				'type' => 'null',
				'value' => '2. Scan the QR Code (JavaScript required) with the app',
			],
			'pairToken' => [
				'type' => 'hidden',
				'value' => $this->pairToken
			],
			'thirdStep' => [
				'type' => 'null',
				'value' => '3. Once the app is setup, click the button',
			],
		];
	}
}
