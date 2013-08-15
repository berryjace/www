<?php

/**
 * OnlinePayment to do online payment
 *
 * @author Masud
 */
class Vendor_Form_OnlinePayment extends Zend_Form {

    private $payment_option;

    public function __construct($payment_option=null) {
        $this->payment_option = $payment_option;
        parent::__construct();
    }

    public function init() {

        $payment_amount = new Zend_Form_Element_Radio('payment_amount');
        $payment_amount->addDecorator('Label', array('escape' => false))
                ->setLabel('<b>Select Payment Amount</b>')
                ->setAttrib('class', 'radio')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->setMultiOptions(array('owed' => " Full Amount Owed: ", 'other' => " Other Amount:"));

        $amount_owed = new Zend_Form_Element_Text('amount_owed');
        $amount_owed->setLabel('')
                ->setDecorators(array('ViewHelper'))
                ->setAttrib('size', '16')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $amount_other = new Zend_Form_Element_Text('amount_other');
        $amount_other->setLabel('')
                ->setDecorators(array('ViewHelper'))
                ->setAttrib('size', '16')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $account_number = new Zend_Form_Element_Text('account_number');
        $account_number->setLabel('Enter your bank account #:')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->addValidator('Digits', true, array('messages' => 'Account no must be digits'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $account_number_re = new Zend_Form_Element_Text('account_number_re');
        $account_number_re->setLabel('Re-enter your bank account #:')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
//                ->addValidator('NotEmpty', true, array('messages' => 'Enter your bank routing'))
                ->addValidator('Digits', true, array('messages' => 'Account no must be digits'))
//                ->addValidator(new BL_Validate_NotEqual(array('0' => 'account_number', '1' => 'account_number_re')))
                ->addValidator('Identical', false, array('token' => 'account_number'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $routing_number = new Zend_Form_Element_Text('routing_number');
        $routing_number->setLabel('Enter your bank routing #:')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
//                ->addValidator('NotEmpty', true, array('messages' => 'Enter your bank routing'))
                ->addValidator('Digits', true, array('messages' => 'Routing no must be digits'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $routing_number_re = new Zend_Form_Element_Text('routing_number_re');
        $routing_number_re->setLabel('Re-enter your bank routing #:')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->addValidator('Digits', true, array('messages' => 'Routing no must be digits'))
//                ->addValidator(new BL_Validate_NotEqual(array('0' => 'routing_number', '1' => 'routing_number_re')))
                ->addValidator('Identical', false, array('token' => 'routing_number'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $bankinfo = new Zend_Form_Element_MultiCheckbox('bankinfo', array('multiOptions' => array('saved' => ' Use saved bank information')));
        $bankinfo->addDecorator('Label', array('escape' => false))
                ->setLabel('<b>Bank Information</b>')
                ->setDisableLoadDefaultDecorators(true)
//                ->setDecorators(array('ViewHelper'))
                //->setRegisterInArrayValidator(false)
//                ->setValue('0')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $memo = new Zend_Form_Element_Text('memo');
        $memo->setLabel('Add a memo for future reference. (optional)')
        		->setAttrib('class', 'text')
        		->setDisableLoadDefaultDecorators(true)
        		->setRequired(false)
       			->addFilter('StripTags')
        		->addFilter('StringTrim');

        if ($this->payment_option === true) {
            $account_number->setRequired(true);
            $account_number_re->setRequired(true);
            $routing_number->setRequired(true);
            $routing_number_re->setRequired(true);
        }
        
        $this->addElements(
                        array(
                            $payment_amount,
                            $amount_owed,
                            $amount_other,
                            $bankinfo,
                            $account_number,
                            $account_number_re,
                            $routing_number,
                            $routing_number_re,
                        	$memo
                        )
                )
                ->setDecorators(array(
                    'ViewHelper',
                    array('FormErrors', array('placement' => 'PREPEND')),
                    'Form'
                ));
    }

}

