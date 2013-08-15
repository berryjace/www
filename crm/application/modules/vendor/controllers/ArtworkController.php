<?php

class Vendor_ArtworkController extends Zend_Controller_Action {

    public function init() {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
        $this->_helper->JSLibs->load_fancy_assets(); //added for vendor sidebar event calendar
    }

    /**
     * Function to view client artworks
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     */
    public function indexAction() {
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets'));
        $params = array(
            'pg' => $this->_getParam('page', 1),
            'itemPerPage' => 10,
            'num_of_link' => 10,
            'User' => $this->_getParam('id')
        );

        $this->view->artworks = $this->em->getRepository("BL\Entity\ClientArtwork")->getArtworks($params);
        $this->view->user = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' => (int) $this->_getParam('id')));
        $this->view->lic = $this->em->getRepository("BL\Entity\License")->findOneBy(array('client_id' => $this->_getParam('id'), 'vendor_id' => (int) $this->_helper->BUtilities->getLoggedInUser()));
        
        if (is_null($this->view->lic)){
        	$this->view->lic = new BL\Entity\License();
        }
	//$this->view->lic = ;
        //$this->view->BUtils()->doctrine_dump($this->view->lic);              
    }

    /**
     * Function to list view artwork
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function viewArtworkAction() {
        $this->_helper->BUtilities->setAjaxLayout();
        $this->_helper->JSLibs->load_jqui_assets();
        $this->view->artwork = $this->em->getRepository("BL\Entity\ClientArtwork")->findOneBy(array('id' => $this->_getParam('id'), 'User' => $this->_getParam('user_id')));
        //print_r($this->view->artwork);
    }

    /**
     * Function to view usage guide of a particular client
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function usageGuidesAction() {
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets'));
        $this->view->user = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' => (int) $this->_getParam('id')));
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
        $json = $this->em->getRepository('BL\Entity\ClientUsageGuide')->getUsageGuidesForVendor($params);
        echo $json;
    }

    public function clientProfileAction() {
        $this->_helper->JSLibs->load_fancy_assets();
        $this->_helper->JSLibs->load_jqui_aristo();
        
        $client = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' => (int) $this->_getParam('id'), 'account_type' => ACC_TYPE_CLIENT));       
        $clientProfile = $this->em->getRepository('BL\Entity\ClientProfile')->findOneBy(array('user_id' => (int) $this->_getParam('id')));
        $this->view->user = $client;
        
        $options = array();
        $org_type[''] = 'Select Type';
        $org_list = $this->em->getRepository("BL\Entity\OrganizationType")->findAll();
        foreach ($org_list as $org){
            $org_type[$org['id']] = $org['name'];
        }
        $options['org_type'] = $org_type;
        $options['greek_org_type'] = !is_null($clientProfile->greek_org_type)?$clientProfile->greek_org_type:'0';

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
            $existing_data['greek_name'] = $clientProfile->greek_name;
            $existing_data['greek_org_type'] = $clientProfile->greek_org_type->id;
            $existing_data['greek_founding_year'] = ($clientProfile->greek_founding_year->format('Y')>0)?$clientProfile->greek_founding_year->format('m/d/Y'):'';
            $existing_data['greek_number_of_alumni'] = $clientProfile->greek_number_of_alumni;
            $existing_data['greek_number_of_undergrads'] = $clientProfile->greek_number_of_undergrads;
            $existing_data['greek_number_of_alumni_chapters'] = $clientProfile->greek_number_of_alumni_chapters;
            $existing_data['greek_total_ug_chapters'] = $clientProfile->greek_total_ug_chapters;
            //$existing_data['profile_status_update_time'] = $clientProfile->profile_status_update_time->format('Y-m-d');
            $existing_data['symbol'] = $clientProfile->symbol;
            $existing_data['founding_address'] = $clientProfile->founding_address;
            $existing_data['founding_address_line1'] = $clientProfile->founding_address_line1;
            $existing_data['founding_address_line2'] = $clientProfile->founding_address_line2;
            $existing_data['founding_city'] = $clientProfile->founding_city;
            $existing_data['founding_state'] = $clientProfile->founding_state;
            $existing_data['headquarters_city'] = $clientProfile->headquarters_city;
            $existing_data['headquarters_state'] = $clientProfile->headquarters_state;
        }
        $form->populate($existing_data);
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

}

