<?php

class Admin_Form_EmailInvoice extends Zend_Form {

    public function init() {

        $from_name = new Zend_Form_Element_Text('from_name');
        $from_name->setLabel('From Name')
                ->setAttrib('size', '16')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $from_email = new Zend_Form_Element_Text('from_email');
        $from_email->setLabel('From Email')
                ->setAttrib('size', '16')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $to_name = new Zend_Form_Element_Text('to_name');
        $to_name->setLabel('Contact Name')
                ->setAttrib('size', '16')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $to_email = new Zend_Form_Element_Text('to_email');
        $to_email->setLabel('Contact Email')
                ->setAttrib('size', '16')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $cc_email = new Zend_Form_Element_Text('cc_email');
        $cc_email->setLabel('CC')
                ->setAttrib('size', '16')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $subject = new Zend_Form_Element_Text('subject');
        $subject->setLabel('Subject')
                ->setAttrib('size', '16')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $email_body = new Zend_Form_Element_Textarea('email_body');
        $email_body->setLabel('Email Body')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttrib('id', 'email_body')
                ->setAttrib('rows', '7')
                ->setAttrib('class', 'text_area')
                ->setAttrib('cols', '90')
                ->setAttrib('size', '50')
                ->setRequired(true);

        $this->addElements(
                        array(
                            $from_name,
                            $from_email,
                            $to_name,
                            $to_email,
                            $cc_email,
                            $subject,
                            $email_body
                        )
                )
                ->setDecorators(array(
                    'ViewHelper',
                    array('FormErrors', array('placement' => 'PREPEND')),
                    'Form'
                ));
    }

}
