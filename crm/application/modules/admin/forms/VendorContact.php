<?php

class Admin_Form_VendorContact extends Zend_Form {

    public function init() {
        $this->setName('contact_form');
        $this->setAttrib('enctype', 'multipart/form-data');
        $addressLine1 = new Zend_Form_Element_Text('address_line_1');
        $addressLine1->setLabel('Address Line 1')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $addressLine2 = new Zend_Form_Element_Text('address_line_2');
        $addressLine2->setLabel('Address Line 2')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $city = new Zend_Form_Element_Text('city');
        $city->setLabel('City')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $state = new Zend_Form_Element_Text('state');
        $state->setLabel('State')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $zip = new Zend_Form_Element_Text('zip');
        $zip->setLabel('Zip')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $phone1 = new Zend_Form_Element_Text('phone_1');
        $phone1->setLabel('Phone 1')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Phone format must be (xxx) xxx-xxxx'))
                ->addFilter('StripTags')->addFilter('StringTrim');



        $phone2 = new Zend_Form_Element_Text('phone_2');
        $phone2->setLabel('Phone 2')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Phone format must be (xxx) xxx-xxxx'))
                ->addFilter('StripTags')->addFilter('StringTrim');





        $fax = new Zend_Form_Element_Text('fax');
        $fax->setLabel('Fax')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Recovery Email')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $company_email = new Zend_Form_Element_Text('company_email');
		$company_email->setLabel('Company Email')
				->setDisableLoadDefaultDecorators(true)
		        ->setAttribs(array('size' => '50', 'class' => 'text'))
		        ->setRequired(false)
		        ->addValidator('EmailAddress', true, array('messages' => 'Invalid email address'))
		        ->addFilter('StripTags')
                ->addFilter('StringTrim');


        $contactInfo = new Zend_Form_Element_Text('contact_info');
        $contactInfo->setLabel('Contact Info')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $sal = new Zend_Form_Element_Text('sal');
        $sal->setLabel('Sal.')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
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
                ->addFilter('StripTags')->addFilter('StringTrim');

        $mobile = new Zend_Form_Element_Text('mobile');
        $mobile->setLabel('Mobile')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $contactType = new Zend_Form_Element_Text('contact_type');
        $contactType->setLabel('Contact Type')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $webPage = new Zend_Form_Element_Text('web_page');
        $webPage->setLabel('Web Page')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $username = new Zend_Form_Element_Text('username');
        $username->setLabel('Username')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $companyName = new Zend_Form_Element_Text('company_name');
        $companyName->setLabel('Company Name')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $usercode = new Zend_Form_Element_Text('user_code');
        $usercode->setLabel('Vendor Number')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $this->addElements(array(
            $addressLine1,
            $addressLine2,
            $city,
            $state,
            $zip,
            $phone1,

            $phone2,

            $fax,
            $email,
            $contactInfo,
            $sal,
            $firstName,
            $lastName,
            $jobTitle,
            $workPhone,
            $mobile,
            $contactType,
            $webPage,
            $username,
            $companyName,
            $usercode,
            $company_email
        ));
    }

}

