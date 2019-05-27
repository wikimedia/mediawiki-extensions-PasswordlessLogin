<?php

namespace PasswordlessLogin\model;

use MediaWiki\Auth\AuthenticationRequest;

class LinkRequest extends AuthenticationRequest {
	public function getFieldInfo() {
		return [
			'passwordless' => [
				'type' => 'button',
				'label' => wfMessage( 'passwordlesslogin-pair-device' ),
			],
		];
	}
}
