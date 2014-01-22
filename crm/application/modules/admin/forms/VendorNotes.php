<?php

class Admin_Form_VendorNotes extends Zend_Form {

    public function init() {
        $this->setName('notes_form');
        $this->setAttrib('enctype', 'multipart/form-data');

        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel('Description')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('rows' => '10','cols' => '62', 'class' => 'textarea'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');
        
        $note_id = new Zend_Form_Element_Hidden('note_id');
        $note_id->setDecorators(array('ViewHelper'));
        
        $button = new Zend_Form_Element_Button('Save');        
        $button->removeDecorator('DtDdWrapper')->setAttribs(array('type'=>'submit','class'=>'button button_black'));

        $this->addElements(array(
            $description,
            $button,
            $note_id
        ));
    }

}

