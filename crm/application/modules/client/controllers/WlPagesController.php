<?php

class Client_WlPagesController extends Zend_Controller_Action {

    public function init() {
	/* Initialize action controller here */
	$this->doctrineContainer = Zend_Registry::get('doctrine');
	$this->em = $this->doctrineContainer->getEntityManager();
    }

    public function indexAction() {
	$this->_helper->layout()->setLayout('layout/layout1');
	$search = new Client_Form_VendorSearch();
	//$clientId =  $this->_getParam('id');
	$slug = $this->_getParam('slug');
	$whiteLabel = $this->em->getRepository('BL\Entity\WhiteLabel')->findOneBy(array('url' => $slug));
	if (empty($whiteLabel)) {
	    $baseUrl = new Zend_View_Helper_BaseUrl();
	    $this->getResponse()->setRedirect($baseUrl->baseUrl());
	}
	$clientId = $whiteLabel->client->id;
	$clientProfile = $this->em->getRepository('BL\Entity\ClientProfile')->findOneBy(array('user_id' => $clientId));

	$city = '';
	$zip_code = '';
	$vendor_name = '';
	$product = '';
	$services = '';
	// if ($this->getRequest()->isPost()) {
	$display_result = 1;
	$display_result = 1;
	$city = $this->_getParam('city');
	$zip_code = $this->_getParam('zip');
	$product = trim($this->_getParam('looking'));
	$services = $this->_getParam('service');
	$vendor_name = $this->_getParam('vendor');
	
	if ($city == "City or State") $city = null;
	if ($zip_code == "Zip Code") $zip_code = null;
	if ($product == "Product Name") $product = null;
	if ($vendor_name == "Company Name") $vendor_name = null;
	
	$user = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id'=>$clientId));

	//}
	//
        $page = $this->_getParam('page');
	if (empty($page)) {
	    $page = 1;
	}
	$vendors = $this->em->getRepository('BL\Entity\User')->getVendors($clientId, $city, $vendor_name, $zip_code, $product, $services);
	/* $db = Zend_Db_Table::getDefaultAdapter();
	  $rowset = $db->query($sql)->fetchAll();
	  $paginator = Zend_Paginator::factory($rowset);
	  //$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($sql));

	  $paginator->setCurrentPageNumber($page);
	  $paginator->setItemCountPerPage(20); */
	$this->view->clientProfile = $clientProfile;
	$this->view->whiteLabel = $whiteLabel;
	$this->view->searchForm = $search;
	$this->view->vendors = $vendors;
	$this->view->slug = $slug;
	
	$this->view->city = $city;
	$this->view->zip_code = $zip_code;
	$this->view->vendor_name = $vendor_name;
	$this->view->product = $product;
	$this->view->services = $services;
	$this->view->user = $user;
	
	if (!empty($user->client_banners)){

		$this->view->banners = $user->client_banners;
	}
    }

    public function vendorProfileAction() {
	$this->_helper->layout()->setLayout('layout/layout1');
	$vendor_id = $this->_getParam('vid');

	$slug = $this->_getParam('c');
	$whiteLabel = $this->em->getRepository('BL\Entity\WhiteLabel')->findOneBy(array('url' => $slug));
	if (empty($whiteLabel)) {
	    $baseUrl = new Zend_View_Helper_BaseUrl();
	    $this->getResponse()->setRedirect($baseUrl->baseUrl());
	}
	$clientId = $whiteLabel->client->id;
	$clientProfile = $this->em->getRepository('BL\Entity\ClientProfile')->findOneBy(array('user_id' => $clientId));



	$vendor = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id'=>$vendor_id));
	$vendorProfile = $this->em->getRepository('BL\Entity\VendorProfile')->findOneBy(array('user_id' => (int) $vendor_id));
        $vendorSampleProduct = $this->em->getRepository('BL\Entity\VendorSampleFile')->findBy(array('Vendor' => (int) $vendor_id, 'use_for' => 'web_profile'));
        $vendorOperation = $this->em->getRepository("BL\Entity\VendorOperation")->findOneBy(array('user_id' => (int) $vendor_id));
        $vendorService = $this->em->getRepository('BL\Entity\VendorService')->findBy(array('vendor_id' => (int) $vendor_id));
	$vendorOfferedProduct = $this->em->getRepository('BL\Entity\VendorWebProfileProducts')->getVendorProducts($vendor_id);


	$this->view->vendor = $vendor;
	$this->view->vendorProfile = $vendorProfile;
	$this->view->vendorSampleProduct = $vendorSampleProduct;
	$this->view->vendorService = $vendorService;
	$this->view->vendorOfferedProduct = $vendorOfferedProduct;
	$this->view->vendorOperation = $vendorOperation;
	$this->view->clientProfile = $clientProfile;
	$this->view->whiteLabel = $whiteLabel;
    }

}

?>
