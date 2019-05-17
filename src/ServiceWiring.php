<?php

use MediaWiki\MediaWikiServices;
use PasswordlessLogin\adapter\DatabaseDeviceRepository;
use PasswordlessLogin\model\DevicesRepository;

return [
	DevicesRepository::SERVICE_NAME => function ( MediaWikiServices $services ) {
		return new DatabaseDeviceRepository( $services->getDBLoadBalancer() );
	},
];
