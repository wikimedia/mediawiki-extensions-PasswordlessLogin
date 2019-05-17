<?php

namespace PasswordlessLogin;

use DatabaseUpdater;

class Hooks {
	public static function onLoadExtensionSchemaUpdates( DatabaseUpdater $updater = null ) {
		$sql = __DIR__ . '/sql';
		$schema = "$sql/devices.sql";
		$updater->addExtensionUpdate( [ 'addTable', 'passwordlesslogin_devices', $schema, true ] );

		return true;
	}
}
