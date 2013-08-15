<?php

class Admin_Form_ClientNotes extends Zend_Form {

    public function init() {
        $this->setName('note_form');
        $note = new Zend_Form_Element_Textarea('note');
        $note->setLabel('Note')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('rows' => '15', 'cols' => '100', 'class' => 'textarea'))
                ->setRequired(false)
                ->addFilter('StringTrim');

        $button = new Zend_Form_Element_Button('Save');
        $button->removeDecorator('DtDdWrapper')->setAttribs(array('type' => 'submit', 'class' => 'button button_brown'));

        $this->addElements(array($note, $button));
    }

}

