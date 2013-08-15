<?php

class Admin_Form_WhiteLabel extends Zend_Form {

    public function init() {
        $this->setName('whiteLabel_form');

        $headerName = new Zend_Form_Element_Text('header_name');
        $headerName->setLabel('Header Name')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->setValidators(array(array('NotEmpty', false, array('messages' => 'Name required'))))
                ->setAttribs(array('size' => '80', 'class' => 'text'))
                ->addFilter('StripTags')->addFilter('StringTrim');
        
        
        $url = new Zend_Form_Element_Text('url');
        $url->setLabel('URL')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->setAttribs(array('size' => '80', 'class' => 'text'))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $headerImage = new Zend_Form_Element_File('header_image');
        $headerImage->setLabel('Header Image')
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


        $bgColor = new Zend_Form_Element_Text('bg_color');
        $bgColor->setLabel('Choose Page Background Color')
                ->setDisableLoadDefaultDecorators(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please put the banner link'))))
                ->setAttribs(array('size' => '50', 'class' => 'text color-picker'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $fontColor = new Zend_Form_Element_Text('font_color');
        $fontColor->setLabel('Choose Font Color')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text color-picker'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $buttonColor = new Zend_Form_Element_Text('button_color');
        $buttonColor->setLabel('Choose Button Color')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text color-picker'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $footerColor = new Zend_Form_Element_Text('footer_color');
        $footerColor->setLabel('Choose Footer Color')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text color-picker'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $header_link = new Zend_Form_Element_Text('header_link');
        $header_link->setLabel('Header Link')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(false)
                ->setAttribs(array('size' => '80', 'class' => 'text'))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $this->addElements(array(
            $headerName,
            $url,
            $headerImage,
            $bgColor,
            $fontColor,
            $buttonColor,
            $footerColor,
            $header_link,
        ));
    }

}