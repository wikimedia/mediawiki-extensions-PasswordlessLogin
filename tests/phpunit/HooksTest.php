<?php

namespace PasswordlessLogin;

use OutputPage;
use RequestContext;
use Title;

class HooksTest extends \MediaWikiTestCase {
	public function testNotAddingOnNonLoginPage() {
		$out = new OutputPage(RequestContext::getMain());
		$out->setTitle(Title::makeTitle(NS_SPECIAL, 'NoLoginPage'));

		Hooks::onBeforePageDisplay($out, RequestContext::getMain()->getSkin());

		$this->assertEquals([], $out->getModules());
	}

	public function testAddsLinkModuleOnLinkPage() {
		$out = new OutputPage(RequestContext::getMain());
		$out->setTitle(Title::makeTitle(NS_SPECIAL, 'LinkAccounts'));

		Hooks::onBeforePageDisplay($out, RequestContext::getMain()->getSkin());

		$this->assertEquals(['ext.PasswordlessLogin.link.scripts'], $out->getModules());
	}
}
