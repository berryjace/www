<?php

/**
 * Admin_Model_Portal is to process vendor portal controller actions
 *
 * @author Masud
 */
class Admin_Model_Portal {

    protected $ct;

    public function __construct(Zend_Controller_Action $ct) {
        $this->ct = $ct;
    }

    /**
     * Function to add AMC new user
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function addUser() {

        $form = new Admin_Form_AddUser();
        $this->ct->view->form = $form;

        if ($this->ct->getRequest()->isPost()) {
            $formData = $this->ct->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $user = new \BL\Entity\User();
                $user->account_type = (int) $form->getValue('account_type');
                $user->first_name = $form->getValue('first_name');
                $user->last_name = $form->getValue('last_name');
                $user->username = $form->getValue('username');
                $user->password = md5($form->getValue('password'));
                $user->email = $form->getValue('email');
                if ($form->getValue('status') === '1') {
                    $user->user_status = 'Current';
                    $user->reg_status = 'activated';
                } else {
                    $user->user_status = 'Cancelled';
                }
                $role = $this->ct->em->getRepository('BL\Entity\Role')->findOneBy(array('id' => (int) ACC_TYPE_ADMIN));  //$form->getValue('account_type')
                $user->roles->add($role);
                $this->ct->em->persist($user);
                $this->ct->em->flush();

                $this->ct->getHelper('flashMessenger')->direct("User added successfully!", "Info");
                $this->ct->view->BUrl()->redirect('admin/portal/users');
            } else {
                $form->populate($formData);
            }
        }
    }

    /**
     * Function to ajax get admin users json data
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetUsers() {
//        print_r($this->ct->getRequest()->getParams());
        $params = array(
            'search' => $this->ct->getRequest()->getParam('sSearch', ''),
            'page_start' => $this->ct->getRequest()->getParam('iDisplayStart', 1),
            'draw_count' => $this->ct->getRequest()->getParam('sEcho', 1),
            'per_page' => $this->ct->getRequest()->getParam('iDisplayLength', 25),
        );

        /**
         * Let's take care of the sorting column to be passed to doctrine.
         * DataTable sends params like iSortCol_0.
         */
        $sorting_cols = array('0' => 'u.username', '2' => 'u.email', '3' => 'u.account_type', '4' => 'u.reg_status', '5' => 'u.last_login');
        $params['sort_key'] = $sorting_cols[$this->ct->getRequest()->getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->ct->getRequest()->getParam('sSortDir_0');

        $records = $this->ct->em->getRepository("BL\Entity\User")->getAdminAndEmployees($params);
        $params['show_total'] = true;
        $records_total = $this->ct->em->getRepository("BL\Entity\User")->getAdminAndEmployees($params);
//        $status_array = $this->ct->getHelper('BUtilities')->parseYAML(APPLICATION_PATH . '/configs/user_status.yml');
        $user_types = $this->ct->getHelper('BUtilities')->parseYAML(APPLICATION_PATH . '/configs/user_types.yml');
        /**
         * Datatable doesn't understand json_encode and have a custom json format
         */
        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":';
        $prec = array();
        foreach ($records as $u) {
            $prec[] = array(
                $u->username,
                $u->first_name . ' ' . $u->last_name,
                $u->email,
                $user_types[$u->account_type],
                trim($u->reg_status) === 'activated' ? 'Active' : ' In-active',
                (!is_null($u->last_login) ? $u->last_login->format("M d, Y H:i A") : 'N/A'),
                '<a href="javascript:;" class="user_details_link" rel="' . $u->id . '">View & Edit</a>
            		&nbsp;<a class="delete user_delete_link" href="' . $this->ct->view->baseUrl("admin/portal/delete-user/id/{$u->id}") . '">Delete</a>'
            );
        }
        $json .= Zend_Json::encode($prec);
        $json .= '}';
        echo $json;
    }

    /**
     * Function to view user details and edit user information
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function userDetails() {
        $form = new Admin_Form_AddUser('edit');
        $this->ct->view->form = $form;

        $user = $this->ct->em->getRepository('BL\Entity\User')->findOneBy(array('id' => (int) $this->ct->getRequest()->getParam('id')));

        if ($this->ct->getRequest()->isPost()) {
        	
            $formData = $this->ct->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $user->account_type = (int) $form->getValue('account_type');
                $user->first_name = $form->getValue('first_name');
                $user->last_name = $form->getValue('last_name');
//                $user->username = $form->getValue('username');
				if ($form->getValue('password') != '' && $form->getValue('password') != null){
						$user->password = md5($form->getValue('password'));
				}
				
				if ($form->getValue('status') === '1') {
                    $user->user_status = 'Current';
                    $user->reg_status = 'activated';
                } else {
                    $user->user_status = 'Cancelled';
                    $user->reg_status = 'Declined';
                }
                
                $role = $this->ct->em->getRepository('BL\Entity\Role')->findOneBy(array('id' => (int) ACC_TYPE_ADMIN));  // $form->getValue('account_type')
                $user->roles->set($role);      

                $this->ct->em->persist($user);
                $this->ct->em->flush();

                $this->ct->getHelper('flashMessenger')->direct("User updated successfully!", "Info");
                $this->ct->view->BUrl()->redirect('admin/portal/users');
            } else {
                $form->populate($formData);
            }
        } else {
            $existing_data = array(
                'account_type' => $user->account_type,
                'username' => $user->username,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email
            );
            if ($user->reg_status == 'activated') {
                $existing_data['status'] = '1';
            } else {
                $existing_data['status'] = '2';
            }
            $form->populate($existing_data);
        }
    }

}