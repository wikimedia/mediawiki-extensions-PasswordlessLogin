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
			'pairToken' => [
				'type' => 'hidden',
				'value' => $this->pairToken
			]
		];
	}
}
