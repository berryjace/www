<?php

class Vendor_IndexController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
        $this->view->action = $this->getRequest()->getParam('action');
        $this->view->controller = $this->getRequest()->getParam('controller');
        $this->_helper->JSLibs->load_fancy_assets();
    }

    public function indexAction() {
        $this->_redirect('vendor/license');
    }

    /**
     * 
     */
    public function contactAction() {
        $form = new Vendor_Form_ContactEdit();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                
            }
        }
    }
    
    public function helpAction(){
        $this->_helper->JSLibs->load_jqui_assets();
    	$this->view->isHelp = true;
    }

    public function editAction() {
        $this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_jquery_validation();

        $form = new Vendor_Form_Vendor();
        $this->view->form = $form;

        $this->view->messages = $this->_helper->FlashMessenger->getMessages();
        $user_id = $this->_helper->BUtilities->getLoggedInUser();

        $user = $this->em->find("BL\Entity\User", (int) $user_id);
        $userOperation = $this->em->getRepository('BL\Entity\VendorOperation')->findOneBy(array('user_id' => (int) $user_id));
        $this->view->user_details = $user;
        $new_formdata = array();

        $new_formdata['organization_name'] = $user->organization_name;
        //$new_formdata['first_name'] = $profile->first_name;
        //$new_formdata['last_name'] = $profile->last_name;
        $new_formdata['state'] = $user->state;
        $new_formdata['zipcode'] = $user->zipcode;
        $new_formdata['city'] = $user->city;
        $new_formdata['website'] = $user->website;
        $new_formdata['phone'] = $user->phone;
        $new_formdata['address_line1'] = $user->address_line1;
        $new_formdata['fax'] = $user->fax;
        $new_formdata['address_line2'] = $user->address_line2;
        $new_formdata['email'] = $user->email;

        if (isset($userOperation)) {
            $new_formdata['vendor_sale_online'] = $userOperation->vendor_sale_online;
            $new_formdata['vendor_have_storefont'] = $userOperation->vendor_have_storefont;
            $new_formdata['vendor_products'] = $userOperation->vendor_products;
            $new_formdata['vendor_recommendation_to_client'] = $userOperation->vendor_recommendation_to_client;
        } else {
            $userOperation = new BL\Entity\VendorOperation();
        }

        $form->populate($new_formdata);

        if ($this->getRequest()->isPost()) {

            $formData = $this->getRequest()->getPost();

            if ($form->isValid($formData)) {

                $user->organization_name = $form->getValue('organization_name');
                //$user->first_name = $form->getValue('first_name');
                //$user->last_name = $form->getValue('last_name');
                $user->city = $form->getValue('city');
                $user->state = $form->getValue('state');
                $user->zipcode = $form->getValue('zipcode');
                $user->website = $form->getValue('website');
                $user->phone = $form->getValue('phone');
                $user->address_line1 = $form->getValue('address_line1');
                $user->address_line2 = $form->getValue('address_line2');
                $user->fax = $form->getValue('fax');
                $user->email = $form->getValue('email');

                $userOperation->vendor_sale_online = $form->getValue('vendor_sale_online');
                $userOperation->vendor_have_storefont = $form->getValue('vendor_have_storefont');
                $userOperation->vendor_products = $form->getValue('vendor_products');
                $userOperation->vendor_recommendation_to_client = $form->getValue('vendor_recommendation_to_client');
                $userOperation->user_id = $user->id;


                if ($form->getValue('picture') != '') {
                    $upload = $form->picture->getTransferAdapter();
                    if (!$upload->isValid()) {
                        
                    } else {
                        try {
                            include("ThirdParty/PhpThumb/ThumbLib.inc.php");
                            $upload->receive();
                            $uploadedData = $upload->getFileInfo();
                            $uploadedData = $uploadedData["picture"];
                            $file_upload_success = true;
                            $uploaded_name = $user->username . "." . end(explode('.', $uploadedData["name"]));
                            $upload_save_path = $this->view->getHelper("BUtils")->set_filename(APPLICATION_PATH . "/../assets/files/user", $uploaded_name);
                            $thumb_save_path = $this->view->getHelper("BUtils")->set_filename(APPLICATION_PATH . "/../assets/files/user/thumbs", "_thumb" . $uploaded_name);
                            // remove previous logo
                            @unlink($upload_save_path);
                            @unlink($thumb_save_path);
                            /* Let's make a thumb from the temp storage */
                            $thumb = PhpThumbFactory::create($uploadedData["tmp_name"]);
                            $thumb->adaptiveResize(75, 75);
                            $thumb->save($thumb_save_path);
                            /* And make a standard sized image for the main landing Page */
                            $thumb = PhpThumbFactory::create($uploadedData["tmp_name"]);
                            $thumb->resize(960, 700);
                            $thumb->save($upload_save_path);
                            $user->picture = $uploaded_name;
                        } catch (Zend_File_Transfer_Exception $e) {
                            $this->view->custom_err_mssg = $e->getMessage();
                        }
                    }
                }
                //if($user->picture['tmp_name'])
                //$this->view->Helper->BUtils->doctrine_dump($user);
                //print_r($user);die();
                $this->em->persist($user);
                $this->em->persist($userOperation);
                $this->em->flush();
                $this->_helper->messenger('success', 'Profile Updated.');
            } else {
                $form->populate($formData);
            }
        }
    }

    /**
     * Function to change a user's password
     * @author Sukhon
     * @copyright Blueliner Marketing
     * @access public
     * @return String
     */
    public function settingAction() {
        $user_id = $this->_helper->BUtilities->getLoggedInUser();
        $this->view->user_details = $this->em->getRepository("BL\Entity\User")->find((int) $user_id);
        if (!sizeof($this->view->user_details)) {
            throw new Exception('Sorry, but you made an invalid page request', 404);
            exit(0);
        }

        $profile = $this->view->user_details;
        $new_formdata = array();
        $form = new Vendor_Form_Password();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                if ($profile->password == hash('MD5', trim($form->getValue('old_password')))) {
                    $profile->password = hash('MD5', trim($form->getValue('password')));
                    $this->em->persist($profile);
                    $this->em->flush();
                    $this->em->clear();
                    $this->_helper->flashMessenger("Password changed successfully!", "Info");
                    $this->_redirect('vendor/license');
                } else {
                    $form->markAsError();
                    $form->old_password->addError('Old password does not matched');
                }
            }
        }
    }

}
