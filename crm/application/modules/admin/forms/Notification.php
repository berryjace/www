<?php

/**
 * Description of Admin_Form_Notification
 *
 * @author Masud
 */
class Admin_Form_Notification extends Zend_Form {

    private $vendor_list;
    private $client_list;
    private $right_vendor;
    private $right_client;
    private $page;

    public function __construct($vendor = null, $client = null, $rVendor = array(), $rClient = array(), $page=null) {
        $this->vendor_list = $vendor;
        $this->client_list = $client;
        $this->right_vendor = $rVendor;
        $this->right_client = $rClient;
        $this->page = $page;
        //print_r($options);
        
        parent::__construct();
    }

    public function init() {
        $this->setName('notification_form');
        $this->setAttrib('enctype', 'multipart/form-data');
        $allClients = '';

        $notification_title = new Zend_Form_Element_Text('notification_title');
        $notification_title->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter notification title'))))
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'notification_title')
                ->setAttrib('size', '50')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $notification_message = new Zend_Form_Element_Textarea('notification_message');
        $notification_message->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter message'))))
                ->setAttrib('id', 'notification_message')
                ->setAttrib('rows', '7')
                ->setAttrib('class', 'text_area')
                ->setAttrib('cols', '100')
                ->setAttrib('size', '50')
                ->setRequired(true);

        $email_notification = new Zend_Form_Element_Checkbox('email_notification');
        $email_notification->setLabel('Email')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttrib('class', 'checkbox')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->setDecorators(array('ViewHelper', 'Errors'));

        $site_notification = new Zend_Form_Element_Checkbox('site_notification');
        $site_notification->setOptions(array('site_notification' => 'Site Notification'))
                ->setLabel('Site Notification')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttrib('class', 'checkbox')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator(new BL_Validate_CheckSendViaForNotification())
                ->setDecorators(array('ViewHelper'));

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
                ->setRegisterInArrayValidator(false)
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setMultiOptions($this->right_vendor);
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
                ->setDecorators(array('ViewHelper'))
                ->setRegisterInArrayValidator(false)
                ->setMultiOptions($this->right_client);
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

        if ($this->page == 'edit') {
            $send_and_update = new Zend_Form_Element_Checkbox('send_and_update');
            $send_and_update->setLabel('Send and update')
                    ->setDisableLoadDefaultDecorators(true)
                    ->setAttrib('class', 'checkbox')
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                    ->setDecorators(array('ViewHelper', 'Errors'));

            $only_update = new Zend_Form_Element_Checkbox('only_update');
            $only_update->setOptions(array('only_update' => 'Update Notification'))
                    ->setLabel('Update Notification')
                    ->setDisableLoadDefaultDecorators(true)
                    ->setAttrib('class', 'checkbox')
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                    ->addValidator(new BL_Validate_UpdateForNotification())
                    ->setDecorators(array('ViewHelper'));

            $this->addElement($send_and_update);
            $this->addElement($only_update);
        }
        $Submit = new Zend_Form_Element_Button('Submit');
        $Submit->setAttrib('id', 'submit')
                ->setAttrib('type', 'submit')
                ->setAttrib('class', 'button button_black');

        $preview = new Zend_Form_Element_Button('Preview');
        $preview->setAttrib('id', 'previewBtn')
                ->setAttrib('class', 'button button_black')
                ->setAttrib('rel', 'Preview');

        $this->addElements(
                array(
                    $notification_title,
                    $notification_message,
                    $email_notification,
                    $site_notification,
                    $invitation_client,
                    $invitation_vendor,
                    $event_vendor_choice,
                    $event_client_choice,
                    $left_vendor,
                    $right_vendor,
                    $right_client,
                    $left_client,
                    $addClient,
                    $addVendor,
                    $Submit,
                    $preview));
    }

}
