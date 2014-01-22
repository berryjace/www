<?php

/**
 * Description of FinancialInfo
 *
 * @author Masud
 */
class Vendor_Form_FinancialInfo extends Zend_Form {

    public function init() {

        $application_process = new Zend_Form_Element_MultiCheckbox('application_process', array(
                    'multiOptions' => array(
                        '1' => ' Letter from financial institution confirming that company has an account in good standing',
                        '2' => ' Closed financial statements for the previous calendar year',
                        '3' => ' For start-ups: access to chart of capital assets or business plan'
                        )));
        $application_process->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please select application process'))))
                //->setRegisterInArrayValidator(false)
                ->setValue('0')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $full_time_employee_num = new Zend_Form_Element_Text('full_time_employee_num');
        $full_time_employee_num->setLabel('How many full time employees do you have?')
                ->setAttrib('size', '30')
                ->setAttrib('class', 'text text_dimmed')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter number of employee'))))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $years_in_business = new Zend_Form_Element_Text('years_in_business');
        $years_in_business->setLabel('')
                ->setAttrib('size', '30')
                ->setAttrib('class', 'text text_dimmed')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter business year'))))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $business_failure_in_5_years = new Zend_Form_Element_Radio('business_failure_in_5_years');
        $business_failure_in_5_years->setLabel('')
                ->setAttrib('class', 'radio')
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Required'))))
                ->setRequired(true)
                ->setMultiOptions(array('yes' => ' Yes', 'no' => ' No'));

        $any_person_bankrupt = new Zend_Form_Element_Radio('any_person_bankrupt');
        $any_person_bankrupt->setLabel('')
                ->setAttrib('class', 'radio')
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Required'))))
                ->setRequired(true)
                ->setMultiOptions(array('yes' => ' Yes', 'no' => ' No'));

        $government_investigation = new Zend_Form_Element_Radio('government_investigation');
        $government_investigation->setLabel('')
                ->setAttrib('class', 'radio')
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Required'))))
                ->setRequired(true)
                ->setMultiOptions(array('yes' => ' Yes', 'no' => ' No'));

        $contract_terminated_in_last_2_years = new Zend_Form_Element_Radio('contract_terminated_in_last_2_years');
        $contract_terminated_in_last_2_years->setLabel('')
                ->setAttrib('class', 'radio')
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Required'))))
                ->setRequired(true)
                ->setMultiOptions(array('yes' => ' Yes', 'no' => ' No'));

        $litigation_against_the_officers = new Zend_Form_Element_Radio('litigation_against_the_officers');
        $litigation_against_the_officers->setLabel('')
                ->setAttrib('class', 'radio')
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Requird'))))
                ->setRequired(true)
                ->setMultiOptions(array('yes' => ' Yes', 'no' => ' No'));

        $any_collections_by_debt_collection_agency = new Zend_Form_Element_Radio('any_collections_by_debt_collection_agency');
        $any_collections_by_debt_collection_agency->setLabel('')
                ->setAttrib('class', 'radio')
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Required'))))
                ->setRequired(true)
                ->setMultiOptions(array('yes' => ' Yes', 'no' => ' No'));

        $additional_explanation = new Zend_Form_Element_Textarea('additional_explanation');
        $additional_explanation->setLabel('')
                ->setAttrib('rows', '3')
                ->setAttrib('cols', '80')
                ->setAttrib('class', 'text_dimmed')
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter account type'))))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $agreement_consent = new Zend_Form_Element_Checkbox('agreement_consent');
        $agreement_consent->setOptions(array('sing_day' => 'Single Day', 'mult_day' => 'Multiple Day'))
                ->setAttrib('class', 'checkbox')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Required'))))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submit')
                ->setAttrib('class', 'large awesome')
                ->setAttrib('value', 'Submit');

        $this->addElements(
                        array(
                            $application_process,
                            $full_time_employee_num,
                            $years_in_business,
                            $business_failure_in_5_years,
                            $any_person_bankrupt,
                            $government_investigation,
                            $contract_terminated_in_last_2_years,
                            $litigation_against_the_officers,
                            $any_collections_by_debt_collection_agency,
                            $additional_explanation,
                            $agreement_consent,
                            $submit)
                )
                ->setDecorators(array(
                    'ViewHelper',
                    array('FormErrors', array('placement' => 'PREPEND')),
                    'Form'));
    }

}