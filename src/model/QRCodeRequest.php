<?php

namespace PasswordlessLogin\model;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
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
		$senderId = $config->get( 'PLFirebaseSenderId' );
		$accountName = $mainConfig->get( 'Sitename' );
		$qrCode = QrCode::create( $accountName . ';' . $apiUrl . ';' . $this->pairToken . ';' . $senderId );
		$writer = new PngWriter();

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
				'value' => $writer->write( $qrCode )->getDataUri(),
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
