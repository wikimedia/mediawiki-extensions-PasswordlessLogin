<?php

namespace PasswordlessLogin\model;

use MediaWiki\Auth\AuthenticationRequest;

class QRCodeRequest extends AuthenticationRequest {
	public $pairToken;

	public function __construct( $pairToken ) {
		$this->pairToken = $pairToken;
	}

	public function getFieldInfo() {
		return [
			'firstStep' => [
				'type' => 'null',
				'label' => wfMessage( 'passwordlesslogin-pair-step-1' ),
			],
			'secondStep' => [
				'type' => 'null',
				'label' => wfMessage( 'passwordlesslogin-pair-step-2' ),
			],
			'pairToken' => [
				'type' => 'hidden',
				'value' => $this->pairToken,
			],
			'thirdStep' => [
				'type' => 'null',
				'label' => wfMessage( 'passwordlesslogin-pair-step-3' ),
			],
		];
	}
}
