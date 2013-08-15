<?php

class Admin_IndexController extends Zend_Controller_Action {

    protected $em;

    public function init() {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
    }

    public function indexAction() {
        $user_id = $this->_helper->BUtilities->getLoggedInUser();
        $this->user = $this->em->getRepository("BL\Entity\User")->find((int) $user_id);
        //$this->view->usr = $user_id;
        $this->_forward("dashboard");
    }

    /**
     * Function to show dashboard of the admin
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function dashboardAction() {
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets'));
    }

    /**
     * Function to provide JSON data to feed data table
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetLicensesDtAction() {
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
        $sorting_cols = array('0' => 'v.organization_name', '1' => 'c.organization_name', '2' => 'l.applied_date', '3' => 'l.status');
        $params['sort_key'] = $sorting_cols[$this->_getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->_getParam('sSortDir_0');

        $records = $this->em->getRepository("BL\Entity\License")->getLicenses($params)->getResult();
        $params['show_total'] = true;
        $records_total = $this->em->getRepository("BL\Entity\License")->getLicenses($params);
        $status_array = $this->_helper->BUtilities->parseYAML(APPLICATION_PATH . '/configs/statuses.yml');
        $this->_helper->BUtilities->setNoLayout();
        /**
         * Datatable doesn't understand json_encode and have a custom json format
         */
        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":';
        $first = 0;
        $prec = array();
        foreach ($records as $v) {
            $prec[] = array(
                '<a href="javascript:;" class="vendor_link" rel="' . $v->vendor_id->id . '">' . $v->vendor_id->organization_name . '</a>',
                $v->client_id->organization_name,
                (($v->applied_date->format("Y") > 1970) ? $v->applied_date->format("M d, Y H:i A") : 'N/A'),
                '<a href="javascript:;" class="lic_link" rel="' . $v->id . '">' . (isset($status_array[$v->status]) ? $status_array[$v->status] : $v->status) . '</a>'
            );
        }
        $json.=Zend_Json::encode($prec);
        $json .= '}';
        echo $json;
    }

    public function ajaxLicenseStatusAction() {
        
    }

    public function searchAction() {
        // action body
    }

    public function filterAction() {
        // action body
    }

    public function licenseStatusAction() {
        // action body
    }

    /**
     * Function to change a user's password
     * @author Sukhon added by Masud
     * @copyright Blueliner Marketing
     * @access public
     * @return String
     */
    public function settingAction() {
        $user_id = $this->_helper->BUtilities->getLoggedInUser();
        $this->view->user_details = $this->em->getRepository("BL\Entity\User")->find((int) $user_id);
        if (!sizeof($this->view->user_details)) {
            throw new Exception('Sorry, but you made an invalid page request', 404);
            exit(0);
        }

        $profile = $this->view->user_details;
        $new_formdata = array();
        $form = new Admin_Form_Password();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                if ($profile->password == hash('MD5', trim($form->getValue('old_password')))) {
                    $profile->password = hash('MD5', trim($form->getValue('password')));
                    $this->em->persist($profile);
                    $this->em->flush();
                    $this->em->clear();
                    $this->_helper->flashMessenger("Password changed successfully!", "Info");
                    $this->_redirect('admin/index');
                } else {
                    $form->markAsError();
                    $form->old_password->addError('Old password does not matched');
                }
            }
        }
    }
}

