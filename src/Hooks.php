<?php

namespace PasswordlessLogin;

use DatabaseUpdater;
use OutputPage;
use Skin;
use Title;

class Hooks {
	public static $addFrontendModules = false;

	public static function onLoadExtensionSchemaUpdates( DatabaseUpdater $updater = null ) {
		$sql = __DIR__ . '/sql';
		$updater->addExtensionUpdate( [ 'addTable', 'passwordlesslogin_devices',
			"$sql/devices.sql", true ] );
		$updater->addExtensionUpdate( [ 'addTable', 'passwordlesslogin_challenges',
			"$sql/challenges.sql", true ] );

		return true;
	}

	public static function onBeforePageDisplay( OutputPage &$out, Skin &$skin ) {
		if (!self::$addFrontendModules) {
			return;
		}
		if ($out->getTitle()->equals(Title::makeTitle(NS_SPECIAL, 'LinkAccounts'))) {
			$out->addModules('ext.PasswordlessLogin.link.scripts');
		}
		if ($out->getTitle()->equals(Title::makeTitle(NS_SPECIAL, 'UserLogin'))) {
			$out->addModules('ext.PasswordlessLogin.login');
		}
	}
}
