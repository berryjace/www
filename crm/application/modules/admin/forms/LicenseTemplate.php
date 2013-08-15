<?php

/*
 * 
 * author: Sukhon
 */

class Admin_Form_LicenseTemplate extends Zend_Form {

    public function init() {
        $this->setName('license_template_form');
        $this->setAttrib('enctype', 'multipart/form-data');

        $template = new Zend_Form_Element_Textarea('template');
        $template->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter template'))))
                ->setAttrib('id', 'template')
                ->setAttrib('name', 'template')
                ->setAttrib('rows', '10')
                ->setAttrib('cols', '100')
                ->setRequired(true)
                ->addFilter('StringTrim');

        $save = new Zend_Form_Element_Submit('save');
        $save->setAttrib('id', 'save')
                ->setAttrib('class', 'large awesome')->setDecorators(array('ViewHelper'));
        $save->setLabel(' Save ');

        $this->addElements(array($template, $save));
    }

}

?>
