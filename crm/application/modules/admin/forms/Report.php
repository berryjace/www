<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * author: Sukhon
 */

class Admin_Form_Report extends Zend_Form {
    private $client_list;
    public function  __construct($client = null) {
        $this->client_list = $client;
        //print_r($options);
        parent::__construct();
    }

    public function init() {
        $this->setName('report_form');
        $this->setAttrib('enctype', 'multipart/form-data');
        $allClients = '';      

        $vendor_id = new Zend_Form_Element_Hidden('vendor_id');
        $vendor_id->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please choose a registered vendor'))))
                ->setAttrib('id', 'vendor_id')
                ->setAttrib('name', 'vendor_id')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $vendor_name = new Zend_Form_Element_Text('vendor_name');
        $vendor_name->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please choose a vendor'))))
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'vendor_name')
                ->setAttrib('size', '40')
                ->setAttrib('name', 'vendor_name')
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $report_year = new Zend_Form_Element_Select('report_year');
        $report_year->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please choose year'))))
                ->setAttrib('id', 'report_year')
                ->setAttrib('name', 'report_year')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $year = range(date('Y', strtotime('-10years')), date('Y'));
        $report_year->setMultiOptions(array_combine($year, $year));
        
        $quarter = new Zend_Form_Element_Select('quarter');
        $quarter->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please choose quarter'))))
                ->setAttrib('class', 'select')
                ->setAttrib('id', 'quarter')
                ->setAttrib('name', 'quarter')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $quarter->setMultiOptions(array('Q1'=>'Q1', 'Q2'=>'Q2', 'Q3'=>'Q3', 'Q4'=>'Q4'));
        
        $product_sold_to = new Zend_Form_Element_Text('product_sold_to');
        $product_sold_to->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter product sold to name'))))
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'product_sold_to')
                ->setAttrib('size', '25')
                ->setAttrib('name', 'product_sold_to[]')
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $invoice_number = new Zend_Form_Element_Text('invoice_number');
        $invoice_number->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter invoice number'))))
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'invoice_number')
                ->setAttrib('name', 'invoice_number[]')
                ->setAttrib('size', '10')
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
                
        $invoice_date = new Zend_Form_Element_Text('invoice_date');
        $invoice_date->setLabel('Invoice Date')
                ->setDisableLoadDefaultDecorators(true)
                //->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter invoice date'))))
                ->setAttrib('class', 'date')
                ->setAttrib('id', 'invoice_date_0')
                ->setAttrib('name', 'invoice_date[]')
                ->setAttrib('size', '7')
                ->setRequired(false)
                ->setDecorators(array('ViewHelper'))
                ->addValidator(new BL_Validate_DateGreaterToday())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $product_description = new Zend_Form_Element_Text('product_description');
        $product_description->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter product description'))))             
                ->setAttrib('id', 'product_description')
                ->setAttrib('name', 'product_description[]')
                ->setAttrib('class', 'text')
                ->setAttrib('size', '45')
                ->setRequired(false);

        $quantity = new Zend_Form_Element_Text('quantity');
        $quantity->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter quantity'))))
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'quantity')
                ->setAttrib('name', 'quantity[]')
                ->setAttrib('size', '5')
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $price_per_unit = new Zend_Form_Element_Text('price_per_unit');
        $price_per_unit->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter price per unit'))))
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'price_per_unit')
                ->setAttrib('name', 'price_per_unit[]')
                ->setAttrib('size', '5')
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $gross_sales = new Zend_Form_Element_Text('gross_sales');
        $gross_sales->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter gross sales'))))
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'gross_sales')
                ->setAttrib('name', 'gross_sales[]')
                ->setAttrib('size', '5')
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $greek_client_name = new Zend_Form_Element_Text('greek_client_name');
        $greek_client_name->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter greek org'))))
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'greek_client_name')
                ->setAttrib('name', 'greek_client_name[]')
                ->setAttrib('size', '20')
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $greek_client_id = new Zend_Form_Element_Hidden('greek_client_id');
        $greek_client_id->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'greek_client_id')
                ->setAttrib('name', 'greek_client_id[]')
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $submit_quarterly = new Zend_Form_Element_Submit('submit_quarterly');
        $submit_quarterly->setAttrib('id', 'submit_quarterly')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                //->setAttrib('class', 'large awesome')
                ->setLabel('Submit Quarterly Report');

        $save = new Zend_Form_Element_Button('save');
        $save->setAttrib('id', 'addClient')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                //->setAttrib('class', 'large awesome')
                ->setLabel('Save');

        $addmore = new Zend_Form_Element_Button('addmore');
        $addmore->setAttrib('id', 'addmore')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setAttrib('class', 'large awesome')
                ->setLabel('Add More Rows');
        
         $this->addElements(array($vendor_name, $vendor_id, $report_year, $quarter, $product_sold_to,$invoice_number,$invoice_date,$product_description,$quantity,
             $price_per_unit,$gross_sales, $greek_client_name, $greek_client_id, $submit_quarterly, $save,$addmore));
    }



}

?>
