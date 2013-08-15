<?php

class Admin_Form_VendorActions extends Zend_Form {

    private $clients;

    public function __construct($clients = NULL) {        
        $this->clients = $clients;
        parent::__construct($clients);
    }
    
    public function init() {
        $this->setName('actions_form');
        $this->setAttrib('enctype', 'multipart/form-data');

        $GreekOrg = new Zend_Form_Element_Select('greek_org');
        $GreekOrg->setLabel('Greek Org')
                ->setDisableLoadDefaultDecorators(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Invalid'))))                
                ->addMultiOptions($this->clients)
               // ->setValue($this->SelectedValue)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $ActionNeeded = new Zend_Form_Element_Text('action_needed');
        $ActionNeeded->setLabel('Action Needed')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $Resolution = new Zend_Form_Element_Text('resolution');
        $Resolution->setLabel('Resolution')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');
        
        $actions_id = new Zend_Form_Element_Hidden('actions_id');
        $actions_id->setDecorators(array('ViewHelper'));
        
        $button = new Zend_Form_Element_Button('Save');        
        $button->removeDecorator('DtDdWrapper')->setAttribs(array('type'=>'submit','class'=>'button button_black'));

        $this->addElements(array(
            $GreekOrg,
            $ActionNeeded,
            $Resolution,
            $actions_id,
            $button
        ));
    }
}

