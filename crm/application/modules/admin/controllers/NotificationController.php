<?php

class Admin_NotificationController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
    }

    /**
     * Function to show admin notifications
     * @author Masud
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     * @return void
     */
    public function indexAction() {
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets'));
    }

    /**
     * Function to get admin notifications using ajax
     * @author Masud
     * @copyright 0.1
     * @copyright Blueliner Marketing
     * @access public
     * @return void
     */
    public function ajaxGetNotificationsDtAction() {
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
        $sorting_cols = array('1' => 'n.time', '2' => 'n.title', '3' => 'n.message');
        $params['sort_key'] = $sorting_cols[$this->_getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->_getParam('sSortDir_0');
        $params['targetPage'] = $targetPage;

        $records = $this->em->getRepository("BL\Entity\Notification")->getNotifications($params)->getResult();
        $params['show_total'] = true;
        $records_total = $this->em->getRepository("BL\Entity\Notification")->getNotifications($params);
        $this->_helper->BUtilities->setNoLayout();

        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":';
        $prec = array();

        foreach ($records as $key => $n) {
            $replace = array("<br>", "<br />", "<br/>");
            $message = str_replace($replace, " ", $n['message']);
            $message = strip_tags($message);

            if (strlen($message) > 50) {
                $message = substr($message, 0, 50) . "...";
            } else {
                $message = strip_tags($message);
            }

            $for_user_type['all_vendor'] = $n['total_vendor'];
            $for_user_type['all_client'] = $n['total_client'];
            $for_user_type['all'] = $n['total_vendor'] + $n['total_client'];
            $for_user_type['random'] = $n['total'];
            $for_user_type['c_vrandom'] = $n['total_client'] + $n['total'];
            $for_user_type['v_crandom'] = $n['total_vendor'] + $n['total'];

            $prec[] = array(
                '<input type="checkbox" class="checkbox" name="notification_name" rel="' . $n['id'] . '">',
                $n['time']->format('M d, Y H:i A'),
                $n['title'],
                $message,
                $for_user_type[$n['for_user_type']] . ' notified',
                '<a class="view_link" href="javascript:;" rel="' . $n['id'] . '">View & Edit</a>&nbsp;&nbsp;
                    <a class="deleteuser" href="javascript:;" title="' . $n['title'] . '" rel="' . $n['id'] . '">Delete</a>'
            );
        }
        $json .= Zend_Json::encode($prec);
        $json .= '}';
        
        echo $json;
    }

    /**
     * Function for add notification
     * @author Masud
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     * @return void
     */
    public function addAction() {
        $this->_helper->JSLibs->load_jqui_aristo();
        $this->_helper->JSLibs->load_tinymce_assets();
        $this->_helper->JSLibs->load_autosuggest_assets();
        
        
 //'label'=>'Lost', 'options'=>array()
        
        error_log("\naddAction()", 3, "./errorLog.log");
        
        $vendor_groups = array();
        $repos = $this->em->getRepository("BL\Entity\User");
        $vendors = $repos->getVendorNames();
        
        $count = 0;
        
        foreach ($vendors as $vendor) {
        	$count ++;
            if (trim($vendor['organization_name']) != "") {
                $vendor_list[$vendor['id']] = $vendor['organization_name'];
            		$status = strtolower($vendor['user_status']);
                
               		if ($status == "current"){
            			$vendor_groups['Current'][$vendor['id']] = $vendor['organization_name'];
            		}
            		else if ($status == "cancelled"){
            			$vendor_groups['Cancelled'][$vendor['id']] = $vendor['organization_name'];
            		}
            		else if ($status == "potential"){
            			$vendor_groups['Potential'][$vendor['id']] = $vendor['organization_name'];
            		}
            		else if ($status == "one-time"){
            			$vendor_groups['One-Time'][$vendor['id']] = $vendor['organization_name'];
            		}
            		else if ($status == "non-producing"){
            			$vendor_groups['Non-Producing'][$vendor['id']] = $vendor['organization_name'];
            		}
            		else if ($status == "suspected"){
            			$vendor_groups['Suspected'][$vendor['id']] = $vendor['organization_name'];
            		}
            		else if ($status == "illegal"){
            			$vendor_groups['Illegal'][$vendor['id']] = $vendor['organization_name'];
            		}
            		else if ($status == "out of business"){
            			$vendor_groups['Out of Business'][$vendor['id']] = $vendor['organization_name'];
            		}
            		else if ($status == "lost"){
            			$vendor_groups['Lost'][$vendor['id']] = $vendor['organization_name'];
            		}
            		else if ($status == "pending"){
            			$vendor_groups['Pending'][$vendor['id']] = $vendor['organization_name'];
            		}
            		else if ($status == "registered"){
            			$vendor_groups['Registered'][$vendor['id']] = $vendor['organization_name'];
            		}
             //*/
            		
            }
        }
        
// 		error_log("\ncount: " . $count, 3, "./errorLog.log");        
        
//         $vendor_groups= array("option1"=>array("a", "b", "c"), "option2"=>array("a", "b", "c"));

        $client_groups = array();
        $clients = $this->em->getRepository("BL\Entity\User")->getClientNames();
    	foreach ($clients as $client) {
            if (trim($client['client_greek_name'] != "")) {
                $client_list[$client['id']] = $client['client_greek_name'];
                $status = strtolower($client["user_status"]);
                
                if ($status == "cancelled"){
                	$client_groups['Cancelled'][$client['id']] = $client['client_greek_name'];
                } else if ($status == "current"){
                	$client_groups['Current'][$client['id']] = $client['client_greek_name'];
                } else if ($status == "pending"){
                	$client_groups['Pending'][$client['id']] = $client['client_greek_name'];
                } else if ($status == "potential"){
                	$client_groups['Potential'][$client['id']] = $client['client_greek_name'];
                }
            }
        }

        $form = new Admin_Form_Notification($vendor_groups, $client_groups); //$vendor_list
        $this->view->form = $form;
        $this->view->vendor_groups = $vendor_groups;
        $this->view->client_groups = $client_groups;

        $ajax = $this->_getParam('ajax', 0);
        
//         error_log("\nbefore post", 3, "./errorLog.log");

        if ($this->getRequest()->isPost() || $this->_request->isXmlHttpRequest()) {
        	error_log("\nis post", 3, "./errorLog.log");
            $all_vendor = false;
            $all_client = false;
            $r_client_list = false;
            $r_vendor_list = false;
            $for_user_type = '';

            $formData = $this->getRequest()->getPost();

            if ($formData['send_seperate_invitation_vendor'] == '1') {
                $all_vendor = true;
            }
            if ($formData['send_seperate_invitation_client'] == '1') {
                $all_client = true;
            }

            if (isset($formData['right_vendor'])) {
                $r_vendor_list = true;
                $right_vendor = @implode(',', $formData['right_vendor']);
                $data = $this->em->getRepository("BL\Entity\User")->getVendorsBatch($right_vendor);
                foreach ($data as $r_v) {
                    $vendor_get_val[$r_v['id']] = $r_v['organization_name'];
                }
                $form->right_vendor->setMultiOptions($vendor_get_val);
            }
            if (isset($formData['right_client'])) {
                $r_client_list = true;
                $right_client = @implode(',', $formData['right_client']);
                $data = $this->em->getRepository("BL\Entity\User")->getClientsBatch($right_client);
                foreach ($data as $r_c) {
                    $client_get_val[$r_c['id']] = $r_c['client_greek_name'];
                }
                $form->right_client->setMultiOptions($client_get_val);
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

            //if form is valid
            if ($form->isValid($formData)) {
                //print_r($formData);
                $myDate = new DateTime();
                $notification_via = '';

                if ($formData['email_notification'] == 1) {
                    $notification_via = 'email';
                }

                if ($formData['site_notification'] == 1) {
                    $notification_via = 'site_notification';
                }

                if ($formData['email_notification'] == 1 && $formData['site_notification'] == 1) {
                    $notification_via = 'email_notification';
                }

                //print_r($notification_via);
                $class = 'BL\Entity\Notification';
                $notification = new $class;
                
                $notification->title = htmlentities($form->getValue('notification_title'));
//                 $notification->message = htmlentities($form->getValue('notification_message'));
                $notification->message = $form->getValue('notification_message');
                $notification->send_via = $notification_via;
                $notification->for_user_type = $for_user_type;
                $notification->time = $myDate;
                $notification->created_by = $this->em->find("BL\Entity\User", (int) $this->_helper->BUtilities->getLoggedInUser());

                //print_r($notification);              
                $this->em->persist($notification);
                $this->em->flush();

                $batchSize = 20;
                if (isset($formData['right_client'])) {
                    $counter = 0;
                    foreach ($formData['right_client'] as $c_id) {
                        $class = 'BL\Entity\NotificationUser';
                        $notificationUser = new $class();
                        $notificationUser->notification_id = $this->em->find('BL\Entity\Notification', $notification->id);
                        $notificationUser->user_id = $this->em->find('BL\Entity\User', $c_id);
                        $this->em->persist($notificationUser);
//                        $counter++;
//                        if ($counter == $batchSize) {
//                            $this->em->flush();
//                            $this->em->clear();
//                            $counter = 0;
//                        }
                    }
                    $this->em->flush();
                    $this->em->clear();
                }

           		$users = $this->em->getRepository('BL\Entity\User');
           		
                $batchSize = 20;
                $batchItem = 0;
                if (isset($formData['right_vendor'])) {
           			
                    $counter = 0;
                    foreach ($formData['right_vendor'] as $v_id) {
                    	error_log("\nv_id: " . $v_id, 3, "./errorLog.log");
                        $class = 'BL\Entity\NotificationUser';
                        
                        $notificationUser = new $class();
                        $notificationUser->notification_id = $this->em->find('BL\Entity\Notification', $notification->id);
                    	$notificationUser->user_id = $users->findOneBy(array('id'=>$v_id));
                        $this->em->persist($notificationUser);
//                        $counter++;
//                        if ($counter == $batchSize) {
//                            $this->em->flush();
//                            $this->em->clear();
//                            $counter = 0;
//                        }
						
						$batchItem ++;
						if ($batchItem >= $batchSize) {
							$this->em->flush();
							$this->em->clear();
							$batchItem = 0;
						}
                    	
                    }
           		
                    $this->em->flush();
                    $this->em->clear();
                }

                /**
                 * Client requested to add a correspondance entry against notifications. So here it goes
                 */
                $this->addCorrespondence($formData, $for_user_type, $notification_via);
                $this->sendMail($formData, $notification_via, $for_user_type);
                
                error_log("\nsuccess!", 3, "./errorLog.log");
                
                echo Zend_Json::encode(array('error' => false, 'message' => 'Success'));
                //$this->_redirect('admin/notification/index');
            } else {
                echo Zend_Json::encode(array('error' => true, 'message' => $form->getMessages()));
                //$form->populate($formData);
            }
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout()->disableLayout();
        }
    }

    /**
     * Function to edit admin notification
     * @author Masud
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     * @return void
     */
    public function editAction() {
        if ($this->_getParam('id') == NULL || $this->_getParam('id') == '') {
            $this->_redirect('admin/notification');
        }

        $this->_helper->JSLibs->load_jqui_aristo();
        $this->_helper->JSLibs->load_tinymce_assets();
        $this->_helper->JSLibs->load_autosuggest_assets();

        $existing_vendor = array();
        $existing_client = array();
        $vendorArr = array();
        $clientArr = array();
        $notification = $this->em->getRepository("BL\Entity\Notification")->findOneBy(array('id' => $this->_getParam('id')));

        if ($this->getRequest()->isPost() || $this->_request->isXmlHttpRequest()) {
            $form = new Admin_Form_Notification($existing_vendor, $existing_client, $vendorArr, $clientArr, 'edit');
            $this->view->form = $form;
            $this->view->assign('id', $this->_getParam('id'));

            $formData = $this->getRequest()->getPost();
            $all_vendor = false;
            $all_client = false;
            $r_client_list = false;
            $r_vendor_list = false;
            $for_user_type = '';

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
                $form->right_vendor->setMultiOptions($vendor_get_val);
            }

            if (isset($formData['right_client'])) {
                $r_client_list = true;
                $right_client = @implode(',', $formData['right_client']);
                $data = $this->em->getRepository("BL\Entity\User")->getClientsBatch($right_client);
                foreach ($data as $r_c) {
                    $client_get_val[$r_c['id']] = $r_c['client_greek_name'];
                }
                $form->right_client->setMultiOptions($client_get_val);
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

            if ($form->isValid($formData)) {
                if ($form->getValue("send_and_update") == "1") {
                    $this->em->getRepository('BL\Entity\NotificationUser')->deleteNotificationUser((int) $notification->id);   // For deleting existing notified user

                    $myDate = new DateTime();
                    $notification_via = '';

                    if ($formData['email_notification'] == 1) {
                        $notification_via = 'email';
                    }

                    if ($formData['site_notification'] == 1) {
                        $notification_via = 'site_notification';
                    }

                    if ($formData['email_notification'] == 1 && $formData['site_notification'] == 1) {
                        $notification_via = 'email_notification';
                    }

                    $notification->title = $form->getValue('notification_title');
                    $notification->message = $form->getValue('notification_message');
                    $notification->send_via = $notification_via;
                    $notification->for_user_type = $for_user_type;

                    $this->em->persist($notification);
                    $this->em->flush();

                    if (isset($formData['right_client'])) {
                        foreach ($formData['right_client'] as $c_id) {
                            $class = 'BL\Entity\NotificationUser';
                            $notificationUser = new $class();
                            $notificationUser->notification_id = $this->em->find('BL\Entity\Notification', $notification->id);
                            $notificationUser->user_id = $this->em->find('BL\Entity\User', $c_id);
                            $this->em->persist($notificationUser);
                        }
                        $this->em->flush();
                        $this->em->clear();
                    }

                    if (isset($formData['right_vendor'])) {
                        foreach ($formData['right_vendor'] as $v_id) {
                            $class = 'BL\Entity\NotificationUser';
                            $notificationUser = new $class();
                            $notificationUser->notification_id = $this->em->find('BL\Entity\Notification', $notification->id);
                            $notificationUser->user_id = $this->em->find('BL\Entity\User', $v_id);
                            $this->em->persist($notificationUser);
                        }
                        $this->em->flush();
                        $this->em->clear();
                    }

                    /**
                     * Client requested to add a correspondance entry against notifications. So here it goes
                     */
                    $this->addCorrespondence($formData, $for_user_type, $notification_via);
                    $this->sendMail($formData, $notification_via, $for_user_type);     //send email using mailchimp API                    
                } else if ($form->getValue("only_update") == "1") {
                    $notification->title = $form->getValue("notification_title");
                    $notification->message = $form->getValue("notification_message");
                    $this->em->persist($notification);
                    $this->em->flush();
                }
                echo Zend_Json::encode(array('error' => false, 'message' => 'Success'));
            } else {
                echo Zend_Json::encode(array('error' => true, 'message' => $form->getMessages()));
            }
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout()->disableLayout();
        } else {

        	$vendor_groups = array();
        	
            $vendors = $this->em->getRepository("BL\Entity\User")->getVendorNames();
            foreach ($vendors as $vendor) {
                if (trim($vendor['organization_name']) != "") {
                    $vendor_list[$vendor['id']] = $vendor['organization_name'];
                    
                    $status = strtolower($vendor['user_status']);
                     
                    if ($status == "current"){
                    	$vendor_groups['Current'][$vendor['id']] = $vendor['organization_name'];
                    }
                    else if ($status == "cancelled"){
                    	$vendor_groups['Cancelled'][$vendor['id']] = $vendor['organization_name'];
                    }
                    else if ($status == "potential"){
                    	$vendor_groups['Potential'][$vendor['id']] = $vendor['organization_name'];
                    }
                    else if ($status == "one-time"){
                    	$vendor_groups['One-Time'][$vendor['id']] = $vendor['organization_name'];
                    }
                    else if ($status == "non-producing"){
                    	$vendor_groups['Non-Producing'][$vendor['id']] = $vendor['organization_name'];
                    }
                    else if ($status == "suspected"){
                    	$vendor_groups['Suspected'][$vendor['id']] = $vendor['organization_name'];
                    }
                    else if ($status == "illegal"){
                    	$vendor_groups['Illegal'][$vendor['id']] = $vendor['organization_name'];
                    }
                    else if ($status == "out of business"){
                    	$vendor_groups['Out of Business'][$vendor['id']] = $vendor['organization_name'];
                    }
                    else if ($status == "lost"){
                    	$vendor_groups['Lost'][$vendor['id']] = $vendor['organization_name'];
                    }
                    else if ($status == "pending"){
                    	$vendor_groups['Pending'][$vendor['id']] = $vendor['organization_name'];
                    }
                    else if ($status == "registered"){
                    	$vendor_groups['Registered'][$vendor['id']] = $vendor['organization_name'];
                    }
                }
            }
            
            $clients = $this->em->getRepository("BL\Entity\User")->getClientNames();
            foreach ($clients as $client) {
                if (trim($client['client_greek_name'] != "")) {
                    $client_list[$client['id']] = $client['client_greek_name'];
                }
            }

            $existing_vendor = $vendor_list;
            $existing_client = $client_list;

            $email_notification = '0';
            $site_notification = '0';
            $send_seperate_invitation_vendor = '0';
            $send_seperate_invitation_client = '0';


            if (trim($notification->send_via) == 'email') {
                $email_notification = '1';
            }
            if (trim($notification->send_via) == 'site_notification') {
                $site_notification = '1';
            }
            if (trim($notification->send_via) == 'email_notification'){
            	$email_notification = '1';
            	$site_notification = '1';
            }

            if (trim($notification->for_user_type == 'all')) {
                $send_seperate_invitation_client = '1';
                $send_seperate_invitation_vendor = '1';
                $existing_vendor = $vendor_list;
                $existing_client = $client_list;
            } else if ((trim($notification->for_user_type == 'c_vrandom')) || (trim($notification->for_user_type == 'all_client'))) {
                $send_seperate_invitation_client = '1';
                list ($clientArr, $vendorArr) = $this->getNotificationUsers((int) $notification->id);
                $existing_client = $client_list;
                $existing_vendor = array_diff_key($vendor_list, $vendorArr);
            } else if ((trim($notification->for_user_type == 'v_crandom')) || (trim($notification->for_user_type == 'all_vendor'))) {
                $send_seperate_invitation_vendor = '1';
                list ($clientArr, $vendorArr) = $this->getNotificationUsers((int) $notification->id);
                $existing_vendor = $vendor_list;
                $existing_client = array_diff_key($client_list, $clientArr);
            } else if (trim($notification->for_user_type == 'random')) {
                list ($clientArr, $vendorArr) = $this->getNotificationUsers((int) $notification->id);
                $existing_vendor = array_diff_key($vendor_list, $vendorArr);
                $existing_client = array_diff_key($client_list, $clientArr);
            }

            $existing_data = array(
                'notification_title' => $notification->title,
                'notification_message' => $notification->message,
                'email_notification' => $email_notification,
                'site_notification' => $site_notification,
                'send_seperate_invitation_vendor' => $send_seperate_invitation_vendor,
                'send_seperate_invitation_client' => $send_seperate_invitation_client,
            );
            $form = new Admin_Form_Notification($existing_vendor, $existing_client, $vendorArr, $clientArr, 'edit');
            $form->populate($existing_data);
            $this->view->form = $form;
            $this->view->assign('id', $this->_getParam('id'));
            $this->view->vendor_groups = $vendor_groups;
        }
    }

    /**
     * Function to delete notification
     * @author Masud
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     * @return void
     */
    public function deleteAction() {

        $notificationIds = $this->_getParam('notificationId');
        $notificationIdsArr = explode(",", $notificationIds);
        $this->_helper->BUtilities->setNoLayout();

        foreach ($notificationIdsArr as $notificationId) {
            $data = $this->em->getRepository("BL\Entity\Notification")->getNotification($notificationId);
            if ($data['0']->for_user_type == 'random' || $data['0']->for_user_type == 'v_crandom' || $data['0']->for_user_type == 'c_vrandom') {
                $this->em->getRepository("BL\Entity\NotificationUser")->deleteNotificationUser($notificationId);
            }
            $this->em->getRepository("BL\Entity\Notification")->deleteNotification($notificationId);
        }
        $this->em->flush();
        $this->em->clear();
    }

    /**
     * Function send email notification
     * @author Masud
     * @version 0.1
     * @copyright Blueliner Marketing
     * @param array $frmField
     * @param string $email_notification
     * @param string $for_user_type
     * @access private
     * @return void
     */
    private function sendMail($frmField, $email_notification, $for_user_type) {
		/*
         echo $email_notification;
          echo $for_user_type;
          print_r($frmField); //*/

        if ((isset($frmField['right_vendor'])) && ($email_notification == "email" || $email_notification == "email_notification")) {
            $data = Array();
            $emails = Array();
            foreach ($frmField['right_vendor'] as $key => $user_id) {
                $user = $this->em->getRepository("BL\Entity\User")->findBy(array('id' => $user_id));
                if ($user['0']->email != NULL) {
                    //$emails[$key] = $user['0']->email;
                    
                    $email = array(
                    	'to' => $user['0']->email,
                    	'to_name' => $user['0']->username,
                    	'from' => 'Licensing@greeklicensing.com',
                    	'from_name' => 'Greek Licensing',
                    	'subject' => $frmField['notification_title'],
                    	'body' => $frmField['notification_message']
                    );
                    
                    $this->_helper->BUtilities->send_mail($email);
                }
                //echo $user_id . "<br />";
            }
            //print_r($emails);
            //echo "Vendors";
          //  $this->MailChimp($emails, $frmField['notification_title'], $frmField['notification_message']);
        }

        if ((isset($frmField['right_client'])) && ($email_notification == "email" || $email_notification == "email_notification")) {

            $data = Array();
            $emails = Array();
            foreach ($frmField['right_client'] as $key => $user_id) {
                $user = $this->em->getRepository("BL\Entity\User")->findBy(array('id' => $user_id));
                if ($user['0']->email != NULL) {
                 //   $emails[$key] = $user['0']->email;
                    
                    $email = array(
                    	'to' => $user['0']->email,
                    	'to_name' => $user['0']->username,
                    	'from' => 'Licensing@greeklicensing.com',
                    	'from_name' => 'Greek Licensing',
                    	'subject' => $frmField['notification_title'],
                    	'body' => $frmField['notification_message']
                    );
                    
                    $this->_helper->BUtilities->send_mail($email);
                }
                //echo $user_id . "<br />";
            }
            //print_r($emails);
            //echo "Clients";
           // $this->MailChimp($emails, $frmField['notification_title'], $frmField['notification_message']);
        }


        $clientGroup = $this->em->getRepository("BL\Entity\Role")->findBy(array('role_name' => 'client'));
        $vendorGroup = $this->em->getRepository("BL\Entity\Role")->findBy(array('role_name' => 'vendor'));
        //print_r($clientGroup);
        //print_r($vendorGroup);

        if ($for_user_type == "c_vrandom" || $for_user_type == "all_client" || $for_user_type == "all") {
            $clientEmails = array();
            $allClients = $this->em->getRepository("BL\Entity\User")->findBy(array('account_type' => $clientGroup['0']->id));
            foreach ($allClients as $user) {
                //$clientEmails[$user->id] = $user->email;
                    
                    $email = array(
                    	'to' => $user['0']->email,
                    	'to_name' => $user['0']->username,
                    	'from' => 'Licensing@greeklicensing.com',
                    	'from_name' => 'Greek Licensing',
                    	'subject' => $frmField['notification_title'],
                    	'body' => $frmField['notification_message']
                    );
                    
                    $this->_helper->BUtilities->send_mail($email);
            }
            //print_r($clientEmails);
            //echo "All clients";
           // $this->MailChimp($clientEmails, $frmField['notification_title'], $frmField['notification_message']);
        }

        if ($for_user_type == "v_crandom" || $for_user_type == "all_vendor" || $for_user_type == "all") {
            $vendorEmails = array();
            $allVendors = $this->em->getRepository("BL\Entity\User")->findBy(array('account_type' => $vendorGroup['0']->id));
            foreach ($allVendors as $key => $user) {
            //    $vendorEmails[$key] = $user->email;
                    
                    $email = array(
                    	'to' => $user['0']->email,
                    	'to_name' => $user['0']->username,
                    	'from' => 'Licensing@greeklicensing.com',
                    	'from_name' => 'Greek Licensing',
                    	'subject' => $frmField['notification_title'],
                    	'body' => $frmField['notification_message']
                    );
                    
                    $this->_helper->BUtilities->send_mail($email);
            }
            //print_r($vendorEmails);
            //echo "All vendors";
          //  $this->MailChimp($vendorEmails, $frmField['notification_title'], $frmField['notification_message']);
        }
    }

    /**
     * Function to send email using mailchimp API
     * @author Masud
     * @version 0.1
     * @copyright Blueliner Marketing
     * @param <type> $email_list
     * @param <type> $title
     * @param <type> $message
     * @access private
     * @return void
     */
    private function MailChimp($email_list, $title, $message) {
        /*
          print_r($email_list);
          echo $title;
          echo $message;
         //*/
        $listId = '';
        $mailChimpList = $this->em->getRepository("BL\Entity\MailChimpList")->findAll();

        foreach ($mailChimpList as $key => $list) {

            //echo $list->id . " " . $list->list_id . " ".$list->sent_time->format('Y-m-d H:i:s');            
            $now = date('Y-m-d H:i:s');
            $to_time = strtotime($now);
            $from_time = strtotime($list->sent_time->format('Y-m-d H:i:s'));
            $timeDiff = round(abs($to_time - $from_time) / 60, 2);
            //echo "To time = " . $to_time . "From time = " . $from_time . " Time diff = " . $timeDiff;
            //print_r($list);

            if ($timeDiff > 15) {//taking the time interval to send an email chunk to 15 min
                $listId = $list->list_id;
                $list->sent_time = new DateTime();
                $this->em->persist($list);
                $this->em->flush();
                break;
            }
        }

        if ($listId != null) {

            require_once 'mailChimpAPI/MCAPI.class.php';
            $api = new MCAPI('496da83a1ca1d819fdee2f8c6b5e0f17-us4');

            //print_r($api);
            //$email_list = $emails;
            //$listId = 'afed1fdab4';
            $emails = $api->listMembers($listId);
            $size = sizeof($emails['data']);
            //echo $size;

            $unsub_list = array();
            for ($i = 0; $i < $size; $i++) {
                $unsub_list[] = $emails['data'][$i]['email'];
            }

            if ($size > 0) {
                $vals = $api->listBatchUnsubscribe($listId, $unsub_list, true, false, false);
            }
            if ($api->errorCode) {
                //$this->flshMssg->addMessage(array('error' => 'Unable to Unsubscribe! Code = ' . $api->errorCode . ' Msg = ' . $api->errorMessage));
                echo $api->errorCode . ' Msg = ' . $api->errorMessage;
            } else {
                foreach ($email_list as $email) {
                    //$em_dt = explode('#', $v);//echo $databaseTime;
                    //$batch[] = array('EMAIL' => $em_dt[0], 'FNAME' => $em_dt[1], 'LNAME' => $this->_helper->BUtilities->site_encrypt($v));
                    $batch[] = array('EMAIL' => $email);
                }
                //print_r($batch);
                $vals = $api->listBatchSubscribe($listId, $batch, false, true, false);
                if ($api->errorCode) {
                    //$this->flshMssg->addMessage(array('error' => 'Unable to Subscribe! Code = ' . $api->errorCode . ' Msg = ' . $api->errorMessage));
                    echo $api->errorCode . ' Msg1 = ' . $api->errorMessage;
                } else {//echo $databaseTime;
                    $type = 'regular';
                    $opts['list_id'] = $listId; //'70f66df1c5';
                    $opts['subject'] = $title;
                    $opts['from_email'] = 'admin@affinity.com';
                    $opts['from_name'] = 'Affinity Consults';


                    $email_text_processed = $message;
                    $content = array('html' => $email_text_processed,
                        'text' => 'text content *|UNSUB|*');

                    $camp_id = $api->campaignCreate($type, $opts, $content);
                    if ($api->errorCode) {
                        echo "Unable to Create New Campaign!";
                        echo "\n\tCode=" . $api->errorCode;
                        echo "\n\tMsg=" . $api->errorMessage . "\n";
                    } else {
                        echo "New Campaign ID:" . $camp_id . "\n";
                    }
                    $retval = $api->campaignSendNow($camp_id);
                }
            }
        } else {
            echo "Please send this notification 30 min later.";
        }
    }

    /**
     * Function to get notification users
     * @author Masud
     * @version 0.1
     * @copyright Blueliner Marketing
     * @param <int> $nId
     * @return <array>
     */
    private function getNotificationUsers($nId) {
        $notificationUsers = $this->em->getRepository("BL\Entity\NotificationUser")->findBy(array('notification_id' => $nId));
        $clientArr = array();
        $vendorArr = array();
        foreach ($notificationUsers as $notificationUser) {
            if ($notificationUser->user_id->account_type == ACC_TYPE_CLIENT) {
                $clientArr[$notificationUser->user_id->id] = $notificationUser->user_id->organization_name;
            } else if ($notificationUser->user_id->account_type == ACC_TYPE_VENDOR) {
                $vendorArr[$notificationUser->user_id->id] = $notificationUser->user_id->organization_name;
            }
        }
        return array($clientArr, $vendorArr);
    }

    /**
     * Function to Add correspondence when notifications are sent
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function addCorrespondence($frmField, $for_user_type, $notification_via) {
        /**
         * Both Clients and vendors are subset of User Entity. So we collect all the ids and then add correspondence
         */
        $vendor_ids = array();
        $client_ids = array();


        if (isset($frmField['right_vendor']) /*&& ($notification_via == "email" || $notification_via == "email_notification")*/) {
            foreach ($frmField['right_vendor'] as $user_id) {
                $vendor_ids[] = $user_id;
            }
        }

        if (isset($frmField['right_client']) /*&& ($notification_via == "email" || $notification_via == "email_notification")*/) {
            foreach ($frmField['right_client'] as $user_id) {
                $client_ids[] = $user_id;
            }
        }
        
        if ($for_user_type == "c_vrandom" || $for_user_type == "all_client" || $for_user_type == "all") {
            $allClients = $this->em->getRepository("BL\Entity\User")->findBy(array('account_type' => ACC_TYPE_CLIENT));
            foreach ($allClients as $user) {
                $client_ids[] = $user->id;
            }
        }

        if ($for_user_type == "v_crandom" || $for_user_type == "all_vendor" || $for_user_type == "all") {
            $allVendors = $this->em->getRepository("BL\Entity\User")->findBy(array('account_type' => ACC_TYPE_VENDOR, 'user_status' => 'Current'));
            
            foreach ($allVendors as $user) {
                $vendor_ids[] = $user->id;
            }
        }

        $vendor_ids = array_unique($vendor_ids);
        $client_ids = array_unique($client_ids);

        //$this->view->BUtils()->pre($vendor_ids,1);

        /**
         *  Ok we have all the clients and vendors 
         *  Lets inserts the correspondence for vendors first (more likely)
         */
        $i = 0;
        $batchSize = 20;
        if (sizeof($vendor_ids)) {
            foreach ($vendor_ids as $vendor) {
                $new_correspondence = new \BL\Entity\VendorCorrespondence();
                $new_correspondence->note = htmlentities($frmField['notification_message']);
                $new_correspondence->subject = htmlentities($frmField['notification_title']);
                $new_correspondence->note_time = new DateTime();
                $new_correspondence->vendor_id = $this->em->find('BL\Entity\User', (int) $vendor);
                
                $this->em->persist($new_correspondence);

                if (sizeof($vendor_ids) < $batchSize) {
                    $this->em->flush();
                    $this->em->clear();
                } elseif ($i++ % $batchSize == 0) {
                    $this->em->flush();
                    $this->em->clear();
                }
            }
            
          	$this->em->flush();
            $this->em->clear();
        }

        /**  Now clients (if any)  */
        $i = 0;
        if (sizeof($client_ids)) {
            foreach ($client_ids as $client) {
                $new_correspondence = new \BL\Entity\ClientCorrespondence();
                $new_correspondence->note = htmlentities($frmField['notification_message']);
                $new_correspondence->subject = htmlentities($frmField['notification_title']);
                $new_correspondence->note_time = new DateTime();
                $new_correspondence->client_id = $this->em->find('BL\Entity\User', (int) $client);
                $this->em->persist($new_correspondence);

                if (sizeof($client_ids) < $batchSize) {
                    $this->em->flush();
                } elseif ($i++ % $batchSize == 0) {
                    $this->em->flush();
                }
            }
            $this->em->flush();
            $this->em->clear();
        }
    }

}

