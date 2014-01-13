<?php

class Vendor_ContactController extends Zend_Controller_Action
{

    public $em;

    public function init()
    {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
        $this->_helper->JSLibs->load_fancy_assets(); //added for vendor sidebar event calendar
		$this->session = new Zend_Session_Namespace('default');
        
        $params = array(
            'account_type' => ACC_TYPE_CLIENT,
            'l_status' => '0',
            'order_by' => 'users.organization_name',
            'vendor_id' => $this->_helper->BUtilities->getLoggedInUser()
        );
        $clients = $this->em->getRepository('BL\Entity\License')->getUnLicensedClients($params);
        $this->organizations = array();
        foreach ($clients as $client) {
            $this->organizations[$client['u_id']] = ' ' . $client['organization_name'] . '           ';
        }

        $product_categories = $this->em->getRepository("BL\Entity\ProductCategory")->findAll();
        $categories = array();
        $categories[''] = 'Select category';
        foreach ($product_categories as $category) {
            $categories[$category->id] = $category->cat_name;
        }

        $all_products = $this->em->getRepository("BL\Entity\Product")->findAll();
        $products = array();
        $products[''] = 'Select product';
        foreach ($all_products as $product) {
            $products[$product->id] = $product->product_name;
        }

        $target_audiences = $this->em->getRepository("BL\Entity\TargetAudience")->findAll();
        $audiences = array();
        foreach ($target_audiences as $audience) {
            $audiences[$audience->id] = ' ' . $audience->name;
        }
        $temp = $audiences[1];
        $audiences[1] = $audiences[sizeof($audiences)];
        $audiences[sizeof($audiences)] = $temp;

        $this->options = array(
            'categories' => $categories,
            'products' => $products,
            'audiences' => $audiences
        );

    }

    /**
     * @author Rasidul
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     */
    public function financialInfoAction(){

        $form = new Vendor_Form_FinancialInfo();
        $this->view->form = $form;
        $vendor_id = $this->_helper->BUtilities->getLoggedInUser();
        $this->view->vendor_id = $vendor_id;
        $user = $this->em->find('BL\Entity\User', $vendor_id);

        $VendorFinancialInfo = $this->em->getRepository('BL\Entity\VendorFinancialInfo')->findOneBy(array('vendor_id' => (int) $vendor_id));
        $application_process = array();
        if(sizeof($VendorFinancialInfo)&&$VendorFinancialInfo->has_account_in_good_standing=='yes'){$application_process[]='1';}
        if(sizeof($VendorFinancialInfo)&&$VendorFinancialInfo->has_closed_financial_statement=='yes'){$application_process[]='2';}
        if(sizeof($VendorFinancialInfo)&&$VendorFinancialInfo->has_chart_of_capital_assets=='yes'){$application_process[]='3';}
        $existing_data = array(
            'application_process' => (sizeof($application_process)?$application_process:''),
            'full_time_employee_num' => (sizeof($VendorFinancialInfo)?$VendorFinancialInfo->full_time_employee_num:''),
            'years_in_business' => (sizeof($VendorFinancialInfo)?$VendorFinancialInfo->years_in_business:''),
            'business_failure_in_5_years' => (sizeof($VendorFinancialInfo)?array($VendorFinancialInfo->business_failure_in_5_years):''),
            'any_person_bankrupt' => (sizeof($VendorFinancialInfo)?array($VendorFinancialInfo->any_person_bankrupt):''),
            'government_investigation' => (sizeof($VendorFinancialInfo)?array($VendorFinancialInfo->government_investigation):''),
            'contract_terminated_in_last_2_years' => (sizeof($VendorFinancialInfo)?array($VendorFinancialInfo->contract_terminated_in_last_2_years):''),
            'litigation_against_the_officers' => (sizeof($VendorFinancialInfo)?array($VendorFinancialInfo->litigation_against_the_officers):''),
            'any_collections_by_debt_collection_agency' => (sizeof($VendorFinancialInfo)?array($VendorFinancialInfo->any_collections_by_debt_collection_agency):''),
            'additional_explanation' => (sizeof($VendorFinancialInfo)?$VendorFinancialInfo->additional_explanation:'')
            );
            //Zend_Debug::dump($existing_data);
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                if(!sizeof($VendorFinancialInfo)){
                    $VendorFinancialInfo = new \BL\Entity\VendorFinancialInfo();
                }

		$arr = array(
		    1	=>  'has_account_in_good_standing',
		    2	=>  'has_closed_financial_statement',
		    3	=>  'has_chart_of_capital_assets'
		);

                if(sizeof($form->getValue('application_process'))){
		    $apps = $form->getValue('application_process');

		    /*
		     * Save checked fields
		     */
		    if(!empty($apps)) {
			foreach ($apps as $app_process) {
			    $VendorFinancialInfo->$arr[$app_process] = 'yes';
			}
		    }

		    /*
		     * Find the missing fields (unchecked checkboxes) and save
		     */
		    $xarr = array_flip(array_diff(array_flip($arr), $apps));
		    if(!empty($xarr)) {
			foreach ($xarr as $xapp_process) {
			    $VendorFinancialInfo->$xapp_process = 'null';
			}
		    }

                }

                $VendorFinancialInfo->full_time_employee_num = $form->getValue('full_time_employee_num');
                $VendorFinancialInfo->years_in_business = $form->getValue('years_in_business');
                $VendorFinancialInfo->business_failure_in_5_years = $form->getValue('business_failure_in_5_years');
                $VendorFinancialInfo->any_person_bankrupt = $form->getValue('any_person_bankrupt');
                $VendorFinancialInfo->government_investigation = $form->getValue('government_investigation');
                $VendorFinancialInfo->contract_terminated_in_last_2_years = $form->getValue('contract_terminated_in_last_2_years');
                $VendorFinancialInfo->litigation_against_the_officers = $form->getValue('litigation_against_the_officers');
                $VendorFinancialInfo->any_collections_by_debt_collection_agency = $form->getValue('any_collections_by_debt_collection_agency');
                $VendorFinancialInfo->additional_explanation = $form->getValue('additional_explanation');
                $VendorFinancialInfo->vendor_id = $user;
                $VendorFinancialInfo->statement = '';
                $VendorFinancialInfo->statement_type = 'financial';
                $this->em->persist($VendorFinancialInfo);
                $this->em->flush();

                $this->_helper->flashMessenger("Financial Information updated succesfully!", "Info");
                $this->_redirect($this->view->BUrl()->absoluteUrl());


            } else {
                // form not valied
            }
        } else{
            $form->populate($existing_data);
        }
    }


    /**
     * @author Rasidul
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     */
    public function productInfoAction(){

        $this->_helper->JSLibs->load_dataTable_assets();

        $vendor_id = $this->_helper->BUtilities->getLoggedInUser();
        $this->view->vendor_id = $vendor_id;
        $user=$this->em->find('BL\Entity\User', $vendor_id);

        $VendorProductAudience = $this->em->getRepository('BL\Entity\VendorProductAudience')->findBy(array('vendor_id' => (int) $vendor_id));

        $vendor_default_audience = array();
        if(sizeof($VendorProductAudience)){
            foreach($VendorProductAudience as $vs){
                $vendor_default_audience[] = $vs->audience_id->id;
            }
        }

        $form = new Vendor_Form_ProductInfo($this->options,$vendor_default_audience);
        $this->view->form = $form;
        $VendorProductInfo = $this->em->getRepository('BL\Entity\VendorProductInfo')->findOneBy(array('vendor_id' => (int) $vendor_id));
        $existing_data = array(
                'supplier_name' => (sizeof($VendorProductInfo)?$VendorProductInfo->supplier_name:''),
                'other_desc' => (sizeof($VendorProductInfo)?$VendorProductInfo->other_desc:'')
            );

        //Zend_Debug::dump($vendor_default_audience);
        //get vendor products
        $this->view->products = $this->em->getRepository('BL\Entity\VendorProductInfoDetails')->getVendorProducts($vendor_id);
        $vendor_default_product = $this->em->getRepository('BL\Entity\VendorProductInfoDetails')->findBy(array('vendor_id' => (int) $vendor_id));
        $this->view->vendor_default_product = $vendor_default_product;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                if(!sizeof($VendorProductInfo)){
                    $VendorProductInfo = new BL\Entity\VendorProductInfo();
                }
                $VendorProductInfo->supplier_name = $form->getValue('supplier_name');
                $VendorProductInfo->other_desc = $form->getValue('other_desc');
                $VendorProductInfo->vendor_id = $user;
                $this->em->persist($VendorProductInfo);
                $this->em->flush();

                //-- saving to vendor_product_infos_dtails table
                $product_array = explode(',',$form->getValue('products'));
                if(sizeof($product_array)){
                    //-- delete existing data 1st (because its ManyToOne relation)
                    $sql = "DELETE FROM BL\Entity\VendorProductInfoDetails s where s.vendor_id = ".$vendor_id;
                    $q = $this->em->createQuery($sql);
                    $q->getResult();

                    foreach($product_array as $product){
                        $class = 'BL\Entity\VendorProductInfoDetails';
                        $VendorProductInfoDetails = new $class();
                        //$VendorProductInfoDetails->product_id=$product;
                        $VendorProductInfoDetails->product_id=$this->em->find('BL\Entity\Product', $product);
                        $VendorProductInfoDetails->vendor_id=$user;
                        $this->em->persist($VendorProductInfoDetails);
                        $this->em->flush();
                    }
                }

                //-- saving to VendorProductAudience table
                if(sizeof($form->getValue('audience'))){
                    //-- delete existing data 1st (because its ManyToOne relation)
                    $sql = "DELETE FROM BL\Entity\VendorProductAudience s where s.vendor_id = ".$vendor_id;
                    $q = $this->em->createQuery($sql);
                    $q->getResult();

                    foreach($form->getValue('audience') as $aud){
                        $class = 'BL\Entity\VendorProductAudience';
                        $VendorProductAudience = new $class();
                        //$VendorProductAudience->audience_id=$aud;
                        $VendorProductAudience->audience_id=$this->em->find('BL\Entity\TargetAudience', $aud);
                        $VendorProductAudience->vendor_id=$user;
                        $this->em->persist($VendorProductAudience);
                        $this->em->flush();
                    }
                }
                //-- sample file update code
                if(isset($formData['pics'])){
                    if(sizeof($formData['pics'])){
                        $i = 0;
                        foreach($formData['pics'] as $product_sample){
                            $class = 'BL\Entity\VendorSampleFile';
                            $VendorSampleFile = new $class();
                            $VendorSampleFile->file_url=$product_sample;
                            $VendorSampleFile->active= -1;
                            $VendorSampleFile->title=$formData['title'][$i];
                            //$VendorSampleFile->product_id=$this->em->find('BL\Entity\Product', $formData['sample_product'][$i]);
                            $VendorSampleFile->file_extension=  substr($product_sample, strpos($product_sample, '.')+1);
                            $VendorSampleFile->upload_date = new DateTime();
                            if($this->getRequest()->getActionName()=='product-info')
                                {
                                $VendorSampleFile->use_for = 'product_info';
                                }
                            $VendorSampleFile->Vendor = $user;
                            $this->em->persist($VendorSampleFile);
                            $this->em->flush();
                            $i++;
                        }
                    }
                }

                $this->_helper->flashMessenger("Product Information updated succesfully!", "Info");
                $this->_redirect($this->view->BUrl()->absoluteUrl());

            } else {
                echo 'Not valid';
            }
        }else{
            $form->populate($existing_data);
        }
    }


    /**
     * @author Rasidul
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     * @param <int> $vendor_id
     * @return <UserContact Entity>
     */
    public function generate_contacts($vendor_id){
        $userOperation2 = $this->em->getRepository('BL\Entity\UserContact')->findBy(array('user_id' => (int) $vendor_id));
        return $userOperation2;
    }


    /**
     * @author Rasidul
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     * @param <int> $vendor_id
     * @return <User Entity>
     */
    public function generate_vendor_info($vendor_id){
        $user2 = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id' => (int) $vendor_id));
        return $user2;
    }

    /**
     * @author Rasidul
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     * @param <user entity object> $user
     * @return <array>
     */
    public function generate_default_info($user){
        if(is_object($user)){
            $existing_data = array(
                'editContact' => "0",
                'v_addNew' => "0",
                'organization_name' => $user->organization_name,
                'address_line_1' => $user->address_line1,
                'address_line_2' => $user->address_line2,
                'phone_1' => $user->phone,
                'phone_2' => $user->phone2,
                'city' => $user->city,
                'state' => $user->state,
                'zip' => $user->zipcode,
                'fax' => $user->fax,
                'web_page' => $user->website,
                'email' => $user->email,
            	'company_email'	=>	$user->company_email
            );
            return $existing_data;
        }
        return false;
    }


    /**
     * @author Rasidul
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     */
    public function vendorProfileAction(){
    	error_log("\nvendorProfileAction()", 3, "./errorLog.log");

        $this->_helper->JSLibs->load_fancy_assets();
        $this->_helper->JSLibs->load_dataTable_assets();

        $vendor_id = $this->_helper->BUtilities->getLoggedInUser();
	
	$user = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id' => (int) $vendor_id));


        $this->view->vendor_id = $vendor_id;
	$VendorProfile = $this->em->getRepository('BL\Entity\VendorProfile')->findBy(array('user_id' => (int) $vendor_id,'active' => '1'),array('update_date' => 'DESC'),1);
	$products= array();
	if($VendorProfile != Null){
		$VendorProfile = $VendorProfile[0];
		$products = explode(",",$VendorProfile->product_offered);
	}
        $vendorSampleFile = $this->em->getRepository('BL\Entity\VendorSampleFile')->findBy(array('Vendor' => (int) $vendor_id,'use_for'=>'web_profile'));
        $this->view->vendorSampleFile = $vendorSampleFile;
        $this->view->vendorSampleFileCount = count($vendorSampleFile);
        $user = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id' => (int) $vendor_id));
        $vendor_service = $this->em->getRepository('BL\Entity\VendorService')->findBy(array('vendor_id' => (int) $vendor_id));
        //$this->view->BUtils()->doctrine_dump($vendor_service);


        $this->view->products = $this->em->getRepository('BL\Entity\VendorWebProfileProducts')->getVendorProducts($vendor_id);
        if(!sizeof($this->view->products)){
            $this->view->products = $this->em->getRepository('BL\Entity\VendorProductInfoDetails')->getVendorProducts($vendor_id);
        }
	
	$this->view->products = array();
	foreach($products as $product){
	if($product != "")
                $this->view->products = array_merge ($this->view->products, $this->em->getRepository('BL\Entity\Product')->findByid($product));

        }

        $vendor_default_service = array();
        if(sizeof($vendor_service)){
            foreach($vendor_service as $vs){
                $vendor_default_service[] = $vs->service_id->id;
            }
        }

//        $this->view->BUtils()->doctrine_dump($VendorProfile);
//        die();

        $this->view->messages = $this->_helper->FlashMessenger->getMessages();
        $this->view->uploadedImages = $this->session->uploadedImages;
	if(sizeof($VendorProfile) > 0 && $VendorProfile->logo_url != '' && file_exists(APPLICATION_PATH.'/../assets/files/vendor_profile/thumbs/'.$VendorProfile->logo_url))
	{
	    $this->view->logo = $VendorProfile->logo_url;
	} else {
	    $this->view->logo = '';
	}



        if(count($VendorProfile)>0){
            $existing_data = array(
                'organization_name' => $VendorProfile->user_id->organization_name,
                'address1' => $VendorProfile->address1,
                'address2' => $VendorProfile->address2,
                'city' => $VendorProfile->city,
                'state' => $VendorProfile->state,
                'email' => $VendorProfile->email,
                'web_page' => $VendorProfile->web_page,
                'phone1' => $VendorProfile->phone1,
                'phone2' => $VendorProfile->phone2,
                'fax' => $VendorProfile->fax,
                'zip' => $VendorProfile->zip,
                'product_offered' => $VendorProfile->product_offered,
                'company_discripction' => $VendorProfile->company_discripction,
		'logo_url' => $VendorProfile->logo_url
            );
        }else{
            reset($existing_data=array());
        }
	$existing_data['organization_name']=$user->organization_name;

        //$services_array = $this->_helper->BUtilities->parseYAML(APPLICATION_PATH . '/configs/services.yml');
        $service_list = $this->em->getRepository("BL\Entity\Service")->findAll();
        foreach ($service_list as $service){
            $service_type[$service['id']] = $service['title'];
        }
        $this->options['service'] = $service_type;
        $this->options['selected_service'] = explode(",",$VendorProfile->services);

        $form = new Vendor_Form_VendorProfile($this->options);

        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            error_log("\nA", 3, "./errorLog.log");
       	    $this->session->uploadedImages = null;
        	
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)){

                if(count($VendorProfile)==0){
                    $class = 'BL\Entity\VendorProfile';
                    $VendorProfile = new $class();
                }
                
                error_log("\nB", 3, "./errorLog.log");

                //-- file(logo) upload code --//
                $destination_dir = APPLICATION_PATH.'/../assets/files/vendor_profile/';
                $extension = null;
                $filename = null;
               
                
                try {
                    $adapter = new Zend_File_Transfer_Adapter_Http();
                    $adapter->setDestination($destination_dir);
                    $files = $adapter->getFileInfo();
                    if (($adapter->isUploaded($files['logo_url']['name'])) && ($adapter->isValid($files['logo_url']['name']))) {
                        $extension = substr($files['logo_url']['name'], strrpos($files['logo_url']['name'], '.') + 1);
                        $filename = 'file_' . date('Ymdhs') . '.' . $extension;
                        $adapter->addFilter('Rename', array('target' => $destination_dir . $filename, 'overwrite' => true));
                        $adapter->receive($files['logo_url']['name']);

                        //-- creating logo thums
                        include("ThirdParty/PhpThumb/ThumbLib.inc.php");
                        $thumb_save_path = $destination_dir . DIRECTORY_SEPARATOR . "thumbs" . DIRECTORY_SEPARATOR . $filename;
                        //$original_copy_save_path = $destination_dir . DIRECTORY_SEPARATOR . $filename;
                        $thumb = PhpThumbFactory::create($destination_dir . DIRECTORY_SEPARATOR . $filename);
                        //$thumb->resize(110, 110)->padding(110, 75, '#FFFFFF');
                        $thumb->resize(250, 250);
			//$thumb->padding(0, 0, '#FFFFFF');
                        $thumb->save($thumb_save_path);
                    }
                } catch (Exception $ex) {
                    echo "Exception!\n";
                    echo $ex->getMessage();
                }


                //--- saving vendor information to DB
		$class = 'BL\Entity\VendorProfile';
                    $VendorProfile = new $class();
                $VendorProfile->user_id=$user;
                $VendorProfile->organization_name=$existing_data['organization_name'];
                $VendorProfile->address1=$form->getValue('address1');
                $VendorProfile->address2=$form->getValue('address2');
                $VendorProfile->city=$form->getValue('city');
                $VendorProfile->state=$form->getValue('state');
                $VendorProfile->email=$form->getValue('email');
                $VendorProfile->web_page=$form->getValue('web_page');
                $VendorProfile->phone1=$form->getValue('phone1');
                $VendorProfile->phone2=$form->getValue('phone2');
                $VendorProfile->fax=$form->getValue('fax');
                $VendorProfile->zip=$form->getValue('zip');
                $VendorProfile->active= -1;
		$VendorProfile->update_date = date('Y-m-d H:i:s');
			if ($form->getValue('services') != null && $form->getValue('services') != "") $VendorProfile->services = implode(",",$form->getValue('services'));
                if($filename!=null){
                    $VendorProfile->logo_url=$filename;
                }
		 else if($form->getValue('use_default') == true){
                    $VendorProfile->logo_url = "OLP_Logo_GRAY_150.jpg";
                }
		else {
			$VendorProfile->logo_url=$this->view->logo;
		}
                if($extension!=null){
                    $VendorProfile->logo_extension=$extension;
                }
                $product_array = explode(',', $form->getValue('products'));
                $VendorProfile->product_offered = implode(",",$product_array);
                $VendorProfile->company_discripction=$form->getValue('company_discripction');
                $VendorProfile->update_date = new DateTime();
                $this->em->persist($VendorProfile);
                $this->em->flush();

                //-- saving to verdor_service table
                if(sizeof($form->getValue('services'))){
                    //-- delete existing data 1st (because its ManyToOne relation)
                    $sql = "DELETE FROM BL\Entity\VendorService s where s.vendor_id = ".$vendor_id;
                    $q = $this->em->createQuery($sql);
                    $q->getResult();

                    foreach($form->getValue('services') as $service){
                        $class = 'BL\Entity\VendorService';
                        $vendorService = new $class();
                        //$vendorService->service_id=$service;
                        $vendorService->service_id=$this->em->find('BL\Entity\Service', $service);
                        $vendorService->vendor_id=$user;
                        $this->em->persist($vendorService);
                        $this->em->flush();
                    }
                }
                
                error_log("\nD", 3, "./errorLog.log");
/*
                //-- saving vendor offered products
                $product_array = explode(',',$form->getValue('products'));
                if(sizeof($product_array)){
                    //-- delete existing data 1st (because its ManyToOne relation)
                    $sql = "DELETE FROM BL\Entity\VendorWebProfileProducts s where s.vendor_id = ".$vendor_id;
                    $q = $this->em->createQuery($sql);
                    $q->getResult();

                    foreach($product_array as $product){
                        $class = 'BL\Entity\VendorWebProfileProducts';
                        $VendorWebProfileProducts = new $class();
                        //$VendorProductInfoDetails->product_id=$product;
                        $VendorWebProfileProducts->product_id=$this->em->find('BL\Entity\Product', $product);
                        $VendorWebProfileProducts->vendor_id=$user;
                        $this->em->persist($VendorWebProfileProducts);
                        $this->em->flush();
                    }
                }
*/
                //-- sample file update code
                
                if(isset($formData['pics'])){
                    $i = 0;
                    foreach($formData['pics'] as $product_sample){
                        $class = 'BL\Entity\VendorSampleFile';
                        $VendorSampleFile = new $class();
                        $VendorSampleFile->file_url=$product_sample;
                        $VendorSampleFile->active= -1;
                        $VendorSampleFile->title=$formData['title'][$i];
                        $VendorSampleFile->file_extension=  substr($product_sample, strpos($product_sample, '.')+1);
                        $VendorSampleFile->upload_date = new DateTime();
                        if($this->getRequest()->getActionName()=='vendor-profile')
                            {
                            $VendorSampleFile->use_for = 'web_profile';
                            }
                        $VendorSampleFile->Vendor = $user;
                        $this->em->persist($VendorSampleFile);
                        $this->em->flush();
                        $i++;
                    }
                }
                
                error_log("\nF", 3, "./errorLog.log");

                //--- send mail to admin
//                $mail = new Zend_Mail();
//                $mail->setBodyText('Dear Admin,'."\n\n".$form->getValue('organization_name').' has changed his web'
//                        .'profile on '.date('m/d/Y').'. Please log into the Admin Portal to review these changes.'
//                        ."\n\n".'The changes must be approved of declined within 1 business day.'."\n\n\n"
//                        .'Regards,'."\n\n".'Greek Licensing Web Admin.')
//                    ->setFrom('web_Admin@greek-licensing.com', 'Web Admin')
//                    //->addTo('rasidul_hasan@yahoo.com', 'Rasidul Hasan')
//                    ->addTo('web_Admin@greek-licensing.com', 'Web Admin')
//                    ->setSubject('Change in Vendor Web Profile')
//                    ->send();

                $params = array(
//                    'to' => 'rasidul_hasan@yahoo.com',
//                    'to_name' => 'Rasidul Hasan',
                    'to' => 'web_Admin@greek-licensing.com',
                    'to_name' => 'Web Admin',
                    'from' => 'web_Admin@greek-licensing.com',
                    'from_name' => 'Web Admin',
                    'subject' => 'Change in Vendor Web Profile',
                    'body' => 'Dear Admin,'."<br/><br/>".$form->getValue('organization_name').' has changed his web'
                        .'profile on '.date('m/d/Y').'. Please log into the Admin Portal to review these changes.'
                        ."<br/><br/>".'The changes must be approved of declined within 1 business day.'."<br/><br/><br/>"
                        .'Regards,'."<br/><br/>".'Greek Licensing Web Admin.'
                    );
                $send = $this->_helper->BUtilities->send_mail($params);
//                if(!$send){
//                }

                //$this->view->message = '@@@@@@@@@@@';
                $this->_helper->flashMessenger("Web Profile update has been requested", "Info");
                $this->_redirect($this->view->BUrl()->absoluteUrl());
            } else {
            	$form->populate($formData);
	            
	            $prods = array();
	            
		    if(isset($formData['products'])&&$formData['products']!="")
	            	$ids = explode(",", $formData['products']);
	            else
			$ids = null;
	            foreach($ids as $id){
	            	
	            	$prods = array_merge ($prods, $this->em->getRepository('BL\Entity\Product')->findByid($id));
	            	
	            }
	            
	            $this->view->products = $prods;
            }
        }else{
            $form->populate($existing_data);
        }
    }


    /**
     * Function to provide JSON data to feed data table
     * @author Rashed
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetSampleProductAction($redirect_url = null) {
        $this->_helper->BUtilities->setNoLayout();
        $redirect_url=$this->_getParam('redirect_url');
        $params = array(
            'search' => $this->_getParam('sSearch', ''),
            'page_start' => $this->_getParam('iDisplayStart', 1),
            'draw_count' => $this->_getParam('sEcho', 1),
            'per_page' => $this->_getParam('iDisplayLength', 10),
        );
        /**
         * Let's take care of the sorting column to be passed to doctrine.
         * DataTable sends params like iSortCol_0.
         */
        $sorting_cols = array('0' => 's.upload_date', '1' => 's.title');
        $params['sort_key'] = $sorting_cols[$this->_getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->_getParam('sSortDir_0');
        $params['vendor_id'] = $this->_getParam('id');
        $params['use_for'] = $this->_getParam('use_for');

        $records = $this->em->getRepository("BL\Entity\VendorSampleFile")->getSample($params)->getResult();
        $params['show_total'] = true;
        $records_total = $this->em->getRepository("BL\Entity\VendorSampleFile")->getSample($params);
        /**
         * Datatable doesn't understand json_encode and have a custom json format
         */
        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":[';
        $first = 0;
        foreach ($records as $v) {
            if ($first++) {
                $json .= ',';
            }
            if($redirect_url!=''){
                $json .= '[
                  "' . $v->upload_date->format("m/d/Y") . '",
                  "' . $v->title . '",
                  "<a href=\"'.$this->view->baseUrl("assets/files/samples/products/").$v->file_url.'\" class=\"fancy\"><img src=\"' . $this->view->baseUrl("assets/files/samples/products/thumbs/") . '' . $v->file_url . '\" height=\"50\" /></a>",
                  "' . '<a class=\"delete\" href=\"' . $this->view->baseUrl("vendor/contact/delete-sample/id/{$v->id}/redirect_url/{$this->_getParam('redirect_url')}") . '\">Delete</a>"
                      ]';
            }
            else{
                $json .= '[
                  "' . $v->upload_date->format("m/d/Y") . '",
                  "' . $v->title . '",
                  "<a href=\"'.$this->view->baseUrl("assets/files/samples/products/").$v->file_url.'\" class=\"fancy\"><img src=\"' . $this->view->baseUrl("assets/files/samples/products/thumbs/") . '' . $v->file_url . '\" height=\"50\" /></a>",
                  "' . '<a class=\"delete\" href=\"' . $this->view->baseUrl("vendor/contact/delete-sample/id/{$v->id}") . '\">Delete</a>"
                      ]';
            }

        }
        $json .= ']}';
        echo $json;
    }


    /**
     * Function to delete a sample product along with all it's resources
     * @author Rashed
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function deleteSampleAction() {
        $banner = $this->em->find("BL\Entity\VendorSampleFile", (int) $this->_getParam('id'));

        @unlink(realpath(APPLICATION_PATH . "/../assets/files/samples/products/{$banner->file_url}"));
        @unlink(realpath(APPLICATION_PATH . "/../assets/files/samples/products/thumbs/{$banner->file_url}"));
        @unlink(realpath(APPLICATION_PATH . "/../assets/files/samples/products/large/{$banner->file_url}"));

        $this->em->remove($banner);
        $this->em->flush();
        $this->em->clear();

        $this->_helper->BUtilities->setNoLayout();
        $this->_helper->flashMessenger("Sample Product Deleted", "Info");
        if($this->_getParam('redirect_url')=='product-info')
            $this->_redirect('vendor/contact/product-info');
        else
            $this->_redirect('vendor/contact/vendor-profile');
    }


    /**
     * Function to Upload multiple Sample files
     * @author Sukhon
     * @copyright Blueliner Marketing
     */
    public function uploadFilesAction() {
        $this->_helper->JSLibs->do_call(array('load_plupload_assets'));
        $this->_helper->BUtilities->setBlankLayout();
    }

    /**
     * Function to Upload multiple Sample files
     * @author Sukhon
     * @copyright Blueliner Marketing
     */
    public function doUploadAction() {
    	error_log("\ndoUploadAction",3, "./errorLog.log");

		if (is_null($this->session->uploadedImages)){
			error_log("\nsetting uploaded images variable", 3, "./errorLog.log");
			$this->session->uploadedImages = array();
		} else {
			foreach($this->session->uploadedImages as $image){
				error_log(" image " . $image["name"],3 , "./errorLog.log");
			}
		}

        /**
         * Todo
         * 4. Add to DB
         */
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        $targetDir = APPLICATION_PATH . '/../assets/files/samples/products/';

        @set_time_limit(10 * 60);

        $chunk = isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0;
        $chunks = isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0;
        $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

        $fileName = preg_replace('/[^\w\._]+/', '', $fileName);

        if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
            $ext = strrpos($fileName, '.');
            $fileName_a = substr($fileName, 0, $ext);
            $fileName_b = substr($fileName, $ext);

            $count = 1;
            while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
                $count++;

            $fileName = $fileName_a . '_' . $count . $fileName_b;
        }

        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0755);
            @mkdir($targetDir . 'thumbs', 0755);
        }

        if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
            $contentType = $_SERVER["HTTP_CONTENT_TYPE"];

        if (isset($_SERVER["CONTENT_TYPE"]))
            $contentType = $_SERVER["CONTENT_TYPE"];

        if (strpos($contentType, "multipart") !== false) {
            if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                // Open temp file
                $out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
                if ($out) {
        			
        			$this->session->uploadedImages[] = array("name"=>$fileName);
        			 
                    // Read binary input stream and append it to temp file
                    $in = fopen($_FILES['file']['tmp_name'], "rb");

                    if ($in) {
                        while ($buff = fread($in, 4096))
                            fwrite($out, $buff);
                    } else
                        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                    fclose($in);
                    fclose($out);

                    /**
                     * Let's create thumbnails here
                     */
                    include("ThirdParty/PhpThumb/ThumbLib.inc.php");
                    $thumb_save_path = $targetDir . DIRECTORY_SEPARATOR . "thumbs" . DIRECTORY_SEPARATOR . $fileName;

                    $thumb = PhpThumbFactory::create($targetDir . DIRECTORY_SEPARATOR . $fileName);
                    $thumb->resize(110, 75);
                    $thumb->save($thumb_save_path);

                    $thumb2_save_path = $targetDir . DIRECTORY_SEPARATOR . "large" . DIRECTORY_SEPARATOR . $fileName;
                    $thumb = PhpThumbFactory::create($targetDir . DIRECTORY_SEPARATOR . $fileName);

                    $thumb->resize(450, 300);
                    $thumb->save($thumb2_save_path);

                    /**
                     * And delete the tmp file
                     */
                    @unlink($_FILES['file']['tmp_name']);
                }
                else
                    die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            } else
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
        } else {
            // Open temp file
            $out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
            if ($out) {
                // Read binary input stream and append it to temp file
                $in = fopen("php://input", "rb");

                if ($in) {
                    while ($buff = fread($in, 4096))
                        fwrite($out, $buff);
                } else
                    die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

                fclose($in);
                fclose($out);
            } else
                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
        }

        die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
    }

    /**
     * Function to function to view vendor contacts informations
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function indexAction()
    {
        $this->_helper->JSLibs->load_fancy_assets();
        $vendor_id = $this->_helper->BUtilities->getLoggedInUser();

        $form = new Vendor_Form_Contact();
        $this->view->form = $form;

        $user = $this->generate_vendor_info($vendor_id);
        $this->view->organization_name = $user->organization_name;
        $existing_data = $this->generate_default_info($user);

        $this->view->userOperation = $this->generate_contacts($vendor_id);
        $this->view->uploadedImaegs = $this->session->uploadedImages;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            //if ($form->isValid($formData)){
            if ($form->isValidPartial($formData)){

                if($form->getValue('editContact')=="1"){

		    $vendorProfile = $this->em->getRepository('BL\Entity\VendorProfile')->findOneBy(array('user_id'=>$vendor_id));
				    $profile = new BL\Entity\UserProfile();
				    	
				    $profile->organization_name = $user->organization_name;
				    $profile->username = $user->username;
				    $profile->address_line1 = $user->address_line1;
				    $profile->address_line2 = $user->address_line2;
				    $profile->city = $user->city;
				    $profile->state = $user->state;
				    $profile->zipcode = $user->zipcode;
				    $profile->email = $user->email;
				    $profile->phone = $user->phone;
				    $profile->phone2 = $user->phone2;
				    $profile->fax = $user->fax;
				    $profile->website = $user->website;
				    $profile->user_code = $user->user_code;
				    $profile->user = $user;
				    $profile->company_email = $user->company_email;
				    	
				    $this->em->persist($profile);
				    $this->em->flush();
				    
                    $user->organization_name = $form->getValue('organization_name');
                    $vendorProfile->organization_name = $form->getValue('organization_name');

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
                    $user->updated_at = new DateTime();
                    $this->em->persist($user);
                    $this->em->persist($vendorProfile);
                    $this->em->flush();
                    $form->reset();
                    $this->em->clear($user);
                    $this->em->clear($vendorProfile);
                    $this->em->remove($vendorProfile);
                    $this->view->success = "Contact updated succesfully!";
                    $this->view->userOperation = $this->generate_contacts($vendor_id);
                    $form->populate($this->generate_default_info($user));

                    $this->_helper->flashMessenger("Contact updated succesfully!", "Info");
                    $this->_redirect($this->view->BUrl()->absoluteUrl());
                }
                if($form->getValue('v_addNew')=="1"){
                    $class = 'BL\Entity\UserContact';
                    $userContact = new $class();
                    $userContact->user_id = $user;
                    $userContact->sal = $form->getValue('v_sal');
                    $userContact->first_name = $form->getValue('v_first_name');
                    $userContact->last_name = $form->getValue('v_last_name');
                    $userContact->title = $form->getValue('v_title');
                    $userContact->phone = $form->getValue('v_phone');
                    $userContact->phone_ext = $form->getValue('v_phone_ext');
                    $userContact->fax = $form->getValue('v_fax');
                    $userContact->email = $form->getValue('v_email');
                    $userContact->mobile = $form->getValue('v_mobile');
                    $userContact->contact_type = $form->getValue('v_contact_type');
                    $this->em->persist($userContact);
                    $this->em->flush();
                    $this->em->clear($userContact);
                    $this->em->remove($userContact);

                    $form->reset();
                    $this->view->success = "New Contact added succesfully!";
                    $this->view->userOperation = $this->generate_contacts($vendor_id);
                    $form->populate($this->generate_default_info($user));

                    $this->_helper->flashMessenger("New Contact added succesfully!", "Info");
                    $this->_redirect($this->view->BUrl()->absoluteUrl());

                }
            }
            if($form->getValue('editContact')=="1"){
                $this->view->editContact = $form->getValue('editContact');
                $form->getElement("editContact")->setValue("0");
            }
            if($form->getValue('v_addNew')=="1"){
                $this->view->v_addNew = $form->getValue('v_addNew');
                $form->populate($this->generate_default_info($this->generate_vendor_info($vendor_id)));
            }
        }
        else{
            $form->populate($existing_data);
        }
    }

    /**
     * Function to edit vendors contact information
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function editContactAction() {
        $contact = new Vendor_Model_Contact($this);
        $contact->editContact();
    }
}

