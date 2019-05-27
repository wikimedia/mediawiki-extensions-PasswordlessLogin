<?php

namespace PasswordlessLogin\model;

use MediaWiki\Auth\AuthenticationRequest;

class LoginRequest extends AuthenticationRequest {
	public function getFieldInfo() {
		return [
			'username' => [
				'type' => 'string',
				'help' => wfMessage( 'authmanager-username-help' ),
			],
			'passwordless' => [
				'type' => 'button',
				'label' => wfMessage( 'passwordlesslogin-login' ),
			],
		];
	}
}
