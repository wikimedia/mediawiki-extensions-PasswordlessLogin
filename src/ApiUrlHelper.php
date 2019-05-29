<?php

function constructApiUrl( Config $mainConfig, Config $config ) {
	if ( $config->has( 'PLDevApiUrl' ) ) {
		$apiUrl = $config->get( 'PLDevApiUrl' );
	} else {
		$apiUrl = $mainConfig->get( 'Server' ) . wfScript( 'api' );
	}

	return $apiUrl;
}
