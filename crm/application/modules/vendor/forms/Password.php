<?php

class Vendor_Form_Password extends Zend_Form {

    public function init() {

        $this->setName('account_setting_form');
        $this->setAttrib('enctype', 'multipart/form-data');

        $old_password = new Zend_Form_Element_Password('old_password');
        $old_password->setLabel('Enter Old Password')
                ->setDisableLoadDefaultDecorators(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter Old Password'))))
                ->setAttrib('class', 'text')
                ->setAttrib('size', '50')
                ->setRequired(true);

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Enter New Password')
                ->setDisableLoadDefaultDecorators(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter new Password'))))
                ->setAttrib('class', 'text')
                ->setAttrib('size', '50')
                ->setRequired(true)
                ->addValidator('StringLength', false,
                        array('min' => 6,
                            'max' => 15,
                            'messages' => array(
                                Zend_Validate_StringLength::INVALID =>
                                'Password must contain between %min% and %max% characters',
                                Zend_Validate_StringLength::TOO_LONG =>
                                'Password cannot contain more than %max% characters',
                                Zend_Validate_StringLength::TOO_SHORT =>
                                'Password must contain more than %min% characters')));

        $repassword = new Zend_Form_Element_Password('repassword');
        $repassword->setLabel('Re-type New Password')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttrib('class', 'text')
                ->setAttrib('size', '50')
                ->setRequired(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please re-type Password')),
                                    array('identical', false, array('token' => 'password', 'messages' => 'Password missmatched'))));

       
        $submit = new Zend_Form_Element_Button('Submit');

        $submit->setAttrib('id', 'submit')
                ->setAttrib('type', 'submit')
                ->setAttrib('class', 'button button_black')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'));

        $this->addElements(array(
            $old_password, $password, $repassword,
            $submit
        ));
    }

}
