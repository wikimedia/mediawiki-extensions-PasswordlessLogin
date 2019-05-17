<?php

namespace PasswordlessLogin\model;

use MediaWiki\Auth\AuthenticationRequest;
use RawMessage;

class LinkRequest extends AuthenticationRequest {
	public function getFieldInfo() {
		return [
			'passwordless' => [
				'type' => 'button',
				'label' => new RawMessage('Passwordless Login')
			]
		];
	}
}
