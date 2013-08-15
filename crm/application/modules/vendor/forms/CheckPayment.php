<?php

class Vendor_Form_CheckPayment extends Zend_Form {

    public function init() {        

        $check_date = new Zend_Form_Element_Text('check_date');
        $check_date->setLabel('Date')
//                ->setDecorators(array('ViewHelper'))
                ->setAttrib('size', '16')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $check_number = new Zend_Form_Element_Text('check_number');
        $check_number->setLabel('Check Number')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
//                ->addValidator('Digits', true, array('messages' => 'Check no must be digits'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');


        $check_amount = new Zend_Form_Element_Text('check_amount');
        $check_amount->setLabel('Check Amount')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
//                ->addValidator('Digits', true, array('messages' => 'Amount no must be digits'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $this->addElements(
                        array(                            
                            $check_date,
                            $check_number,
                            $check_amount
                        )
                )
                ->setDecorators(array(
                    'ViewHelper',
                    array('FormErrors', array('placement' => 'PREPEND')),
                    'Form'
                ));
    }

}

