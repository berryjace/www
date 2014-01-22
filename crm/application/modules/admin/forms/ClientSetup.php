<?php

class Admin_Form_ClientSetup extends Zend_Form {

    private $org_type;
    private $selected_org_type;

    public function __construct($options = null) {
        if (!is_null($options['org_type'])) {
            $this->org_type = $options['org_type'];
        }
        $this->selected_org_type = !is_null($options['greek_org_type']) ? $options['greek_org_type'] : '0';
        parent::__construct();
    }

    public function init() {
        $this->setName('setup_form');
        $this->setAttrib('enctype', 'multipart/form-data');

        /*$username = new Zend_Form_Element_Text('username');
        $username->addDecorator('Label', array('escape' => false))
                ->setLabel('Username')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->setValidators(array(
                    array('NotEmpty', true, array('messages' => 'Value is required')),
                    array(new Zend_Validate_Db_NoRecordExists(
                                array(
                                    'table' => 'users',
                                    'field' => 'username')
                        ), true, array('messages' => 'Username already exists'))
                ))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $password = new Zend_Form_Element_Password('password');
        $password->addDecorator('Label', array('escape' => false))
                ->setLabel('Password')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setRequired(false)
                ->addErrorMessage('Value is required')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');*/


        $clientName = new Zend_Form_Element_Text('client_name');
        $clientName->addDecorator('Label', array('escape' => false))
                ->setLabel('Client name <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter Client Name'))))
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(true)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $greekLetters = new Zend_Form_Element_Text('greek_letters');
        $greekLetters->addDecorator('Label', array('escape' => false))
                ->setLabel('Greek Letters <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter Greek Letters'))))
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(true)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $description = new Zend_Form_Element_Text('description');
        $description->addDecorator('Label', array('escape' => false))
                ->setLabel('Description <sup classs="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter Description'))))
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(true)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $address1 = new Zend_Form_Element_Text('address1');
        $address1->addDecorator('Label', array('escape' => false))
                ->setLabel('Address Line 1 <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter Address'))))
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(true)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $address2 = new Zend_Form_Element_Text('address2');
        $address2->setLabel('Address Line 2')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $city = new Zend_Form_Element_Text('city');
        $city->setLabel('City')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

//        $state = new Zend_Form_Element_Text('state');
//        $state->setLabel('State')
//                ->setDisableLoadDefaultDecorators(true)
//                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
//                ->addFilter('StripTags')->addFilter('StringTrim');

        $state_options = \BL_AMC::parseYAML(APPLICATION_PATH . '/configs/state_options.yml');
        $state = new Zend_Form_Element_Select('state');
        $state->setLabel('State / Province *')
                ->setDisableLoadDefaultDecorators(true)
                ->addErrorMessage('Select state')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addMultiOptions($state_options);

        $zip = new Zend_Form_Element_Text('zip_code');
        $zip->setLabel('Zip Code')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Product Design Approval & General Communication Email')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $agree_note_email = new Zend_Form_Element_Text('agreement_notification_email');
        $agree_note_email->setLabel('Agreement Notification Email')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $url = new Zend_Form_Element_Text('url');
        $url->setLabel('Web Address')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $approvedContact = new Zend_Form_Element_Text('approved_contact');
        $approvedContact->setLabel('Portal Contact Person')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $phone = new Zend_Form_Element_Text('phone');
        $phone->setLabel('Phone No.')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Phone must be in (xxx) xxx-xxxx format'))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $fax = new Zend_Form_Element_Text('fax');
        $fax->setLabel('Fax No.')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Fax must be in (xxx) xxx-xxxx format'))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $defRenewFee = new Zend_Form_Element_Text('def_ren_fee');
        $defRenewFee->addDecorator('Label', array('escape' => false))
                ->setLabel('Renewal Fee <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please Eneter Default Renewal Fee'))))
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(true)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $defLateFee = new Zend_Form_Element_Text('def_late_fee');
        $defLateFee->addDecorator('Label', array('escape' => false))
                ->setLabel('Default Late Fee <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please Eneter Default Late Fee'))))
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(true)
                ->addFilter('StripTags')->addFilter('StringTrim');

        /* $royalty = new Zend_Form_Element_Text('royalty');
          $royalty->setLabel('Royalty (Default Value)')
          ->setDisableLoadDefaultDecorators(true)
          ->setValidators(array(array('NotEmpty', false, array('messages' => 'Please enter Royalty Fee'))))
          ->setAttribs(array('size'=>'50','class'=>'text'))->setRequired(true)
          ->addFilter('StripTags')->addFilter('StringTrim'); */

        $orgType = new Zend_Form_Element_Select('org_type');
        $orgType->addDecorator('Label', array('escape' => false))
                ->setLabel('Organization Type <sup class="errors">*</sup>')
                ->setDisableLoadDefaultDecorators(true)
                ->addMultiOptions($this->org_type)
                ->setValue($this->selected_org_type)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please Select Organization Type'))))
                ->setAttribs(array('class' => 'text'))->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $numAlumni = new Zend_Form_Element_Text('num_alumni');
        $numAlumni->setLabel('Number of Alumni/ae')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $numAlumniChapters = new Zend_Form_Element_Text('num_alumni_chapters');
        $numAlumniChapters->setLabel('Number of Alumni/ae Chapters')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $numUndergrads = new Zend_Form_Element_Text('num_undergrads');
        $numUndergrads->setLabel('Number of UnderGrads')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $numUgChapters = new Zend_Form_Element_Text('num_ug_chapters');
        $numUgChapters->setLabel('Number of UnderGrad Chapters')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $foundingYear = new Zend_Form_Element_Text('founding_year');
        $foundingYear->setLabel('Founding Year')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $totalUgChapters = new Zend_Form_Element_Text('total_ug_chapters');
        $totalUgChapters->setLabel('Total UG Chapters')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $grantOfLicense = new Zend_Form_Element_Textarea('grant_of_lic');
        $grantOfLicense->setLabel('Grant of License')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('rows' => 8, 'cols' => 50, 'class' => 'text_area'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $Submit = new Zend_Form_Element_Submit('Submit');
        $Submit->setAttrib('id', 'submit')
                ->setAttrib('class', 'button button_black detail_button');
        
        $user_code = new Zend_Form_Element_Text('user_code');
        $user_code->setLabel('Client Number')
		        ->setDisableLoadDefaultDecorators(true)
		        ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
		        ->addFilter('StripTags')->addFilter('StringTrim');

        $this->addElements(array(
        	$user_code,
            $clientName,
            $description,
            $address1,
            $address2,
            $city,
            $state,
            $zip,
            $approvedContact,
            $phone,
            $email,
        	$agree_note_email,
            $url,
            $fax,
            $greekLetters,
            $defRenewFee,
            $defLateFee,
            $orgType,
            //$royalty,
            $foundingYear,
            $numAlumni,
            $numAlumniChapters,
            $numUndergrads,
            $numUgChapters,
            $totalUgChapters,
            $grantOfLicense,
            $Submit
        ));
    }

}
