<?php

class Client_Form_Contact extends Zend_Form {

//    private $states;
//    private $default_value;

    public function __construct($states = null, $default_value = null) {
//        $this->states = $states;
//        if ($default_value != null) {
//            $this->default_value = $default_value;
//        }
        parent::__construct($states);
    }

    public function init() {
        /* Form Elements & Other Definitions Here ... */
        $this->setName('addClient');

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Organization Name')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $greekLetters = new Zend_Form_Element_Text('greek_letters');
        $greekLetters->setLabel('GREEK Letters')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $description = new Zend_Form_Element_Text('description');
        $description->setLabel('Description')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $address1 = new Zend_Form_Element_Text('address1');
        $address1->setLabel('Address 1')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $address2 = new Zend_Form_Element_Text('address2');
        $address2->setLabel('Address 2')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $city = new Zend_Form_Element_Text('city');
        $city->setLabel('City')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

//        $state = new Zend_Form_Element_Text('state');
//        $state->setLabel('State *')
//                        ->setDisableLoadDefaultDecorators(true)
//                        ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(true)
//                        ->addFilter('StripTags')->addFilter('StringTrim');
//        $state = new Zend_Form_Element_Select('state');
//        $state->setLabel('State')
//                ->setDisableLoadDefaultDecorators(true)
//                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Invalid'))))
//                ->setRequired(false)
//                ->setAttribs(array('class'=> 'default'))
//                ->addMultiOptions($this->states)
//                ->addFilter('StripTags')
//                ->addFilter('StringTrim');
//
//        if($this->default_value!=null){
//            $state->setValue('NY');
//        }

        $state_options = \BL_AMC::parseYAML(APPLICATION_PATH . '/configs/state_options.yml');
        $state = new Zend_Form_Element_Select('state');
        $state->setLabel('State / Province')
                ->setDisableLoadDefaultDecorators(true)
                ->addMultiOptions($state_options);

        $contactPerson = new Zend_Form_Element_Text('contact_person');
        $contactPerson->setLabel('Approval Contact Person')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Portal Communication E-mail Address')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $webAddress = new Zend_Form_Element_Text('web_address');
        $webAddress->setLabel('Web Address')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $phone = new Zend_Form_Element_Text('phone');
        $phone->setLabel('Phone No.')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $fax = new Zend_Form_Element_Text('fax');
        $fax->setLabel('Fax No.')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $zip = new Zend_Form_Element_Text('zip');
        $zip->setLabel('Zip Code')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');



        $this->addElements(array(
            $name,
            $greekLetters,
            $description,
            $address1,
            $address2,
            $city,
            $state,
            $contactPerson,
            $email,
            $webAddress,
            $phone,
            $fax,
            $zip
        ));
    }

}

