<?php

class Admin_Form_AddVendor extends Zend_Form {

    public function init() {
        $this->setName('addVendor_form');

        $username = new Zend_Form_Element_Text('username');
        $username->addDecorator('Label', array('escape' => false))
//                ->setLabel('Username <sup class="errors">*</sup>')
                ->setLabel('Username')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text medium'))
//                ->setRequired(true)
                ->setRequired(false)
                ->addValidator(new Zend_Validate_Db_NoRecordExists(array('table' => 'users', 'field' => 'username')), true, array('messages' => 'Username already exists.'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
        		->addValidator('alnum', true, array('messages'=>'Usernam can only contain letters and numbers (no spaces, spcial characters, etc.)'));

        $password = new Zend_Form_Element_Password('password');
        $password->addDecorator('Label', array('escape' => false))
//                ->setLabel('Password <sup class="errors">*</sup>')
                ->setLabel('Password')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text medium'))
//                ->setRequired(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $confirm_password = new Zend_Form_Element_Password('confirm_password');
        $confirm_password->addDecorator('Label', array('escape' => false))
//                ->setLabel('Confirm password <sup class="errors">*</sup>')
                ->setLabel('Confirm Password')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text medium'))
//                ->setRequired(true)
                ->setRequired(false)
                ->addValidator('Identical', false, array('token' => 'password'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $email = new Zend_Form_Element_Text('recovery_email');
        $email->addDecorator('Label', array('escape' => false))
//                ->setLabel('Recovery email <sub class="errors">*</sup>')
                ->setLabel('Recovery Email')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text medium'))
//                ->setRequired(true)
                ->setRequired(false)
                ->addValidator('EmailAddress', true, array('messages' => 'Invalid email address'))
                ->addValidator(new Zend_Validate_Db_NoRecordExists(array('table' => 'users', 'field' => 'email')), true, array('messages' => 'Email already exists'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $user_status = \BL_AMC::parseYAML(APPLICATION_PATH . '/configs/user_status.yml');
        $status_array = array('' => 'Select');
        foreach ($user_status as $key => $value) {
            $status_array[$value] = $value;
        }
        $status = new Zend_Form_Element_Select('status');
        $status->addDecorator('Label', array('escape' => false))
                ->setLabel('Status <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->addErrorMessage('Select user status')
                ->setAttribs(array('class' => 'elWidth'))
                ->setRequired(true)
                ->addMultiOptions($status_array)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $organization_name = new Zend_Form_Element_Text('organization_name');
        $organization_name->addDecorator('Label', array('escape' => false))
                ->setLabel('Company name <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $address_line_1 = new Zend_Form_Element_Text('address_line_1');
        $address_line_1->addDecorator('Label', array('escape' => false))
//                ->setLabel('Address 1 <sup class="errors">*</sup>')
                ->setLabel('Address 1')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
//                ->setRequired(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $address_line_2 = new Zend_Form_Element_Text('address_line_2');
        $address_line_2->setLabel('Address 2')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $city = new Zend_Form_Element_Text('city');
        $city->addDecorator('Label', array('escape' => false))
//                ->setLabel('City <sup class="errors">*</sup>')
                ->setLabel('City')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
//                ->setRequired(true)
                ->setRequired(false)
                ->addErrorMessage('Value is required')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        //$state = new Zend_Form_Element_Text('state');
        //$state->addDecorator('Label', array('escape' => false))
//                ->setLabel('State <sup class="errors">*</sup>')->setRequired(true)->addErrorMessage('Value is required')
        //->setLabel('State')->setDisableLoadDefaultDecorators(true)->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)->addValidator('Regex', true, array('pattern' => '/^[A-Z]{2}$/i', 'messages' => 'Must be two letter format(example- XX)'))->addFilter('StripTags')->addFilter('StringTrim');

        $state_options = \BL_AMC::parseYAML(APPLICATION_PATH . '/configs/state_options.yml');
        $state = new Zend_Form_Element_Select('state');
        $state->setLabel('State / Province')
                ->setDisableLoadDefaultDecorators(true)
                ->addMultiOptions($state_options);

        $zip = new Zend_Form_Element_Text('zip');
        $zip->setLabel('Zip')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $phone_1 = new Zend_Form_Element_Text('phone_1');
        $phone_1->addDecorator('Label', array('escape' => false))
//                ->setLabel('Phone <sup class="errors">*</sup>')
                ->setLabel('Phone 1')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
//                ->setRequired(true)
                ->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Phone must be (xxx) xxx-xxxx format'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');


        $phone_2 = new Zend_Form_Element_Text('phone_2');
        $phone_2->setLabel('Phone 2')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
               ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Phone must be (xxx) xxx-xxxx format'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $fax = new Zend_Form_Element_Text('fax');
        $fax->setLabel('Fax')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Fax must be (xxx) xxx-xxxx format'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $company_email = new Zend_Form_Element_Text('company_email');
        $company_email->setLabel('Company Email')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addValidator('EmailAddress', true, array('messages' => 'Invalid email address'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $web_page = new Zend_Form_Element_Text('web_page');
        $web_page->setLabel('Web Page')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $v_sal = new Zend_Form_Element_Select('v_sal');
        $v_sal->setLabel('Sal.')
                ->setDisableLoadDefaultDecorators(true)
                ->addErrorMessage('Value is required')
                ->setAttribs(array('class' => 'elWidth'))
                ->setRequired(false)
                ->addMultiOptions(array("Mr." => "Mr.",
                    "Mrs." => "Mrs.",
                    "Ms." => "Ms."
                ))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $v_first_name = new Zend_Form_Element_Text('v_first_name');
        $v_first_name->setLabel('First name')
                ->setDisableLoadDefaultDecorators(true)
                ->addErrorMessage('Value is required')
                ->setAttribs(array('class' => 'elWidth'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $v_last_name = new Zend_Form_Element_Text('v_last_name');
        $v_last_name->setLabel('Last name')
                ->setDisableLoadDefaultDecorators(true)
                ->addErrorMessage('Value is required')
                ->setAttribs(array('class' => 'elWidth'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $v_title = new Zend_Form_Element_Text('v_title');
        $v_title->setLabel('Job title')
                ->setDisableLoadDefaultDecorators(true)
                ->addErrorMessage('Value is required')
                ->setAttribs(array('class' => 'elWidth'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $v_phone = new Zend_Form_Element_Text('v_phone');
        $v_phone->setLabel('Work phone')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('class' => 'elWidth'))
                ->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Phone format must be (xxx) xxx-xxxx'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

//        $v_phone_ext = new Zend_Form_Element_Text('v_phone_ext');
//        $v_phone_ext ->setLabel('Ext.')
//                ->setDisableLoadDefaultDecorators(true)
//                ->setAttribs(array('class' => 'elWidth'))
//                ->setRequired(false)
//                ->addFilter('StripTags')
//                ->addFilter('StringTrim');


        $v_mobile = new Zend_Form_Element_Text('v_mobile');
        $v_mobile->setLabel('Mobile')
                ->setDisableLoadDefaultDecorators(true)
                ->addErrorMessage('Invalid mobile no')
                ->setAttribs(array('class' => 'elWidth'))
                ->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Mobile format must be in (xxx) xxx-xxxx'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $v_email = new Zend_Form_Element_Text('v_email');
        $v_email->setLabel('Email')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('class' => 'elWidth'))
                ->setRequired(false)
                ->addValidator('EmailAddress', true, array('messages' => 'Invalid email address'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $v_contact_type = new Zend_Form_Element_Select('v_contact_type');
        $v_contact_type->setLabel('Contact Type')
                ->setDisableLoadDefaultDecorators(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Select contact type'))))
                ->setRequired(false)
                ->addMultiOptions(array(
                    "" => "Select",
                    "Primary" => "Primary",
                    "Alternate" => "Alternate",
                    "Royalties" => "Royalties",
                    "Attorney" => "Attorney",
                    "Send Ad Info" => "Send Ad Info",
                    "Sent Art To" => "Sent Art To",
                    "Past Empl" => "Past Empl",
                    "Send Agreements To" => "Send Agreements To"))
                ->setAttribs(array('class' => 'elWidth'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $this->addElements(array(
            $username,
            $password,
            $confirm_password,
            $email,
            $status,
            $organization_name,
            $address_line_1,
            $address_line_2,
            $phone_1,
            $phone_2,
            $city,
            $state,
            $zip,
            $fax,
            $company_email,
            $web_page,
            $v_sal,
            $v_first_name,
            $v_last_name,
            $v_title,
            $v_phone,
//            $v_phone_ext,
            $v_mobile,
            $v_email,
            $v_contact_type
        ));
    }

}

