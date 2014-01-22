<?php


require_once('ThirdParty/tcpdf/tcpdf.php');
class MYPDF extends TCPDF {

    public $info="";
    //Page header
    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
		$this->Cell(0, 10, "License Number : " . $this->info, 0, false, 'C', 0, '', 0, false, 'T', 'M');
		$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
}

class Admin_VendorsController extends Zend_Controller_Action {

    public $vendor = null;
    public $em;

    public function init() {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
        $product_categories = $this->em->getRepository("BL\Entity\ProductCategory")->findAll();
		$this->session = new Zend_Session_Namespace('default');
		
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
     * Function to generate list of category/product/and audience
     * @author Rasidul
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access private
     * @return array
     */
    private function generateList() {

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

    /**
     * Function to get Vendor
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getVendor() {
        $vendor_id = $this->_getParam('id');
		$this->view->vendor_id = $vendor_id;
        $this->vendor = $this->em->getRepository('BL\Entity\User')->findOneBy(array('account_type' => ACC_TYPE_VENDOR, 'id' => $vendor_id));
        if (!sizeof($this->vendor)) {
            throw new Zend_Controller_Action_Exception("Required Parameter Missing or Incorrect", 404);
        }
    }

    /*
     * Action to manually add a license in Vendor
     * @author Bal
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function addLicenseAction()
    {
	$this->_helper->JSLibs->load_jqui_assets();

	$form = new Admin_Form_AddLicense();
        $form2 = new Vendor_Form_Apply3($this->options);

	$clients = $this->em->getRepository('BL\Entity\User')->getClientNames();

	$clientsArr = array();
	foreach($clients as $k=>$client){
	    $clientsArr[$client['id']] = $client['client_greek_name'];
	}
	$targetDir = APPLICATION_PATH . '/../assets/files/licenses/';

	//@mkdir($targetDir, 0755, true);


	$form->getElement('client_id')->addMultiOptions($clientsArr);
	$form->getElement('status_id')->setRegisterInArrayValidator(false);
	$form->getElement('license_file')->setDestination($targetDir);

	$description_array = $this->getHelper('BUtilities')->parseYAML(APPLICATION_PATH . '/configs/royalty_description.yml');

	$this->view->form = $form;
        $this->view->form2 = $form2;

	if($this->_request->isPost()) {
	    if($form->isValid($_POST)) {
			$formData = $form->getValues();
	
			$client = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id'=> (int) $formData['client_id']));
			$vendor = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id'=> (int) $this->_getParam('id')));
	
	
			$license = new \BL\Entity\License;
			$license->client_id = $client;
			$license->vendor_id = $vendor;
			$license->status = $formData['status_id'];
			$license->sharing = $formData['sharing'];
			$license->royalty_commission =  str_replace(array('$','%'), '', $formData['royalty']) ;
			$license->grant_of_license = $formData['grant_of_license'];
			$license->royalty_description = $formData['royalty_description'];
			$license->vendor_type = $formData['vendor_type_id'];
			$license->royalty_structure = $description_array[$formData['vendor_type_id']];
			$license->annual_advance = $license->default_renewal_fee = $formData['advance'];
			$license->license_agree_number = $formData['license_number'];
	
			if($formData['vendor_type_id'] ==1) {
			    $license->royalty_commission_type = '%';
			} else {
			    $license->royalty_commission_type = '$';
			}
	
			if(!empty($formData['license_file'])) {
				$license->file_path = '/assets/files/licenses/'.$formData['license_file'];
			}
			$dt = new \DateTime($formData['agreement_date']);
	
			$license->applied_date = $dt;
			$this->em->persist($license);
			$this->em->flush();
	
			$this->_helper->flashMessenger("License Added Successfully!", "Info");
			$this->_redirect("admin/vendors/lisc-agreements/id/".$vendor->id);
	

	    } else {
			$this->view->status_id = $_POST['status_id'];
	    }
	} else {
		$license_description = "Greek letters of Organization, the crest, badge, name \"[client name]\", seal, flag and Normal Shield, including nicknames and symbols".
				" commonly used by FRATERNITY in trade, as determined by FRATERNITY from time to time. It is understood and agreed that LICENSEE will not ".
				"utilize any variations on any of the foregoing insignia, or any insignia confusingly similar to any of these insignia absent prior written approval from FRATERNITY.";
		
		$royalty_description = "$40 annual advance against royalties owed, paid as an annual minimum guarantee; 8.5% of gross sales (defined as total customer invoice for the full ".
				"value of embellished product(s) less actual shipping charges, taxes or actual returns. Gross sales shal be calculated before applying discounts, rebates, allowances ".
				"or other adjustments, and the gross sales is inclusive of labor charges, design fees or other related charges).";
	
		$license_data = array(
				'grant_of_license'=>$license_description,
				'royalty_description'=>$royalty_description,
				'royalty'=>'8.5%'
		);
		
		$this->view->form->populate($license_data);
	}




    }





    /**
     * Function to search Vendor
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function indexAction() {
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets', 'load_fancy_assets', 'load_jquery_multiselect_assets'));
        $this->view->status_array = $this->_helper->BUtilities->parseYAML(APPLICATION_PATH . '/configs/user_status.yml');
    }

    /**
     * Function to provide JSON data to feed data table
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetVendorsListAction() {
        $this->_helper->BUtilities->setNoLayout();
        $vendor_model = new Admin_Model_Vendors($this);
        echo $vendor_model->getVendorList();
    }

    /**
     * Function to save Invoice data by ajax
     * @author Anil
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxAddInvoiceDateAction()
    {
    	$this->_helper->BUtilities->setNoLayout();
    	if($this->_request->isPost()){
    		$invoice = $this->em->getRepository('BL\Entity\Invoice')
    			->findOneBy(array('id'	=>	 $this->_getParam('invoice_id')));

    		$invoice->email_date .= $this->_getParam('date').',';
    		$this->em->persist($invoice);
    		$this->em->flush();

    		echo Zend_Json::encode(array('code'	=>	'success'));
    	}
    }

    /**
     * Function to View Vendor Profile
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function viewAction() {
        $this->getVendor();
        $this->view->vendor = $this->vendor;
    }

    /**
     * Function to add royalty report for Vendor Via Admin
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     */
    public function addReportAction() {

    }

    /**
     * Function to add royalty report UI
     * @author sukhon
     * @copyright Blueliner Marketing
     * @version 0.1
     */
    public function reportsAction() {
        $this->view->vendor = $this->vendor;
        //$this->view->reports = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSubmissions")->getVendorWithReport(array('status' => $this->_getParam('status', 'pending')));
    }

    /**
     * Function to add royalty report UI
     * @author sukhon
     * @copyright Blueliner Marketing
     * @version 0.1
     */
    public function reportsByAction() {
        $data = array('vendor_id' => $this->_getParam('vendor_id', 0),
            'year' => $this->_getParam('year', ''),
            'status' => $this->_getParam('year', null)
        );
        $this->view->reports = $this->em->getRepository("BL\Entity\Report")->getReportBy($data);
        $this->view->vendor = $this->em->find("BL\Entity\User", (int) $this->_getParam('vendor_id', 0));
    }

    /**
     * Function to licenses list
     * @author sukhon
     * @copyright Blueliner Marketing
     * @version 0.1
     */
    public function licensesAction() {
        $this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_dataTable_assets();
        $this->view->status_array = $this->_helper->BUtilities->parseYAML(APPLICATION_PATH . '/configs/statuses.yml');
        $this->view->a_confirmation = $this->_helper->flashMessenger->getMessages();
        $from_review = $this->_getParam('a_confirm', '');
        if ($from_review != '') {
            if ($from_review == "approved") {
                $this->_helper->flashMessenger->addMessage("The vendor\'s application has been approved and the vendor has been notified via email.", "Approved");
            } else if ($from_review == "declined") {
                $this->_helper->flashMessenger->addMessage("The vendor\'s application has been declined and the vendor has been notified via email.", "Declined");
            }
            $this->_helper->redirector('licenses');
        }
    }

    /**
     * Function to provide JSON data to feed data table
     * @author Sukhon
     * @copyright Blueliner Marketing
     */
    public function ajaxGetLicensedVendorsDtAction() {
        $this->_helper->BUtilities->setNoLayout();
        $vendor_model = new Admin_Model_Vendors($this);
        echo $vendor_model->getLicensedVendors();
    }

    /**
     * Function to provide JSON data of licenses to feed data table
     * @author Sukhon
     * @copyright Blueliner Marketing
     */
    public function ajaxGetLicensesDtAction() {
        $this->_helper->BUtilities->setNoLayout();
        $vendor_model = new Admin_Model_Vendors($this);
        echo $vendor_model->getLicenses();
    }

    /**
     * Function to Search Vendors for Autocomplete
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxSearchVendorAction() {
        $this->_helper->BUtilities->setNoLayout();
        $term = $this->_getParam('term');
        $data = $this->em->getRepository("BL\Entity\User")->getAutocompleteVendors($term);
        echo Zend_Json::encode($data);
    }

    /**
     * Function to
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetInvoiceNumberAction() {
    	error_log("\najaxGetInvoiceNumberAction()", 3, "./errorLog.log");
        $this->_helper->BUtilities->setNoLayout();
        $vendor = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' => $this->_getParam('vendor_id'), 'account_type' => ACC_TYPE_VENDOR));
        $clients = $this->em->getRepository("BL\Entity\User")->findBy(array('account_type' => ACC_TYPE_CLIENT, 'user_status' => 'Current'), array('organization_name' => 'asc'));

        $client_array = array();
        $affinityData = null;
        $affinity2Data = null;
        $bankData = null;
        
        $affinity = $this->em->getRepository("BL\Entity\User")->findOneBy(array('account_type'=>ACC_TYPE_CLIENT, 'organization_name'=>'Affinity Consultants'));
        $affinity2 = $this->em->getRepository("BL\Entity\User")->findOneBy(array('account_type'=>ACC_TYPE_CLIENT, 'organization_name'=>'Affinity Overpayment Refund'));
		$bank = $this->em->getRepository("BL\Entity\User")->findOneBy(array('account_type'=>ACC_TYPE_CLIENT, 'organization_name'=>'Bank Fee'));
        
        if ($affinity != NULL) $affinityData = array('id'=>$affinity->id, 'name'=>$affinity->organization_name);
        if ($affinity2 != NULL) $affinity2Data = array('id'=>$affinity2->id, 'name'=>$affinity2->organization_name);
        if ($bank != NULL) $bankData = array('id'=>$bank->id, 'name'=>$bank->organization_name);
                
        foreach ($clients as $key => $client) {
            $client_array[$client->id] = $client->organization_name;
        }
        
        $inv_num = $this->view->BUtils()->getInvoiceNumber($vendor->id);
        
        $latestInvoice = $this->em->createQuery("SELECT i.id FROM BL\Entity\Invoice i ORDER BY i.id DESC")->setMaxResults(1)->getSingleScalarResult();
        
        $inv_num = "INV_";
        
        for($i =0; $i < (9-strlen($latestInvoice)); $i++){
        	$inv_num .= "0";
        }
        
        $inv_num .= ($latestInvoice + 1);
        
        $vendor_data = array(
            'inv_num' => $inv_num,
            'inv_date' => date('m/d/Y'),
            'email' => $vendor->email,
            'address_line_1' => $vendor->address_line1,
            'address_line_2' => $vendor->address_line2,
            'city' => $vendor->city,
            'state' => $vendor->state,
            'zip' => $vendor->zipcode,
            'phone_1' => $vendor->phone,
            'phone_2' => $vendor->phone2,
            'fax' => $vendor->fax,
            'clients' => $client_array,
        	'affinity' => $affinityData,
        	'affinity2' => $affinity2Data,
        	'bank' => $bankData
        );
        echo Zend_Json::encode($vendor_data);
    }

    /**
     * Function to licenses list
     * @author sukhon
     * @copyright Blueliner Marketing
     * @version 0.1
     */
    public function licenseReviewAction() {
    	error_log("\nlicensereviewaction()", 3, "./errorLog.log");
        $this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_tinymce_assets();
        $this->_helper->JSLibs->load_fancy_assets();
        $form = new Admin_Form_License();
        $license = $this->em->getRepository("BL\Entity\License")->findOneBy(array('id' => (int) $this->_getParam('id', 0)));
        $this->view->license = $license;
        //$this->view->BUtils()->doctrine_dump($license);
        $vendor = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id'=>$license->vendor_id->id));

		$description_array = $this->getHelper('BUtilities')->parseYAML(APPLICATION_PATH . '/configs/royalty_description.yml');

		$samples = $this->em->getRepository('BL\Entity\VendorSampleFile')->findBy(array('Vendor'=>$vendor));
		
		
        $ajax = $this->_getParam('ajax', 0);
        $license_number = 'A' . $license->applied_date->format('ym') . $license->id;
        if ($this->getRequest()->isPost() || $this->_request->isXmlHttpRequest()) {
        	error_log("\nis xmlhttprequest", 3, "./errorLog.log");

            $formData = $this->getRequest()->getPost();
            if ($formData['payment_status'] == 1) {
                $payment_status = 'paid';
            } else {
                $payment_status = 'not-paid';
            }
            $product_sample_link = explode(",", $formData['sample_product_list_link']);
            $send_mail = $this->_getParam('send_mail', 0);
            $email = array(
                'to' => $license->vendor_id->email,
                'to_name' => $license->vendor_id->organization_name,
                'from' => 'Licensing@greeklicensing.com',
                'from_name' => 'Greek Licensing',
                'subject' => $formData['mail_subject_text'],
                'body' => $formData['hidden_mail_body']
            );
            $notification = array(
                'title' => $email['subject'],
                'message' => $email['body'],
                'send_via' => 'site_notification',
                'for_user_type' => 'random',
                'created_by' => $this->_helper->BUtilities->getLoggedInUser(),
                'notification_user' => array('' => $license->vendor_id->id)
            );

            if ($formData['approved'] == "1") {
            	
            	
            	//$vendor->user_status = "Current";
            	
            	//$this->em->persist($vendor);
            	//$this->em->flush();
            	
                $license->admin_sign_date = new DateTime(date('Y-m-d H:i:s'));
                $license->status = '1';
                if ($form->isValid($formData)) {
                    $license->vendor_name = $formData['vendor_name'];
                    $license->client_name = $formData['client_name'];
                    $license->vendor_products = $formData['vendor_products'];
                    $license->royalty_structure = $formData['royalty_structure'];
                    $license->vendor_type = $formData['vendor_type'];
                    $license->royalty_commission = $formData['royalty_commission'];
                    $license->royalty_commission_type = $formData['vendor_type']==1 ? '%' : '$' ;
                    $license->sharing = $formData['sharing'];
                    $license->annual_advance = $formData['annual_advance'];
                    $license->default_renewal_fee = $formData['annual_advance'];
                    $license->royalty_description = $formData['royalty_description'];
                    $license->grant_of_license = $formData['grant_of_license'];
                    $license->sample_status = $formData['sample_status'];
                    $license->payment_status = $payment_status;
                    $license->agreement_statement = $formData['agreement_statement'];
                    $license->license_specific_note = $formData['license_specific_note'];
                    $license->recom_for_client = $formData['recom_for_client'];
                    $license->recom_for_vendor = $formData['recom_for_vendor'];
                    $license->supplier_name = $formData['supplier_name'];
                    $license->target_audience_vendor = $formData['target_audience'];
                    $license->product_sample_link = $formData['sample_product_list_link'];
                    $license->license_agree_number = $license_number;
                    if ($send_mail == 1) {
                        $this->_helper->BUtilities->send_mail($email);
                        $this->_helper->Notification->send_notification($notification);
                        $this->em->persist($license);
                        $this->em->flush();
                        echo Zend_Json::encode(array('error' => false, 'title' => $email['subject'], 'message' => 'Success'));
                    } else {
                        echo Zend_Json::encode(array('error' => false, 'message' => $form->getMessages()));
                    }
                } else {
                    $product_sample_link = explode(",", $formData['sample_product_list_link']);
                    $count = 0;
                    foreach ($product_sample_link as $sp) {
                        $sample_link_saver[$count] = $sp;
                        $sample_link_saver_array_make[$count] = "'" . $sp . "'";
                        $count++;
                    }
                    echo Zend_Json::encode(array('error' => true, 'message' => $form->getMessages()));
                    $form->populate($formData);
                    $this->view->sample_link_saver = $sample_link_saver;
                    $this->view->sample_link_saver_array_make = $sample_link_saver_array_make;
                }
                
                
            } else {
                $license->cancel_date = new DateTime(date('Y-m-d H:i:s'));
                $license->status = '8';
                $license->license_agree_number = $license_number;
                $license->admin_decline_reason = $this->_getParam('decline_reason');
                if ($send_mail == 1) {
                    $this->_helper->BUtilities->send_mail($email);
                    $this->_helper->Notification->send_notification($notification);
                    $this->em->persist($license);
                    $this->em->flush();
                    echo Zend_Json::encode(array('error' => false, 'title' => $email['subject'], 'message' => 'Success'));
                } else {
                    echo Zend_Json::encode(array('error' => false, 'message' => $form->getMessages()));
                }
            }
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout()->disableLayout();


//                if ($formData['sample_file']) {
//                    $class = 'BL\Entity\VendorSpecificFile';
//                    $targetDir = APPLICATION_PATH . '/../assets/files/samples/';
//                    !is_dir($targetDir . '/specific/') ? mkdir($targetDir . '/specific/', '755') : '';
//                    foreach ($formData['sample_file'] as $sample_id) {
//                        $sample = $this->em->find("BL\Entity\VendorSampleFile", (int) $sample_id);
//                        $lic_sample = new $class();
//                        $lic_sample->title = $sample->title;
//                        $lic_sample->file_url = $sample->file_url;
//                        $lic_sample->file_extension = $sample->file_extension;
//                        $lic_sample->License = $license;
//                        $this->em->persist($lic_sample);
//                        copy($targetDir . $sample->file_url, $targetDir . '/specific/' . $sample->file_url);
//                    }
//                    $this->em->flush();
//                    $this->em->clear();
//                }
            //$this->view->samples = $this->em->getRepository("BL\Entity\VendorSpecificFile")->findBy(array('Vendor' => $license->vendor_id, 'use_for' =>'product_info'));
        } else if ($license->status == '3' && $license->save_status == NULL) {

        	error_log("\nelse license status 3", 3, "./errorLog.log");
        	
            /**
             * All the clients might not have an entry in the client profile table.
             * So if it's null, let's insert it first.
             */
            $clinet_profile_check = $this->em->getRepository("BL\Entity\ClientProfile")->findOneBy(array('user_id' => $license->client_id->id));
            $vendor_op = $this->em->getRepository("BL\Entity\VendorOperation")->findOneBy(array('user_id' => $license->vendor_id->id));
            $client_op = $this->em->getRepository("BL\Entity\ClientOperation")->findOneBy(array('user_id' => $license->client_id->id));
            $vendor_products = $this->em->getRepository("BL\Entity\VendorProduct")->findBy(array('license_id' => $license->id));

            $license_template = $this->em->getRepository("BL\Entity\LicenseTemplate")->findOneBy(array('client' => $license->client_id->id, 'has_insurance' => '1'));
            $samples = $this->em->getRepository("BL\Entity\VendorSampleFile")->findBy(array('Vendor' => $license->vendor_id, 'use_for' => 'product_info'));

            if (is_null($license->license_agree_number)) {
                $license_number = 'A' . $license->applied_date->format('ym') . $license->id;
            } else {
                $license_number = $license->license_agree_number;
            }
            if (is_null($clinet_profile_check)) {
                $client_profile = new \BL\Entity\ClientProfile();
                $client_profile->user_id = $license->client_id;
                $this->em->persist($client_profile);
                $this->em->flush();
            }

            if (sizeof($vendor_op)) {
                if (is_null($vendor_op->vendor_reporting_type)) {
                    $form->vendor_type->setMultiOptions(array('1' => 'Type 1: Vendors that pay a royalty fee based on a percentage commission',
                        '2' => 'Type 2: Vendors that pay a royalty fee based on unit sales',
                        '3' => 'Type 3: Vendors that have a unique royalty structure'));
                } else {
                    if ($vendor_op->vendor_reporting_type == "1") {
                        $form->vendor_type->setMultiOptions(array('1' => 'Type 1: Vendors that pay a royalty fee based on a percentage commission',
                            '2' => 'Type 2: Vendors that pay a royalty fee based on unit sales',
                            '3' => 'Type 3: Vendors that have a unique royalty structure'));
                    }
                    if ($vendor_op->vendor_reporting_type == "2") {
                        $form->vendor_type->setMultiOptions(array('2' => 'Type 2: Vendors that pay a royalty fee based on unit sales',
                            '1' => 'Type 1: Vendors that pay a royalty fee based on a percentage commission',
                            '3' => 'Type 3: Vendors that have a unique royalty structure'));
                    }
                    if ($vendor_op->vendor_reporting_type == "3") {
                        $form->vendor_type->setMultiOptions(array('3' => 'Type 3: Vendors that have a unique royalty structure',
                            '1' => 'Type 1: Vendors that pay a royalty fee based on a percentage commission',
                            '2' => 'Type 2: Vendors that pay a royalty fee based on unit sales'
                        ));
                    }
                }
            }
            if (sizeof($client_op) > 0) {
               // $form->recom_for_vendor->setValue($client_op->notes);
            }
            if (sizeof($vendor_op) > 0) {
                $form->royalty_structure->setValue($vendor_op->vendor_royalty_structure);
                $form->recom_for_client->setValue($vendor_op->vendor_recommendation_to_client);
                $form->recom_for_vendor->setValue($vendor_op->default_note_to_vendor);
            }
            $vendor_products_name = "&nbsp;";
            if (sizeof($vendor_products) > 0) {
                $counts = 0;
                foreach ($vendor_products as $vp) {
                    if ($counts == 0) {
                        $vendor_products_name = $vp->product_id->product_name;
                    } else {
                        $vendor_products_name = $vendor_products_name . ", " . $vp->product_id->product_name;
                    }
                    $counts++;
                }
            }
            if (sizeof($license_template) > 0) {
                $form->license_specific_note->setValue(@$license_template->notes);
            } else {
                $master_template = $this->em->getRepository("BL\Entity\MasterTemplate")->findBy(array(), array('id' => 'DESC'), 1);
                $license_template = @$master_template[0];
            }
            if (sizeof($samples) > 0) {
                $count = 0;
                foreach ($samples as $sp) {
                    $sample_link_saver[$count] = $sp->file_url;
                    $sample_link_saver_array_make[$count] = "'" . $sp->file_url . "'";
                    $count++;
                }
            }
            $counts = 0;
            $target_audience = '';
            foreach ($license->TargetAudience as $ta) {
                if ($counts == 0) {
                    $target_audience = $ta->name;
                } else {
                    $target_audience = $target_audience . ", " . $ta->name;
                }
                $counts++;
            }

            $pattern = array('/\[CLIENT_ORG]/', '/\[VENDOR_COMPANY]/', '/\[VENDOR_ADDRESS]/', '/\[CLIENT_ADDRESS]/', '/\[NOW_DATE]/', '/\[LICENSE_NUM]/',
                '/\[GREEK_TRADEMARKS]/', '/\[ROYALTY_DESCRIPTION]/', '/\[CLIENT_LATE_FEE]/', '/\[PRODUCT_DETAIL]/', '/\[CLIENT_STATE]/', '/\[CLIENT_ADDRESS1]/', '/\[VENDOR_ADDRESS1]/');
            $replace = array($license->client_id->organization_name, $license->vendor_id->organization_name,
                $license->vendor_id->address_line1 . ' ' . $license->vendor_id->address_line2 . '<br />' . $license->vendor_id->city . ' ' . $license->vendor_id->state . ' ' . $license->vendor_id->zipcode,
                $license->client_id->address_line1 . ' ' . $license->client_id->address_line2 . '<br />' . $license->client_id->city . ' ' . $license->client_id->state . ' ' . $license->client_id->zipcode,
                date('d') . ' of ' . date('M') . ', ' . date('Y'), $license_number, $clinet_profile_check->greek_grant_of_license,
                '<span class="commission">$__' . $clinet_profile_check->greek_royalty_description . ' </span>',
                '<span class="latefee">$__' . $clinet_profile_check->greek_default_late_fee . '</span>', '<span class="v_product">' . @$vendor_products_name . '</span>', $license->client_id->state,
                $license->client_id->address_line1 . ' ' . $license->client_id->address_line2 . ' ' . $license->client_id->city . ' ' . $license->client_id->state . ' ' . $license->client_id->zipcode, $license->vendor_id->address_line1 . ' ' . $license->vendor_id->address_line2 . ' ' . $license->vendor_id->city . ' ' . $license->vendor_id->state . ' ' . $license->vendor_id->zipcode);
//'<span class="commission">commission: $__' . $clinet_profile_check->greek_default_renewal_fee . ' Renewal Fee: $__' . $clinet_profile_check->annual_advance . '</span>',
            $template = preg_replace($pattern, $replace, $license_template->template);
            $template = str_replace('$__', '$', $template);

            $royalty_description = $clinet_profile_check->greek_royalty_description; //"$40 renewal fee against royalties owed, paid as an annual minimum guarantee; 8.5% of gross sales (defined as total customer invoice for the full value of the embellished product(s) less actual shipping charges, taxes or actual returns. Gross sales shall be calculated before applying discounts, rebates, allowances or other adjustments, and the gross sales is inclusive of labor charges, design fees or other related charges).";
            $start_pos_ann_adv = strpos($royalty_description, '$');
            $start_pos_rlt_com = strpos($royalty_description, '%');
            $rlt_adv = '';
            $rlt_com = '';

            /**
             * for royalty advance
             */

	    if($start_pos_ann_adv !== FALSE) {
		for ($i = $start_pos_ann_adv; $i < strlen($royalty_description); $i++) {
		    if ($royalty_description[$i] == ' ') {
			break;
		    } else {
			$rlt_adv .= $royalty_description[$i];
		    }
		}
	    }
            /**
             * For roaylty comission
             */
	    if($start_pos_rlt_com !== FALSE) {
		for ($i = ($start_pos_rlt_com - 3); $i <= $start_pos_rlt_com; $i++) {
		    $rlt_com .= $royalty_description[$i];
		}
	    }
            $this->view->rlt_adv = !empty($rlt_adv) ? $rlt_adv : 'N/A';
            $this->view->rlt_com = !empty($rlt_com) ? $rlt_com : 'N/A';

//            echo $rlt_adv. " ".$rlt_com;
//            die('----');

            
            $has_samples = $license->sample_status;
            
            if ($license->sample_status == 0){
            	if (count($samples) > 0) $has_samples = 1;
            }
            
            $payment_status = $license->payment_status;
            
           	if ($license->payment_status == 'not_charged') $payment_status = "paid";
           	
           	error_log("\npayment status " . $payment_status, 3, "./errorLog.log");
           	
           	if ($payment_status == "paid") $payment_status = 1;
           	else $payment_status = 0;
            
           	error_log("\ngreek royalty description" . $clinet_profile_check->greek_royalty_description, 3, "./errorLog.log");
           	
            $form->agreement_statement->setValue($template);
            $form->vendor_products->setValue($vendor_products_name);
            $form->vendor_name->setValue($license->vendor_id->organization_name);
            $form->sample_status->setValue($has_samples);
            $form->payment_status->setValue($payment_status);
            $form->client_name->setValue($license->client_id->organization_name);
            $form->royalty_description->setValue($clinet_profile_check->greek_royalty_description);
            $form->grant_of_license->setValue($clinet_profile_check->greek_grant_of_license);
            $form->annual_advance->setValue($clinet_profile_check->greek_default_renewal_fee); //annual_advance);
            $form->supplier_name->setValue($license->supplier_name);
            $form->license_number->setValue($license_number);
            $form->target_audience->setValue($target_audience);

            if (sizeof($samples) > 0) {
                $this->view->sample_link_saver = $sample_link_saver;
                $this->view->sample_link_saver_array_make = $sample_link_saver_array_make;
                $form->sample_status->setChecked(true);
            }
            if ($license->payment_status == 'paid') {
                $form->payment_status->setChecked(true);
            }


//            $products = $this->em->getRepository("BL\Entity\VendorProduct")->findBy(array('license_id' => $license->id));
//            if (sizeof($products)) {
//                foreach ($products as $product) {
//                    //$this->view->BUtils()->doctrine_dump($product->product_id->id,1);
//                    @$vendor_product .= '<tr><td>' . $license->vendor_id->id . $product->license_id->id . $product->product_id->id . '</td><td>' . $product->product_id->product_name . '</td></tr>';
//                }
//                $vendor_product = '<table width="400">
//                    <thead>
//                    <tr><td width="30%"><span style="text-decoration: underline;">Product Number</span></td>
//                    <td><span style="text-decoration: underline;">Product Description</span></td>
//                    </tr></thead>
//                    <tbody>' . @$vendor_product . '</tbody>
//                    </table>';
//            }
        } else {
        	error_log("\nelse", 3, "./errorLog.log");

            //$this->view->BUtils()->doctrine_dump($license->vendor_id);
            $form->vendor_name->setValue($license->vendor_id->organization_name);
            $form->sample_status->setValue($license->sample_status);
            $form->sharing->setValue($license->sharing);
            $form->vendor_type->setValue($license->vendor_type);
            $form->royalty_commission->setValue($license->royalty_commission);
            $form->payment_status->setValue($license->payment_status);
            $form->vendor_products->setValue($license->vendor_products);
            $form->royalty_structure->setValue($license->royalty_structure);
            $form->recom_for_client->setValue($license->recom_for_client);
            $form->client_name->setValue($license->client_id->organization_name);
            $form->royalty_description->setValue($license->royalty_description);
            $form->grant_of_license->setValue($license->grant_of_license);
            $form->annual_advance->setValue($license->annual_advance);
            $form->recom_for_vendor->setValue($license->recom_for_vendor);
            $form->license_number->setValue($license->license_agree_number);
            $form->recom_for_vendor->setValue($license->recom_for_vendor);
            $form->agreement_statement->setValue($license->agreement_statement);
            $form->license_specific_note->setValue($license->license_specific_note);
            $form->supplier_name->setValue($license->supplier_name);
            $form->target_audience->setValue($license->target_audience_vendor);

            if (is_null($license->product_sample_link)) {
                $this->view->sample_link_saver = '';
                $this->view->sample_link_saver_array_make = NULL;
            } else {
                $product_sample_link = explode(",", $license->product_sample_link);
                $count = 0;
                foreach ($product_sample_link as $sp) {
                    $sample_link_saver[$count] = $sp;
                    $sample_link_saver_array_make[$count] = "'" . $sp . "'";
                    $count++;
                }
                $this->view->sample_link_saver = $sample_link_saver;
                $this->view->sample_link_saver_array_make = $sample_link_saver_array_make;
            }
            $product_sample_link = explode(",", $license->product_sample_link);
            $count = 0;
            foreach ($product_sample_link as $sp) {
                $sample_link_saver[$count] = $sp;
                $sample_link_saver_array_make[$count] = "'" . $sp . "'";
                $count++;
            }



            if (sizeof($product_sample_link) > 0) {
                $form->sample_status->setChecked(true);
            }
            if ($license->payment_status == 'paid') {
                $form->payment_status->setChecked(true);
            }
        }



	if(empty($license->royalty_structure)) {
	    $form->getElement('royalty_structure')->setValue($description_array[1]);
	}

        $this->view->form = $form;
    }

  	public function ajaxSaveLicenseNotesAction(){
  		
  		$this->_helper->BUtilities->setNoLayout();

  		$license_id = $this->_getParam('id');
  		$notes = $this->_getParam('notes');
  		
  		$license = $this->em->getRepository('BL\Entity\License')->findOneBy(array('id'=>$license_id));
  		
  		$license->license_specific_note = $notes;
  		
  		$this->em->persist($license);
  		$this->em->flush();
  		
  		echo Zend_Json::encode(array("code" => "success", "success" => "true"));
  	}
  	
    public function ajaxSaveAgreementAction(){
    	error_log("\najaxSaveAgreementAction()", 3, "./errorLog.log");
    	$this->_helper->BUtilities->setNoLayout();
    	
    	$vendor_id = $this->_getParam('vendor_id');
    	$license_number = $this->_getParam('license_number');
    	$oldFileName = $this->_getParam('upload_file_name');
    	$oldFileNameArray = explode(',', $oldFileName);
    	
    	$license = $this->em->getRepository('BL\Entity\License')->findOneBy(array('id'=>$license_number));
    	
    	if (empty($vendor_id)) $vendor_id = $this->_helper->BUtilities->getLoggedInUser();
    	
    	$targetDir = APPLICATION_PATH . '/../assets/files/licenses/';
    	$url = '/assets/files/licenses/';
    	
    	$index = 0;
    	
    	foreach($oldFileNameArray as $oldFile){
    		error_log("\nsaving file for license: " . $license->id, 3, "./errorLog.log");
    		$ext = pathinfo($oldFile, PATHINFO_EXTENSION);
    		
    		$newName = $license_number . '_' . (++$index) . "." . $ext;
    		rename($targetDir . $oldFile, $targetDir . $newName);
    		
    		$license->file_path = '/assets/files/licenses/' . $newName;
    		
    		$this->em->persist($license);
    		$this->em->flush();
    	}
		error_log("\n end of function", 3, "./errorLog.log");
    	echo Zend_Json::encode(array("code" => "success", "success" => "true"));
    }
    
    public function ajaxDeleteAgreementAction(){
    	error_log("\najaxDeleteAgreementAction()", 3, "./errorLog.log");
    	$this->_helper->BUtilities->setNoLayout();
    	
    	//unlink() - deletes file at supplied path
    	
    	$licenseID = $this->_getParam('license_id');
    	
    	$license = $this->em->getRepository('BL\Entity\License')->findOneBy(array('id'=>$licenseID));
    	
    	$path = $license->file_path;
    	
    	$paths = explode('/', $path);
    	
    	$path= $paths[count($paths)-1];
    	
    	$path = APPLICATION_PATH . '/../assets/files/licenses/' . $path;
    	
    	error_log("\nPath: " . $path, 3, "./errorLog.log");
    	
    	if (unlink($path)){
	    	$license->file_path = "";
	    	
	    	$this->em->persist($license);
	    	$this->em->flush();
	    	
	    	echo Zend_Json::encode(array("code"=>"success", "success"=>"true"));
    	} else {
    		echo Zend_Json::encode(array("code"=>"failure", "success"=>"false"));
    	}
    }
    
    /**
     * Function to get the image
     * @author Zea
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access pulic
     * @param <string> image link
     * @return void
     */
    public function getImageAction() {
        $this->_helper->BUtilities->setAjaxLayout();
        $this->_helper->JSLibs->load_jqui_assets();
        $this->view->imageLink = $this->_getParam('link');
        // echo $this->_getParam('link');
    }

    public function operationsAction() {
        $this->view->vendor = $this->getVendor();
        $vendor_model = new Admin_Model_Vendors($this);
        $vendor_model->getOperations();
    }

    public function clientsAction() {
        $this->getVendor();
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets', 'load_jqui_aristo', 'load_fancy_assets'));
        $this->view->vendor = $this->vendor;
    }

    /**
     * Function to provide JSON data to feed data table
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetClientsAction() {
        $this->_helper->BUtilities->setNoLayout();
        $search_model = new Admin_Model_Search($this);
        echo $search_model->getClients();
    }

    /**
     * Function to provide JSON data to feed data table
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetCorrespondenceAction() {
        $this->_helper->BUtilities->setNoLayout();
        $search_model = new Admin_Model_Search($this);
        echo $search_model->getCorrespondence();
    }



    public function ajaxGetClientInfoAction()
    {
	$this->_helper->BUtilities->setNoLayout();
	$id = $this->_getParam('id');

	$client = $this->em->getRepository('BL\Entity\ClientProfile')->findOneBy(array('user_id'=>  $id));

	$fld = $this->_getParam('fld');

	$json = array();
	if(is_array($fld)) {
	    foreach($fld as $val) {
		$json[$val] = $client->$val;
	    }
	} else {
	    $json[$fld] = $client->$fld;
	}

	echo Zend_Json::encode($json);
    }




    /**
     * Function to
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetCorrespondenceDetailsAction() {
        $this->_helper->BUtilities->setNoLayout();
        $note_id = $this->_getParam('note-id', 0);
        $note = $this->em->find('BL\Entity\VendorCorrespondence', (int) $note_id);
        if ($note->subject == "") {
            echo $note->note;
        } else {
            echo "<b>" . $note->subject . "</b><br />" . $note->note;
        }
    }

    /**
     * Function to
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetDocsDetailsAction() {
        $this->_helper->BUtilities->setNoLayout();
        $docs_id = $this->_getParam('docs-id', 0);
        $docs = $this->em->find('BL\Entity\VendorDocs', (int) $docs_id);
        if ($docs->update_date->format("M d, Y H:i A") == "") {
            echo (($docs->doc_name != NULL || $docs->doc_name != "") ? $docs->doc_name : 'N/A');
        } else {
            if (!is_null($docs->update_date)) {
                $update_date = ( (int) $docs->update_date->format("Y") > 0 ? $docs->update_date->format("M d, Y H:i A") : 'N/A');
            } else {
                $update_date = 'N/A';
            }
            echo "<b>" . $update_date . "</b><br /><b>Type: </b>" . (($docs->doc_type != NULL || $docs->doc_type != "") ? $docs->doc_type : 'N/A') . "<br /><b>Document Name: </b>" . (($docs->doc_name != NULL || $docs->doc_name != "") ? $docs->doc_name : 'N/A');
        }
    }

    /**
     * Function to add and view vendor correspondence
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function correspondenceAction() {
        $this->getVendor();
        $this->_helper->JSLibs->load_fancy_assets();
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets'));
        $this->view->vendor = $this->vendor;
        $form = new Admin_Form_VendorCorrespondence();
        $this->view->form = $form;
    }

    /**
     * Function to show correspondence details
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function correspondenceDetailsAction() {
        $this->_helper->BUtilities->setEmptyLayout();
        $correspondence = $this->em->getRepository('BL\Entity\VendorCorrespondence')->findOneBy(array('id' => $this->_getParam('id')));
        $this->view->correspondence = $correspondence;
    }

    /**
     * Function to show license agreements
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function liscAgreementsAction() {
        $this->getVendor();
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets'));
        $this->view->vendor = $this->vendor;
    }

    /**
     * Function to provide JSON data to feed data table
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetLicenseAgreementsAction() {
        $this->_helper->BUtilities->setNoLayout();
        $search_model = new Admin_Model_Search($this);
        echo $search_model->getLicenseAgreements();
    }

    /*
     * Function
     */
    public function ajaxGetLicenseStatusesAction()
    {
	$this->_helper->BUtilities->setNoLayout();
	$status_array = $this->getHelper('BUtilities')->parseYAML(APPLICATION_PATH . '/configs/statuses.yml');
	echo json_encode($status_array);
    }

        /**
     * Function to show actions
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function actionsAction() {
        $this->getVendor();
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets'));
        $this->view->vendor = $this->vendor;

        $clients = array("" => "Select");
        $clients_list = $this->em->getRepository("BL\Entity\User")->getClientNames();
        foreach ($clients_list as $client) {
            $clients[$client['id']] = $client['client_greek_name'];
        }
        $form = new Admin_Form_VendorActions($clients);
        $this->view->form = $form;
    }

    /**
     * Function to provide JSON data to feed data table
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetActionsAction() {
        $this->_helper->BUtilities->setNoLayout();
        $search_model = new Admin_Model_Search($this);
        echo $search_model->getActions();
    }



    /**
     * Function to show Vendor Payments
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function vendorPaymentsAction() {
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets', 'load_fancy_assets'));
        $this->getVendor();
        $this->view->vendor = $this->vendor;
    }

    /**
     * Function to fet Vendor specific payments records for ajax calls
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetVendorPaymentsAction() {
        $this->_helper->BUtilities->setNoLayout();
        $vendors_model = new Admin_Model_Vendors($this);
        echo $vendors_model->getVendorPayments();
    }

    /**
     * Function to show lineitems for payments
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function showVendorPaymentLineItemsAction() {
        $this->_helper->BUtilities->setEmptyLayout();
        $items = $this->em->getRepository('BL\Entity\PaymentLineItems')->getLineItemsByPayment($this->_getParam('id'));;
        $this->view->items = $items;
    }
    /**
     * edit the payment detail
     */
    public function updateVendorPaymentLineItemsAction() {
        $id=$this->_getParam('id');
        $late_paid = $this->_getParam('late_paid');
        $amount_paid = $this->_getParam('amount_paid');
        $adv_paid = $this->_getParam('adv_paid');
        $sharing = $this->_getParam('sharing');
        $percent_amc = $this->_getParam('percent_amc');
        $existingPayment = $this->em->getRepository('BL\Entity\PaymentLineItems')->findOneBy(array('id' => $id));
        if($existingPayment->late_paid)
        {
           $oldAmount = $existingPayment->late_paid;
        }
        elseif($existingPayment->amount_paid)
        {
           $oldAmount = $existingPayment->amount_paid;
        }
        elseif($existingPayment->adv_paid)
        {
           $oldAmount = $existingPayment->adv_paid;
        }
        else
        {
            $oldAmount=0;
        }
        $existingPayment->late_paid = $late_paid ? $late_paid : '';
        $existingPayment->amount_paid = $amount_paid ? $amount_paid : '';
        $existingPayment->adv_paid = $adv_paid ? $adv_paid : '';
        $existingPayment->sharing = strtolower($sharing) =='yes' ? 1 : 0;
        $existingPayment->percent_amc = $percent_amc;
        $this->em->persist($existingPayment);
        $this->em->flush();

        $payment = $this->em->getRepository('BL\Entity\Payment')->findOneBy(array('id' => $existingPayment->pmt_id));
        if($late_paid)
        {
           $amount= $late_paid;
        }
        elseif($amount_paid)
        {
           $amount= $amount_paid;
        }
        elseif($adv_paid)
        {
           $amount= $adv_paid;
        }
        else
        {
            $amount=0;
        }
        $payment->amount_paid = $payment->amount_paid - $oldAmount + $amount;
        $payment->amount_remaining = $payment->amount_remaining + $oldAmount - $amount;
        $this->em->persist($payment);
        $this->em->flush();
        $amcAmount=0;
        $subTotal=0;
        if (($existingPayment->amount_paid) && $existingPayment->pmt_id->invoice->invoice_type!='Refund')
         {
            if($existingPayment->sharing==1)
            {
             $amcAmount=($existingPayment->amount_paid * $existingPayment->percent_amc);
            }
            $subTotal=$existingPayment->amount_paid - $amcAmount;
         }
         elseif (($existingPayment->late_paid) && $existingPayment->pmt_id->invoice->invoice_type!='Refund')
         {
             if($existingPayment->sharing==1)
            {
             $amcAmount=($existingPayment->late_paid * $existingPayment->percent_amc);
            }
            $subTotal=$existingPayment->late_paid - $amcAmount;
         }
         elseif (($existingPayment->adv_paid) && $existingPayment->pmt_id->invoice->invoice_type!='Refund')
         {
             if($existingPayment->sharing==1)
            {
             $amcAmount=($existingPayment->adv_paid * $existingPayment->percent_amc);
            }
            $subTotal=$existingPayment->adv_paid - $amcAmount;
         }
     else
         {
             $amcAmount=0;
             $subTotal=0;
         }
         echo Zend_Json::encode(array('success' => true, 'message' => 'Payment updated successfully.', 'amcAmount'=> $amcAmount, 'subTotal'=> $subTotal));
         exit;

    }
    /**
     * Function to provide JSON data to feed data table
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function notesAction() {
        $this->getVendor();
        $this->_helper->JSLibs->load_fancy_assets();
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets'));
        $this->view->vendor = $this->vendor;
        $form = new Admin_Form_VendorNotes();
        $this->view->form = $form;
    }

    /**
     * Function to Save Notes via AJAX
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxSaveNotesAction() {
        $this->_helper->BUtilities->setNoLayout();
        $vendor_id = $this->_getParam('vid');
        $note_id = $this->_getParam('note_id', 0);
        $note = $this->_getParam('description');
        if ($note_id != '' && $note_id != NULL && $note_id != 0) {
            $new_note = $this->em->getRepository("BL\Entity\VendorNote")->findOneBy(array('id' => (int) $note_id, 'vendor_id' => (int) $vendor_id));
        } else {
            $new_note = new \BL\Entity\VendorNote();
            $new_note->vendor = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' => (int) $vendor_id));
            $new_note->created_at = new \DateTime(date("Y-m-d H:i:s"));
        }
        $new_note->note = $note;
        $new_note->updated_at = new \DateTime(date("Y-m-d H:i:s"));

        $this->em->persist($new_note);
        $this->em->flush();
        if ($new_note->id) {
            echo Zend_Json::encode(array('code' => 'success', 'msg' => 'Successfully Added the Note'));
        } else {
            echo Zend_Json::encode(array('code' => 'error', 'msg' => 'There was a problem adding the Note'));
        }
    }

    /**
     * Function to provide JSON data to feed data table
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetNotesAction() {
        $this->_helper->BUtilities->setNoLayout();
        $search_model = new Admin_Model_Search($this);
        echo $search_model->getNotes();
    }

    /**
     * Function to
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function notesDetailsAction() {
        $this->_helper->BUtilities->setEmptyLayout();
        $note_id = $this->_getParam('note-id', 0);
        $this->view->vendor_notes = $this->em->find('BL\Entity\VendorNote', (int) $note_id);
        //$note = $this->em->find('BL\Entity\VendorNote', (int) $note_id);
        //echo $note->note;
    }

    /**
     * Function to edit vendor notes
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function notesEditAction() {
        $this->_helper->BUtilities->setPopupLayout();
        $vendor_model = new Admin_Model_Vendors($this);
        $vendor_model->notesEdit();
    }

    /**
     * Function to Save Notes via AJAX
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxSaveDocsAction() {
        $this->_helper->BUtilities->setNoLayout();
        $vendor_id = $this->_getParam('vid');
        $docs_id = $this->_getParam('docs_id', 0);
        $doc_name = $this->_getParam('file_name');
        if ($docs_id) {
            $new_docs = $this->em->find('BL\Entity\VendorDocs', (int) $docs_id);
        } else {
            $new_docs = new \BL\Entity\VendorDocs();
        }

        $destination_dir = APPLICATION_PATH . '/../assets/files/vendor_documents/';
        try {
            die('zzzzz');
            $adapter = new Zend_File_Transfer_Adapter_Http();
            $adapter->setDestination($destination_dir);
            $files = $adapter->getFileInfo();
            if (($adapter->isUploaded($files['upload_doc']['name'])) && ($adapter->isValid($files['upload_doc']['name']))) {
                $extension = substr($files['upload_doc']['name'], strrpos($files['upload_doc']['name'], '.') + 1);
                $filename = 'file_' . $vendor_id . '_' . date('Ymdhs') . '.' . $extension;
                $adapter->addFilter('Rename', array('target' => $destination_dir . $filename, 'overwrite' => true));
                $adapter->receive($files['logo_url']['name']);
            }
        } catch (Exception $ex) {
            echo Zend_Json::encode(array('code' => 'error', 'msg' => $ex->getMessage()));
        }

        $new_docs->doc_name = $doc_name;
        $new_docs->doc_url = (!is_null($filename) ? $filename : NULL);
        $new_docs->doc_type = (!is_null($extension) ? $extension : NULL);
        $new_docs->update_date = new DateTime();
        $new_docs->doc_time = new DateTime();
        $new_docs->vendor_id = $this->em->find('BL\Entity\User', (int) $vendor_id);

        $this->em->persist($new_docs);
        $this->em->flush();
        $this->em->clear();
        if ($new_docs->id) {
            echo Zend_Json::encode(array('code' => 'success', 'msg' => 'Successfully Added the Documents'));
        } else {
            echo Zend_Json::encode(array('code' => 'error', 'msg' => 'There was a problem adding the Documents'));
        }
    }

    /**
     * Function to Save Notes via AJAX
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxSaveCorrespondenceAction() {
        $this->_helper->BUtilities->setNoLayout();
        $vendor_id = $this->_getParam('vid');
        $note_id = $this->_getParam('note_id', 0);
        $note = $this->_getParam('note');
        $subject = $this->_getParam('subject');
        if ($note_id) {
            $new_note = $this->em->find('BL\Entity\VendorCorrespondence', (int) $note_id);
        } else {
            $new_note = new \BL\Entity\VendorCorrespondence();
        }
        $new_note->note = $note;
        $new_note->subject = $subject;
        $new_note->note_time = new DateTime();
        $new_note->vendor_id = $this->em->find('BL\Entity\User', (int) $vendor_id);
        $this->em->persist($new_note);
        $this->em->flush();
        if ($new_note->id) {
            echo Zend_Json::encode(array('code' => 'success', 'msg' => 'Successfully Added the note'));
        } else {
            echo Zend_Json::encode(array('code' => 'error', 'msg' => 'There was a problem adding the note'));
        }
    }

    /**
     * Function to
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function editCorrespondenceAction() {
        $this->_helper->BUtilities->setEmptyLayout();
        $this->_helper->JSLibs->do_call(array('load_jqui_timepicker'));
        $correspondence = $this->em->getRepository('BL\Entity\VendorCorrespondence')->findOneBy(array('id' => (int) $this->_getParam('id')));
//        $this->view->BUtils()->doctrine_dump($correspondence);
        $form = new Admin_Form_VendorCorrespondence();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $correspondence->subject = $form->getValue('subject');
                $correspondence->note = $form->getValue('note');
//                $correspondence->note_time = new DateTime(date('y-m-d h:i:s', strtotime($form->getValue('note_time') . " " . date('h:i:s'))));
                $correspondence->note_time = new DateTime(date('Y-m-d H:i:s', strtotime($form->getValue('note_time'))));
                $this->update_date = new DateTime(date("Y-m-d H:i:s"));
                $this->em->persist($correspondence);
                $this->em->flush();
                $this->view->message = 'Successfully updated';
            } else {
                $form->populate($formData);
            }
        } else {
            $existing_data = array(
                'subject' => $correspondence->subject,
                'note' => $correspondence->note,
                'note_time' => (int) $correspondence->note_time->format("Y") > 0 ? $correspondence->note_time->format("Y-m-d H:i:s") : 'N/A'
            );
            $form->populate($existing_data);
        }
    }

    /**
     * Function to Delete Note via Ajax
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxDeleteCorrespondenceAction() {
        $this->_helper->BUtilities->setNoLayout();
        $vendor_id = $this->_getParam('vid');
        $note_id = $this->_getParam('note_id');
        $note = $this->em->find('BL\Entity\VendorCorrespondence', (int) $note_id);
        $this->em->remove($note);
        $this->em->flush();
        $this->em->clear();
        $note = $this->em->find('BL\Entity\VendorCorrespondence', (int) $note_id);
        if (!sizeof($note)) {
            echo Zend_Json::encode(array('code' => 'success', 'msg' => 'Successfully Deleted the note'));
        } else {
            echo Zend_Json::encode(array('code' => 'error', 'msg' => 'There was a problem deleting the note'));
        }
    }

    /**
     * Function to Delete Note via Ajax
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxDeleteNotesAction() {
        $this->_helper->BUtilities->setNoLayout();
        $vendor_id = $this->_getParam('vid');
        $note_id = $this->_getParam('note_id');
        $note = $this->em->find('BL\Entity\VendorNote', (int) $note_id);
        $this->em->remove($note);
        $this->em->flush();
        $this->em->clear();
        $note = $this->em->find('BL\Entity\VendorNote', (int) $note_id);
        if (!sizeof($note)) {
            echo Zend_Json::encode(array('code' => 'success', 'msg' => 'Successfully Deleted the note'));
        } else {
            echo Zend_Json::encode(array('code' => 'error', 'msg' => 'There was a problem deleting the note'));
        }
    }

    /**
     * Function to show docs and submit doc
     * @author tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function docsAction() {
        $this->getVendor();
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets'));
        $this->view->vendor = $this->vendor;
        $form = new Admin_Form_VendorDocs();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($form->isValid($formData)) {
                $VendorDoc = new \BL\Entity\VendorDocs();
                $VendorDoc->doc_name = $form->getValue('file_name');
                $VendorDoc->update_date = new DateTime();
                $VendorDoc->doc_time = new DateTime();
                $VendorDoc->vendor_id = $this->em->find('BL\Entity\User', (int) $this->vendor->id);
                $destination_dir = APPLICATION_PATH . '/../assets/files/vendor_documents/';
                try {
                    $adapter = new Zend_File_Transfer_Adapter_Http();
                    $adapter->setDestination($destination_dir);
                    $files = $adapter->getFileInfo();

                    if (isset($files['upload_doc'])) {
                        if (($adapter->isUploaded($files['upload_doc']['name'])) && ($adapter->isValid($files['upload_doc']['name']))) {
                            $extension = substr($files['upload_doc']['name'], strrpos($files['upload_doc']['name'], '.') + 1);
                            $filename = 'file_' . $this->vendor->id . '_' . date('Ymdhs') . '.' . $extension;
                            $adapter->addFilter('Rename', array('target' => $destination_dir . $filename, 'overwrite' => true));
                            $adapter->receive($files['upload_doc']['name']);
                            $VendorDoc->doc_url = $filename;
                            $VendorDoc->doc_type = $extension;
                        }
                    }
                    $this->em->persist($VendorDoc);
                    $this->em->flush();
                    $this->em->clear();
                    $this->view->msg = "Vendor Document added successfuly!";
                    $this->_helper->flashMessenger($this->view->msg, "Info");
                    $this->_redirect('/admin/vendors/docs/id/' . (string) $this->vendor->id);
                } catch (Exception $ex) {
                    $this->view->msg = $ex->getMessage();
                    $form->populate($formData);
                }
            } else {
                $form->populate($formData);
            }
        }
    }

    /**
     * Function to provide JSON data to feed data table
     * @author tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetDocsAction() {
        $this->_helper->BUtilities->setNoLayout();
        $search_model = new Admin_Model_Search($this);
        echo $search_model->getDocs();
    }

    /**
     * Function to Delete Documents via Ajax
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxDeleteDocsAction() {
        $this->_helper->BUtilities->setNoLayout();
        $vendor_id = $this->_getParam('vid');
        $docs_id = $this->_getParam('docs_id');
        $docs = $this->em->getRepository('BL\Entity\VendorDocs')->findOneBy(array('id' => (int) $docs_id));
        if ($docs) {
            $targetDir = APPLICATION_PATH . '/../assets/files/vendor_documents/';
            @unlink($targetDir . $docs->doc_url);
            $this->em->remove($docs);
            $this->em->flush();
            $this->em->clear();
            echo Zend_Json::encode(array('code' => 'success', 'msg' => 'Successfully Deleted the document'));
        } else {
            echo Zend_Json::encode(array('code' => 'error', 'msg' => 'There was a problem deleting the document'));
        }
    }

    /**
     * Function to get vendor specific invoices
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function invoicesAction() {
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets', 'load_fancy_assets'));
        $this->getVendor();
        $this->view->vendor = $this->vendor;
    }

    /**
     * Function to fet Vendor specific invoices records for ajax calls
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetInvoicesAction() {
        $this->_helper->BUtilities->setNoLayout();
        $search_model = new Admin_Model_Search($this);
        echo $search_model->searchInvoices();
    }/**
     * Function to delete Vendor specific invoices records for ajax calls
     * @author Jason B
     * @copyright Softura
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxDeleteInvoicesAction(){
    	$this->_helper->BUtilities->setNoLayout();
    	
    	$invoice_id = $this->_getParam('inv_id');
    	
    	if (is_numeric($invoice_id)){
    	
	    	$invoice = $this->em->getRepository("BL\Entity\Invoice")->findOneBy(array("id"=>$invoice_id));
	    	if ($invoice != null){
		    	$items = $this->em->getRepository("BL\Entity\InvoiceLineItems")->findBy(array("invoice_id" => $invoice));
		
		    	
		    	$onlinePayments = $this->em->getRepository("BL\Entity\OnlinePayment")->findBy(array("invoice"=>$invoice));
		    	
		    	foreach($onlinePayments as $onlinePayment){
		    		$this->em->remove($onlinePayment);
		    		$this->em->flush();
		    	}
		    	
		    	$payments = $this->em->getRepository("BL\Entity\Payment")->findBy(array("invoice" => $invoice));
		    	
		    	$payments_lineitems = array();
		
		    	if ($payments != null){
			    	foreach($payments as $payment){
			    		$payments_lineitems[] = $this->em->getRepository("BL\Entity\PaymentLineItems")->findBy(array("payment_id"=>$payment->id));
			    	}
			    	
			    	foreach($payments_lineitems as $payments_items){
			    		foreach($payments_items as $item){
			    			$this->em->remove($item);
			    			$this->em->flush();
			    		}
			    	}
			    	
			    	foreach($payments as $pament){
			    		$this->em->remove($payment);
			    		$this->em->flush();
			    	}
		    	
		    	}
		    	 
		    	foreach($items as $item){
		    		$this->em->remove($item);
		    		$this->em->flush();
		    	}
		    	
		    	$this->em->remove($invoice);
		    	$this->em->flush();
		    	$this->em->clear();
		    	
		    	$this->em->remove($invoice);
		    	 
		    	echo Zend_Json_Encoder::encode(array('code'=>'success', 'msg'=>"Successfully deleted invoice"));
	    	} else {
	    		echo Zend_Json_Encoder::encode(array('code'=>'success', 'msg'=>"Unable to find invoice"));
	    	}
    	} else {
    		echo Zend_Json_Encoder::encode(array('code'=>'success', 'msg'=>"Invalid invoice information"));
    	}
    }

    /**
     * Function to show invoice
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function showInvoiceAction() {
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_fancy_assets'));
        $invoice = $this->em->getRepository('BL\Entity\Invoice')->findOneBy(array('id' => $this->_getParam('inv_id')));
        $items = $this->em->getRepository('BL\Entity\InvoiceLineItems')->getLineItemsByInvoice($this->_getParam('inv_id'));

//      $this->view->BUtils()->doctrine_dump($items);
        $form = new Admin_Form_CreateInvoice();
        $existing_data = array(
            'vendor_name' => $invoice->company_name,
            'inv_num' => $invoice->invoice_number,
            'inv_type' => $invoice->invoice_type,
            'inv_term' => isset($invoice->invoice_term)? $invoice->invoice_term: "Net 15 days",
            'inv_date' => $invoice->invoice_date->format('M d, Y H:i a'),
            'email' => $invoice->email,
            'address_line_1' => $invoice->address_line1,
            'address_line_2' => $invoice->address_line2,
            'city' => $invoice->city,
            'state' => $invoice->state,
            'zip' => $invoice->zip,
            'phone_1' => $invoice->phone1,
            'phone_2' => $invoice->phone2,
            'fax' => $invoice->fax
        );
        
        $form->removeElement('inv_type');
        $form->removeElement('inv_term');
        
        $invType = new Zend_Form_Element_Text('inv_type');
        $invType->setLabel('Invoice Type')
        ->setAttribs(array('class' => 'text'))
        ->setDisableLoadDefaultDecorators(true);
        
        $invTerm = new Zend_Form_Element_Text('inv_term');
        $invTerm->setLabel('Invoice Term')
        ->setAttribs(array('class' => 'text'))
        ->setDisableLoadDefaultDecorators(true);
        
        $form->addElement($invType);
        $form->addElement($invTerm);
        
        $form->populate($existing_data);
        $this->view->form = $form;
        $this->view->invoice = $invoice;
        $this->view->items = $items;
    }

    /**
     * Function to show lineitems for Invoice
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function showInvoiceLineItemsAction() {
        $this->_helper->BUtilities->setEmptyLayout();
        $items = $this->em->getRepository('BL\Entity\InvoiceLineItems')->getLineItemsByInvoice($this->_getParam('id'));
        $this->view->items = $items;
        $this->view->em = $this->em;
        $vendor_id = $this->em->getRepository('BL\Entity\Invoice')->findOneBy(array('id'=>$this->_getParam('id')))->vendor_id;
        $this->view->Invoice_number = $this->em->getRepository('BL\Entity\Invoice')->findOneBy(array('id'=>$this->_getParam('id')))->invoice_number;
        
       // $vendor_id = !empty($vendor_id) ? $vendor_id : $this->_helper->BUtilities->getLoggedInUser();
        $this->view->vendor_id = $vendor_id;
		$status_array = $this->getHelper('BUtilities')->parseYAML(APPLICATION_PATH . '/configs/statuses.yml');
		$this->view->status_array = $status_array;
    }

    public function designsAction() {
        $this->getVendor();
        $this->view->vendor = $this->vendor;
    }

    /**
     * Function to List product Sample files
     * @author Sukhon
     * @copyright Blueliner Marketing
     */
    public function productSamplesAction() {
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets', 'load_fancy_assets', 'load_plupload_assets'));
        $this->getVendor();
        $this->view->vendor = $this->vendor;
        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->BUtilities->setNoLayout();
            $formData = $this->getRequest()->getPost();
            $uploaded_pic = $this->_getParam('pics');
            //print_r($formData);
            if ($uploaded_pic) {
                $class = 'BL\Entity\VendorSampleFile';
                $sample = new $class();
                $sample->title = substr($uploaded_pic, -15, 15);
                $sample->file_url = $uploaded_pic;
                $sample->file_extension = end(explode('.', $uploaded_pic));
                $sample->Vendor = $this->vendor;
                //$sample->upload_date = '';
                $this->em->persist($sample);
                $this->em->flush();
                $this->em->clear();
                //$this->_redirect('admin/vendors/sample/confirmation');

                echo Zend_Json_Encoder::encode(array('success' => true, 'message' => 'Successfully uploaded'));
            } else {
                echo Zend_Json_Encoder::encode(array('success' => false, 'message' => 'There is no file uploaded'));
            }
        }
    }

    /**
     * @author Rasidul
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     */
    public function webProfileAction() {
        $this->getVendor();
        $this->generateList();
        $this->_helper->JSLibs->load_fancy_assets();
        $this->_helper->JSLibs->load_dataTable_assets();

        $vendor_id = $this->vendor->id;
        $this->view->vendor = $this->vendor;
        $this->view->vendor_id = $vendor_id;
        $VendorProfile = $this->em->getRepository('BL\Entity\VendorProfile')->findBy(array('user_id' => (int) $vendor_id,'active' => '1'),array('update_date' => 'DESC'),1);
	if(isset($VendorProfile[0])){
                $VendorProfile = $VendorProfile[0];
		$products = explode(",",$VendorProfile->product_offered);
	}
        else
        {
	        $class = 'BL\Entity\VendorProfile';
	        $VendorProfile = new $class();
        }

        $vendorSampleFile = $this->em->getRepository('BL\Entity\VendorSampleFile')->findBy(array('Vendor' => (int) $vendor_id, 'use_for' => 'web_profile'));
        $vendorOperation = $this->em->getRepository("BL\Entity\VendorOperation")->findOneBy(array('user_id' => (int) $vendor_id));
	$this->view->vendorSampleFile = $vendorSampleFile;
        $user = $this->vendor;
        //$vendor_service = $this->em->getRepository('BL\Entity\VendorService')->findBy(array('vendor_id' => (int) $vendor_id));
	$vendor_service = $VendorProfile->services;
	
	$vendor_service = explode(",",$vendor_service);


	$this->view->products = array();
	foreach($products as $product){
		/*
		//$this->view->BUtils()->doctrine_dump($this->em->getRepository('BL\Entity\Product')->findByid($product));
		//echo "-------------";
		$this->view->BUtils()->doctrine_dump($this->em->getRepository('BL\Entity\VendorWebProfileProducts')->getVendorProducts($vendor_id));
		echo "-------------";
		*/
		if($product != "")
		$this->view->products = array_merge ($this->view->products, $this->em->getRepository('BL\Entity\Product')->findByid($product));
	
	}


        $vendor_default_service = array();
        if (sizeof($vendor_service)) {
            foreach ($vendor_service as $vs) {
                $vendor_default_service[] = $vs;
            }
        }

        $this->view->messages = $this->_helper->FlashMessenger->getMessages();

	if(sizeof($VendorProfile) > 0 && $VendorProfile->logo_url != '' && file_exists(APPLICATION_PATH.'/../assets/files/vendor_profile/thumbs/'.$VendorProfile->logo_url))
	{
	    $this->view->logo = $VendorProfile->logo_url;
	} else {
	    $this->view->logo = '';
	}


        if (count($VendorProfile) > 0) {
            $existing_data = array(
                'organization_name' => $VendorProfile->organization_name,
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
                'company_description' => $VendorProfile->company_discripction
            );
        } else {
            reset($existing_data = array());
        }


        //$services_array = $this->_helper->BUtilities->parseYAML(APPLICATION_PATH . '/configs/services.yml');
        $service_list = $this->em->getRepository("BL\Entity\Service")->findAll();
        foreach ($service_list as $service) {
            $service_type[$service['id']] = $service['title'];
        }
        $this->options['service'] = $service_type;
        $this->options['selected_service'] = $vendor_default_service;

        $form = new Admin_Form_VendorWebProfile($this->options);

        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                //Zend_Debug::dump($formData);
                if (count($VendorProfile) == 0) {
                    $class = 'BL\Entity\VendorProfile';
                    $VendorProfile = new $class();
                }

                //-- file(logo) upload code --//
                $filename = '';
                $extension = '';
                $destination_dir = APPLICATION_PATH . '/../assets/files/vendor_profile/';
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
                        $thumb = PhpThumbFactory::create($destination_dir . DIRECTORY_SEPARATOR . $filename);
                        $thumb->resize(150, 80)->padding(150, 80, '#FFFFFF');
                        $thumb->save($thumb_save_path);
                    }
                } catch (Exception $ex) {
                    echo "Exception!\n";
                    echo $ex->getMessage();
                }


                //--- saving vendor information to DB
                $VendorProfile->user_id = $user;
                $VendorProfile->organization_name = $form->getValue('organization_name');
                $VendorProfile->address1 = $form->getValue('address1');
                $VendorProfile->address2 = $form->getValue('address2');
                $VendorProfile->city = $form->getValue('city');
                $VendorProfile->state = $form->getValue('state');
                $VendorProfile->email = $form->getValue('email');
                $VendorProfile->web_page = $form->getValue('web_page');
                $VendorProfile->phone1 = $form->getValue('phone1');
                $VendorProfile->phone2 = $form->getValue('phone2');
                $VendorProfile->fax = $form->getValue('fax');
                $VendorProfile->zip = $form->getValue('zip');
				$VendorProfile->services = ($form->getValue('services') != '')? implode(",",$form->getValue('services')) : null;
                $VendorProfile->active = 1;
                if ($filename != null) {
                    $VendorProfile->logo_url = $filename;
                }
		else if($form->getValue('use_default') == true){
		    $VendorProfile->logo_url = "OLP_Logo_GRAY_150.jpg";
		}
		echo $form->use_default;
                if ($extension != null) {
                    $VendorProfile->logo_extension = $extension;
                }
		$product_array = explode(',', $form->getValue('products'));
                $VendorProfile->product_offered = implode(",",$product_array);
                $VendorProfile->company_discripction = $form->getValue('company_description');
                $VendorProfile->update_date = new DateTime();
                $this->em->persist($VendorProfile);
                $this->em->flush();
                //$this->view->BUtils()->doctrine_dump($VendorProfile);
                //-- saving to verdor_service table
                if (sizeof($form->getValue('services'))) {
                    //-- delete existing data 1st (because its ManyToOne relation)
                    $sql = "DELETE FROM BL\Entity\VendorService s where s.vendor_id = " . $vendor_id;
                    $q = $this->em->createQuery($sql);
                    $q->getResult();


                    foreach ($form->getValue('services') as $service) {
                        $class = 'BL\Entity\VendorService';
                        $vendorService = new $class();
                        $vendorService->service_id = $this->em->find('BL\Entity\Service', $service);
                        $vendorService->vendor_id = $user;
                        $this->em->persist($vendorService);
                        $this->em->flush();
                    }
                }

/*
                //-- saving vendor offered products
                $products = trim($form->getValue('products'));
                if (!empty($products)) {
                    $product_array = explode(',', $form->getValue('products'));
                    if (sizeof($product_array)) {
                        //-- delete existing data 1st (because its ManyToOne relation)
                        $sql = "DELETE FROM BL\Entity\VendorWebProfileProducts s where s.vendor_id = " . $vendor_id;
                        $q = $this->em->createQuery($sql);
                        $q->getResult();

                        foreach ($product_array as $product) {
                            $class = 'BL\Entity\VendorWebProfileProducts';
                            $VendorWebProfileProducts = new $class();
                            $VendorWebProfileProducts->product_id = $this->em->find('BL\Entity\Product', $product);
                            $VendorWebProfileProducts->vendor_id = $user;
                            $this->em->persist($VendorWebProfileProducts);
                            $this->em->flush();
                        }
                    }
                }
*/

                //-- sample file update code

                if (array_key_exists('pics', $formData)){
	                if (sizeof($formData['pics'])) {
	                    $i = 0;
	                    foreach ($formData['pics'] as $product_sample) {
	                        $class = 'BL\Entity\VendorSampleFile';
	                        $VendorSampleFile = new $class();
	                        $VendorSampleFile->file_url = $product_sample;
	                        $VendorSampleFile->active = 1;
	                        $VendorSampleFile->title = $formData['title'][$i];
	                        $VendorSampleFile->file_extension = substr($product_sample, strpos($product_sample, '.') + 1);
	                        $VendorSampleFile->upload_date = new DateTime();
	                        $VendorSampleFile->use_for = 'web_profile';
	                        $VendorSampleFile->Vendor = $user;
	                        $this->em->persist($VendorSampleFile);
	                        $this->em->flush();
	                        $i++;
	                    }
	                }
                }

                $this->_helper->flashMessenger("Web Profile updated succesfully!", "Info");
                $this->_redirect($this->view->BUrl()->absoluteUrl());
            }
        } else {
            $form->populate($existing_data);
        }
    }

    /**
     * Function to provide JSON data to feed data table
     * @author Sukhon
     * @copyright Blueliner Marketing
     */
    public function ajaxGetVendorSamplesDtAction() {
        $params = array(
            'search' => $this->_getParam('sSearch', ''),
            'page_start' => $this->_getParam('iDisplayStart', 1),
            'draw_count' => $this->_getParam('sEcho', 1),
            'per_page' => $this->_getParam('iDisplayLength', 10),
            'vendor_id' => $this->_getParam('id', ''),
        );
        /**
         * Let's take care of the sorting column to be passed to doctrine.
         * DataTable sends params like iSortCol_0.
         */
        $sorting_cols = array(
            '0' => 's.upload_date',
            '1' => 's.title'
        );

        $params['sort_key'] = $sorting_cols[$this->_getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->_getParam('sSortDir_0', 'asc');

        $records = $this->em->getRepository("BL\Entity\VendorSampleFile")->getSamples($params)->getResult();
        $params['show_total'] = true;
        $records_total = $this->em->getRepository("BL\Entity\VendorSampleFile")->getSamples($params);
        //echo($records_total.'===');
        $this->_helper->BUtilities->setNoLayout();

        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":[';
        $first = 0;
        foreach ($records as $v) {
            if ($first++) {
                $json .= ',';
            }
            //$this->view->BUtils()->doctrine_dump($v->upload_date,1);
            $json .= '["' . $v->upload_date->format('m/d/Y h:s A') . '",
              "<a href=\"' . $this->view->baseUrl("assets/files/samples/") . $v->file_url . '\" class=\"vendor_sample_link\" target=\"_blank\">' . str_replace(chr(13), '', str_replace(chr(10), "", $v->title)) . '</a>",
              "<a href=\"javascript:;\" class=\"remove\" rel=\"' . $v->id . '\"><img src=\"' . $this->view->baseUrl("assets/images/") . 'delete.png\" /></a>"]';
        }
        $json .= ']}';
        echo $json;
    }

    /**
     * Function to delete product Sample files
     * @author Sukhon
     * @copyright Blueliner Marketing
     */
    public function ajaxDelProductSamplesAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $sample = $this->em->find("BL\Entity\VendorSampleFile", (int) $this->_getParam('id', ''));
            $targetDir = APPLICATION_PATH . '/../assets/files/samples/';
            @unlink($targetDir . $sample->file_url);
            $this->em->remove($sample);
            $this->em->flush();
            $this->em->clear();
            echo Zend_Json_Encoder::encode(array('success' => true, 'message' => 'Successfully deleted'));
        }
        $this->_helper->BUtilities->setNoLayout();
    }

    public function searchAction() {
        $this->_forward('index');
    }

    /**
     * Function to show registrant vendors
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function registrantsAction() {
        $this->_helper->JSLibs->load_fancy_assets();
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets'));
        // action body
    }

    /**
     * Function to data table view of registrant vendors
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetRegistrantsAction() {
        $this->_helper->BUtilities->setNoLayout();
        $vendor_model = new Admin_Model_Vendors($this);
        echo $vendor_model->getRegistrants();
    }

    /**
     * Function to review registrant
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function reviewRegistrantAction() {
        $this->_helper->JSLibs->load_fancy_assets();
        $this->_helper->BUtilities->setEmptyLayout();
        $vendors_model = new Admin_Model_Vendors($this);
        $vendors_model->reviewRegistrant();
    }

    /**
     * Function to ADD new vendors
     * @author Rashed
     * @copyright Blueliner Marketing
     */
    public function addAction() {
        $vendor_model = new Admin_Model_Vendors($this);
        $vendor_model->addVendor();
    }

    public function overviewAction() {
        $this->_forward('view');
    }

    public function royaltyReportsAction() {
        // action body
    }

    public function contactAction() {
        $this->_helper->JSLibs->load_fancy_assets();
        $vendor_model = new Admin_Model_Vendors($this);
        $vendor_model->getContact();
    }

    public function oldContactAction(){
    	$this->_helper->BUtilities->setEmptyLayout();
    	
    	$form = new Admin_Form_Contact();
    	
    	$id = $this->_getParam("id");
    	
    	$profile = $this->em->getRepository("BL\Entity\UserProfile")->findOneBy(array("id"=>$id));
    	
    	$existing = array(
    			'username'=>$profile->username,
    			'organization_name'=>$profile->organization_name,
    			'vendor_number' => $profile->user_code,
    			'address_line_1'=>$profile->address_line1,
    			'address_line_2'=>$profile->address_line2,
    			 'phone_1' => $profile->phone,
    			'phone_2' => $profile->phone2,
    			'city' => $profile->city,
    			'state'=>$profile->state,
    			'zip' => $profile->zipcode,
    			'fax' => $profile->fax,
    			'web_page'=> $profile->website,
    			'email'=>$profile->email,
    			'company_email'=>$profile->company_email
    	);
    	
    	$form->populate($existing);
    	
    	$this->view->form = $form;
    }

    /**
     * Function to edit vendors contact
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function editContactAction() {
        $vendor_model = new Admin_Model_Vendors($this);
        $vendor_model->editContact();
    }

    /**
     * Function to delete contact
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function deleteContactAction() {
        $contact_id = $this->_getParam('id');
        $vendor_id = $this->_getParam('vendor_id');
        $contact_to_delete = $this->em->getRepository("BL\Entity\UserContact")->findOneBy(array("id" => (int) $contact_id));
        /**
         * Just make sure if it's a vendor contact or not
         */
        if ($contact_to_delete->user_id->account_type == ACC_TYPE_VENDOR) {
            $this->em->remove($contact_to_delete);
            $this->em->flush();
            $this->em->clear();
            $this->_helper->flashMessenger("Successfully Deleted the contact!", "Info");
            $this->_redirect("/admin/vendors/contact/id/" . $vendor_id);
        }
    }

    /**
     * Function to Upload multiple Sample files
     * @author Sukhon
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function uploadFilesAction() {
        $this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_plupload_assets();
        $this->_helper->BUtilities->setBlankLayout();
        
        $this->session->vendor_id = $this->_getParam('vid');
    }
    
    public function clearUploadedFilesAction(){
    	error_log("\nclearing uploaded", 3, "./errorLog.log");
    	
    	$this->_helper->BUtilities->setBlankLayout();
    	
    	if ($this->session->uploadedImages != null) $this->session->uploadedImages = null;
    	
    	echo Zend_Json::encode(array('code' => 'success', 'success'=>true));
    	
    }

    public function doUploadAction() {

    	$vendor = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id'=>$this->session->vendor_id));
    	 
    	if (is_null($this->session->uploadedImages)){
    		error_log("\nsetting uploaded images variable", 3, "./errorLog.log");
    		$this->session->uploadedImages = array();
    	} else {
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

        @set_time_limit(5 * 60);

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
	                if (!in_array(array("name"=>$fileName), $this->session->uploadedImages)){
	        				
        				$class = 'BL\Entity\VendorSampleFile';
        				$VendorSampleFile = new $class();
        				$VendorSampleFile->file_url = $fileName;
        				$VendorSampleFile->active = 1;
        				$VendorSampleFile->file_extension = substr($fileName, strpos($fileName, '.') + 1);
        				$VendorSampleFile->upload_date = new DateTime();
        				$VendorSampleFile->use_for = 'product_info';
        				$VendorSampleFile->Vendor = $vendor;
        				$this->em->persist($VendorSampleFile);
        				$this->em->flush();
        				$this->session->uploadedImages[] = array("name"=>$fileName);
        				
        			}
        			
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
     * Function to add others category product
     * @author Masud
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     * @return void
     */
    public function otherCheckAction() {

        $request = $this->getRequest();
        $category_id = trim($request->getParam('otherid'));
        $product_name = trim($request->getParam('productName'));
        //echo $category_id." ".$product_name;

        $products = $this->em->getRepository("BL\Entity\Product")->findByCategoryProductName($category_id, $product_name);
        //print_r($products);
        //print_r($products['0']->id);

        if (count($products) >= 1) {
            echo $products['0']->id;
        } else {
            //echo 'No recod found';
            //print_r($this->session->apply1);

            $product = new \BL\Entity\Product();
            $productCategory = $this->em->find('BL\Entity\ProductCategory', $category_id);

            $product->__set('product_name', $product_name);
            $product->__set('product_category_id', $productCategory);

            $this->em->persist($product);
            $this->em->flush();

            echo $product->__get('id');
        }

        $this->_helper->BUtilities->setNoLayout();
    }

    /**
     * Function to Return form search by type
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function searchByCatAction() {
        $search_form = $this->_getParam('type');
        $this->_helper->BUtilities->setEmptyLayout();
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_jquery_multiselect_assets'));
        $this->view->user_status = $this->_helper->BUtilities->parseYAML(APPLICATION_PATH . '/configs/user_status.yml');
        switch ($search_form) {
            case 'contact':
                $form = new Admin_Form_VendorContact();
                $this->view->form = $form;
                $this->_helper->viewRenderer('search-forms/contact-form');
                break;
            case 'web-profile':
                $form = new Admin_Form_VendorWebProfile();
                $this->view->form = $form;
                $this->_helper->viewRenderer('search-forms/web-profile-form');
                break;
            case 'docs':
                $this->_helper->viewRenderer('search-forms/docs-form');
                break;
            case 'invoices':
                $form = new Admin_Form_VendorInvoices();
                $this->view->form = $form;
                $this->_helper->viewRenderer('search-forms/invoices-form');
                break;
            case 'payments':
                $form = new Admin_Form_VendorPayments();
                $this->view->form = $form;
                $this->_helper->viewRenderer('search-forms/payments-form');
                break;
            case 'operations':
                $form = new Admin_Form_VendorOperations();
                $form->removeElement("vendor_status");
                $this->view->form = $form;
                $this->_helper->viewRenderer('search-forms/operations-form');
                break;
            case 'correspondence':
                $this->_helper->viewRenderer('search-forms/correspondence-form');
                break;
            case 'clients':
                $this->_helper->viewRenderer('search-forms/clients-form');
                break;
            case 'lisc-agreements':
                $this->_helper->viewRenderer('search-forms/lisc-agreements-form');
                break;
            case 'actions':
                $this->_helper->viewRenderer('search-forms/actions-form');
                break;
            case 'notes':
                $this->_helper->viewRenderer('search-forms/note-form');
                break;
            default:
                echo "<h2>Search</h2>";
                exit(0);
                break;
        }
    }

    /**
     * Function to Show Search Result
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function searchResultAction() {
        $this->_helper->BUtilities->setNoLayout();
        $search_type = $this->_getParam('search_type');
        switch ($search_type) {
            case 'contact':
                echo $this->em->getRepository('BL\Entity\User')->searchVendorByContact($this->_getAllParams());
                break;
            case 'web-profile':
                $this->em->getRepository('BL\Entity\VendorProfile')->searchVendorByWebProfile($this->_getAllParams());
                break;
            case 'correspondence':
                echo $this->em->getRepository('BL\Entity\VendorCorrespondence')->searchVendorByCorrespondence($this->_getAllParams());
                break;
            case 'clients':
                echo $this->em->getRepository('BL\Entity\License')->searchVendorByClients($this->_getAllParams());
                break;
            case 'lisc-agreements':
                echo $this->em->getRepository('BL\Entity\License')->searchVendorByLiscAgreements($this->_getAllParams());
                break;
            case 'operations':
                echo $this->em->getRepository('BL\Entity\VendorOperation')->searchVendorByOperations($this->_getAllParams());
                break;
            case 'notes':
                echo $this->em->getRepository('BL\Entity\VendorNote')->searchVendorByNotes($this->_getAllParams());
                break;
            case 'docs':
                echo $this->em->getRepository('BL\Entity\VendorDocs')->searchVendorByDocs($this->_getAllParams());
                break;
            case 'actions':
                echo $this->em->getRepository('BL\Entity\VendorActions')->searchVendorByActions($this->_getAllParams());
                break;
            case 'payments':
                echo $this->em->getRepository('BL\Entity\Payment')->searchVendorByPayments($this->_getAllParams());
                break;
            case 'invoices':
                echo $this->em->getRepository('BL\Entity\Invoice')->searchVendorByInvoice($this->_getAllParams());
                break;
            default:
                break;
        }
    }

    /**
     * Function to Show search result
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function showResultDetailsAction() {
        $result_type = $this->_getParam('res-type', '');
        $this->_helper->BUtilities->setEmptyLayout();
        $vendor_id = $this->_getParam('id');
        $this->view->vendor = $this->em->getRepository('BL\Entity\User')->findOneBy(array('account_type' => ACC_TYPE_VENDOR, 'id' => $vendor_id));
        switch ($result_type) {
            case 'correspondence':
                $form = new Admin_Form_VendorCorrespondence();
                $note_id = $this->_getParam('note-id', 0);
                $correspondence = $this->em->find('BL\Entity\VendorCorrespondence', (int) $this->_getParam('cid'));
                $form->populate(array('subject' => $correspondence->subject, 'note' => $correspondence->note, 'note_id' => $correspondence->id));
                $this->view->is_popup = "yes";
                $this->view->correspondence = $correspondence;
                $this->view->form = $form;
                $this->_helper->viewRenderer('correspondence-form-partial');
                break;
            case 'contact':
                $this->_forward('contact', null, null, array('id' => $this->_getParam('id'), 'action' => 'contact', 'from_popup' => 'yes'));
                break;
            case 'notes':
                $form = new Admin_Form_VendorNotes();
                $note_id = $this->_getParam('note-id', 0);
                $notes = $this->em->find('BL\Entity\VendorNote', (int) $this->_getParam('cid'));
                $form->populate(array('description' => $notes->note, 'note_id' => $notes->id));
                $this->view->is_popup = "yes";
                $this->view->notes = $notes;
                $this->view->form = $form;
                $this->_helper->viewRenderer('notes-form-partial');
                break;
            case 'docs':
                $form = new Admin_Form_VendorDocs();
                $docs_id = $this->_getParam('docs-id', 0);
                $docs = $this->em->find('BL\Entity\VendorDocs', (int) $this->_getParam('cid'));
                $form->populate(array('file_name' => $docs->doc_name, 'docs_id' => $docs->id));
                $this->view->is_popup = "yes";
                $this->view->docs = $docs;
                $this->view->form = $form;
                $this->_helper->viewRenderer('docs-form-partial');
                break;
            case 'actions':
                $clients = array();
                $clients_list = $this->em->getRepository("BL\Entity\User")->getClientNames();
                foreach ($clients_list as $client) {
                    $clients[$client['id']] = $client['client_greek_name'];
                }

                $actions_id = $this->_getParam('actions-id', 0);
                $actions = $this->em->find('BL\Entity\VendorActions', (int) $this->_getParam('cid'));
                $form = new Admin_Form_VendorActions($clients);
                $form->populate(array('action_needed' => $actions->action, "resolution" => $actions->resolution, "greek_org" => $actions->client_id->id, 'actions_id' => $actions->id));
                $this->view->is_popup = "yes";
                $this->view->actions = $actions;
                $this->view->form = $form;
                $this->_helper->viewRenderer('actions-form-partial');
                break;
            case 'operations':
                $this->_forward('operations', null, null, array('id' => $this->_getParam('id'), 'action' => 'operations', 'from_popup' => 'yes'));
                break;
            case 'clients':
                $this->_forward('clients', null, null, array('id' => $this->_getParam('id'), 'action' => 'clients', 'from_popup' => 'yes'));
                break;
            case 'lisc-agreements':
                $this->_forward('lisc-agreements', null, null, array('id' => $this->_getParam('id'), 'action' => 'lisc-agreements', 'from_popup' => 'yes'));
                break;
            case 'web-profile':
                $this->_forward('web-profile', null, null, array('id' => $this->_getParam('id'), 'action' => 'web-profile', 'from_popup' => 'yes'));
                break;
            case 'payments':
                $this->_forward('vendor-payments', null, null, array('id' => $this->_getParam('id'), 'action' => 'payments', 'from_popup' => 'yes'));
                break;
            case 'invoices':
                $this->_forward('invoices', null, null, array('id' => $this->_getParam('id'), 'action' => 'invoices', 'from_popup' => 'yes'));
                break;
            default:
                break;
        }
    }

    /**
     * Function to view vendor product info
     * @author Rasidul
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function productInfoAction() {
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets', 'load_fancy_assets', 'load_jquery_multiselect_assets'));

        $this->getVendor();
        $this->view->vendor = $this->vendor;
        $this->generateList();

        $vendor_id = $this->vendor->id;
        $this->view->vendor_id = $vendor_id;
        $user = $this->vendor;

        $VendorProductAudience = $this->em->getRepository('BL\Entity\VendorProductAudience')->findBy(array('vendor_id' => (int) $vendor_id));

        $vendor_default_audience = array();
        if (sizeof($VendorProductAudience)) {
            foreach ($VendorProductAudience as $vs) {
                $vendor_default_audience[] = $vs->audience_id->id;
            }
        }


        $form = new Admin_Form_ProductInfo($this->options, $vendor_default_audience);
        $this->view->form = $form;

        $VendorProductInfo = $this->em->getRepository('BL\Entity\VendorProductInfo')->findOneBy(array('vendor_id' => (int) $vendor_id));

        $existing_data = array(
            'supplier_name' => (sizeof($VendorProductInfo) ? $VendorProductInfo->supplier_name : ''),
            'other_desc' => (sizeof($VendorProductInfo) ? $VendorProductInfo->other_desc : '')
        );

        $this->view->products = $this->em->getRepository('BL\Entity\VendorProductInfoDetails')->getVendorProducts($vendor_id);
        $this->view->uploadedImages = $this->session->uploadedImages;
        //Zend_Debug::dump($this->view->products);


        $vendor_default_product = $this->em->getRepository('BL\Entity\VendorProductInfoDetails')->findBy(array('vendor_id' => (int) $vendor_id));
        $this->view->vendor_default_product = $vendor_default_product;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
            	$this->session->uploadedImages = null;
            	
                //Zend_Debug::dump($formData);
                //-- saving to vendor product infos
                if (!sizeof($VendorProductInfo)) {
                    $VendorProductInfo = new BL\Entity\VendorProductInfo();
                }
                $VendorProductInfo->supplier_name = $form->getValue('supplier_name');
                $VendorProductInfo->other_desc = $form->getValue('other_desc');
                $VendorProductInfo->vendor_id = $user;
                $this->em->persist($VendorProductInfo);
                $this->em->flush();

                //-- saving to vendor_product_infos_dtails table
                $product_array = explode(',', $form->getValue('products'));
                if (sizeof($product_array)) {
                    //-- delete existing data 1st (because its ManyToOne relation)
                    $sql = "DELETE FROM BL\Entity\VendorProductInfoDetails s where s.vendor_id = " . $vendor_id;
                    $q = $this->em->createQuery($sql);
                    $q->getResult();

                    foreach ($product_array as $product) {
                        $class = 'BL\Entity\VendorProductInfoDetails';
                        $VendorProductInfoDetails = new $class();
                        //$VendorProductInfoDetails->product_id=$product;
                        $VendorProductInfoDetails->product_id = $this->em->find('BL\Entity\Product', $product);
                        $VendorProductInfoDetails->vendor_id = $user;
                        $this->em->persist($VendorProductInfoDetails);
                        $this->em->flush();
                    }
                }

                //-- saving to VendorProductAudience table
                if (sizeof($form->getValue('audience'))) {
                    //-- delete existing data 1st (because its ManyToOne relation)
                    $sql = "DELETE FROM BL\Entity\VendorProductAudience s where s.vendor_id = " . $vendor_id;
                    $q = $this->em->createQuery($sql);
                    $q->getResult();

                    foreach ($form->getValue('audience') as $aud) {
                        $class = 'BL\Entity\VendorProductAudience';
                        $VendorProductAudience = new $class();
                        //$VendorProductAudience->audience_id=$aud;
                        $VendorProductAudience->audience_id = $this->em->find('BL\Entity\TargetAudience', $aud);
                        $VendorProductAudience->vendor_id = $user;
                        $this->em->persist($VendorProductAudience);
                        $this->em->flush();
                    }
                }

                //-- sample file update code
                if (isset($formData['pics'])) {

                    if (sizeof($formData['pics'])) {
                        $i = 0;
                        foreach ($formData['pics'] as $product_sample) {
                        	
                            $VendorSampleFile = $this->em->getRepository('BL\Entity\VendorSampleFile')->findOneBy(array('file_url'=>$product_sample));
                            
                            $VendorSampleFile->title = $formData['title'][$i];
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
        } else {
            $form->populate($existing_data);
        }
    }

    /**
     * Function to view vendor financial info. only view.
     * @author Rasidul
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     */
    public function financialInfoAction() {
        $nolayout = $this->_getParam('nolayout', 0);
        if ($nolayout == 1) {
            $this->_helper->BUtilities->setBlankLayout();
            $this->_helper->JSLibs->load_jqui_assets();
            $this->view->fancy = 1;
        }
        $this->getVendor();
        $this->view->vendor = $this->vendor;

        $form = new Vendor_Form_Apply4();
        $this->view->form = $form;
        $vendor_id = $this->vendor->id;
        $user = $this->vendor;
        $this->view->vendor_id = $vendor_id;

        $VendorFinancialInfo = $this->em->getRepository('BL\Entity\VendorFinancialInfo')->findOneBy(array('vendor_id' => (int) $vendor_id));
        $application_process = array();
        if (sizeof($VendorFinancialInfo) && $VendorFinancialInfo->has_account_in_good_standing == 'yes') {
            $application_process[] = '1';
        }
        if (sizeof($VendorFinancialInfo) && $VendorFinancialInfo->has_closed_financial_statement == 'yes') {
            $application_process[] = '2';
        }
        if (sizeof($VendorFinancialInfo) && $VendorFinancialInfo->has_chart_of_capital_assets == 'yes') {
            $application_process[] = '3';
        }
        $existing_data = array(
            'application_process' => (sizeof($application_process) ? $application_process : ''),
            'full_time_employee_num' => (sizeof($VendorFinancialInfo) ? $VendorFinancialInfo->full_time_employee_num : ''),
            'years_in_business' => (sizeof($VendorFinancialInfo) ? $VendorFinancialInfo->years_in_business : ''),
            'business_failure_in_5_years' => (sizeof($VendorFinancialInfo) ? array($VendorFinancialInfo->business_failure_in_5_years) : ''),
            'any_person_bankrupt' => (sizeof($VendorFinancialInfo) ? array($VendorFinancialInfo->any_person_bankrupt) : ''),
            'government_investigation' => (sizeof($VendorFinancialInfo) ? array($VendorFinancialInfo->government_investigation) : ''),
            'contract_terminated_in_last_2_years' => (sizeof($VendorFinancialInfo) ? array($VendorFinancialInfo->contract_terminated_in_last_2_years) : ''),
            'litigation_against_the_officers' => (sizeof($VendorFinancialInfo) ? array($VendorFinancialInfo->litigation_against_the_officers) : ''),
            'any_collections_by_debt_collection_agency' => (sizeof($VendorFinancialInfo) ? array($VendorFinancialInfo->any_collections_by_debt_collection_agency) : ''),
            'additional_explanation' => (sizeof($VendorFinancialInfo) ? $VendorFinancialInfo->additional_explanation : '')
        );
        //Zend_Debug::dump($existing_data);
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                //Zend_Debug::dump($formData);
                if (!sizeof($VendorFinancialInfo)) {
                    $VendorFinancialInfo = new \BL\Entity\VendorFinancialInfo();
                }

                if (sizeof($form->getValue('application_process'))) {
                    foreach ($form->getValue('application_process') as $app_process) {
                        if ($app_process == 1)
                            $VendorFinancialInfo->has_account_in_good_standing = 'yes';
                        else
                            $VendorFinancialInfo->has_account_in_good_standing = null;
                        if ($app_process == 2)
                            $VendorFinancialInfo->has_closed_financial_statement = 'yes';
                        else
                            $VendorFinancialInfo->has_closed_financial_statement = null;
                        if ($app_process == 3)
                            $VendorFinancialInfo->has_chart_of_capital_assets = 'yes';
                        else
                            $VendorFinancialInfo->has_chart_of_capital_assets = null;
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
                $form->populate($formData);
            }
        } else {
            $form->populate($existing_data);
        }
    }

    /**
     * function to get all product of a particular category using ajax call
     * @author Rasidul
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     * @return string
     */
    public function productAction() {
        $request = $this->getRequest();
        //$product_categories = $em->getRepository("BL\Entity\ProductCategory")->findAll();
        $products = '';
        if ($request->getParam('cat') == '' || $request->getParam('cat') == NULL) {
            $products = $this->em->getRepository("BL\Entity\Product")->findAll();
        } else {
            $products = $this->em->getRepository("BL\Entity\Product")->findByCategory($request->getParam('cat'));
        }

        $this->_helper->BUtilities->setNoLayout();
        $options = '';
        foreach ($products as $product) {
            $options.= '<option value="' . $product->id . '">' . $product->product_name . '</option>';
        }
        echo $options;
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
        $redirect_url = $this->_getParam('redirect_url');
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
            if ($redirect_url != '') {
                $json .= '[
                  "' . $v->upload_date->format("m/d/Y") . '",
                  "' . $v->title . '",
                  "<a href=\"' . $this->view->baseUrl("assets/files/samples/products/") . $v->file_url . '\" class=\"fancy\"><img src=\"' . $this->view->baseUrl("assets/files/samples/products/thumbs/") . '' . $v->file_url . '\" height=\"50\" /></a>",
                  "' . '<a class=\"delete\" href=\"' . $this->view->baseUrl("admin/vendors/delete-sample/id/{$this->_getParam('id')}/sample_id/{$v->id}/redirect_url/{$this->_getParam('redirect_url')}") . '\">Delete</a>"
                      ]';
            } else {
                $json .= '[
                  "' . $v->upload_date->format("m/d/Y") . '",
                  "' . $v->title . '",
                  "<a href=\"' . $this->view->baseUrl("assets/files/samples/products/") . $v->file_url . '\" class=\"fancy\"><img src=\"' . $this->view->baseUrl("assets/files/samples/products/thumbs/") . '' . $v->file_url . '\" height=\"50\" /></a>",
                  "' . '<a class=\"delete\" href=\"' . $this->view->baseUrl("admin/vendors/delete-sample/id/{$this->_getParam('id')}/sample_id/{$v->id}/redirect_url/{$this->_getParam('redirect_url')}") . '\">Delete</a>"
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
        $banner = $this->em->find("BL\Entity\VendorSampleFile", (int) $this->_getParam('sample_id'));

        @unlink(realpath(APPLICATION_PATH . "/../assets/files/samples/products/{$banner->file_url}"));
        @unlink(realpath(APPLICATION_PATH . "/../assets/files/samples/products/thumbs/{$banner->file_url}"));
        @unlink(realpath(APPLICATION_PATH . "/../assets/files/samples/products/large/{$banner->file_url}"));

        $this->em->remove($banner);
        $this->em->flush();
        $this->em->clear();

        $this->_helper->BUtilities->setNoLayout();
        $this->_helper->flashMessenger("Sample Product Deleted", "Info");
        if ($this->_getParam('redirect_url') == 'product-info')
            $this->_redirect('admin/vendors/product-info/id/' . $this->_getParam('id'));
        else
            $this->_redirect('admin/vendors/web-profile/id/' . $this->_getParam('id'));
    }

    /**
     * Function to Show Export Wizard window for exporting search result
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function exportResultsAction() {
        $this->_helper->BUtilities->setEmptyLayout();
        $this->view->search_type = $this->_getParam("search_type", "");
    }

    /**
     * Function to Export selected fields to Excel
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function exportResultsExcelAction() {
        $this->_helper->BUtilities->setNoLayout();
        parse_str($this->_getParam("fields_to_export"), $export_fields);
        parse_str($this->_getParam("current_search_params"), $search_params);


        $result = $this->em->getRepository("BL\Entity\User")->exportVendorInformation($search_params, $export_fields['export_fields'], $export_fields['labels']);

        /**
         * PHPExcel times out and runs out of Memory when dealing with Large Data (even > 200 rows).
         * So if the total num is more than 100, we export to CSV
         */
        if (sizeof($result['data']) > 0) {
            $vendor_model = new Admin_Model_Vendors($this);
            $vendor_model->saveCSV($result);
        } else {
            $vendor_model = new Admin_Model_Vendors($this);
            $vendor_model->saveExcel($result);
        }
        exit;
    }
    public function exportResultsExcel2Action() {
        $this->_helper->BUtilities->setNoLayout();
//	echo $_GET["data"];
	//$data = isset($_REQUEST['arr']) ? json_decode($_REQUEST['arr']) : array();;
	//var_dump($_GET);

	//$data = array();
	//$data = array(array('1'=>'this','2'=>'is','3'=>'export','4'=>'!!','111','222'));
	//$newitem = array('1'=>'this','2'=>'is','3'=>'export','4'=>'!!','111','223');
	//$data[1] = $newitem;	
        //$result = array('labels'=> array('Company Name','Invoice Type','Invoice Status','Payment Status','Invoice Total','Remaining Due'),'data' => $data ,'title'=>'300');
	//$this->_helper->BUtilities->setNoLayout();
	$vendors_model = new Admin_Model_Vendors($this);
	$data = $vendors_model->getInvoicesByParams2();
	$result = array('labels'=> array('Company Name','Invoice Type','Invoice Status','Payment Status','Invoice Total','Remaining Due'),'data' => $data ,'title'=>'Invoice Report');

        /**
         * PHPExcel times out and runs out of Memory when dealing with Large Data (even > 200 rows).
         * So if the total num is more than 100, we export to CSV
         */
        if (sizeof($result['data']) > 0) {
            $vendor_model = new Admin_Model_Vendors($this);
            $vendor_model->saveCSV($result);
        } else {
            $vendor_model = new Admin_Model_Vendors($this);
            $vendor_model->saveExcel($result);
        }
        exit;
    }

    /**
     * Function to
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetActionsDetailsAction() {
        $this->_helper->BUtilities->setNoLayout();
        $actions_id = $this->_getParam('actions-id', 0);
        $actions = $this->em->find('BL\Entity\VendorActions', (int) $actions_id);
        echo "<b>" . $actions->client_id->organization_name . "</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $actions->assignment_date->format("M d, Y H:i A") . "<br /><b>Action Needed: </b>" . (($actions->action != NULL || $actions->action != "") ? $actions->action : "N/A") . "<br /> <b>Resolution: </b>" . (($actions->resolution != NULL || $actions->resolution != "") ? $actions->resolution : "N/A");
    }

    /**
     * Function to Save License Status via AJAX
     * @author Bal
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxSaveLicenseStatusAction() {
        $this->_helper->BUtilities->setNoLayout();
        $lic_id = $this->_getParam('lic_id');
	$status_id = $this->_getParam('status_id');
	$license = $this->em->getRepository("BL\Entity\License")->findOneBy(array('id' => (int) $lic_id));

	$license->status = $status_id;
	$this->em->persist($license);
        $this->em->flush();


	$vendorId = $license->vendor_id->id;

	/*
	 * Find the liceses with this vendor which are
	 * 4 - Once Time
	 * 5 - Licensed
	 */
	$licensesByThisVendor = $this->em->getRepository("BL\Entity\License")
		->findBy(array(
		    'vendor_id'=> $vendorId,
		    'status'=>   array(4,5)
		    )
		);

	$count = count($licensesByThisVendor);

	$user = $license->vendor_id;

	if($count < 1) {
	    $user->user_status = 'Registered';
	} else {
	    $user->user_status = 'Current';
	}
	$this->em->persist($user);
	$this->em->flush();
	$this->em->clear();

        if ($license->id) {
            echo Zend_Json::encode(array('code' => 'success', 'msg' => 'Successfully Updated Status'));
        } else {
            echo Zend_Json::encode(array('code' => 'error', 'msg' => 'There was a problem updating the status'));
        }

    }



    /**
     * Function to Save Actions via AJAX
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxSaveActionsAction() {
        $this->_helper->BUtilities->setNoLayout();
        $vendor_id = $this->_getParam('vid');
        $actions_id = $this->_getParam('actions_id', 0);
        $client_id = $this->_getParam('greek_org');
        $action_needed = $this->_getParam('action_needed');
        $resolution = $this->_getParam('resolution');
        if ($actions_id) {
            $new_actions = $this->em->find('BL\Entity\VendorActions', (int) $actions_id);
        } else {
            $new_actions = new \BL\Entity\VendorActions();
        }
        $new_actions->action = $action_needed;
        $new_actions->resolution = $resolution;
        $new_actions->assignment_date = new DateTime();
        $new_actions->client_id = $this->em->find('BL\Entity\User', (int) $client_id);
        $new_actions->vendor_id = $this->em->find('BL\Entity\User', (int) $vendor_id);
        $this->em->persist($new_actions);
        $this->em->flush();
        if ($new_actions->id) {
            echo Zend_Json::encode(array('code' => 'success', 'msg' => 'Successfully Added the Action'));
        } else {
            echo Zend_Json::encode(array('code' => 'error', 'msg' => 'There was a problem adding the Action'));
        }
    }

    /**
     * Function to Delete Actions via Ajax
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxDeleteActionsAction() {
        $this->_helper->BUtilities->setNoLayout();
        $vendor_id = $this->_getParam('vid');
        $actions_id = $this->_getParam('actions_id');
        $actions = $this->em->find('BL\Entity\VendorActions', (int) $actions_id);
        $this->em->remove($actions);
        $this->em->flush();
        $this->em->clear();
        $actions = $this->em->find('BL\Entity\VendorActions', (int) $actions_id);
        if (!sizeof($actions)) {
            echo Zend_Json::encode(array('code' => 'success', 'msg' => 'Successfully Deleted the Action'));
        } else {
            echo Zend_Json::encode(array('code' => 'error', 'msg' => 'There was a problem deleting the Action'));
        }
    }

    /**
     * Function to view all payments without any filters
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function allPaymentsAction() {
        if ($this->view->BUtils()->getLoggedInUserRole() === 1) {
            $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets', 'load_fancy_assets'));
        } else {
            $this->_helper->flashMessenger("Sorry, But you do not have access to this section of the portal!", "Info");
            $this->_redirect("admin/vendors/index");
        }
    }

    /**
     * Function to get AJAX response to all payments of all vendors
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetAllPaymentsAction() {
        $this->_helper->BUtilities->setNoLayout();
        $vendor_model = new Admin_Model_Vendors($this);
        echo $vendor_model->getAllVendorPayments();
    }

    /**
     * Function to add payment for an invoice
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function addPaymentAction() {
        /**
         * if admin user
         */
        if ($this->view->BUtils()->getLoggedInUserRole() === 1) {
            $vendor_model = new Admin_Model_Vendors($this);
            $vendor_model->addPayment();
        } else {
            /**
             * if user type is employee
             */
            $this->_helper->flashMessenger("Sorry, But you do not have access to this section of the portal!", "Info");
            $this->_redirect("admin/vendors/index");
        }
    }

    /**
     * Function to fiew list of vendor profile
     * @author Rasidul
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     */
    public function webProfileApprovalAction() {
        $getEventTitle = $this->_getParam('tit');
        $getEventTitle_edit = $this->_getParam('tit_edit');
        $this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_fancy_assets();
        $this->_helper->JSLibs->load_dataTable_assets();
        // $this->view->event_title =  $getEventTitle;
        $this->view->assign('event_title', $getEventTitle);
        $this->view->assign('event_title_edit', $getEventTitle_edit);
    }

    /**
     * Function to provide JSON data to feed data table
     * @author Rasidul
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetWebProfileAction() {
        $targetPage = $this->_getParam('targetPage', 'all');
        $this->_helper->BUtilities->setNoLayout();
        $redirect_url = $this->_getParam('redirect_url');
        $params = array(
            'search' => $this->_getParam('sSearch', ''),
            'page_start' => $this->_getParam('iDisplayStart', 1),
            'draw_count' => $this->_getParam('sEcho', 1),
            'per_page' => $this->_getParam('iDisplayLength', 10),
            'targetPage' => $targetPage
        );
        /**
         * Let's take care of the sorting column to be passed to doctrine.
         * DataTable sends params like iSortCol_0.
         */
        $sorting_cols = array('0' => 'vp.organization_name', '1' => 'vp.active', '2' => 'vp.update_date');
        $params['sort_key'] = $sorting_cols[$this->_getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->_getParam('sSortDir_0');
        $params['use_for'] = $this->_getParam('use_for');

        $records = $this->em->getRepository("BL\Entity\VendorProfile")->getWebProfiles($params)->getResult();
        $params['show_total'] = true;
        $records_total = $this->em->getRepository("BL\Entity\VendorProfile")->getWebProfiles($params);
        /**
         * Datatable doesn't understand json_encode and have a custom json format
         */
        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":[';
        $first = 0;
        foreach ($records as $v) {
            $profile_status = '';
            switch ($v->active) {
                case '0':
                    $profile_status = 'Declined';
                    break;
                case '1':
                    $profile_status = 'Approved';
                    break;
                case '-1':
                    $profile_status = 'Pending';
                    break;
                default:
                    break;
            }
            //$user=$this->em->find('BL\Entity\User', $v->user_id);

            if ($first++) {
                $json .= ',';
            }
            
            $org_name = (!is_null($v->organization_name) ? $v->organization_name : 'N/A') ;
            
            $org_name = trim(str_replace('"', '\"',str_replace("\r", "", str_replace("\n", "", $org_name))));
            
            $json .= '[
                  "' . $org_name . '",
                  "' . $profile_status . '",
                  "' . (!is_null($v->update_date) ? $v->update_date->format("m/d/Y") : "N/A") . '",
                  "<a class=\"details\" rel=\"' . $v->id . '\" href=\"javascript:;\" id=\"' . $v->id . '\">' . 'View' . '</a>"
                      ]';
        }
        $json .= ']}';
        echo $json;
    }

    /**
     * Function to view vendor profile by ajax
     * @author Rasidul
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetVendorWebProfileAction() {
        $this->_helper->JSLibs->load_fancy_assets();
        $this->_helper->BUtilities->setEmptyLayout();
        $this->view->profile_id = $this->_getParam("profile_id", "");
        $this->view->vendor_profile = $this->em->find('BL\Entity\VendorProfile', (int) $this->view->profile_id);
        $this->view->vendor_product_info = $this->em->getRepository("BL\Entity\VendorProductInfo")->findOneBy(array('vendor_id' => $this->view->vendor_profile->user_id));
        $this->view->vendor_product_info_details = $this->em->getRepository("BL\Entity\VendorProductInfoDetails")->findBy(array('vendor_id' => $this->view->vendor_profile->user_id));

        $this->view->vendor_sample_file = $this->em->getRepository("BL\Entity\VendorSampleFile")->findBy(array('Vendor' => $this->view->vendor_profile->user_id, 'use_for' => 'web_profile'));


        $VendorProfile = $this->em->getRepository('BL\Entity\VendorProfile')->findBy(array('id' => $this->view->profile_id),array('update_date' => 'DESC'),1);
	$VendorProfile = $VendorProfile[0];

	$VendorProfileCurrent = $this->em->getRepository('BL\Entity\VendorProfile')->findBy(array('user_id' => $this->view->vendor_profile->user_id,'active' => '1'),array('update_date' => 'DESC'),1);
	$services = explode(",",$VendorProfile->services);
	if($VendorProfileCurrent != Null){
        $VendorProfileCurrent = $VendorProfileCurrent[0];
	$productsCurrent = explode(",",$VendorProfileCurrent->product_offered);
	$this->view->productsCurrent = array();
        foreach($productsCurrent as $product){
                if($product != null)
                $this->view->productsCurrent = array_merge ($this->view->productsCurrent, $this->em->getRepository('BL\Entity\Product')->findByid($product));

        }
	}

        $vendor_service = $this->em->getRepository('BL\Entity\VendorService')->findBy(array('vendor_id' => $this->view->vendor_profile->user_id));

	$products = explode(",",$VendorProfile->product_offered);

        $this->view->products = $this->em->getRepository('BL\Entity\VendorWebProfileProducts')->getVendorProducts($this->view->vendor_profile->user_id->id);
        if (!sizeof($this->view->products)) {
            $this->view->products = $this->em->getRepository('BL\Entity\VendorProductInfoDetails')->getVendorProducts($this->view->vendor_profile->user_id->id);
        }

	$this->view->products = array();
	foreach($products as $product){
		if($product != null)
                $this->view->products = array_merge ($this->view->products, $this->em->getRepository('BL\Entity\Product')->findByid($product));

        }

        $this->view->messages = $this->_helper->FlashMessenger->getMessages();

        $this->view->logo = sizeof($VendorProfile) ? $VendorProfile->logo_url : '';
	$this->view->logoCurrent = sizeof($VendorProfileCurrent) ? $VendorProfileCurrent->logo_url : '';

        if (count($VendorProfile) > 0) {
            $existing_data = array(
                'organization_name' => $VendorProfile->organization_name,
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
                'company_discripction' => $VendorProfile->company_discripction
            );
        } else {
            reset($existing_data = array());
        }
	if (count($VendorProfileCurrent) > 0) {
            $existing_dataCurrent = array(
                'organization_name' => $VendorProfileCurrent->organization_name,
                'address1' => $VendorProfileCurrent->address1,
                'address2' => $VendorProfileCurrent->address2,
                'city' => $VendorProfileCurrent->city,
                'state' => $VendorProfileCurrent->state,
                'email' => $VendorProfileCurrent->email,
                'web_page' => $VendorProfileCurrent->web_page,
                'phone1' => $VendorProfileCurrent->phone1,
                'phone2' => $VendorProfileCurrent->phone2,
                'fax' => $VendorProfileCurrent->fax,
                'zip' => $VendorProfileCurrent->zip,
                'product_offered' => $VendorProfileCurrent->product_offered,
                'company_discripction' => $VendorProfileCurrent->company_discripction
            );
        } else {
            $existing_dataCurrent = array();
        }


        $vendor_default_service = array();
        if (sizeof($vendor_service)) {
            foreach ($vendor_service as $vs) {
                //echo $vs->vendor_id->id.','.$vs->service_id.'<br/>';
                $vendor_default_service[] = $vs->service_id->id;
            }
        }

        $service_list = $this->em->getRepository("BL\Entity\Service")->findAll();
        foreach ($service_list as $service) {
            $service_type[$service['id']] = $service['title'];
        }
        $this->options['categories'] = array();
        $this->options['products'] = array();
        $this->options['service'] = $service_type;
        $this->options['selected_service'] = explode(",",$VendorProfile->services);
        $form = new Vendor_Form_VendorProfile($this->options);
	$this->options['selected_service'] = explode(",",(count($VendorProfileCurrent) > 0)? $VendorProfileCurrent->services : "");
	$form2 = new Vendor_Form_VendorProfile($this->options);

        $this->view->form = $form;
	$this->view->formCurrent = $form2;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                // add code
            }
        } else {
            $form->populate($existing_data);
	    $form2->populate($existing_dataCurrent);
        }
    }

    /**
     * Function to download vendor documents
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function downloadDocumentsAction() {
        $file = $this->_getParam('file');
        $ext = explode('.', $file);
        $targetDir = APPLICATION_PATH . '/../assets/files/vendor_documents/';

        header('Content-Disposition: attachment; filename="' . $file . '"');
        readfile($targetDir . $file);
        $this->_helper->BUtilities->setNoLayout();
    }

    /**
     * Function to update Profile Status(0= dicline, 1=active)
     * @author Rasidul
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxUpdateProfileStatusAction() {
        //$this->_helper->BUtilities->setEmptyLayout();
        $this->_helper->BUtilities->setNoLayout();
        $profile_id = $this->_getParam("profile_id", "");
        $status = $this->_getParam("status", "0");
        $reason = $this->_getParam("reason", "");
        $vendor_profile = $this->em->find('BL\Entity\VendorProfile', (int) $profile_id);
        $user = $this->em->getRepository('BL\Entity\User')->findBy(array('id' => $vendor_profile->user_id));
        if (sizeof($vendor_profile)) {
            $vendor_profile->active = $status;

	    $con = mysql_connect('localhost','greekamc', 'am@kMg!RcA47');	
	    mysql_select_db("admin_amc",$con);
	    $sql = "update admin_amc.vendor_sample_files set active = 1 where vendor_id =".$user['0']->id." and use_for = 'web_profile';";
            mysql_query($sql,$con);
	    mysql_close($con);

            $vendor_profile->reason_for_declining = $reason;
            $this->em->persist($vendor_profile);
            $this->em->flush();
            $this->view->status = $status;
	    $this->em->find("BL\Entity\VendorSampleFile", (int) $this->_getParam('sample_id'));

            //-- sending notification email to vendor
            if ($status == 1) {

                $params = array(
                    'to' => $user['0']->email,
                    'to_name' => $vendor_profile->organization_name,
                    'from' => 'webprofiles@greeklicensing.com',
                    'from_name' => 'Greek Licensing Web Profiles',
                    'subject' => 'Your web profile changes have been accepted',
                    'body' => 'Dear ' . $vendor_profile->organization_name . ',' . "<br/><br/>" . 'All or some of the recent changes you have requested to your web profile have been approved.'
                    . "<br/><br/>" . 'To see your new web profile, simply click on the link below and login with your username and password.' . "<br/><br/>"
                    . '<a href="http://Greeklicensing.com">http://Greeklicensing.com</a>' . "<br/><br/>"
                    . 'Once you are logged in, click on the Profile tab and navigate to \'Web Profile \'.<br /><br />'
                    . "Sincerely, <br /><br />"
                    . "Web Profile Department<br />e: webprofiles@greeklicensing.com<br />"
                    . "p: 760-734-6764<br />f: 707-202-0532<br />"
                );

            } 
	    else {
                $params = array(
                    'to' => $user['0']->email,
                    'to_name' => $vendor_profile->organization_name,
                    'from' => 'webprofiles@greeklicensing.com',
                    'from_name' => 'Greek Licensing Web Profiles',
                    'subject' => 'Request to change web profile has been declined',
                    'body' => 'Dear ' . $vendor_profile->organization_name . ',' . "<br/><br/>" . 'We have reviewed the information you have submitted for your public web profile and unfortunately we must decline your request at this time.'
                    . "<br/><br/>" . 'Our reason for declining:' . "<br/><br/>"
                    . "<b>" . $reason . "</b><br/><br/>"
                    . 'We are committed to helping your business by assisting you in creating an acceptable web profile. If you have any questions about the process, please do not hesitate to ask.<br />'
                    . "<br/>Thank you, <br /><br />"
                    . "Web Profile Administrator<br />e: webprofiles@greeklicensing.com<br />"
                    . "p: 760-734-6764<br />f:  707-202-0532 <br />"
                );
            }


            $send = $this->_helper->BUtilities->send_mail($params);
            if (!$send) {

            }
        } else {
            $this->view->status = 'Profile Not Exist.';
        }
    }

    /**
     * Function to
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function invoiceAction() {
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets', 'load_jqui_aristo', 'load_fancy_assets'));
        
        $this->view->status_array = $this->_helper->BUtilities->parseYAML(APPLICATION_PATH . '/configs/user_status.yml');
        
        $this->view->user = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id'=>$this->_helper->BUtilities->getLoggedInUser()));
    }

    /**
     * Function to get vendor invoices
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetVendorsInvoicesAction() {
        $this->_helper->BUtilities->setNoLayout();
        $vendors_model = new Admin_Model_Vendors($this);
        echo $vendors_model->getInvoicesByParams();
    }
    /**
     * Function to get vendor invoices
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetVendorsInvoices2Action() {
        $this->_helper->BUtilities->setNoLayout();
        $vendors_model = new Admin_Model_Vendors($this);
        echo $vendors_model->getInvoicesByParams2();
    }

    /**
     * Function to
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function invoiceCreateAction() {
//        $this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_tinymce_assets();
        $vendor_model = new Admin_Model_Vendors($this);
        $vendor_model->showCreateInvoice();
    }

    /**
     * Function to create invoice by vendor
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function invoiceCreateVendorAction() {
        $this->_helper->JSLibs->load_tinymce_assets();
        $vendor_model = new Admin_Model_Vendors($this);
        $vendor_model->showCreateVendorInvoice();
    }

    public function invoiceGenerateLateFeesAction() {
    	//$this->_helper->JSLibs->load_jqui_assets();
    	$year = $this->_getParam('year');
    	
    	$this->view->yr = $year;
    	

    	if($this->_request->isPost()) {
    		if ($this->getRequest()->getParam('quarter')) {
    			
    			$quarter = $this->getRequest()->getParam('quarter');
    			$year = $this->getRequest()->getParam('year');
    			
    			$this->_helper->BUtilities->setNoLayout();
    			$vendor_model = new Admin_Model_Vendors($this);
    			$vendor_model->getInvoiceGenerateLateFees($quarter, $year);
    		}
    	}
    	
    	
    }

    public function invoiceGenerateAdvPmtAction() {
        $this->_helper->JSLibs->load_jqui_assets();
        if ($this->getRequest()->getParam('quarter') && $this->getRequest()->getParam('fiscal_year')) {
        	error_log("\nfiscal year and quarter is set", 3, "./errorLog.log");
        	$fsYear = $this->getRequest()->getParam('fiscal_year');
        	error_log("\nA", 3, "./errorLog.log");
            $this->_helper->BUtilities->setNoLayout();
            $vendor_model = new Admin_Model_Vendors($this);
        	error_log("\nA", 3, "./errorLog.log");
            $vendor_model->getInvoiceGenerateAdvPmt($fsYear);
//            print_r($this->getRequest()->getParam('quarter'));
        } else {
        	error_log("\nfiscal year or quarter not set", 3, "./errorLog.log");
        }
    }

    public function invoiceSendLateFeeNotificationAction() {

    	$this->_helper->JSLibs->load_jqui_assets(); 
    	
    }

    /**
     * Function to get Vendor Basic Information in a JSON
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetVendorInfoAction() {
        $vendor_id = $this->_getParam('id');
        $vendor = $this->em->getRepository('BL\Entity\User')->findOneBy(array('account_type' => ACC_TYPE_VENDOR, 'id' => $vendor_id));
        if (!sizeof($vendor)) {
            throw new Zend_Controller_Action_Exception("Required Parameter Missing or Incorrect", 404);
        }
        $this->_helper->BUtilities->setNoLayout();
        echo Zend_Json::encode(array(
            'vendor_name' => $vendor->organization_name,
            'address_line1' => $vendor->address_line1,
            'address_line2' => $vendor->address_line2,
            'city' => $vendor->city,
            'state' => $vendor->state,
            'zipcode' => $vendor->zipcode,
            'phone' => $vendor->phone,
            'fax' => $vendor->fax,
            'email' => $vendor->email,
            'phone2' => $vendor->phone2,
            'inv_date' => date("m/d/Y")
        ));
    }

    /**
     * Function to test gearman workers
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function testAction() {
        $this->_helper->BUtilities->setNoLayout();
        $gmc = new GearmanClient();
        $gmc->addServer('127.0.0.1');
        $task = $gmc->doBackground("amcwork", "sendInvoices");
        /** This will get the work done untill all invoices are done * */
    }

    /**
     * Function to generate license for insurance and non insurance licens
     * @author Zea
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access pulic
     * @param <string> image link
     * @return void
     */
    public function licenseTemplateAction() {
        $this->_helper->BUtilities->setNoLayout();
        $license = $this->em->getRepository("BL\Entity\License")->findOneBy(array('id' => (int) $this->_getParam('id', 0)));
        if ($this->_getParam('license') == "null") {
            $license_template = $this->em->getRepository("BL\Entity\LicenseTemplate")->findOneBy(array('client' => $license->client_id->id, 'has_insurance' => NULL));
        } else if ($this->_getParam('license') == "insurance") {
            $license_template = $this->em->getRepository("BL\Entity\LicenseTemplate")->findOneBy(array('client' => $license->client_id->id, 'has_insurance' => '1'));
        }

        $clinet_profile_check = $this->em->getRepository("BL\Entity\ClientProfile")->findOneBy(array('user_id' => $license->client_id->id));
        $vendor_products = $this->em->getRepository("BL\Entity\VendorProduct")->findBy(array('license_id' => $license->id));
        if (is_null($license->license_agree_number)) {
            $license_number = 'A' . $license->applied_date->format('ym') . $license->id;
        } else {
            $license_number = $license->license_agree_number;
        }

        $vendor_products_name = "&nbsp;";
        if (sizeof($vendor_products) > 0) {
            $counts = 0;
            foreach ($vendor_products as $vp) {
                if ($counts == 0) {
                    $vendor_products_name = $vp->product_id->product_name;
                } else {
                    $vendor_products_name = $vendor_products_name . ", " . $vp->product_id->product_name;
                }
                $counts++;
            }
        }
        if (sizeof($license_template) > 0) {
            //$form->license_specific_note->setValue(@$license_template->notes);
        } else {
            $master_template = $this->em->getRepository("BL\Entity\MasterTemplate")->findBy(array(), array('id' => 'DESC'), 1);
            $license_template = @$master_template[0];
        }

        $pattern = array('/\[CLIENT_ORG]/', '/\[VENDOR_COMPANY]/', '/\[VENDOR_ADDRESS]/', '/\[CLIENT_ADDRESS]/', '/\[NOW_DATE]/', '/\[LICENSE_NUM]/',
            '/\[GREEK_TRADEMARKS]/', '/\[ROYALTY_DESCRIPTION]/', '/\[CLIENT_LATE_FEE]/', '/\[PRODUCT_DETAIL]/', '/\[CLIENT_STATE]/', '/\[CLIENT_ADDRESS1]/', '/\[VENDOR_ADDRESS1]/');
        $replace = array($license->client_id->organization_name, $license->vendor_id->organization_name,
            $license->vendor_id->address_line1 . ' ' . $license->vendor_id->address_line2 . '<br />' . $license->vendor_id->city . ' ' . $license->vendor_id->state . ' ' . $license->vendor_id->zipcode,
            $license->client_id->address_line1 . ' ' . $license->client_id->address_line2 . '<br />' . $license->client_id->city . ' ' . $license->client_id->state . ' ' . $license->client_id->zipcode,
            date('d') . ' of ' . date('M') . ', ' . date('Y'), $license_number, $clinet_profile_check->greek_grant_of_license,
            '<span class="commission">$__' . $clinet_profile_check->greek_royalty_description . ' </span>',
            '<span class="latefee">$__' . $clinet_profile_check->greek_default_late_fee . '</span>', '<span class="v_product">' . $vendor_products_name . '</span>', $license->client_id->state,
            $license->client_id->address_line1 . ' ' . $license->client_id->address_line2 . ' ' . $license->client_id->city . ' ' . $license->client_id->state . ' ' . $license->client_id->zipcode, $license->vendor_id->address_line1 . ' ' . $license->vendor_id->address_line2 . ' ' . $license->vendor_id->city . ' ' . $license->vendor_id->state . ' ' . $license->vendor_id->zipcode);
//'<span class="commission">commission: $__' . $clinet_profile_check->greek_default_renewal_fee . ' Renewal Fee: $__' . $clinet_profile_check->annual_advance . '</span>',
        $template = preg_replace($pattern, $replace, $license_template->template);
        $template = str_replace('$__', '$', $template);

        echo Zend_Json::encode(array('template' => $template));
    }

    /**
     * Function to print in pdf
     * @author Zea
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     * @param Object $license
     * @return void
     */
    public function printinpdfAction() {

        $this->_helper->BUtilities->setNoLayout();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $formData = $request->getPost();
        $license = $formData['agreement_statement'];
        $path = $request->getScheme() . '://' . $request->getHttpHost() . Zend_Controller_Front::getInstance()->getBaseUrl();
        $text = "../../../../assets";
        $licensing_agreement = $license;
        $licensing_agreement = str_replace($text, $path . '/assets', $licensing_agreement);
        $pattern = '/&nbsp;<br>/';
        $replace = '<br pagebreak="true" />';
        $licensing_agreement = preg_replace($pattern, $replace, $licensing_agreement);
		//$this->view->BUtils()->doctrine_dump($licensing_agreement);
        //die('----------------');


        require_once('ThirdParty/tcpdf/config/lang/eng.php');
        require_once('ThirdParty/tcpdf/tcpdf.php');

        // create new PDF document
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
		$pdf->info = $formData['lic_num'];
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('AMC Admin');
        $pdf->SetTitle('Licensing Agreement');
        $pdf->SetSubject('Licensing Agreement between clinet and Vendor');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        //set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT, true);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);   //for margin footer and add page number in each page
        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // set JPEG quality
        $pdf->setJPEGQuality(100);

        //set some language-dependent strings
        $pdf->setLanguageArray($l);
        // ---------------------------------------------------------
        // set default font subsetting mode
        $pdf->setFontSubsetting(true);
        // Set font
//        $pdf->SetFont('helvetica', '', 10);
        //$fontname = $pdf->addTTFfont('/../../../../assets/fonts/droidsans-webfont.ttf', 'TrueTypeUnicode', '', 32);
        $pdf->SetFont('dejavusans', '', 8, '', true);
        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage();

//        $pdf_html = $licensing_agreement;

        $pdf->writeHTML($licensing_agreement, true, 0, true, 0);
        $dt = date("m_d_y_h_i_s");
        $real_path = realpath(dirname(__FILE__) . '/../../../../tmp');
        $pdf->Output($real_path . "/license_agreement_" . "_" . $dt . ".pdf", 'F');
        //return $real_path . "/license_agreement_" . "_" . $dt . ".pdf";
        echo Zend_Json::encode(array('template' => $real_path . "/license_agreement_" . "_" . $dt . ".pdf", 'name' => "/license_agreement_" . "_" . $dt . ".pdf"));
    }

    /**
     * Function to generate pdf link
     * @author Zea
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access pulic
     * @param <string> image link
     * @return void
     */
    public function pdflinkAction() {
        $this->_helper->BUtilities->setNoLayout();
        $path = rtrim(Zend_Controller_Front::getInstance()->getBaseUrl(), '/') . "/tmp/" . $this->_getParam('filename');
        echo '<div style="font-family: DroidSansRegular,\"Segoe UI\",\"Lucida Sans Unicode\",\"Lucida Grande\",sans-serif;font-size: 13px;">';
        echo '<a target="_blank" href="' . $path . '">Click </a>to download the PDF</div> ';
    }

    /**
     * Function to save licensing applicatin
     * @author Zea
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access pulic
     * @param <string> image link
     * @return void
     */
    public function saveAction() {
        $this->_helper->BUtilities->setNoLayout();
        $formData = $this->getRequest()->getPost();

        //print_r($formData);
        $license = $this->em->getRepository("BL\Entity\License")->findOneBy(array('id' => (int) $this->_getParam('id', 0)));

        if ($formData['payment_status'] == 1) {
            $payment_status = 'paid';
        } else {
            $payment_status = 'not-paid';
        }
        if (is_null($license->license_agree_number)) {
            $license_number = 'A' . $license->applied_date->format('ym') . $license->id;
        } else {
            $license_number = $license->license_agree_number;
        }

        $license->vendor_name = $formData['vendor_name'];
        $license->client_name = $formData['client_name'];
        $license->vendor_products = $formData['vendor_products'];
        $license->royalty_structure = $formData['royalty_structure'];
        $license->vendor_type = $formData['vendor_type'];
        $license->royalty_commission = $formData['royalty_commission'];
        $license->royalty_commission_type = $formData['vendor_type']==1 ? '%' : '$';
        $license->sharing = $formData['sharing'];
        $license->annual_advance = $formData['annual_advance'];
        $license->default_renewal_fee = $formData['annual_advance'];
        $license->royalty_description = $formData['royalty_description'];
        $license->grant_of_license = $formData['grant_of_license'];
        $license->sample_status = $formData['sample_status'];
        $license->payment_status = $payment_status;
        $license->agreement_statement = $formData['agreement_statement'];
        $license->license_specific_note = $formData['license_specific_note'];
        $license->recom_for_client = $formData['recom_for_client'];
        $license->recom_for_vendor = $formData['recom_for_vendor'];
        $license->supplier_name = $formData['supplier_name'];
        $license->target_audience_vendor = $formData['target_audience'];
        $license->product_sample_link = $formData['sample_product_list_link'];
        $license->license_agree_number = $formData['license_number'];
        $license->save_status = 1;
        //echo     $formData['license_number'];
//       $this->view->BUtils()->doctrine_dump($license->license_number);
//       die();
        $this->em->persist($license);
        $this->em->flush();

        echo Zend_Json::encode(array('error' => false, 'message' => "Saved the information"));
    }

    /**
     * Function to Edit Clients
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function editLicClientsAction() {
        $this->_helper->BUtilities->setEmptyLayout();
        $search_model = new Admin_Model_Search($this);
        $search_model->editLicClients();
    }

    /**
     * get the detail of advance balance
     */
    public function advanceBalanceAction() {
        $this->_helper->BUtilities->setEmptyLayout();
    }
    /**
     * get the detail of advance balance
     */
    public function paymentreportsAction() {
	if(!isset($_REQUEST["quarter"]))
		$_REQUEST["quarter"]="";
	if(!isset($_REQUEST["export"]))
                $_REQUEST["export"]="";
	if(isset($_REQUEST["year"])){
	        $items = $this->em->getRepository('BL\Entity\PaymentLineItems')->getLineItemsByVendor($_REQUEST["year"]);
	}
	else
	{
		$items = $this->em->getRepository('BL\Entity\PaymentLineItems')->getLineItemsByVendor('-');
	}
	$quarters = array('1' => 'First', '2' => 'Second', '3' => 'Third', '4' => 'Fourth');
        $alt = true;
        $set = array();
	$total = array();
	$total['Total Amount Per Quarter'][2] = 0;
        $total['Total Amount Per Quarter'][3] = 0;
        $total['Total Amount Per Quarter'][4] = 0;
        $total['Total Amount Per Quarter'][5] = 0;
        $total['Total Amount Per Quarter'][6] = 0;
        $total['Total Amount Per Quarter'][7] = 0;
        $total['Total Amount Per Quarter'][8] = 0;
        $total['Total Amount Per Quarter'][9] = 0;
        $total['Total Amount Per Quarter'][10] = 0;
        $total['Total Amount Per Quarter'][11] = 0;
        $total['Total Amount Per Quarter'][12] = 0;
        $total['Total Amount Per Quarter'][13] = 0;

        foreach ($items as $item){
                $amcAmount = 0;
                $subTotal = 0;
                if (isset($item->pmt_id->invoice)) {
                        if (($item->amount_paid) && $item->pmt_id->invoice->invoice_type != 'Refund') {
                                if ($item->sharing == 1) {
                                        $amcAmount = ($item->amount_paid * $item->percent_amc);
                                }
                                $subTotal = $item->amount_paid - $amcAmount;
                        } elseif (($item->late_paid) && $item->pmt_id->invoice->invoice_type != 'Refund') {
                                if ($item->sharing == 1) {
                                        $amcAmount = ($item->late_paid * $item->percent_amc);
                                }
                                $subTotal = $item->late_paid - $amcAmount;
                        } elseif (($item->adv_paid) && $item->pmt_id->invoice->invoice_type != 'Refund') {
                                if ($item->sharing == 1) {
                                    $amcAmount = ($item->adv_paid * $item->percent_amc);
                                }
                                $subTotal = $item->adv_paid - $amcAmount;
                        } else {
                                $amcAmount = 0;
                                $subTotal = 0;
                        }
                } else {
                    if (($item->amount_paid)) {
                        if ($item->sharing == 1) {
                            $amcAmount = ($item->amount_paid * $item->percent_amc);
                        }
                        $subTotal = $item->amount_paid - $amcAmount;
                    } elseif (($item->late_paid)) {
                        if ($item->sharing == 1) {
                            $amcAmount = ($item->late_paid * $item->percent_amc);
                        }
                        $subTotal = $item->late_paid - $amcAmount;
                    } elseif (($item->adv_paid)) {
                        if ($item->sharing == 1) {
                            $amcAmount = ($item->adv_paid * $item->percent_amc);
                        }
                        $subTotal = $item->adv_paid - $amcAmount;
                    } else {
                        $amcAmount = 0;
                        $subTotal = 0;
                    }
                }
                if(isset($set[$item->pmt_id->invoice->company_name])){
                        switch($item->payment_quarter){
                                case 1:
                                        $set[$item->pmt_id->invoice->company_name]['2']= $set[$item->pmt_id->invoice->company_name]['2']+$subTotal;
                                        $set[$item->pmt_id->invoice->company_name]['3']= $set[$item->pmt_id->invoice->company_name]['3']+$amcAmount;
                                        $set[$item->pmt_id->invoice->company_name]['4']= $set[$item->pmt_id->invoice->company_name]['4']+$subTotal+$amcAmount;
					$total['Total Amount Per Quarter'][2] +=$subTotal;
			                $total['Total Amount Per Quarter'][3] +=$amcAmount;
                			$total['Total Amount Per Quarter'][4] +=$subTotal+$amcAmount;
                                        break;
                                case 2:
                                        $set[$item->pmt_id->invoice->company_name]['5']= $set[$item->pmt_id->invoice->company_name]['5']+$subTotal;
                                        $set[$item->pmt_id->invoice->company_name]['6']= $set[$item->pmt_id->invoice->company_name]['6']+$amcAmount;
                                        $set[$item->pmt_id->invoice->company_name]['7']= $set[$item->pmt_id->invoice->company_name]['7']+$subTotal+$amcAmount;
					$total['Total Amount Per Quarter'][5] +=$subTotal;
                                        $total['Total Amount Per Quarter'][6] +=$amcAmount;
                                        $total['Total Amount Per Quarter'][7] +=$subTotal+$amcAmount;
                                        break;
                                case 3:
                                        $set[$item->pmt_id->invoice->company_name]['8']= $set[$item->pmt_id->invoice->company_name]['8']+$subTotal;
                                        $set[$item->pmt_id->invoice->company_name]['9']= $set[$item->pmt_id->invoice->company_name]['9']+$amcAmount;
                                        $set[$item->pmt_id->invoice->company_name]['10']= $set[$item->pmt_id->invoice->company_name]['10']+$subTotal+$amcAmount;
					$total['Total Amount Per Quarter'][8] +=$subTotal;
                                        $total['Total Amount Per Quarter'][9] +=$amcAmount;
                                        $total['Total Amount Per Quarter'][10] +=$subTotal+$amcAmount;
                                        break;
                                case 4:
                                        $set[$item->pmt_id->invoice->company_name]['11']= $set[$item->pmt_id->invoice->company_name]['11']+$subTotal;
                                        $set[$item->pmt_id->invoice->company_name]['12']= $set[$item->pmt_id->invoice->company_name]['12']+$amcAmount;
                                        $set[$item->pmt_id->invoice->company_name]['13']= $set[$item->pmt_id->invoice->company_name]['13']+$subTotal+$amcAmount;
					$total['Total Amount Per Quarter'][11] +=$subTotal;
                                        $total['Total Amount Per Quarter'][12] +=$amcAmount;
                                        $total['Total Amount Per Quarter'][13] +=$subTotal+$amcAmount;
                                        break;
                        }
                }
                else{
                        $set[$item->pmt_id->invoice->company_name] =array('2'=>0,'3'=>0,'4'=>0,'5'=>0,'6'=>0,'7'=>0,'8'=>0,'9'=>0,'10'=>0,'11'=>0,'12'=>0,'13'=>0);
                        switch($item->payment_quarter){
                                case 1:
                                        $set[$item->pmt_id->invoice->company_name]['2']= $subTotal;
                                        $set[$item->pmt_id->invoice->company_name]['3']= $amcAmount;
                                        $set[$item->pmt_id->invoice->company_name]['4']= $subTotal+$amcAmount;
					$total['Total Amount Per Quarter'][2] +=$subTotal;
                                        $total['Total Amount Per Quarter'][3] +=$amcAmount;
                                        $total['Total Amount Per Quarter'][4] +=$subTotal+$amcAmount;
                                        break;
                                case 2:
                                        $set[$item->pmt_id->invoice->company_name]['5']= $subTotal;
                                        $set[$item->pmt_id->invoice->company_name]['6']= $amcAmount;
                                        $set[$item->pmt_id->invoice->company_name]['7']= $subTotal+$amcAmount;
					$total['Total Amount Per Quarter'][5] +=$subTotal;
                                        $total['Total Amount Per Quarter'][6] +=$amcAmount;
                                        $total['Total Amount Per Quarter'][7] +=$subTotal+$amcAmount;
                                        break;
                                case 3:
                                        $set[$item->pmt_id->invoice->company_name]['8']= $subTotal;
                                        $set[$item->pmt_id->invoice->company_name]['9']= $amcAmount;
                                        $set[$item->pmt_id->invoice->company_name]['10']= $subTotal+$amcAmount;
					$total['Total Amount Per Quarter'][8] +=$subTotal;
                                        $total['Total Amount Per Quarter'][9] +=$amcAmount;
                                        $total['Total Amount Per Quarter'][10] +=$subTotal+$amcAmount;
                                        break;
                                case 4:
                                        $set[$item->pmt_id->invoice->company_name]['11']= $subTotal;
                                        $set[$item->pmt_id->invoice->company_name]['12']= $amcAmount;
                                        $set[$item->pmt_id->invoice->company_name]['13']= $subTotal+$amcAmount;
					$total['Total Amount Per Quarter'][11] +=$subTotal;
                                        $total['Total Amount Per Quarter'][12] +=$amcAmount;
                                        $total['Total Amount Per Quarter'][13] +=$subTotal+$amcAmount;
                                        break;
                        }
                }


                }
		if(!empty($set))
			$set = array_merge($set,$total);
//	var_dump($set);
	if($_REQUEST["export"]){
		$this->_helper->BUtilities->setNoLayout();
		foreach($set as $key=>$value)
		{
			$set[$key]['1']=$key;
                        $label = array('Vendor','Q1 Client','Q1 Affinity','Q1 Total','Q2 Client','Q2 Affinity','Q2 Total','Q3 Client','Q3 Affinity','Q3 Total','Q4 Client','Q4 Affinity','Q4 Total','');
			switch($_REQUEST["quarter"]){
				case '1':
					$set[$key]['5']= "";
                                        $set[$key]['6']= "";
                                        $set[$key]['7']= "";
					$set[$key]['8']= "";
                                        $set[$key]['9']= "";
                                        $set[$key]['10']= "";
					$set[$key]['11']= "";
                                        $set[$key]['12']= "";
                                        $set[$key]['13']= "";
                                        $label = array('Vendor','Q1c','Q1a','Q1t','','','','','','','','','','');
                                        break;
                                case '2':
                                        $set[$key]['2']= "";
                                        $set[$key]['3']= "";
                                        $set[$key]['4']= "";
                                        $set[$key]['8']= "";
                                        $set[$key]['9']= "";
                                        $set[$key]['10']= "";
                                        $set[$key]['11']= "";
                                        $set[$key]['12']= "";
                                        $set[$key]['13']= "";
                                        $label = array('Vendor','','','','Q2c','Q2a','Q2t','','','','','','','');
                                        break;
                                case '3':
                                        $set[$key]['2']= "";
                                        $set[$key]['3']= "";
                                        $set[$key]['4']= "";
                                        $set[$key]['5']= "";
                                        $set[$key]['6']= "";
                                        $set[$key]['7']= "";
                                        $set[$key]['11']= "";
                                        $set[$key]['12']= "";
                                        $set[$key]['13']= "";
                                        $label = array('Vendor','','','','','','','Q3c','Q3a','Q3t','','','','');
                                        break;
                                case '4':
                                        $set[$key]['2']= "";
                                        $set[$key]['3']= "";
                                        $set[$key]['4']= "";
                                        $set[$key]['5']= "";
                                        $set[$key]['6']= "";
                                        $set[$key]['7']= "";
                                        $set[$key]['8']= "";
                                        $set[$key]['9']= "";
                                        $set[$key]['10']= "";
					$label = array('Vendor','','','','','','','','','','Q4c','Q4a','Q4t','');
                                        break;
			}
		}
		$result = array('labels'=> $label,'data' => $set ,'title'=>'Royalty Report');
		$vendor_model = new Admin_Model_Vendors($this);
            	$vendor_model->saveCSV($result);
	}
	$this->view->set = $set;
    }
    public function invoicereportsAction() {
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets', 'load_jqui_aristo', 'load_fancy_assets'));
        $this->view->status_array = $this->_helper->BUtilities->parseYAML(APPLICATION_PATH . '/configs/user_status.yml');
    }
    /**
     * Function to Export to pdf
     * @version 0.1
     * @access public
     * @return String
     */
        public function exportResultsPdfAction() {
        $this->_helper->BUtilities->setNoLayout();

        $vendor_model = new Admin_Model_Vendors($this);
        $vendor_model->getPDF();

        exit;
    }
}
