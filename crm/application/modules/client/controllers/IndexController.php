<?php

class Client_IndexController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
    }

    /**
     * Function to 
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */    
    public function indexAction() {
        $this->_redirect('/client/license/index');
    }
    
    /**
     * Function to Validate on the form using AJAX
     * @author Rashed
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public     
     */
    public function contactAction() {   
        $this->_helper->JSLibs->load_fancy_assets();

        $client = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' => (int) $this->_helper->BUtilities->getLoggedInUser(), 'account_type' => ACC_TYPE_CLIENT));
        $clientProfile = $this->em->getRepository('BL\Entity\ClientProfile')->findOneBy(array('user_id' => (int) $this->_helper->BUtilities->getLoggedInUser()));

        $existing_data = array(
                        'name' => $client->organization_name,
                        //'greek_letters' => $clientLegal->licensor_title,
                        //'description' => $clientProfile->legal_name,
                        'address1' => $client->address_line1,
                        'address2' => $client->address_line2,
                        'city' => $client->city,
                        'state' => ($client->state == '' || $client->state == NULL ? 'NULL' : $client->state),
                        'zip' => $client->zipcode,
                        //'contact_person' => $clientProfile->legal_zipcode,
                        'email' => $client->email,
                        'web_address' => $client->website,
                        'phone' => $client->phone,
                        'fax' => $client->fax
                        );
        if(count($clientProfile)>0){
            $existing_data['description'] = $clientProfile->greek_royalty_description;
            $existing_data['greek_letters'] = $clientProfile->greek_name;
            $existing_data['contact_person'] = $clientProfile->greek_approved_contact_person;
        }

        


//        $states = array();
//        $states_list = $this->em->getRepository("BL\Entity\State")->findAll();
//        foreach ($states_list as $state){
//            $states[$state['abbrev']] = $state['name'];
//        }
//        $form = new Client_Form_Contact($states,($existing_data['state']!=null)?$existing_data['state']:null);
        $form = new Client_Form_Contact();
        $this->view->form = $form;



        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)){
                $client->organization_name = $form->getValue('name');
                $client->address_line1 = $form->getValue('address1');
                $client->address_line2 = $form->getValue('address2');
                $client->city = $form->getValue('city');
                $client->state = $form->getValue('state');
                $client->zipcode = $form->getValue('zip');
                $client->email = $form->getValue('email');
                $client->website = $form->getValue('web_address');
                $client->phone = $form->getValue('phone');
                $client->fax = $form->getValue('fax');                
                $this->em->persist($client);
                $this->em->flush();

                $clientProfile->user_id = $client;
                $clientProfile->greek_royalty_description = $form->getValue('description');
                $clientProfile->greek_name = $form->getValue('greek_letters');
                $clientProfile->greek_approved_contact_person = $form->getValue('contact_person');                
                $this->em->persist($clientProfile);
                $this->em->flush();

                $this->_helper->flashMessenger("Contact update succesfully!", "Info");
                $this->_redirect($this->view->BUrl()->absoluteUrl());

            }
        }else{
            $form->populate($existing_data);
        }
    } 

    /**
     * Function to 
     * @author 
     * @copyright iVive Labs
     * @version 0.1
     * @access public
     * @return String
     */    
    public function webProfileAction() {
        $this->_helper->JSLibs->load_fancy_assets();
        
        $client = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' => (int) $this->_helper->BUtilities->getLoggedInUser(), 'account_type' => ACC_TYPE_CLIENT));
        $clientProfile = $this->em->getRepository('BL\Entity\ClientProfile')->findOneBy(array('user_id' => (int) $this->_helper->BUtilities->getLoggedInUser()));
        $this->view->client = $client;

        $options = array();
        $org_type[''] = 'Select Type';
        $org_list = $this->em->getRepository("BL\Entity\OrganizationType")->findAll();
        foreach ($org_list as $org){
            $org_type[$org['id']] = $org['name'];
        }
        $options['org_type'] = $org_type;
        $options['greek_org_type'] = !is_null($clientProfile->greek_org_type)?$clientProfile->greek_org_type:'0';

        $form = new Client_Form_WebProfile($options);
        $this->view->form = $form;
        
        $existing_data = array(
            'organization_name' => $client->organization_name,
            'address_line1' => $client->address_line1,
            'address_line2' => $client->address_line2,
            'city' => $client->city,
            'state' => $client->state,
            'zip' => $client->zipcode,
            'email' => $client->email,
            'phone1' => $client->phone,
            'phone2' => $client->phone2,
            'fax' => $client->fax,
            'webpage' => $client->website,
        );
        if ($clientProfile) {
            $existing_data['greek_name'] = $clientProfile->greek_name;
            $existing_data['greek_org_type'] = $clientProfile->greek_org_type->id;
            $existing_data['greek_founding_year'] = $clientProfile->greek_founding_year->format('Y-m-d');
            $existing_data['greek_number_of_alumni'] = $clientProfile->greek_number_of_alumni;
            $existing_data['greek_number_of_undergrads'] = $clientProfile->greek_number_of_undergrads;
            $existing_data['greek_number_of_alumni_chapters'] = $clientProfile->greek_number_of_alumni_chapters;
            $existing_data['greek_total_ug_chapters'] = $clientProfile->greek_total_ug_chapters;
            $existing_data['profile_status_update_time'] = $clientProfile->profile_status_update_time;
            $existing_data['symbol'] = $clientProfile->symbol;
            //$existing_data['founding_address'] = $clientProfile->founding_address;
            //$existing_data['founding_address_line1'] = $clientProfile->founding_address_line1;
            //$existing_data['founding_address_line2'] = $clientProfile->founding_address_line2;
            //$existing_data['founding_city'] = $clientProfile->founding_city;
            //$existing_data['founding_state'] = $clientProfile->founding_state;
            $existing_data['headquarters_city'] = $clientProfile->headquarters_city;
            $existing_data['headquarters_state'] = $clientProfile->headquarters_state;
        } else {
            $clientProfile = new \BL\Entity\ClientProfile();
        }

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $this->ajaxValidate($form, $formData);

            if ($form->isValid($formData)) {

                $client->organization_name = $form->getValue('organization_name');
                $client->address_line1 = $form->getValue('address_line1');
                $client->address_line2 = $form->getValue('address_line2');
                $client->city = $form->getValue('city');
                $client->state = $form->getValue('state');
                $client->zipcode = $form->getValue('zip');
                $client->email = $form->getValue('email');
                $client->phone = $form->getValue('phone1');
                $client->phone2 = $form->getValue('phone2');
                $client->fax = $form->getValue('fax');
                $client->website = $form->getValue('webpage');
                $client->updated_at = new DateTime();
                $this->em->persist($client);
                $this->em->flush();

                $clientProfile->greek_name = $form->getValue('greek_name');
                //$clientProfile->greek_org_type = $form->getValue('greek_org_type');
                $clientProfile->greek_org_type = $this->em->find('BL\Entity\OrganizationType', $form->getValue('greek_org_type'));
                $clientProfile->greek_founding_year = new DateTime($form->getValue('greek_founding_year'));
                $clientProfile->greek_number_of_alumni = $form->getValue('greek_number_of_alumni');
                $clientProfile->greek_number_of_undergrads = $form->getValue('greek_number_of_undergrads');
                $clientProfile->greek_number_of_alumni_chapters = $form->getValue('greek_number_of_alumni_chapters');
                $clientProfile->greek_total_ug_chapters = $form->getValue('greek_total_ug_chapters');
                $clientProfile->profile_status_update_time = new DateTime();
                //$clientProfile->symbol = 'N/A';
                //$clientProfile->founding_address = $form->getValue('founding_address_line1') . $form->getValue('founding_address_line2');
                //$clientProfile->founding_address_line1 = $form->getValue('founding_address_line1');
                //$clientProfile->founding_address_line2 = $form->getValue('founding_address_line2');
                //$clientProfile->founding_city = $form->getValue('founding_city');
                //$clientProfile->founding_state = $form->getValue('founding_state');
                $clientProfile->headquarters_city = $form->getValue('headquarters_city');
                $clientProfile->headquarters_state = $form->getValue('headquarters_state');
                $clientProfile->user_id = $client;
                $this->em->persist($clientProfile);
                $this->em->flush();
            } else {
                $form->populate($formData);
            }
        } else {
            $form->populate($existing_data);
        }
    }

    /**
     * Function to change a user's password
     * @author Sukhon added by Masud
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
        $form = new Client_Form_Password();
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
                    $this->_redirect('client/index');
                } else {
                    $form->markAsError();
                    $form->old_password->addError('Old password does not matched');
                }
            }
        }
    }
    
    /**
     * Function to Validate on the form using AJAX
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxValidate($form, $formData) {
        if ($this->_request->isXmlHttpRequest()) {
            if (!$form->isValid($formData)) {
                $json = $form->processAjax($formData);
                echo $json;
                exit(0);
            } else {
                echo Zend_Json::encode(array());
                exit(0);
            }
        }
    }

}

