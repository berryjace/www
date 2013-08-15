<?php

/**
 * Description of Lisc Clients
 *
 * @author Tanzim
 */
class Admin_Form_LicClients extends Zend_Form {

    public function __construct($options = null) {
        parent::__construct($options);
    }

    public function init() {
        $this->setName('lic_clients_edit_form');
        $this->setAttrib('enctype', 'multipart/form-data');

        $lic_agree_num = new Zend_Form_Element_Text('lic_agree_num');
        $lic_agree_num->setLabel('Lic #')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('class' => 'text', 'id' => 'lic_agree_num', 'size' => '50'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $renewal_fee = new Zend_Form_Element_Text('renewal_fee');
        $renewal_fee->setLabel('Renewal Fee')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('class' => 'text', 'size' => '50'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $sharing = new Zend_Form_Element_Select('sharing');
        $sharing->setLabel('sharing')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->addMultiOptions(array("" => "-- Select --", "Yes" => "Yes", "No" => "No"))
                ->setAttribs(array('class' => 'select'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $statuses_options = \BL_AMC::parseYAML(APPLICATION_PATH . '/configs/statuses.yml');
        $lic_status = new Zend_Form_Element_Select('lic_status');
        $lic_status->setLabel('Licensing Status')
                ->setDisableLoadDefaultDecorators(true)
                ->addErrorMessage('Select Licensing Status')
                ->setAttribs(array('class' => 'select'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addMultiOptions($statuses_options);

        $this->addElements(array(
            $lic_agree_num,
            $renewal_fee,
            $lic_status,
            $sharing
        ));
    }

}
