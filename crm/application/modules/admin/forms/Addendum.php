<?php

/*
 * 
 * author: Sukhon
 */

class Admin_Form_Addendum extends Zend_Form {

    public function init() {
        $this->setName('addendum_form');
        $this->setAttrib('enctype', 'multipart/form-data');
        
        $content = new Zend_Form_Element_Textarea('content');
        $content->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter content'))))
                ->setAttrib('id', 'content')
                ->setAttrib('name', 'content')
                ->setAttrib('rows', '10')
                ->setAttrib('cols', '100')
                ->setRequired(true)
                ->addFilter('StringTrim');
        
        $reason = new Zend_Form_Element_Text('reason');
        $reason->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter reason'))))
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'reason')
                ->setAttrib('name', 'reason')
                ->setAttrib('size', '50')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $greek_org = new Zend_Form_Element_Checkbox('greek_org');
        $greek_org->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setAttrib('id', 'greek_org')
                ->setAttrib('name', 'greek_org[]')
                ->setRequired(true);
        
        $is_draft = new Zend_Form_Element_Hidden('is_draft');
        $is_draft->setDecorators(array('ViewHelper'));
                
        $save = new Zend_Form_Element_Button('save');
        $save->setLabel(' Save ')
             ->setAttrib('id', 'save')     
             ->setAttrib('type', 'submit')             
             ->setAttrib('class', 'button button_blue detail_button')
             ->setDecorators(array('ViewHelper'));        
        
        $submit = new Zend_Form_Element_Button('submit');
        $submit->setLabel(' Submit ')
                ->setAttrib('id', 'submit')
                ->setAttrib('type', 'submit')             
                ->setAttrib('class', 'button button_blue detail_button')
                ->setDecorators(array('ViewHelper'));

        $this->addElements(
                array(
                    $reason,
                    $content, 
                    $greek_org, 
                    $is_draft, 
                    $save,                     
                    $submit));
    }
}

?>
