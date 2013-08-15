<?php

class Admin_Form_AddUser extends Zend_Form {

    private $action;
    
    public function __construct($options = null) {
        $this->action = $options;
        parent::__construct($options);
    }


    public function init() {
        $this->setName('add_user');

        $username = new Zend_Form_Element_Text('username');
        $username->setLabel('Username')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(true)                
                ->addValidator(new Zend_Validate_Db_NoRecordExists(array('table' => 'users', 'field' => 'username')), true, array('messages' => 'Username already exists.'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(true)                
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $confirm_password = new Zend_Form_Element_Password('confirm_password');
        $confirm_password->setLabel('Re-type Password')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(true)                
                ->addValidator('Identical', false, array('token' => 'password'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(true)                
                ->addValidator('EmailAddress', true, array('messages' => 'Invalid email address'))
                ->addValidator(new Zend_Validate_Db_NoRecordExists(array('table' => 'users', 'field' => 'email')), true, array('messages' => 'Email already exists'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $first_name = new Zend_Form_Element_Text('first_name');
        $first_name->setLabel('First name')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('class' => 'text'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $last_name = new Zend_Form_Element_Text('last_name');
        $last_name->setLabel('Last name')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('class' => 'text'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $account_type = new Zend_Form_Element_Radio('account_type');
        $account_type->setLabel('User Type')
                ->setAttrib('class', 'radio')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->setMultiOptions(array('1' => ' Admin', '4' => ' Employee'));

        $status = new Zend_Form_Element_Radio('status');
        $status->setLabel('Status')
                ->setAttrib('class', 'radio')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->setMultiOptions(array('1' => ' Active', '2' => ' In-active'));

        if($this->action == 'edit'){
            $username->removeValidator('Zend_Validate_Db_NoRecordExists');
            $username->setRequired(false);
            $email->setRequired(false);
            $email->removeValidator('Zend_Validate_Db_NoRecordExists');
            $first_name->setRequired(true);
            $last_name->setRequired(true);    
            $password->setRequired(false);
            $confirm_password->setRequired(false);        
        }
        
        $this->addElements(array(
            $username,
            $password,
            $confirm_password,
            $email,
            $first_name,
            $last_name,
            $account_type,
            $status
        ));
    }

}
