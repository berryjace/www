<?php

/**
 * Description of AddendumSign
 *
 * @author Masud
 */
class Vendor_Form_AddendumSign extends Zend_Form {
    
    public function __construct($options = null) {
        parent::__construct($options);
    }
    
    public function init() {
        $this->setName('addendum_sign_form');
        $this->setAttrib('enctype', 'multipart/form-data');
        
        $greek_org = new Zend_Form_Element_Checkbox('greek_org');
        $greek_org->setAttrib('id', 'greek_org')
                ->setAttrib('name', 'greek_org[]')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))                
                ->setRequired(false);
        
        $vendor_name = new Zend_Form_Element_Text('vendor_name');
        $vendor_name->setLabel('Vendor Name')
                ->setAttrib('id', 'vendor_name')                
                ->setAttrib('class', 'text')    
//                ->setAttrib('disabled', true)
                ->setDisableLoadDefaultDecorators(true)                               
                ->setRequired(false);
        
        $signator_name = new Zend_Form_Element_Text('signator_name');
        $signator_name->setLabel('Signator Name')
                ->setAttrib('id', 'signator_name')
                ->setAttrib('class', 'text')                                
                ->setDisableLoadDefaultDecorators(true)
//                ->setDecorators(array('ViewHelper', 'Errors'))
//                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter reason'))))                                
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $signator_title = new Zend_Form_Element_Text('signator_title');
        $signator_title->setLabel('Signator Title')
                ->setAttrib('id', 'signator_title')
                 ->setAttrib('class', 'text')                
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $signed_on = new Zend_Form_Element_Text('signed_on');
        $signed_on->setLabel('Signed on')
                ->setAttrib('id', 'signed_on')
                ->setAttrib('class', 'text')                
                ->setAttrib('disabled', true)
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $submit = new Zend_Form_Element_Button('submit');
        $submit->setLabel(' Submit ')
                ->setAttrib('id', 'submit')
                ->setAttrib('type', 'submit')             
                ->setAttrib('class', 'button button_blue detail_button')
                ->setDecorators(array('ViewHelper'));
        
        $this->addElements(
                array(
                    $greek_org,
                    $vendor_name,
                    $signator_name, 
                    $signator_title,        
                    $signed_on,
                    $submit));
        
    }
    
    
}

?>
