<?php

/**
 * Description of contact
 *
 * @author Masud
 */
class Vendor_Model_Contact {
    protected $ct;

    public function __construct(Zend_Controller_Action $ct) {
        $this->ct = $ct;
    }        
    
    /**
     * Function to update vendors contact
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function editContact() {
        $this->ct->getHelper('BUtilities')->setEmptyLayout();
        $form = new Admin_Form_UserContact();
        $this->ct->view->form = $form;
        $userContact = $this->ct->em->getRepository('BL\Entity\UserContact')->findOneBy(array('id' => (int) $this->ct->getRequest()->getParam('id'), 'user_id' => (int) $this->ct->getHelper('BUtilities')->getLoggedInUser()));

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
                    $userContact->fax = $form->getValue('fax');
                    $userContact->contact_type = $form->getValue('contact_type');
                    $this->ct->em->persist($userContact);
                    $this->ct->em->flush();                    
                    $this->ct->view->msg = "Contact updated succesfully!";
//                    $this->ct->getHelper('flashMessenger')->direct("Contact updated succesfully!", "Info");
//                    $this->ct->view->BUrl()->redirect($this->ct->view->BUrl()->absoluteUrl());                                        
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
                'fax' => $userContact->fax,
                'contact_type' => $userContact->contact_type
            );
//        print_r($existing_data);
            $form->populate($existing_data);
        }
    }
}

?>
