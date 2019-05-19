<?php

namespace PasswordlessLogin;

use MediaWikiTestCase;
use OutputPage;
use RequestContext;
use Title;

class HooksTest extends MediaWikiTestCase {
	public function testNotAddingLinksOnNonLoginPage() {
		$out = new OutputPage(RequestContext::getMain());
		$out->setTitle(Title::makeTitle(NS_SPECIAL, 'NoLoginPage'));

		Hooks::onBeforePageDisplay($out, RequestContext::getMain()->getSkin());

		$this->assertEquals([], $out->getModules());
	}

	public function testNotAddingLinksIfNotRequested() {
		$out = new OutputPage(RequestContext::getMain());
		$out->setTitle(Title::makeTitle(NS_SPECIAL, 'LinkAccounts'));

		Hooks::$addFrontendModules = true;
		Hooks::onBeforePageDisplay($out, RequestContext::getMain()->getSkin());

		$this->assertEquals(['ext.PasswordlessLogin.link.scripts'], $out->getModules());
	}

	public function testAddsLinkModuleOnLinkPage() {
		$out = new OutputPage(RequestContext::getMain());
		$out->setTitle(Title::makeTitle(NS_SPECIAL, 'LinkAccounts'));

		Hooks::onBeforePageDisplay($out, RequestContext::getMain()->getSkin());

		$this->assertEquals(['ext.PasswordlessLogin.link.scripts'], $out->getModules());
	}

	public function testNotAddingLoginOnNonLoginPage() {
		$out = new OutputPage(RequestContext::getMain());
		$out->setTitle(Title::makeTitle(NS_SPECIAL, 'NoLoginPage'));

		Hooks::onBeforePageDisplay($out, RequestContext::getMain()->getSkin());

		$this->assertEquals([], $out->getModules());
		$this->assertEquals([], $out->getModuleStyles());
	}

	public function testNotAddingLoginIfNotRequested() {
		$out = new OutputPage(RequestContext::getMain());
		$out->setTitle(Title::makeTitle(NS_SPECIAL, 'UserLogin'));

		Hooks::$addFrontendModules = false;
		Hooks::onBeforePageDisplay($out, RequestContext::getMain()->getSkin());

		$this->assertEquals([], $out->getModules());
		$this->assertEquals([], $out->getModuleStyles());
	}

	public function testAddsLoginModule() {
		$out = new OutputPage(RequestContext::getMain());
		$out->setTitle(Title::makeTitle(NS_SPECIAL, 'UserLogin'));

		Hooks::$addFrontendModules = true;
		Hooks::onBeforePageDisplay($out, RequestContext::getMain()->getSkin());

		$this->assertEquals(['ext.PasswordlessLogin.login'], $out->getModules());
		$this->assertEquals(['ext.PasswordlessLogin.login.styles'], $out->getModuleStyles());
	}
}
