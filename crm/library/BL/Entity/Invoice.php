<?php

//use Doctrine\ORM\Mapping as ORM;      

namespace BL\Entity;

/**
 * Invoice
 *
 * @Table(name="invoice")
 * @Entity(repositoryClass="BL\Entity\Repository\InvoiceRepository")
 * @author Tanzim
 */
class Invoice {

    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", length=11)
     * @Id         
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(name="created_at" , type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @Column(name="updated_at" , type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @Column(name="invoice_date" , type="datetime", nullable=true)
     */
    private $invoice_date;
    
    /**
     * @Column(name="due_date" , type="datetime", nullable=true)
     */
    private $due_date;

    /**
     * @Column(name="invoice_number" , type="string" , length=30 , nullable=true)
     */
    private $invoice_number;

    /**
     * @Column(name="invoice_type" , type="string" , length=30 , nullable=true)
     */
    private $invoice_type;
    
    /**
     * @Column(name="fiscal_year" , type="string" , length=25 , nullable=true)
     */
    private $fiscal_year;

    /**
     * @Column(name="quarter" , type="integer" , length=11 , nullable=true)
     */
    private $quarter;
        
    /**
     * @Column(name="company_name" , type="string" , length=30 , nullable=true)
     */
    private $company_name;
    
    /**
     * @Column(name="webpage" , type="string" , length=100 , nullable=true)
     */
    private $webpage;    
    
    /**
     * @Column(name="invoice_term" , type="string" , length=25 , nullable=true)
     */
    private $invoice_term;

    /**
     * @Column(name="address_line1" , type="string" , length=100 , nullable=true)
     */
    private $address_line1;
   
    /**
     * @Column(name="address_line2" , type="string" , length=100 , nullable=true)
     */
    private $address_line2;
    
    /**
     * @Column(name="city" , type="string" , length=25 , nullable=true)
     */
    private $city;

    /**
     * @Column(name="state" , type="string" , length=25 , nullable=true)
     */
    private $state;

    /**
     * @Column(name="zip" , type="string" , length=25 , nullable=true)
     */
    private $zip;

    /**
     * @Column(name="phone1" , type="string" , length=25 , nullable=true)
     */
    private $phone1;

    /**
     * @Column(name="phone2" , type="string" , length=25 , nullable=true)
     */
    private $phone2;

    /**
     * @Column(name="email" , type="string" , length=150 , nullable=true)
     */
    private $email;

    /**
     * @Column(name="fax" , type="string" , length=25 , nullable=true)
     */
    private $fax;

     /**
     * @Column(name="invoice_status" , type="string" , length=25 , nullable=true)
     */
    private $invoice_status;
    
    /**
     * @Column(name="payment_status" , type="string" , length=25 , nullable=true)
     */
    private $payment_status;

    /**
     * @Column(name="display_record" , type="string" , length=30 , nullable=true)
     */
    private $display_record;

    /**
     * @Column(name="amount_due" , type="float" ,  nullable=true)
     */
    private $amount_due;

    /**
     * @Column(name="amount_paid" , type="float"  , nullable=true)
     */
    private $amount_paid;

    /**
     * @Column(name="email_date" , type="string" , length=255 , nullable=true)
     */
    private $email_date;
    
    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="vendor_id", referencedColumnName="id", nullable=true)
     */
    private $vendor_id;

    public function __construct() {
        $this->line_items = new \Doctrine\Common\Collections\ArrayCollection();
        $this->created_at = new \DateTime(date("Y-m-d H:i:s"));
        $this->updated_at = new \DateTime(date("Y-m-d H:i:s"));
    }

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}
