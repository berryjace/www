<?php

class Vendor_SignupController extends Zend_Controller_Action {

    protected $solr_config;

    public function init() {
        
        $this->view->headMeta()->appendName('robots', 'index,follow');
        $config = $this->_helper->Hybrid->getConfig();
        require_once( "ThirdParty/hybridauth/Hybrid/Auth.php" );
        $this->hybridauth = new Hybrid_Auth($config);
        $this->_helper->JSLibs->load_fancy_assets(); //added for vendor sidebar event calendar
    }

    public function indexAction() {
        $this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_jquery_validation();

        $form = new Vendor_Form_Signup();
        $this->view->form = $form;

        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $em = $this->doctrineContainer->getEntityManager();

        $this->view->messages = $this->_helper->FlashMessenger->getMessages();
        

        $this->view->social = false;

        if ($this->getRequest()->isPost()) {

            $formData = $this->getRequest()->getPost();

            if ($form->isValid($formData)) {
                
                $class = 'BL\Entity\User';
                $user = new $class();

                $user->organization_name = $form->getValue('organization_name');
                $user->first_name = $form->getValue('first_name');
                $user->last_name = $form->getValue('last_name');
                $user->city = $form->getValue('city');
                $user->state = $form->getValue('state');
                $user->zipcode = $form->getValue('zipcode');
                $user->website = $form->getValue('website');
                $user->phone = $form->getValue('phone');
                $user->address_line1 = $form->getValue('address_line1');
                $user->address_line2 = $form->getValue('address_line2');
                $user->fax = $form->getValue('fax');
                $user->email = $form->getValue('email');

                $user->account_type = ACC_TYPE_VENDOR;
                $user->username = $form->getValue('username');
                $user->password = hash('MD5', $form->getValue('password'));
                $user->activation_key = '';
                $user->user_status = $from_social_signup ? "activated" : "activated";
                //$this->view->Helper->BUtils->doctrine_dump($user);
                //print_r($user);die();
                $new_role = $em->getRepository("BL\Entity\Role")->findOneBy(array('role_name' => 'vendor'));
                $user->roles->add($new_role);
                $em->persist($user);
                $em->flush();

                /**
                 * Ok customer creation is done. Now let's check if the user is
                 * trying to signup from social sign-ins. In that case we have
                 * to save the identity in the table
                 */
                if ($from_social_signup) {
                    $social_id = new BL\Entity\SocialSigninIdentity();
                    $social_id->user = $em->find("BL\Entity\User", (int) $user->id);
                    $social_id->identity_element = "identity";

                    $provider = $adapters_list[0]; //$_GET["provider"];
                    $adapter = $this->hybridauth->getAdapter($provider);
                    $user_data = $adapter->getUserProfile();

                    $social_id->provider = $provider;
                    $social_id->identity_element_value = $user_data->identifier;

                    $em->persist($social_id);
                    $em->flush();
                    $this->_redirect("login" . $extra_param); //remove after email activated
                }


                $role_id = $em->getRepository("BL\Entity\Role")->get_role_id("vendor");


                if (!$from_social_signup) {
                    /**
                     * Users registering from social signins don't get emails for
                     * activation. So no emails :)
                     */
                    $this->flshMssg = $this->_helper->FlashMessenger;
                    //$this->flshMssg->addMessage(array('success' => 'Signup completed. Please check your mail to activate this account.'));
                    $this->flshMssg->addMessage(array('success' => 'Signup completed.'));
                    $this->_redirect('vendor/signup/success');
                }
            } else {
                $form->populate($formData);
            }
        }
    }

    public function successAction() {
        
    }

    public function OAUTH_socialAction() {
        if (!BL_Auth::isLoggedIn()) {

            $this->_helper->JSLibs->load_jqui_assets();
            $this->_helper->JSLibs->load_jquery_validation();

            $form = new Application_Form_Customer();
            /**
             * Prepopulate the form based on what we have from social signin
             */
            $auth = Zend_Auth::getInstance()->getIdentity();
            $form->populate(BL_Auth::populate_from_social());
            $this->view->form = $form;

            $this->doctrineContainer = Zend_Registry::get('doctrine');
            $em = $this->doctrineContainer->getEntityManager();

            $this->view->messages = $this->_helper->FlashMessenger->getMessages();
        }
    }

    public function index_hybridAction() {
        $this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_jquery_validation();

        $form = new Application_Form_Customer();
        $this->view->form = $form;

        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $em = $this->doctrineContainer->getEntityManager();

        $this->view->messages = $this->_helper->FlashMessenger->getMessages();

        $from_social_signup = $this->hybridauth->hasSession();
        $this->view->social = false;
        if ($from_social_signup)
            $this->view->social = true;

        if ($this->getRequest()->isPost()) {


            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $this->view->custom_err_mssg = '';
                if (sizeof($em->getRepository("BL\Entity\User")->findOneBy(array('username' => $form->getValue('username'))))) {
                    $this->view->custom_err_mssg = "This User Name already taken. Please choose another one.";
                }
                if (sizeof($em->getRepository("BL\Entity\User")->findOneBy(array('email' => $form->getValue('customer_email'))))) {
                    $this->view->custom_err_mssg .= "<br/>The email you entered is already in use. Please enter a different email address.";
                }
                if (!empty($this->view->custom_err_mssg))
                    return;
                $customer_first_name = $form->getValue('customer_first_name');
                $customer_last_name = $form->getValue('customer_last_name');
                $customer_email = $form->getValue('customer_email');
                $customer_username = $form->getValue('username');
                $customer_password = $form->getValue('customer_password');


                $class = 'BL\Entity\User';
                $user = new $class();
                $user->first_name = $customer_first_name;
                $user->last_name = $customer_last_name;
                $user->email = $customer_email;
                $user->username = $customer_username;
                $user->password = hash('MD5', $customer_password);
                $user->reg_date = new DateTime(date("Y-m-d H:i:s"));
                $user->activation_key = md5(rand() . microtime());
                $user->user_status = $from_social_signup ? "activated" : "not confirmed";
                $new_role = $em->getRepository("BL\Entity\Role")->findOneBy(array('role_name' => 'customer'));
                $user->roles->add($new_role);
                $em->persist($user);
                $em->flush();

                if ($from_social_signup) {
                    $identity = Zend_Auth::getInstance()->getIdentity();
                    $social_id = new BL\Entity\SocialSigninIdentity();
                    $social_id->user = $em->find("BL\Entity\User", (int) $user->id);
                    $social_id->provider = Hybrid_Auth::storage()->get("hauth_session.id_provider_id");
                    $social_id->identity_element = "identity";
                    $provider_adapter = $this->hybridauth->wakeup();
                    $social_id->identity_element_value = $provider_adapter->user()->providerUID; //$this->{$callfunc}();

                    $em->persist($social_id);
                    $em->flush();
                    $this->_redirect("login" . $extra_param); //remove after email activated
                }


                $role_id = $em->getRepository("BL\Entity\Role")->get_role_id("customer");

                $activate_url = $this->view->serverUrl() . $this->view->BUrl()->full_url() . '/account/activate/user/' . $user->username . "/code/" . $user->activation_key;

                $email_body = '<table width="600" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                    <td style="border-left:1px solid #fff270; border-right:1px solid#fff270; padding:10px; font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#333333">

                        Dear ' . $customer_first_name . ' ' . $customer_last_name . ',<br/><br/>

                        Welcome to Dayplanit.com
                        <br/><br/>
                        Your Dayplanit.com customer account has been created. <br />
                        Activate your account by clicking on the link below:
                        <br/><br/>
                        Activation link: <a href="' . $activate_url . '">Click this link</a>
                        <br/><br/><br/>
                        If you have any questions or suggestions, please contact us at <a href="support@Dayplanit.com">support@dayplanit.com</a>.
                        <br/><br/><br/>
                        Sincerely,<br/>
                        Admin<br/>
                        The DayPlanIt Team<br/>
                    </td>
                  </tr>

                  <tr>
                    <td style="background:#ded9cf; font-family:Arial, Helvetica, sans-serif; font-size:12px; text-align:center; padding:10px"><a href="http://www.dayplanit.com" style="color:#333333; text-decoration:none">dayplanit.com</a></td>
                  </tr>

                </table>';

                $mail = new Zend_Mail();
                $mail->setType(Zend_Mime::MULTIPART_RELATED);
                $mail->setBodyHtml($email_body);
                $mail->setFrom("Dayplanit Admin", "admin@Dayplanit.com");
                $mail->addTo($customer_email, $customer_first_name . ' ' . $customer_first_name);
                $mail->setSubject('Please activate your account at Dayplanit');
                if (!$from_social_signup) {
                    /**
                     * Users registering from social signins don't get emails for
                     * activation. So no emails :)
                     */
                    $mail->send();
                    $this->flshMssg = $this->_helper->FlashMessenger;
                    $this->flshMssg->addMessage(array('success' => 'Signup completed. Please check your mail to activate this account.'));
                }
                $extra_param = $this->_hasParam("extra_param") ? "?" . $this->view->BUtils()->site_decrypt($this->_getParam("extra_param")) : "";
                $this->_redirect("login" . $extra_param);
            } else {
                $form->populate($formData);
            }
        }
    }

    public function socialAction() {
        if (!BL_Auth::isLoggedIn()) {
            $hybridauth_session_data = $this->hybridauth->getSessionData();
            if ($hybridauth_session_data) {
                $adapters_list = $this->hybridauth->getAuthenticatedProviders();
                $provider = $adapters_list[0]; //$_GET["provider"];
                $adapter = $this->hybridauth->getAdapter($provider);
                $user_data = $adapter->getUserProfile();
            }
            $this->view->adapter = $adapter->id;
            $this->_helper->JSLibs->load_jqui_assets();
            $this->_helper->JSLibs->load_jquery_validation();

            $form = new Application_Form_Customer();
            /**
             * Prepopulate the form based on what we have from social signin
             * As we're providing 4 types, lets just be specific here
             */
            if (strtolower($adapter->id) == "twitter") {
                $user['username'] = $user_data->displayName;
                $user['customer_first_name'] = (isset($user_data->firstName) && $user_data->firstName <> "") ? reset(explode(" ", $user_data->firstName)) : reset(explode(" ", $user_data->displayName));
                $user['customer_last_name'] = (isset($user_data->firstName) && $user_data->firstName <> "") ? end(explode(" ", $user_data->firstName)) : end(explode(" ", $user_data->displayName));
                $user['customer_email'] = $user_data->email;
            } else if (strtolower($adapter->id) == "facebook") {
                $user['username'] = $user_data->displayName;
                $user['customer_first_name'] = (isset($user_data->firstName) && $user_data->firstName <> "") ? reset(explode(" ", $user_data->firstName)) : reset(explode(" ", $user_data->displayName));
                $user['customer_last_name'] = (isset($user_data->lastName) && $user_data->lastName <> "") ? end(explode(" ", $user_data->lastName)) : end(explode(" ", $user_data->displayName));
                $user['customer_email'] = $user_data->email;
            } else if (strtolower($adapter->id) == "yahoo") {
                $user['username'] = $user_data->displayName;
                $user['customer_first_name'] = (isset($user_data->firstName) && $user_data->firstName <> "") ? $user_data->firstName : "";
                $user['customer_last_name'] = (isset($user_data->lastName) && $user_data->lastName <> "") ? $user_data->lastName : "";
                $user['customer_email'] = $user_data->email;
            } else if (strtolower($adapter->id) == "google") {
                $user['username'] = $user_data->displayName;
                $user['customer_first_name'] = (isset($user_data->firstName) && $user_data->firstName <> "") ? $user_data->firstName : "";
                $user['customer_last_name'] = (isset($user_data->lastName) && $user_data->lastName <> "") ? $user_data->lastName : "";
                $user['customer_email'] = $user_data->email;
            }
            $form->populate((array) $user);
            $this->view->form = $form;

            $this->doctrineContainer = Zend_Registry::get('doctrine');
            $em = $this->doctrineContainer->getEntityManager();

            $this->view->messages = $this->_helper->FlashMessenger->getMessages();
        }
    }

    public function checkUsernameAction() {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $em = $this->doctrineContainer->getEntityManager();
        if (sizeof($em->getRepository("BL\Entity\User")->findOneBy(array('username' => $this->_getParam('username'))))) {
            echo 'found';
        } else {
            echo 'nothing';
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function checkEmailAction() {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $em = $this->doctrineContainer->getEntityManager();
        if (sizeof($em->getRepository("BL\Entity\User")->findOneBy(array('email' => $this->_getParam('email'))))) {
            echo 'found';
        } else {
            echo 'nothing';
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    protected function _getFacebookUserID() {
        $provider_adapter = $this->hybridauth->wakeup();
        $api = $provider_adapter->getConfigById(Hybrid_Auth::storage()->get("hauth_session.id_provider_id"));
        $app_id = $api['keys']['APPLICATION_ID'];
        $app_secret = $api['keys']['CONSUMER_SECRET'];
        $my_url = "http://bluelinerdev.zapto.org:8887/zippity/signup/social/";
        $code = $_REQUEST["code"];

        if (empty($code)) {
            $_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
            $dialog_url = "http://www.facebook.com/dialog/oauth?client_id="
                    . $app_id . "&redirect_uri=" . urlencode($my_url) . "&state="
                    . $_SESSION['state'];

            echo('<script type="text/javascript"> top.location.href="' . $dialog_url . '"</script>');
        }

        if ($_REQUEST['state'] == $_SESSION['state']) {
            $token_url = "https://graph.facebook.com/oauth/access_token?"
                    . "client_id=" . $app_id . "&redirect_uri=" . urlencode($my_url)
                    . "&client_secret=" . $app_secret . "&code=" . $code;

            $response = file_get_contents($token_url);
            $params = null;
            parse_str($response, $params);

            $graph_url = "https://graph.facebook.com/me?access_token="
                    . $params['access_token'];

            $user = json_decode(file_get_contents($graph_url));
            return $user->id;
        } else {
            //echo("The state does not match. You may be a victim of CSRF.");
            return false;
        }
    }

}
