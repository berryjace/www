<?php

/**
 * Description of Apply5
 *
 * @author Masud
 */
class Vendor_Form_Apply5 extends Zend_Form {

    private $payment = '';
    private $amount = '';

    public function __construct($options = null, $amount=null) {
        $this->payment = $options;
        $this->amount = $amount;
        parent::__construct();
    }

    public function init() {

        $obj = new BL_View_Helper_BUtils();

        $amount_total = new Zend_Form_Element_Hidden('amount_total');
        $amount_total->setAttrib('size', '40')
                ->setAttrib('class', 'text')
//                ->setAttrib('value', $this->amount)
                ->setDecorators(array('ViewHelper'))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $bank_acc_no = new Zend_Form_Element_Text('bank_acc_no');
        $bank_acc_no->setLabel('')
                ->setAttrib('class', 'text')
                ->setDecorators(array('ViewHelper'))
                ->setDisableLoadDefaultDecorators(true)
                ->setValidators(
                        array(
                            array('NotEmpty', true, array('messages' => 'Enter your bank account.')),
                            array('Digits', true, array('messages' => 'Please enter digits'))))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $bank_acc_no_re = new Zend_Form_Element_Text('bank_acc_no_re');
        $bank_acc_no_re->setLabel('')
                ->setAttrib('class', 'text')
                ->setDecorators(array('ViewHelper'))
                ->setDisableLoadDefaultDecorators(true)
                ->setValidators(
                        array(
                            array('NotEmpty', true, array('messages' => 'Re-enter your bank account.')),
                            array('Digits', true, array('messages' => 'Please enter digits'))))
                ->addValidator(new BL_Validate_NotEqual(array('0' => 'bank_acc_no', '1' => 'bank_acc_no_re')))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $bank_routing = new Zend_Form_Element_Text('bank_routing');
        $bank_routing->setLabel('')
                ->setAttrib('class', 'text')
                ->setDecorators(array('ViewHelper'))
                ->setDisableLoadDefaultDecorators(true)
                ->setValidators(
                        array(
                            array('NotEmpty', true, array('messages' => 'Enter your bank routing.')),
                            array('Digits', true, array('messages' => 'Please enter digits'))))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $bank_routing_re = new Zend_Form_Element_Text('bank_routing_re');
        $bank_routing_re->setLabel('')
                ->setAttrib('class', 'text')
                ->setDecorators(array('ViewHelper'))
                ->setDisableLoadDefaultDecorators(true)
                ->setValidators(
                        array(
                            array('NotEmpty', true, array('messages' => 'Re-enter your bank routing.')),
                            array('Digits', true, array('messages' => 'Please enter digits'))))
                ->addValidator(new BL_Validate_NotEqual(array('0' => 'bank_routing', '1' => 'bank_routing_re')))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $billing_options = new Zend_Form_Element_Radio('billing_options');
        $billing_options->setLabel('')
                ->setAttrib('class', 'radio')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please select billing type'))))
                ->setRequired(true)
                ->setMultiOptions(array(
                    'card' => ' Deduct [' . $obj->getCurrency($this->amount) . '] fee from my bank account.',
                    'check' => ' Do not bill me at this time. Send me an invoice and I will mail a check'));

        $submit = new Zend_Form_Element_Button('submit');
        $submit->setAttrib('id', 'submit')
                ->setAttrib('class', 'large awesome')
                ->setAttrib('value', 'Submit');

        if ($this->payment == 'card') {
            $bank_acc_no->setRequired(true);
            $bank_acc_no_re->setRequired(true);
            $bank_routing->setRequired(true);
            $bank_routing_re->setRequired(true);
        }

        $this->addElements(
                        array(
                            $amount_total,
                            $bank_acc_no,
                            $bank_acc_no_re,
                            $bank_routing,
                            $bank_routing_re,
                            $billing_options,
                            $submit
                        )
                )
                ->setDecorators(array(
                    'ViewHelper',
                    array('FormErrors', array('placement' => 'PREPEND')),
                    'Form'
                ));
    }

}
