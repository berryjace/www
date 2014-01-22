<?php

/**
 * Description of forgotPassword
 *
 * @author mahbub
 */
class Application_Form_ForgotPassword extends Zend_Form {

    public function init() {
        
        $this->setName('forgot_password_form');

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Enter your Email address')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors', array('Label', array('tag' => 'dt'))))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter email'))))
                ->setAttrib('class', 'text')
                ->setAttrib('size', '50')
                ->addFilter('StringToLower')
                ->setRequired(true)
                ->addValidator('EmailAddress');

        $this->addElements(array($email));
                
    }

}
