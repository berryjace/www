<?php

/*
 * author: Tanzim
 */

class Admin_Form_VendorPayments extends Zend_Form {

    public function init() {
        $this->setName('payments_form');
        $this->setAttrib('enctype', 'multipart/form-data');

        $VendorName = new Zend_Form_Element_Text('vendor_name');
        $VendorName->setLabel('Vendor Name')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $PaymentID = new Zend_Form_Element_Text('payment_id');
        $PaymentID->setLabel('Payment ID')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $InvoiceNumber = new Zend_Form_Element_Text('invoice_number');
        $InvoiceNumber->setLabel('Invoice Number')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $PaymentAmount = new Zend_Form_Element_Text('payment_amount');
        $PaymentAmount->setLabel('Payment Amount')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $PaymentYear = new Zend_Form_Element_Text('payment_year');
        $PaymentYear->setLabel('Payment Year')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $Reference = new Zend_Form_Element_Text('reference');
        $Reference->setLabel('Reference')
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
            $PaymentID,
            $InvoiceNumber,
            $PaymentAmount,
            $PaymentYear,
            $Reference,
            $From,
            $To
        ));
    }

}

