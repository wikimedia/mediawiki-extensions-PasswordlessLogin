<?php

namespace PasswordlessLogin\model;

use Endroid\QrCode\QrCode;
use MediaWiki\Auth\AuthenticationRequest;
use MediaWiki\MediaWikiServices;
use PasswordlessLogin\Hooks;

class QRCodeRequest extends AuthenticationRequest {
	public $pairToken;

	/**
	 * QRCodeRequest constructor.
	 * @param string $pairToken
	 */
	public function __construct( $pairToken ) {
		$this->pairToken = $pairToken;
	}

	/**
	 * @inheritDoc
	 */
	public function getFieldInfo() {
		$mainConfig = MediaWikiServices::getInstance()->getMainConfig();
		$config =
			MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'passwordless' );

		$apiUrl = Hooks::constructApiUrl( $mainConfig, $config );
		$accountName = $mainConfig->get( 'Sitename' );
		$qrCode = new QrCode( $accountName . ';' . $apiUrl . ';' . $this->pairToken );

		return [
			'firstStep' => [
				'type' => 'null',
				'label' => wfMessage( 'passwordlesslogin-pair-step-1' ),
			],
			'googleplay' => [
				'type' => 'null',
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
