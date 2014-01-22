<?php

/**
 * Description of Signup
 *
 * @author Masud
 */
class Application_Form_ExistingKey extends Zend_Form {

    public function init() {

        $this->setName('existing_form');

        $number = new Zend_Form_Element_Text('number');
        $number->addDecorator('Label', array('escape' => false))
                ->setLabel('Vendor/Client Number: <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(true)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $key = new Zend_Form_Element_Text('key');
        $key->addDecorator('Label', array('escape' => false))
                ->setLabel('Unique Key: <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(true)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $this->addElements(array(
          	$number,
        	$key
        ));
        
        error_log("\nend of init", 3, "./errorLog.log");
    }

}