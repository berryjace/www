<?php

class Admin_Form_ClientLegal extends Zend_Form {

    public function __construct() {
        parent::__construct();
    }

    public function init() {
        $this->setName('legal_form');
        $this->setAttrib('enctype', 'multipart/form-data');

        $orgChoiceOfLaw = new Zend_Form_Element_Text('org_choice_law');
        $orgChoiceOfLaw->setLabel('ORG Choice of LAW')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $licensorTitle = new Zend_Form_Element_Text('licensor_title');
        $licensorTitle->addDecorator('Label', array('escape' => false))
                ->setLabel('Licensor Title <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter licensor title'))))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $legalName = new Zend_Form_Element_Text('legal_name');
        $legalName->setLabel('Legal Name')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $legalFirm = new Zend_Form_Element_Text('legal_firm');
        $legalFirm->setLabel('Legal Firm')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $legalAddress1 = new Zend_Form_Element_Text('legal_address1');
        $legalAddress1->setLabel('Legal Address Line 1')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $legalAddress2 = new Zend_Form_Element_Text('legal_address2');
        $legalAddress2->setLabel('Legal Address Line 2')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $legalCity = new Zend_Form_Element_Text('legal_city');
        $legalCity->setLabel('Legal City')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $legalState = new Zend_Form_Element_Text('legal_state');
        $legalState->setLabel('Legal State')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $legalZip = new Zend_Form_Element_Text('legal_zip');
        $legalZip->setLabel('Legal Zip')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $legalPhone = new Zend_Form_Element_Text('legal_phone');
        $legalPhone->setLabel('Legal Phone')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Phone must be (xxx) xxx-xxxx format'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $this->addElements(array(
            $orgChoiceOfLaw,
            $licensorTitle,
            $legalFirm,
            $legalName,
            $legalAddress1,
            $legalAddress2,
            $legalCity,
            $legalState,
            $legalZip,
            $legalPhone,
        ));
    }

}