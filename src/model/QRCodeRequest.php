<?php

namespace PasswordlessLogin\model;

use Endroid\QrCode\QrCode;
use MediaWiki\Auth\AuthenticationRequest;
use MediaWiki\MediaWikiServices;

class QRCodeRequest extends AuthenticationRequest {
	public $pairToken;

	public function __construct( $pairToken ) {
		$this->pairToken = $pairToken;
	}

	public function getFieldInfo() {
		$mainConfig = MediaWikiServices::getInstance()->getMainConfig();
		$config =
			MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'passwordless' );

		$apiUrl = constructApiUrl( $mainConfig, $config );
		$accountName = $mainConfig->get( 'Sitename' );
		$qrCode = new QrCode( $accountName . ';' . $apiUrl . ';' . $this->pairToken );

		return [
			'firstStep' => [
				'type' => 'null',
				'label' => wfMessage( 'passwordlesslogin-pair-step-1' ),
			],
			'secondStep' => [
				'type' => 'null',
				'label' => wfMessage( 'passwordlesslogin-pair-step-2' ),
			],
			'qrCode' => [
				'type' => 'null',
				'value' => 'data:' . $qrCode->getContentType() . ';base64,' .
					base64_encode( $qrCode->writeString() ),
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
