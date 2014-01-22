<?php

class Vendor_Form_BankInfo extends Zend_Form {

    public function init() {

        $this->setName('bank_info_form');
//        $this->setAttrib('enctype', 'multipart/form-data');

        $account_number = new Zend_Form_Element_Text('account_number');
        $account_number->setLabel('Enter your bank account #:')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->addValidator('Digits', true, array('messages' => 'Account no must be digits'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $account_number_re = new Zend_Form_Element_Text('account_number_re');
        $account_number_re->setLabel('Re-enter your bank account #:')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->addValidator('Digits', true, array('messages' => 'Account no must be digits'))
                ->addValidator('Identical', false, array('token' => 'account_number'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $routing_number = new Zend_Form_Element_Text('routing_number');
        $routing_number->setLabel('Enter your bank routing #:')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->addValidator('Digits', true, array('messages' => 'Routing no must be digits'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $routing_number_re = new Zend_Form_Element_Text('routing_number_re');
        $routing_number_re->setLabel('Re-enter your bank routing #:')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->addValidator('Digits', true, array('messages' => 'Routing no must be digits'))
                ->addValidator('Identical', false, array('token' => 'routing_number'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $this->addElements(array(
            $account_number,
            $account_number_re,
            $routing_number,
            $routing_number_re
        ));

        $this->setDecorators(array(
            'ViewHelper',
            array('FormErrors', array('placement' => 'PREPEND')),
            'Form'
        ));
    }

}