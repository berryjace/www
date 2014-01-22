<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * author: Sukhon
 */

class Vendor_Form_Signature extends Zend_Form {

    public function init() {
        $this->setName('sign_form');
        $this->setAttrib('enctype', 'multipart/form-data');

        $vendor_signature = new Zend_Form_Element_Text('vendor_signature');
        $vendor_signature->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter signature'))))
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'vendor_signature')
                ->setAttrib('name', 'vendor_signature')
                ->setAttrib('size', '20')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $vendor_title = new Zend_Form_Element_Text('vendor_title');
        $vendor_title->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter title'))))
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'vendor_title')
                ->setAttrib('name', 'vendor_title')
                ->setAttrib('size', '20')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $approved = new Zend_Form_Element_Hidden('approved');
        $approved->setAttrib('id', 'approved')
                ->setDecorators(array('ViewHelper'));


        $vendor_name = new Zend_Form_Element_Text('vendor_name');
        $vendor_name->setLabel('Vendor Name')
                        ->setDisableLoadDefaultDecorators(true)
                        ->setAttribs(array('size' => '50', 'class' => 'text_dimmed', 'readonly'=>'readonly'))
                        ->setDecorators(array('ViewHelper', 'Errors'))
                        ->setRequired(true)
                        ->addFilter('StripTags')->addFilter('StringTrim');

        $client_name = new Zend_Form_Element_Text('client_name');
        $client_name->setLabel('client_name')
                        ->setDisableLoadDefaultDecorators(true)
                        ->setAttribs(array('size' => '50', 'class' => 'text_dimmed', 'readonly'=>'readonly'))
                        ->setDecorators(array('ViewHelper'))
                        ->setRequired(true)
                        ->addFilter('StripTags')->addFilter('StringTrim');

        $license_number = new Zend_Form_Element_Text('license_number');
        $license_number->setLabel('license_number')
                        ->setDisableLoadDefaultDecorators(true)
                        ->setAttribs(array('size' => '12', 'class' => 'text_dimmed', 'readonly'=>'readonly'))
                        ->setDecorators(array('ViewHelper'))
                        ->addFilter('StripTags')->addFilter('StringTrim');

        $royalty_structure = new Zend_Form_Element_Text('royalty_structure');
        $royalty_structure->setLabel('royalty_structure')
                        ->setDisableLoadDefaultDecorators(true)
                        ->setAttribs(array('size' => '70', 'class' => 'text_dimmed', 'readonly'=>'readonly'))
                        ->setRequired(false)
                        ->setDecorators(array('ViewHelper'))
                        ->addFilter('StripTags')->addFilter('StringTrim');

        $royalty_commission = new Zend_Form_Element_Text('royalty_commission');
        $royalty_commission->setLabel('royalty_commission')
                        ->setDisableLoadDefaultDecorators(true)
                        ->setAttribs(array('size' => '5', 'class' => 'text_dimmed', 'readonly'=>'readonly'))->setRequired(false)
                        ->setDecorators(array('ViewHelper'))
                        ->addFilter('StripTags')->addFilter('StringTrim');

        $annual_advance = new Zend_Form_Element_Text('annual_advance');
        $annual_advance->setLabel('annual_advance')
                        ->setDisableLoadDefaultDecorators(true)
                        ->setAttribs(array('size' => '5', 'class' => 'text_dimmed', 'readonly'=>'readonly'))->setRequired(false)
                        ->setDecorators(array('ViewHelper'))
                        ->addFilter('StripTags')->addFilter('StringTrim');

        $royalty_description = new Zend_Form_Element_Textarea('royalty_description');
        $royalty_description->setLabel('Royalty Description')
                        ->setDisableLoadDefaultDecorators(true)
                        ->setAttribs(array('cols' => '135', 'rows'=>'4', 'class' => 'textarea', 'readonly'=>'readonly'))->setRequired(true)
                        ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter Royalty Description'))))
                        //->setDecorators(array('ViewHelper'))
                        ->addFilter('StripTags')->addFilter('StringTrim');

        $grant_of_license = new Zend_Form_Element_Textarea('grant_of_license');
        $grant_of_license->setLabel('Grant of License')
                        ->setDisableLoadDefaultDecorators(true)
                        ->setAttribs(array('cols' => '135', 'rows'=>'4', 'class' => 'textarea', 'readonly'=>'readonly'))->setRequired(true)
                        //->setDecorators(array('ViewHelper'))
                        ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter Licensing Agreement'))))
                        ->addFilter('StripTags')->addFilter('StringTrim');


        $vendor_products = new Zend_Form_Element_Textarea('vendor_products');
        $vendor_products->setLabel('Vendor Products')
                        ->setDisableLoadDefaultDecorators(true)
                        ->setAttribs(array('cols' => '135', 'rows'=> '4', 'class' => 'textarea', 'readonly'=>'readonly'))->setRequired(true)
                        //->setDecorators(array('ViewHelper'))
                        ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter Vendor Products'))))
                        ->addFilter('StripTags')->addFilter('StringTrim');

        $agreement_statement = new Zend_Form_Element_Textarea('agreement_statement');
        $agreement_statement->setLabel('Licensing Agreement')
                        ->setDisableLoadDefaultDecorators(true)
                        ->setAttribs(array('cols' => '135', 'rows'=> '4', 'class' => 'textarea', 'readonly'=>'readonly'))->setRequired(true)
                        ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter Licensing Agreement'))));
                        //->setDecorators(array('ViewHelper'));

        $sample_file = new Zend_Form_Element_Hidden('sample_file');
        $sample_file->setLabel('sample file')
                        ->setDisableLoadDefaultDecorators(true)
                        ->setRequired(false)
                        ->setAttrib('name', 'sample_file[]')
                        ->setDecorators(array('ViewHelper'))
                        ->addFilter('StripTags')->addFilter('StringTrim');
        $add_sample = new Zend_Form_Element_Button('add_sample');
        $add_sample->setAttrib('id', 'add_sample')
                ->setAttrib('class', 'button button_blue')
                ->setAttrib('onclick', 'document.location')
                ->setDecorators(array('ViewHelper'));
        $add_sample->setLabel('Add Sample');

        $license_specific_note = new Zend_Form_Element_Textarea('license_specific_note');
        $license_specific_note->setLabel('Notes specific to the licensing agreement')
                        ->setDisableLoadDefaultDecorators(true)
                        ->setAttribs(array('cols' => '135', 'rows'=> '4', 'class' => 'textarea'))->setRequired(false)
                        //->setDecorators(array('ViewHelper'))
                        ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter Notes'))))
                        ->addFilter('StripTags')->addFilter('StringTrim');

        $recom_for_vendor = new Zend_Form_Element_Textarea('recom_for_vendor');
        $recom_for_vendor->setLabel('Notes For Vendor From Affinity')
                        ->setDisableLoadDefaultDecorators(true)
                        ->setAttribs(array('cols' => '135', 'rows'=> '4', 'class' => 'textarea'))->setRequired(true)
                        ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter Recommendation to Vendor'))))
                        ->addFilter('StripTags')->addFilter('StringTrim');

        $recom_for_client = new Zend_Form_Element_Textarea('recom_for_client');
        $recom_for_client->setLabel('Recommendation to Client')
                        ->setDisableLoadDefaultDecorators(true)
                        ->setAttribs(array('cols' => '135', 'rows'=> '4', 'class' => 'textarea'))->setRequired(true)
                        ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter Recommendation to Client'))))
                        ->addFilter('StripTags')->addFilter('StringTrim');

        $reject = new Zend_Form_Element_Button('reject');
        $reject->setAttrib('id', 'reject')
                ->setAttrib('class', 'button button_blue')
                ->setDecorators(array('ViewHelper'));
        $reject->setLabel('Decline');

        $approved = new Zend_Form_Element_Hidden('approved');
        $approved->setDecorators(array('ViewHelper'));

        $approve = new Zend_Form_Element_Button('approve');
        $approve->setAttrib('id', 'approve')
                ->setAttrib('class', 'button button_blue')
                ->setDecorators(array('ViewHelper'));
        $approve->setLabel('Submit Signature');

        $sample_product_list_link = new Zend_Form_Element_Hidden('sample_product_list_link');
        $sample_product_list_link->setDecorators(array('ViewHelper'))
                                ->setRequired(false)
                                ->setAttrib('name', 'sample_product_list_link')
                                ->setDecorators(array('ViewHelper'))
                                ->addFilter('StripTags')->addFilter('StringTrim');

        $supplier_name = new Zend_Form_Element_Text('supplier_name');
        $supplier_name->setLabel('Supplier Name')
                        ->setDisableLoadDefaultDecorators(true)
                        ->setAttribs(array('size' => '138', 'class' => 'text_dimmed', 'readonly'=>'readonly'))
                        ->setRequired(false)
                        ->setDecorators(array('ViewHelper'))
                        ->addFilter('StripTags')->addFilter('StringTrim');

        $target_audience = new Zend_Form_Element_Text('target_audience');
        $target_audience->setLabel('Target Audience')
                        ->setDisableLoadDefaultDecorators(true)
                        ->setAttribs(array('size' => '138', 'class' => 'text_dimmed', 'readonly'=>'readonly'))
                        ->setRequired(false)
                        ->setDecorators(array('ViewHelper'))
                        ->addFilter('StripTags')->addFilter('StringTrim');

        $email_body_text = new Zend_Form_Element_Textarea('email_body_text');
        $email_body_text->setLabel('Email Body:')
                        ->setDisableLoadDefaultDecorators(true)
                        ->setAttribs(array('cols' => '80', 'rows'=> '8', 'class' => 'textarea'))
                        ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter mail body'))));


        $this->addElements(array(
            $vendor_signature,
            $vendor_title,
            $vendor_name,
                $client_name,
                $license_number,
                $royalty_structure,
                $royalty_commission,
                $annual_advance,
                $royalty_description,
                $grant_of_license,
                $vendor_products,
                $agreement_statement, $sample_file, $add_sample,
                $recom_for_vendor,
                $approved,
                $reject,
                $approve,
                $sample_product_list_link,
                $supplier_name,
                $target_audience,
                $email_body_text));
    }
}

?>
