<?php

class Admin_Form_ChangePassword extends Zend_Form {
    
    private $bool;
    
    public function __construct($options=false) {
        $this->bool = $options;
        parent::__construct($options);
    }
    
    public function init() {

        $this->setName('account_setting_form');
        $this->setAttrib('enctype', 'multipart/form-data');

        $username = new Zend_Form_Element_Text('username');
        $username->setLabel('Username')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(true)                
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        if($this->bool){
            $username->setValidators(array(
                    array(new Zend_Validate_Db_NoRecordExists(
                                array(
                                    'table' => 'users',
                                    'field' => 'username')
                        ), true, array('messages' => 'Username already exists'))
                ));
        }
        
        $recovery_email = new Zend_Form_Element_Text('recovery_email');
        $recovery_email->setLabel('Recovery Email')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttrib('class', 'text desable')
                ->setAttrib('size', '50')
                ->setRequired(false);

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Enter New Password')
                ->setDisableLoadDefaultDecorators(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter new Password'))))
                ->setAttrib('class', 'text')
                ->setAttrib('size', '50')
                ->setRequired(true)
                ->addValidator('StringLength', false, array('min' => 6,
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
            $username,
            $recovery_email,
            $password,
            $repassword,
            $submit
        ));
    }

}

