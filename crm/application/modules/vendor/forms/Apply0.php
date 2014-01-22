<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Apply_Form_Apply1
 *
 * @author medhad
 */
class Vendor_Form_Apply0 extends Zend_Form {

    public function init() {

        $this->setName('vendor_add');
        $this->setAttrib('enctype', 'multipart/form-data');

        $firstname = new Zend_Form_Element_Text('firstname');
        $firstname->setLabel('Signatory First Name:')
                ->setAttrib('size', '40')
                ->setAttrib('class', 'text')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $lastname = new Zend_Form_Element_Text('lastname');
        $lastname->setLabel('Signatory Last Name:')
                ->setAttrib('size', '40')
                ->setAttrib('class', 'text')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email address')
                ->setAttrib('class', 'text')
                ->setAttrib('size', '40')
                ->addValidator('EmailAddress')
                ->addFilter('StringToLower')
                ->addFilter('StringTrim')
                ->addFilter('StripTags');

        $aggrement = new Zend_Form_Element_Radio('aggrement');
        $aggrement->setLabel('Agreement')
                ->setAttrib('class', 'radio')
                ->setAttrib('name', 'agrement')
                ->setAttrib('value', 'yes')
                //->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter account type'))))
                ->setRequired(false)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setMultiOptions(array('yes' => 'yes', 'no' => 'no'));


        $element = new Zend_Form_Element_MultiCheckbox('element');
        $element->setValue(array('bar', 'bat'))
                ->setMultiOptions(array('card' => ' Charge my Card the $[40] Application Fee', 'check' => ' Do not bill me at this time. Send me an invoice and I will mail a check'));

        $start_date = new Zend_Form_Element_Text('start_date');
        $start_date->setLabel('Event Start Date')
                ->setDisableLoadDefaultDecorators(true)
                //->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter event start date'))))
                ->setAttrib('class', 'date')
                ->setAttrib('size', '10')
                ->setRequired(true)
                ->setDecorators(array('ViewHelper'))
                ->addValidator(new BL_Validate_DateGreaterToday())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $this->addElements(array($element, $firstname, $lastname, $email, $aggrement, $start_date));

    }

}
?>
