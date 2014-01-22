<?php

/**
 * Description of UserContact
 *
 * @author Masud
 */
class Admin_Form_UserContact extends Zend_Form{

    public function __construct($options = null) {
        parent::__construct($options);
    }

    public function init(){
        $this->setName('user_contact_form');
        $this->setAttrib('enctype', 'multipart/form-data');

        $sal = new Zend_Form_Element_Select('sal');
        $sal->setLabel('Sal.')
                ->setDisableLoadDefaultDecorators(true)
 //               ->addErrorMessage('Required')
                ->setAttribs(array('class' => 'elWidth','size'=>'1','maxlength'=>'4'))
                ->setRequired(true)
                ->addMultiOptions(array("Mr."=>"Mr.",
                                        "Mrs."=>"Mrs.",
                                        "Ms."=>"Ms."
                                        ))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $first_name = new Zend_Form_Element_Text('first_name');
        $first_name->setLabel('First name')
                ->setDisableLoadDefaultDecorators(true)
                ->addErrorMessage('Required')
                ->setAttribs(array('class' => 'text', 'size' => '50'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $last_name = new Zend_Form_Element_Text('last_name');
        $last_name->setLabel('Last name')
                ->setDisableLoadDefaultDecorators(true)
                ->addErrorMessage('Required')
                ->setAttribs(array('class' => 'text', 'size' => '50'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Title')
                ->setDisableLoadDefaultDecorators(true)
 //              ->addErrorMessage('Required')
                ->setAttribs(array('class' => 'text', 'size' => '50'))
                ->setRequired(False)

                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $address_line1 = new Zend_Form_Element_Text('address_line1');
        $address_line1->setLabel('Address')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $city = new Zend_Form_Element_Text('city');
        $city->setLabel('City')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $state = new Zend_Form_Element_Text('state');
        $state->setLabel('State')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $zipcode = new Zend_Form_Element_Text('zipcode');
        $zipcode->setLabel('Zip')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $phone = new Zend_Form_Element_Text('phone');
        $phone->setLabel('Work Phone')
                ->setDisableLoadDefaultDecorators(true)
//                ->addErrorMessage('Required')
                ->setAttribs(array('class' => 'text', 'size' => '50'))
                 ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Phone format must be (xxx) xxx-xxxx'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $phone_ext = new Zend_Form_Element_Text('phone_ext');
        $phone_ext->setLabel('Phone Ext.')
                ->setDisableLoadDefaultDecorators(true)
//                ->addErrorMessage('required')
                ->setAttribs(array('class' => 'text', 'size' => '50'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $mobile = new Zend_Form_Element_Text('mobile');
        $mobile->setLabel('Mobile')
                ->setDisableLoadDefaultDecorators(true)
 //              ->addErrorMessage('required')
                ->setAttribs(array('class' => 'text', 'size' => '50'))
                 ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Mobile format must be (xxx) xxx-xxxx'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $fax = new Zend_Form_Element_Text('fax');
        $fax->setLabel('Fax')
                ->setDisableLoadDefaultDecorators(true)
//                ->addErrorMessage('Required')
                ->setAttribs(array('class' => 'text', 'size' => '50'))
                 ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Fax format must be (xxx) xxx-xxxx'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email')
                ->setDisableLoadDefaultDecorators(true)
//                ->addErrorMessage('Required')
                ->setAttribs(array('class' => 'text', 'size' => '50'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $contact_type = new Zend_Form_Element_Select('contact_type');
        $contact_type->setLabel('Contact type')
                ->setDisableLoadDefaultDecorators(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Invalid'))))
                ->setRequired(false)
                ->addMultiOptions(array("Primary"=>"Primary",
                                        "Alternate"=>"Alternate",
                                        "Royalties"=>"Royalties",
                                        "Attorney"=>"Attorney",
                                        "Send Ad Info"=>"Send Ad Info",
                                        "Sent Art To"=>"Sent Art To",
                                        "Past Empl"=>"Past Empl",
                                        "Send Agreements To"=>"Send Agreements To"))
                //->setAttribs(array('class' => 'elWidth'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
	/*
        $contact_type = new Zend_Form_Element_Text('contact_type');
        $contact_type->setLabel('Contact For')
                ->setDisableLoadDefaultDecorators(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Invalid'))))
                ->setRequired(false)
                ->setAttribs(array('class' => 'text'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        */
        $this->addElements(array(
            $sal,
            $first_name,
            $last_name,
            $title,
            $address_line1,
            $city,
            $state,
            $zipcode,
            $phone,
            $phone_ext,
            $mobile,
            $email,
            $fax,
            $contact_type
        ));

    }
}
