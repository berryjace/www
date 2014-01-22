<?php

/*
 * author: Tanzim
 */

class Admin_Form_VendorInvoices extends Zend_Form {

    public function init() {
        $this->setName('docs_form');
        $this->setAttrib('enctype', 'multipart/form-data');

        $VendorName = new Zend_Form_Element_Text('vendor_name');
        $VendorName->setLabel('Vendor Name')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $VendorAddress = new Zend_Form_Element_Text('address');
        $VendorAddress->setLabel('Address')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');
        
        $City = new Zend_Form_Element_Text('city');
        $City->setLabel('City')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');
        
        $State = new Zend_Form_Element_Text('state');
        $State->setLabel('State')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $Zip = new Zend_Form_Element_Text('zip');
        $Zip->setLabel('Zip')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');
        
        $InvoiceNumber = new Zend_Form_Element_Text('invoice_number');
        $InvoiceNumber->setLabel('Invoice Number')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');
        
        $InvoiceDate = new Zend_Form_Element_Text('invoice_date');
        $InvoiceDate->setLabel('Invoice Date')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text date'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');
        
        $Email = new Zend_Form_Element_Text('email');
        $Email->setLabel('Email')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');
        
        $Phone_1 = new Zend_Form_Element_Text('phone_1');
        $Phone_1->setLabel('Phone 1 / Ext')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');
        
        $Phone_2 = new Zend_Form_Element_Text('phone_2');
        $Phone_2->setLabel('Phone 2 / Ext')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');
        
        $Fax = new Zend_Form_Element_Text('fax');
        $Fax->setLabel('Fax')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');
        
        $From = new Zend_Form_Element_Text('date_from');
        $From->setLabel('From')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text date'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $To = new Zend_Form_Element_Text('date_to');
        $To->setLabel('To')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text date'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $this->addElements(array(
            $VendorName,            
            $VendorAddress,
            $City,
            $State,
            $Zip,
            $InvoiceNumber,
            $InvoiceDate,
            $Email,
            $Phone_1,
            $Phone_2,
            $Fax,
            $From,
            $To
        ));
    }

}

