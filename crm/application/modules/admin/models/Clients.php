<?php

/**
 * Description of Clients
 *
 * @author Medhad
 */
class Admin_Model_Clients {

    protected $ct;

    public function __construct(Zend_Controller_Action $ct) {
        $this->ct = $ct;
    }

    /**
     * Function to get licensed clients against status for data table
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getLicensedClients() {
        $params = array(
            'search' => $this->ct->getRequest()->getParam('sSearch', ''),
            'page_start' => $this->ct->getRequest()->getParam('iDisplayStart', 1),
            'draw_count' => $this->ct->getRequest()->getParam('sEcho', 1),
            'per_page' => $this->ct->getRequest()->getParam('iDisplayLength', 10),
            'status' => $this->ct->getRequest()->getParam('status', '3'),
        );
        /**
         * Let's take care of the sorting column to be passed to doctrine.
         * DataTable sends params like iSortCol_0.
         */
        $sorting_cols = array(
            '0' => 'c.organization_name',
            '1' => 'c.id'
        );

        $params['sort_key'] = $sorting_cols[$this->ct->getRequest()->getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->ct->getRequest()->getParam('sSortDir_0', 'asc');

        $records = $this->ct->em->getRepository("BL\Entity\License")->getLicensedClients($params);
        $params['show_total'] = true;
        $records_total = $this->ct->em->getRepository("BL\Entity\License")->getLicensedClients($params);

        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":';
        $prec = array();
        foreach ($records as $c) {

            $prec[] = array(
                '<a href="javascript:;" class="client_license_link" rel="' . $c['id'] . '">' . str_replace(chr(13), '', str_replace(chr(10), "", $c['organization_name'])) . '</a>',
                $c['total']
            );
        }
        $json .= Zend_Json::encode($prec);
        $json .= '}';

        return $json;
    }

    /**
     * Function to get lincenses for clients
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getLicenses() {
        $params = array(
            'search' => $this->ct->getRequest()->getParam('sSearch', ''),
            'page_start' => $this->ct->getRequest()->getParam('iDisplayStart', 1),
            'draw_count' => $this->ct->getRequest()->getParam('sEcho', 1),
            'per_page' => $this->ct->getRequest()->getParam('iDisplayLength', 10),
            'client_id' => $this->ct->getRequest()->getParam('client', ''),
            'status' => $this->ct->getRequest()->getParam('status', '3'),
        );
        /**
         * Let's take care of the sorting column to be passed to doctrine.
         * DataTable sends params like iSortCol_0.
         */
        $sorting_cols = array(
            '0' => 'c.organization_name',
            '1' => 'v.organization_name',
            '2' => 'l.applied_date',
            '3' => 'l.status');
        $params['sort_key'] = $sorting_cols[$this->ct->getRequest()->getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->ct->getRequest()->getParam('sSortDir_0');

        $records = $this->ct->em->getRepository("BL\Entity\License")->getLicensesByClient($params);
        $params['show_total'] = true;
        $records_total = $this->ct->em->getRepository("BL\Entity\License")->getLicensesByClient($params);
        $status_array = $this->ct->getHelper('BUtilities')->parseYAML(APPLICATION_PATH . '/configs/statuses.yml');
        /**
         * Datatable doesn't understand json_encode and have a custom json format
         */
        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":';
        $prec = array();
        foreach ($records as $v) {
            $prec[] = array(
                $v->client_id->organization_name,
                '<a href="javascript:;" class="vendor_link" rel="' . $v->vendor_id->id . '">' . $v->vendor_id->organization_name . '</a>',
                $v->applied_date->format("m-d-Y"),
                '<a href="javascript:;" class="lic_link" rel="' . $v->id . '">' . $status_array[$v->status] . '</a>'
            );
        }
        $json .= Zend_Json::encode($prec);
        $json .= '}';

        return $json;
    }

    /**
     * Function to serve contact action for clients
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getContacts() {
        $this->ct->view->clientContacts = $this->ct->em->getRepository('BL\Entity\UserContact')->findBy(array('user_id' => (int) $this->ct->client->id));
    }

    /**
     * Function to edit contacts
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function editContact() {
        $this->ct->getHelper('BUtilities')->setEmptyLayout();
        $form = new Admin_Form_UserContact();
        $this->ct->view->form = $form;
        $userContact = $this->ct->em->getRepository('BL\Entity\UserContact')->findOneBy(array('id' => (int) $this->ct->getRequest()->getParam('id')));

        if ($this->ct->getRequest()->isPost()) {
            $formData = $this->ct->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $userContact->sal = $form->getValue('sal');
                $userContact->first_name = $form->getValue('first_name');
                $userContact->last_name = $form->getValue('last_name');
                $userContact->title = $form->getValue('title');
                $userContact->address_line1 = $form->getValue('address_line1');
                $userContact->city = $form->getValue('city');
                $userContact->email = $form->getValue('email');
                $userContact->state = $form->getValue('state');
                $userContact->zipcode = $form->getValue('zipcode');
                $userContact->phone = $form->getValue('phone');
                $userContact->phone_ext = $form->getValue('phone_ext');
                $userContact->mobile = $form->getValue('mobile');
                $userContact->contact_type = $form->getValue('contact_type');
                $this->ct->em->persist($userContact);
                $this->ct->em->flush();
                $this->ct->view->msg = "Contact updated succesfully!";
            }
        } else {
            $existing_data = array(
                'sal' => $userContact->sal,
                'first_name' => $userContact->first_name,
                'last_name' => $userContact->last_name,
                'title' => $userContact->title,
                'address_line1' => $userContact->address_line1,
                'city' => $userContact->city,
                'email' => $userContact->email,
                'state' => $userContact->state,
                'zipcode' => $userContact->zipcode,
                'phone' => $userContact->phone,
                'phone_ext' => $userContact->phone_ext,
                'mobile' => $userContact->mobile,
                'contact_type' => $userContact->contact_type
            );
            $form->populate($existing_data);
        }
    }

    /**
     * Function to edit contacts
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function addContact() {
        $this->ct->getHelper('BUtilities')->setEmptyLayout();
        $form = new Admin_Form_UserContact();
        $this->ct->view->form = $form;
        if ($this->ct->getRequest()->isPost()) {
            $formData = $this->ct->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $userContact = new BL\Entity\UserContact();
                $userContact->sal = $form->getValue('sal');
                $userContact->first_name = $form->getValue('first_name');
                $userContact->last_name = $form->getValue('last_name');
                $userContact->title = $form->getValue('title');
                $userContact->address_line1 = $form->getValue('address_line1');
                $userContact->city = $form->getValue('city');
                $userContact->email = $form->getValue('email');
                $userContact->state = $form->getValue('state');
                $userContact->zipcode = $form->getValue('zipcode');
                $userContact->phone = $form->getValue('phone');
                $userContact->phone_ext = $form->getValue('phone_ext');
                $userContact->mobile = $form->getValue('mobile');
                $userContact->contact_type = $form->getValue('contact_type');
                $userContact->user_id = $this->ct->em->find("BL\Entity\User", (int) $this->ct->client->id);
                $this->ct->em->persist($userContact);
                $this->ct->em->flush();
                $this->ct->view->msg = "Contact added succesfully!";
            }
        }
    }

    /**
     * Function to change client password
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function changePassword() {
        $client = $this->ct->em->getRepository('BL\Entity\User')->findOneBy(array('id' => (int) $this->ct->getRequest()->getParam('id'), 'account_type' => (int) ACC_TYPE_CLIENT));
        $form = new Admin_Form_ChangePassword(is_null($client->username) ? true : false);

        if ($this->ct->getRequest()->isPost()) {
            $formData = $this->ct->getRequest()->getPost();        
            if (strcmp($client->username, trim($formData['username']))) {
                $form = new Admin_Form_ChangePassword(true);
                $form->populate($formData);
            }
            if ($form->isValid($formData)) {
                $client->username = $form->getValue('username');
                $client->password = hash('MD5', $form->getValue('password'));
                if ($client->user_status == "Current") {
                    $client->reg_status = "activated";
                }
                $role = $this->ct->em->getRepository('BL\Entity\Role')->findOneBy(array('id' => (int) ACC_TYPE_CLIENT));
                if (sizeof($client->roles)) {
                    $client->roles->set($role);
                } else {
                    $client->roles->add($role);
                }
                $this->ct->em->persist($client);
                $this->ct->em->flush();
                $this->ct->em->clear();
                $this->ct->getHelper('flashMessenger')->direct("Password changed successfully!", "Info");
                $this->ct->view->BUrl()->redirect('admin/clients/view/id/' . $client->id);
            } else {
                $form->populate($formData);
            }
        } else {
            $form->populate(array('username' => $client->username, 'recovery_email' => $client->email));
        }
        $this->ct->view->form = $form;
        $this->ct->view->client = $client;
    }

}
