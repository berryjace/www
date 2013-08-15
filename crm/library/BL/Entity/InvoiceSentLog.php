<?php

namespace BL\Entity;

/**
 * 
 * @Table(name="invoice_sent_log")
 * @Entity(repositoryClass="BL\Entity\Repository\InvoiceSentLogRepository")
 * @author Mahbub
 */
class InvoiceSentLog {

    /**
     * @Column(name="id", type="integer",nullable=false)
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="datetime",nullable=false)
     */
    private $date_sent;

    /**
     * @Column(name="quarter" , type="integer" , length=11 , nullable=true)
     */
    private $quarter;

    /**
     * @Column(name="invoice_type" , type="string" , length=30 , nullable=true)
     */
    private $invoice_type;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="vendor_id", referencedColumnName="id", nullable=true)
     */
    private $vendor_id;

    public function __construct() {
        $this->date_sent = new \DateTime(date("Y-m-d H:i:s"));
    }

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}