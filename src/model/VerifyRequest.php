<?php

namespace PasswordlessLogin\model;

use MediaWiki\Auth\AuthenticationRequest;
use RawMessage;

class VerifyRequest extends AuthenticationRequest {
	public function getFieldInfo() {
		return [
			'request' => [
				'type' => 'hidden',
				'value' => 'hidden'
			]
		];
	}
}
