<?php

class Admin_Form_PayInvoice extends Zend_Form {

    public function init() {

        $this->setName('pay_invoice_form');

        $vendorName = new Zend_Form_Element_Text('vendor_name');
        $vendorName->setLabel('Vendor Name')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->setValidators(array(array('NotEmpty', true, array('messages' => 'Name required'))))
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $invNum = new Zend_Form_Element_Text('inv_num');
        $invNum->setLabel('Invoice Number')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->addFilter('StripTags')->addFilter('StringTrim');

        $inv_types = \BL_AMC::parseYAML(APPLICATION_PATH . '/configs/invoice_types.yml');
        $invType = new Zend_Form_Element_Select('inv_type');
        $invType->setLabel('Invoice Type')
                ->setDisableLoadDefaultDecorators(true)
                ->addMultiOptions($inv_types);

        $invDate = new Zend_Form_Element_Text('inv_date');
        $invDate->setLabel('Invoice Date')
                ->setDisableLoadDefaultDecorators(true)
                ->setRequired(true)
                ->setAttribs(array('size' => '50', 'class' => 'text'))
                ->addFilter('StripTags')->addFilter('StringTrim');


        $year = Date("Y");
         
        $yearRange = range($year + 1, $year - 5);
        $years = array();
        
        foreach($yearRange as $yr){
        	$years[$yr . "-" . substr($yr+1, 2)] = $yr . "-" . substr($yr+1, 2);
        }
		$fiscalYear = new Zend_Form_Element_Select('fiscal_year');
		$fiscalYear->setLabel("'Paid Out In' Fiscal Year")
				->setDisableLoadDefaultDecorators(true)
				->addMultiOptions($years);
		
		$quarter = new Zend_Form_Element_Select('quarter');
		$quarter->setLabel("'Paid Out In' Quarter")
				->setDisableLoadDefaultDecorators(true)
				->addMultiOptions(array("1"=>"First", "2"=>"Second", "3"=>"Third", "4"=>"Fourth"));
		
		$refNumber = new Zend_Form_Element_Text('ref_number');
		$refNumber->setLabel("Check/ACH #")
				->setDisableLoadDefaultDecorators(true)
				->setRequired(true)
				->setAttribs(array('size'=>'50', 'class'=>'text'))
				->addFilter('StripTags')->addFilter('StringTrim');

		$total = new Zend_Form_Element_Text('total');
		$total->setLabel("Payment Total")
		->setDisableLoadDefaultDecorators(true)
		->setRequired(true)
		->setAttribs(array('size'=>'50', 'class'=>'text'))
		->addFilter('StripTags')->addFilter('StringTrim');
        
		//*
       	$this->addElements(array(
            $vendorName,
        	$invNum,
        	$invType,
        	$invDate,
        	$fiscalYear,
        	$quarter,
        	$refNumber,
        	$total
        ));//*/
		
		//$this->addElement($vendorName);
		
		error_log("\nmethod exisss: " . method_exists($this, "addElements"),3, "./errorLog.log");
       	
        error_log("\nthis: " . get_class($this).  "\nvendorName: " . get_class($vendorName) . "\ninvNum: ". get_class($invNum) . "\ninvType: " . get_class($invType) . "\ninvDate: " . get_class($invDate) . "\nfiscalYear: " . get_class($fiscalYear) . "\nquarter: " . get_class($quarter) . "\nrefNumber: " . get_class($refNumber) . "\ntotal: " . get_class($total), 3, "./errorLog.log");
    }

}