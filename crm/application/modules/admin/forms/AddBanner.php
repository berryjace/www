<?php

class Admin_Form_AddBanner extends Zend_Form {

    public function init() {
        $this->setName('addBanner_form');

        $bannerName = new Zend_Form_Element_Text('name');
        $bannerName->setLabel('Banner Name')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Name required'))))
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $bannerImage = new Zend_Form_Element_File('image');
        $bannerImage->setLabel('Banner Image')
                ->setDisableLoadDefaultDecorators(true)
                ->addValidators(array(
                    array('Count', false, 1),
                    array('Size', false, 2097152),
                    array('Extension', false, 'gif,jpg,png'),
                    array('ImageSize', false, array('minwidth' => 100,
                            'minheight' => 100,
                            'maxwidth' => 3500,
                            'maxheight' => 3500))
                ))
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->addFilter('StripTags')->addFilter('StringTrim');


        $bannerLink = new Zend_Form_Element_Text('link');
        $bannerLink->setLabel('Banner Link')
                ->setDisableLoadDefaultDecorators(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please put the banner link'))))
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(true)
                ->addFilter('StripTags')->addFilter('StringTrim');


        $bannerLocation = new Zend_Form_Element_Select('location');
        $bannerLocation->setLabel('Banner Location')
                ->setDisableLoadDefaultDecorators(true)
                ->addMultiOptions(array("Top Nav" => "Top Nav"))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $bannerStatus = new Zend_Form_Element_Select('status', array(
                    'multiOptions' => array(
                        '1' => ' Active',
                        '0' => ' Inactive'
                    )
                ));
        $bannerStatus->setLabel("Banner Status:");
        
        $startDate = new Zend_Form_Element_Text('startDate');
        $startDate->setLabel('Start Date')->setDisableLoadDefaultDecorators(true)
       				->setValidators(array(array('NotEmpty', true, array('messages'=>'Please put in a start date'))))
       				->setAttribs(array('class'=>'text startDate', 'readonly'=>'readonly'))
       				->addFilter('StripTags')->addFilter('StringTrim');

        $endDate = new Zend_Form_Element_Text('endDate');
        $endDate->setLabel('End Date')->setDisableLoadDefaultDecorators(true)
        ->setValidators(array(array('NotEmpty', true, array('messages'=>'Please put in an end date'))))
        ->setAttribs(array('class'=>'text startDate', 'readonly'=>'readonly'))
        ->addFilter('StripTags')->addFilter('StringTrim');

        $this->addElements(array(
            $bannerName,
            $bannerImage,
            $bannerLink,
            $bannerLocation,
            $bannerStatus,
        	$startDate,
        	$endDate
        ));
    }

}