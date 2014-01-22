<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * author: zea
 */

class Admin_Form_Events extends Zend_Form {

    private $vendor_list;
    private $client_list;

    public function __construct($vendor = null, $client = null) {
        $this->vendor_list = $vendor;
        $this->client_list = $client;
        //print_r($options);
        parent::__construct();
    }

    public function init() {
        $this->setName('event_form');
        $this->setAttrib('enctype', 'multipart/form-data');
        $allClients = '';

        $event_title = new Zend_Form_Element_Text('event_title');
        $event_title->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter event title'))))
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'event_title')
                ->setAttrib('size', '50')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $event_type = new Zend_Form_Element_Select('event_type');
        $event_type->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->addMultiOptions(array('s_d' => 'Single Day', 'm_d' => 'Multiple Day'))
                ->setAttrib('class', '')
                ->setAttrib('id', 'event_type')
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

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

        $start_time = new Zend_Form_Element_Text('start_time');
        $start_time->setLabel('Start Time')
                ->setDisableLoadDefaultDecorators(true)
                ->setValue('08:00 am')
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setAttrib('class', 'time')
                ->setAttrib('size', '50')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $end_date = new Zend_Form_Element_Text('end_date');
        $end_date->setLabel('Event End Date')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter event end date'))))
                ->setAttrib('class', 'date')
                ->addValidator(new BL_Validate_EventEndDate($start_date))
                ->setAttrib('size', '50')
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $end_time = new Zend_Form_Element_Text('end_time');
        $end_time->setLabel('End Time')
                ->setDisableLoadDefaultDecorators(true)
                ->setValue('05:00 pm')
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setAttrib('class', 'time')
                ->setAttrib('size', '50')
                ->setRequired(true)
                ->setAttrib('id', 'end_time')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $event_location = new Zend_Form_Element_Text('event_location');
        $event_location->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter event location'))))
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'event_location')
                ->setAttrib('size', '50')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $event_message = new Zend_Form_Element_Textarea('event_message');
        $event_message->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter message'))))
                ->setAttrib('id', 'event_message')
                ->setAttrib('rows', '7')
                ->setAttrib('class', 'text_area')
                ->setAttrib('cols', '100')
                ->setAttrib('size', '50')
                ->addFilter('StringTrim')
                ->setRequired(true);

        $invitation_vendor = new Zend_Form_Element_Checkbox('send_seperate_invitation_vendor');
        $invitation_vendor->setLabel('All vendors')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttrib('class', 'checkbox')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->setDecorators(array('ViewHelper', 'Errors'));

        $event_client_choice = new Zend_Form_Element_Text('event_client_choice');
        $event_client_choice->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                //->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter event location'))))
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'event_client_choice')
                ->setAttrib('size', '50')
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $addVendor = new Zend_Form_Element_Button('add Vendor');
        $addVendor->setAttrib('id', 'addVendor')
                ->setAttrib('class', 'button button_black')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                //->setAttrib('class', 'large awesome')
                ->setLabel('Add');

        $addClient = new Zend_Form_Element_Button('add Client');
        $addClient->setAttrib('id', 'addClient')
                ->setAttrib('class', 'button button_black')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                //->setAttrib('class', 'large awesome')
                ->setLabel('Add');

        $event_vendor_choice = new Zend_Form_Element_Text('event_vendor_choice');
        $event_vendor_choice->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                //->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter event location'))))
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'event_vendor_choice')
                ->setAttrib('size', '50')
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        //$this->addElement(new Application_Form_Element_clientSelect('country_id'));

        $left_vendor = new Zend_Form_Element_Multiselect('left_vendor');
        $left_vendor->setLabel('Select Vendor')
                ->setRequired(false)
                ->setDecorators(array('ViewHelper'))
                ->addValidator('NotEmpty')
                ->setDisableLoadDefaultDecorators(true)
                ->setMultiOptions($this->vendor_list);
        $left_vendor->size = 10;

        $right_vendor = new Zend_Form_Element_Multiselect('right_vendor');
        $right_vendor->setLabel('Select Vendor')
                ->setRequired(false)
                ->addValidator('NotEmpty')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'));
        //->setMultiOptions($this->vendor_list);
        $right_vendor->size = 10;

        $left_client = new Zend_Form_Element_Multiselect('left_client');
        $left_client->setLabel('Select Client')
                ->setDecorators(array('ViewHelper'))
                ->addValidator('NotEmpty')
                ->setDisableLoadDefaultDecorators(true)
                ->setMultiOptions($this->client_list);
        $left_client->size = 10;

        $right_client = new Zend_Form_Element_Multiselect('right_client');
        $right_client->setLabel('Select client')
                ->addValidator('NotEmpty')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'));
        //->setMultiOptions($this->vendor_list);
        $right_client->size = 10;

        $invitation_client = new Zend_Form_Element_Checkbox('send_seperate_invitation_client');
        $invitation_client->setOptions(array('all_clients' => 'All Clients'))
                ->setLabel('All Clients')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttrib('class', 'checkbox')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator(new BL_Validate_CheckVendorClientForEvent())
                ->setDecorators(array('ViewHelper'));

        $Submit = new Zend_Form_Element_Button('Submit');
        $Submit->setAttrib('id', 'submit')
                ->setAttrib('class', 'button button_black');

        $this->addElements(array(
            $event_title,
            $event_type,
            $start_date,
            $start_time,
            $end_date,
            $end_time,
            $event_location,
            $event_message,
            $invitation_client,
            $invitation_vendor,
            $event_vendor_choice,
            $event_client_choice,
            $Submit,
            $addVendor,
            $left_vendor,
            $right_vendor,
            $right_client,
            $left_client,
            $addClient));
    }

}

