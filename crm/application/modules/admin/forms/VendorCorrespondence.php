<?php

class Admin_Form_VendorCorrespondence extends Zend_Form {

    public function init() {
        $this->setName('note_form');

        $subject = new Zend_Form_Element_Text('subject');
        $subject->setLabel('Subject')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $note = new Zend_Form_Element_Textarea('note');
        $note->setLabel('Note')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('rows' => '10', 'cols' => '62', 'class' => 'textarea'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $note_time = new Zend_Form_Element_Text('note_time');
        $note_time->setLabel('Date')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '25', 'class' => ''))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $note_id = new Zend_Form_Element_Hidden('note_id');
        $note_id->setDecorators(array('ViewHelper'));
                
        $button = new Zend_Form_Element_Button('Save');
        $button->removeDecorator('DtDdWrapper')->setAttribs(array('type' => 'submit', 'class' => 'button button_brown'));

        $this->addElements(array(
            $subject,
            $note,
            $note_time,
            $note_id,            
            $button
        ));
    }

}

