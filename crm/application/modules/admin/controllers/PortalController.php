<?php

class Admin_PortalController extends Zend_Controller_Action {

    public $em;

    public function init() {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
    }

    /**
     * Function to Validate on the form using AJAX
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     *
     */
    public function indexAction() {
        if ($this->view->BUtils()->getLoggedInUserRole() === 1) {
            $this->_redirect("admin/portal/users");
        } elseif ($this->view->BUtils()->getLoggedInUserRole() === 4) {
            $this->_redirect("admin/portal/banners");
        }
    }

    /**
     * Function to add AMC new user
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function addUsersAction() {
        if ($this->view->BUtils()->getLoggedInUserRole() === 1) {
            $portal_model = new Admin_Model_Portal($this);
            $portal_model->addUser();
        } else {
            $this->_helper->flashMessenger("Sorry, But you do not have access to this section of the portal!", "Info");
            $this->_redirect("admin/portal/banners");
        }
    }

    /**
     * Function to show list of all AMC users
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function usersAction() {
        if ($this->view->BUtils()->getLoggedInUserRole() === 1) {
            $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets'));
        } else {
            $this->_helper->flashMessenger("Sorry, But you do not have access to this section of the portal!", "Info");
            $this->_redirect("admin/portal/banners");
        }
    }

    /**
     * Function to ajax get admin users
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetUsersAction() {
        $this->_helper->BUtilities->setNoLayout();
        $portal_model = new Admin_Model_Portal($this);
        $portal_model->ajaxGetUsers();
        
        
    }

    /**
     * Function to show user details
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function usersDetailsAction() {
        if ($this->view->BUtils()->getLoggedInUserRole() === 1) {
            $portal_model = new Admin_Model_Portal($this);
            $portal_model->userDetails();
        } else {
            $this->_helper->flashMessenger("Sorry, But you do not have access to this section of the portal!", "Info");
            $this->_redirect("admin/portal/banners");
        }
    }

    /**
     * Function to show Banners
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String     
     */
    public function bannersAction() {
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets', 'load_fancy_assets'));
    }

    /**
     * Function to Get Banners for the DataTable
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String     
     */
    public function ajaxGetBannersAction() {
        $this->_helper->BUtilities->setNoLayout();
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
        $sorting_cols = array('0' => 'b.name', '1' => 'b.location', '2' => 'b.is_active', '3' => 'b.added_on', '4' => 'b.start_date', '5'=> 'b.end_date');
        $params['sort_key'] = $sorting_cols[$this->_getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->_getParam('sSortDir_0');
        $params['vendor_id'] = $this->_getParam('id');

        $records = $this->em->getRepository("BL\Entity\Banner")->getBanners($params)->getResult();
        $params['show_total'] = true;
        $records_total = $this->em->getRepository("BL\Entity\Banner")->getBanners($params);
        /**
         * Datatable doesn't understand json_encode and have a custom json format
         */
        
        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":';
        $prec = array();

        foreach ($records as $v) {
        	$start_date = ($v->start_date != null)? $v->start_date->format("m/d/Y"): 'no start date';
        	$end_date = ($v->end_date != null)? $v->end_date->format("m/d/Y"): 'no end date';
        	
            $prec[] = array(
                '<a href="javascript:;" class="fancy"><img src="' . $this->view->baseUrl("assets/files/banners/thumbs/_thumb") . '' . $v->image . '" height="50" /></a>',
                $v->name,
                $v->location,
                $v->added_on->format("m/d/Y"),
                (($v->is_active) ? "Active" : "Inactive"),
            	$start_date,
            	$end_date,
                '<a href="' . $this->view->baseUrl("admin/portal/edit-banner/id/{$v->id}") . '">Edit</a>&nbsp; 
                    <a class="delete" href="' . $this->view->baseUrl("admin/portal/delete-banner/id/{$v->id}") . '">Delete</a>&nbsp;
                    <a href="javascript:;" class="view_clients" rel="' . $v->id . '">View Orgs</a>'
            );
        }
        $json .= Zend_Json::encode($prec);
        $json .= '}';
        echo $json;
    }

    /**
     * Function to show white label
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String     
     */
    public function whiteLabelAction() {
        $this->view->clients = $this->em->getRepository('BL\Entity\WhiteLabel')->getClients();
    }

    /**
     * Function to Add a banner to the system with clients selected
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function addBannerAction() {
        $form = new Admin_Form_AddBanner();
        $this->view->form = $form;
        $this->view->clients = $this->em->getRepository("BL\Entity\User")->findBy(array('account_type' => ACC_TYPE_CLIENT), array('organization_name' => 'asc'));
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                /**
                 * Let's handle the upload first.
                 */
                $destination = APPLICATION_PATH . '/../assets/files/banners/';
                $uploader = new Zend_File_Transfer_Adapter_Http();
                $uploader->setDestination(realpath($destination));
                if (!$uploader->receive()) {
                    $messages = $uploader->getMessages();
                }
                /**
                 * Let's create thumbnails here
                 */
                $uploaded_file = $uploader->getFileInfo();
                $fileName = $uploaded_file['image']['name'];
                include("ThirdParty/PhpThumb/ThumbLib.inc.php");
                $thumb_save_path = $destination . DIRECTORY_SEPARATOR . "thumbs" . DIRECTORY_SEPARATOR . "_thumb" . $fileName;
                $thumb2_save_path = $destination . DIRECTORY_SEPARATOR . "thumbs" . DIRECTORY_SEPARATOR . "_thumb2" . $fileName;
                $thumb = PhpThumbFactory::create($destination . DIRECTORY_SEPARATOR . $fileName);
                $thumb->resize(110, 110)->padding(110, 75, '#FFFFFF');
                $thumb->save($thumb_save_path);

                $thumb = PhpThumbFactory::create($destination . DIRECTORY_SEPARATOR . $fileName);
                $thumb->resize(728, 90)->padding(728, 90, '#FFFFFF');
                $thumb->save($thumb2_save_path);


                $banner = new \BL\Entity\Banner();
                $user = $this->em->find("BL\Entity\User", (int) $this->_helper->BUtilities->getLoggedInUser());
                $banner->name = $form->getValue('name');
                $banner->link = $form->getValue('link');
                $banner->location = $form->getValue('location');
                $banner->is_active = $form->getValue('status');
                $banner->image = $fileName;
                $banner->added_by = $user;
                $banner->start_date = new DateTime($form->getValue('startDate'));
                $banner->end_date = new DateTime($form->getValue('endDate'));
                $this->em->persist($banner);
                $this->em->flush();
                $batchSize = 20;
                if (sizeof($_POST['clients'])) {
                    foreach ($_POST['clients'] as $client) {
                        $new_banner_client = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' => $client));
                        $banner->clients->add($new_banner_client);
                        if (sizeof($_POST['clients']) < $batchSize) {
                            $this->em->flush();
                        } elseif ($i % $batchSize == 0) {
                            $this->em->flush();
                        }
                    }
                    $this->em->flush();
                    $this->em->clear();
                }
                $this->_helper->flashMessenger("Banner(s) Added", "Info");
                $this->_redirect('admin/portal/banners');
            }
        }
    }

    /**
     * Function to Edit Banner 
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function editBannerAction() {
        $this->_helper->JSLibs->do_call(array('load_fancy_assets'));
        $banner_id = $this->_getParam("id");
        $banner = $this->em->find("BL\Entity\Banner", (int) $banner_id);
        $this->view->banner = $banner;
        $form = new Admin_Form_AddBanner();
        $this->view->form = $form;
        $this->view->clients = $this->em->getRepository("BL\Entity\User")->findBy(array('account_type' => ACC_TYPE_CLIENT), array('organization_name' => 'asc'));
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                /**
                 * Let's handle the upload first.
                 */
                $destination = APPLICATION_PATH . '/../assets/files/banners/';
                $uploader = new Zend_File_Transfer_Adapter_Http();
                $uploader->setDestination(realpath($destination));
                if ($uploader->receive()) {

                    /**
                     * Let's create thumbnails here
                     */
                    $uploaded_file = $uploader->getFileInfo();
                    $fileName = $uploaded_file['image']['name'];
                    include("ThirdParty/PhpThumb/ThumbLib.inc.php");
                    $thumb_save_path = $destination . DIRECTORY_SEPARATOR . "thumbs" . DIRECTORY_SEPARATOR . "_thumb" . $fileName;
                    $thumb2_save_path = $destination . DIRECTORY_SEPARATOR . "thumbs" . DIRECTORY_SEPARATOR . "_thumb2" . $fileName;
                    $thumb = PhpThumbFactory::create($destination . DIRECTORY_SEPARATOR . $fileName);
                    $thumb->resize(110, 110)->padding(110, 75, '#FFFFFF');
                    $thumb->save($thumb_save_path);

                    $thumb = PhpThumbFactory::create($destination . DIRECTORY_SEPARATOR . $fileName);
                    $thumb->resize(728, 90)->padding(728, 90, '#FFFFFF');
                    $thumb->save($thumb2_save_path);
                    /**
                     * Ok new image is uploaded, now delete the existing ones
                     */
                    @unlink(realpath(APPLICATION_PATH . "/../assets/files/banners/{$banner->image}"));
                    @unlink(realpath(APPLICATION_PATH . "/../assets/files/banners/thumbs/_thumb{$banner->image}"));
                    @unlink(realpath(APPLICATION_PATH . "/../assets/files/banners/thumbs/_thumb2{$banner->image}"));
                }
                $user = $this->em->find("BL\Entity\User", (int) $this->_helper->BUtilities->getLoggedInUser());
                $banner->name = $form->getValue('name');
                $banner->link = $form->getValue('link');
                $banner->location = $form->getValue('location');
                $banner->is_active = $form->getValue('status');
                $banner->image = $uploader->receive() ? $fileName : $banner->image;
                $banner->start_date = new DateTime($form->getValue('startDate'));
                $banner->end_date = new DateTime($form->getValue('endDate'));
                $this->em->flush();
                $batchSize = 20;
                /**
                 * Before adding clients to the banner, we have to delete the earlier ones
                 */
                foreach ($banner->clients as $c) {
                    $banner_client = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' => $c->id));
                    $banner->clients->removeElement($banner_client);
                }
                $this->em->flush();
                if (sizeof($_POST['clients'])) {
                    foreach ($_POST['clients'] as $client) {
                        $new_banner_client = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' => $client));
                        $banner->clients->add($new_banner_client);
                        if (sizeof($_POST['clients']) < $batchSize) {
                            $this->em->flush();
                        } elseif ($i % $batchSize == 0) {
                            $this->em->flush();
                        }
                    }
                    $this->em->flush();
                    $this->em->clear();
                }
                $this->_helper->flashMessenger("Banner Information Saved", "Info");
                $this->_redirect('admin/portal/banners');
            }
        } else {
            /**
             * $form->populate expects array but we have object from doctrine. So dirty work now :(
             */
            $form->populate(array(
                'name' => $banner->name,
                'link' => $banner->link,
                'location' => $banner->location,
                'is_active' => $banner->is_active,
            	'startDate' => $banner->start_date->format('m/d/Y'),
            	'endDate' => $banner->end_date->format('m/d/Y')
            ));
        }
    }

    /**
     * Function to Add White Label to Client 
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function addWhiteLabelAction() {
        $form = new Admin_Form_WhiteLabel();
        $this->view->form = $form;
        $this->view->clients = $this->em->getRepository("BL\Entity\User")->findBy(array('account_type' => ACC_TYPE_CLIENT), array('organization_name' => 'asc'));
        $this->view->client_id = $this->_getParam("client", false);
        $existing_label = $this->em->getRepository('BL\Entity\WhiteLabel')->findOneBy(array('client' => $this->_getParam('client')));
        
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $headerUrl= explode("/",$this->getRequest()->getPost('url'));
            $slug=end($headerUrl);
            /*$form->getElement('url')->addValidator(new Zend_Validate_Db_NoRecordExists(array('table' => 'white_label', 'field' => 'url','exclude' => array(
				         					'field' => 'id',
				           					'value' => $existing_label->id))), true)
                ->addErrorMessage("URL already exist."); */
            
            if ($form->isValid($formData)) {
                /**
                 * Let's handle the upload first.
                 */
                $destination = APPLICATION_PATH . '/../assets/files/labels/';
                $uploader = new Zend_File_Transfer_Adapter_Http();
                $uploader->setDestination(realpath($destination));
                $uploaded = false;
                if ($uploader->receive()) {

                    /**
                     * Let's create thumbnails here
                     */
                    $uploaded = true;
                    $uploaded_file = $uploader->getFileInfo();
                    $fileName = $uploaded_file['header_image']['name'];
                    include("ThirdParty/PhpThumb/ThumbLib.inc.php");
                    $thumb_save_path = $destination . DIRECTORY_SEPARATOR . "thumbs" . DIRECTORY_SEPARATOR . "_thumb" . $fileName;
                    $thumb = PhpThumbFactory::create($destination . DIRECTORY_SEPARATOR . $fileName);
                    $thumb->resize(110, 110)->padding(110, 75, '#FFFFFF');
                    $thumb->save($thumb_save_path);
                } else {
                    $messages = $uploader->getMessages();
                }
                /**
                 * Now here we'll either add or edit the record based on the existing record which has client id.
                 */
                if (!sizeof($existing_label)) {
                    $label = new \BL\Entity\WhiteLabel();
                } else {
                    $label = $existing_label;
                }
                                
                $label->header_name = $form->getValue('header_name');
                $label->url = $slug;
                $label->header_file = $uploaded ? $fileName : $label->header_file;
                $label->client = $this->em->find("BL\Entity\User", (int) $this->_getParam('client'));
                $label->bg_color = $form->getValue('bg_color');
                $label->button_color = $form->getValue('button_color');
                $label->font_color = $form->getValue('font_color');
                $label->footer_color = $form->getValue('footer_color');
                $label->header_link = $form->getValue('header_link');
                $this->em->persist($label);
                $this->em->flush();
                
                $this->_helper->flashMessenger("White Label Saved", "Info");
                $this->_redirect('admin/portal/add-white-label/client/'.$this->_getParam('client'));
            }
        } else {
            
            $url='crm/client/wl-pages/';
            if (sizeof($existing_label)) {
                $this->view->existingUrl=$existing_label->url;
                //$url=$this->view->BUrl()->absoluteUrl('/').'crm/client/wl-pages/';
                $this->view->baseUrlLink=$this->view->BUrl()->absoluteUrl('/').$url;
                $this->view->label = $existing_label;
                $headerUrl=  strtolower($existing_label->client->organization_name);
                $headerUrl= str_replace(' ', '-', $headerUrl);
                $form->populate(array(
                    'header_name' => $existing_label->header_name,
                    'url' => ($existing_label->url <> "") ? $url.$existing_label->url : $url.$headerUrl,
                    'bg_color' => $existing_label->bg_color,
                    'font_color' => $existing_label->font_color,
                    'button_color' => $existing_label->button_color,
                    'footer_color' => $existing_label->footer_color,
                    'header_link' => ($existing_label->header_link <> "") ? $existing_label->header_link : '',
                ));
            } else {
                
                // It's a fresh add action with client preselected
                $client = $this->em->find('BL\Entity\User', (int) $this->_getParam("client", false));
                if (sizeof($client)) {
                    
                    $headerUrl=  strtolower($client->organization_name);
                    $headerUrl= str_replace(' ', '-', $headerUrl);
                    $url='crm/client/wl-pages/'.$headerUrl;
                    $form->getElement('url')->setValue($url);
                }
            }
        }
    }

    /**
     * Function to Get Label Record so that no label is overriden
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetLabelAction() {
        $this->_helper->BUtilities->setNoLayout();
        $label = $this->em->getRepository('BL\Entity\WhiteLabel')->findOneBy(array('client' => $this->_getParam('client')));
        $client = $this->em->find('BL\Entity\User', (int) $this->_getParam('client'));
        if (sizeof($label)) {
            echo Zend_Json::encode(array('exists' => 'yes', 'client_id' => $label->client_id->id));
            exit(0);
        } else {
            echo Zend_Json::encode(array('exists' => 'no', 'url' => "http://www.greeklicensing.com/clients/view/" . $this->view->BUrl()->url_title($client->organization_name, 'dash', true)));
            exit(0);
        }
    }

    /**
     * Function to get Clients for a banner in Ajax Popup
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetBannerClientsAction() {
        $this->_helper->BUtilities->setEmptyLayout();
        $this->view->banner = $this->em->find("BL\Entity\Banner", (int) $this->_getParam("id"));
    }

    /**
     * Function to delete a banner along with all it's resources
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function deleteBannerAction() {
        /**
         * Things to delete
         * Banner from banner table
         * Relational records [should be deleted automatically with cascade]
         * Images + Thumbs
         */
        $banner = $this->em->find("BL\Entity\Banner", (int) $this->_getParam('id'));

        @unlink(realpath(APPLICATION_PATH . "/../assets/files/banners/{$banner->image}"));
        @unlink(realpath(APPLICATION_PATH . "/../assets/files/banners/thumbs/_thumb{$banner->image}"));
        @unlink(realpath(APPLICATION_PATH . "/../assets/files/banners/thumbs/_thumb2{$banner->image}"));

        $this->em->remove($banner);
        $this->em->flush();
        $this->em->clear();

        $this->_helper->BUtilities->setNoLayout();
        $this->_helper->flashMessenger("Banner Deleted", "Info");
        $this->_redirect('admin/portal/banners');
    }
    
	/**
	 * Function to delete a user
	 * @author Jason Balazovich
	 * @copyright Softura inc.
	 * @version 1.0
	 * @access public
	 * @return String
	 */
    public function deleteUserAction(){
    	$user = $this->em->find("BL\Entity\User", (int)$this->_getParam('id'));
    	
    	$this->em->remove($user);
    	$this->em->flush();
    	$this->em->clear();
    	
    	$this->_helper->BUtilities->setNoLayout();
    	$this->_helper->flashMessenger("User Deleted", "Info");
    	$this->_redirect('admin/portal/users');
    }
    
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