<?php

namespace BL\Entity;

/**
 * Class CheckPayment to mapping with check_payments table
 * @Table(name="check_payments")
 * @Entity(repositoryClass="BL\Entity\Repository\CheckPaymentRepository")
 * @author Masud
 */
class CheckPayment {

    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", length=11)
     * @Id         
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(name="check_date", type="datetime", nullable=true)
     */
    private $check_date;

    /**
     * @Column(name="check_number", type="string", length=30, nullable=true)
     */
    private $check_number;

    /**
     * @Column(name="check_amount", type="float", length=30, nullable=true)
     */
    private $check_amount;

    /**
     * @Column(name="created_at", type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="vendor_id", referencedColumnName="id", nullable=false) 
     */
    private $vendor;

    /**
     * @ManyToOne(targetEntity="Invoice")
     * @JoinColumn(name="invoice_id", referencedColumnName="id", nullable=false) 
     */
    private $invoice;

    public function __construct() {
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
