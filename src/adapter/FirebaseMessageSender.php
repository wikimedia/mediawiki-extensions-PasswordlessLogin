<?php

namespace PasswordlessLogin\adapter;

use Config;
use PasswordlessLogin\model\Challenge;
use PasswordlessLogin\model\Device;

class FirebaseMessageSender {
	const SERVICE_NAME = 'FirebaseMessageSender';

	/** @var Config */
	private $config;

	public function __construct( Config $config ) {
		$this->config = $config;
	}

	public function send( Device $device, Challenge $challenge ) {
		$url = 'https://fcm.googleapis.com/fcm/send';

		$fields = [
			'to' => $device->getDeviceId(),
			'data' => [
				"challenge" => $challenge->getChallenge(),
				"apiUrl" => "http://10.0.2.2:8080/w/api.php",
			],
			"priority" => "high",
		];
		$fields = json_encode( $fields );

		$headers = [
			'Authorization: key=' . $this->config->get('PLFirebaseAccessToken'),
			'Content-Type: application/json',
		];

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields );

		$result = curl_exec( $ch );
		curl_close( $ch );
	}
}
