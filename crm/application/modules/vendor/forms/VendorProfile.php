<?php

class Vendor_Form_VendorProfile extends Zend_Form {

    private $services;
    private $default_value;
    private $categories;
    private $products;

    public function __construct($options = null) {
        $this->services = !is_null($options['service']) ? $options['service'] : array();
        $this->categories = $options['categories'];
        $this->products = $options['products'];
        if (!is_null($options['selected_service'])) {
            $this->default_value = $options['selected_service'];
        }
        parent::__construct($options);
    }

    public function init() {
        /* Form Elements & Other Definitions Here ... */
        $this->setName('vendor_profile');
        $this->setAttrib('enctype', 'multipart/form-data');


        $product_category = new Zend_Form_Element_Select('product_category');
        $product_category->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('class' => 'bgWhite'))
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Select product category'))))
                ->setRequired(false)
                //->addMultiOptions($this->categories)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        if (is_array($this->categories)) {
            $product_category->addMultiOptions($this->categories);
        }

        $product = new Zend_Form_Element_Select('product');
        $product->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('class' => 'bgWhite'))
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Select product'))))
                ->setRequired(false)
                //->addMultiOptions($this->products)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        if (is_array($this->products)) {
            $product->addMultiOptions($this->products);
        }

        $products = new Zend_Form_Element_Hidden('products');
        $products->setAttrib('size', '40')
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'products')
                ->setAttrib('value', '')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please add product from product list'))))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $organization_name = new Zend_Form_Element_Text('organization_name');
        $organization_name->setLabel('Company Name')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $address1 = new Zend_Form_Element_Text('address1');
        $address1->setLabel('Address 1')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $address2 = new Zend_Form_Element_Text('address2');
        $address2->setLabel('Address 2')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $city = new Zend_Form_Element_Text('city');
        $city->setLabel('City')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $state = new Zend_Form_Element_Text('state');
        $state->setLabel('State')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^[A-Z]{2}$/i', 'messages' => 'Must be two letter format(example- XX)'))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $zip = new Zend_Form_Element_Text('zip');
        $zip->setLabel('Zip Code')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('E-mail')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addValidator('EmailAddress', true, array('messages' => 'Invalid email address'))
//                ->addValidator(new Zend_Validate_Db_NoRecordExists(array('table' => 'users', 'field' => 'email')), true, array('messages' => 'Email already exists'))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $web_page = new Zend_Form_Element_Text('web_page');
        $web_page->setLabel('Web Address')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addFilter('StripTags')->addFilter('StringTrim');

        $phone1 = new Zend_Form_Element_Text('phone1');
        $phone1->setLabel('Phone 1/Ext')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Phone must be (xxx) xxx-xxxx format'))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $phone2 = new Zend_Form_Element_Text('phone2');
        $phone2->setLabel('Phone 2/Ext')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'Phone must be (xxx) xxx-xxxx format'))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $fax = new Zend_Form_Element_Text('fax');
        $fax->setLabel('Fax No.')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))->setRequired(false)
                ->addValidator('Regex', true, array('pattern' => '/^\(\d{3}\)\040\d{3}-\d{4}/i', 'messages' => 'FAX must be (xxx) xxx-xxxx format'))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $logo_url = new Zend_Form_Element_File('logo_url');
        $logo_url->setLabel('Update Company Logo')
                //->addValidator('Size', false, array('max' => '5242880'))
                ->addValidators(array(
                    array('Count', false, 1),
                    array('Size', false, 2097152),
                    array('Extension', false, 'gif,jpg,png,bmp'),
                    array('ImageSize', false, array('minwidth' => 100,'minheight' => 100
                            //'maxwidth' => 250,'maxheight' => 250
                        ))
                ))
                ->setRequired(false);
	$use_default = new Zend_Form_Element_Checkbox('use_default');
        $use_default->setLabel('Use Default Company Logo (will over-ride current upload)');
         $use_default->setDecorators(array(
            'ViewHelper',
             array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $services = new Zend_Form_Element_MultiCheckbox('services');
        $services->setDisableLoadDefaultDecorators(true)
                ->removeDecorator('label')
                ->setSeparator(PHP_EOL)
                ->setRequired(false)
                ->setAttribs(array('class' => 'default'))
                ->addMultiOptions($this->services);

        if ($this->default_value != null) {
            $services->setValue($this->default_value);
        }

        $product_offered = new Zend_Form_Element_Textarea('product_offered');
        $product_offered->setLabel('Product Offered')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('rows' => '4', 'cols' => 126, 'class' => 'textarea'))->setRequired(false)
                ->addFilter('StringTrim');

        $company_discripction = new Zend_Form_Element_Textarea('company_discripction');
        $company_discripction->setLabel('Company Description')
                ->setDisableLoadDefaultDecorators(true)
                ->setAttribs(array('rows' => '4', 'cols' => 126, 'class' => 'textarea'))->setRequired(false)
                ->addFilter('StringTrim');

        $this->addElements(array(
            $product_category,
            $product,
            $products,
            $organization_name,
            $address1,
            $address2,
            $city,
            $state,
            $email,
            $web_page,
            $phone1,
            $phone2,
            $fax,
            $zip,
            $logo_url,
            $services,
            $product_offered,
            $company_discripction,
	    $use_default
        ));
    }

}
