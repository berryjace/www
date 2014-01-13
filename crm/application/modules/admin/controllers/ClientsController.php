<?php
require_once('ThirdParty/tcpdf/tcpdf.php');
class VPDF extends TCPDF {

	public $imgUrl="";
	public $client="";
	//Page header
	// Page footer
	public function Footer() {
		// Position at 15 mm from bottom
		$this->SetY(-15);
		// Set font
		$this->SetFont('helvetica', 'I', 8);
		// Page number
		$this->Cell(0, 10, $this->client->organization_name, 0, false, 'L', 0, '', 0, false, 'T', 'M');
		$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
	}//*/

	public function Header(){
		//$this->Cell(0, 0, "<b>".$this->client->organization_name."--Licensed Vendors</b><small>(as of ".date("M d, Y").")</small>", 0, false, 'C', 0, '', 0, false, 'T', 'M');

		$this->SetFont('helvetica', '', 18);

		$this->Cell(0, 30, $this->client->organization_name . "---Licensed Vendors", 0, false, 'C', 0, '', 0, false, 'M', 'B');

		$this->SetFont('helvetica', '', 10);
		$this->Cell(0, 30, "(as of ". date("M d,Y").")          ", 0, false, 'R', 0, '', 0, false, 'M', 'B');

		$this->Image($this->imgUrl, 3, 1, 25, 25, 'JPG', '', 'L', false, 300, '', false, false, 0, false, false, false);
	}
}

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
		$this->Cell(0, 10, "License Number : [License Number]", 0, false, 'C', 0, '', 0, false, 'T', 'M');
		$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
	}
}

class Admin_ClientsController extends Zend_Controller_Action {

    public $client = null;

    public function init() {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
        //$this->getClient();
    }

    /**
     * Function to getClient
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getClient() {
        $client_id = $this->_getParam('id');
        $this->client = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id' => $client_id, 'account_type' => ACC_TYPE_CLIENT));
        if (!sizeof($this->client)) {
            throw new Zend_Controller_Action_Exception("Required Parameter Missing or Incorrect", 404);
        }
    }

    /**
     * Function to Validate on the form using AJAX
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     *
     *
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

    public function indexAction() {
        $this->view->clients = $this->em->getRepository("BL\Entity\User")->findBy(array('account_type' => ACC_TYPE_CLIENT), array('organization_name' => 'asc'));
    }

    /**
     * Function to get organization list
     * @author Rasidul
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access private
     * @return Array
     *
     *
     */
    private function getOrganizationList() {
        $org_type[''] = 'Select Type';
        $org_list = $this->em->getRepository("BL\Entity\OrganizationType")->findAll();
        foreach ($org_list as $org) {
            $org_type[$org['id']] = $org['name'];
        }
        return $org_type;
    }

    /**
     * Function to View Client basic info [Setup Tab]
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     *
     *
     */
    public function viewAction() {
        $this->_helper->JSLibs->load_jqui_aristo();
        $this->getClient();

        $clientProfile = $this->em->getRepository('BL\Entity\ClientProfile')->findOneBy(array('user_id' => $this->client->id));

        $options = array();
        $options['org_type'] = $this->getOrganizationList();
        $options['greek_org_type'] = (isset($clientProfile->greek_org_type) AND !is_null($clientProfile->greek_org_type)) ? $clientProfile->greek_org_type : '0';


        $form = new Admin_Form_ClientSetup($options);
        $this->view->form = $form;
        $this->view->client = $this->client;
        //$this->view->BUtils()->doctrine_dump($this->client);
        //die('@@@');
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $this->ajaxValidate($form, $formData);

            if ($form->isValid($formData)) {
                //Zend_Debug::dump($formData);

                $user = $this->client;
                $user->organization_name = $form->getValue('client_name');
                $user->address_line1 = $form->getValue('address1');
                $user->address_line2 = $form->getValue('address2');
                $user->city = $form->getValue('city');
                $user->state = $form->getValue('state');
                $user->zipcode = $form->getValue('zip_code');
                $user->email = $form->getValue('email');
                $user->agreement_notification_email = $form->getValue('agreement_notification_email');
                $user->phone = $form->getValue('phone');
                $user->fax = $form->getValue('fax');
                $user->website = $form->getValue('url');
                $user->updated_at = new DateTime();
                $user->user_code = $form->getValue('user_code');
                $this->em->persist($user);
                $this->em->flush();

                $userContact = $this->em->getRepository('BL\Entity\UserContact')->findOneBy(array('user_id' => $user->id));
                if (count($userContact) > 0) {
                    $userContact->user_id = $user;
                    $userContact->address_line1 = $user->address_line1;
                    $userContact->city = $user->city;
                    $userContact->state = $user->state;
                    $userContact->zipcode = $user->zipcode;
                    $userContact->phone = $user->phone;
                    $userContact->fax = $user->fax;
                    $this->em->persist($userContact);
                    $this->em->flush();
                } else {
                    $class = 'BL\Entity\UserContact';
                    $userContact = new $class();
                    $userContact->user_id = $user;
                    $userContact->address_line1 = $user->address_line1;
                    $userContact->city = $user->city;
                    $userContact->state = $user->state;
                    $userContact->zipcode = $user->zipcode;
                    $userContact->phone = $user->phone;
                    $userContact->fax = $user->fax;
                    $this->em->persist($userContact);
                    $this->em->flush();
                }


                $clientProfile = $this->em->getRepository('BL\Entity\ClientProfile')->findOneBy(array('user_id' => $user->id));
                if (count($clientProfile) > 0) {
                    $clientProfile->user_id = $user;
                    $clientProfile->greek_name = $form->getValue('greek_letters');
                    $clientProfile->greek_royalty_description = $form->getValue('description');
                    $clientProfile->greek_approved_contact_person = $form->getValue('approved_contact');
                    $clientProfile->greek_default_renewal_fee = $form->getValue('def_ren_fee');
                    $clientProfile->greek_default_late_fee = $form->getValue('def_late_fee');
                    //$clientProfile->greek_org_type = $form->getValue('org_type');
                    $clientProfile->greek_org_type = $this->em->find('BL\Entity\OrganizationType', $form->getValue('org_type'));
                    $clientProfile->greek_founding_year = new DateTime(date('Y-m-d', strtotime('01/01/'.$form->getValue('founding_year'))));
                    $clientProfile->greek_number_of_alumni = (int) $form->getValue('num_alumni');
                    $clientProfile->greek_number_of_undergrads = (int) $form->getValue('num_undergrads');
                    $clientProfile->greek_number_of_alumni_chapters = (int) $form->getValue('num_alumni_chapters');
                    $clientProfile->greek_total_ug_chapters = (int) $form->getValue('total_ug_chapters');
                    $clientProfile->greek_grant_of_license = $form->getValue('grant_of_lic');
                    $this->em->persist($clientProfile);
                    $this->em->flush();
                } else {
                    $class = 'BL\Entity\ClientProfile';
                    $clientProfile = new $class();
                    $clientProfile->user_id = $user;
                    $clientProfile->greek_name = $form->getValue('greek_letters');
                    $clientProfile->greek_royalty_description = $form->getValue('description');
                    $clientProfile->greek_approved_contact_person = $form->getValue('approved_contact');
                    $clientProfile->greek_default_renewal_fee = $form->getValue('def_ren_fee');
                    $clientProfile->greek_default_late_fee = $form->getValue('def_late_fee');
                    $clientProfile->greek_org_type = $this->em->find('BL\Entity\OrganizationType', $form->getValue('org_type'));
                    $clientProfile->greek_founding_year = new DateTime(date('Y-m-d', strtotime($form->getValue('founding_year'))));
                    $clientProfile->greek_number_of_alumni = (int) $form->getValue('num_alumni');
                    $clientProfile->greek_number_of_undergrads = (int) $form->getValue('num_undergrads');
                    $clientProfile->greek_number_of_alumni_chapters = (int) $form->getValue('num_alumni_chapters');
                    $clientProfile->greek_total_ug_chapters = (int) $form->getValue('total_ug_chapters');
                    $clientProfile->greek_grant_of_license = $form->getValue('grant_of_lic');
                    $this->em->persist($clientProfile);
                    $this->em->flush();
                }

                $this->_helper->flashMessenger("Client information update succesfully!", "Info");
                $this->_redirect($this->view->BUrl()->absoluteUrl());
            } else {
                $form->populate($formData);
            }
        } else {
            /**
             * Populate from DB with existing data
             */
            $existing_data = array(
                'client_name' => $this->client->organization_name,
                'address1' => $this->client->address_line1,
                'address2' => $this->client->address_line2,
                'city' => $this->client->city,
                'state' => ($this->client->state == '' || $this->client->state == NULL ? 'NULL' : $this->client->state ),
                'zip_code' => $this->client->zipcode,
                'email' => $this->client->email,
            	'agreement_notification_email' => $this->client->agreement_notification_email,
                'url' => $this->client->website,
                'phone' => $this->client->phone,
                'fax' => $this->client->fax,
            	'user_code' => $this->client->user_code
            );
            if (sizeof($clientProfile)) {
                $existing_data['greek_letters'] = $clientProfile->greek_name;
                $existing_data['description'] = $clientProfile->greek_royalty_description;
                $existing_data['approved_contact'] = $clientProfile->greek_approved_contact_person;
                $existing_data['def_ren_fee'] = $clientProfile->greek_default_renewal_fee;
                $existing_data['def_late_fee'] = $clientProfile->greek_default_late_fee;
                //'royalty' => $clientProfile->zipcode,
                $existing_data['org_type'] = $clientProfile->greek_org_type->id;
                $existing_data['founding_year'] = ($clientProfile->greek_founding_year) != '' ? $clientProfile->greek_founding_year->format('Y') : '';
                $existing_data['num_alumni'] = $clientProfile->greek_number_of_alumni;
                $existing_data['num_undergrads'] = $clientProfile->greek_number_of_undergrads;
                $existing_data['num_alumni_chapters'] = $clientProfile->greek_number_of_alumni_chapters;
                $existing_data['total_ug_chapters'] = $clientProfile->greek_total_ug_chapters;
                $existing_data['grant_of_lic'] = $clientProfile->greek_grant_of_license;
            }
            $form->populate($existing_data);
        }
    }

    public function addNoteAction() {
        $this->view->client = $this->client;
    }

    public function deleteFileAction() {
        $this->view->client = $this->client;
    }

    public function uploadAction() {
        $this->view->client = $this->client;
    }

    public function getAppendixListAction() {
        $this->view->client = $this->client;
    }

    public function editLicenseAction() {
        $this->view->client = $this->client;
    }

    public function showLicenseAction() {
        $this->view->client = $this->client;
    }

    public function legalAction() {
        $this->getClient();
        $form = new Admin_Form_ClientLegal();
        $this->view->form = $form;
        $this->view->client = $this->client;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $clientLegal = $this->em->getRepository('BL\Entity\ClientLegal')->findOneBy(array('user_id' => $this->client->id));
                if (count($clientLegal) > 0) {
                    $clientLegal->user_id = $this->client;
                    $clientLegal->choice_of_law = $form->getValue('org_choice_law');
                    $clientLegal->licensor_title = $form->getValue('licensor_title');
                    $clientLegal->legal_name = $form->getValue('legal_name');
                    $clientLegal->legal_firm = $form->getValue('legal_firm');
                    $clientLegal->legal_address_line1 = $form->getValue('legal_address1');
                    $clientLegal->legal_address_line2 = $form->getValue('legal_address2');
                    $clientLegal->legal_city = $form->getValue('legal_city');
                    $clientLegal->legal_state = $form->getValue('legal_state');
                    $clientLegal->legal_zipcode = $form->getValue('legal_zip');
                    $clientLegal->legal_phone = $form->getValue('legal_phone');
                } else {
                    $class = 'BL\Entity\ClientLegal';
                    $clientLegal = new $class();
                    $clientLegal->user_id = $this->client;
                    $clientLegal->choice_of_law = $form->getValue('org_choice_law');
                    $clientLegal->licensor_title = $form->getValue('licensor_title');
                    $clientLegal->legal_name = $form->getValue('legal_name');
                    $clientLegal->legal_firm = $form->getValue('legal_firm');
                    $clientLegal->legal_address_line1 = $form->getValue('legal_address1');
                    $clientLegal->legal_address_line2 = $form->getValue('legal_address2');
                    $clientLegal->legal_city = $form->getValue('legal_city');
                    $clientLegal->legal_state = $form->getValue('legal_state');
                    $clientLegal->legal_zipcode = $form->getValue('legal_zip');
                    $clientLegal->legal_phone = $form->getValue('legal_phone');
                }
                $this->em->persist($clientLegal);
                $this->em->flush();

                $this->_helper->flashMessenger("Legal Information Update succesfully!", "Info");
                $this->_redirect($this->view->BUrl()->absoluteUrl());
            } else {
                $form->populate($formData);
            }
        } else {
            $form->populate($this->generate_default_client_legal($this->client));
        }
    }

    /**
     * Function to View Client Operation info
     * @author Rashed
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     */
    public function operationsAction() {
        $this->_helper->JSLibs->load_jqui_aristo();
        $this->getClient();
        $form = new Admin_Form_ClientOperations();
        $this->view->form = $form;
        $this->view->client = $this->client;
        $existing_data = array();
        $clientOperation = $this->em->getRepository('BL\Entity\ClientOperation')->findOneBy(array('user_id' => $this->client->id));

        if (sizeof($clientOperation)) {
            $existing_data = array(
                'license_agreement_signee' => $clientOperation->letter_signee,
                'signee_title' => $clientOperation->letter_signee2,
                'org_magazine_circulation_size' => $clientOperation->circulation_size,
                'frequency_of_mag' => $clientOperation->frequency_of_mag,
                'accept_advertising' => $clientOperation->accept_advertising,
                'advertising_rates' => $clientOperation->advertising_rates,
                'advertising_deadlines' => (!is_null($clientOperation->advertising_deadlines)) ? $clientOperation->advertising_deadlines->format("m/d/Y") : '',
                'workshop_names' => $clientOperation->workshop_names,
                'org_workshop_type' => $clientOperation->workshop_type,
                'org_workshop_dates' => (!is_null($clientOperation->workshop_dates)) ? $clientOperation->workshop_dates->format("m/d/Y") : '',
                'convention_name' => $clientOperation->convention_name,
                'org_convention_site' => $clientOperation->convention_site,
                'org_convention_dates' => (!is_null($clientOperation->convention_date)) ? $clientOperation->convention_date->format("m/d/Y") : '',
                'org_pms_colors_1' => $clientOperation->pms_color_1,
                'org_pms_colors_2' => $clientOperation->pms_color_2,
                'org_pms_colors_3' => $clientOperation->pms_color_3,
                'org_pms_colors_4' => $clientOperation->pms_color_4,
                'default_note_to_all_applying_vendors' => $clientOperation->notes,
                'client_status' => $this->client->user_status,
                'commission_start_date' => (!is_null($clientOperation->commission_start_date)) ? $clientOperation->commission_start_date->format("m/d/Y") : '',
                'commission_per' => ($clientOperation->commission_per != null && $clientOperation->commission_per != '') ? $clientOperation->commission_per : '0.30' ,
                'sharing' => ($clientOperation->sharing != null && $clientOperation->sharing != '') ? $clientOperation->sharing : 'yes'
            );
        }

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                if (count($clientOperation) > 0) {
                    $clientOperation->user_id = $this->client;
                    $clientOperation->letter_signee = $form->getValue('license_agreement_signee');
                    $clientOperation->letter_signee2 = $form->getValue('signee_title');
                    $clientOperation->circulation_size = $form->getValue('org_magazine_circulation_size');
                    $clientOperation->frequency_of_mag = $form->getValue('frequency_of_mag');
                    $clientOperation->accept_advertising = $form->getValue('accept_advertising');
                    $clientOperation->advertising_rates = $form->getValue('advertising_rates');
                    $clientOperation->advertising_deadlines = new DateTime(date('Y-m-d', strtotime($form->getValue('advertising_deadlines'))));
                    $clientOperation->workshop_names = $form->getValue('workshop_names');
                    $clientOperation->workshop_type = $form->getValue('org_workshop_type');
                    $clientOperation->workshop_dates = new DateTime(date('Y-m-d', strtotime($form->getValue('org_workshop_dates'))));
                    $clientOperation->convention_name = $form->getValue('convention_name');
                    $clientOperation->convention_site = $form->getValue('org_convention_site');
                    $clientOperation->convention_date = new DateTime(date('Y-m-d', strtotime($form->getValue('org_convention_dates'))));
                    $clientOperation->pms_color_1 = $form->getValue('org_pms_colors_1');
                    $clientOperation->pms_color_2 = $form->getValue('org_pms_colors_2');
                    $clientOperation->pms_color_3 = $form->getValue('org_pms_colors_3');
                    $clientOperation->pms_color_4 = $form->getValue('org_pms_colors_4');
                    $clientOperation->notes = $form->getValue('default_note_to_all_applying_vendors');
                    $clientOperation->commission_start_date = new DateTime(date('Y-m-d', strtotime($form->getValue('commission_start_date'))));
                    $clientOperation->commission_per = $form->getValue('commission_per');
                    $clientOperation->sharing = $form->getValue('sharing');


                    $this->client->user_status = $form->getValue('client_status');
                    if ($form->getValue('client_status') == "Current") {
                        $this->client->reg_status = "activated";
                    } else {
                        $this->client->reg_status = $form->getValue('client_status');
                    }
                } else {
                    $class = 'BL\Entity\ClientOperation';
                    $clientOperation = new $class();
                    $clientOperation->user_id = $this->client;
                    $clientOperation->letter_signee = $form->getValue('license_agreement_signee');
                    $clientOperation->letter_signee2 = $form->getValue('signee_title');
                    $clientOperation->circulation_size = $form->getValue('org_magazine_circulation_size');
                    $clientOperation->frequency_of_mag = $form->getValue('frequency_of_mag');
                    $clientOperation->accept_advertising = $form->getValue('accept_advertising');
                    $clientOperation->advertising_rates = $form->getValue('advertising_rates');
                    $clientOperation->advertising_deadlines = new DateTime(date('Y-m-d', strtotime($form->getValue('advertising_deadlines'))));
                    $clientOperation->workshop_names = $form->getValue('workshop_names');
                    $clientOperation->workshop_type = $form->getValue('org_workshop_type');
                    $clientOperation->workshop_dates = new DateTime(date('Y-m-d', strtotime($form->getValue('org_workshop_dates'))));
                    $clientOperation->convention_name = $form->getValue('convention_name');
                    $clientOperation->convention_site = $form->getValue('org_convention_site');
                    $clientOperation->convention_date = new DateTime(date('Y-m-d', strtotime($form->getValue('org_convention_dates'))));
                    $clientOperation->pms_color_1 = $form->getValue('org_pms_colors_1');
                    $clientOperation->pms_color_2 = $form->getValue('org_pms_colors_2');
                    $clientOperation->pms_color_3 = $form->getValue('org_pms_colors_3');
                    $clientOperation->pms_color_4 = $form->getValue('org_pms_colors_4');
                    $clientOperation->notes = $form->getValue('default_note_to_all_applying_vendors');

                    $this->client->user_status = $form->getValue('client_status');
                    if ($form->getValue('client_status') == "Current") {
                        $this->client->reg_status = "activated";
                    } else {
                        $this->client->reg_status = $form->getValue('client_status');
                    }
                }
                $this->em->persist($clientOperation);
                $this->em->flush();

                $this->em->persist($this->client);
                $this->em->flush();

                $this->_helper->flashMessenger("Operation updates succesfully!", "Info");
                $this->_redirect($this->view->BUrl()->absoluteUrl());
            } else {
                $form->populate($formData);
            }
        } else {
            $form->populate($existing_data);
        }
    }

    /**
     * Function to Show Vendors of a specific client.
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function vendorsAction() {
        $this->getClient();
        $this->view->client = $this->client;
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets'));
        $this->_helper->JSLibs->load_fancy_assets();
    }

    /**
     * Function to provide JSON data to feed data table
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetVendorsAction() {
        $params = array(
            'search' => $this->_getParam('sSearch', ''),
            'page_start' => $this->_getParam('iDisplayStart', 1),
            'draw_count' => $this->_getParam('sEcho', 1),
            'per_page' => $this->_getParam('iDisplayLength', 10),
            'client_id' => $this->_getParam('id', null)
        );
        /**
         * Let's take care of the sorting column to be passed to doctrine.
         * DataTable sends params like iSortCol_0.
         */
        $sorting_cols = array('0' => 'v.organization_name', '1' => 'c.organization_name', '2' => 'l.applied_date', '3' => 'l.status');
//        $params['sort_key'] = $sorting_cols[$this->_getParam('iSortCol_0')];
        $params['sort_key'] = $sorting_cols[$this->_getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->_getParam('sSortDir_0');

        $records = $this->em->getRepository("BL\Entity\License")->getClientVendors($params)->getResult();
        $params['show_total'] = true;
        $records_total = $this->em->getRepository("BL\Entity\License")->getClientVendors($params);
        $status_array = $this->_helper->BUtilities->parseYAML(APPLICATION_PATH . '/configs/statuses.yml');
        $this->_helper->BUtilities->setNoLayout();
        /**
         * Datatable doesn't understand json_encode and have a custom json format
         */
        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":';
        $prec = array();
        foreach ($records as $v) {
            $prec[] = array(
                "<a href='javascript:;' class='vendor_link' rel='{$v->vendor_id->id}'>{$v->vendor_id->organization_name}</a>",
                $v->client_id->organization_name,
                $v->applied_date->format("M d, Y H:i A"),
                (isset($status_array[$v->status]) ? $status_array[$v->status] : $v->status)
            );
        }
        $json .= Zend_Json::encode($prec);
        $json .= '}';
        echo $json;
    }

    public function ajaxGetVendorPdfAction(){
		ini_set('max_execution_time', 600); //300 seconds = 5 minutes
    	$this->_helper->BUtilities->setNoLayout();
    	
    	$client_id = $this->_getParam('client_id');
    	
    	$client = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id'=>$client_id));
    	
    	$licenses = $this->em->getRepository("BL\Entity\License")->getClientLicensesSortedVendor($client->id);
    	
    	$licenseArray = array();
    	
    	foreach($licenses as $license){
    		if ($license->vendor_id->user_status == "Current") $licenseArray[] = $license;
    	}
    	
    	$this->view->client = $client;
    	$this->view->licenses = $licenseArray;
    	
    	$this->view->vendorOperationRepository = $this->em->getRepository("BL\Entity\VendorOperation");
    	
    	$html = $this->view->render('clients/ajax-get-vendor-pdf.phtml');
    	
    	require_once('ThirdParty/tcpdf/config/lang/eng.php');
    	require_once('ThirdParty/tcpdf/tcpdf.php');
    	
    	$pdf = new VPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
    	
    	$pdf->client = $client;
    	
    	$pdf->imgUrl = APPLICATION_PATH . "/../assets/images/olp-logo.jpg";
    	
    	
    	$pdf->SetCreator(PDF_CREATOR);
    	$pdf->SetAuthor('');
    	$pdf->SetTitle('Vendors Export PDF');
    	
    	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    	
    	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    	
    	$pdf->SetMargins(0, PDF_MARGIN_TOP, 0, true);
    	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    	
    	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    	
    	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    	
    	$pdf->setLanguageArray($l);
    	
    	$pdf->setFontSubsetting(true);
    	
    	$pdf->SetFont('dejavusans', '', 8, '', true);
    	
    	$pdf->AddPage();
    	
    	$guid = date("mdYhis");
    	
    	$pdf->writeHTML($html, true, 0, true, 0);
    	//$save_to = APPLICATION_PATH . '/../tmp/' . $client->organization_name . "-vendors-" . $guid . ".pdf";
    	$save_to = realpath(dirname(__FILE__) . '/../../../../tmp');
    	
    	$pdf->Output($save_to . "/" . $client->organization_name . "-vendors-" . $guid . ".pdf", 'F');
    	echo Zend_Json::encode(array('code'	=>	'success', 'name'=>( "/" . $client->organization_name . "-vendors-" . $guid . ".pdf")));
    	/*
    	 * 
        $real_path = realpath(dirname(__FILE__) . '/../../../../tmp');
        $pdf->Output($real_path . "/license_agreement_" . "_" . $dt . ".pdf", 'F');
        //return $real_path . "/license_agreement_" . "_" . $dt . ".pdf";
        echo Zend_Json::encode(array('template' => $real_path . "/license_agreement_" . "_" . $dt . ".pdf", 'name' => "/license_agreement_" . "_" . $dt . ".pdf"));
    	 */
    	// echo Zend_Json::encode(array('template' => $real_path . "/license_agreement_" . "_" . $dt . ".pdf", 'name' => "/license_agreement_" . "_" . $dt . ".pdf"));
    }
    
    /**
     * Function add/edit royalty fee for a client.
     * @author Rasidul
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     */
    public function royaltyAction() {
        $this->getClient();
        $this->view->client = $this->client;
        $form = new Admin_Form_RoyaltyFee();
        $this->view->form = $form;
        $clientProfile = $this->em->getRepository('BL\Entity\ClientProfile')->findOneBy(array('user_id' => (int) $this->client->id));
        $form->getElement('greek_grant_of_license')->setValue((sizeof($clientProfile)) ? $clientProfile->greek_grant_of_license : '');
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $clientProfile->greek_grant_of_license = $form->getValue('greek_grant_of_license');
                $this->em->persist($clientProfile);
                $this->em->flush();

                $this->_helper->flashMessenger("Grant of License Update succesfully!", "Info");
                $this->_redirect($this->view->BUrl()->absoluteUrl());
            }
        }
    }

    public function clientNotesAction() {
        $this->getClient();
        $this->view->client = $this->client;
        $form = new Admin_Form_ClientNotes();
        $this->view->form = $form;
        $this->view->client = $this->client;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $clientNote = $this->em->getRepository('BL\Entity\ClientNote')->findOneBy(array('client_id' => $this->client->id));
                if (count($clientNote) > 0) {
                    $clientNote->vendor_id = $this->client;
                    $clientNote->note = $form->getValue('note');
                } else {
                    $class = 'BL\Entity\ClientNote';
                    $clientNote = new $class();
                    $clientNote->client_id = $this->client;
                    $clientNote->note = $form->getValue('note');
                }
                $this->em->persist($clientNote);
                $this->em->flush();

                $this->_helper->flashMessenger("Client Notes Update succesfully!", "Info");
                $this->_redirect($this->view->BUrl()->absoluteUrl());
            } else {
                $form->populate($formData);
            }
        } else {
            $form->populate($this->generate_default_client_note($this->client->id));
        }
    }

    /**
     * @author Rashed
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     * @param <int> $user_id
     * @return <array>
     */
    public function generate_default_client_note($user_id) {

        if ((int) $user_id > 0) {
            $clientNote = $this->em->getRepository('BL\Entity\ClientNote')->findOneBy(array('client_id' => (int) $user_id));
            if (count($clientNote) > 0) {
                $existing_data = array(
                    'note' => $clientNote->note
                );
            } else {
                $existing_data = array(
                    'note' => ''
                );
            }

            return $existing_data;
        }
        return false;
    }

    public function paymentsAction() {
        $this->getClient();
        $this->view->client = $this->client;
    }

    /**
     * Function to show clients correspondence
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function correspondenceAction() {
        $this->getClient();
        $this->view->client = $this->client;
        $this->_helper->JSLibs->load_fancy_assets();
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets'));
        $form = new Admin_Form_ClientCorrespondence();
        $this->view->form = $form;
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
        //echo "testing";
        //echo $search_model->getCorrespondenceClient();
        
        //*
        $params = array(
            'search' => $this->_getParam('sSearch', ''),
            'page_start' => $this->_getParam('iDisplayStart', 1),
            'draw_count' => $this->_getParam('sEcho', 1),
            'per_page' => $this->_getParam('iDisplayLength', 20),
        );
        /**
         * Let's take care of the sorting column to be passed to doctrine.
         * DataTable sends params like iSortCol_0.
         */
        $sorting_cols = array('0' => 'cc.note_time', '1' => 'cc.note');
        $params['sort_key'] = $sorting_cols[$this->_getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->_getParam('sSortDir_0');
        $params['client_id'] = $this->_getParam('id');

        $records = $this->em->getRepository("BL\Entity\ClientCorrespondence")->getCorrespondence($params)->getResult();
        $params['show_total'] = true;
        $records_total = $this->em->getRepository("BL\Entity\ClientCorrespondence")->getCorrespondence($params);
        /**
         * Datatable doesn't understand json_encode and have a custom json format
         */
        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":';
        $prec = array();
        foreach ($records as $v) {
            if (!is_null($v->note_time)) {
                $note_time = ( (int) $v->note_time->format("Y") > 0 ? $v->note_time->format("M d, Y H:i A") : 'N/A');
            } else {
                $note_time = 'N/A';
            }
            $prec[] = array(
              $note_time,
              $this->view->BUtils()->neat_trim(str_replace(chr(13), '', str_replace(chr(10), "", strip_tags(html_entity_decode($v->subject)))), 50),
              $this->view->BUtils()->neat_trim(str_replace(chr(13), '', str_replace(chr(10), "", strip_tags(html_entity_decode($v->note)))), 120),
              '<a href="javascript:;" class="clients_notes_link" rel="' . $v->id . '">View</a>&nbsp;
                  <a href="javascript:;" class="edit_notes_link" rel="' . $v->id . '">Edit</a>&nbsp;
                  <a href="javascript:;" class="delete_note" rel="d-' . $v->id . '">Delete</a>'
             );
        }
        $json .= Zend_Json::encode($prec);
        $json .= '}';
        echo $json;
        //*/
    }

    /**
     * Function to
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetCorrespondenceDetailsAction() {
        $this->_helper->BUtilities->setPopupLayout();
        $note_id = $this->_getParam('note-id', 0);
        $note = $this->em->find('BL\Entity\ClientCorrespondence', (int) $note_id);
        $this->view->correspondence = $note;
//        if ($note->subject == "") {
//            $this->view->correspondence = $note->note;
//
//        } else {
//            $this->view->correspondence = "<b>" . $note->subject . "</b><br />" . $note->note;
//        }
    }

    /**
     * Function to edit client correspondence
     * @author Masud
     * @copyright iVive Labs
     * @version 0.1
     * @access public
     * @return String
     */
    public function editCorrespondenceAction() {
        $this->_helper->BUtilities->setPopupLayout();
        $note_id = $this->_getParam('note-id', 0);
        $note = $this->em->find('BL\Entity\ClientCorrespondence', (int) $note_id);
//        echo $note_id;
        $form = new Admin_Form_ClientCorrespondence();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $note->subject = $form->getValue('subject');
                $note->note = $form->getValue('note');
                $note->update_date = new \DateTime(date("Y-m-d H:i:s"));
                $this->em->persist($note);
                $this->em->flush();

                $this->view->result = array('success' => true, 'message' => 'Correspondence updated successfully!');
            } else {
                $form->populate($formData);
            }
        } else {
            $form->populate(array('subject' => $note->subject, 'note' => $note->note, 'note_id' => $note->id));
        }
        $this->view->form = $form;
    }

    /**
     * Function to Delete Note via Ajax
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxDeleteCorrespondenceAction() {
        $this->_helper->BUtilities->setNoLayout();
        $client_id = $this->_getParam('cid');
        $note_id = $this->_getParam('note_id');
        $note = $this->em->find('BL\Entity\ClientCorrespondence', (int) $note_id);
        $this->em->remove($note);
        $this->em->flush();
        $this->em->clear();
        $note = $this->em->find('BL\Entity\ClientCorrespondence', (int) $note_id);
        if (!sizeof($note)) {
            echo Zend_Json::encode(array('code' => 'success', 'msg' => 'Successfully Deleted the Correspondence Note'));
        } else {
            echo Zend_Json::encode(array('code' => 'error', 'msg' => 'There was a problem deleting the note'));
        }
    }

    /**
     * Function to handle edit contacts of clients
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function addContactAction() {
        $this->getClient();
        $client_model = new Admin_Model_Clients($this);
        $client_model->addContact();
    }

    /**
     * Function to handle edit contacts of clients
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function contactsAction() {
        $this->getClient();
        $this->_helper->JSLibs->do_call(array("load_jqui_assets", "load_dataTable_assets", "load_fancy_assets"));
        $this->view->client = $this->client;
        $client_model = new Admin_Model_Clients($this);
        $client_model->getContacts();
    }

    /**
     * Function to handle edit contacts of clients
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function editContactAction() {
        $client_model = new Admin_Model_Clients($this);
        $client_model->editContact();
    }

    /**
     * Function to handle delete contacts of clients
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function deleteContactAction() {
        $contact_id = $this->_getParam('id');
        $client_id = $this->_getParam('client_id');
        $contact_to_delete = $this->em->getRepository("BL\Entity\UserContact")->findOneBy(array("id" => $contact_id));
        /**
         * Just make sure if it's a client contact or not
         */
        if ($contact_to_delete->user_id->account_type == ACC_TYPE_CLIENT) {
            $this->em->remove($contact_to_delete);
            $this->em->flush();
            $this->em->clear();
            $this->_helper->flashMessenger("Successfully Deleted the contact!", "Info");
            $this->_redirect("/admin/clients/contacts/id/" . $client_id);
        }
    }

    public function licenseAgreementAction() {
        $this->getClient();
        $this->view->client = $this->client;
        $this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_tinymce_assets();
        $this->_helper->JSLibs->load_fancy_assets();
        //$this->_helper->JSLibs->do_call(array('load_fancy_assets'));

        $marge_fields = array(
            'organization name',
            'company name',
            'company address',
            'company city, state zip',
            'day',
            'month',
            'year',
            'organization',
            'address',
            'company',
            'Insignia of organization',
            'Executive Director'
        );
        $this->view->marge_fields = $marge_fields;
        $form = new Admin_Form_LicenseTemplate();

        $this->view->noinsurance = $this->_getParam('noinsurance', NULL);
        $condition = array('client' => $this->client->id);
        $this->view->noinsurance ? ($condition['has_insurance'] = NULL) : ($condition['has_insurance'] = 1);
        $template = $this->em->getRepository("BL\Entity\LicenseTemplate")->findBy($condition, null, 1);
        
        error_log("\ntemplate id: " .$template[0]->id, 3, "./errorLog.log");
        error_log("\nbase template: " . $template[0]->template, 3, "./errorLog.log");

        if ($this->getRequest()->isPost()) {
        	error_log("is post",3 ,"./errorLog.log");
            //if ($this->_request->isXmlHttpRequest()) {
            $formData = $this->getRequest()->getPost();
            $template_content = $formData['template'];
            //print_r($formData);
            if (sizeof($template)) {
                $lic_template = $template[0];
                $lic_template->template = $template_content;
                $this->em->persist($lic_template);
                $this->em->flush();
                $this->em->clear();
                $this->_helper->flashMessenger('Successfully updated', "Info");
                if ($this->_getParam('noinsurance') == 1) {
                    $this->_redirect('/admin/clients/license-agreement/id/' . $this->client->id . '/noinsurance/1');
                } else {
                    $this->_redirect('/admin/clients/license-agreement/id/' . $this->client->id);
                }
            } else if ($template_content) {
                $lic_template = new \BL\Entity\LicenseTemplate();
                $lic_template->template = $template_content;
                $lic_template->client = $this->client;
                $lic_template->has_insurance = $this->view->noinsurance ? NULL : 1;
                $this->em->persist($lic_template);
                $this->em->flush();
                $this->em->clear();
                //$this->_redirect('admin/vendors/sample/confirmation');
//                $this->view->message = array('success' => true, 'message' => 'Successfully added');
                $this->_helper->flashMessenger('Successfully added', "Info");
                if ($this->_getParam('noinsurance') == 1) {
                    $this->_redirect('/admin/clients/license-agreement/id/' . $this->client->id . '/noinsurance/1');
                } else {
                    $this->_redirect('/admin/clients/license-agreement/id/' . $this->client->id);
                }
            } else {
                $this->view->message = array('success' => false, 'message' => 'Please enter license template');
            }
            $template = $this->em->getRepository("BL\Entity\LicenseTemplate")->findBy($condition, null, 1);
        }
        if (!sizeof($template)) {
        	error_log("\ntemplate doesn't exist", 3, "./errorLog.log");
        	
            if ($this->_getParam('noinsurance') == 1) {
                $template = $this->em->getRepository("BL\Entity\MasterTemplate")->findBy(array('has_insurance' => NULL), array('id' => 'DESC'), 1);
            } else {
                $template = $this->em->getRepository("BL\Entity\MasterTemplate")->findBy(array('has_insurance' => (int) 1), array('id' => 'DESC'), 1);
            }
        } else {
        	error_log("\ntemplate exists", 3, "./errorLog.log");
        }

        /**
         * "lop logo" relative path to absolute path conversion
         */
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $path = $request->getScheme() . '://' . $request->getHttpHost() . Zend_Controller_Front::getInstance()->getBaseUrl();
        
        //error_log("\nPath: " . $path, 3, "./errorLog.log");
        
        $text = "../../../../assets";
        $text2 = "../../http:/devgreeklicensing.com/crm/assets";
        $text3 = "../../";
        $licensing_template = @$template[0]->template;
        //error_log("\nunchanged license:\n" . $licensing_template, 3, "./errorLog.log");
        $licensing_template = str_replace($text, $path . '/assets', $licensing_template);
        $licensing_template = str_replace($text2, $path . '/assets', $licensing_template);
        $licensing_template = str_replace($text3, '', $licensing_template);

        //error_log("\nlicense Template:\n".$licensing_template,3 ,"./errorLog.log");
        
        $form->template->setValue($licensing_template);
        $this->view->form = $form;
        //$this->view->template = @$template[0]->template;
    }

    /**
     * @author Rashed
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     */
    public function ajaxTemplateMergeFieldAction() {

        $this->_helper->BUtilities->setNoLayout();
        if ($this->_request->isXmlHttpRequest()) {
            $formData = $this->getRequest()->getPost();
            //$title = $formData['title'];
            //echo '<pre>';print_r($formData); echo '</pre>';
            //$this->getClient();
            $search = array();
            $replace = array();
            $condition = array('user_id' => $formData['user_id']);
            $template = $this->em->getRepository("BL\Entity\LicenseTemplate")->findOneBy($condition, null, 1);
            foreach ($formData as $k => $v) {
                if ($k != 'user_id' && trim($v) != '') {
                    $search[] = htmlspecialchars("<" . str_replace('_', ' ', $k) . ">");
                    $replace[] = trim($v);
                }
            }
            if (count($replace) > 0) {
                $marge = str_replace($search, $replace, $template->template);
                echo $marge;
            }
        }
    }

    /**
     * Function to show client artworks and useage guides
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function artworkAction() {
        $this->getClient();
        $this->_helper->JSLibs->do_call(array('load_fancy_assets', 'load_dataTable_assets'));
        $this->view->client = $this->client;
        $params = array(
            'pg' => $this->_getParam('page', 1),
            'itemPerPage' => 10,
            'num_of_link' => 10,
            'User' => $this->client->id
        );
        $this->view->artworks = $this->em->getRepository("BL\Entity\ClientArtwork")->getArtworks($params);
    }

    /**
     * Function to get usage guide for a particular client
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetUsageGuidesDtAction() {
        $this->_helper->BUtilities->setNoLayout();

        $sorting_cols = array(
            '0' => 'cug.id',
            '1' => 'cug.guide_name',
            '2' => 'cug.guide_content'
        );
        $post_params = $this->_getAllParams();
        $params = array(
            'base_url' => $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl(),
            'user_id' => $this->_getParam('id'),
            'search' => isset($post_params['sSearch']) ? $post_params['sSearch'] : '',
            'current_page' => isset($post_params['iDisplayStart']) ? $post_params['iDisplayStart'] : 1,
            'draw_count' => isset($post_params['sEcho']) ? $post_params['sEcho'] : "",
            'per_page' => isset($post_params['iDisplayLength']) ? $post_params['iDisplayLength'] : 10,
            'sort_key' => isset($sorting_cols[$post_params['iSortCol_0']]) ? $sorting_cols[$post_params['iSortCol_0']] : '',
            'search_op' => isset($post_params['search_op']) ? $post_params['search_op'] : 'AND',
            'sort_dir' => isset($post_params['sSortDir_0']) ? $post_params['sSortDir_0'] : 'ASC',
        );
        //print_r($params);
        $json = $this->em->getRepository('BL\Entity\ClientUsageGuide')->getUsageGuidesForAdmin($params);
        echo $json;
    }

    /**
     * Function to view usage guideline
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function viewUsageGuideAction() {
        $this->_helper->BUtilities->setBlankLayout();
        $this->view->usage_guide = $this->em->getRepository('BL\Entity\ClientUsageGuide')->findOneBy(array('id' => $this->_getParam('id'), 'user_id' => $this->_getParam('user_id')));
    }

    /**
     * Function to download file
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function downloadAction() {
        $file = $this->_getParam('file');
        $ext = explode('.', $file);
        $targetDir = APPLICATION_PATH . '/../assets/files/usage_guides/';

        header('Content-Type: image/' . $ext[sizeof($ext) - 1]);
        header('Content-Disposition: attachment; filename="' . $file . '"');
        readfile($targetDir . $file);
        $this->_helper->BUtilities->setNoLayout();
    }

    /**
     * Function to download artwork file
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function downloadArtworkAction() {
        $file = $this->_getParam('file');
        $ext = explode('.', $file);
        $targetDir = APPLICATION_PATH . '/../assets/files/artworks/';
       
        $path = $targetDir . 'originals/' . $file;
        
        if (!file_exists($path)){
        	$path = $targetDir . $file;
        }
        
        header('Content-Type: image/' . $ext[sizeof($ext) - 1]);
        header('Content-Disposition: attachment; filename="' . $file . '"');
        readfile($path);
        $this->_helper->BUtilities->setNoLayout();
    }

    /**
     * Function to save product title by ajax
     * @author Rashed
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxSaveArtworkTitleAction() {
        $this->_helper->BUtilities->setNoLayout();
        if ($this->_request->isXmlHttpRequest()) {
            $formData = $this->getRequest()->getPost();
            $title = $formData['title'];
            $user = $this->em->getRepository("BL\Entity\User")->findBy(array('id' => $formData['user_id']));

            $artwork = $this->em->getRepository("BL\Entity\ClientArtwork")->findOneBy(array('id' => $formData['id'], 'User' => $user));
            //print_r($formData);
            if (sizeof($artwork)) {
                $artwork->title = $title;
                $this->em->persist($artwork);
                $this->em->flush();
                $this->em->clear();
                echo Zend_Json_Encoder::encode(array('success' => true, 'message' => 'Successfully updated', 'title' => $artwork->title));
            } else {
                echo Zend_Json_Encoder::encode(array('success' => true, 'message' => 'no update'));
            }
        }
    }

    /**
     * Function to add artwork
     * @author Sukhon
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function addArtworkAction() {
        $this->getClient();
        $this->view->client = $this->client;
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_fancy_assets', 'load_plupload_assets'));
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $uploaded_pic = $formData['pics'];
            $titles = $formData['title'];
            //print_r($formData);
            if ($uploaded_pic) {
                foreach ($uploaded_pic as $key => $pic) {
                    $class = 'BL\Entity\ClientArtwork';
                    $artwork = new $class();
                    $artwork->title = $titles[$key];
                    $artwork->file_url = $pic;
		    $parts = explode('.',$pic);
                    $ext = end($parts);
                    $artwork->file_extension = $ext;
                    $artwork->User = $this->client;
                    $artwork->upload_date = new DateTime();
                    $this->em->persist($artwork);
                }
                $this->em->flush();
                $this->em->clear();
                $this->view->message = array('success' => true, 'message' => 'Successfully uploaded');
            } else {
                $this->view->message = array('success' => false, 'message' => 'There is no file uploaded');
            }
            $this->_helper->flashMessenger($this->view->message['message'], "Info");
            $this->_redirect('/admin/clients/artwork/id/' . $this->client->id);
        }
    }

    /**
     * Function to add usage guide
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function addUsageGuidesAction() {
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_fancy_assets'));
        $this->_helper->JSLibs->load_tinymce_assets();
        $this->getClient();
        $this->view->client = $this->client;
        $form = new Admin_Form_UsageGuide();
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $clientUsageGuide = new \BL\Entity\ClientUsageGuide();
                $clientUsageGuide->guide_name = $form->getValue('guide_name');
                $clientUsageGuide->guide_content = $form->getValue('guide_content');
                $clientUsageGuide->user_id = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id' => (int) $this->_getParam('id')));

                $destination_dir = APPLICATION_PATH . '/../assets/files/usage_guides/';
                try {
                    $adapter = new Zend_File_Transfer_Adapter_Http();
                    $adapter->setDestination($destination_dir);
                    $files = $adapter->getFileInfo();
//                    print_r($files);
                    if (($adapter->isUploaded($files['guide_document']['name'])) && ($adapter->isValid($files['guide_document']['name']))) {
                        $extension = substr($files['guide_document']['name'], strrpos($files['guide_document']['name'], '.') + 1);
                        $filename = 'file_' . $this->_helper->BUtilities->getLoggedInUser() . '_' . date('Ymdhs') . '.' . $extension;
                        $adapter->addFilter('Rename', array('target' => $destination_dir . $filename, 'overwrite' => true));
                        $adapter->receive($files['guide_document']['name']);
                        $clientUsageGuide->guide_url = $filename;
                        $clientUsageGuide->guide_type = $extension;
                    }
                    $this->em->persist($clientUsageGuide);
                    $this->em->flush();
                    $this->em->clear();
                    $this->view->msg = "Usage guide added successfuly!";
                    $this->_helper->flashMessenger($this->view->msg, "Info");
                    $this->_redirect('/admin/clients/artwork/id/' . $this->_getParam('id'));
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
     * Function to save product Sample files
     * @author Sukhon
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxSaveArtworkAction() {
        $this->_helper->BUtilities->setNoLayout();
        if ($this->_request->isXmlHttpRequest()) {
            $formData = $this->getRequest()->getPost();
            $title = $formData['title'];
            $artwork = $this->em->getRepository("BL\Entity\ClientArtwork")->findOneBy(array('id' => $formData['id'], 'User' => $this->client->id));
            //print_r($formData);
            if (sizeof($artwork)) {
                $artwork->title = $title;
                $this->em->persist($artwork);
                $this->em->flush();
                $this->em->clear();
                echo Zend_Json_Encoder::encode(array('success' => true, 'message' => 'Successfully updated', 'title' => $artwork->title));
            } else {
                echo Zend_Json_Encoder::encode(array('success' => true, 'message' => 'no update'));
            }
        }
    }

    /**
     * Function to delete product Sample files
     * @author Sukhon
     * @copyright Blueliner Marketing
     */
    public function ajaxDelArtworkAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $artwork = $this->em->getRepository("BL\Entity\ClientArtwork")->findOneBy(array('id' => $this->_getParam('id', '')));
            if (sizeof($artwork)) {
                $targetDir = APPLICATION_PATH . '/../assets/files/artworks/';
		if(file_exists($targetDir . $artwork->file_url))
                        @unlink($targetDir . $artwork->file_url);
                if(file_exists($targetDir . 'thumbs/_thumb' . $artwork->file_url))
                        @unlink($targetDir . 'thumbs/_thumb' . $artwork->file_url);
                $this->em->remove($artwork);
                $this->em->flush();
                $this->em->clear();
                echo Zend_Json_Encoder::encode(array('success' => true, 'message' => 'Successfully deleted'));
            } else {
                echo Zend_Json_Encoder::encode(array('success' => false, 'message' => 'Invalid Artwork'));
            }
        }
        $this->_helper->BUtilities->setNoLayout();
    }

    /**
     * Function to Upload multiple artwork files
     * @author Sukhon
     * @copyright Blueliner Marketing
     */
    public function uploadFilesAction() {
        $this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_plupload_assets();
        $this->_helper->BUtilities->setBlankLayout();
    }

    /**
     * Function to upload Sample files
     * @author Sukhon
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function doUploadAction() {

        /**
         * Todo
         * 4. Add to DB
         */
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        $targetDir = APPLICATION_PATH . '/../assets/files/artworks/';

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
                    $thumb_save_path = $targetDir . DIRECTORY_SEPARATOR . "thumbs" . DIRECTORY_SEPARATOR . base64_encode("_thumb") . $fileName;
                    $thumb = PhpThumbFactory::create($targetDir . DIRECTORY_SEPARATOR . $fileName);
                    $thumb->resize(100, 100)->padding(110, 75, '#FFFFFF');
                    $thumb->save($thumb_save_path);

                    $thumb2_save_path = $targetDir . DIRECTORY_SEPARATOR . "large" . DIRECTORY_SEPARATOR . $fileName;
                    $thumb = PhpThumbFactory::create($targetDir . DIRECTORY_SEPARATOR . $fileName);
                    $thumb->resize(450, 450)->padding(450, 300, '#FFFFFF');
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

    public function webProfileAction() {
        $this->getClient();
        $this->_helper->JSLibs->load_jqui_aristo();
        $this->_helper->JSLibs->load_fancy_assets();
        $this->view->client = $this->client;

        $client = $this->client;
        $clientProfile = $this->em->getRepository('BL\Entity\ClientProfile')->findOneBy(array('user_id' => (int) $this->client->id));

        $options = array();
        $org_type[''] = 'Select Type';
        $org_list = $this->em->getRepository("BL\Entity\OrganizationType")->findAll();
        foreach ($org_list as $org) {
            $org_type[$org['id']] = $org['name'];
        }
        $options['org_type'] = $org_type;
        $options['greek_org_type'] = !is_null($clientProfile->greek_org_type) ? $clientProfile->greek_org_type : '0';
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
            $this->view->client_logo = $clientProfile->client_logo;
            $this->view->symbol = $clientProfile->symbol;
            $existing_data['greek_name'] = $clientProfile->greek_name;
            $existing_data['greek_org_type'] = $clientProfile->greek_org_type->id;
            $existing_data['greek_founding_year'] = strlen($clientProfile->greek_founding_year->format('m/d/Y')) == 10 ?
                    $clientProfile->greek_founding_year->format('m/d/Y') : '';
            $existing_data['greek_number_of_alumni'] = $clientProfile->greek_number_of_alumni;
            $existing_data['greek_number_of_undergrads'] = $clientProfile->greek_number_of_undergrads;
            $existing_data['greek_number_of_alumni_chapters'] = $clientProfile->greek_number_of_alumni_chapters;
            $existing_data['greek_number_of_colg_chapters'] = $clientProfile->greek_number_of_colg_chapters;
            $existing_data['profile_status_update_time'] = $clientProfile->profile_status_update_time;
            $existing_data['headquarters_city'] = $clientProfile->headquarters_city;
            $existing_data['headquarters_state'] = $clientProfile->headquarters_state;
	    $existing_data['foundingYear'] = $clientProfile->org_founding_year;
        } else {
            $clientProfile = new \BL\Entity\ClientProfile();
        }

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            //Zend_Debug::dump($formData);
            if ($form->isValid($formData)) {



                //Zend_Debug::dump($uploaded_files_array);die('@@@@@@@');
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
                $clientProfile->greek_org_type = $this->em->find('BL\Entity\OrganizationType', $form->getValue('greek_org_type'));
                $clientProfile->greek_founding_year = new DateTime($form->getValue('greek_founding_year'));
                $clientProfile->greek_number_of_alumni = $form->getValue('greek_number_of_alumni');
                $clientProfile->greek_number_of_undergrads = $form->getValue('greek_number_of_undergrads');
                $clientProfile->greek_number_of_alumni_chapters = $form->getValue('greek_number_of_alumni_chapters');
                $clientProfile->greek_number_of_colg_chapters = $form->getValue('greek_number_of_colg_chapters');
                $clientProfile->profile_status_update_time = new DateTime();
                $clientProfile->headquarters_city = $form->getValue('headquarters_city');
                $clientProfile->headquarters_state = $form->getValue('headquarters_state');
                $clientProfile->user_id = $client;
		$clientProfile->org_founding_year = $form->getValue('foundingYear');
                $this->em->persist($clientProfile);
                $this->em->flush();

                $this->_helper->flashMessenger("Client Web Profile updated succesfully!", "Info");
                $this->_redirect($this->view->BUrl()->absoluteUrl());
            } else {
                $form->populate($formData);
            }
        } else {
            $form->populate($existing_data);
        }
    }

    /**
     * Function to change clients password
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function changePasswordAction() {
        $this->getClient();
        $this->view->client = $this->client;
        $client_model = new Admin_Model_Clients($this);
        $client_model->changePassword();
    }

    /**
     * Function to get search
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function searchAction() {
        // action body
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets', 'load_fancy_assets', 'load_jquery_multiselect_assets'));
        $this->view->status_array = $this->_helper->BUtilities->parseYAML(APPLICATION_PATH . '/configs/user_status.yml');
    }

    /**
     * Function to get client license agreements
     * @author Zea
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function liscAgreementsAction() {
        $this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_dataTable_assets();
        $this->view->status_array = $this->_helper->BUtilities->parseYAML(APPLICATION_PATH . '/configs/statuses.yml');
        // action body
    }

    /**
     * Function to get licenses json data for data table view
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetLicensesDtAction() {
        $this->_helper->BUtilities->setNoLayout();
        $client_model = new Admin_Model_Clients($this);
        echo $client_model->getLicenses();
    }

    /**
     * Function to get licensed clients json data for data table view
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetLicensedClientsDtAction() {
        $this->_helper->BUtilities->setNoLayout();
        $client_model = new Admin_Model_Clients($this);
        echo $client_model->getLicensedClients();
    }

    /*
     *
     */
    public function paymentReportsAction() {
        $this->view->clients = $this->em->getRepository("BL\Entity\User")->findBy(array('account_type' => ACC_TYPE_CLIENT), array('organization_name' => 'asc'));
        $this->view->fiscalYear = $this->em->getRepository("BL\Entity\Payment")->getPaymentYear();
        $year = $this->_getParam('year');

        if($year)
        {
            if($year == BL_AMC::getCurrentFiscalYear())
            {
                $this->view->currentQuarter = BL_AMC::getCurrentQarter();
            }
            else
            {
                $this->view->currentQuarter = 5;
            }


            //$this->view->payment = $this->em->getRepository("BL\Entity\PaymentLineItems")->getPaymentReportByClient($year);
            //$this->view->quarter = $this->em->getRepository("BL\Entity\PaymentLineItems")->getPaymentReportByClient($year);

            //$this->view->payment = $this->em->getRepository("BL\Entity\Payment")->findBy(array('payment_year' => $year));
            //$this->view->payment2 = $this->em->getRepository("BL\Entity\PaymentLineItems")->findBy(array('payment_year' => $year, 'payment_quarter'=> 2));
            //$this->view->payment3 = $this->em->getRepository("BL\Entity\PaymentLineItems")->findBy(array('payment_year' => $year, 'payment_quarter'=> 3));
            //$this->view->payment4 = $this->em->getRepository("BL\Entity\PaymentLineItems")->findBy(array('payment_year' => $year, 'payment_quarter'=> 4));
        }
        $this->view->year=$year;
    }
    public function reportDetailAction() {
         $this->view->currency = new Zend_Currency('en_US');
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets'));
         $clientId = $this->_getParam('clientId');
         $quarter = $this->_getParam('quarter');
         $year = $this->_getParam('year');
         $this->view->year=$year;
         $this->view->clientId=$clientId;
         $this->view->quarter=$quarter;
         $this->view->itemsRepository = $this->em->getRepository("BL\Entity\InvoiceLineItems");
         $this->view->paymentRepository = $this->em->getRepository("BL\Entity\Payment");
         $this->view->reportDetail = $this->em->getRepository("BL\Entity\PaymentLineItems")->findBy(array('client' => $clientId, 'payment_quarter'=> $quarter, 'payment_year' => $year));
         $this->view->clientDetail = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' => $clientId));
         $existingClientReport = $this->em->getRepository("BL\Entity\ClientReport")->findOneBy(array('client_id' =>  (int) $clientId, 'fiscal_year' => $year, 'quarter' =>  (int) $quarter));
         if($existingClientReport)
         {
            $this->view->lastPostDate = $existingClientReport->mdate;
         }
    }
    public function affinityReportDetailAction() {
         $this->view->currency = new Zend_Currency('en_US');
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets'));
         $clientId = $this->_getParam('clientId');
         $quarter = $this->_getParam('quarter');
         $year = $this->_getParam('year');
         $this->view->year=$year;
         $this->view->clientId=$clientId;
         $this->view->quarter=$quarter;
         $this->view->itemsRepository = $this->em->getRepository("BL\Entity\InvoiceLineItems");
         $this->view->paymentRepository = $this->em->getRepository("BL\Entity\Payment");
         $this->view->reportDetail = $this->em->getRepository("BL\Entity\PaymentLineItems")->findBy(array('client' => $clientId, 'payment_quarter'=> $quarter, 'payment_year' => $year));
         $this->view->clientDetail = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' => $clientId));

    }
    //Added by Jace 9-17
    public function quarterlyReportDetailAction() 
    {
         $this->view->currency = new Zend_Currency('en_US');
         $clientId = $this->_getParam('clientId');
         $quarter = $this->_getParam('quarter');
         $year = $this->_getParam('year');
         $this->view->year=$year;
         $this->view->clientId=$clientId;
         $this->view->quarter=$quarter;
         $this->view->reportDetail = $this->em->getRepository("BL\Entity\PaymentLineItems")->findBy(array('client' => $clientId, 'payment_year' => $year));
         $this->view->clientDetail = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' => $clientId));
         $existingClientReport = $this->em->getRepository("BL\Entity\ClientReport")->findOneBy(array('client_id' =>  (int) $clientId, 'fiscal_year' => $year));
         if($existingClientReport)
         {
            $this->view->lastPostDate = $existingClientReport->mdate;
         }
    }

    public function affinityQuarterlyReportDetailAction()
    {
         $this->view->currency = new Zend_Currency('en_US');
         $clientId = $this->_getParam('clientId');
         $quarter = $this->_getParam('quarter');
         $year = $this->_getParam('year');
         $this->view->year=$year;
         $this->view->clientId=$clientId;
         $this->view->quarter=$quarter;
         $this->view->reportDetail = $this->em->getRepository("BL\Entity\PaymentLineItems")->findBy(array('client' => $clientId, 'payment_year' => $year));
         $this->view->clientDetail = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' => $clientId));
         $existingClientReport = $this->em->getRepository("BL\Entity\ClientReport")->findOneBy(array('client_id' =>  (int) $clientId, 'fiscal_year' => $year));
         if($existingClientReport)
         {
            $this->view->lastPostDate = $existingClientReport->mdate;
         }
    }
    public function exportPdfAction() {
         $this->view->currency = new Zend_Currency('en_US');
         $clientId = $this->_getParam('clientId');
         $quarter = $this->_getParam('quarter');
	 $this->view->quarter = $quarter;
         $year = $this->_getParam('year');
	 $this->view->year = $year;
         $postToClient = $this->_getParam('postToClient');
	 	$isAdminReport = $this->_getParam('aReport');
	 	$this->view->isAdminReport = ($isAdminReport == 't');
         
         $reportDetail = $this->em->getRepository("BL\Entity\PaymentLineItems")->findBy(array('client' => $clientId, 'payment_quarter'=> $quarter, 'payment_year' => $year));

		 usort ($reportDetail, function($a, $b){
         	$ret = strcasecmp($a->pmt_id->vendor->organization_name, $b->pmt_id->vendor->organization_name);
         	
         	if ($ret == 0){
         		if ($a->pmt_id->record_date > $b->pmt_id->record_date) $ret = 1;
         		if ($b->pmt_id->record_date > $a->pmt_id->record_date) $ret = -1;
         	}
         	
         	return $ret;
		 });
         
         $this->view->reportDetail = $reportDetail;
         $this->view->clientDetail = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' => $clientId));
         $organizationName=$this->view->clientDetail->organization_name;
         $this->view->invoiceItemsRepository = $this->em->getRepository("BL\Entity\InvoiceLineItems");
         $html = $this->view->render('clients/export-pdf.phtml');
		//                print_r($html);
		//                die('---------');
		$pdf_params = array(
		    'author' => '',
		    'title' => 'Export PDF',
		    'subject' => 'report',
		    'pdf_content' => $html,
		    'file_name' => str_replace(",", "", $organizationName) .'-'.$year.'-'.$quarter,
		    'file_path' => APPLICATION_PATH . '/../tmp/',
		    'output_type' => 'F'
		);
		$save_to = $this->view->BUtils()->getPDF($pdf_params);
                if($postToClient=='')
                {
                    if($save_to)
                    {
                       $filename = $save_to;
                        header("Pragma: public");
                        header("Expires: 0");
                        header("Pragma: no-cache");
                        header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
                        header("Content-Type: application/force-download");
                        header("Content-Type: application/octet-stream");
                        header("Content-Type: application/download");
                        header('Content-disposition: attachment; filename=' . basename($filename));
                        header("Content-Type: application/pdf");
                        header("Content-Transfer-Encoding: binary");
                        header('Content-Length: ' . filesize($filename));
                        @readfile($filename);
                        exit(0);
                    }
                }
                if($postToClient)
                {
                    $existingClientReport = $this->em->getRepository("BL\Entity\ClientReport")->findOneBy(array('client_id' =>  (int) $clientId, 'fiscal_year' => $year, 'quarter' =>  (int) $quarter));
                    if (!sizeof($existingClientReport)) {
                         $clientReport = new \BL\Entity\ClientReport();
                    } else {
                        $clientReport = $existingClientReport;
                    }

                    $clientReport->client_id = $clientId;
                    $clientReport->report = $organizationName.'-'.$year.'-'.$quarter;
                    $clientReport->cdate = $existingClientReport ? $existingClientReport->cdate : new \DateTime(date("Y-m-d H:i:s"));
                    $clientReport->mdate = new \DateTime(date("Y-m-d H:i:s"));
                    $clientReport->fiscal_year = $year;
                    $clientReport->quarter = $quarter;
                    $this->em->persist($clientReport);
                    $this->em->flush();
                }
                $this->_redirect('/admin/clients/report-detail/year/'.$year.'/clientId/'.$clientId.'/quarter/'.$quarter);

    }
    public function exportCsvAction() {
        $currency = new Zend_Currency('en_US');
        $clientId = $this->_getParam('clientId');
        $quarter = $this->_getParam('quarter');
        $year = $this->_getParam('year');
        $this->view->reportDetail = $this->em->getRepository("BL\Entity\PaymentLineItems")->findBy(array('client' => $clientId, 'payment_quarter' => $quarter, 'payment_year' => $year));
        $this->view->clientDetail = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' => $clientId));
        $organizationName = $this->view->clientDetail->organization_name;
        $reportDetail=$this->view->reportDetail;
        $i=0;
        $reportDetailArray[$i][] = "Company Name";
        $reportDetailArray[$i][] = "Date";
        $reportDetailArray[$i][] = "Pmt Type";
        $reportDetailArray[$i][] = "Ref#";
        $reportDetailArray[$i][] = "Total Amount";
        $reportDetailArray[$i][] = "Affinity";
        $reportDetailArray[$i][] = $this->view->clientDetail?$this->view->clientDetail->organization_name:'';
        $reportDetailArray[$i][] = "Sharing";

        $i=1;
        $totalClientAmount=0;
        foreach ($reportDetail as $item) {
            $amcAmount = 0;
            $clientAmount=0;
            if (($item->amount_paid) && $item->pmt_id->invoice->invoice_type != 'Refund') {
                $totalAmount = $item->amount_paid;
                if ($item->sharing == 1) {
                    $amcAmount = ($item->amount_paid * $item->percent_amc);
                    }
                    $clientAmount = $item->amount_paid - $amcAmount;
            } elseif (($item->late_paid) && $item->pmt_id->invoice->invoice_type != 'Refund') {
                $totalAmount = $item->late_paid;
                if ($item->sharing == 1) {
                    $amcAmount = ($item->late_paid * $item->percent_amc);
                    }
                    $clientAmount = $item->late_paid - $amcAmount;
            } elseif (($item->adv_paid) && $item->pmt_id->invoice->invoice_type != 'Refund') {
                $totalAmount = $item->adv_paid;
                if ($item->sharing == 1) {
                    $amcAmount = ($item->adv_paid * $item->percent_amc);
                    }
                    $clientAmount = $item->adv_paid - $amcAmount;
            } else {
                $$amcAmount = 0;
                $clientAmount = 0;
                $totalAmount=0;
            }
            $reportDetailArray[$i][] = $item->pmt_id->vendor->organization_name;
            $reportDetailArray[$i][] = $item->last_modified_date->format("m/d/Y");
            $reportDetailArray[$i][] = $item->pmt_id->invoice ? str_replace('Received','',$item->pmt_id->invoice->payment_status) : "N/A";
            $reportDetailArray[$i][] = $item->pmt_id->check_num;
            $reportDetailArray[$i][] = $currency->toCurrency($totalAmount);
            $reportDetailArray[$i][] = $currency->toCurrency($amcAmount);
            $reportDetailArray[$i][] = $currency->toCurrency($clientAmount);
            $reportDetailArray[$i][] = $item->sharing ? 'Yes' : 'No';
            $totalClientAmount = $totalClientAmount + $clientAmount;
            $i++;
        }
        $reportDetailArray[$i][] ="";
        $reportDetailArray[$i][] ="";
        $reportDetailArray[$i][] ="";
        $reportDetailArray[$i][] ="";
        $reportDetailArray[$i][] ="";
        $reportDetailArray[$i][] ="Total Amount for Quarter:";
        $reportDetailArray[$i][] =$currency->toCurrency($totalClientAmount);
        $reportDetailArray[$i][] ="";

        ob_start();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=report.csv');
	$filename = APPLICATION_PATH.'/../tmp/report.csv';
        $fp = fopen($filename, "w");
        foreach ($reportDetailArray as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
        ob_end_flush();
        readfile($filename);
        exit(0);

    }
    /**
     * Function to get client payment reports by year and quarter
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetClientPaymentReportsAction() {
        $this->_helper->BUtilities->setAjaxLayout();
        $this->getClient();
        $this->view->client=$this->client;
        $this->view->data=$this->em->getRepository("BL\Entity\Payment")->getClientPaymentReports($this->_getAllParams());
    }

    /**
     * Function to add master template
     * @author Sukhon
     * @version 0.1
     */
    public function masterTemplatesAction() {
		error_log("\nmasterTemplatesAction()", 3, "./errorLog.log");
    	
    	$this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_tinymce_assets();
        $this->_helper->JSLibs->load_fancy_assets();
        $form = new Admin_Form_LicenseTemplate();

        $this->view->noinsurance = $this->_getParam('noinsurance', NULL);
        if ($this->getRequest()->isPost()) {
            //if ($this->_request->isXmlHttpRequest()) {
            $template_content = $this->_getParam('template', NULL);
            if ($template_content) {
                $class = 'BL\Entity\MasterTemplate';
                $template = new $class();
                $template->template = $template_content;
                $template->user_id = $this->em->find("BL\Entity\User", (int) $this->_helper->BUtilities->getLoggedInUser());
                $template->has_insurance = $this->view->noinsurance ? NULL : 1;
                $this->em->persist($template);
                $this->em->flush();
                $this->em->clear();

                $this->view->message = array('success' => true, 'message' => 'Successfully added');
            } else {
                $this->view->message = array('success' => false, 'message' => 'Please enter license template');
            }
        }
        $this->view->noinsurance ? ($condition['has_insurance'] = NULL) : ($condition['has_insurance'] = 1);
        $template = $this->em->getRepository("BL\Entity\MasterTemplate")->findBy($condition, array('id' => 'DESC'), 1);

        /**
         * "lop logo" relative path to absolute path conversion
         */
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $path = $request->getScheme() . '://' . $request->getHttpHost() . Zend_Controller_Front::getInstance()->getBaseUrl();
        $text = "../../../../assets";
        $text3 = "../../assets";
        $text2 = "http://www.greeklicensing.com/crm/assets";
        $master_template = @$template[0]->template;
        
        error_log("\nmaster template:\n" . $master_template, 3, "./errorLog.log");
        
        error_log("\nswapping portions",3 , "./errorLog.log");
        
        $master_template = str_replace($text, $path . '/assets', $master_template);
        $master_template = str_replace($text3, $path . '/assets', $master_template);
        $master_template = str_replace($text2, $path . '/assets', $master_template);

        $form->template->setValue($master_template);
        $this->view->form = $form;
        //$this->view->template = @$template[0]->template;
    }

    /**
     * Function to addendums list
     * @author Sukhon
     * @version 0.1
     */
    public function addendumsAction() {
        $this->view->addendums = $this->em->getRepository("BL\Entity\Addendum")->getAddendums(array('page' => $this->_getParam('page', 1), 'per_page' => 5, 'num_of_link' => 8));
    }

    /**
     * Function to edit addendum
     * @author Sukhon
     * @version 0.1
     */
    public function editAddendumAction() {
        $this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_tinymce_assets();
        $form = new Admin_Form_Addendum();

        $addendum = $this->em->find("BL\Entity\Addendum", (int) $this->_getParam('id'));
//        $this->view->BUtils()->doctrine_dump($addendum);
//        die();
        $this->view->addendum = $addendum;
        $this->view->clients = $this->em->getRepository("BL\Entity\User")->getClientNames();

        $new_formdata['reason'] = $this->view->addendum->reason;
        $new_formdata['content'] = $this->view->addendum->content;
        $form->populate($new_formdata);
        $this->view->form = $form;

        $addendumUser = $this->em->getRepository("BL\Entity\AddendumUser")->findBy(array('addendum_id' => $addendum->id));
        $addendumUsers = array();
        if ($addendumUser) {
            foreach ($addendumUser as $au) {
                $addendumUsers[] = $au->user_id->id;
            }
        }
        $this->view->addendumUsers = $addendumUsers;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $addendum->content = $formData['content'];
                $addendum->reason = $formData['reason'];
                $addendum->is_draft = $formData['is_draft'];
//                $this->view->BUtils()->doctrine_dump($addendum);
//                die();
                $this->em->persist($addendum);
                $this->em->flush();
                if (!$addendum->is_draft && sizeof($formData['greek_org'])) {
                    $new_clients = array_diff($formData['greek_org'], $addendumUsers);
                    $remove_clients = array_diff($addendumUsers, $formData['greek_org']);
                    //remove cleints
                    foreach ($remove_clients as $client_id) {
                        $this->em->remove($this->em->getRepository("BL\Entity\AddendumUser")->findOneBy(array('addendum_id' => $addendum->id, 'user_id' => $client_id)));
                    }
                    //add clients
                    foreach ($new_clients as $client_id) {
                        $client = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' => $client_id));
                        $addendumUser = new \BL\Entity\AddendumUser();
                        $addendumUser->user_id = $client;
                        $addendumUser->addendum_id = $addendum;
                        $this->em->persist($addendumUser);

                        $emails = preg_split('/[;,]/', $client->email);
                        $email_body = "Hi, <br /><br />Please read the addendum and sign it to activate your license. <br /><br />Thank you<br />AMC admin";
                        $params = array(
                            'to' => $emails[0],
                            'to_name' => $client->organization_name,
                            'from' => 'admin@greeklicensing.com',
                            'from_name' => 'AMC Admin',
                            'subject' => 'New addendum to be signed',
                            'body' => $email_body
                        );
                        $this->_helper->BUtilities->send_mail($params);
                    }
                }
                $this->em->flush();
                $this->em->clear();
                $this->_helper->flashMessenger('Successfully updated');
                $this->_redirect('/admin/clients/addendums');
            } else {
                $form->populate($formData);
            }
        }
    }

    /**
     * Function to view addendum
     * @author Sukhon
     * @version 0.1
     */
    public function viewAddendumAction() {
        $this->view->addendum = $this->em->find("BL\Entity\Addendum", (int) $this->_getParam('id'));
        $addendumUser = $this->em->getRepository("BL\Entity\AddendumUser")->findBy(array('addendum_id' => $this->_getParam('id')));
        //$this->view->BUtils()->doctrine_dump($this->view->addendumUser);
        $addendumUsers = array();
        if ($addendumUser) {
            foreach ($addendumUser as $c) {
                $addendumUsers[] = $c->user_id->id;
            }
        }
//        print_r($addendumUsers);
//        die();
//
        $this->view->clients = $this->em->getRepository("BL\Entity\User")->getClientNames();
        $this->view->form = new Admin_Form_Addendum();
        $this->view->addendumUsers = $addendumUsers;
    }

    /**
     * Function to add addendums
     * @author Sukhon
     * @version 0.1
     */
    public function addAddendumAction() {
        $this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_tinymce_assets();
        $form = new Admin_Form_Addendum();
        $this->view->clients = $this->em->getRepository("BL\Entity\User")->getClientNames();
        //$this->view->BUtils()->doctrine_dump($this->view->clients,1);
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $addendum = new \BL\Entity\Addendum();
                $addendum->content = $formData['content'];
                $addendum->reason = $formData['reason'];
                $addendum->is_draft = $formData['is_draft'];
                $this->em->persist($addendum);
                $this->em->flush();

                if (!$addendum->is_draft && sizeof($formData['greek_org'])) {
                    foreach ($formData['greek_org'] as $client_id) {
                        $client = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' => $client_id));
                        $addendumUser = new \BL\Entity\AddendumUser();
                        $addendumUser->user_id = $client;
                        $addendumUser->addendum_id = $addendum;
                        $this->em->persist($addendumUser);

                        $emails = preg_split('/[;,]/', $client->email);
                        $email_body = "Hi, <br /><br />Please read the addendum and sign it to activate your license. <br /><br />Thank you<br />AMC admin";
                        $params = array(
                            'to' => $emails[0],
                            'to_name' => $client->organization_name,
                            'from' => 'admin@greeklicensing.com',
                            'from_name' => 'AMC Admin',
                            'subject' => 'New addendum to be signed',
                            'body' => $email_body
                        );
                        $this->_helper->BUtilities->send_mail($params);
                    }
                }
                $this->em->flush();
                $this->em->clear();
                $this->_helper->flashMessenger('Addendum added successfully!');
                $this->_redirect('/admin/clients/addendums');
            } else {
                $form->populate($formData);
            }
        }
    }

    /**
     * Function to add client
     * @author Rashed
     * @version 0.1
     */
    public function addAction() {
        $this->_helper->JSLibs->load_jqui_aristo();
//        $states = array('' => 'Select');
//        $states_list = $this->em->getRepository("BL\Entity\State")->findAll();
//        foreach ($states_list as $state) {
//            $states[$state['abbrev']] = $state['name'];
//        }

        $options = array();
//        $options['states'] = $states;
        $options['org_type'] = $this->getOrganizationList();
        $options['greek_org_type'] = '0';

        $form = new Admin_Form_AddClient($options);
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $user_first_name = $user_last_name = '';
                $username = explode(' ', trim($form->getValue('name')));
                if (count($username) > 1) {
                    $user_first_name = array_shift($username);
                    $user_last_name = implode(' ', $username);
                }

                //for add client
                $user = new \BL\Entity\User();
                $user->account_type = ACC_TYPE_CLIENT;
                $user->first_name = ($user_first_name != '') ? $user_first_name : $form->getValue('name');
                $user->last_name = ($user_last_name != '') ? $user_last_name : '';
                $user->organization_name = $form->getValue('name');
                $user->address_line1 = $form->getValue('address1');
                $user->address_line2 = $form->getValue('address2');
                $user->city = $form->getValue('city');
                $user->state = $form->getValue('state');
                $user->zipcode = $form->getValue('zip');
                $user->email = $form->getValue('email');
                $user->phone = $form->getValue('phone');
                $user->fax = $form->getValue('fax');
                $user->website = $form->getValue('web_address');
                $user->username = $form->getValue('username');
                $user->password = md5($form->getValue('password'));
                $user->user_status = "Current";
                $user->reg_status = "activated";
                $user->user_code = $form->getValue('user_code');
                $role = $this->em->getRepository('BL\Entity\Role')->findOneBy(array('id' => ACC_TYPE_CLIENT));
                $user->roles->add($role);
                $this->em->persist($user);
                $this->em->flush();

                //for save client contact
                $userContact = new \BL\Entity\UserContact();
                $userContact->user_id = $user;
                $userContact->first_name = $user->first_name;
                $userContact->last_name = $user->last_name;
                $userContact->address_line1 = $user->address_line1;
                $userContact->city = $user->city;
                $userContact->state = $user->state;
                $userContact->zipcode = $user->zipcode;
                $userContact->phone = $user->phone;
                $userContact->fax = $user->fax;
                $this->em->persist($userContact);
                $this->em->flush();

                //for save cleint profile
                $clientProfile = new \BL\Entity\ClientProfile();
                $clientProfile->user_id = $user;
                $clientProfile->greek_name = $form->getValue('greek_letters');
                $clientProfile->greek_royalty_description = $form->getValue('description');
                $clientProfile->greek_approved_contact_person = $form->getValue('contact_person');
                $clientProfile->greek_default_renewal_fee = $form->getValue('renewal_fee');
                $clientProfile->greek_default_late_fee = $form->getValue('late_fee');
                //$clientProfile->greek_org_type = $form->getValue('organisation_type');
                $clientProfile->greek_org_type = $this->em->find('BL\Entity\OrganizationType', $form->getValue('organisation_type'));
                //$clientProfile->greek_founding_year = $form->getValue('founding_year');
                $clientProfile->greek_founding_year = new DateTime(date('Y-m-d', strtotime($form->getValue('founding_year'))));
                $clientProfile->greek_number_of_alumni = (int) $form->getValue('number_of_alumni');
                $clientProfile->greek_number_of_undergrads = (int) $form->getValue('number_of_undergrads');
                $clientProfile->greek_number_of_alumni_chapters = (int) $form->getValue('number_of_alumni_chapters');
                $clientProfile->greek_total_ug_chapters = (int) $form->getValue('total_ug_chapters');
                $clientProfile->greek_grant_of_license = $form->getValue('grant_on_license');
                $this->em->persist($clientProfile);
                $this->em->flush();
                $this->view->success = "New Client Created succesfully!";
                $this->_helper->flashMessenger("New client created succesfully!", "Info");
                //$this->_redirect($this->view->BUrl()->absoluteUrl());
                $this->_redirect('admin/clients/add-legal/id/' . $user->id);
            } else {
                $form->populate($formData);
            }
        }
    }

    /**
     * Function to add client
     * @author Rashed
     * @version 0.1
     */
    public function addLegalAction() {
        $this->getClient();
        $this->view->client = $this->client;

        $form = new Admin_Form_ClientLegal();
        $this->view->form = $form;
        $this->view->client = $this->client;
        $clientLegal = $this->em->getRepository('BL\Entity\ClientLegal')->findOneBy(array('user_id' => (int) $this->client->id));
        if (sizeof($clientLegal)) {
            $existing_data = array(
                'org_choice_law' => $clientLegal->choice_of_law,
                'licensor_title' => $clientLegal->licensor_title,
                'legal_name' => $clientLegal->legal_name,
                'legal_firm' => $clientLegal->legal_firm,
                'legal_address1' => $clientLegal->legal_address_line1,
                'legal_address2' => $clientLegal->legal_address_line2,
                'legal_city' => $clientLegal->legal_city,
                'legal_state' => $clientLegal->legal_state,
                'legal_zip' => $clientLegal->legal_zipcode,
                'legal_phone' => $clientLegal->legal_phone
            );
            $form->populate($existing_data);
        } else {
            $clientLegal = new \BL\Entity\ClientLegal();
        }

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $user = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id' => $this->client));

//                $clientLegal = new \BL\Entity\ClientLegal();
                $clientLegal->user_id = $user;
                $clientLegal->choice_of_law = $form->getValue('org_choice_law');
                $clientLegal->licensor_title = $form->getValue('licensor_title');
                $clientLegal->legal_name = $form->getValue('legal_name');
                $clientLegal->legal_firm = $form->getValue('legal_firm');
                $clientLegal->legal_address_line1 = $form->getValue('legal_address1');
                $clientLegal->legal_address_line2 = $form->getValue('legal_address2');
                $clientLegal->legal_city = $form->getValue('legal_city');
                $clientLegal->legal_state = $form->getValue('legal_state');
                $clientLegal->legal_zipcode = $form->getValue('legal_zip');
                $clientLegal->legal_phone = $form->getValue('legal_phone');
                $this->em->persist($clientLegal);
                $this->em->flush();

                $this->_helper->flashMessenger("Client legal information saved succesfully!", "Info");
                $this->_redirect('admin/clients/add-operation/id/' . $user->id);
            } else {
                $form->populate($formData);
            }
        }
    }

    /**
     * Function to add client operation
     * @author Rashed
     * @version 0.1
     */
    public function addOperationAction() {

        $this->_helper->JSLibs->load_jqui_aristo();

        $this->getClient();
        $this->view->client = $this->client;

        $form = new Admin_Form_ClientOperations();
        $this->view->form = $form;
        $this->view->client = $this->client;
        $clientOperation = $this->em->getRepository('BL\Entity\ClientOperation')->findOneBy(array('user_id' => (int) $this->client->id));
        if (sizeof($clientOperation)) {
            $existing_data = array(
                'license_agreement_signee' => $clientOperation->letter_signee,
                'signee_title' => $clientOperation->letter_signee2,
                'org_magazine_circulation_size' => $clientOperation->circulation_size,
                'frequency_of_mag' => $clientOperation->frequency_of_mag,
                'accept_advertising' => $clientOperation->accept_advertising,
                'advertising_rates' => $clientOperation->advertising_rates,
                'advertising_deadlines' => $clientOperation->advertising_deadlines ? $clientOperation->advertising_deadlines->format('m/d/y') : '',
                'workshop_names' => $clientOperation->workshop_names,
                'org_workshop_type' => $clientOperation->workshop_type,
                'org_workshop_dates' => $clientOperation->workshop_dates ? $clientOperation->workshop_dates->format('m/d/y') : '',
                'convention_name' => $clientOperation->convention_name,
                'org_convention_site' => $clientOperation->convention_site,
                'org_convention_dates' => $clientOperation->convention_date ? $clientOperation->convention_date->format('m/d/y') : '',
                'org_pms_colors_1' => $clientOperation->pms_color_1,
                'org_pms_colors_2' => $clientOperation->pms_color_2,
                'org_pms_colors_3' => $clientOperation->pms_color_3,
                'org_pms_colors_4' => $clientOperation->pms_color_4,
                'default_note_to_all_applying_vendors' => $clientOperation->notes
            );
            $form->populate($existing_data);
        } else {
            $clientOperation = new \BL\Entity\ClientOperation();
        }

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $user = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id' => $this->client));

//                $clientOperation = new \BL\Entity\ClientOperation();
                $clientOperation->user_id = $user;
                $clientOperation->letter_signee = $form->getValue('license_agreement_signee');
                $clientOperation->letter_signee2 = $form->getValue('signee_title');
                $clientOperation->circulation_size = $form->getValue('org_magazine_circulation_size');
                $clientOperation->frequency_of_mag = $form->getValue('frequency_of_mag');
                $clientOperation->accept_advertising = $form->getValue('accept_advertising');
                $clientOperation->advertising_rates = $form->getValue('advertising_rates');
                $clientOperation->advertising_deadlines = new DateTime(date('Y-m-d', strtotime($form->getValue('advertising_deadlines'))));
                $clientOperation->workshop_names = $form->getValue('workshop_names');
                $clientOperation->workshop_type = $form->getValue('org_workshop_type');
                $clientOperation->workshop_dates = new DateTime(date('Y-m-d', strtotime($form->getValue('org_workshop_dates'))));
                $clientOperation->convention_name = $form->getValue('convention_name');
                $clientOperation->convention_site = $form->getValue('org_convention_site');
                $clientOperation->convention_date = new DateTime(date('Y-m-d', strtotime($form->getValue('org_convention_dates'))));
                $clientOperation->pms_color_1 = $form->getValue('org_pms_colors_1');
                $clientOperation->pms_color_2 = $form->getValue('org_pms_colors_2');
                $clientOperation->pms_color_3 = $form->getValue('org_pms_colors_3');
                $clientOperation->pms_color_4 = $form->getValue('org_pms_colors_4');
                $clientOperation->notes = $form->getValue('default_note_to_all_applying_vendors');
                $this->em->persist($clientOperation);
                $this->em->flush();

                $this->_helper->flashMessenger("Operations created succesfully!", "Info");
                //$this->_redirect('admin/clients/add-client-notes/id/' . $user->id);
                $this->_redirect('admin/clients/add-royalty-fee/id/' . $user->id);
            } else {
                $form->populate($formData);
            }
        }
    }

    /**
     * Function add royalty fee for newly created client.
     * @author Rasidul
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     */
    public function addRoyaltyFeeAction() {
        $this->getClient();
        $this->view->client = $this->client;
        $form = new Admin_Form_RoyaltyFee();
        $this->view->form = $form;
        $clientProfile = $this->em->getRepository('BL\Entity\ClientProfile')->findOneBy(array('user_id' => (int) $this->client->id));
        $form->getElement('greek_grant_of_license')->setValue((sizeof($clientProfile)) ? $clientProfile->greek_grant_of_license : '');
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $clientProfile->greek_grant_of_license = $form->getValue('greek_grant_of_license');
                $this->em->persist($clientProfile);
                $this->em->flush();

                $this->_helper->flashMessenger("Grant of license update succesfully!", "Info");
                //$this->_redirect($this->view->BUrl()->absoluteUrl());
                $this->_redirect('admin/clients/add-client-notes/id/' . $this->client->id);
            } else {
                $form->populate($formData);
            }
        }
    }

    /**
     * Function to add client Notes
     * @author Rashed
     * @version 0.1
     */
    public function addClientNotesAction() {
        $this->getClient();
        $this->view->client = $this->client;

        $form = new Admin_Form_ClientNotes();
        $this->view->form = $form;
        $this->view->client = $this->client;
        $ClientNote = $this->em->getRepository('BL\Entity\ClientNote')->findOneBy(array('client_id' => (int) $this->client->id));
        if (sizeof($ClientNote)) {
            $form->getElement('note')->setValue((sizeof($ClientNote)) ? $ClientNote->note : '');
        } else {
            $clientNote = new \BL\Entity\ClientNote();
        }

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $user = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id' => $this->client));

//                $clientNote = new \BL\Entity\ClientNote();
                $clientNote->client_id = $user;
                $clientNote->note = $form->getValue('note');
                $this->em->persist($clientNote);
                $this->em->flush();

                $this->_helper->flashMessenger("Client notes created succesfully!", "Info");
                $this->_redirect('admin/clients/add-lic-template/id/' . $user->id);
            } else {
                $form->populate($formData);
            }
        }
    }

    /**
     * Function to add client Lic Template
     * @author Rashed
     * @version 0.1
     */
    public function addLicTemplateAction() {
        $this->getClient();
        $this->view->client = $this->client;
        $this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_tinymce_assets();
        $this->_helper->JSLibs->load_fancy_assets();
        $form = new Admin_Form_LicenseTemplate();

        $marge_fields = array(
            'organization name',
            'company name',
            'company address',
            'company city, state zip',
            'day',
            'month',
            'year',
            'organization',
            'address',
            'company',
            'Insignia of organization',
            'Executive Director'
        );
        $this->view->marge_fields = $marge_fields;
        $this->view->noinsurance = $this->_getParam('noinsurance', NULL);
        $condition = array('client' => $this->client->id);
        $this->view->noinsurance ? ($condition['has_insurance'] = NULL) : ($condition['has_insurance'] = 1);
        $template = $this->em->getRepository("BL\Entity\LicenseTemplate")->findBy($condition, null, 1);
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $template_content = $formData['template'];
                if (sizeof($template)) {
                    $lic_template = $template[0];
                    $lic_template->template = $template_content;
                    $this->em->persist($lic_template);
                    $this->em->flush();
                    $this->em->clear();
                    $this->_helper->flashMessenger("License template updated succesfully!", "Info");
                    $this->_redirect($this->view->BUrl()->absoluteUrl());
                } else if ($template_content) {
                    $lic_template = new \BL\Entity\LicenseTemplate();
                    $lic_template->template = $template_content;
                    $lic_template->client = $this->client;
                    $lic_template->has_insurance = $this->view->noinsurance ? NULL : 1;
                    $this->em->persist($lic_template);
                    $this->em->flush();
                    $this->em->clear();
                    $this->_helper->flashMessenger("License template created succesfully!", "Info");
                    $this->_redirect($this->view->BUrl()->absoluteUrl());

                    $this->view->message = array('success' => true, 'message' => 'Successfully added');
                } else {
                    $this->view->message = array('success' => false, 'message' => 'Please enter license template');
                }
                $template = $this->em->getRepository("BL\Entity\LicenseTemplate")->findBy($condition, null, 1);
            } else {
                $form->populate($formData);
            }
        }
        if (!sizeof($template)) {
            if ($this->_getParam('noinsurance') == 1) {
                $template = $this->em->getRepository("BL\Entity\MasterTemplate")->findBy(array('has_insurance' => NULL), array('id' => 'DESC'), 1);
            } else {
                $template = $this->em->getRepository("BL\Entity\MasterTemplate")->findBy(array('has_insurance' => (int) 1), array('id' => 'DESC'), 1);
            }
        }
        $form->template->setValue(@$template[0]->template);
        $this->view->form = $form;
    }

    public function allPaymentsAction() {
        // action body
    }

    /**
     * @author Rashed
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     * @param <user entity object> $user
     * @return <array>
     */
    public function generate_default_client_legal($user) {

        if (is_object($user)) {
            $clientLegal = $this->em->getRepository('BL\Entity\ClientLegal')->findOneBy(array('user_id' => (int) $user->id));

            if (count($clientLegal) > 0) {
                $existing_data = array(
                    'org_choice_law' => $clientLegal->choice_of_law,
                    'licensor_title' => $clientLegal->licensor_title,
                    'legal_name' => $clientLegal->legal_name,
                    'legal_firm' => $clientLegal->legal_firm,
                    'legal_address1' => $clientLegal->legal_address_line1,
                    'legal_address2' => $clientLegal->legal_address_line2,
                    'legal_city' => $clientLegal->legal_city,
                    'legal_state' => $clientLegal->legal_state,
                    'legal_zip' => $clientLegal->legal_zipcode,
                    'legal_phone' => $clientLegal->legal_phone
                );
            } else {
                $existing_data = array(
                    'org_choice_law' => '',
                    'licensor_title' => '',
                    'legal_name' => '',
                    'legal_firm' => '',
                    'legal_address1' => '',
                    'legal_address2' => '',
                    'legal_city' => '',
                    'legal_state' => '',
                    'legal_zip' => '',
                    'legal_phone' => ''
                );
            }


            return $existing_data;
        }
        return false;
    }

    /**
     * @author Rashed
     * @version 0.1
     * @copyright Blueliner Marketing
     */
    public function addRoyaltyAction() {

    }

    /**
     * Function to provide JSON data to feed data table
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetClientsListAction() {
        $params = array(
            'search' => $this->_getParam('sSearch', ''),
            'page_start' => $this->_getParam('iDisplayStart', 1),
            'draw_count' => $this->_getParam('sEcho', 1),
            'per_page' => $this->_getParam('iDisplayLength', 10),
            'status' => $this->_getParam('status', 'all')
        );
        /**
         * Let's take care of the sorting column to be passed to doctrine.
         * DataTable sends params like iSortCol_0.
         */
        $sorting_cols = array(
            '0' => 'u.organization_name',
            '1' => 'u.created_at',
            '2' => 'u.user_status'
        );

        $params['sort_key'] = $sorting_cols[$this->_getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->_getParam('sSortDir_0', 'asc');
        $records = $this->em->getRepository("BL\Entity\User")->getAllClients($params)->getResult();
        $params['show_total'] = true;
        $records_total = $this->em->getRepository("BL\Entity\User")->getAllClients($params);
        $this->_helper->BUtilities->setNoLayout();

        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":[';
        $first = 0;
        foreach ($records as $v) {
            if ($first++) {
                $json .= ',';
            }
            if (!is_null($v->created_at)) {
                $created_at = ( (int) $v->created_at->format("Y") > 0 ? $v->created_at->format("M d, Y H:i A") : 'N/A');
            } else {
                $created_at = 'N/A';
            }
            $json .= '["<a href=\"javascript:;\" class=\"client_link\" rel=\"' . $v->id . '\">' . str_replace(chr(13), "", str_replace(chr(10), "", $v->organization_name)) . '</a>",
           "' . $created_at . /* '","' . (!is_null($v->user_status) ? $v->user_status : "-") . */ '"]';
        }
        $json .= ']}';

        echo $json;
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
        switch ($search_form) {
            case 'setup':
//                $form = new Admin_Form_VendorContact();
//                $this->view->form = $form;
                $this->view->states_list = $this->em->getRepository("BL\Entity\State")->findAll();
                $this->_helper->viewRenderer('search-forms/setup-form');
                break;
            case 'correspondence':
                $this->_helper->viewRenderer('search-forms/correspondence-form');
                break;
            default:
                echo "<h2>Search</h2>";
                exit(0);
                break;
        }
    }

    /**
     * Function to Show Search Result
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function searchResultAction() {
        $this->_helper->BUtilities->setNoLayout();
        $search_type = $this->_getParam('search_type');
        switch ($search_type) {
            case 'setup':
                echo $this->em->getRepository('BL\Entity\ClientProfile')->searchClientBySetup($this->_getAllParams());
                break;
            case 'correspondence':
                echo $this->em->getRepository('BL\Entity\ClientCorrespondence')->searchClientByCorrespondence($this->_getAllParams());
                break;
            default:
                break;
        }
    }

    public function setupActon() {
        $this->getClient();
        $this->view->client = $this->client;
    }

    /**
     * Function to Show search result
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function showResultDetailsAction() {
        $result_type = $this->_getParam('res-type', '');
        $this->_helper->BUtilities->setEmptyLayout();
        $client_id = $this->_getParam('id');
        $this->view->client = $this->em->getRepository('BL\Entity\User')->findOneBy(array('account_type' => ACC_TYPE_CLIENT, 'id' => $client_id));
        switch ($result_type) {
            case 'correspondence':
                $form = new Admin_Form_ClientCorrespondence();
                $note_id = $this->_getParam('note-id', 0);
                $correspondence = $this->em->find('BL\Entity\ClientCorrespondence', (int) $this->_getParam('cid'));
                $form->populate(array('subject' => $correspondence->subject, 'note' => $correspondence->note, 'note_id' => $correspondence->id));
                $this->view->is_popup = "yes";
                $this->view->correspondence = $correspondence;
                $this->view->form = $form;
                $this->_helper->viewRenderer('correspondence-form-partial');
                break;
            case 'setup':
                $this->_forward('view', null, null, array('id' => $this->_getParam('id'), 'action' => 'view', 'from_popup' => 'yes'));
                break;
            default:
                break;
        }
    }

    /**
     * Function to Save Correspondence Notes via AJAX
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxSaveCorrespondenceAction() {
        $this->_helper->BUtilities->setNoLayout();
        $client_id = $this->_getParam('cid');
        $note_id = $this->_getParam('note_id', 0);
        $note = $this->_getParam('note');
        $subject = $this->_getParam('subject');
        if ($note_id) {
            $new_note = $this->em->find('BL\Entity\ClientCorrespondence', (int) $note_id);
        } else {
            $new_note = new \BL\Entity\ClientCorrespondence();
        }
        $new_note->note = $note;
        $new_note->subject = $subject;
        $new_note->note_time = new DateTime();
        $new_note->client_id = $this->em->find('BL\Entity\User', (int) $client_id);
        $this->em->persist($new_note);
        $this->em->flush();
        if ($new_note->id) {
            echo Zend_Json::encode(array('code' => 'success', 'msg' => 'Successfully Added the Correspondence Note'));
        } else {
            echo Zend_Json::encode(array('code' => 'error', 'msg' => 'There was a problem adding the note'));
        }
    }

     public function printinpdfAction() {

        $this->_helper->BUtilities->setNoLayout();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $formData = $request->getPost();
        $license = $formData['template'];
        $path = $request->getScheme() . '://' . $request->getHttpHost() . Zend_Controller_Front::getInstance()->getBaseUrl();
        
        
        $text = "../../../../assets";
        $text2 = "../../http:/devgreeklicensing.com/crm/assets";
        $licensing_agreement = $license;
        $licensing_agreement = str_replace($text, $path . '/assets', $licensing_agreement);
        $licensing_agreement = str_replace($text2, $path . '/assets', $licensing_agreement);
        
        $pattern = '/&nbsp;<br>/';
        $replace = '<br pagebreak="true" />';
        $licensing_agreement = preg_replace($pattern, $replace, $licensing_agreement);
       // print_r($licensing_agreement);

       /* $sitnature = '<br pagebreak="true" /><table style="border-style: none;" border="0">
        <tbody>
            <tr>
                <td style="border-style: none; width: 45%; vertical-align: top;">
                    <div style="border-right: 1px dotted #aaa;">
                        <h3>'. $license->vendor_name .'</h3><br /><br />' . $license->vendor_signature . '<br /><br />' . $license->vendor_title . '<br /><br />' . $license->vendor_sign_date->format('m-d-y') . '
                    </div>
                </td>
                <td style="border-style: none; width: 55%; vertical-align: top;">
                    <div style="padding-left: 20px;">
                        <h3>'. $license->client_name .'</h3><br /><br />' . $license->client_signature . '<br /><br />' . $license->client_title . '<br /><br />' . $license->client_sign_date->format('m-d-y') . '
                    </div>
                </td>
            </tr>
        </tbody>
        </table>';
        $licensing_agreement .= $sitnature;  */

        require_once('ThirdParty/tcpdf/config/lang/eng.php');
        require_once('ThirdParty/tcpdf/tcpdf.php');

        // create new PDF document
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
        //$pdf =  new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, false, 'ISO-8859-1', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('AMC Admin');
        $pdf->SetTitle('Licensing Agreement');
        $pdf->SetSubject('Licensing Agreement between clinet and Vendor');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        // set default header data
//        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 065', PDF_HEADER_STRING);
        // set header and footer fonts
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

    public function pdflinkAction(){
        $this->_helper->BUtilities->setNoLayout();
        $path =  rtrim(Zend_Controller_Front::getInstance()->getBaseUrl(),'/')."/tmp/". $this->_getParam('filename');
        echo '<div style="font-family: DroidSansRegular,\"Segoe UI\",\"Lucida Sans Unicode\",\"Lucida Grande\",sans-serif;font-size: 13px;">';
        echo '<a target="_blank" href="'.$path.'">Click </a>to download the PDF</div> ';
    }
/**
     * Function to delete usage guide
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function deleteUsageGuideAction() {
        $this->_helper->BUtilities->setNoLayout();
        if ($this->_request->isXmlHttpRequest()) {
            $Ids = explode(',', $this->_getParam('id'));
            foreach ($Ids as $id) {
                $guide = $this->em->getRepository('BL\Entity\ClientUsageGuide')->findOneBy(array('id' => $id));
                if ($guide) {
                    $targetDir = APPLICATION_PATH . '/../assets/files/usage_guides/';
                    @unlink($targetDir . $guide->guide_url);
                    $this->em->remove($guide);
                    $this->em->flush();
                    $this->em->clear();
                } else {
                    throw new Zend_Controller_Action_Exception("Required Parameter Missing or Incorrect", 404);
                }
            }
        }
    }

}

