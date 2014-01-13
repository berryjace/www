<?php

/**
 * Class to edit or update vendor contacts
 */
class Admin_Form_Contact extends Zend_Form {

    public function init() {
        $this->setName('contact_form');
        $this->setAttrib('enctype', 'multipart/form-data');

        $editContact = new Zend_Form_Element_Hidden('editContact');
        $editContact->setValue('0')
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->setDisableLoadDefaultDecorators(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $username = new Zend_Form_Element_Text('username');
        $username->setLabel('User Name')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text desable'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $user_password = new Zend_Form_Element_Password('user_password');
        $user_password->setLabel('Password')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text desable', 'type' => 'password'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $addressLine1 = new Zend_Form_Element_Text('address_line_1');
        $addressLine1->setLabel('Address 1/Apt')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text desable'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $addressLine2 = new Zend_Form_Element_Text('address_line_2');
        $addressLine2->setLabel('Address 2/Apt')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text desable'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $city = new Zend_Form_Element_Text('city');
        $city->setLabel('City')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text desable'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

//        $state = new Zend_Form_Element_Text('state');
//        $state->setLabel('State *')
//                ->setDisableLoadDefaultDecorators(true)
//                ->setAttribs(array('size' => '50', 'class' => 'text desable'))->setRequired(false)
//                ->addFilter('StripTags')->addFilter('StringTrim');

        $state_options = \BL_AMC::parseYAML(APPLICATION_PATH . '/configs/state_options.yml');
        $state = new Zend_Form_Element_Select('state');
        $state->setLabel('State / Province')
                ->setDisableLoadDefaultDecorators(true)
                ->addMultiOptions($state_options);

        $zip = new Zend_Form_Element_Text('zip');
        $zip->setLabel('Zip')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text desable'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $phone1 = new Zend_Form_Element_Text('phone_1');
        $phone1->setLabel('Phone 1')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text desable'))->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Phone format must be (xxx) xxx-xxxx'))
                ->addFilter('StripTags')->addFilter('StringTrim');



        $phone2 = new Zend_Form_Element_Text('phone_2');
        $phone2->setLabel('Phone 2')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text desable'))->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Phone format must be (xxx) xxx-xxxx'))
                ->addFilter('StripTags')->addFilter('StringTrim');




        $fax = new Zend_Form_Element_Text('fax');
        $fax->setLabel('Fax')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text desable'))->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Fax format be (xxx) xxx-xxxx'))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Gateway Main Contact/Recovery Email')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text desable'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $company_email = new Zend_Form_Element_Text('company_email');
        $company_email->setLabel('Company Email')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text desable'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');


        $contactInfo = new Zend_Form_Element_Text('contact_info');
        $contactInfo->setLabel('Contact Info')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text desable'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $sal = new Zend_Form_Element_Text('sal');
        $sal->setLabel('Sal.')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text desable'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $firstName = new Zend_Form_Element_Text('first_name');
        $firstName->setLabel('First Name')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $lastName = new Zend_Form_Element_Text('last_name');
        $lastName->setLabel('Last Name')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $jobTitle = new Zend_Form_Element_Text('job_title');
        $jobTitle->setLabel('Job Title')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $workPhone = new Zend_Form_Element_Text('work_phone');
        $workPhone->setLabel('Work Phone')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Phone must be (xxx) xxx-xxxx format'))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $mobile = new Zend_Form_Element_Text('mobile');
        $mobile->setLabel('Mobile')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Phone must be (xxx) xxx-xxxx format'))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $contactType = new Zend_Form_Element_Text('contact_type');
        $contactType->setLabel('Contact Type')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $webPage = new Zend_Form_Element_Text('web_page');
        $webPage->setLabel('Web Page')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text desable'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

//        $username = new Zend_Form_Element_Text('username');
//        $username->setLabel('Username')
//                ->setDisableLoadDefaultDecorators(true)
//                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
//                ->addFilter('StripTags')->addFilter('StringTrim');

        $organizationName = new Zend_Form_Element_Text('organization_name');
        $organizationName->setLabel('Company Name *')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text desable'))->setRequired(true)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $vendor_number = new Zend_Form_Element_Text('vendor_number');
        $vendor_number->setLabel('Vendor Number')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text desable'))->setRequired(true)
                ->addFilter('StripTags')->addFilter('StringTrim');


        /*         * ********* add contact *********** */
        $v_addNew = new Zend_Form_Element_Hidden('v_addNew');
        $v_addNew->setValue('0')
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->setDisableLoadDefaultDecorators(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $v_sal = new Zend_Form_Element_Select('v_sal');
        $v_sal->setLabel('')
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->setDisableLoadDefaultDecorators(true)
                ->addErrorMessage('required')
                ->setAttribs(array('class' => 'elWidth', 'size' => '1', 'maxlength' => '4'))->setRequired(true)
                ->addMultiOptions(array("Mr." => "Mr.",
                    "Mrs." => "Mrs.",
                    "Ms." => "Ms."
                ))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $v_first_name = new Zend_Form_Element_Text('v_first_name');
        $v_first_name->setLabel('')
                ->setValue('')
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->setDisableLoadDefaultDecorators(true)
                ->addErrorMessage('required')
                ->setAttribs(array('class' => 'elWidth'))
//                ->setRequired(true)
                ->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $v_last_name = new Zend_Form_Element_Text('v_last_name');
        $v_last_name->setLabel('')
                ->setValue('')
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->setDisableLoadDefaultDecorators(true)
                ->addErrorMessage('required')
                ->setAttribs(array('class' => 'elWidth'))
//                ->setRequired(true)
                ->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $v_title = new Zend_Form_Element_Text('v_title');
        $v_title->setLabel('')
                ->setValue('')
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->setDisableLoadDefaultDecorators(true)
                ->addErrorMessage('required')
                ->setAttribs(array('class' => 'elWidth'))
//                ->setRequired(true)
                ->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $v_phone = new Zend_Form_Element_Text('v_phone');
        $v_phone->setLabel('')
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->setDisableLoadDefaultDecorators(true)
                ->addErrorMessage('required')
                ->setAttribs(array('class' => 'elWidth'))
//                ->setRequired(true)
                ->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $v_phone_ext = new Zend_Form_Element_Text('v_phone_ext');
        $v_phone_ext->setLabel('')
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->setDisableLoadDefaultDecorators(true)
                ->addErrorMessage('required')
                ->setAttribs(array('class' => 'elWidth', 'size' => '4', 'maxlength' => '4'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $v_mobile = new Zend_Form_Element_Text('v_mobile');
        $v_mobile->setLabel('')
                ->setValue('')
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->setDisableLoadDefaultDecorators(true)
                ->addErrorMessage('required')
                ->setAttribs(array('class' => 'elWidth'))
//                ->setRequired(true)
                ->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $v_fax = new Zend_Form_Element_Text('v_fax');
        $v_fax->setLabel('')
                ->setValue('')
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->setDisableLoadDefaultDecorators(true)
                ->addErrorMessage('required')
                ->setAttribs(array('class' => 'elWidth'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $v_email = new Zend_Form_Element_Text('v_email');
        $v_email->setLabel('')
                ->setValue('')
                ->removeDecorator('label')
                ->removeDecorator('HtmlTag')
                ->setDisableLoadDefaultDecorators(true)
                ->addErrorMessage('required')
                ->setAttribs(array('class' => 'elWidth'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $v_contact_type = new Zend_Form_Element_Select('v_contact_type');
        $v_contact_type->setLabel('')
		->removeDecorator('label')
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

//        $v_contact_type = new Zend_Form_Element_Text('v_contact_type');
//        $v_contact_type->setLabel('')
//                ->removeDecorator('label')
//                ->removeDecorator('HtmlTag')
//                ->setDisableLoadDefaultDecorators(true)
//                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Invalid'))))
//                ->setAttribs(array('class' => 'elWidth'))->setRequired(false)
//                ->addFilter('StripTags')
//                ->addFilter('StringTrim');
//

        /*         * ******************** */
        $this->addElements(array(
            $editContact,
            $username,
            $user_password,
            $addressLine1,
            $addressLine2,
            $city,
            $state,
            $zip,
            $phone1,
            $phone2,
            $fax,
            $email,
            $company_email,
            $contactInfo,
            $sal,
            $webPage,
            //$username,
            $organizationName,
            $vendor_number,
            $v_addNew,
            $v_sal,
            $v_first_name,
            $v_last_name,
            $v_title,
            $v_phone,
            $v_phone_ext,
            $v_mobile,
            $v_email,
            $v_fax,
            $v_contact_type
        ));
    }

}

