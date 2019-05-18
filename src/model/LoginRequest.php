<?php

namespace PasswordlessLogin\model;

use MediaWiki\Auth\AuthenticationRequest;

class LoginRequest extends AuthenticationRequest {
	public function getFieldInfo() {
		return [
			'username' => [
				'type' => 'string'
			],
			'passwordless' => [
				'type' => 'button',
				'label' => new \RawMessage('Login with your Smartphone')
			]
		];
	}
}
