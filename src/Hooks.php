<?php

namespace PasswordlessLogin;

use DatabaseUpdater;
use OutputPage;
use Skin;
use Title;

class Hooks {
	public static function onLoadExtensionSchemaUpdates( DatabaseUpdater $updater = null ) {
		$sql = __DIR__ . '/sql';
		$schema = "$sql/devices.sql";
		$updater->addExtensionUpdate( [ 'addTable', 'passwordlesslogin_devices', $schema, true ] );

		return true;
	}

	public static function onBeforePageDisplay( OutputPage &$out, Skin &$skin ) {
		if (!$out->getTitle()->equals(Title::makeTitle(NS_SPECIAL, 'LinkAccounts'))) {
			return;
		}
		$out->addModules('ext.PasswordlessLogin.link.scripts');
	}
}
