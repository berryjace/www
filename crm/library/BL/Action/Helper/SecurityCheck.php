<?php

/**
 * SecurityCheck Helper for ACL
 * @author Noman
 * @copyright Blueliner Marketing
 * @version 0.1
 */
class BL_Action_Helper_SecurityCheck extends Zend_Controller_Plugin_Abstract {

	//const MODULE_NO_AUTH='default';
	private $_module_no_auth;
	private $_controller;
	private $_module;
	private $_action;
	private $_role;
	protected $_redirector = null;

	/**
	 * preDispacth
	 * @author Noman
	 * @copyright Blueliner Marketing
	 * @version 0.1
	 * @access public
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request) {

		if (PHP_SAPI == 'cli') {
			/**
			 * command line request should go off the Auth because it's within the host
			 */
			return true;
		}
		$this->initFlashMessages();
		$this->_module_no_auth = array('index', 'default'); //, "customer");

		$this->_controller = $this->getRequest()->getControllerName();
		$this->_module = $this->getRequest()->getModuleName();
		$this->_action = $this->getRequest()->getActionName();
		$this->_mod_cont_no_auth = array("default/login", "vendor/login", "vendor/signup", "client/login","client/wl-pages");

		/*
		 * Allowed for admin
	 */

		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$identity = $auth->getIdentity();
			if("admin" == $identity->roles->role_name) {
				$allowed_for_admin = array('vendor/royalty','vendor/royalty/sale-revenue', 'vendor/invoice');
				$this->_mod_cont_no_auth = array_merge($this->_mod_cont_no_auth, $allowed_for_admin);
			}
		}

		$this->_mod_cont_auth = array();
		$this->current_mod_cont = $this->getRequest()->getModuleName() . "/" . $this->getRequest()->getControllerName();
		//Zend_Auth::getInstance()->clearIdentity();
		$auth = Zend_Auth::getInstance();
		//echo $this->current_mod_cont.'//'.in_array($this->current_mod_cont, $this->_mod_cont_no_auth);die();

		$redirect = true;

		//Zend_Debug::dump($this->_mod_cont_no_auth);
		//echo in_array($this->current_mod_cont, $this->_mod_cont_no_auth) ; exit;
		//if ($this->_module != self::MODULE_NO_AUTH) {
		//if (in_array($this->current_mod_cont, $this->_mod_cont_auth) || in_array($this->_module, $this->_module_auth)) {
			if (!in_array($this->current_mod_cont, $this->_mod_cont_no_auth) && !in_array($this->_module, $this->_module_no_auth)) {
			    if ($this->_isAuth($auth)) {
					$user = Zend_Auth::getInstance()->getIdentity();
					$this->_role = $user->roles->id;
					//		$cache = $manager->getCache('acl');
					//		if (($acl= $cache->load('ACL_'.$this->_role))===false) {
					$acl = new BL_Action_Helper_Acl($this->_role);
					//print_r($this->_isAllowed($auth, $acl));die('---');
					//                    $cache->save($acl,'ACL_'.$this->_role);
					//		}
					if ($this->_isAllowed($auth, $acl)) {
						$redirect = false;
					} else {
						$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
						$redirector->gotoUrl('/denied');
					}
				} else {
					//$redirect=false;
					$redirect = true;
				}
			} else {
				$redirect = false;
			}

			if ($redirect) {
				$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
				$request = Zend_Controller_Front::getInstance()->getRequest();
				$url = $request->getScheme() . '://' . $request->getHttpHost() . $request->getRequestUri();
				if ($request->getRequestUri() <> "") {
					$redirector->gotoUrl('/login?ref=' . $url);
				} else {
					$redirector->gotoUrl('/login');
				}
			}
	}

	/**
	 * Function to Check user identity using Zend_Auth
	 * @author Noman
	 * @copyright Blueliner Marketing
	 * @version 0.1
	 * @param Zend_Auth $auth
	 * @return boolean
	 * @access private
	 */
	private function _isAuth(Zend_Auth $auth) {
		if (!empty($auth) && ($auth instanceof Zend_Auth)) {
			return $auth->hasIdentity();
		}
		return false;
	}

	/**
	 * Function to Check permission using Zend_Auth and Zend_Acl
	 * @author Noman
	 * @copyright Blueliner Marketing
	 * @version 0.1
	 * @access private
	 * @param Zend_Auth $auth
	 * @param Zend_Acl $acl
	 * @return boolean
	 */
	private function _isAllowed(Zend_Auth $auth, Zend_Acl $acl) {
		if (empty($auth) || empty($acl) ||
				!($auth instanceof Zend_Auth) ||
				!($acl instanceof Zend_Acl)) {
			return false;
		}
		$resources = array(
				'*/*/*',
				$this->_module . '/*/*',
				$this->_module . '/' . $this->_controller . '/*',
				$this->_module . '/' . $this->_controller . '/' . $this->_action
		);
		$result = false;
		foreach ($resources as $res) {
			if ($acl->has($res)) {
				$result = $acl->isAllowed($this->_role, $res);
			}
		}
		return $result;
	}

	/**
	 * Function to Add Flash messages to view
	 * @author Mahbub
	 * @copyright Blueliner Marketing
	 * @version 0.1
	 * @access public
	 * @return String
	 */
	public function initFlashMessages() {
		$messages = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getMessages();
		$layout = Zend_Layout::getMvcInstance();
		$view = $layout->getView();
		$view->messages= $messages;
	}

}
