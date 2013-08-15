<?php

class Vendor_Form_ProductInfo extends Zend_Form {

    private $categories;
    private $products;
    private $audiences;
    private $default_audience;

    public function __construct($options = null, $default_audience = null) {

        $this->categories = $options['categories'];
        $this->products = $options['products'];
        $this->audiences = $options['audiences'];
        if ($default_audience != null) {
            $this->default_audience = $default_audience;
        }

        parent::__construct($options);
    }

    public function init() {

        $product_category = new Zend_Form_Element_Select('product_category');
        $product_category->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Select product category'))))
                ->setRequired(false)
                ->addMultiOptions($this->categories)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $product = new Zend_Form_Element_Select('product');
        $product->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Select product'))))
                ->setRequired(false)
                ->addMultiOptions($this->products)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $products = new Zend_Form_Element_Hidden('products');
        $products->setAttrib('size', '40')
                ->setAttrib('class', 'text')
                ->setAttrib('id', 'products')
                ->setAttrib('value', '')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please add product from product list'))))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $supplier_name = new Zend_Form_Element_Text('supplier_name');
        $supplier_name->setLabel('Name of Supplier(s) :')
                ->setAttrib('size', '40')
                ->setAttrib('class', 'proposed_use_text')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please enter supplier name'))))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');


        /* $audience = new Zend_Form_Element_MultiCheckbox('audience');
          $audience->setDisableLoadDefaultDecorators(true)
          ->setDecorators(array('ViewHelper'))
          ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please select target audience'))))
          ->setRequired(true)
          ->setRegisterInArrayValidator(false)
          ->addFilter('StripTags')
          ->addFilter('StringTrim')
          ->addMultiOptions($this->audiences); */

        $audience = new Zend_Form_Element_MultiCheckbox('audience');
        $audience->setDisableLoadDefaultDecorators(true)
                ->removeDecorator('label')
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Please select target audience'))))
                ->setRequired(true)
                //->setSeparator(PHP_EOL)
                //->setRequired(false)
                ->setAttribs(array('class' => 'default'))
                ->addMultiOptions($this->audiences);

        if ($this->default_audience != null) {
            $audience->setValue($this->default_audience);
        }

        $other_desc = new Zend_Form_Element_Text('other_desc');
        $other_desc->setLabel('')
                ->setAttrib('size', '40')
                ->setAttrib('class', 'proposed_use_text')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $client_id = new Zend_Form_Element_Checkbox('client_id');
        $client_id->setAttrib('checked', 'true')
                ->setAttrib('value', '1')
                ->setDisableLoadDefaultDecorators(true)
                ->setDecorators(array('ViewHelper', 'Errors'));

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submit')
                ->setAttrib('class', 'large awesome')
                ->setAttrib('value', 'Submit');

        $this->addElements(
                array(
                    $product_category,
                    $product,
                    $products,
                    $supplier_name,
                    $audience,
                    $other_desc,
                    $client_id,
                    $submit));
    }

}