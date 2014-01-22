<?php

//use Doctrine\ORM\Mapping as ORM;      

namespace BL\Entity;

/**
 * Payment
 *
 * @Table(name="payments")
 * @Entity(repositoryClass="BL\Entity\Repository\PaymentRepository")
 * @author Tanzim
 */
class Payment {

    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", length=8)
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(name="amount_paid", type="float", nullable=true)
     */
    private $amount_paid;

    /**
     * @Column(name="amount_remaining", type="float", nullable=true)
     */
    private $amount_remaining;

    /**
     * @Column(name="record_date", type="datetime", nullable=true)
     */
    private $record_date;

    /**
     * @Column(name="last_modified_date", type="datetime", nullable=true)
     */
    private $last_modified_date;

    /**
     * @Column(name="payment_year", type="string", length=7, nullable=true)
     */
    private $payment_year;

    /**
     * @Column(name="payment_quarter", type="integer", nullable=true)
     */
    private $payment_quarter;

    /**
     * @Column(name="payment_month", type="integer", nullable=true)
     */
    private $payment_month;

    /**
     * @Column(name="check_num", type="string", length=30, nullable=true)
     */
    private $check_num;

    /**
     * @Column(name="kp_payment", type="string", length=15, nullable=true)
     */
    private $kp_payment;    

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="vendor_id", referencedColumnName="id", nullable=true)
     */
    private $vendor;
    
    /**
     * @OneToOne(targetEntity="Invoice")
     * @JoinColumn(name="invoice_id", referencedColumnName="id", nullable=true) 
     */     
    private $invoice;

    public function __construct() {
        $this->last_modified_date = new \DateTime(date("Y-m-d H:i:s"));
        $this->record_date = new \DateTime(date("Y-m-d H:i:s"));
    }

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}