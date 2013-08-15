<?php

/**
 * Description of Apply_Form_Apply1
 *
 * @author Masud
 */
class Vendor_Form_Apply1 extends Zend_Form {

    public function init() {

        $this->setName('vendor_add');
        $this->setAttrib('enctype', 'multipart/form-data');

        $organization_name = new Zend_Form_Element_Text('organization_name');
        $organization_name->addDecorator('Label', array('escape' => false))
                ->setLabel('Company/Organization Name <sup class="errors">*</sup>')
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'organization_name')
                ->setAttrib('size', '50')
                ->setDisableLoadDefaultDecorators(true)
//                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Organization Name is required and can\'t be empty.'))))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $firstname = new Zend_Form_Element_Text('firstname');
        $firstname->addDecorator('Label', array('escape' => false))
                ->setLabel('Signatory First Name <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'First Name is required and can\'t be empty.'))))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $lastname = new Zend_Form_Element_Text('lastname');
        $lastname->addDecorator('Label', array('escape' => false))
                ->setLabel('Signatory Last Name <sup class="errors">*</sup>')
                ->setAttrib('size', '50')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
//                ->setDecorators(array('ViewHelper'))
                ->setRequired(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Last Name is required and can\'t be empty.'))))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $email = new Zend_Form_Element_Text('email');
        $email->addDecorator('Label', array('escape' => false))
                ->setLabel('Email Address <sup class="errors">*</sup>')
                ->setAttrib('class', 'text')
                ->setAttrib('size', '50')
                //->setAttrib('onBlur', 'check_email()')
                ->setRequired(true)
                ->setDisableLoadDefaultDecorators(true)
//                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Email is required and can\'t be empty.'))))
                ->setRequired(true)
                ->addValidator('EmailAddress')
                ->addFilter('StringToLower')
                ->addFilter('StringTrim')
                ->addFilter('StripTags');


        $address_line1 = new Zend_Form_Element_Text('address_line1');
        $address_line1->addDecorator('Label', array('escape' => false))
                ->setLabel('Address 1<sup class="errors">*</sup>')
                ->setRequired(false)
                ->setAttrib('size', '50')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Address 1 is required and can\'t be empty.'))))
                ->addFilter('StripTags')
//                ->setDecorators(array('ViewHelper'))
                ->addFilter('StringTrim');

        $address_line2 = new Zend_Form_Element_Text('address_line2');
        $address_line2->addDecorator('Label', array('escape' => false))
                ->setLabel('Address 2')
                ->setRequired(false)
                ->setAttrib('size', '50')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->addFilter('StripTags')
//                ->setDecorators(array('ViewHelper'))
                ->addFilter('StringTrim');

        $city = new Zend_Form_Element_Text('city');
        $city->addDecorator('Label', array('escape' => false))
                ->setLabel('City <sup class="errors">*</sup>')
                ->setRequired(true)
                ->setAttrib('size', '40')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'City is required and can\'t be empty.'))))
                ->addFilter('StripTags')
//                ->setDecorators(array('ViewHelper'))
                ->addFilter('StringTrim');

//        $state = new Zend_Form_Element_Text('state');
//        $state->setLabel('State <sup class="errors">*</sup>')
//                ->setRequired(false)
//                ->setAttrib('size', '20')
//                ->setAttrib('class', 'text')
//                ->setDisableLoadDefaultDecorators(true)
//                ->addFilter('StripTags')
//                ->setDecorators(array('ViewHelper'))
//                ->addFilter('StringTrim');

        $state_options = \BL_AMC::parseYAML(APPLICATION_PATH . '/configs/state_options.yml');
        $state = new Zend_Form_Element_Select('state');
        $state->addDecorator('Label', array('escape' => false))
                ->setLabel('State / Province <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'State / Province is required and can\'t be empty.'))))
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addMultiOptions($state_options);

        $phone = new Zend_Form_Element_Text('phone');
        $phone->addDecorator('Label', array('escape' => false))
                ->setLabel('Phone <sup class="errors">*</sup>')
                ->setAttrib('size', '40')
                ->setAttrib('class', 'text')
//                ->setDecorators(array('ViewHelper'))
                ->setDisableLoadDefaultDecorators(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Phone is required and can\'t be empty.'))))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $alternate_phone = new Zend_Form_Element_Text('alternate_phone');
        $alternate_phone->addDecorator('Label', array('escape' => false))
                ->setLabel('Alternate Phone')
                ->setAttrib('size', '40')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
//                ->setDecorators(array('ViewHelper'))
                ->addFilter('StringTrim');

        $fax = new Zend_Form_Element_Text('fax');
        $fax->addDecorator('Label', array('escape' => false))
                ->setLabel('Fax')
                ->setRequired(false)
                ->setAttrib('size', '40')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
                ->addFilter('StripTags')
//                ->setDecorators(array('ViewHelper'))
                ->addFilter('StringTrim');

        $zipcode = new Zend_Form_Element_Text('zipcode');
        $zipcode->addDecorator('Label', array('escape' => false))
                ->setLabel('Zip Code <sup class="errors">*</sup>')
                ->setAttrib('size', '20')
                ->setAttrib('class', 'text')
                ->setDisableLoadDefaultDecorators(true)
//                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Zip Code is required and can\'t be empty.'))))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $website = new Zend_Form_Element_Text('website');
        $website->addDecorator('Label', array('escape' => false))
                ->setLabel('Website')
                ->setAttrib('size', '40')
                ->setAttrib('class', 'text')
                ->setRequired(false)
                ->setDisableLoadDefaultDecorators(true)
//                ->setDecorators(array('ViewHelper'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $v_past_experience = new Zend_Form_Element_Textarea('v_past_experience');
        $v_past_experience->addDecorator('Label', array('escape' => false))
                ->setLabel('Prior Experience with Licensors ')
                ->setAttrib('rows', 7)
                ->setAttrib('cols', 62)
                ->setRequired(false)
//                ->setDecorators(array('ViewHelper'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submit')
                ->setAttrib('class', 'large awesome')
                ->setAttrib('value', 'Sign Up');

        $this->addElements(
                array(
                    $organization_name,
                    $firstname,
                    $lastname,
                    $email,
                    $address_line1,
                    $address_line2,
                    $zipcode,
                    $city,
                    $state,
                    $website,
                    $phone,
                    $alternate_phone,
                    $fax,
                    $v_past_experience,
                    $submit
        ));
    }

}

?>
