<?php

class Vendor_Form_Signup extends Zend_Form {

    public function init() {

        $this->setName('vendor_add');
        $this->setAttrib('enctype', 'multipart/form-data');

        /*$account_type = new Zend_Form_Element_Radio('account_type');
        $account_type->setLabel('Account type')
                ->setAttrib('class', 'radio')
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter account type'))))
                ->setRequired()
                ->setDecorators(array('ViewHelper', 'Errors'));
        */

        $organization_name = new Zend_Form_Element_Text('organization_name');
        $organization_name->setLabel('Organization Name')
                        ->setAttrib('size', '50')
                        ->setAttrib('class', 'text')
                        ->setDisableLoadDefaultDecorators(true)
                        ->setDecorators(array('ViewHelper', 'Errors'))
                        ->addFilter('StripTags')
                        ->addFilter('StringTrim');
        $first_name = new Zend_Form_Element_Text('first_name');
        $first_name->setLabel('First name')
                ->setRequired(true)
                ->setAttrib('size', '50')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter first name'))))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');


        $last_name = new Zend_Form_Element_Text('last_name');
        $last_name->setLabel('Last Name')
                ->setRequired(true)
                ->setAttrib('size', '50')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter last name'))))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');


        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email address')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter email'))))
                ->setAttrib('class', 'text')
                ->setAttrib('size', '50')
                ->setAttrib('onBlur', 'check_email()')
                ->addFilter('StringToLower')
                ->setRequired(true)
                ->addValidator('EmailAddress');


        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
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
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setValidators(array())
                ->setAttrib('class', 'text')
                ->setAttrib('size', '50')
                ->setRequired(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please confirm Password')),
                                    array('identical', false, array('token' => 'password', 'messages' => 'Password missmatched'))));



        $username = new Zend_Form_Element_Text('username');
        $username->setLabel('Username')
                ->setAttrib('size', '50')
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'username')
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter a username'))))
                ->setRequired(true)
                ->setAttrib('onBlur', 'check_username()')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('Alnum', false, array('allowWhiteSpace' => false, 'messages' => array(Zend_Validate_Alnum::NOT_ALNUM=> "Username can contain only AlphaNumeric characters allowed. No spaces")))
                ->addValidator('StringLength', false, array('min' => 5,
                    'max' => 100
                ));

        $address_line1 = new Zend_Form_Element_Text('address_line1');
        $address_line1->setLabel('Address')
                //->setRequired(true)
                ->setAttrib('size', '70')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->addFilter('StripTags')
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->addFilter('StringTrim');

        $address_line2 = new Zend_Form_Element_Text('address_line2');
        $address_line2->setLabel('Address')
                ->setRequired(false)
                ->setAttrib('size', '70')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->addFilter('StripTags')
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->addFilter('StringTrim');

        $city = new Zend_Form_Element_Text('city');
        $city->setLabel('City')
               // ->setRequired(true)
                ->setAttrib('size', '70')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->addFilter('StripTags')
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->addFilter('StringTrim');

        $state = new Zend_Form_Element_Text('state');
        $state->setLabel('State')
                //->setRequired(true)
                ->setAttrib('size', '20')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->addFilter('StripTags')

                ->setDecorators(array('ViewHelper', 'Errors'))
                ->addFilter('StringTrim');

        $phone = new Zend_Form_Element_Text('phone');
        $phone->setLabel('Phone')
                ->setAttrib('size', '70')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->addFilter('StripTags')
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->addFilter('StringTrim');

        $fax = new Zend_Form_Element_Text('fax');
        $fax->setLabel('Fax')
                ->setRequired(false)
                ->setAttrib('size', '70')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->addFilter('StripTags')
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->addFilter('StringTrim');

        $zipcode = new Zend_Form_Element_Text('zipcode');
        $zipcode->setLabel('Zip code')
                // ->setRequired(true)
                ->setAttrib('size', '20')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                //->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter zipcode'))))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $website = new Zend_Form_Element_Text('website');
        $website->setLabel('Website')
                ->setAttrib('size', '50')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $secret_question = new Zend_Form_Element_Text('secret_question');
        $secret_question->setLabel('Secret Question')
                ->setRequired(false)
                ->setAttrib('size', '50')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                //->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter zipcode'))))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $secret_answer = new Zend_Form_Element_Text('secret_answer');
        $secret_answer->setLabel('Secret Answer')
                ->setRequired(false)
                ->setAttrib('size', '50')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                //->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter zipcode'))))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $verification = new Zend_Form_Element_Captcha('verification', array(
                    'label' => "Type the code shown",
                    'required' => false,
                    'class' => 'text captcha',
                    'captcha' => array(
                        'captcha' => 'image',
                        'name' => 'verification',
                        'wordLen' => 1,
                        'font' => realpath(dirname(__FILE__).'/../../../../').'/assets/fonts/droidsans-webfont.ttf',
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


        $this->addElements(array($organization_name, $first_name, $last_name, $email, $username, $password,
                                $customer_confirm_password, $secret_question, $secret_answer,
                                $zipcode, $city, $state, $address_line1, $address_line2, $website,
                                $phone, $fax, $verification, $submit))
                ->setDecorators(array(
                    'ViewHelper',
                    array('FormErrors', array('placement' => 'PREPEND')),
                    'Form'
                ));
    }

}
