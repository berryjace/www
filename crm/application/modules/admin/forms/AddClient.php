<?php

class Admin_Form_AddClient extends Zend_Form {

//    private $states;
    private $org_type;
    private $selected_org_type;

    public function __construct($options = null) {
//        $this->states = $options['states'];
        if (!is_null($options['org_type'])) {
            $this->org_type = $options['org_type'];
        }
        $this->selected_org_type = !is_null($options['greek_org_type']) ? $options['greek_org_type'] : '0';
        parent::__construct($options);
    }

    public function init() {
        /* Form Elements & Other Definitions Here ... */
        $this->setName('addClient');

        $name = new Zend_Form_Element_Text('name');
        $name->addDecorator('Label', array('escape' => false))
                ->setLabel('Client Name <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(true)
                ->addErrorMessage('Value is required')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $greekLetters = new Zend_Form_Element_Text('greek_letters');
        $greekLetters->addDecorator('Label', array('escape' => false))
                ->setLabel('GREEK Letters <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(true)
                ->addErrorMessage('Value is required')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $description = new Zend_Form_Element_Text('description');
        $description->setLabel('Description')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addErrorMessage('Value is required')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $address1 = new Zend_Form_Element_Text('address1');
        $address1->addDecorator('Label', array('escape' => false))
                ->setLabel('Address 1 <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(true)
                ->addErrorMessage('Value is required')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $address2 = new Zend_Form_Element_Text('address2');
        $address2->setLabel('Address 2')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addErrorMessage('Value is required')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $city = new Zend_Form_Element_Text('city');
        $city->addDecorator('Label', array('escape' => false))
                ->setLabel('City <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(true)
                ->addErrorMessage('Value is required')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

//        $state = new Zend_Form_Element_Select('state');
//        $state->addDecorator('Label', array('escape' => false))
//                ->setLabel('State <sup class="errors">*</sup>')
//                ->setDisableLoadDefaultDecorators(true)
//                ->setRequired(true)
//                ->addErrorMessage('Select state')
//                ->addMultiOptions($this->states)
//                ->addFilter('StripTags')
//                ->addFilter('StringTrim');

        $state_options = \BL_AMC::parseYAML(APPLICATION_PATH . '/configs/state_options.yml');
        $state = new Zend_Form_Element_Select('state');
        $state->setLabel('State / Province *')
                ->setDisableLoadDefaultDecorators(true)
                ->addErrorMessage('Select state')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addMultiOptions($state_options);

        $contactPerson = new Zend_Form_Element_Text('contact_person');
        $contactPerson->setLabel('Portal Contact Person')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addErrorMessage('Value is required')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $email = new Zend_Form_Element_Text('email');
        $email->addDecorator('Label', array('escape' => false))
                ->setLabel('Portal Contact Email <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(true)
                ->setValidators(
                        array(
                            array('NotEmpty', true, array('messages' => 'Value is required')),
                            array('EmailAddress', true, array('messages' => 'Invalid email address.')),
                            array(new Zend_Validate_Db_NoRecordExists(
                                        array(
                                            'table' => 'users',
                                            'field' => 'email')
                                ), true, array('messages' => 'Email already exists'))
                ))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $webAddress = new Zend_Form_Element_Text('web_address');
        $webAddress->setLabel('Web Address')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addErrorMessage('Value is required')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $phone = new Zend_Form_Element_Text('phone');
        $phone->addDecorator('Label', array('escape' => false))
                ->setLabel('Phone No. <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Value is required'))))
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Phone must be in (xxx) xxx-xxxx format'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $fax = new Zend_Form_Element_Text('fax');
        $fax->setLabel('Fax No.')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Fax must be (xxx) xxx-xxxx format'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $zip = new Zend_Form_Element_Text('zip');
        $zip->addDecorator('Label', array('escape' => false))
                ->setLabel('Zip Code <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(true)
                ->addErrorMessage('Value is required')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $username = new Zend_Form_Element_Text('username');
        $username->addDecorator('Label', array('escape' => false))
                ->setLabel('Username <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(true)
                ->setValidators(array(
                    array('NotEmpty', true, array('messages' => 'Value is required')),
                    array(new Zend_Validate_Db_NoRecordExists(
                                array(
                                    'table' => 'users',
                                    'field' => 'username')
                        ), true, array('messages' => 'Username already exists')),
                	array('alnum', true, array('messages' => 'Usernam can only contain letters and numbers (no spaces, spcial characters, etc.)'))
                ))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $password = new Zend_Form_Element_Password('password');
        $password->addDecorator('Label', array('escape' => false))
                ->setLabel('Password <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(true)
                ->addErrorMessage('Value is required')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $renewalFee = new Zend_Form_Element_Text('renewal_fee');
        $renewalFee->setLabel('Default Renewal Fee')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addErrorMessage('Value is required')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $lateFee = new Zend_Form_Element_Text('late_fee');
        $lateFee->setLabel('Default Late Fee')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addErrorMessage('Value is required')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $organisationType = new Zend_Form_Element_Select('organisation_type');
        $organisationType->addDecorator('Label', array('escape' => false))
                ->setLabel('Organization Type <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->addMultiOptions($this->org_type)
                ->setValue($this->selected_org_type)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Select organization type'))))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $foundingTear = new Zend_Form_Element_Text('founding_year');
        $foundingTear->setLabel('Founding Year')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text date'))
                ->setRequired(false)
                ->addErrorMessage('Value is required')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $numberOfAlumni = new Zend_Form_Element_Text('number_of_alumni');
        $numberOfAlumni->setLabel('Number of Alumni/ae')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addErrorMessage('Value is required')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $numberOfUndergrads = new Zend_Form_Element_Text('number_of_undergrads');
        $numberOfUndergrads->setLabel('Number of UnderGrads')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addErrorMessage('Value is required')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $numberOfAlumniChapters = new Zend_Form_Element_Text('number_of_alumni_chapters');
        $numberOfAlumniChapters->setLabel('Number of Alumni/ae Chapters')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addErrorMessage('Value is required')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $totalUgChapters = new Zend_Form_Element_Text('total_ug_chapters');
        $totalUgChapters->setLabel('Total UG Chapters')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addErrorMessage('Value is required')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $grantOnLicense = new Zend_Form_Element_Textarea('grant_on_license');
        $grantOnLicense->setLabel('Grant on License')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('cols' => '40', 'rows' => '12', 'class' => 'text_area'))
                ->setRequired(false)
                ->addErrorMessage('Value is required')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $user_code = new Zend_Form_Element_Text('user_code');
        $user_code->setLabel('Client Number')
		        ->setDisableLoadDefaultDecorators(true)
		        ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
		        ->addFilter('StripTags')->addFilter('StringTrim');

        $this->addElements(array(
        	$user_code,
            $name,
            $greekLetters,
            $description,
            $address1,
            $address2,
            $city,
            $state,
            $contactPerson,
            $email,
            $webAddress,
            $phone,
            $fax,
            $zip,
            $username,
            $password,
            $renewalFee,
            $lateFee,
            $organisationType,
            $foundingTear,
            $numberOfAlumni,
            $numberOfUndergrads,
            $numberOfAlumniChapters,
            $totalUgChapters,
            $grantOnLicense
        ));
    }

}

