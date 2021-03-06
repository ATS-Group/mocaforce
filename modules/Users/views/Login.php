<?php
/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Contributor(s): YetiForce.com
 * ********************************************************************************** */

class Users_Login_View extends \App\Controller\View\Base
{
	/**
	 * {@inheritdoc}
	 */
	public function loginRequired()
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function checkPermission(App\Request $request)
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function preProcess(App\Request $request, $display = true)
	{
		parent::preProcess($request, false);
		$viewer = $this->getViewer($request);
		$viewer->assign('QUALIFIED_MODULE', $request->getModule());
		if ($display) {
			$this->preProcessDisplay($request);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function postProcess(App\Request $request, $display = true)
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function process(App\Request $request)
	{
		$viewer = $this->getViewer($request);
		$viewer->assign('IS_BLOCKED_IP', Settings_BruteForce_Module_Model::getCleanInstance()->isBlockedIp());
		if (\App\Session::has('UserLoginMessage')) {
			$viewer->assign('MESSAGE', \App\Session::get('UserLoginMessage'));
			$viewer->assign('MESSAGE_TYPE', \App\Session::get('UserLoginMessageType'));
			\App\Session::delete('UserLoginMessage');
			\App\Session::delete('UserLoginMessageType');
		}
		if ('2fa' === \App\Session::get('LoginAuthyMethod')) {
			$viewer->view('Login2faTotp.tpl', 'Users');
		} else {
			$viewer->assign('LANGUAGE_SELECTION', App\Config::main('langInLoginView'));
			$viewer->assign('LAYOUT_SELECTION', App\Config::main('layoutInLoginView'));
			$viewer->view('Login.tpl', 'Users');
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getHeaderCss(App\Request $request)
	{
		return array_merge(parent::getHeaderCss($request), $this->checkAndConvertCssStyles([
			'modules.Users.Login'
		]));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getHeaderScripts(App\Request $request)
	{
		return array_merge(parent::getHeaderScripts($request), $this->checkAndConvertJsScripts([
			'~libraries/device-uuid/lib/device-uuid.js',
			'modules.Users.resources.Login'
		]));
	}

	/**
	 * {@inheritdoc}
	 */
	public function setCspHeaders()
	{
		header("content-security-policy: default-src 'self' 'nonce-" . App\Session::get('CSP_TOKEN') . "'; object-src 'none';base-uri 'self'; frame-ancestors 'self';");
	}
}
