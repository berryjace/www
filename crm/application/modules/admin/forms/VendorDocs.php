<?php

/*
 * author: Tanzim
 */

class Admin_Form_VendorDocs extends Zend_Form {

    public function init() {
        $this->setName('docs_form');
        $this->setAttrib('enctype', 'multipart/form-data');

        $FileName = new Zend_Form_Element_Text('file_name');
        $FileName->setLabel('File Name')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '100', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $DateFrom = new Zend_Form_Element_Text('date_from');
        $DateFrom->setLabel('From')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text date'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $DateTo = new Zend_Form_Element_Text('date_to');
        $DateTo->setLabel('To')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text date'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');
                
        $upload_document = new Zend_Form_Element_File('upload_doc');
        $upload_document->setLabel('Upload New Document')
                ->setAttrib('id', 'guide_document')
                ->addValidators(array(
                    array('Count', false, 1),
                    array('Size', false, 5242880),
                    array('Extension', false, array('doc', 'docx', 'xls', 'xlsx', 'pdf', 'txt', 'jpg', 'jpeg', 'gif', 'png', 'case' => true))
                ))        
               ->setRequired(false);      
        
        $docs_id = new Zend_Form_Element_Hidden('docs_id');
        $docs_id->setDecorators(array('ViewHelper'));
        $button = new Zend_Form_Element_Button('Save');        
        $button->removeDecorator('DtDdWrapper')->setAttribs(array('type'=>'submit','class'=>'button button_brown'));

        $this->addElements(array(
            $FileName,
            $docs_id,
            $upload_document,
            $button
        ));
    }

}

