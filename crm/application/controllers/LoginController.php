 <?php

class LoginController extends Zend_Controller_Action {

    public function init() {
        $config = $this->_helper->Hybrid->getConfig();
        require_once( "ThirdParty/hybridauth/Hybrid/Auth.php" );
        $this->hybridauth = new Hybrid_Auth($config);

        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
    }

    public function indexAction() {
        /*
         * Let's set layout for popup calls
         */
        Zend_Controller_Action_HelperBroker::addHelper(
                        new BL_Action_Helper_JSLibs()
        );
        $this->_helper->getHelper('JSLibs')->load_hybrid_assets();


        $this->checkLogged();

        $type = $this->_getParam('type');
        $this->view->type = $type;
        // }
        $this->view->custom_err_mssg = '';
        if ($this->_hasParam('redir') && $this->_hasParam('sendemail')) {
            $this->view->custom_err_mssg = 'You must log-in in order to share this over with your friends.';
        }
        if ($this->getRequest()->isPost()) {
            $adapter = new BL_Auth_Adapter($this->_getParam('username'), $this->_getParam('password'));

            if (trim(($this->_getParam('username')) == "")) {
                $this->view->custom_err_mssg = 'Please fill in the required credentials<br/>';
            } else {


                $result = Zend_Auth::getInstance()->authenticate($adapter);

		//Zend_Debug::dump($result);exit;
                if (Zend_Auth::getInstance()->hasIdentity()) {

                    $auth1 = Zend_Auth::getInstance()->getIdentity();
                    if (isset($_POST['rememberme'])) {
                        Zend_Session::rememberMe(60 * 60 * 24 * 30); //remember for 30 days
                    } else {
                        Zend_Session::forgetMe();
                    }

		    $ref = $this->_getParam('ref');
		    if(!empty($ref)) {
			$this->_redirect($ref);
		    }
                    $this->manage_redirects(NULL);
                    //$this->manage_redirects('profile');
                } else {
                    $this->view->custom_err_mssg = 'Authentication Error<br/>';
                }
            }
        } elseif (Zend_Auth::getInstance()->hasIdentity()) {
            $auth1 = Zend_Auth::getInstance()->getIdentity();
            /**
             * We need to check if user is logged in via social
             */
            $auth_provider = BL_Auth::getProvider($auth1);

            if ($auth_provider == 'zend'&&isset($auth1->roles->role_name)) {
                if ("admin" == $auth1->roles->role_name) {
                    $this->_redirect('admin');
                } elseif ("client" == $auth1->roles->role_name) {
                    $this->_redirect('client/');
                } elseif ("vendor" == $auth1->roles->role_name) {
                    $showNotifications = $this->em->getRepository("BL\Entity\Notification")->notificationTitles();
                    $this->_redirect('vendor/index/index/showNotifications/' . $showNotifications);
                }
            } else {
		//Zend_Debug::dump($auth1);exit;
		if (1 == $auth1->account_type) {
                    $this->_redirect('admin');
                } elseif (3 == $auth1->account_type) {
                    $this->_redirect('client/');
                } elseif (2 == $auth1->account_type) {
		    $auth1->roles->role_name = "vendor";
		    $auth1->roles->id = 2;
                    $showNotifications = $this->em->getRepository("BL\Entity\Notification")->notificationTitles();
                    $this->_redirect('vendor/index/index/showNotifications/' . $showNotifications);
                }
            }
        }
    }

    private function manage_redirects($popup_param) {
        $auth1 = Zend_Auth::getInstance()->getIdentity();
        /**
         * added code for tracking user login time
         */
        $user = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id' => $auth1->id));
        $user->last_login = new \DateTime(date("Y-m-d H:i:s"));
	$user->reg_status = 'activated';
        $this->em->persist($user);
        $this->em->flush();
	//Zend_Debug::dump($user);exit();
	if (isset($auth1->roles->role_name)){
	        if ("admin" == $auth1->roles->role_name) {
        	    $this->_redirect('admin' . $popup_param);
	        } elseif ("client" == $auth1->roles->role_name) {
        	    $this->_redirect('client' . $popup_param);
	        } elseif ("vendor" == $auth1->roles->role_name) {
        	    //$this->_redirect('vendor' . $popup_param);
	            $this->_redirect('vendor/');
        	} elseif ("customer" == $auth1->roles->role_name) {
	            if ($this->_hasParam("ref")) {
        	        $this->_redirect($this->_getParam('ref'));
	            } 
		    else {
               	 	$this->_redirect('profile/');
            	    }
        	}
	}else {
                //Zend_Debug::dump($auth1);exit;
                if (1 == $auth1->account_type) {
                    $this->_redirect('admin');
                } elseif (3 == $auth1->account_type) {
                    $this->_redirect('client/');
                } elseif (2 == $auth1->account_type) {
                    $auth1->roles->role_name = "vendor";
                    $auth1->roles->id = 2;
		    $user->roles->user_id = $user->id;
		    $user->roles->role_id =2;
		    $this->em->persist($user);
        	    $this->em->flush();
		    Zend_Debug::dump($user);exit();
                    $showNotifications = $this->em->getRepository("BL\Entity\Notification")->notificationTitles();
                    $this->_redirect('vendor/index/index/showNotifications/' . $showNotifications);
                }
            }
    }

    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        $adapters_list = $this->hybridauth->getAuthenticatedProviders();
        if ($adapters_list) {
            foreach ($adapters_list as $adapter)
                $adapter = $this->hybridauth->getAdapter($adapter);
            $adapter->logout();
        }
        $this->hybridauth->restoreSessionData(null);
        $this->_redirect('/login');
    }

    public function checkLogged($popup_param="") {
        if (BL_Auth::isLoggedIn()) {
            $this->manage_redirects($popup_param);
        }

        //$hybridauth_session_data = $this->hybridauth->getSessionData();
        //print_r($hybridauth_session_data);die('==');
        $adapters_list = $this->hybridauth->getAuthenticatedProviders();
        $provider = @$_GET["provider"] ? @$_GET["provider"] : @$adapters_list[0]; //$_GET["provider"];
        if ($provider) {
            $adapter = $this->hybridauth->authenticate($provider);
            $this->social_login($provider);
        }
    }

    public function check_valid_social_login($provider) {
        if ($provider == "none") {
            return true;
        }
        $adapter = $this->hybridauth->getAdapter($provider);
        $user_data = $adapter->getUserProfile();
        //print_r($user_data);die('====');
        $fields_to_check = array('provider' => $provider,
            'identity_element_value' => $user_data->identifier,
            'identity_element' => 'identity');
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $em = $this->doctrineContainer->getEntityManager();
        $social_login_data = $em->getRepository("BL\Entity\SocialSigninIdentity")->findOneBy($fields_to_check);
        //print_r($social_login_data);
        //die();
        return $social_login_data;
    }

    public function social_login($provider) {
        $popup_param = '';
        if ($this->_hasParam("usepopup") AND $this->_getParam("usepopup") == "true") {
            $this->_helper->layout()->setLayout('layout/layout_popup');
            $this->view->is_popup = true;
            $popup_param = "&usepopup=true";
        }
        $valid_social = $this->check_valid_social_login($provider);

        if ($valid_social <> null) {
            /**
             * User logged in via social media AND is registered with us
             * So make him logged in
             */
            $adapter = new BL_Auth_Adapter($valid_social->user->username, '', true);
            $result = Zend_Auth::getInstance()->authenticate($adapter);
            $this->manage_redirects($popup_param);
        } else {
            /**
             * User logged in via social media BUT dont have registration
             * with us. So drive them to social registration
             */
            return $this->_redirect('signup/social');
        }
    }

}

