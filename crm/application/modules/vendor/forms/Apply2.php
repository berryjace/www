<?php

/**
 * Description of Apply2
 *
 * @author Masud
 */
class Vendor_Form_Apply2 extends Zend_Form {

    private $clients;

    public function __construct($options = array()) {
        $this->clients = $options;
        parent::__construct();
    }

    public function init() {

        $organizations = new Zend_Form_Element_MultiCheckbox('client_id', array(
                    'multiOptions' => $this->clients
                ));
        $organizations->setDisableLoadDefaultDecorators(false)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please select organization'))))
                ->setRequired(true)
                ->setRegisterInArrayValidator(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submit')
                ->setAttrib('class', 'large awesome')
                ->setAttrib('value', 'Submit');

        $this->addElements(array($organizations, $submit))
                ->setDecorators(array(
                    'ViewHelper',
                    array('FormErrors', array('placement' => 'PREPEND')),
                    'Form'
                ));
    }
}
?>
