<?php

use MediaWiki\MediaWikiServices;
use PasswordlessLogin\adapter\DatabaseChallengesRepository;
use PasswordlessLogin\adapter\DatabaseDeviceRepository;
use PasswordlessLogin\adapter\FirebaseMessageSender;
use PasswordlessLogin\Hooks;
use PasswordlessLogin\model\ChallengesRepository;
use PasswordlessLogin\model\DevicesRepository;

/** @phpcs-require-sorted-array */
return [
	ChallengesRepository::SERVICE_NAME => static function (
		MediaWikiServices $services,
	): DatabaseChallengesRepository {
		return new DatabaseChallengesRepository( $services->getDBLoadBalancer() );
	},

	DevicesRepository::SERVICE_NAME => static function (
		MediaWikiServices $services,
	): DatabaseDeviceRepository {
		return new DatabaseDeviceRepository( $services->getDBLoadBalancer() );
	},

	FirebaseMessageSender::SERVICE_NAME => static function (
		MediaWikiServices $services,
	): FirebaseMessageSender {
		$config = $services->getConfigFactory()->makeConfig( 'passwordless' );
		$mainConfig = $services->getMainConfig();

		$apiUrl = Hooks::constructApiUrl( $mainConfig, $config );

		return new FirebaseMessageSender( $config, $apiUrl );
	},
];
