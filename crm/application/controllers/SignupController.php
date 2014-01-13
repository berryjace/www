<?php

class SignupController extends Zend_Controller_Action {

    public function init() {
        $this->view->headMeta()->appendName('robots', 'index,follow');
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
		$this->session = new Zend_Session_Namespace('default');
    }

    /**
     * Function to Register a vendor into the system. An email is sent after the registration
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function indexAction() {
        $this->_helper->JSLibs->load_jquery_validation();
        $form = new Application_Form_Signup();
        $this->view->form = $form;

        $type = $this->_getParam('type');
        if ($type == 'become-a-licensed-vendor' )
            $this->_redirect('/signup/index/type/vendor');
        else
            $this->view->type = $type ;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            $form->getElement('email')->addValidator(new Zend_Validate_Db_NoRecordExists(array('table' => 'users', 'field' => 'email')), true)
                    ->addErrorMessage("Recovery email already exists: Please select a different email or contact Registration@greeklicensing.com so that we may assist you.");

            $form->getElement('username')->addValidator(new Zend_Validate_Db_NoRecordExists(array('table' => 'users', 'field' => 'username')), true)
                    ->addErrorMessage("Username already exists or contains a special character (spaces, %, etc). Please enter a different username.");

            if ($form->isValid($formData)) {
                $user = new \BL\Entity\User();
                if($type=='client')
                    $user->account_type = ACC_TYPE_CLIENT;
                else
                    $user->account_type = ACC_TYPE_VENDOR;
                $user->organization_name = $form->getValue('organization_name');
                $user->username = $form->getValue('username');
                $user->password = md5($form->getValue('password'));
                $user->first_name = $form->getValue('first_name');
                $user->last_name = $form->getValue('last_name');
                $user->address_line1 = $form->getValue('address_line_1');
                $user->address_line2 = $form->getValue('address_line_2');
                $user->city = $form->getValue('city');
                $user->state = $form->getValue('state');
                $user->zipcode = $form->getValue('zip');
                $user->email = $form->getValue('email');
                $user->company_email = $form->getValue('company_email');
                $user->phone = $form->getValue('phone_1');
                $user->phone2 = $form->getValue('phone_2');
                $user->fax = $form->getValue('fax');
                $user->website = $form->getValue('web_page');

                $user->user_status = "Pending";
                $user->reg_status = "Pending";
		
                $user->reg_date = new DateTime();
                $user->created_at = new DateTime();
                $this->em->persist($user);
                $this->em->flush();

                /**
                 * Add the role "Vendor"
                 */
                $vendor_role = $this->em->getRepository("BL\Entity\Role")->findOneBy(array('role_name' => ($type=='client')?$type:'vendor'));
                $user->roles->add($vendor_role);
                $this->em->flush();
                $this->em->clear();
                /**
                 * Let's send the user/Vendor an Email
                 */

                $this->view->username = $user->username;
                $this->view->organization_name = $user->organization_name;
                $send=$this->_helper->BUtilities->send_mail(array(
                    'to' => $user->email,
                    'to_name' => $user->organization_name,
                    'from' => 'registration@greeklicensing.com',
                    'from_name' => 'Greek Licensing Registration',
                    'subject' => 'Greek Licensing '.(($type=='client')?ucfirst($type):'Vendor').' Portal Registration',
                    'body' => $this->view->render('emails/register-vendors.phtml')
                ));
                if(!$send){
                    //die('Mail SENDING ERROR');
                }

                $this->_helper->flashMessenger("New ".($type=='client')?ucfirst($type):'Vendor'." added succesfully!", "Info");
                $this->_redirect('signup/thank-you');
            } else {

            }
        }
    }
    
    public function existingUserAction(){
    	$this->_helper->JSLibs->load_jquery_validation();
    	$form = new Application_Form_Existing();
    	
    	$this->view->form = $form;
    	
    	$this->view->error = '';
    	
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    	
    		$vendor = $this->em->getRepository('BL\Entity\User')->findOneBy(array("user_code"=>$formData["number"], "email"=>$formData["email"]));
    		
    		if ($vendor == null){
    			$this->_helper->flashMessenger("Sorry, but our records don't match the data you provided, please try again or contact the admin to correct the issue", "Info");
    			$this->view->error .= "<br />Sorry, but our records don't match the data you provided.<br/> Please try again or contact the admin to correct the information";
    		} else if (!empty($vendor->username) || $vendor->username != '' || $vendor->username != null || $vendor->password != null){
    			$this->view->error .= "<br/><b style='color:red'>This account already has a username and password, please log in using those credentials.</b><br /> If you cannot remember your account information then <a href='".$this->view->baseUrl("forgot/")."'>click here</a> to recover it.";
    		} else {
    			$key = uniqid();
    			
    			$vendor->activation_key = $key;
    			$this->em->persist($vendor);
    			$this->em->flush();
    			
    			$subject = "Greek licensing Existing Vendor";
    			
    			if ($vendor->account_type == ACC_TYPE_CLIENT) $subject = "Greek licensing Existing Client";
    			
    			$this->view->key = $key;
    			$this->view->organization_name = $vendor->organization_name;
    			$params = array(
    				'to'=>$formData["email"],
    				'to_name'=>$vendor->organization_name,
    				'from'=>'registration@greeklicensing.com',
    				'from_name'=>'Greek Licensing Registration',
    				'subject'=>$subject,
    				'body'=>$this->view->render('emails/existing-users.phtml')
    			);
    			
    			$send = $this->_helper->BUtilities->send_mail($params);
    			
    			if (!$send){
    				error_log("\nerror in sending new user email", 3, "./errorLog.log");
    			}
    			
                $this->_helper->flashMessenger("An email has been sent to your recovery address contianing a unique key and a link to the next step.", "Info");
                $this->_redirect('signup/email-sent');
    		}
    	}
    	
    	if ($this->view->error != '') $this->view->error .= "<br /><br />";
    }
    
    public function emailSentAction(){
    	
    }
    
    public function existingUserKeyAction(){
    	$this->_helper->JSLibs->load_jquery_validation();
    	$form = new Application_Form_ExistingKey();
    	 
    	$this->view->form = $form;
    	
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		
    		$user = $this->em->getRepository("BL\Entity\User")->findOneBy(array("activation_key"=>$formData["key"], "user_code"=>$formData["number"]));
    		
    		if ($user != null){
    			$this->_redirect('signup/register-user/id/' . $user->id);
    		} else {
    			$this->_helper->flashMessenger("I'm sorry, but the information you have provided is not correct, please try again.", "Info");
    			$this->view->error = ("*I'm sorry, but the information you have provided is not correct, please try again");
    		}
    	}
    }
    
    public function registerUserAction(){
    	$this->_helper->JSLibs->load_jquery_validation();
    	$form = new Application_Form_RegisterUser();
    	
    	$id = $this->_getParam("id");
    	 
    	$vendor = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id' => $id));
    	
    	$this->view->form = $form;
    	
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		
    		if ($form->isValid($formData)){

    			if (!ctype_alnum($formData['username'])){
    				$this->view->error = "<span style='color:red;'>Please enter a username that consists only of letters and numbers (no spaces, or special characters)</span>";
    			} else if ($formData['password'] != $formData['confirm_password']){
    				$this->view->error = "<span style='color:red'>Please enter the same information in both password fields</span>";
    			} else {
	    			$vendorTest = $this->em->getRepository('BL\Entity\User')->findOneBy(array('username'=>$formData['username']));
	    			
	    			$bIsSame = false;
	    			
	    			if ($vendorTest != null){
	    				if ($vendorTest->id == $id) $bIsSame = true;
	    			}
	    			
	    			if (empty($vendorTest) || count($vendorTest) <= 0 || $bIsSame){
	    				$vendor->username = $formData["username"];
	    				$vendor->password = md5($formData["password"]);
	    				$vendor->activation_key = null;
	    				$vendor->reg_status = "activated";
	    				
	    				$role = $this->em->getRepository("BL\Entity\Role")->findOneBy(array('role_name' => 'vendor'));
	    				
	    				if ($vendor->account_type == 3) $role = $this->em->getRepository("BL\Entity\Role")->findOneBy(array('role_name'=>'client'));
	    				if(!isset($vendor->roles))//added if statment	
	    					$vendor->roles->add($role);
	    				
	    				$this->em->persist($vendor);
	    				$this->em->flush();
	    				
	    				$this->_redirect('signup/registered');
	    			} else {
	    				$this->_helper->flashMessenger("I'm sorry, but the username you have provided is already taken, please try again.", "Info");
	    				$this->view->error = "<span style='color:red'>Username has been taken</span>";
	    			}
    			}
    		} else {
    		}
    		
    	}
    }
    
    public function registeredAction(){
    	
    }

    /**
     * Function to Add a Thank you page after the signup completes
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function thankYouAction() {

    }

}
