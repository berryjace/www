<?php

class Admin_Form_PartialPayment extends Zend_Form {

    public function init() {

        $payment_amount = new Zend_Form_Element_Text('payment_amount');
        $payment_amount->setLabel('Payment Total:')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
//                ->addValidator('Digits', true, array('messages' => 'Amount no must be digits'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
                
        $payment_type = new Zend_Form_Element_Radio('payment_type');
        $payment_type->setLabel('Payment Type:')
                ->setAttrib('class', 'radio')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->setMultiOptions(array('Received Check' => " Check", 'Received EFT' => " EFT"));

        $payment_ref = new Zend_Form_Element_Text('payment_ref');
        $payment_ref->setLabel('Check/ACH #:')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $payment_date = new Zend_Form_Element_Text('payment_date');
        $payment_date->setLabel('Payment Date:')
                ->setAttrib('size', '16')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $this->addElements(
                        array(
                            $payment_amount,
                            $payment_type,
                            $payment_ref,
                            $payment_date
                        )
                )
                ->setDecorators(array(
                    'ViewHelper',
                    array('FormErrors', array('placement' => 'PREPEND')),
                    'Form'
                ));
    }

}

