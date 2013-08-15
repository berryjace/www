<?php

class Application_Form_Customer extends Zend_Form {

    public function init() {

        $this->setName('customer_add');
        $this->setAttrib('enctype', 'multipart/form-data');

        $account_type = new Zend_Form_Element_Radio('account_type');
        $account_type->setLabel('Account type')
                ->setAttrib('class', 'radio')
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter account type'))))
                ->setRequired()
                ->setDecorators(array('ViewHelper', 'Errors'));
        $account_type->setMultiOptions(array('0' => 'Individual', '1' => 'Organization'));

        $organization_name = new Zend_Form_Element_Text('organization_name');
        $organization_name->setLabel('Organization Name')
                        ->setAttrib('size', '50')
                        ->setAttrib('class', 'text')
                        ->setDisableLoadDefaultDecorators(true)
                        ->addFilter('StripTags')
                        ->addFilter('StringTrim');
        $customer_first_name = new Zend_Form_Element_Text('customer_first_name');
        $customer_first_name->setLabel('First name')
                ->setRequired(true)
                ->setAttrib('size', '50')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors', array('Label', array('tag' => 'dt'))))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter first name'))))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');


        $customer_last_name = new Zend_Form_Element_Text('customer_last_name');
        $customer_last_name->setLabel('Last Name')
                ->setRequired(true)
                ->setAttrib('size', '50')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors', array('Label', array('tag' => 'dt'))))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter last name'))))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');


        $customer_email = new Zend_Form_Element_Text('customer_email');
        $customer_email->setLabel('Email address')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors', array('Label', array('tag' => 'dt'))))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter email'))))
                ->setAttrib('class', 'text')
                ->setAttrib('size', '50')
                ->setAttrib('onBlur', 'check_email()')
                ->addFilter('StringToLower')
                ->setRequired(true)
                ->addValidator('EmailAddress');


        $customer_password = new Zend_Form_Element_Password('customer_password');
        $customer_password->setLabel('Password')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors', array('Label', array('tag' => 'dt'))))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter Password'))))
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

        $customer_confirm_password = new Zend_Form_Element_Password('customer_confirm_password');
        $customer_confirm_password->setLabel('Confirm Password')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors', array('Label', array('tag' => 'dt'))))
                ->setValidators(array())
                ->setAttrib('class', 'text')
                ->setAttrib('size', '50')
                ->setRequired(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please confirm Password')),
                                    array('identical', false, array('token' => 'customer_password', 'messages' => 'Password missmatched'))));



        $customer_sub_domain = new Zend_Form_Element_Text('username');
        $customer_sub_domain->setLabel('Username')
                ->setAttrib('size', '50')
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'customer_username')
                ->setDecorators(array('ViewHelper', 'Errors', array('Label', array('tag' => 'dt'))))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter a username'))))
                ->setRequired(true)
                ->setAttrib('onBlur', 'check_username()')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                    ->addValidator('Alnum', false, array('allowWhiteSpace' => false, 'messages' => array(Zend_Validate_Alnum::NOT_ALNUM=> "Username can contain only AlphaNumeric characters allowed. No spaces")))
                ->addValidator('StringLength', false, array('min' => 5,
                    'max' => 100
                ));


        $verification = new Zend_Form_Element_Captcha('verification', array(
                    'label' => "Type the code shown",
                    'required' => true,
                    'class' => 'text captcha',
                    'captcha' => array(
                        'captcha' => 'image',
                        'name' => 'verification',
                        'wordLen' => 6,
                        'font' => 'assets/fonts/DroidSans.ttf',
                        'fontSize' => 22,
                        'height'=>50,
                        'dotNoiseLevel'=>15,
                        'lineNoiseLevel'=>1,
                        'imgDir' => 'assets/images/captcha',
                        'imgUrl' => $this->getView()->baseUrl('assets/images/captcha'),
                        'timeout' => 300)
                ));

        $submit = new Zend_Form_Element_Submit('submit');

        $submit->setAttrib('id', 'submit');
        $submit->setAttrib('class', 'large awesome');
        $submit->setAttrib('value', 'Sign Up');


        $this->addElements(array($account_type, $organization_name, $customer_first_name, $customer_last_name, $customer_email, $customer_password, $customer_confirm_password, $customer_sub_domain, $verification, $submit))
                ->setDecorators(array(
                    'ViewHelper',
                    array('FormErrors', array('placement' => 'PREPEND')),
                    'Form'
                ));
    }

}
