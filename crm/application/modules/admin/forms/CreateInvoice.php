<?php

class Admin_Form_CreateInvoice extends Zend_Form {

    public function init() {

        $this->setName('create_invoice_form');

        $vendorName = new Zend_Form_Element_Text('vendor_name');
        $vendorName->setLabel('Vendor Name')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Name required'))))
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $invNum = new Zend_Form_Element_Text('inv_num');
        $invNum->setLabel('Invoice Number')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $inv_types = \BL_AMC::parseYAML(APPLICATION_PATH . '/configs/invoice_types.yml');
        $invType = new Zend_Form_Element_Select('inv_type');
        $invType->setLabel('Invoice Type')
                ->setDisableLoadDefaultDecorators(true)
                ->addMultiOptions($inv_types);
                
        $inv_terms = \BL_AMC::parseYAML(APPLICATION_PATH . '/configs/invoice_terms.yml');
        $invTerm = new Zend_Form_Element_Select('inv_term');
        $invTerm->setLabel('Invoice Term')
                ->setDisableLoadDefaultDecorators(true)
                ->addMultiOptions($inv_terms);
                

        $invDate = new Zend_Form_Element_Text('inv_date');
        $invDate->setLabel('Invoice Date')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $address_line_1 = new Zend_Form_Element_Text('address_line_1');
        $address_line_1->setLabel('Vendor Address')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $address_line_2 = new Zend_Form_Element_Text('address_line_2');
        $address_line_2->setLabel('Address Line 2')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $city = new Zend_Form_Element_Text('city');
        $city->setLabel('City')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim')
                ->addErrorMessage('City required');

        $state = new Zend_Form_Element_Text('state');
        $state->setLabel('State')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim')
                ->addErrorMessage('State required');

        $zip = new Zend_Form_Element_Text('zip');
        $zip->setLabel('Zip')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim')
                ->addErrorMessage('Zip required');

        $phone_1 = new Zend_Form_Element_Text('phone_1');
        $phone_1->setLabel('Phone 1 / Ext')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $phone_2 = new Zend_Form_Element_Text('phone_2');
        $phone_2->setLabel('Phone 2 / Ext')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $fax = new Zend_Form_Element_Text('fax');
        $fax->setLabel('Fax')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');


        $this->addElements(array(
            $invType,
            $invTerm,
            $vendorName,
            $invNum,
            $invDate,
            $email,
            $address_line_1,
            $address_line_2,
            $city,
            $state,
            $zip,
            $phone_1,
            $phone_2,
            $fax,
        ));
    }

}