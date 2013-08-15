<?php

/**
 * Description of WebProfile
 *
 * @author Masud
 */
class Client_Form_WebProfile extends Zend_Form {

    private $org_type;
    private $selected_org_type;

    public function __construct($options = null) {
        if (!is_null($options['org_type'])) {
            $this->org_type = $options['org_type'];
        }
        $this->selected_org_type = !is_null($options['greek_org_type']) ? $options['greek_org_type'] : '0';
        parent::__construct($options);
    }

    public function init() {
        $this->setName('webprofile_form');
        $this->setAttrib('enctype', 'multipart/form-data');

        $organization_name = new Zend_Form_Element_Text('organization_name');
        $organization_name->addDecorator('Label', array('escape' => false))
                ->setLabel('Organization Name')
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter Organization Name'))))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $greek_name = new Zend_Form_Element_Text('greek_name');
        $greek_name->setLabel('Greek Name')
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        /* $organization_logo = new Zend_Form_Element_File('organization_logo');
          $organization_logo->setLabel('Upload Organization Logo')
          ->setAttrib('id', 'organization_logo')
          ->setDisableLoadDefaultDecorators(true)
          ->addValidator('Size', false, array('max' => '5242880'))
          ->setRequired(false)
          ->addFilter('StripTags')
          ->addFilter('StringTrim'); */

        /* $organization_logo = new Zend_Form_Element_File('organization_logo');
          $organization_logo->setLabel('Update Organization Logo')
          ->setRequired(false)
          ->setAttrib('id', 'organization_logo')
          ->addValidators(array(
          array('Count', false, 1),
          array('Size', false, 2097152),
          array('Extension', false, 'gif,jpg,png'),
          array('ImageSize', false, array(
          'minwidth' => 100,
          'minheight' => 100,
          'maxwidth' => 750,
          'maxheight' => 750))
          )); */

        /* $organization_symbol = new Zend_Form_Element_File('organization_symbol');
          $organization_symbol->setLabel('Upload Organization Symbol')
          ->setAttrib('id', 'organization_symbol')
          ->setDisableLoadDefaultDecorators(true)
          ->addValidator('Size', false, array('max' => '5242880'))
          ->setRequired(false)
          ->addFilter('StripTags')
          ->addFilter('StringTrim'); */

        /* $organization_symbol = new Zend_Form_Element_File('organization_symbol');
          $organization_symbol->setLabel('Update Organization Symbol')
          ->setRequired(false)
          ->setAttrib('id', 'organization_symbol')
          ->addValidators(array(
          array('Count', false, 1),
          array('Size', false, 2097152),
          array('Extension', false, 'gif,jpg,png'),
          array('ImageSize', false, array(
          'minwidth' => 100,
          'minheight' => 100,
          'maxwidth' => 750,
          'maxheight' => 750))
          )); */

        $address_line1 = new Zend_Form_Element_Text('address_line1');
        $address_line1->setLabel('Address 1')
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $address_line2 = new Zend_Form_Element_Text('address_line2');
        $address_line2->setLabel('Address 2')
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $phone1 = new Zend_Form_Element_Text('phone1');
        $phone1->setLabel('Phone1 / Ext')
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Phone format must be (xxx) xxx-xxxx'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $phone2 = new Zend_Form_Element_Text('phone2');
        $phone2->setLabel('Phone2 / Ext')
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Phone format must be (xxx) xxx-xxxx'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $city = new Zend_Form_Element_Text('city');
        $city->setLabel('City')
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $state = new Zend_Form_Element_Text('state');
        $state->setLabel('State')
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $zip = new Zend_Form_Element_Text('zip');
        $zip->setLabel('Zip')
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $fax = new Zend_Form_Element_Text('fax');
        $fax->setLabel('Fax')
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Fax format must be (xxx) xxx-xxxx'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email')
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $webpage = new Zend_Form_Element_Text('webpage');
        $webpage->setLabel('webpage')
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $greek_org_type = new Zend_Form_Element_Select('greek_org_type');
        $greek_org_type->addDecorator('Label', array('escape' => false))
                ->setLabel('Organization Type')
                ->setAttribs(array('class' => 'text'))
                ->setDisableLoadDefaultDecorators(true)
                ->addMultiOptions($this->org_type)
                ->setValue($this->selected_org_type)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please Select Organization Type'))))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $founding_address_line1 = new Zend_Form_Element_Text('founding_address_line1');
        $founding_address_line1->setLabel('Address 1')
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $founding_address_line2 = new Zend_Form_Element_Text('founding_address_line2');
        $founding_address_line2->setLabel('Address 2')
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $founding_city = new Zend_Form_Element_Text('founding_city');
        $founding_city->setLabel('City')
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $founding_state = new Zend_Form_Element_Text('founding_state');
        $founding_state->setLabel('State')
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $greek_founding_year = new Zend_Form_Element_Text('greek_founding_year');
        $greek_founding_year->setLabel('Founded In')
                ->setAttribs(array('size' => '50', 'class' => 'text date'))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $greek_number_of_colg_chapters = new Zend_Form_Element_Text('greek_number_of_colg_chapters');
        $greek_number_of_colg_chapters->setLabel('Number of Collegiate Chapters')
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $greek_number_of_alumni_chapters = new Zend_Form_Element_Text('greek_number_of_alumni_chapters');
        $greek_number_of_alumni_chapters->setLabel('Number of Alumni/ae Chapters')
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $greek_number_of_undergrads = new Zend_Form_Element_Text('greek_number_of_undergrads');
        $greek_number_of_undergrads->setLabel('Total Number of Undergraduates')
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $greek_number_of_alumni = new Zend_Form_Element_Text('greek_number_of_alumni');
        $greek_number_of_alumni->setLabel('Total Number of Alumni/ae')
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $headquarters_city = new Zend_Form_Element_Text('headquarters_city');
        $headquarters_city->setLabel('Headquarters City')
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $headquarters_state = new Zend_Form_Element_Text('headquarters_state');
        $headquarters_state->setLabel('Headquarters State')
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submit')
                ->setAttrib('class', 'large awesome');

        $this->addElements(
                array(
                    $organization_name,
                    $greek_name,
                    //$organization_logo,
                    //$organization_symbol,
                    $address_line1,
                    $address_line2,
                    $phone1,
                    $phone2,
                    $city,
                    $state,
                    $zip,
                    $fax,
                    $email,
                    $webpage,
                    $greek_org_type,
                    $founding_address_line1,
                    $founding_address_line2,
                    $founding_city,
                    $founding_state,
                    $greek_founding_year,
                    $greek_number_of_colg_chapters,
                    $greek_number_of_alumni_chapters,
                    $greek_number_of_alumni,
                    $greek_number_of_undergrads,
                    $headquarters_city,
                    $headquarters_state
                )
        );
    }

}
