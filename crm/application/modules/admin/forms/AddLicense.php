<?php

class Admin_Form_AddLicense extends Zend_Form {


    public function init() {




        $this->setName('addLicense_form');


        $client = new Zend_Form_Element_Select('client_id');
        $client->addDecorator('Label', array('escape' => false))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
		->setAttrib('class', 'elWidth')
		->addMultiOption('', 'Select')
                //->addValidator(new Zend_Validate_Db_NoRecordExists(array('table' => 'users', 'field' => 'username')), true, array('messages' => 'Username already exists.'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $sharing = new Zend_Form_Element_Select('sharing');
        $sharing->addDecorator('Label', array('escape' => false))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
		->setAttrib('class', 'elWidth')
		->addMultiOption('', 'Select')
		->addMultiOption('yes', 'Yes')
		->addMultiOption('no', 'No')
                //->addValidator(new Zend_Validate_Db_NoRecordExists(array('table' => 'users', 'field' => 'username')), true, array('messages' => 'Username already exists.'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $royalty = new Zend_Form_Element_Text('royalty');
        $royalty->addDecorator('Label', array('escape' => false))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
		->setAttrib('class', 'text')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');





        $grant_of_license = new Zend_Form_Element_Textarea('grant_of_license');
        $grant_of_license->addDecorator('Label', array('escape' => false))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
		->setAttribs(array('cols'=>  135, 'rows'=>4, 'class'=>'textarea'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $royalty_description = new Zend_Form_Element_Textarea('royalty_description');
        $royalty_description->addDecorator('Label', array('escape'=>false))
        		->setDisableLoadDefaultDecorators(true)
        		->setRequired(true)
        		->setAttribs(array('cols'=>135, 'rows'=>4, 'class'=>'textarea'))
        		->addFilter('StripTags')
        		->addFilter('StringTrim');

	$agreement_date = new Zend_Form_Element_Text('agreement_date');
        $agreement_date->addDecorator('Label', array('escape' => false))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
		->setAttrib('class', 'text')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

	$advance = new Zend_Form_Element_Text('advance');
        $advance->addDecorator('Label', array('escape' => false))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
		->setAttrib('class', 'text')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
    $license_number = new Zend_Form_Element_Text('license_number');
    	$license_number->addDecorator('Label', array('escape'=>false))
    			->setDisableLoadDefaultDecorators(true)
    			->setRequired(true)
    			->setAttrib('class', 'text')
    			->addFilter('StripTags')
    			->addFilter('StringTrim');

	$status = new Zend_Form_Element_Select('status_id');
        $status->addDecorator('Label', array('escape' => false))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
		->setAttrib('class', 'elWidth')
		->addMultiOption('', 'Select')
                //->addValidator(new Zend_Validate_Db_NoRecordExists(array('table' => 'users', 'field' => 'username')), true, array('messages' => 'Username already exists.'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

	$vendor_type = new Zend_Form_Element_Select('vendor_type_id');
        $vendor_type->addDecorator('Label', array('escape' => false))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
		->setAttrib('class', 'elWidth')
		->addMultiOption('', 'Select')
		->addMultiOption('1', 'Vendor Type 1: %age on gross sales')
		->addMultiOption('2', 'Vendor Type 2: fix amount on per unit sale')
		->addMultiOption('3', 'Vendor Type 3: custom')
                //->addValidator(new Zend_Validate_Db_NoRecordExists(array('table' => 'users', 'field' => 'username')), true, array('messages' => 'Username already exists.'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

	$file = new Zend_Form_Element_File('license_file');
	$file->setDisableLoadDefaultDecorators(true);


        $this->addElements(array(
            $client,
	    $sharing,
	    $vendor_type,
	    $royalty,
	    $advance,
       	$license_number,
	    $grant_of_license,
        $royalty_description,
	    $agreement_date,
	    $status,
	    $file
        ));
    }

}

