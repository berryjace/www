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

class Client_LicenseController extends Zend_Controller_Action {
	
    protected $em;

    public function init() {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
    }

    /**
     * Function to show licenses
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function indexAction() {
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_fancy_assets', 'load_dataTable_assets'));
        $this->view->status_array = $this->_helper->BUtilities->parseYAML(APPLICATION_PATH . '/configs/statuses.yml');
        $from_review = $this->_getParam('confirm', '');
        if ($from_review != '') {
            $vendor_name = $this->_getParam('vendor_name', '');
            $vendor_name = str_replace("&apos;", "'", $vendor_name);
            if ($from_review == "approved") {
                $this->_helper->flashMessenger->addMessage("Signed a License Agreement – A license agreement has now been activated with a vendor (" . $vendor_name . ")", "Approved");
            } else if ($from_review == "cancel") {
                $this->_helper->flashMessenger->addMessage("A license agreement has been declined between you and " . $vendor_name, "Declined");
            }
            $this->view->license_msg_header = $from_review;
            $session_message_header = new Zend_Session_Namespace('msg_header');
            $session_message_header->message_header = $from_review;
            //$this->_helper->redirector('');
            $this->_redirect('client/license/index');
        }
        
		$client_id = $this->_helper->BUtilities->getLoggedInUser();
		
		$client = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id'=>$client_id));
		
		$this->view->client = $client;
    }
    
    public function ajaxGetVendorPdfAction(){
    	$this->_helper->BUtilities->setNoLayout();
    
    	$client_id = $this->_getParam('client_id');
    
    	$client = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id'=>$client_id));
    
    	$licenses = $this->em->getRepository("BL\Entity\License")->getClientLicensesSortedVendor($client->id);
    
    	$this->view->client = $client;
    	$this->view->licenses = $licenses;
    
    	$this->view->vendorOperationRepository = $this->em->getRepository("BL\Entity\VendorOperation");
    
    	$html = $this->view->render('license/ajax-get-vendor-pdf.phtml');
    
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
    }

    public function showAction() {
        // action body
    }

    /**
     * Function to provide JSON data to feed data table for pending license
     * @author Sukhon
     * @copyright Blueliner Marketing
     */
    public function ajaxGetLicensedVendorsDtAction() {
        $this->_helper->BUtilities->setNoLayout();
        $params = array(
            'search' => $this->_getParam('sSearch', ''),
            'page_start' => $this->_getParam('iDisplayStart', 1),
            'draw_count' => $this->_getParam('sEcho', 1),
            'per_page' => $this->_getParam('iDisplayLength', 10),
            'iSortCol_0' => $this->_getParam('iSortCol_0', 0),
            'client_id' => $this->_helper->BUtilities->getLoggedInUser(),
            'status' => '2',
        );
        /**
         * Let's take care of the sorting column to be passed to doctrine.
         * DataTable sends params like iSortCol_0.
         */
        $sorting_cols = array(
            '0' => 'v.organization_name',
            '1' => 'l.applied_date',
            '2' => 'l.status'
        );

        $params['sort_key'] = $sorting_cols[$this->_getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->_getParam('sSortDir_0', 'asc');
        $status_array = $this->_helper->BUtilities->parseYAML(APPLICATION_PATH . '/configs/statuses.yml');
        $json = $this->em->getRepository("BL\Entity\License")->getClientLicenses($params, $status_array);
        echo $json;
    }

    /**
     * function to get all product of a particular category using ajax call
     * @author sukhon
     * @copyright Blueliner Marketing

     */
    public function signatureAction() {
        $this->_helper->JSLibs->load_fancy_assets();
        $this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_tinymce_assets();
        $ajax = $this->_getParam('ajax', 0);
        $send = $this->_getParam('send', 0);
        $license = $this->em->getRepository('BL\Entity\License')->findOneBy(array('id' => (int) $this->_getParam('l_id'),
            'client_id' => (int) $this->_helper->BUtilities->getLoggedInUser()));
        //$this->_helper->BUtilities->doctrine_dump($license);
        $form = new Client_Form_Signature();
        $this->view->license = $license;
        if (!sizeof($license)) {
            $this->view->msg = "Invalid license for signature";
            $this->_helper->flashMessenger($this->view->msg, "Info");
            $this->_redirect('/client/license');
        } else if ($this->getRequest()->isPost() || $this->_request->isXmlHttpRequest()) {
            
            $form->vendor_products->setAttribs(array('cols' => '135', 'rows' => '4', 'class' => 'textarea', 'readonly' => 'readonly'));
            
            $formData = $this->getRequest()->getPost();
            if ($formData['app_form'] == 'cancel') {
                $license->cancel_date = new DateTime(date('Y-m-d H:i:s'));
                $license->status = '7';
                $license->client_decline_reason = $formData['hidden_decline_reasons'];
                $this->em->persist($license);
                $this->em->flush();
                $this->em->clear();
                $admin = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id' => (int) 1));
                $form_email = preg_split('/[;,]/', $license->client_id->agreement_notification_email);
                $params = array(
                    //'to' => preg_split('/[;,]/', $license->vendor_id->email),
                    //'to_name' => $license->vendor_id->organization_name,
                    'to'=>'licensing@greeklicensing.com',
                	'to_name'=>'Licensing',
                    'from' => $form_email[0],
                    'from_name' => $license->client_id->organization_name,
                    'subject' => $formData['mail_subject_text'],
                    'body' => $formData['hidden_mail_body'],
                );
                $notification = array(
                    'title' => $params['subject'],
                    'message' => $params['body'],
                    'send_via' => 'site_notification',
                    'for_user_type' => 'random',
                    'created_by' => $this->_helper->BUtilities->getLoggedInUser(),
                    'notification_user' => array('' => $license->vendor_id->id)
                );
                $this->_helper->Notification->send_notification($notification);
                $send = $this->_helper->BUtilities->send_mail($params);
                echo Zend_Json::encode(array('error' => false, 'message' => $form->getMessages()));
            } else {
                if ($form->isValid($formData)) {
                    $license->client_signature = $formData['client_signature'];
                    $license->client_title = $formData['client_title'];
                    $license->agreement_statement = $formData['agreement_statement'];
                    $license->vendor_products = $formData['vendor_products'];
                    if ($formData['app_form'] == 'approved') {
                        $license->client_sign_date = new DateTime(date('Y-m-d H:i:s'));
                        $license->status = '4';
                    }
                    if ($send == 1) {
                        $this->em->persist($license);
                        $this->em->flush();
                        $this->em->clear();
                        $pdf = $this->printinpdf($license);
                        $mail_body = 'Dear ' . $license->vendor_id->first_name . ',<br><br>Your license agreement with ' . $license->client_id->organization_name . ' has been countersigned and is now active.<br><br>';
                        $mail_body = $mail_body . 'This agreement is attached for your reference. Please review it carefully as it outlines your obligations as a licensed vendor. These obligations include, but are not limited to:<br/><br/>';
                        $mail_body .= '<b>Quality Standards</b> - New products/designs that utilize the trademarks or any that are similar (nicknames, symbols, etc.) must be submitted for approval prior to production, marketing and/or sale.<br/><br/>';
                        $mail_body .= '<b>Reporting</b> - Licensee must report sales and pay royalties on a quarterly basis (withink thirty (30) days of September 30, December 31, March 31 and June 30), including zero sales reports. Please note that all late reports will incur a $15 fee per group and sales reports must include any and all use of trademarks or any that are similar (nicknames, symbols, etc.).<br/><br/>';
                        $mail_body .= '<b>Labeling</b> - All individual products must be labeled with the Official Licensed Product hologram stcker (<a href="http://www.opsecsecurity.com/assets/files/brand-protection/ONLINE_INSTRUCTIONS.pdf">http://www.opsecsecurity.com/assets/files/brand-protection/ONLINE_INSTRUCTIONS.pdf</a>). All websites/promotional pieces advertising these products must prominently display the Official Licensed Product logo.<br/><br/>';
                        $mail_body .= 'If you have any questions about these provisions of your agreement, please reach out to us and we will be happy to assist. <br><br>Thank you,<br><br>Licensing Department';
                        $mail_body = $mail_body . '<br>e: Licensing@greeklicensing.com<br>p: 760-734-6764 ext. 140<br>';
                        $mail_body = $mail_body . 'f: 707-202-0532';
                        $form_email = preg_split('/[;,]/', $license->client_id->email);
			//$license->vendor_id->user_status = "Current";
			
                        $vendor = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id'=>$license->vendor_id->id));
                        
                        $vendor->user_status = "Current";
                        $this->em->persist($vendor);
                        $this->em->flush();

                        //$cc = array("ringleader@finelinegames.com"); //array("licensing@greeklicensing.com");
                        //$cc = array(preg_split('/[;,]/', $license->vendor_id->email));
                        $cc = array($license->client_id->email);
                        
                        $params = array(
                            'to' => $license->vendor_id->email,
                            'to_name' => $license->vendor_id->organization_name,
                            'from' => 'Licensing@greeklicensing.com',
                            'from_name' => 'Greek Licensing',
                            'subject' => 'Your license agreement with ' . $license->client_id->organization_name . ' is now active',
                            'body' => $mail_body,
                            'file' => $pdf,
                            'bcc' => $cc
                        );
                        $notification = array(
                            'title' => $params['subject'],
                            'message' => $params['body'],
                            'send_via' => 'site_notification',
                            'for_user_type' => 'random',
                            'created_by' => $this->_helper->BUtilities->getLoggedInUser(),
                            'notification_user' => array('' => $license->vendor_id->id)
                        );
                        $this->_helper->Notification->send_notification($notification);
                        $send = $this->_helper->BUtilities->send_mail($params);
                        echo Zend_Json::encode(array('error' => false, 'message' => $form->getMessages()));
                    } else {
                        echo Zend_Json::encode(array('error' => false, 'message' => $form->getMessages()));
                    }
                } else {
                    echo Zend_Json::encode(array('error' => true, 'message' => $form->getMessages()));
                    $form->populate($formData);
                }
            }
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout()->disableLayout();
        } else {
        	error_log("\nelse", 3, "./errorLog.log");

            $form->vendor_name->setValue($license->vendor_name);
            if ($license->vendor_type == 1) {
                $form->royalty_commission->setValue($license->royalty_commission . "%");
            } else {
                $form->royalty_commission->setValue($license->royalty_commission);
            }
            $form->vendor_products->setValue($license->vendor_products);
            $form->royalty_structure->setValue($license->royalty_structure);
            $form->client_name->setValue($license->client_name);
            $form->royalty_description->setValue($license->royalty_description);
            $form->grant_of_license->setValue($license->grant_of_license);
            $form->annual_advance->setValue($license->annual_advance);
            $form->recom_for_client->setValue($license->recom_for_client);
            $form->license_number->setValue($license->license_agree_number);
            $form->agreement_statement->setValue($license->agreement_statement);
            $form->supplier_name->setValue($license->supplier_name);
            $form->target_audience->setValue($license->target_audience_vendor);

            if (is_null($license->product_sample_link)) {
                $this->view->sample_link_saver = '';
                $this->view->sample_link_saver_array_make = '';
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
        }
        $this->view->form = $form;
    }

    /**
     * Function to print in pdf
     * @author Zea
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access private
     * @param Object $license
     * @return void
     */
    private function printinpdf($license) {
//        $this->view->BUtils()->doctrine_dump($license);
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $path = $request->getScheme() . '://' . $request->getHttpHost() . Zend_Controller_Front::getInstance()->getBaseUrl();
        $text = "../../../../assets";


        $licensing_agreement = $license->agreement_statement;
        $licensing_agreement = str_replace($text, $path . '/assets', $licensing_agreement);
        $pattern = '&nbsp;<br />';
        $replace = '<br pagebreak="true" />';
        $licensing_agreement = str_replace($pattern, $replace, $licensing_agreement);

        error_log($licensing_agreement."\n-------------------------\n", 3, "./Licensing.log");

        require_once('ThirdParty/tcpdf/config/lang/eng.php');
        require_once('ThirdParty/tcpdf/tcpdf.php');

        // create new PDF document
        //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
		$pdf->info = $license->license_agree_number;
        
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('AMC Admin');
        $pdf->SetTitle('Licensing Agreement');
        $pdf->SetSubject('Licensing Agreement between clinet and Vendor');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

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
        // set default font subsetting mode
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('dejavusans', '', 8, '', true);
        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage();

//        $pdf_html = $licensing_agreement;
        //$this->view->BUtils()->doctrine_dump($licensing_agreement);
        $pdf->writeHTML($licensing_agreement, true, 0, true, 0);
        $dt = date("m_d_y");
        $real_path = realpath(dirname(__FILE__) . '/../../../../tmp');
        $pdf->Output($real_path . "/license_agreement_" . $license->license_agree_number . "_" . $dt . ".pdf", 'F');
        return $real_path . "/license_agreement_" . $license->license_agree_number . "_" . $dt . ".pdf";

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

        require_once('ThirdParty/tcpdf/config/lang/eng.php');
        require_once('ThirdParty/tcpdf/tcpdf.php');

        // create new PDF document
        //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
		$pdf->info = $formData['lic_num'];
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('AMC Admin');
        $pdf->SetTitle('Licensing Agreement');
        $pdf->SetSubject('Licensing Agreement between clinet and Vendor');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

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
        // set default font subsetting mode
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('dejavusans', '', 8, '', true);
        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage();

        $pdf->writeHTML($licensing_agreement, true, 0, true, 0);
        $dt = date("m_d_y_h_i_s");
        $real_path = realpath(dirname(__FILE__) . '/../../../../tmp');
        $pdf->Output($real_path . "/license_agreement_" . "_" . $dt . ".pdf", 'F');
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

        $from = $this->_getParam('from');
        
        if ($from == null || $from == ""){
	        echo '<div style="font-family: DroidSansRegular,\"Segoe UI\",\"Lucida Sans Unicode\",\"Lucida Grande\",sans-serif;font-size: 13px;">';
	        echo 'Click <a target="_blank" href="' . $path . '">here</a> to downalod a PDF copy for your viewing convenience.<br/>*please note, however, that all agreements must be signed electronically at the bottom of the webpage*</div> ';
        } else if ($from == "vpdf"){
        	echo '<div style="font-family: DroidSansRegular,\"Segoe UI\",\"Lucida Sans Unicode\",\"Lucida Grande\",sans-serif;font-size: 13px;">';
        	echo 'Click <a target="_blank" href="'.$path.'">here</a> to download your PDF.';	
        }
    }

}

