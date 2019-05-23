<?php

namespace PasswordlessLogin\model;

use MediaWiki\Auth\AuthenticationRequest;

class ConfirmRequest extends AuthenticationRequest {
	public function getFieldInfo() {
		return [
			'confirm' => [
				'type' => 'hidden',
				'value' => 'hidden'
			]
		];
	}
}
