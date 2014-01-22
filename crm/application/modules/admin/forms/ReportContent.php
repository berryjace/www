<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * author: Sukhon
 */

class Admin_Form_ReportContent extends Zend_Form {

    public function init() {
        $this->setName('report_form');
        $this->setAttrib('enctype', 'multipart/form-data');
        
        $product_sold_to = new Zend_Form_Element_Text('product_sold_to');
        $product_sold_to->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter product sold to name'))))
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'product_sold_to')
                ->setAttrib('size', '30')
                ->setAttrib('name', 'product_sold_to')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $invoice_number = new Zend_Form_Element_Text('invoice_number');
        $invoice_number->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter invoice number'))))
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'invoice_number')
                ->setAttrib('name', 'invoice_number')
                ->setAttrib('size', '10')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
                
        $invoice_date = new Zend_Form_Element_Text('invoice_date');
        $invoice_date->setLabel('Invoice Date')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter invoice date'))))
                ->setAttrib('class', 'date')
                ->setAttrib('id', 'invoice_date_0')
                ->setAttrib('name', 'invoice_date')
                ->setAttrib('size', '7')
                ->setRequired(true);
        
        $product_description = new Zend_Form_Element_Text('product_description');
        $product_description->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter product description'))))             
                ->setAttrib('id', 'product_description')
                ->setAttrib('name', 'product_description')
                ->setAttrib('class', 'text')
                ->setAttrib('size', '45')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $quantity = new Zend_Form_Element_Text('quantity');
        $quantity->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter quantity'))))
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'quantity')
                ->setAttrib('name', 'quantity')
                ->setAttrib('size', '5')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $price_per_unit = new Zend_Form_Element_Text('price_per_unit');
        $price_per_unit->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter price per unit'))))
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'price_per_unit')
                ->setAttrib('name', 'price_per_unit')
                ->setAttrib('size', '5')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $gross_sales = new Zend_Form_Element_Text('gross_sales');
        $gross_sales->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter gross sales'))))
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'gross_sales')
                ->setAttrib('name', 'gross_sales')
                ->setAttrib('size', '5')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $greek_client_name = new Zend_Form_Element_Text('greek_client_name');
        $greek_client_name->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter greek org'))))
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'greek_client_name')
                ->setAttrib('name', 'greek_client_name')
                ->setAttrib('size', '20')
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $greek_client_id = new Zend_Form_Element_Hidden('greek_client_id');
        $greek_client_id->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter registered greek org'))))
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'greek_client_id')
                ->setAttrib('name', 'greek_client_id')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
         $this->addElements(array($product_sold_to,$invoice_number,$invoice_date,$product_description,$quantity,
             $price_per_unit,$gross_sales, $greek_client_name, $greek_client_id));
    }



}

?>
