<?php

class Admin_DesignController extends Zend_Controller_Action {

    protected $em;
    private $ItemPerPage;

    public function init() {
        /* Initialize action controller here */
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
        $this->ApprovalStatus = array("all" => -1, "pending" => 0, "approved" => 1, "rejected" => 2);
        $this->ItemPerPage = 24;
    }

    public function indexAction() {
        // index action body
        //$this->pendingAction();
        $this->_helper->redirector('pending');
    }

    /**
     * All Action
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     */
    public function allAction() {
        // all action body
        $this->_helper->JSLibs->load_jqTextExt_assets();
        $this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_fancy_assets();
        $this->SelectVendor();
        $this->SetCurrentDate();
        $this->view->tagfilter = trim($this->_getParam('tagfilter'));

        /* All Design - Vendor List */
        $this->view->owner_list = $this->DistinctOwners($this->ApprovalStatus["all"]);
        $this->view->currentvendor = $this->_getParam('vendor');
        $this->view->counter = $this->em->getRepository("BL\Entity\ProductDesign")->getDesignTypeCounter($this->_getParam('vendor'), $this->ApprovalStatus["all"], $this->_getParam('start_date'), $this->_getParam('end_date'), $this->view->tagfilter);
        if ($this->view->tagfilter == "" || $this->view->tagfilter == NULL) {
            $this->view->all_designs = $this->em->getRepository("BL\Entity\ProductDesign")->getDesignByApproval($this->_getParam('page', 1), $this->_getParam('vendor'), $this->ApprovalStatus["all"], $this->_getParam('start_date'), $this->_getParam('end_date'), $this->ItemPerPage);
        } else {
            $this->view->tag_designs = $this->em->getRepository("BL\Entity\ProductDesign")->getDesignByTags($this->_getParam('page', 1), $this->_getParam('vendor'), $this->ApprovalStatus["all"], $this->_getParam('start_date'), $this->_getParam('end_date'), $this->view->tagfilter, $this->ItemPerPage);
        }
        $this->render('index');
    }

    /**
     * Pending Action
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     */
    public function pendingAction() {
        $this->_helper->JSLibs->load_jqTextExt_assets();
        $this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_fancy_assets();
        $this->SelectVendor();
        $this->SetCurrentDate();
        $this->view->tagfilter = trim($this->_getParam('tagfilter'));
	$page = $this->_getParam('page', 1);
	$this->view->page = $page;

        /* Pending Design - Vendor List */
        $this->view->owner_list = $this->DistinctOwners($this->ApprovalStatus["pending"]);
        $this->view->currentvendor = $this->_getParam('vendor');
        $this->view->counter = $this->em->getRepository("BL\Entity\ProductDesign")->getDesignTypeCounter($this->_getParam('vendor'), $this->ApprovalStatus["pending"], $this->_getParam('start_date'), $this->_getParam('end_date'), $this->view->tagfilter);
        if ($this->view->tagfilter == "" || $this->view->tagfilter == NULL) {
            $this->view->all_designs = $this->em->getRepository("BL\Entity\ProductDesign")->getDesignByApproval($this->_getParam('page', 1), $this->_getParam('vendor'), $this->ApprovalStatus["pending"], $this->_getParam('start_date'), $this->_getParam('end_date'), $this->ItemPerPage); //, trim($this->_getParam('tagfilter'))
        } else {
            $this->view->tag_designs = $this->em->getRepository("BL\Entity\ProductDesign")->getDesignByTags($this->_getParam('page', 1), $this->_getParam('vendor'), $this->ApprovalStatus["pending"], $this->_getParam('start_date'), $this->_getParam('end_date'), $this->view->tagfilter, $this->ItemPerPage);
        }
        $this->render('index');
    }

    /**
     * Approved Action
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     */
    public function approvedAction() {
        $this->_helper->JSLibs->load_jqTextExt_assets();
        $this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_fancy_assets();
        $this->SelectVendor();
        $this->SetCurrentDate();
        $this->view->tagfilter = trim($this->_getParam('tagfilter'));

        /* Approved Design - Vendor List */
        $this->view->owner_list = $this->DistinctOwners($this->ApprovalStatus["approved"]);
        $this->view->currentvendor = $this->_getParam('vendor');
        $this->view->counter = $this->em->getRepository("BL\Entity\ProductDesign")->getDesignTypeCounter($this->_getParam('vendor'), $this->ApprovalStatus["approved"], $this->_getParam('start_date'), $this->_getParam('end_date'), $this->view->tagfilter);
        if ($this->view->tagfilter == "" || $this->view->tagfilter == NULL) {
            $this->view->all_designs = $this->em->getRepository("BL\Entity\ProductDesign")->getDesignByApproval($this->_getParam('page', 1), $this->_getParam('vendor'), $this->ApprovalStatus["approved"], $this->_getParam('start_date'), $this->_getParam('end_date'), $this->ItemPerPage); //, trim($this->_getParam('tagfilter'))
        } else {
            $this->view->tag_designs = $this->em->getRepository("BL\Entity\ProductDesign")->getDesignByTags($this->_getParam('page', 1), $this->_getParam('vendor'), $this->ApprovalStatus["approved"], $this->_getParam('start_date'), $this->_getParam('end_date'), $this->view->tagfilter, $this->ItemPerPage);
        }
        $this->render('index');
    }

    /**
     * Rejected Action
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     */
    public function rejectedAction() {
        $this->_helper->JSLibs->load_jqTextExt_assets();
        $this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_fancy_assets();
        $this->SelectVendor();
        $this->SetCurrentDate();
        $this->view->tagfilter = trim($this->_getParam('tagfilter'));

        /* Rejected Design - Vendor List */
        $this->view->owner_list = $this->DistinctOwners($this->ApprovalStatus["rejected"]);
        $this->view->currentvendor = $this->_getParam('vendor');
        $this->view->counter = $this->em->getRepository("BL\Entity\ProductDesign")->getDesignTypeCounter($this->_getParam('vendor'), $this->ApprovalStatus["rejected"], $this->_getParam('start_date'), $this->_getParam('end_date'), $this->view->tagfilter);
        if ($this->view->tagfilter == "" || $this->view->tagfilter == NULL) {
            $this->view->all_designs = $this->em->getRepository("BL\Entity\ProductDesign")->getDesignByApproval($this->_getParam('page', 1), $this->_getParam('vendor'), $this->ApprovalStatus["rejected"], $this->_getParam('start_date'), $this->_getParam('end_date'), $this->ItemPerPage); //, trim($this->_getParam('tagfilter'))
        } else {
            $this->view->tag_designs = $this->em->getRepository("BL\Entity\ProductDesign")->getDesignByTags($this->_getParam('page', 1), $this->_getParam('vendor'), $this->ApprovalStatus["rejected"], $this->_getParam('start_date'), $this->_getParam('end_date'), $this->view->tagfilter, $this->ItemPerPage);
        }
        $this->render('index');
    }

    /**
     * Function to provide Details View of FancyBox
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function detailsViewAction() {
        //$this->_helper->JSLibs->load_jqTextExt_assets();
        $this->_helper->JSLibs->load_tinymce_assets();
        $this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_fancy_assets();

        // First Time Request
        if ($this->_getParam('status') != "" || $this->_getParam('status') != NULL) {
            $this->view->status = $status = $this->_getParam('status');
            $this->view->currentvendor = $currentvendor = $this->_getParam('vendor');
            $design_id = $this->_getParam('design_id');
            $this->view->from = $from = $this->_getParam('start_date');
            $this->view->to = $to = $this->_getParam('end_date');
            $this->view->pg = $pg = $this->_getParam('pg');
            $this->view->tagfilter = trim($this->_getParam('tagfilter'));
        }

        if (isset($_GET['save'])) {
            if ($this->_getParam('statusgrp') != "" || $this->_getParam('statusgrp') != NULL) /* Approval or Rejected */
                $statusgrp = $this->_getParam('statusgrp');
            else /* Pending */
                $statusgrp = 0;

            if ($this->_getParam('id') != "" || $this->_getParam('id') != NULL) /* ID is NOT NULL */ {
                $design_id = $this->_getParam('id');
                $tagwith_array = explode(",", $this->_getParam('tagwith'));
                if (count($tagwith_array) > 0) {
                    foreach ($tagwith_array as $tag) {
                        $tag = trim($tag);
                        if ($tag != '') {
                            $design_tag = $this->em->getRepository("BL\Entity\DesignTag")->findOneBy(array('tag_name' => $tag));
                            if ($design_tag == NULL) /* No Design Tag found - Insert into database. */ {
                                $class = 'BL\Entity\DesignTag';
                                $DesignTag = new $class();
                                $DesignTag->tag_name = $tag;
                                $this->em->persist($DesignTag);
                                $this->em->flush();
                                $this->em->clear();
                                $design_tag = $this->em->getRepository("BL\Entity\DesignTag")->findOneBy(array('tag_name' => $tag));
                            }
                            $product_design_tag = $this->em->getRepository("BL\Entity\ProductDesignDesignTag")->findOneBy(array('product_design_id' => $this->_getParam('id'), 'design_tag_id' => $design_tag->id));
                            if ($product_design_tag == NULL) {
                                $class = 'BL\Entity\ProductDesignDesignTag';
                                $ProductDesignDesignTag = new $class();
                                $ProductDesignDesignTag->product_design_id = $this->em->find('BL\Entity\ProductDesign', $this->_getParam('id'));
                                $ProductDesignDesignTag->design_tag_id = $this->em->find('BL\Entity\DesignTag', $design_tag->id);
                                $this->em->persist($ProductDesignDesignTag);
                                $this->em->flush();
                                $this->em->clear();
                            }
                        }
                    }
                }
                $vendor = $this->em->getRepository("BL\Entity\ProductDesign")->findOneBy(array('id' => $this->_getParam('id')));
                $vendor->note_from_admin = $this->_getParam('comment');
                $vendor->is_approved = $this->_getParam('statusgrp');
                $this->em->persist($vendor);
                $this->em->flush();
            }

		error_log("\nsending Email", 3, "./errorLog.log");

	    /**
	    * send invoice email to vendor
	    */
	    $design = $this->em->getRepository("BL\Entity\ProductDesign")->findOneBy(array('id'	=>  $this->_getParam('id')));
	    $vendorUser = $design->owner_id;
	    $body = 'Dear '.$vendorUser->first_name.' '.$vendorUser->last_name.',<br/><br/>';
	    $body .= $this->_getParam('comment');
	    $params = array(
		'to' => $vendorUser->email,
		'to_name' => $vendorUser->first_name.' '.$vendorUser->last_name,
		'from' => 'admin@greeklicensing.com',
                'from_name' => 'AMC Admin',
                'subject' => 'Greek licensing design notification',
                'body' => $body,
		'file'	=>  APPLICATION_PATH.'/../assets/files/design/thumbs/_thumb2' . $design->file_url
	    );
	    $dump = $this->getHelper('BUtilities')->send_mail($params);

	    error_log(" - Sent!", 3, "./errorLog.log");
	    
        }
        if ($this->view->tagfilter == "" || $this->view->tagfilter == NULL) {
            $this->view->all_designs = $this->em->getRepository("BL\Entity\ProductDesign")->getDesignByApproval($this->_getParam('page', $pg), $currentvendor, $status, $from, $to, 1);
        } else {
            $this->view->tag_designs = $this->em->getRepository("BL\Entity\ProductDesign")->getDesignByTags($this->_getParam('page', $pg), $currentvendor, $status, $from, $to, $this->view->tagfilter, 1);
        }

        if ($currentvendor != '') {
            $this->view->licensed_clients = $this->em->getRepository("BL\Entity\License")->getActiveClients($currentvendor);
            $this->view->all_tagwith = $this->em->getRepository("BL\Entity\ProductDesignDesignTag")->findBy(array('product_design_id' => $design_id));
        } else {
            if ($this->view->tagfilter == "" || $this->view->tagfilter == NULL) {
                if (sizeof($this->view->all_designs)) {
                    foreach ($this->view->all_designs as $design) {
                        $this->view->licensed_clients = $this->em->getRepository("BL\Entity\License")->getActiveClients((string) $design->owner_id->id);
                        $this->view->all_tagwith = $this->em->getRepository("BL\Entity\ProductDesignDesignTag")->findBy(array('product_design_id' => (string) $design->id));
                    }
                }
            } else {
                if (sizeof($this->view->tag_designs)) {
                    foreach ($this->view->tag_designs as $design) {
                        $this->view->licensed_clients = $this->em->getRepository("BL\Entity\License")->getActiveClients((string) $design['user_id']);
                        $this->view->all_tagwith = $this->em->getRepository("BL\Entity\ProductDesignDesignTag")->findBy(array('product_design_id' => (string) $design['id']));
                    }
                }
            }
        }
        $this->_helper->layout()->setLayout('layout/iframe_layout');
    }

    /**
     * Function to provide JSON data to feed Tag Filter
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetTagsAction() {
        $this->_helper->BUtilities->setNoLayout();
        $q = strtolower($this->_getParam('term'));
        if (!$q)
            return;
        $Clients = $this->em->getRepository("BL\Entity\User")->findBy(array('account_type' => ACC_TYPE_CLIENT), array('organization_name' => 'asc'));
        $data = array();
        foreach ($Clients as $client) {
            $data[htmlentities(stripslashes($client->organization_name))] = $client->id;
        }
        $result = array();
        foreach ($data as $key => $value) {
            if (strpos(strtolower($key), $q) !== false) {
                array_push($result, array("id" => $value, "label" => $key, "value" => strip_tags($key)));
            }
            if (count($result) > 11)
                break;
        }
        echo Zend_Json::encode($result);
    }

    /**
     * Function to Selected Vendors Array
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access private
     * @return Array
     */
    private function SelectVendor() {
        if ($this->_getParam('vendor') == "" || $this->_getParam('vendor') == NULL)
            $this->view->selected_vendor = "All";
        else
            $this->view->selected_vendor = $this->_getParam('vendor');
    }

    /**
     * Function to Selected Current Date
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access private
     * @return DateTime
     */
    private function SetCurrentDate() {
        if (( $this->_getParam('start_date') != '' || $this->_getParam('start_date') != NULL) && ( $this->_getParam('end_date') != '' || $this->_getParam('end_date') != NULL)) {
            $this->view->from_date = $this->_getParam('start_date');
            $this->view->to_date = $this->_getParam('end_date');
        } else {
            $this->view->from_date = "";
            $this->view->to_date = "";
        }
    }

    /**
     * Function to Return Distinct Owner List
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access private
     * @return Array
     */
    private function DistinctOwners($token) {
        $distinct_owners = $this->em->getRepository("BL\Entity\ProductDesign")->getDistinctOwners($token);
        $ownerList = array();
        foreach ($distinct_owners as $owner) {
            $Users = $this->em->getRepository("BL\Entity\User")->getUser($owner['owner_id']);
            array_push($ownerList, array('vendor_id' => $Users[0]['id'], 'vendor_org_name' => $Users[0]['organization_name']));
        }
        return $ownerList;
    }

    /**
     * Function to download Vendor Design Image file
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function downloadDesignAction() {
        $file = $this->_getParam('file');
        $ext = explode('.', $file);
        $targetDir = APPLICATION_PATH . '/../assets/files/design/';

        header('Content-Type: image/' . $ext[sizeof($ext) - 1]);
        header('Content-Disposition: attachment; filename="' . $file . '"');
        readfile($targetDir . $file);
        $this->_helper->BUtilities->setNoLayout();
    }

}

