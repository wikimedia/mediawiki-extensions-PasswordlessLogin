<?php

namespace PasswordlessLogin\adapter;

use ApiBase;
use MediaWiki\MediaWikiServices;
use PasswordlessLogin\model\DevicesRepository;

class ApiPasswordlessLogin extends ApiBase {
	public function execute() {
		/** @var DevicesRepository $devicesRepository */
		$devicesRepository =
			MediaWikiServices::getInstance()->getService( DevicesRepository::SERVICE_NAME );

		$this->requirePostedParameters( [ 'pairToken', 'deviceId' ] );
		$params = $this->extractRequestParams();
		$pairToken = $params['pairToken'];
		$deviceId = $params['deviceId'];

		$device = $devicesRepository->findByPairToken( $pairToken );
		if ( $device == null ) {
			$this->getResult()->addValue( null, 'register', [
				'result' => 'Failed',
			] );

			return;
		}

		$device->setDeviceId( $deviceId );
		$devicesRepository->save( $device );

		$this->getResult()->addValue(null, 'register', [
			'result' => 'Success',
		]);
	}

	public function getAllowedParams() {
		return [
			'pairToken' => [
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true,
			],
			'deviceId' => [
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true,
			],
		];
	}
}
