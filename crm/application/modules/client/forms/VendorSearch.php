<?php

/**
 * Description of WebProfile
 *
 * @author Masud
 */
class Client_Form_VendorSearch extends Zend_Form {

    public function init() {
        $this->setName('vendorSearch_form');
        $service = $this->createElement('multiCheckbox', 'service', array(
                    'multiOptions' => array(
                        '1' => ' Sell Online',
                        '2' => ' Have a Storefront',
                        '3' => ' Sell Wholesale to Retailers'
                    )
                ))
                ->setLabel('')
		->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setRequired(false);
        $looking = $this->createElement('text', 'looking')
                ->setLabel('')
		->setValue('Product Name')
                ->setRequired(false)
		->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setAttribs(array('max-length' => '50'))
                ->setAttribs(array('style' => 'width: 124px'))
                ->setAttribs(array('onclick' => "javascript:$(this).val('')"))
                ->setAttribs(array('onblur' => "javascript:if($(this).val()=='')$(this).val('Product Name')"));
        $vendor = $this->createElement('text', 'vendor')
                ->setLabel('')
		->setValue('Company Name')
                ->setRequired(false)
		->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
		->setAttribs(array('onclick' => "javascript:$(this).val('')"))
		->setAttribs(array('style' => 'width: 124px'))
		->setAttribs(array('onblur' => "javascript:if($(this).val()=='')$(this).val('Company Name')"))
                ->setAttribs(array('max-length' => '50'));
        $zip = $this->createElement('text', 'zip')
                ->setLabel('')
		->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
		->setValue('Zip Code')
		->setAttribs(array('onclick' => "javascript:$(this).val('')"))
		->setAttribs(array('onblur' => "javascript:if($(this).val()=='')$(this).val('Zip')"))
                ->setRequired(false)
		->setAttribs(array('style' => 'width: 124px'))
                ->setAttribs(array('max-length' => '50'));
        $city = $this->createElement('text', 'city')
                ->setLabel('')
		->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'))
                ->setRequired(false)
		->setAttribs(array('style' => 'width: 124px'))
		->setValue('City or State')
		->setAttribs(array('onclick' => "javascript:$(this).val('')"))
		->setAttribs(array('onblur' => "javascript:if($(this).val()=='')$(this).val('City or State')"))
                ->setAttribs(array('max-length' => '50'));
        $submit = $this->createElement('submit', 'submit')
                ->setLabel('Submit')
		->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setAttribs(array('class' => 'btn btn-primary'));


        $this->addElements(array($service, $looking, $vendor, $zip, $city, $submit));
    }


}
