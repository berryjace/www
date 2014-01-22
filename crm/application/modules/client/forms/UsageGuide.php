<?php

/**
 * Description of AddUsageGuide
 *
 * @author Masud
 */
class Client_Form_UsageGuide extends Zend_Form {

    private $action;

    public function __construct($options = null) {
        $this->action = $options;
        parent::__construct($options);
    }

    public function init() {

        $this->setName('usageguide_form');
        $this->setAttrib('enctype', 'multipart/form-data');

        $guide_name = new Zend_Form_Element_Text('guide_name');
        $guide_name->setLabel('Guide Name')
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $guide_document = new Zend_Form_Element_File('guide_document');
        $guide_document->setLabel('Upload document')
                ->setAttrib('id', 'guide_document')
                ->addValidators(array(
                    array('Count', false, 1),
                    array('Size', false, 5242880),
                    array('Extension', false, array('doc', 'docx', 'xls', 'xlsx', 'pdf', 'txt', 'jpg', 'jpeg', 'gif', 'png', 'case' => true))
                ))
               ->setRequired(false);

        $guide_content = new Zend_Form_Element_Textarea('guide_content');
        $guide_content->setLabel('Guide Description')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->setAttrib('id', 'guide_content')
                ->setAttrib('rows', '7')
                ->setAttrib('class', 'text_area')
                ->setAttrib('cols', '100')
                ->setAttrib('size', '50')
                ->addFilter('StringTrim');

        $this->addElements(array(
                    $guide_name,
                    $guide_document,
                    $guide_content))
                ->setDecorators(array(
                    'ViewHelper',
                    array('FormErrors', array('placement' => 'PREPEND')), 'Form'));
    }

}

?>
