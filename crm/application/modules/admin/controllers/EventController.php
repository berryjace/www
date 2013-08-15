<?php

class Admin_EventController extends Zend_Controller_Action {

    protected $em;

    public function init() {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
    }

    /**
     * Function to show event.
     * @author Zea
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public     
     */
    public function indexAction() {
        $getEventTitle = $this->_getParam('tit');
        $getEventTitle_edit = $this->_getParam('tit_edit');
        $this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_dataTable_assets();
        // $this->view->event_title =  $getEventTitle;
        $this->view->assign('event_title', $getEventTitle);
        $this->view->assign('event_title_edit', $getEventTitle_edit);
    }

    /**
     * Function to provide JSON data to feed data table
     * @author Zea
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetEventsDtAction() {
        $targetPage = $this->_getParam('targetPage', 'all');
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
        $sorting_cols = array('1' => 'e.start_time', '2' => 'e.title', '3' => 'e.location');
        $params['sort_key'] = $sorting_cols[$this->_getParam('iSortCol_0')];
        $params['sort_dir'] = $this->_getParam('sSortDir_0');
        $params['targetPage'] = $targetPage;

        $records = $this->em->getRepository("BL\Entity\Event")->getEvents($params)->getResult();
        $params['show_total'] = true;
        $records_total = $this->em->getRepository("BL\Entity\Event")->getEvents($params);
        //$status_array = $this->_helper->BUtilities->parseYAML(APPLICATION_PATH . '/configs/statuses.yml');
        $this->_helper->BUtilities->setNoLayout();
        /**
         * Datatable doesn't understand json_encode and have a custom json format
         */
        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":';
        $prec = array();
        foreach ($records as $key => $e) {
            //$this->view->BUtils()->doctrine_dump($e);
            $for_user_type['all_vendor'] = $e['total_vendor'];
            $for_user_type['all_client'] = $e['total_client'];
            $for_user_type['all'] = $e['total_vendor'] + $e['total_client'];
            $for_user_type['random'] = $e['total'];
            $for_user_type['c_vrandom'] = $e['total_client'] + $e['total'];
            $for_user_type['v_crandom'] = $e['total_vendor'] + $e['total'];

            $prec[] = array(
                '<input type="checkbox" class="checkbox" name="event_name" rel="' . $e['id'] . '">',
                $e['start_time']->format("m-d-Y,  h:i a"),
                $e['title'],
                $e['location'],
                $for_user_type[$e['for_user_type']] . ' invited',
                '<a class="view" href="javascript:;" rel="' . $e['id'] . '">View & Edit</a>&nbsp;&nbsp; 
                    <a class="deleteuser" href="javascript:;" title="' . $e['title'] . '" rel="' . $e['id'] . '">Delete</a>'
            );
        }                
        $json .= Zend_Json::encode($prec);
        $json .= '}';
        
        echo $json;        
    }

    /**
     * Function to add event.
     * @author Zea
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public    
     */
    public function addAction() {
        $this->_helper->JSLibs->load_jqui_aristo();
        $this->_helper->JSLibs->load_tinymce_assets();
        $this->_helper->JSLibs->load_autosuggest_assets();

        $vendors = $this->em->getRepository("BL\Entity\User")->getVendorNames();
        foreach ($vendors as $vendor) {
            if (trim($vendor['organization_name']) != "") {
                $vendor_list[$vendor['id']] = $vendor['organization_name'];
            }
        }
        $clients = $this->em->getRepository("BL\Entity\User")->getClientNames();
        foreach ($clients as $client) {
            if (trim($client['client_greek_name'] != "")) {
                $client_list[$client['id']] = $client['client_greek_name'];
            }
        }

        $form = new Admin_Form_Events($vendor_list, $client_list);
        $this->view->form = $form;

        $ajax = $this->_getParam('ajax', 0);
        if ($this->getRequest()->isPost() || $this->_request->isXmlHttpRequest()) {
            $all_vendor = false;
            $all_client = false;
            $r_client_list = false;
            $r_vendor_list = false;
            $for_user_type = '';

            $formData = $this->getRequest()->getPost();
            if ($formData['send_seperate_invitation_vendor'] == 1) {
                $all_vendor = true;
            }
            if ($formData['send_seperate_invitation_client'] == 1) {
                $all_client = true;
            }

            if (isset($formData['right_vendor'])) {
                $r_vendor_list = true;
                $right_vendor = @implode(',', $formData['right_vendor']);
                $data = $this->em->getRepository("BL\Entity\User")->getVendorsBatch($right_vendor);
                foreach ($data as $r_v) {
                    $vendor_get_val[$r_v['id']] = $r_v['organization_name'];
                }
                //$vendor_get_val = array_combine($formData['right_vendor'], $formData['right_vendor']);
                //$diff_vendor_list= array_diff($vendor_list,$vendor_get_val);
                $form->right_vendor->setMultiOptions($vendor_get_val);
                //$form->left_vendor->setMultiOptions($vendor_list);
            }
            if (isset($formData['right_client'])) {
                $r_client_list = true;
                $right_client = @implode(',', $formData['right_client']);
                $data = $this->em->getRepository("BL\Entity\User")->getClientsBatch($right_client);
                foreach ($data as $r_c) {
                    $client_get_val[$r_c['id']] = $r_c['client_greek_name'];
                }
                //$client_get_val = array_combine($formData['right_client'], $formData['right_client']);
                //$diff_client_list= array_diff($client_list,$client_get_val);
                $form->right_client->setMultiOptions($client_get_val);
                //$form->left_client->setMultiOptions($diff_client_list);
            }
            if ($formData['event_type'] == "m_d" && $formData['end_date'] == '') {
                $form->markAsError();
                $form->end_date->addError('Please enter event end date');
            }
            if ($all_client == true && $all_vendor == true) {
                $for_user_type = "all";
            } else if (($r_client_list == true && $r_vendor_list == true) || ($r_client_list == true && $all_vendor == false) || ($r_vendor_list == true && $all_client == false)) {
                $for_user_type = "random";
            } else if ($all_vendor == true && $r_client_list == true) {
                $for_user_type = "v_crandom";
            } else if ($all_client == true && $r_vendor_list == true) {
                $for_user_type = "c_vrandom";
            } else if ($all_client == true && $all_vendor == false && $r_vendor_list == false) {
                $for_user_type = "all_client";
            } else if ($all_vendor == true && $all_client == false && $r_client_list == false) {
                $for_user_type = "all_vendor";
            } else {
                $for_user_type = '';
            }
            //print_r($formData);
            //if form is valid
            if ($form->isValid($formData)) {
                if ($formData['event_type'] == "m_d" && $formData['end_date'] == '') {
                    $form->markAsError();
                    $form->end_date->addError('Please enter event end date');
                    return false;
                } else {
                    $startDate = date('Y-m-d H:i:s', strtotime($formData['start_date'] . $formData['start_time']));
                    $startDate = new DateTime($startDate);
                    if ($formData['event_type'] == "m_d") {
                        $endDate = date('Y-m-d H:i:s', strtotime($formData['end_date'] . $formData['end_time']));
                        $endDate = new DateTime($endDate);
                    } else {
                        $endDate = date('Y-m-d H:i:s', strtotime($formData['start_date'] . $formData['end_time']));
                        $endDate = new DateTime($endDate);
                    }

                    $class = 'BL\Entity\Event';
                    $event = new $class();

                    $event->title = $form->getValue('event_title');
                    $event->message = $form->getValue('event_message');
                    $event->location = $form->getValue('event_location');
                    $event->for_user_type = $for_user_type;
                    $event->start_time = $startDate;
                    $event->end_time = $endDate;
                    $event->created_by = $this->em->find("BL\Entity\User", (int) $this->_helper->BUtilities->getLoggedInUser());
                    $this->em->persist($event);
                    $this->em->flush();
                    $batchSize = 20;
                    if (isset($formData['right_client'])) {
                        $counter = 0;
                        foreach ($formData['right_client'] as $c_id) {
                            $class = 'BL\Entity\EventUser';
                            $eventUser = new $class();
                            //$eventUser->Event = $this->em->find("BL\Entity\Event", (int) $event->id);
                            $eventUser->event_id = $event->id;
                            //$eventUser->User = $this->em->find("BL\Entity\User", (int) $c_id);
                            $eventUser->user_id = $c_id;
                            $this->em->persist($eventUser);
                            $counter++;
//                            if($counter == $batchSize){
//                               $this->em->flush();
//                               $this->em->clear();
//                               $counter = 0;
//                            }
                        }
                        $this->em->flush();
                        $this->em->clear();
                    }
                    $batchSize = 20;
                    if (isset($formData['right_vendor'])) {
                        foreach ($formData['right_vendor'] as $v_id) {
                            $counter = 0;
                            $class = 'BL\Entity\EventUser';
                            $eventUser = new $class();
//                            $eventUser->Event = $this->em->find("BL\Entity\Event", (int) $event->id);
//                            $eventUser->User = $this->em->find("BL\Entity\User", (int) $v_id);
                            $eventUser->event_id = $event->id;
                            $eventUser->user_id = $v_id;
                            $this->em->persist($eventUser);
                            $counter++;
//                            if($counter == $batchSize){
//                               $this->em->flush();
//                               $this->em->clear();
//                               $counter = 0;
//                            }
                        }
                        $this->em->flush();
                        $this->em->clear();
                    }
                }
                echo Zend_Json::encode(array('error' => false, 'message' => 'Success', 'event_title' => $form->getValue('event_title')));
            } else {
                echo Zend_Json::encode(array('error' => true, 'message' => $form->getMessages(), 'event_message' => $formData['event_message']));
                //$form->populate($formData);
            }
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout()->disableLayout();
        }
    }

    /**
     * Function to edit a particular event.
     * @author Zea
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     */
    public function editAction() {
        $this->_helper->JSLibs->load_jqui_aristo();
        $this->_helper->JSLibs->load_tinymce_assets();
        $this->_helper->JSLibs->load_autosuggest_assets();
        $eventId = $this->_getParam('id');
        $eventDetails = $this->em->getRepository("BL\Entity\Event")->findOneById($eventId);
        $vendors = $this->em->getRepository("BL\Entity\User")->getVendorNames();
        foreach ($vendors as $vendor) {
            if (trim($vendor['organization_name']) != "") {
                $vendor_list[$vendor['id']] = $vendor['organization_name'];
            }
        }
        $clients = $this->em->getRepository("BL\Entity\User")->getClientNames();
        foreach ($clients as $client) {
            if (trim($client['client_greek_name'] != "")) {
                $client_list[$client['id']] = $client['client_greek_name'];
            }
        }

        $form = new Admin_Form_Events($vendor_list, $client_list);
        $this->view->form = $form;
        $this->view->id = $eventId;

        $ajax = $this->_getParam('ajax', 0);
        if ($this->getRequest()->isPost() || $this->_request->isXmlHttpRequest()) {
            $all_vendor = false;
            $all_client = false;
            $r_client_list = false;
            $r_vendor_list = false;
            $for_user_type = '';

            $formData = $this->getRequest()->getPost();
            if ($formData['send_seperate_invitation_vendor'] == 1) {
                $all_vendor = true;
            }
            if ($formData['send_seperate_invitation_client'] == 1) {
                $all_client = true;
            }

            if (isset($formData['right_vendor'])) {
                $r_vendor_list = true;
                $right_vendor = @implode(',', $formData['right_vendor']);
                $data = $this->em->getRepository("BL\Entity\User")->getVendorsBatch($right_vendor);
                foreach ($data as $r_v) {
                    $vendor_get_val[$r_v['id']] = $r_v['organization_name'];
                }
                //$vendor_get_val = array_combine($formData['right_vendor'], $formData['right_vendor']);
                //$diff_vendor_list= array_diff($vendor_list,$vendor_get_val);
                $form->right_vendor->setMultiOptions($vendor_get_val);
                //$form->left_vendor->setMultiOptions($vendor_list);
            }
            if (isset($formData['right_client'])) {
                $r_client_list = true;
                $right_client = @implode(',', $formData['right_client']);
                $data = $this->em->getRepository("BL\Entity\User")->getClientsBatch($right_client);
                foreach ($data as $r_c) {
                    $client_get_val[$r_c['id']] = $r_c['client_greek_name'];
                }
                //$client_get_val = array_combine($formData['right_client'], $formData['right_client']);
                //$diff_client_list= array_diff($client_list,$client_get_val);
                $form->right_client->setMultiOptions($client_get_val);
                //$form->left_client->setMultiOptions($diff_client_list);
            }
            if ($formData['event_type'] == "m_d" && $formData['end_date'] == '') {
                $form->markAsError();
                $form->end_date->addError('Please enter event end date');
            }
            if ($all_client == true && $all_vendor == true) {
                $for_user_type = "all";
            } else if (($r_client_list == true && $r_vendor_list == true) || ($r_client_list == true && $all_vendor == false) || ($r_vendor_list == true && $all_client == false)) {
                $for_user_type = "random";
            } else if ($all_vendor == true && $r_client_list == true) {
                $for_user_type = "v_crandom";
            } else if ($all_client == true && $r_vendor_list == true) {
                $for_user_type = "c_vrandom";
            } else if ($all_client == true && $all_vendor == false && $r_vendor_list == false) {
                $for_user_type = "all_client";
            } else if ($all_vendor == true && $all_client == false && $r_client_list == false) {
                $for_user_type = "all_vendor";
            } else {
                $for_user_type = '';
            }
            //print_r($formData);
            //if form is valid
            if ($form->isValid($formData)) {
                if ($formData['event_type'] == "m_d" && $formData['end_date'] == '') {
                    $form->markAsError();
                    $form->end_date->addError('Please enter event end date');
                    return false;
                } else {
                    $startDate = date('Y-m-d H:i:s', strtotime($formData['start_date'] . $formData['start_time']));
                    $startDate = new DateTime($startDate);
                    if ($formData['event_type'] == "m_d") {
                        $endDate = date('Y-m-d H:i:s', strtotime($formData['end_date'] . $formData['end_time']));
                        $endDate = new DateTime($endDate);
                    } else {
                        $endDate = date('Y-m-d H:i:s', strtotime($formData['start_date'] . $formData['end_time']));
                        $endDate = new DateTime($endDate);
                    }

                    //$eventDetails->id = $eventId;
                    $eventDetails->title = $form->getValue('event_title');
                    $eventDetails->message = $form->getValue('event_message');
                    $eventDetails->location = $form->getValue('event_location');
                    $eventDetails->for_user_type = $for_user_type;
                    $eventDetails->start_time = $startDate;
                    $eventDetails->end_time = $endDate;

                    $this->em->persist($eventDetails);
                    $this->em->flush();

                    $event_user = $this->em->getRepository("BL\Entity\Event")->forUserType($eventId);
                    $eventType = $event_user[0]['for_user_type'];
                    if ($eventType == 'random' || $eventType == 'v_crandor' || $eventType == 'c_vrandom') {
                        $this->em->getRepository("BL\Entity\Event")->deleteEventUser($eventId);
                    }
                    $batchSize = 20;
                    if (isset($formData['right_client'])) {
                        $counter = 0;
                        foreach ($formData['right_client'] as $c_id) {
                            $class = 'BL\Entity\EventUser';
                            $eventUser = new $class();
                            //$eventUser->Event = $this->em->find("BL\Entity\Event", (int) $event->id);
                            $eventUser->event_id = $eventDetails->id;
                            //$eventUser->User = $this->em->find("BL\Entity\User", (int) $c_id);
                            $eventUser->user_id = $c_id;
                            $this->em->persist($eventUser);
                            $counter++;
//                            if($counter == $batchSize){
//                               $this->em->flush();
//                               $this->em->clear();
//                               $counter = 0;
//                            }
                        }
                        $this->em->flush();
                        $this->em->clear();
                    }
                    $batchSize = 20;
                    if (isset($formData['right_vendor'])) {
                        foreach ($formData['right_vendor'] as $v_id) {
                            $counter = 0;
                            $class = 'BL\Entity\EventUser';
                            $eventUser = new $class();
//                            $eventUser->Event = $this->em->find("BL\Entity\Event", (int) $event->id);
//                            $eventUser->User = $this->em->find("BL\Entity\User", (int) $v_id);
                            $eventUser->event_id = $eventDetails->id;
                            $eventUser->user_id = $v_id;
                            $this->em->persist($eventUser);
                            $counter++;
//                            if($counter == $batchSize){
//                               $this->em->flush();
//                               $this->em->clear();
//                               $counter = 0;
//                            }
                        }
                        $this->em->flush();
                        $this->em->clear();
                    }
                }
                echo Zend_Json::encode(array('error' => false, 'message' => 'Success', 'event_title' => $form->getValue('event_title')));
            } else {
                echo Zend_Json::encode(array('error' => true, 'message' => $form->getMessages(), 'event_message' => $formData['event_message']));
                //$form->populate($formData);
            }
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout()->disableLayout();
        } else {

            //$this->view->BUtils()->doctrine_dump($eventDetails->start_time);
            //print_r($eventDetails->for_user_type);

            $dateDiff = $eventDetails->end_time->diff($eventDetails->start_time);

            $form->event_title->setValue($eventDetails->title);
            $form->event_location->setValue($eventDetails->location);
            $form->event_message->setValue($eventDetails->message);

            if ($dateDiff->d == 0) {
                $form->event_type->setMultiOptions(array('s_d' => 'Single Day', 'm_d' => 'Multiple Day'));
                $form->start_date->setValue($eventDetails->start_time->format("m/d/Y"));
                $form->start_time->setValue($eventDetails->start_time->format("h:i a"));
                $form->end_time->setValue($eventDetails->end_time->format("h:i a"));
            } else {
                $form->event_type->setMultiOptions(array('m_d' => 'Multiple Day', 's_d' => 'Single Day'));
                $form->start_date->setValue($eventDetails->start_time->format("m/d/Y"));
                $form->start_time->setValue($eventDetails->start_time->format("h:i a"));
                $form->end_date->setValue($eventDetails->end_time->format("m/d/Y"));
                $form->end_time->setValue($eventDetails->end_time->format("h:i a"));
            }

            $eventUsers = $this->em->getRepository("BL\Entity\EventUser")->getEventUsersClientVendor($eventId);

            if ($eventDetails->for_user_type == 'all') {
                $form->send_seperate_invitation_client->setChecked(true);
                $form->send_seperate_invitation_vendor->setChecked(true);
            }
            if ($eventDetails->for_user_type == 'all_vendor') {
                $form->send_seperate_invitation_vendor->setChecked(true);
            }
            if ($eventDetails->for_user_type == 'all_client') {
                $form->send_seperate_invitation_client->setChecked(true);
            }
            if ($eventDetails->for_user_type == 'v_crandom') {
                $form->send_seperate_invitation_vendor->setChecked(true);
                //$this->view->BUtils()->doctrine_dump($eventUsers);
                foreach ($eventUsers as $user) {
                    $c[$user['user_id']] = $user['organization_name'];
                }
                $form->right_client->setMultiOptions($c);
                $diff_client_list = array_diff($client_list, $c);
                $form->left_client->setMultiOptions($diff_client_list);
            }
            if ($eventDetails->for_user_type == 'c_vrandom') {
                $form->send_seperate_invitation_client->setChecked(true);
                //$this->view->BUtils()->doctrine_dump($eventUsers);
                foreach ($eventUsers as $user) {
                    $v[$user['user_id']] = $user['organization_name'];
                }
                $form->right_vendor->setMultiOptions($v);
                $diff_vendor_list = array_diff($vendor_list, $v);
                $form->left_vendor->setMultiOptions($diff_vendor_list);
            }
            if ($eventDetails->for_user_type == 'random') {
                foreach ($eventUsers as $user) {
                    if ($user['account_type'] == ACC_TYPE_VENDOR) {
                        $vr[$user['user_id']] = $user['organization_name'];
                    } else {
                        $cr[$user['user_id']] = $user['organization_name'];
                    }
                }
                if (isset($vr)) {
                    $form->right_vendor->setMultiOptions($vr);
                    $diff_vendor_lst = array_diff($vendor_list, $vr);
                    $form->left_vendor->setMultiOptions($diff_vendor_lst);
                }
                if (isset($cr)) {
                    $form->right_client->setMultiOptions($cr);
                    $diff_client_lst = array_diff($client_list, $cr);
                    $form->left_client->setMultiOptions($diff_client_lst);
                }
            }
        }
    }

    public function deleteAction() {
        // action body
    }

    /**
     * Function to get All Vendors for Autocomplete.
     * @author Zea
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return string
     */
    public function getvendorsAction() {
        $term = $this->_getParam('term');
        $data = $this->em->getRepository("BL\Entity\User")->getAutocompleteVendors($term);
        echo Zend_Json::encode($data);
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
    }

    /**
     * Function to get All Clients for Autocomplete.
     * @author zea
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return string
     */
    public function getclientsAction() {
        $term = $this->_getParam('term');
        $data = $this->em->getRepository("BL\Entity\User")->getAutocompleteClients($term);
        echo Zend_Json::encode($data);
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
    }

    /**
     * Function to get All licensed Vendor with particular client in axaj call.
     * @author zea
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return string
     */
    public function getlicensedvendorAction() {
        $orgName = $this->_getParam('orgName');
        $vendor = $this->em->getRepository("BL\Entity\User")->findBy(array('organization_name' => $orgName));
        $id = $vendor[0]->id;
        $vendor_list = array();
        $licensedVendors = $this->em->getRepository("BL\Entity\License")->getActiveVendors($id);
        //$this->view->BUtils()->doctrine_dump($licensedVendors);        
        foreach ($licensedVendors as $licensedVendor) {
            $vendor_list[$licensedVendor->vendor_id->organization_name] = $licensedVendor->vendor_id->id;
        }
        echo Zend_Json::encode($vendor_list);
        //print_r($vendor_list);
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
    }

    /**
     * Function to get All licensed Client with particular client in axaj call.
     * @author Zea
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return string
     */
    public function getlicensedclientAction() {
        $orgName = $this->_getParam('orgName');
        $client = $this->em->getRepository("BL\Entity\User")->findBy(array('organization_name' => $orgName));
        $id = $client[0]->id;
        $licensedClients = $this->em->getRepository("BL\Entity\License")->getActiveClients($id);
        $clientList = array();
        //$this->view->BUtils()->doctrine_dump($licensedClients);
        foreach ($licensedClients as $licensedClient) {
            $clientList[$licensedClient->client_id->organization_name] = $licensedClient->client_id->id;
        }
        echo Zend_Json::encode($clientList);
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
    }

    public function deleteEventAction() {
        $getEventId = $this->_getParam('eventId');
        $eventIds = explode(",", $getEventId);
        $this->_helper->BUtilities->setNoLayout();

        foreach ($eventIds as $eventId) {
            $event = $this->em->getRepository("BL\Entity\Event")->forUserType($eventId);
            $eventType = $event[0]['for_user_type'];
            if ($eventType == 'random' || $eventType == 'v_crandor' || $eventType == 'c_vrandom') {
                $this->em->getRepository("BL\Entity\Event")->deleteEventUser($eventId);
            }
            $event = $this->em->find("BL\Entity\Event", (int) $eventId);
            $this->em->remove($event);
        }
        $this->em->flush();
        $this->em->clear();

        return true;
    }

    public function formatdate($param) {
        return date("m-d-Y,  h:i a", strtotime($param));
    }

}

