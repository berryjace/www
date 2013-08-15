<?php

/**
 * Description of Signup
 *
 * @author Masud
 */
class Application_Form_Signup extends Zend_Form {

    public function init() {

        $this->setName('signup_form');

        $username = new Zend_Form_Element_Text('username');
        $username->addDecorator('Label', array('escape' => false))
                ->setLabel('Username <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(true)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $password = new Zend_Form_Element_Password('password');
        $password->addDecorator('Label', array('escape' => false))
                ->setLabel('Password <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $confirm_password = new Zend_Form_Element_Password('confirm_password');
        $confirm_password->addDecorator('Label', array('escape' => false))
                ->setLabel('Confirm Password <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim')
                ->addValidator('Identical', false, array('token' => 'password'));

        $email = new Zend_Form_Element_Text('email');
        $email->addDecorator('Label', array('escape' => false))
                ->setLabel('Recovery Email <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(true)
                ->addValidator('EmailAddress', true, array('messages' => 'Invalid email address'))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $organization_name = new Zend_Form_Element_Text('organization_name');
        $organization_name->addDecorator('Label', array('escape' => false))
                ->setLabel('Company Name <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(true)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $address_line_1 = new Zend_Form_Element_Text('address_line_1');
        $address_line_1->setLabel('Address 1/Apt')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $address_line_2 = new Zend_Form_Element_Text('address_line_2');
        $address_line_2->setLabel('Address 2/Apt')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $city = new Zend_Form_Element_Text('city');
        $city->setLabel('City')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim')
                ->addErrorMessage('City required');

//        $state = new Zend_Form_Element_Text('state');
//        $state->setLabel('State / Province')
//                ->setDisableLoadDefaultDecorators(true)
//                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
//                ->addValidator('Regex', true, array('pattern' => '/^[A-Z]{2}$/i', 'messages' => 'Must be two letter format(example- XX)'))
//                ->addFilter('StripTags')->addFilter('StringTrim');
        
        $state_options = \BL_AMC::parseYAML(APPLICATION_PATH . '/configs/state_options.yml');
        $state = new Zend_Form_Element_Select('state');
        $state->setLabel('State / Province')
                ->setDisableLoadDefaultDecorators(true)
                ->addMultiOptions($state_options);

        $zip = new Zend_Form_Element_Text('zip');
        $zip->setLabel('Zip')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim')
                ->addErrorMessage('Zip required');

        $phone_1 = new Zend_Form_Element_Text('phone_1');
        $phone_1->setLabel('Phone 1/Ext')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Phone must be (xxx) xxx-xxxx format'))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $phone_2 = new Zend_Form_Element_Text('phone_2');
        $phone_2->setLabel('Phone 2/Ext')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Phone must be (xxx) xxx-xxxx format'))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $fax = new Zend_Form_Element_Text('fax');
        $fax->setLabel('Fax')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Fax must be (xxx) xxx-xxxx format'))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $company_email = new Zend_Form_Element_Text('company_email');
        $company_email->setLabel('Company Email')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addValidator('EmailAddress', true, array('messages' => 'Invalid email address'))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $web_page = new Zend_Form_Element_Text('web_page');
        $web_page->setLabel('Web Page')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $this->addElements(array(
            $username,
            $password,
            $confirm_password,
            $email,
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
        ));
    }

}