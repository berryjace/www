<?php

class Admin_Form_VendorOperations extends Zend_Form {

    public function init() {
        $this->setName('operations_form');
        $this->setAttrib('enctype', 'multipart/form-data');
//        $vendorName = new Zend_Form_Element_Text('vendor_name');
//        $vendorName->setLabel('Vendor Name')
//                ->setDisableLoadDefaultDecorators(true)
//                ->setAttribs(array('size' => '50', 'class' => 'text', 'id' => 'vendor_name'))
//                ->setRequired(false)
//                ->addFilter('StripTags')->addFilter('StringTrim');

        $vendorType = new Zend_Form_Element_MultiCheckbox('vendor_type');
        $vendorType->setLabel('Vendor Type')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('class' => 'vendor_types'))
                ->addMultiOption('Retail', 'Retail')
                ->addMultiOption('Wholesale', 'Wholesale')
                ->addMultiOption('Travel', 'Travel');

        $insuranceReceivedOn = new Zend_Form_Element_Text('insurance_received');
        $insuranceReceivedOn->setLabel('Insurance Received On')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text date'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $insuranceExpiresOn = new Zend_Form_Element_Text('insurance_expire');
        $insuranceExpiresOn->setLabel('Insurance Expires On')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text date'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $vendorInsuranceCompany = new Zend_Form_Element_Text('insurance_company');
        $vendorInsuranceCompany->setLabel('Vendor Insurance Company')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $vendorInsuranceContact = new Zend_Form_Element_Text('insurance_contact');
        $vendorInsuranceContact->setLabel('Vendor Insurance Contact')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $vendorInsurancePhone = new Zend_Form_Element_Text('insurance_phone');
        $vendorInsurancePhone->setLabel('Vendor Insurance Phone')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $lateFees = new Zend_Form_Element_Radio('have_late_fee', array(
                    'multiOptions' => array(
                        '1' => 'Yes',
                        '0' => 'No'
                    )
                ));
        $lateFees->setLabel('Late Fees Applicable')
                ->setAttribs(array('class' => 'vendor_types'))
                ->setDisableLoadDefaultDecorators(true);

        $royaltyStructure = new Zend_Form_Element_Text('vendor_royalty_structure');
        $royaltyStructure->setLabel('Royalty Structure')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $productsOffered = new Zend_Form_Element_Textarea('vendor_products');
        $productsOffered->setLabel('Products Offered')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('rows' => '1', 'class' => 'textarea'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $defaultRecommendationForClients = new Zend_Form_Element_Textarea('vendor_recommendation_to_client');
        $defaultRecommendationForClients->setLabel('Default Recommendation for Clients')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('rows' => '1', 'class' => 'textarea'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $defaultNoteToVendor = new Zend_Form_Element_Textarea('default_note_to_vendor');
        $defaultNoteToVendor->setLabel('Default Note to Vendor')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('rows' => '1', 'class' => 'textarea'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');
        $vendorReportingType = new Zend_Form_Element_Select('vendor_reporting_type');
        $vendorReportingType->setLabel('Vendor Reporting Type')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('class' => 'select'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim')
        ;
        $vendorReportingType->setMultiOptions(array(
            '' => '-- Select Reporting Type --',
            '1' => 'Type 1: Vendors that pay a royalty fee based on a percentage commission',
            '2' => 'Type 2: Vendors that pay a royalty fee based on unit sales',
            '3' => 'Type 3: Vendors that have a unique royalty structure'));

        $status = \BL_AMC::parseYAML(APPLICATION_PATH . '/configs/user_status.yml');
//        $this->view->BUtils()->doctrine_dump($this->view->lic);
        $status_options = array();
        foreach ($status as $key => $value) {
            $status_options[$value] = $value;
        }

        $vendor_status = new Zend_Form_Element_Select('vendor_status');
        $vendor_status->setLabel('Vendor Status')
                ->setDisableLoadDefaultDecorators(true)
                ->addErrorMessage('Select Vendor Status')
                ->setAttribs(array('class' => 'select', 'id' => 'operation_vendor_status'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addMultiOptions($status_options);

        $this->addElements(array(
//            $vendorName,
            $vendorType,
            $vendor_status,
            $insuranceReceivedOn,
            $insuranceExpiresOn,
            $vendorInsuranceCompany,
            $vendorInsuranceContact,
            $vendorInsurancePhone,
            $lateFees,
            $royaltyStructure,
            $productsOffered,
            $defaultRecommendationForClients,
            $defaultNoteToVendor,
            $vendorReportingType,
        ));
    }

}

