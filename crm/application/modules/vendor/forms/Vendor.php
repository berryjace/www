<?php

class Vendor_Form_Vendor extends Zend_Form {

    public function init() {

        $this->setName('vendor_edit');
        $this->setAttrib('enctype', 'multipart/form-data');

        $picture = new Zend_Form_Element_File('picture');
        $picture->setLabel('Upload Company Logo')
                //->setDestination('assets\files\user')
                // ensure only 1 file
                ->addValidator('Count', false, 1)
                // limit to 100K
                ->addValidator('Size', false, 1024000)
                // only JPEG, PNG, and GIFs
                ->addValidator('Extension', false, 'jpg,png,gif');
        

        $organization_name = new Zend_Form_Element_Text('organization_name');
        $organization_name->setLabel('Organization Name')
                        ->setAttrib('size', '40')
                        ->setAttrib('class', 'text')
                        ->setDisableLoadDefaultDecorators(true)
                        ->setDecorators(array('ViewHelper', 'Errors'))
                        ->addFilter('StripTags')
                        ->addFilter('StringTrim');

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email address')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter email'))))
                ->setAttrib('class', 'text')
                ->setAttrib('size', '40')
                ->setAttrib('onBlur', 'check_email()')
                ->addFilter('StringToLower')
                ->setRequired(true)
                ->addValidator('EmailAddress');

        $address_line1 = new Zend_Form_Element_Text('address_line1');
        $address_line1->setLabel('Address')
                //->setRequired(true)
                ->setAttrib('size', '50')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->addFilter('StripTags')
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->addFilter('StringTrim');

        $address_line2 = new Zend_Form_Element_Text('address_line2');
        $address_line2->setLabel('Address')
                ->setRequired(false)
                ->setAttrib('size', '50')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->addFilter('StripTags')
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->addFilter('StringTrim');

        $city = new Zend_Form_Element_Text('city');
        $city->setLabel('City')
               // ->setRequired(true)
                ->setAttrib('size', '40')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->addFilter('StripTags')
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->addFilter('StringTrim');

        $state = new Zend_Form_Element_Text('state');
        $state->setLabel('State')
                //->setRequired(true)
                ->setAttrib('size', '20')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->addFilter('StripTags')

                ->setDecorators(array('ViewHelper', 'Errors'))
                ->addFilter('StringTrim');

        $phone = new Zend_Form_Element_Text('phone');
        $phone->setLabel('Phone')
                ->setAttrib('size', '40')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->addFilter('StripTags')
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->addFilter('StringTrim');

        $fax = new Zend_Form_Element_Text('fax');
        $fax->setLabel('Fax')
                ->setRequired(false)
                ->setAttrib('size', '40')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->addFilter('StripTags')
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->addFilter('StringTrim');

        $zipcode = new Zend_Form_Element_Text('zipcode');
        $zipcode->setLabel('Zip code')
                // ->setRequired(true)
                ->setAttrib('size', '20')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                //->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter zipcode'))))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $website = new Zend_Form_Element_Text('website');
        $website->setLabel('Website')
                ->setAttrib('size', '40')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $vendor_sale_online = new Zend_Form_Element_Checkbox('vendor_sale_online');
        $vendor_sale_online->setLabel('Sale Online')
                ->setAttrib('class', 'checkbox')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'));
        
        $vendor_have_storefont = new Zend_Form_Element_Checkbox('vendor_have_storefont');
        $vendor_have_storefont->setLabel('Have a Storefont')
                ->setAttrib('class', 'checkbox')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'));
        
        $vendor_products = new Zend_Form_Element_Textarea('vendor_products');
        $vendor_products->setLabel('Products Offered')
                ->setAttrib('cols', '120')
                ->setAttrib('rows', '4')
                ->setAttrib('class', 'textarea')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $vendor_recommendation_to_client = new Zend_Form_Element_Textarea('vendor_recommendation_to_client');
        $vendor_recommendation_to_client->setLabel('Company Description')
                ->setAttrib('cols', '120')
                ->setAttrib('rows', '4')
                ->setAttrib('class', 'textarea')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $submit = new Zend_Form_Element_Submit('submit');

        $submit->setAttrib('id', 'submit');
        $submit->setAttrib('class', 'large awesome');


        $this->addElements(array($organization_name, $email,
                                $zipcode, $city, $state, $address_line1, $address_line2, $website,
                                $phone, $fax, $picture, $vendor_sale_online, $vendor_have_storefont, $vendor_products, $vendor_recommendation_to_client, $submit))
                ->setDecorators(array(
                    'ViewHelper',
                    array('FormErrors', array('placement' => 'PREPEND')),
                    'Form'
                ));
    }

}
