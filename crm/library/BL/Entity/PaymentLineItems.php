<?php

namespace BL\Entity;

/**
 * Report
 *
 * @Table(name="payment_lineitems")
 * @Entity(repositoryClass="BL\Entity\Repository\PaymentLineItemsRepository")
 * @author Mahbub
 */
class PaymentLineItems {

    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", length=4)
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(name="payment_id",type="string", length=15, nullable=true)
     */
    private $payment_id;

    /**
     * @Column(name="kp_lineitem", type="string", length=25, nullable=true)
     */
    private $kp_lineitem;

    /**
     * @Column(name="record_date", type="datetime", nullable=true)
     */
    private $record_date;

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
     * @Column(name="sharing", type="boolean", nullable=true)
     */
    private $sharing;

    /**
     * @Column(name="percent_amc", type="float", nullable=true)
     */
    private $percent_amc;

    /**
     * @Column(name="last_modified_date", type="datetime", nullable=true)
     */
    private $last_modified_date;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="client_id", referencedColumnName="id", nullable=true) 
     */
    private $client;

    /**
     * @ManyToOne(targetEntity="Payment")
     * @JoinColumn(name="pmt_id", referencedColumnName="id", nullable=true) 
     */
    private $pmt_id;
    /**
      * @Column(name="amount_paid", type="float", nullable=true) 
     */
    private $amount_paid;
    /**
      * @Column(name="late_paid", type="float", nullable=true) 
     */
    private $late_paid;
    /**
      * @Column(name="adv_paid", type="float", nullable=true) 
     */
    private $adv_paid;

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
