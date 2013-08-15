<?php

class Admin_Form_RoyaltyFee extends Zend_Form {

    public function init() {
        $this->setName('royaltyfee_form');

        $greek_grant_of_license = new Zend_Form_Element_Textarea('greek_grant_of_license');
        $greek_grant_of_license->addDecorator('Label', array('escape' => false))
                ->setLabel('Grant of License (Default Value) <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('rows' => '15', 'cols' => '100', 'class' => 'textarea'))
//                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Grant of license is required.'))))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $button = new Zend_Form_Element_Button('Save');
        $button->removeDecorator('DtDdWrapper')->setAttribs(array('type' => 'submit', 'class' => 'button button_brown'));

        $this->addElements(array(
            $greek_grant_of_license,
            $button
        ));
    }

}

