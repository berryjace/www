<?php

/**
 * Description of Signup
 *
 * @author Masud
 */
class Application_Form_RegisterUser extends Zend_Form {

    public function init() {

        $this->setName('existing_form');

        $username = new Zend_Form_Element_Text('username');
        $username->addDecorator('Label', array('escape' => false))
                ->setLabel('User Name: <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(true)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $password = new Zend_Form_Element_Password('password');
        $password->addDecorator('Label', array('escape' => false))
                ->setLabel('Password: <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(true)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $confirm_password = new Zend_Form_Element_Password('confirm_password');
        $confirm_password->addDecorator('Label', array('escape' => false))
                ->setLabel('Re-Enter Password <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim')
                ->addValidator('Identical', false, array('token' => 'password'));
        
        $this->addElements(array(
          	$username,
        	$password,
        	$confirm_password
        ));
    }

}