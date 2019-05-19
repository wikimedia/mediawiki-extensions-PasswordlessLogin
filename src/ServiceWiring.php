<?php

use MediaWiki\MediaWikiServices;
use PasswordlessLogin\adapter\DatabaseChallengesRepository;
use PasswordlessLogin\adapter\DatabaseDeviceRepository;
use PasswordlessLogin\adapter\FirebaseMessageSender;
use PasswordlessLogin\model\ChallengesRepository;
use PasswordlessLogin\model\DevicesRepository;

return [
	DevicesRepository::SERVICE_NAME => function ( MediaWikiServices $services ) {
		return new DatabaseDeviceRepository( $services->getDBLoadBalancer() );
	},

	ChallengesRepository::SERVICE_NAME => function ( MediaWikiServices $services ) {
		return new DatabaseChallengesRepository( $services->getDBLoadBalancer() );
	},

	FirebaseMessageSender::SERVICE_NAME => function ( MediaWikiServices $services ) {
		$config = $services->getConfigFactory()->makeConfig( 'passwordless' );
		$apiUrl = '';
		if ( $config->has( 'PLDevApiUrl' ) ) {
			$apiUrl = $config->get( 'PLDevApiUrl' );
		} else {
			$mainConfig = $services->getMainConfig();
			$apiUrl = $mainConfig->get( 'Server' ) . wfScript( 'api' );
		}

		return new FirebaseMessageSender( $config, $apiUrl );
	},
];
