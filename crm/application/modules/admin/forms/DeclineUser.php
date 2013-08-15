<?php

/**
 * Description of DeclineUser
 *
 * @author Masud
 */
class Admin_Form_DeclineUser extends Zend_Form {
    
    public function __construct($options = null) {
        parent::__construct($options);
    }
        
    public function init(){
        $this->setName('declineUser_form');
        $this->setAttrib('enctype', 'multipart/form-data');
        
        
        $decline_reason = new Zend_Form_Element_Textarea('decline_reason');
        $decline_reason->setLabel('Reason for Declining')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->setAttrib('id', 'decline_reason')
                ->setAttrib('rows', '8')
                ->setAttrib('class', 'text_area')
                ->setAttrib('cols', '110')
                ->setAttrib('size', '50')
                ->addFilter('StringTrim');

        $submit = new Zend_Form_Element_Select('submit');
        $submit->setLabel('Send to user')
                ->setAttrib('id', 'submit')
               ->setDisableLoadDefaultDecorators(true);
        
        $this->addElements(array(                    
                    $decline_reason,
                    $submit))
                ->setDecorators(array(
                    'ViewHelper',
                    array('FormErrors', array('placement' => 'PREPEND')), 'Form'));                        
    }
}

?>
