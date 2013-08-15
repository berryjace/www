<?php

class Admin_Form_Payment extends Zend_Form
{

    public function init()
    {               
        $this->setName('payment_form');       
        
        $fiscal_year = new Zend_Form_Element_Text('fiscal_year');
        $fiscal_year->setLabel('Fiscal Year')
                ->setAttrib('class', 'text')                
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
                 
        $quarter = new Zend_Form_Element_Select('quarter');
        $quarter->setLabel('Quarter')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addMultiOptions(array("1" => "Q1", "2" => "Q2", "3" => "Q3", "4" => "Q4"));
        
        $payment_statuses = \BL_AMC::parseYAML(APPLICATION_PATH . '/configs/payment_status.yml');        
        $payment_status = new Zend_Form_Element_Select('payment_status');
        $payment_status->setLabel('Payment Status')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->addMultiOptions($payment_statuses);

        $invoice_statuses = \BL_AMC::parseYAML(APPLICATION_PATH . '/configs/invoice_status.yml');        
        $invoice_status = new Zend_Form_Element_Select('invoice_status');
        $invoice_status->setLabel('Invoice Status')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->addMultiOptions($invoice_statuses);
        
        $payment_amount = new Zend_Form_Element_Text('payment_amount');
        $payment_amount->setLabel('Payment Amount')
                ->setAttrib('class', 'text')                
                ->setDisableLoadDefaultDecorators(true)
//                ->addValidator('NotEmpty', true, array('messages' => 'Enter your bank routing'))
                ->addValidator('Digits', true, array('messages' => 'Amount must be digits'))                   
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $check_number = new Zend_Form_Element_Text('check_number');
        $check_number->setLabel('Check Number')
                ->setAttrib('class', 'text')                
                ->setDisableLoadDefaultDecorators(true)
//                ->addValidator('NotEmpty', true, array('messages' => 'Enter your bank routing'))
                ->addValidator('Digits', true, array('messages' => 'Check number must be digits'))                   
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $this->addElements(array(
            $fiscal_year,
            $quarter,
            $payment_status, 
            $invoice_status, 
            $payment_amount, 
            $check_number));
        $this->setDecorators(array(
                    'ViewHelper',
                    array('FormErrors', array('placement' => 'PREPEND')),
                    'Form'
                ));
        
    }


}

