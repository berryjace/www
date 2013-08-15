<?php

class Vendor_LicenseController extends Zend_Controller_Action {

    public $numberOfLicenseProcessed = 0;
    public $processResult = array();
    private $invoiceNumber = '';

    public function init() {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();

        $this->session = new Zend_Session_Namespace('default');
        $this->session->apply1;
        $this->session->apply2;
        $this->session->apply3;
        $this->session->apply4;
        $this->session->apply5;
        $this->session->client_id;
        $this->session->clientArr;
        $this->session->vendorFee;
        $this->session->applyType;

        $params = array(
            'account_type' => ACC_TYPE_CLIENT,
            'l_status' => '0',
            'order_by' => 'users.organization_name',
            'vendor_id' => $this->_helper->BUtilities->getLoggedInUser()
        );

        $this->organizations = array();
        if ($this->session->applyType == 'reapply') {
            $this->organizations = $this->em->getRepository('BL\Entity\License')->getRejectedClients($params);
        } else {
            $this->organizations = $this->em->getRepository('BL\Entity\License')->getUnLicensedClients($params);
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

        $this->_helper->JSLibs->load_fancy_assets();
    }

    /**
     * Function to show vendor license clients list
     * @author Masud
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     * @return void
     */
    public function indexAction() {
        Zend_Session::namespaceUnset('default');
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets', 'load_fancy_assets'));
        $from_review = $this->_getParam('confirm', '');
        if ($from_review != '') {
            $client_name = $this->_getParam('client_name', '');
            $client_name = str_replace("&apos;", "'", $client_name);
            if ($from_review == "approved") {
                $this->_helper->flashMessenger->addMessage("Signed a License Agreement. A license agreement has now been sent to " . $client_name . " for countersignature.", "Approved");
            } else if ($from_review == "cancel") {
                $this->_helper->flashMessenger->addMessage("A license agreement has been declined between you and " . $client_name, "Declined");
            }
            $this->view->license_msg_header = $from_review;
            $session_message_header = new Zend_Session_Namespace('msg_header');
            $session_message_header->message_header = $from_review;
            //$this->_helper->redirector(''); 
            $this->_redirect('vendor/license/index');
        }
    }

    /**
     * Function to provide JSON data to feed data table
     * @author Masud
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
        );
        /**
         * Let's take care of the sorting column to be passed to doctrine.
         * DataTable sends params like iSortCol_0.
         */
        $sorting_cols = array(
            '0' => 'users.organization_name',
            '1' => 'client_profiles.greek_name',
            '2' => 'licenses.applied_date',
            '3' => 'licenses.status'
        );

        $params['sort_key'] = $sorting_cols[$this->_getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->_getParam('sSortDir_0', 'asc');
        $params['vendor_id'] = $this->_helper->BUtilities->getLoggedInUser();
        $params['l_status'] = $this->_getParam('l_status', 'all');
        $params['iSortCol_0'] = $this->_getParam('iSortCol_0', 3);
        $status_array = $this->_helper->BUtilities->parseYAML(APPLICATION_PATH . '/configs/statuses.yml');
        $json = $this->em->getRepository("BL\Entity\License")->getVendorLicenses($params, $status_array);
        $this->_helper->BUtilities->setNoLayout();
        echo $json;
    }

    /**
     * Function to ajax view of vendor license company information form
     * @author Masud
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     * @return void
     */
    public function apply1Action() {
        $form1 = new Vendor_Form_Apply1();
        $request = $this->getRequest();
        $request->getParam('prev');
        $this->session->client_id = $request->getParam('uid');
        $this->session->applyType = $this->_getParam('ap_type') ? $this->_getParam('ap_type') : '';
        $this->_helper->BUtilities->setAjaxLayout();

        if ($request->getParam('prev') == 'yes') {
            $form1->populate($this->session->apply1);
            $this->view->form = $form1;
        } else if (isset($this->session->apply1)) {
            $form1->populate($this->session->apply1);
            $this->view->form = $form1;
        } else {
            $user = $this->em->getRepository("BL\Entity\User")->findBy(array('id' => (int) $this->_helper->BUtilities->getLoggedInUser()));
            $data = array(
                'organization_name' => $user['0']->organization_name,
                'firstname' => $user['0']->first_name,
                'lastname' => $user['0']->last_name,
                'address_line1' => $user['0']->address_line1,
                'address_line2' => $user['0']->address_line2,
                'city' => $user['0']->city,
                'state' => $user['0']->state,
                'zipcode' => $user['0']->zipcode,
                'phone' => $user['0']->phone,
                'alternate_phone' => $user['0']->phone2,
                'fax' => $user['0']->fax,
                'email' => $user['0']->email,
                'website' => $user['0']->website,
                'v_past_experience' => '' //$user['0']->v_past_experience
            );
            $this->session->apply1 = $data;
            $form1->populate($this->session->apply1);
            $this->view->form = $form1;
        }
    }

    /**
     * Function to ajax view of vendor license organizations form
     * @author Masud
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     * @return void
     */
    public function apply2Action() {

        $form1 = new Vendor_Form_Apply1();
        $form2 = new Vendor_Form_Apply2($this->organizations);

        $request = $this->getRequest();
        $request->getParam('prev');
        $request->getParam('uid');

        $this->_helper->BUtilities->setAjaxLayout();
        $client_id = array('0' => $this->session->client_id);
        $form2->populate($client_id);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form1->isValid($formData)) {
                $this->session->apply1 = $formData;
                $this->view->form = $form2;
                $this->view->client_ids = $client_id;
                if (isset($this->session->apply2)) {
                    $this->view->client_ids = ($this->session->apply2['client_id']);
                    $form2->populate($this->session->apply2);
                }
            } else {
                $form1->populate($formData);
                $this->view->form = $form1;
                $this->_helper->viewRenderer->setRender('apply1');
            }
        } else if ($request->getParam('prev') == 'yes') {
            $this->view->client_ids = ($this->session->apply2['client_id']);
            $form2->populate($this->session->apply2);
            $this->view->form = $form2;
        }
    }

    /**
     * Function to ajax view of vendor license proposed use form
     * @author Masud
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     * @return void
     */
    public function apply3Action() {

        $form2 = new Vendor_Form_Apply2($this->organizations);
        $form3 = new Vendor_Form_Apply3($this->options);

        $request = $this->getRequest();
        $request->getParam('prev');
        $request->getParam('uid');
        //print_r($options);
        $this->_helper->BUtilities->setAjaxLayout();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form2->isValid($formData)) {
                $this->session->apply2 = $formData;
                $this->view->form = $form3;
                if (isset($this->session->apply3)) {
                    $this->view->audience_ids = $this->session->apply3['audience'];
                    $form3->populate($this->session->apply3);
                } else {
                    $this->view->audience_ids = array();
                    list($audiences, $products, $existingData) = $this->getVendorProposedUse();
                    $this->view->audience_ids = $audiences;
                    $this->view->products = $products;
                    $form3->populate($existingData);
                }
            } else {
                $form2->populate($formData);
                $this->view->form = $form2;
                $this->view->client_ids = array();
                $this->_helper->viewRenderer->setRender('apply2');
            }
        } else if ($request->getParam('prev') == 'yes') {
            $this->view->audience_ids = $this->session->apply3['audience'];
            $this->view->form = $form3;
            $form3->populate($this->session->apply3);
        }
    }

    /**
     * Function to ajax view of vendor license financials form
     * @author Masud
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     * @return void
     */
    public function apply4Action() {

        $form3 = new Vendor_Form_Apply3($this->options);
        $form4 = new Vendor_Form_Apply4();

        $request = $this->getRequest();
        $request->getParam('prev');
        $request->getParam('uid');

        $this->_helper->BUtilities->setAjaxLayout();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($form3->isValid($formData)) {
                $this->session->apply3 = $formData;
                $this->view->form = $form4;
                if (isset($this->session->apply4)) {
                    $form4->populate($this->session->apply4);
                } else {
                    $form4->populate($this->getVendorFinancials());
                }
            } else {
                $form3->populate($formData);
                $this->view->form = $form3;
                $this->view->audience_ids = array();
                $this->_helper->viewRenderer->setRender('apply3');
            }
        } else if ($request->getParam('prev') == 'yes') {
            $this->view->form = $form4;
            $form4->populate($this->session->apply4);
        }
    }

    /**
     * Function to ajax view of vendor license checkout form
     * @author Masud
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     * @return void
     */
    public function apply5Action() {

        $request = $this->getRequest();
        $request->getParam('prev');
        $request->getParam('uid');
        $this->_helper->BUtilities->setAjaxLayout();

        $count = 0;
        foreach ($this->session->apply2['client_id'] as $client_id) {
            $clientProfile = $this->em->getRepository("BL\Entity\ClientProfile")->findOneBy(array('user_id' => $client_id));
            $this->session->clientArr[$count] = array(
                'greek_name' => $clientProfile->greek_name,
                'organization_name' => $clientProfile->user_id->organization_name
            );
            $count++;
        }

        $this->session->vendorFee = $this->getVendorFee($this->_helper->BUtilities->getLoggedInUser());
        $form4 = new Vendor_Form_Apply4();
        $form5 = new Vendor_Form_Apply5('check', ($count * $this->session->vendorFee));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form4->isValid($formData)) {
                $this->session->apply4 = $formData;
                $this->view->form = $form5;
                $this->view->clients = $this->session->clientArr;
                $this->view->vendorFee = $this->session->vendorFee;
                if ($this->session->vendorFee == 0) {
                    $this->view->message = "Because your application has been received toward the ended the fiscal year (June 30), you will not be charged an annual advance per organization at this time. However, $40 per organization will be invoiced separately on July 1 of this year.";
                } else {
                    $existing_data = array('amount_total' => $count * $this->session->vendorFee);
                    if (isset($this->session->apply5)) {
                        $form5->populate($this->session->apply5);
                    } else {
                        $form5->populate($existing_data);
                    }
                }
            } else {
                $form4->populate($formData);
                $this->view->form = $form4;
                $this->_helper->viewRenderer->setRender('apply4');
            }
        } else if ($request->getParam('prev') == 'yes') {
            $this->view->form = $form5;
            $form5->populate($this->session->apply5);
        }
    }

    /**
     * Function to process vendor licensing forms
     * @author Masud
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     * @return void
     */
    public function applyPayAction() {

        $form5 = new Vendor_Form_Apply5('check', (sizeof($this->session->clientArr) * $this->session->vendorFee));
        $request = $this->getRequest();
        $request->getParam('prev');
        $request->getParam('uid');
        $this->_helper->BUtilities->setAjaxLayout();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
//            print_r($formData);

            if ($this->session->vendorFee < 0 ) {
                $this->processLicense();
                $this->processResult[] = 'A total of <b>' . $this->numberOfLicenseProcessed . '</b> license application(s) have been successfully submitted!';
                $this->processResult[] = "Our administrators will review each application for completeness and
                                            appropriateness before sending a license agreement to you for electronic
                                            signature.<br /><br />
                                            <b>Please note:</b> If you are applying for the first time, or if you are expanding your product offering, we must receive and approve a physical quality sample from each product category. Please mail quality samples to:<br/><br/>
                                            <em>
                                            Affinity Licensing Division<br/>
                                            106 W. Main St.<br/>
                                            Cortland, NY 13045<br /><br />
                                            </em>
                                            After each application is approved, you will receive a notification that an
                                            agreement is awaiting your signature. Once you sign the agreement it will
                                            be sent to the respective organization for review and counter signature.
                                            (Please note that you are not licensed with a group until you receive a
                                            counter-signed agreement). <br /><br />
                                            You can click on the LICENSES tab in your profile at any time to view an
                                            applicationâ€™s status.";
                Zend_Session::namespaceUnset('default');
                $this->view->assign('processResult', $this->processResult);
            } else {
                if ($form5->isValid($formData)) {
                    if (isset($formData['billing_options']) && $formData['billing_options'] == 'card') {
                        $form5 = new Vendor_Form_Apply5('card', (sizeof($this->session->clientArr) * $this->session->vendorFee));
                        $form5->populate($formData);
                        $this->view->form = $form5;
                        $this->view->assign('clients', $this->session->clientArr);
                        $this->view->assign('vendorFee', $this->session->vendorFee);
                        $formData = $this->getRequest()->getPost();
                        if ($form5->isValid($formData)) {
                            $response = $this->call_bill_highway_api(array('amount' => $formData['amount_total'], 'rtn_no' => $formData['bank_routing'], 'account_no' => $formData['bank_acc_no']));
//                            print_r($response);
//                            echo $response->eCheckPaymentByGroupResult->anyType['4'];
                            $this->session->apply5 = $formData;
                            $this->updateUserInfo();     //update user changes information
                            $this->processWithCard();
                            Zend_Session::namespaceUnset('default');
                            $this->view->assign('processResult', $this->processResult);
                        } else {
                            $form5->populate($formData);
                            $this->_helper->viewRenderer->setRender('apply5');
                        }
                    } else {
                        $this->view->form = $form5;
                        $this->view->assign('clients', $this->session->clientArr);
                        $this->view->assign('vendorFee', $this->session->vendorFee);
                        $this->session->apply5 = $formData;
                        $this->updateUserInfo();     //update user changes information
                        $this->processWithCheck();
                        Zend_Session::namespaceUnset('default');
                        $this->view->assign('processResult', $this->processResult);
                    }
                } else {
                    $form5->populate($formData);
                    $this->view->form = $form5;
                    $this->view->assign('clients', $this->session->clientArr);
                    $this->view->assign('vendorFee', $this->session->vendorFee);
                    $this->_helper->viewRenderer->setRender('apply5');
                }
            }
        }
    }

    public function applyPayOldCardAction() {
        $client = $this->em->getRepository("BL\Entity\User")->findOneBy(array('account_type' => ACC_TYPE_CLIENT, 'id' => (int) 7089));
        $this->view->BUtils()->doctrine_dump($client);

        //action body
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

        $this->_helper->BUtilities->setAjaxLayout();
    }

    /**
     * function to get all product of a particular category using ajax call
     * @author Zea
     * @copyright Blueliner Marketing
     */
    public function signatureAction() {
        $this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_tinymce_assets();
        $this->_helper->JSLibs->load_fancy_assets();

        $ajax = $this->_getParam('ajax', 0);
        $send = $this->_getParam('send', 0);
        $license = $this->em->getRepository('BL\Entity\License')->findOneBy(array('id' => (int) $this->_getParam('l_id'),
            'vendor_id' => (int) $this->_helper->BUtilities->getLoggedInUser()));
        //$this->_helper->BUtilities->doctrine_dump($license);
        $form = new Vendor_Form_Signature();
        $this->view->license = $license;
        if (!sizeof($license)) {
            $this->view->msg = "Invalid license for signature";
            $this->_helper->flashMessenger($this->view->msg, "Info");
            $this->_redirect('/vendor/license');
        } else if ($this->getRequest()->isPost() || $this->_request->isXmlHttpRequest()) {
            $formData = $this->getRequest()->getPost();
            $email = array(
                'to' => preg_split('/[;,]/', $license->client_id->email),
                'to_name' => $license->client_id->organization_name,
                'from' => $license->vendor_id->email,
                'from_name' => $license->vendor_id->organization_name
            );
            $notification = array(
                'send_via' => 'site_notification',
                'for_user_type' => 'random',
                'created_by' => $this->_helper->BUtilities->getLoggedInUser(),
                'notification_user' => array('' => $license->client_id->id)
            );
            if ($formData['app_form'] == 'cancel') {
                $mail_body = 'Dear ' . $license->client_id->first_name;
                $mail_body .= 'The application to be licensed with an organization (' . $license->vendor_id->organization_name . ') was declined.<br><br>';
                $mail_body .= 'Regards,<br><br>';
                $mail_body .= $license->vendor_id->first_name;

                $email['subject'] = 'License application was declined';
                $email['body'] = $mail_body;

                $notification['title'] = $email['subject'];
                $notification['message'] = $email['body'];
                $license->cancel_date = new DateTime(date('Y-m-d H:i:s'));
                $license->status = '6';
                $this->em->persist($license);
                $this->em->flush();
                $this->em->clear();
                $this->_helper->Notification->send_notification($notification);
                $this->_helper->BUtilities->send_mail($email);
                echo Zend_Json::encode(array('error' => false, 'message' => $form->getMessages()));
            } else {
                if ($form->isValid($formData)) {
                    $license->vendor_signature = $formData['vendor_signature'];
                    $license->vendor_title = $formData['vendor_title'];
                    $license->agreement_statement = $formData['agreement_statement'];
                    if ($formData['app_form'] == 'approved') {
                        $license->vendor_sign_date = new DateTime(date('Y-m-d H:i:s'));
                        $license->status = '2';
                    }
                    if ($send == 1) {
                        $mail_body = 'Dear ' . $license->client_id->organization_name . ',<br /><br />';
                        $mail_body .= 'A license agreement with a company <b>(' . $license->vendor_id->organization_name . ')</b> has been reviewed by Affinity and signed by the company. It is now ready for your review and countersignature.<br><br>';
                        $mail_body .= 'In order to view this agreement and provide an electronic signature, please log in to your portal by visiting: ';
                        $mail_body .= '<a href="' . $this->view->BUrl()->site_url('login/index/type/client') . '" target="_blank">' . $this->view->BUrl()->site_url('login/index/type/client') . '</a><br /><br />';
                        $mail_body .= 'Regards,<br><br>';
                        $mail_body .= 'Licensing Department<br>e: Licensing@greeklicensing.com<br>p: 760-734-6764<br>f: 707-202-0532';
                        $email['subject'] = 'A License Agreement is Ready for Counter-Signature';
                        $email['body'] = $mail_body;

                        $notification['title'] = $email['subject'];
                        $notification['message'] = $email['body'];
                        $this->em->persist($license);
                        $this->em->flush();
                        $this->em->clear();
                        $this->_helper->Notification->send_notification($notification);
                        $this->_helper->BUtilities->send_mail($email);
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
            $form->vendor_name->setValue($license->vendor_id->organization_name);
            if ($license->vendor_type == 1) {
                $form->royalty_commission->setValue($license->royalty_commission . "%");
            } else {
                $form->royalty_commission->setValue($license->royalty_commission);
            }
            $form->vendor_products->setValue($license->vendor_products);
            $form->royalty_structure->setValue($license->royalty_structure);
            $form->client_name->setValue($license->client_id->organization_name);
            $form->royalty_description->setValue($license->royalty_description);
            $form->grant_of_license->setValue($license->grant_of_license);
            $form->annual_advance->setValue($license->annual_advance);
            $form->recom_for_vendor->setValue($license->recom_for_vendor);
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
     * Function to ajax view of vendor license details for a client
     * @author Masud
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     * @return void
     */
    public function showLicenseAction() {
        $request = $this->getRequest();
        //$this->_helper->BUtilities->setAjaxLayout();        //for ajax view
        $license = $this->em->getRepository('BL\Entity\License')->findOneBy(array('id' => (int) $this->_getParam('lid'), 'client_id' => (int) $this->_getParam('uid'), 'vendor_id' => (int) $this->_helper->BUtilities->getLoggedInUser()));
        $status_array = $this->_helper->BUtilities->parseYAML(APPLICATION_PATH . '/configs/statuses.yml');
//        $this->_helper->BUtilities->doctrine_dump($license);
//        die();
        $pendingSigns = $this->em->getRepository("BL\Entity\License")->findBy(array(
            'vendor_id' => (int) $this->_helper->BUtilities->getLoggedInUser(),
            'status' => 1));
        $status_array = $this->_helper->BUtilities->parseYAML(APPLICATION_PATH . '/configs/statuses.yml');
        $this->view->license_status = $license->status ? $status_array[$license->status] : 'Unlicensed';
        $this->view->license = $license;
        $this->view->pendingSigns = $pendingSigns ? sizeof($pendingSigns) : 0;
    }

    public function showPendingSignatureAction() {
        // action body
        $form0 = new Vendor_Form_Apply0();
        $this->view->form = $form0;
    }

    /**
     * function to get all product of a particular category using ajax call
     * @author Masud
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
        //print_r($products);
        $this->_helper->BUtilities->setAjaxLayout();
        $this->view->assign('products', $products);
        $this->view->assign('cat', $request->getParam('cat')); //echo 'processWithCheck' . $clients_id . '<br />';
        //print_r($productsArr);
        //print_r($request->getParam('cat'));
    }

    /**
     * Function to update user information
     * @author Masud
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access private
     * @return void
     */
    private function updateUserInfo() {
        $user = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' => (int) $this->_helper->BUtilities->getLoggedInUser()));
        //print_r($user);
        $user->organization_name = $this->session->apply1['organization_name'];
        $user->first_name = $this->session->apply1['firstname'];
        $user->last_name = $this->session->apply1['lastname'];
        $user->address_line1 = $this->session->apply1['address_line1'];
        $user->address_line2 = $this->session->apply1['address_line2'];
        $user->city = $this->session->apply1['city'];
        $user->state = $this->session->apply1['state'];
        $user->zipcode = $this->session->apply1['zipcode'];
        $user->phone = $this->session->apply1['phone'];
        $user->phone2 = $this->session->apply1['alternate_phone'];
        $user->fax = $this->session->apply1['fax'];
        $user->email = $this->session->apply1['email'];
        $user->website = $this->session->apply1['website'];
        //print_r($user);
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * Function to process vendor license application using check
     * @author Masud
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access private
     * @return void
     */
    private function processWithCheck() {
        $clients_id = $this->session->apply2['client_id'];
        if ($this->sendInvoice($clients_id)) {
            $this->processLicense();
            $this->processResult[] = 'A total of <b>' . $this->numberOfLicenseProcessed . '</b> license application(s) have been successfully submitted!';
            $this->processResult[] = "Our administrators will review each application for completeness and
                                            appropriateness before sending a license agreement to you for electronic
                                            signature.<br /><br />
                                            <b>Please note:</b> If you are applying for the first time, or if you are expanding your product offering, we must receive and approve a physical quality sample from each product category. Please mail quality samples to:<br/><br/>
                                            <em>
                                            Affinity Licensing Division<br/>
                                            106 W. Main St.<br/>
                                            Cortland, NY 13045<br /><br />
                                            </em>
                                            After each application is approved, you will receive a notification that an
                                            agreement is awaiting your signature. Once you sign the agreement it will
                                            be sent to the respective organization for review and counter signature.
                                            (Please note that you are not licensed with a group until you receive a
                                            counter-signed agreement). <br /><br />
                                            You can click on the LICENSES tab in your profile at any time to view an
                                            application's status.";
        } else {
            $this->processResult[] = 'Some error occured while sending you an invoice; please try again.';
        }
    }

    /**
     * Function to process vendor license application using card
     * @author Masud
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access private
     * @return void
     */
    private function processWithCard() {
//        print_r($this->session->apply4);
        $this->processLicense();
        $this->processResult[] = 'A total of <b>' . $this->numberOfLicenseProcessed . '</b> license application(s) have been successfully submitted!';
        $this->processResult[] = "Our administrators will review each application for completeness and
                                    appropriateness before sending a license agreement to you for electronic
                                    signature.<br /><br />
                                    <b>Please note:</b> If you are applying for the first time, or if you are expanding your product offering, we must receive and approve a physical quality sample from each product category. Please mail quality samples to:<br/><br/>
                                    <em>
                                    Affinity Licensing Division<br/>
                                    106 W. Main St.<br/>
                                    Cortland, NY 13045<br /><br />
                                    </em>
                                    After each application is approved, you will receive a notification that an
                                    agreement is awaiting your signature. Once you sign the agreement it will
                                    be sent to the respective organization for review and counter signature.
                                    (Please note that you are not licensed with a group until you receive a
                                    counter-signed agreement). <br /><br />
                                    You can click on the LICENSES tab in your profile at any time to view an
                                    application's status.";
//        $this->processResult[] = 'A total of <b>' . $this->numberOfLicenseProcessed . '</b> number of license(s) have been processed successfully!';
//        $this->processResult[] = "The Affinity Licensing Administrator will review your application. If the Administrator approves your application, you will receive a notification to sign the license agreement. Expect to hear back in <b>3</b> to <b>5</b> business days.";
        //echo 'processWithCard' . $client_id;
        // sleep(1);
    }

    /**
     * Function to process license application
     * @author Masud
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access private
     * @return void
     */
    private function processLicense() {
        $clients_id = $this->session->apply2['client_id'];
        $vendor_id = $this->_helper->BUtilities->getLoggedInUser();
        $products = $this->session->apply3['products'];
        $productsArr = explode(',', $products);
        $vendor = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id'=>$vendor_id));

        $quarter = Date("m");
        $quarter = ((($quarter / 4) + 2) % 4) + 1;
        $year = Date("Y");
        if ($quarter < 3) $year --;
        
        $invoice = new \BL\Entity\Invoice();
        $invoice->created_at = new DateTime();
        $invoice->updated_at = new DateTime();
        $invoice->invoice_date = new DateTime();
        $invoice->invoice_type="Application Fees";
        $invoice->fiscal_year = $year . '-' . substr(($year+1), 2);
        $invoice->quarter = $quarter;
        $invoice->company_name = $vendor->organization_name;
        $invoice->webpage = $vendor->website;
        $invoice->address_line1 = $vendor->address_line1;
        $invoice->address_line2 = $vendor->address_line2;
        $invoice->city = $vendor->city;
        $invoice->state = $vendor->state;
        $invoice->zip = $vendor->zipcode;
        $invoice->phone1 = $vendor->phone;
        $invoice->phone2 = $vendor->phone2;
        $invoice->email = $vendor->email;
        $invoice->fax = $vendor->fax;
        $invoice->invoice_status = "Open";
        $invoice->payment_status = "Due";
        $invoice->amount_paid = 0;
        $invoice->vendor_id = $vendor;
        
        $this->em->persist($invoice);
        $this->em->flush();
        
        $invoice->invoice_number = $this->view->BUtils()->getInvoiceNumber($vendor->id);
        
        $total_due = 0;
        $total_paid = 0;
        
        foreach ($clients_id as $client_id) {
            try {
                $duplicate_check = $this->em->getRepository('BL\Entity\License')->findBy(array('client_id' => (int) $client_id, 'vendor_id' => (int) $this->_helper->BUtilities->getLoggedInUser()));
                if (!$duplicate_check) {
                    $license = new \BL\Entity\License();
                    $license->client_id = $this->em->find('BL\Entity\User', $client_id);
                    $license->vendor_id = $this->em->find('BL\Entity\User', $vendor_id);
                    $license->status = '3';
                    $license->payment_method = isset($this->session->apply5['billing_options']) ? $this->session->apply5['billing_options'] : 'N/A';
                    $license->payment_status = isset($this->session->apply5['billing_options']) ? 'not_paid' : 'not_charged';
                    $license->supplier_name = $this->session->apply3['supplier_name'];
                    $license->other_desc = $this->session->apply3['other_desc'];
                    $license->applied_date = new DateTime();

                    $item = new \BL\Entity\InvoiceLineItems();
                    
                    $item->invoice_id = $invoice;
                    $item->invoice_number_li = $invoice->invoice_number;
                    $item->lineitems_number = $invoice->invoice_number;
                    $item->amount_due = $this->session->vendorFee;
                    $item->amount_paid = 0;
                    $item->created_at = new DateTime();
                    $item->updated_at = new DateTime();
                    $item->payment_status = "Due";
                    $item->invoice_status = "Open";
                    $item->fiscal_year =$year . '-' . substr(($year + 1), 2);
                    $item->quarter = $quarter;
                    $item->license_status = 0;
                    $item->client_id = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id'=>$client_id));
                    
                    
                    $total_due += $this->session->vendorFee;
                    
                    foreach ($this->session->apply3['audience'] as $audience) {
                        $license_audience = $this->em->getRepository('BL\Entity\TargetAudience')->findOneBy(array('id' => $audience));
                        $license->TargetAudience->add($license_audience);
                    }
                    $this->em->persist($license);
                    $this->em->flush();

                    //for license payments
                    if ($this->session->apply5['billing_options'] == 'card') {
                        $licensePayment = new \BL\Entity\LicensePayment();
                        $licensePayment->amount = $this->session->vendorFee;
                        $licensePayment->bank_account = $this->session->apply5['bank_acc_no'];
                        $licensePayment->bank_routing = $this->session->apply5['bank_routing'];
                        $licensePayment->invoice_number = $this->invoiceNumber;
                        $licensePayment->license_id = $license;
                        $this->em->persist($licensePayment);
                        $this->em->flush();
                        
                        $item->amount_paid = $this->session->vendorFee;
                        $item->license_status = 4;
                        $item->payment_status = "Recieved Check";
                        $item->invoice_status = "Partially Paid";
                        
                        $total_paid += $this->session->vendorFee;
                    }

                    $this->em->persist($item);
                    $this->em->flush();
                    //for license financial statements
                    $licenseFinStatement = new \BL\Entity\LicenseFinStatement();
                    foreach ($this->session->apply4['application_process'] as $app_process) {
                        if ($app_process == 1) {
                            $licenseFinStatement->has_account_in_good_standing = 'yes';
                        } else if ($app_process == 2) {
                            $licenseFinStatement->has_closed_financial_statement = 'yes';
                        } else if ($app_process == 3) {
                            $licenseFinStatement->has_chart_of_capital_assets = 'yes';
                        }
                    }
                    $licenseFinStatement->full_time_employee_num = $this->session->apply4['full_time_employee_num'];
                    $licenseFinStatement->years_in_business = $this->session->apply4['years_in_business'];
                    $licenseFinStatement->business_failure_in_5_years = $this->session->apply4['business_failure_in_5_years'];
                    $licenseFinStatement->any_person_bankrupt = $this->session->apply4['any_person_bankrupt'];
                    $licenseFinStatement->government_investigation = $this->session->apply4['government_investigation'];
                    $licenseFinStatement->contract_terminated_in_last_2_years = $this->session->apply4['contract_terminated_in_last_2_years'];
                    $licenseFinStatement->litigation_against_the_officers = $this->session->apply4['litigation_against_the_officers'];
                    $licenseFinStatement->any_collections_by_debt_collection_agency = $this->session->apply4['any_collections_by_debt_collection_agency'];
                    $licenseFinStatement->additional_explanation = $this->session->apply4['additional_explanation'];
                    $licenseFinStatement->client_id = $this->em->find('BL\Entity\User', $client_id);
                    $licenseFinStatement->vendor_id = $this->em->find('BL\Entity\User', $vendor_id);
                    $licenseFinStatement->license_id = $license;
                    $licenseFinStatement->statement = '';
                    $licenseFinStatement->statement_type = 'financial';
                    $this->em->persist($licenseFinStatement);
                    $this->em->flush();

                    //for vendor products insertion
                    foreach ($productsArr as $product) {
                        $vendorProduct = new BL\Entity\VendorProduct();
                        $vendorProduct->product_id = $this->em->find('BL\Entity\Product', $product);
                        $vendorProduct->vendor_id = $this->em->find('BL\Entity\User', $vendor_id);
                        $vendorProduct->license_id = $this->em->find('BL\Entity\License', $license->id);
                        $this->em->persist($vendorProduct);
                        $this->em->flush();

                        //save if product not exist in venor product info details table
                        $vpid = new BL\Entity\VendorProductInfoDetails();
                        $check_exist = $this->em->getRepository('BL\Entity\VendorProductInfoDetails')->findOneBy(array('product_id' => $product, 'vendor_id' => $vendor_id));
                        if (!sizeof($check_exist)) {
                            $vpid->product_id = $vendorProduct->product_id;
                            $vpid->vendor_id = $vendorProduct->vendor_id;
                            $this->em->persist($vpid);
                            $this->em->flush();
                        }
                    }

                    $this->numberOfLicenseProcessed++;
                } else {
                    $this->processResult[] = 'Duplicate license application issue occured!'; //needs to set up a error variable.
                }
            } catch (Exception $e) {
                $this->processResult[] = $e->getMessage();
            }
        }
        $invoice->amount_paid = $total_paid;
        $invoice->amount_due = $total_due;
        
        if ($total_paid == $total_due){
        	$invoice->payment_status = "Received Check";
        }
        
        $this->em->persist($invoice);
        $this->em->flush();
    }

    /**
     * Function to send email with invoice
     * @author Masud
     * @version 0.1
     * @copyright Blueliner marketing
     * @access private
     * @param integer $client_id
     * @return boolean
     */
    private function sendInvoice($clients_id) {

        $organizations = array();
        foreach ($clients_id as $client_id) {
            $client = $this->em->getRepository("BL\Entity\User")->findOneBy(array('account_type' => ACC_TYPE_CLIENT, 'id' => $client_id));
            array_push($organizations, $client->organization_name);
        }

        //for sending pdf invoice
        $this->invoiceNumber = date('Ymdhs');
        $this->view->organizations = $organizations;
        $this->view->vendorFee = $this->session->vendorFee;
        $this->view->organization_name = $this->session->apply1['organization_name'];
        $this->view->address1 = $this->session->apply1['address_line1'];
        $this->view->address2 = $this->session->apply1['address_line2'];
        $this->view->city = $this->session->apply1['city'];
        $this->view->state= $this->session->apply1['state'];
        $this->view->zip  = $this->session->apply1['zipcode'];
        $pdf_params = array(
            'author' => 'AMC Admin',
            'title' => 'Licensing Application Invoice Payment',
            'subject' => 'Invoice',
            'pdf_content' => $this->view->render('/license/license-pdf-template.phtml'),
            'file_name' => $this->invoiceNumber,
            'file_path' => APPLICATION_PATH . '/../tmp/',
            'output_type' => 'F'
        );
        $save_to = $this->view->BUtils()->getPDF($pdf_params);

        $params = array(
            'to' => $this->session->apply1['email'],
            'to_name' => $this->session->apply1['firstname'],
            'from' => 'registration@greeklicensing.com',
            'from_name' => 'AMC Admin',
            'subject' => 'Licensing Application Received',
            'body' => $this->view->render('/license/license-email-template.phtml'),
            'file' => $save_to
        );
//        print_r($params);
//        die();
        $send = $this->_helper->BUtilities->send_mail($params);
//            print_r($send);
        if ($send) {
            $this->processResult[] = 'An invoice for this application has been sent to your email address.';
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function to get vendor fee
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @param int $vendor_id
     * @return String
     */
    public function getVendorFee($vendor_id) {
//        $vendor = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id' => $vendor_id));
//        $signupDate = $vendor->reg_date->format('m-d-y');
//        $month = explode('-', $signupDate);
        $month = explode('-', date('m-d-y'));
        $fee = 0;

        if ($month['0'] < 5 || $month['0'] > 6) $fee = 20;

        return $fee;
    }

    /**
     * Function to addendums list
     * @author Sukhon
     * @version 0.1
     */
    public function addendumsAction() {
        $params = array(
            'page' => $this->_getParam('page', 1),
            'per_page' => 5,
            'num_of_link' => 5,
            'is_draft' => 0);
        $this->view->addendums = $this->em->getRepository("BL\Entity\Addendum")->getAddendums($params);
//        $this->view->BUtils()->doctrine_dump($this->view->addendums);
    }

    /**
     * Function to sign addendum
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function signAddendumAction() {
        $this->view->addendum = $this->em->getRepository("BL\Entity\Addendum")->findOneBy(array('id' => (int) $this->_getParam('id')));
        $addendumUser = $this->em->getRepository("BL\Entity\AddendumUser")->findBy(array('addendum_id' => (int) $this->_getParam('id')));
//        $this->view->BUtils()->doctrine_dump($this->view->addendum);
//        die();
        $addendumUsers = array();
        if ($addendumUser) {
            foreach ($addendumUser as $c) {
                $addendumUsers[] = $c->user_id->id;
            }
        }
        $this->view->clients = $this->em->getRepository("BL\Entity\User")->getClientNames();
        $this->view->addendumUsers = $addendumUsers;
        $form = new Vendor_Form_AddendumSign();
        $this->view->form = $form;

        $loggedUser = Zend_Auth::getInstance()->getIdentity();
        $existing_data = array('vendor_name' => $loggedUser->organization_name);
        $form->populate($existing_data);
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            print_r($formData);
            die();

//            $this->ajaxValidate($form, $formData);
            if ($form->isValid($formData)) {

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
        $this->view->addendum = $this->em->getRepository("BL\Entity\Addendum")->findOneBy(array('id' => (int) $this->_getParam('id')));
        $addendumUser = $this->em->getRepository("BL\Entity\AddendumUser")->findBy(array('addendum_id' => (int) $this->_getParam('id')));
//        $this->view->BUtils()->doctrine_dump($this->view->addendum);
//        die();
        $addendumUsers = array();
        if ($addendumUser) {
            foreach ($addendumUser as $c) {
                $addendumUsers[] = $c->user_id->id;
            }
        }
        $this->view->clients = $this->em->getRepository("BL\Entity\User")->getClientNames();
        $this->view->form = new Admin_Form_Addendum();
        $this->view->addendumUsers = $addendumUsers;
    }

    /**
     * Function to test action
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function testAction() {
//
//        $iv = chr(18). chr(52). chr(86). chr(120). chr(144). chr(171). chr(205).chr(239);
//        echo $iv;
//        echo $this->view->BUtils()->getCurrency('en_US', 1000);
        //billhighway
//        $client = new SoapClient('https://staging.billhighway.com/api/payment.asmx?WSDL');
//
//        $apiKey = '785A571C-17E3-482C-97EE-A207E2C7F8F4';
//        $featureID = '3';
//        $clientId = '1199';
//        $groupId = '18976';
//        $amount = 50.00;
//        $RTN = '021200339'; //'000048084';
//        $AcctNumber = '12345678'; //003813783087
//        $key = 'E394C14EF283AABAFE816E41';
//        $EncryptedRTN = '';
//        $EncryptedAcctNumber = '';
//
//        $EncryptedAcctNumber = $this->TripeDesEncrypt($key, $AcctNumber);
//        $EncryptedRTN = $this->TripeDesEncrypt($key, $RTN);
//          echo $EncryptedAcctNumber." len = ".  strlen($EncryptedAcctNumber)."<br />";
//          echo $EncryptedRTN." len = ".  strlen($EncryptedRTN);
//        $params = array(
//            'apiKey' => $apiKey,
//            'featureID' => $featureID,
//            'clientID' => $clientId,
//            'groupID' => $groupId,
//            'Amount' => $amount,
//            'rtn' => $EncryptedRTN,
//            'accountNumber' => $EncryptedAcctNumber,
//            'payerName' => 'Test Payer',
//            'checkNumber' => '123',
//            'email' => 'testpayer@billhighway.com',
//            'memo' => 'Test ACH group payment',
//            'occurs' => 'once',
//            'numberOfOccurrences' => 1
//        );
//          var_dump($params);
//        $response = $client->__soapCall("eCheckPaymentByGroup", array("parameters" => $params));
//        var_dump($response);
//        print_r($response);
//        $response = $this->call_bill_highway_api(array('amount' => 50, 'rtn_no' => '021200339', 'account_no' => '12345678'));
//        print_r($response);
//        echo $response->eCheckPaymentByGroupResult->anyType['4'];
    }

    /**
     * Function to call billhighway API
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return Object
     */
    private function call_bill_highway_api($params) {
        $client = new SoapClient('https://staging.billhighway.com/api/payment.asmx?WSDL');

        $apiKey = '785A571C-17E3-482C-97EE-A207E2C7F8F4';
        $featureID = '3';
        $clientId = '1199';
        $groupId = '18976';
        $key = 'E394C14EF283AABAFE816E41';
        $EncryptedRTN = '';
        $EncryptedAcctNumber = '';

        $EncryptedAcctNumber = $this->TripeDesEncrypt($key, $params['account_no']);
        $EncryptedRTN = $this->TripeDesEncrypt($key, $params['rtn_no']);
//          echo $EncryptedAcctNumber." len = ".  strlen($EncryptedAcctNumber)."<br />";
//          echo $EncryptedRTN." len = ".  strlen($EncryptedRTN);

        $params = array(
            'apiKey' => $apiKey,
            'featureID' => $featureID,
            'clientID' => $clientId,
            'groupID' => $groupId,
            'Amount' => $params['amount'],
            'rtn' => $EncryptedRTN,
            'accountNumber' => $EncryptedAcctNumber,
            'payerName' => 'Test Payer',
            'checkNumber' => '123',
            'email' => 'testpayer@billhighway.com',
            'memo' => 'Test ACH group payment',
            'occurs' => 'once',
            'numberOfOccurrences' => 1
        );
        $response = $client->__soapCall("eCheckPaymentByGroup", array("parameters" => $params));
        return $response;
    }

    /**
     * Function to encrypt account and RTN number using billhighway private key
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return encrypted data
     */
    private function TripeDesEncrypt($privateKey, $str) {
//        $iv_size = mcrypt_get_iv_size(MCRYPT_TRIPLEDES, MCRYPT_MODE_ECB);
//        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        //must be less than bolck size which is 8
//        $iv = "greekorg";
        $iv = chr(18) . chr(52) . chr(86) . chr(120) . chr(144) . chr(171) . chr(205) . chr(239);
        $block = mcrypt_get_block_size(MCRYPT_TRIPLEDES, MCRYPT_MODE_CBC);
        $len = strlen($str);
        $padding = $block - ($len % $block);
        $str .= str_repeat(chr($padding), $padding);
        $enc = mcrypt_encrypt(MCRYPT_TRIPLEDES, $privateKey, $str, MCRYPT_MODE_CBC, $iv);
        return base64_encode($enc);
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
     * Function to get vendors existing proposed uses
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access private
     * @return <array>
     */
    private function getVendorProposedUse() {
        $VendorProductAudience = $this->em->getRepository('BL\Entity\VendorProductAudience')->findBy(array('vendor_id' => (int) $this->_helper->BUtilities->getLoggedInUser()));
        $audiences = array();
        foreach ($VendorProductAudience as $audience) {
            $audiences[] = $audience->audience_id->id;
        }

        $VendorProductInfoDetails = $this->em->getRepository('BL\Entity\VendorProductInfoDetails')->getVendorProducts((int) $this->_helper->BUtilities->getLoggedInUser());
        $products = '';
        $product_ids = '';
        for ($i = 0; $i < count($VendorProductInfoDetails); $i++) {
            $products .= '<span id="set' . $VendorProductInfoDetails[$i]['id'] . '" class="d_class" style="width:auto"><a href="javascript:;" class="cross" style="text-decoration:none;" rel="' . $VendorProductInfoDetails[$i]['id'] . '">' . $VendorProductInfoDetails[$i]['product_name'] . '<img align="absmiddle" src="' . Zend_Controller_Front::getInstance()->getBaseUrl() . '/assets/images/delete.png"/></a></span>';
            $product_ids .= $VendorProductInfoDetails[$i]['id'];
            if ($i < count($VendorProductInfoDetails) - 1) {
                $product_ids.=",";
            }
        }

        $VendorProductInfo = $this->em->getRepository('BL\Entity\VendorProductInfo')->findOneBy(array('vendor_id' => (int) $this->_helper->BUtilities->getLoggedInUser()));

        $existingData = array();
        $existingData['products'] = $product_ids;
        $existingData['supplier_name'] = isset($VendorProductInfo) ? $VendorProductInfo->supplier_name : '';
        $existingData['audience'] = $audiences;
        $existingData['other_desc'] = isset($VendorProductInfo) ? $VendorProductInfo->other_desc : '';


        return array($audiences, $products, $existingData);
    }

    /**
     * Function to get vendors existng financial informations
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access private
     * @return Array $existingData
     */
    public function getVendorFinancials() {
        $VendorFinancialInfo = $this->em->getRepository('BL\Entity\VendorFinancialInfo')->findOneBy(array('vendor_id' => (int) $this->_helper->BUtilities->getLoggedInUser()));
        $existingData = array();

        if (sizeof($VendorFinancialInfo)) {
            $applicationProcess = array();

            if ($VendorFinancialInfo->has_account_in_good_standing == 'yes') {
                $applicationProcess[] = '1';
            }
            if ($VendorFinancialInfo->has_closed_financial_statement == 'yes') {
                $applicationProcess[] = '2';
            }
            if ($VendorFinancialInfo->has_chart_of_capital_assets == 'yes') {
                $applicationProcess[] = '3';
            }

            $existingData['application_process'] = $applicationProcess;
            $existingData['full_time_employee_num'] = $VendorFinancialInfo->full_time_employee_num;
            $existingData['years_in_business'] = $VendorFinancialInfo->years_in_business;
            $existingData['business_failure_in_5_years'] = $VendorFinancialInfo->business_failure_in_5_years;
            $existingData['any_person_bankrupt'] = $VendorFinancialInfo->any_person_bankrupt;
            $existingData['government_investigation'] = $VendorFinancialInfo->government_investigation;
            $existingData['contract_terminated_in_last_2_years'] = $VendorFinancialInfo->contract_terminated_in_last_2_years;
            $existingData['litigation_against_the_officers'] = $VendorFinancialInfo->litigation_against_the_officers;
            $existingData['any_collections_by_debt_collection_agency'] = $VendorFinancialInfo->any_collections_by_debt_collection_agency;
            $existingData['additional_explanation'] = $VendorFinancialInfo->additional_explanation;
        }
//        print_r($existingData);
        return $existingData;
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
     * Function to show pending amc review page
     * @author Zea
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access pulic
     * @param vendor id
     * @return void
     */
    public function pendingAmcReviewAction() {
        $this->_helper->BUtilities->setBlankLayout();
        $this->_helper->JSLibs->load_jqui_assets();
        $license_id = $this->_getParam('l_id', 0);
        $vendor_id = (int) $this->_helper->BUtilities->getLoggedInUser();
        //$vendor_products = $this->em->getRepository("BL\Entity\VendorProduct")->findBy(array('license_id' => $license_id));

	/*
	$vendor_products = $this->em->getRepository("BL\Entity\VendorSampleFile")->findBy(array('Vendor' => $vendor_id));
	$this->view->product_size = count($vendor_products);
        $this->view->products = $vendor_products;

	$financila_status = 0;
        $this->view->financial_info = $financila_status;
	*/


	/*
	 * Modified by Anil to reflect the changes as per the
	 * checkboxes of sample received and Advance payment status
	 * To Rollback uncomment the immidiate upper block
	 */
        
        $vendor = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id'=>$vendor_id));

        $license = $this->em->getRepository('BL\Entity\License')->findOneBy(array('id' => $license_id));
        
        $samples = $this->em->getRepository('BL\Entity\ProductDesign')->findBy(array('owner_id'=>$vendor, 'is_approved'=>1) );
        
        $this->view->product_size = count($samples);
        
        if ($license->sample_status > 0) $this->view->product_size = $license->sample_status;
        
	$financila_status = $license->payment_status == 'paid' ? 1 : 0;
        $this->view->financial_info = $financila_status;



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
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
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
        echo '<div style="font-family: DroidSansRegular,\"Segoe UI\",\"Lucida Sans Unicode\",\"Lucida Grande\",sans-serif;font-size: 13px;">';
        echo '<a target="_blank" href="' . $path . '">Click </a>to download the PDF</div> ';
    }

}
