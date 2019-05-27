<?php

namespace PasswordlessLogin\model;

use MediaWiki\Auth\AuthenticationRequest;

class RemoveRequest extends AuthenticationRequest {
	public function describeCredentials() {
		return [
			'provider' => wfMessage( 'passwordlesslogin' ),
			'account' => wfMessage( 'passwordlesslogin-authentication-app' ),
		];
	}

	public function getFieldInfo() {
		// TODO: Implement getFieldInfo() method.
	}
}
