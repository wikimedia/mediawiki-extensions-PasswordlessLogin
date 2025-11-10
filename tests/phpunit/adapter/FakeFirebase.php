<?php

namespace PasswordlessLogin\adapter;

use MediaWiki\Config\GlobalVarConfig;
use PasswordlessLogin\model\Challenge;
use PasswordlessLogin\model\Device;

class FakeFirebase extends FirebaseMessageSender {
	/**
	 * @inheritDoc
	 */
	public function __construct() {
		parent::__construct( GlobalVarConfig::newInstance(), "" );
	}

	/**
	 * @inheritDoc
	 */
	public function send( Device $device, Challenge $challenge ) {
	}
}
