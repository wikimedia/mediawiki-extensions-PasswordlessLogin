<?php

namespace PasswordlessLogin;

use DatabaseUpdater;
use MediaWiki\Auth\AuthenticationRequest;
use OutputPage;
use PasswordlessLogin\adapter\HTMLImageField;
use PasswordlessLogin\model\QRCodeRequest;
use Skin;
use Title;

class Hooks {
	public static $addFrontendModules = false;

	public static function onLoadExtensionSchemaUpdates( DatabaseUpdater $updater = null ) {
		$sql = __DIR__ . '/sql';
		$updater->addExtensionUpdate( [
			'addTable',
			'passwordlesslogin_devices',
			"$sql/devices.sql",
			true,
		] );
		$updater->addExtensionUpdate( [
			'addTable',
			'passwordlesslogin_challenges',
			"$sql/challenges.sql",
			true,
		] );

		return true;
	}

	public static function onBeforePageDisplay( OutputPage &$out, Skin &$skin ) {
		if ( !self::$addFrontendModules ) {
			return;
		}
		if ( $out->getTitle()->equals( Title::makeTitle( NS_SPECIAL, 'UserLogin' ) ) ) {
			$out->addModules( 'ext.PasswordlessLogin.login' );
			$out->addModuleStyles( 'ext.PasswordlessLogin.login.styles' );
		}
	}

	public function onAuthChangeFormFields(
		array $requests, array $fieldInfo, array &$formDescriptor, $action
	) {
		/** @var QRCodeRequest $req */
		$req = AuthenticationRequest::getRequestByClass( $requests, QRCodeRequest::class, true );
		if ( !$req ) {
			return;
		}

		$formDescriptor['qrCode']['class'] = HTMLImageField::class;
		$formDescriptor['qrCode']['data-uri'] = $formDescriptor['qrCode']['default'];
	}
}
