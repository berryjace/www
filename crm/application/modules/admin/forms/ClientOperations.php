<?php

/**
 * Description of ClientOperations
 *
 * @author mahbub
 */
class Admin_Form_ClientOperations extends Zend_Form {

    public function __construct() {
        parent::__construct();
    }

    public function init() {
        $licenseAgreementSignee = new Zend_Form_Element_Text('license_agreement_signee');
        $licenseAgreementSignee->setLabel('License Agreement Signee')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $orgConventionDates = new Zend_Form_Element_Text('org_convention_dates');
        $orgConventionDates->setLabel('Org Convention Dates')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text date'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $orgPmsColors1 = new Zend_Form_Element_Text('org_pms_colors_1');
        $orgPmsColors1->setLabel('ORG PMS COLORS 1')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $acceptAdvertising = new Zend_Form_Element_Text('accept_advertising');
        $acceptAdvertising->setLabel('Accept Advertising')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $signeeTitle = new Zend_Form_Element_Text('signee_title');
        $signeeTitle->setLabel('Signee Title')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $orgMagazineCirculationSize = new Zend_Form_Element_Text('org_magazine_circulation_size');
        $orgMagazineCirculationSize->setLabel('ORG Magazine Circulation Size')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $orgPmsColors2 = new Zend_Form_Element_Text('org_pms_colors_2');
        $orgPmsColors2->setLabel('ORG PMS COLORS 2')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $orgPmsColors3 = new Zend_Form_Element_Text('org_pms_colors_3');
        $orgPmsColors3->setLabel('ORG PMS COLORS 3')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $orgPmsColors4 = new Zend_Form_Element_Text('org_pms_colors_4');
        $orgPmsColors4->setLabel('ORG PMS COLORS 4')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $defaultNoteToAllApplyingVendors = new Zend_Form_Element_Textarea('default_note_to_all_applying_vendors');
        $defaultNoteToAllApplyingVendors->setLabel('Default Note to all Applying Vendors')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('rows' => '4', 'class' => 'text_area'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $frequencyOfMag = new Zend_Form_Element_Text('frequency_of_mag');
        $frequencyOfMag->setLabel('Frequency of Mag')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $advertisingRates = new Zend_Form_Element_Text('advertising_rates');
        $advertisingRates->setLabel('Advertising Rates')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $advertisingDeadlines = new Zend_Form_Element_Text('advertising_deadlines');
        $advertisingDeadlines->setLabel('Advertising Deadlines')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text  date'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $workshopNames = new Zend_Form_Element_Text('workshop_names');
        $workshopNames->setLabel('Workshop Names')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $orgWorkshopType = new Zend_Form_Element_Text('org_workshop_type');
        $orgWorkshopType->setLabel('ORG Workshop Type')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $orgWorkshopDates = new Zend_Form_Element_Text('org_workshop_dates');
        $orgWorkshopDates->setLabel('ORG Workshop Dates')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text  date'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $conventionName = new Zend_Form_Element_Text('convention_name');
        $conventionName->setLabel('Convention Name')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $orgConventionSite = new Zend_Form_Element_Text('org_convention_site');
        $orgConventionSite->setLabel('Org Convention Site')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

       
        $commissionStartDate = new Zend_Form_Element_Text('commission_start_date');
        $commissionStartDate->setLabel('Commission Start Date')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text  date'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');
        $commissionPer = new Zend_Form_Element_Text('commission_per');
        $commissionPer->setLabel('Commission %')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addValidator('Float', true, array('messages' => 'Check commission must be Float'))
                ->addFilter('StripTags')->addFilter('StringTrim');
        $sharing = new Zend_Form_Element_Select('sharing');
        $sharing->setLabel('Sharing')
                ->setDisableLoadDefaultDecorators(true)                                
                ->setRequired(false)
                ->addMultiOptions(array(
                    "1" => "Yes",
                    "0" => "No",
                    ))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $client_status = new Zend_Form_Element_Select('client_status');
        $client_status->setLabel('Client Status')
                ->setDisableLoadDefaultDecorators(true)                                
                ->setRequired(false)
                ->addMultiOptions(array(
                    "Current" => "Current",
                    "Cancelled" => "Cancelled",
                    "Potential" => "Potential"
                ))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $this->addElements(array(
            $licenseAgreementSignee,
            $orgConventionDates,
            $orgPmsColors1,
            $acceptAdvertising,
            $signeeTitle,
            $orgMagazineCirculationSize,
            $orgPmsColors2,
            $orgPmsColors3,
            $orgPmsColors4,
            $defaultNoteToAllApplyingVendors,
            $frequencyOfMag,
            $advertisingRates,
            $advertisingDeadlines,
            $workshopNames,
            $orgWorkshopType,
            $orgWorkshopDates,
            $conventionName,
            $orgConventionSite,
            $commissionStartDate,
            $commissionPer,
            $sharing,
            $client_status));
    }

}
