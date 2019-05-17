<?php

namespace PasswordlessLogin\model;

use MediaWiki\Auth\AuthenticationRequest;
use RawMessage;

class RemoveRequest extends AuthenticationRequest {
	public function describeCredentials() {
		return [
			'provider' => new RawMessage('PasswordlessLogin'),
			'account' => 'Authentication App'
		];
	}

	public function getFieldInfo() {
		// TODO: Implement getFieldInfo() method.
	}
}
