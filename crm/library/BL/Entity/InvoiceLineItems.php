<?php

namespace BL\Entity;

/**
 * InvoiceLineItems
 *
 * @Table(name="invoice_lineitems")
 * @Entity(repositoryClass="BL\Entity\Repository\InvoiceLineItemsRepository")
 * @author Tanzim
 */
class InvoiceLineItems {

    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", length=11)
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(name="lineitems_number", type="string", length=30, nullable=true)
     */
    private $lineitems_number;

    /**
     * @Column(name="invoice_number_li", type="string", length=30, nullable=true)
     */
    private $invoice_number_li;

    /**
     * @Column(name="amount_due", type="float", nullable=true)
     */
    private $amount_due;

    /**
     * @Column(name="amount_paid", type="float", nullable=true)
     */
    private $amount_paid;

    /**
     * @Column(name="created_at", type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @Column(name="check_number", type="string", length=25, nullable=true)
     */
    private $check_number;

    /**
     * @Column(name="payment_status", type="string", length=25, nullable=true)
     */
    private $payment_status;

    /**
     * @Column(name="invoice_status", type="string", length=25, nullable=true)
     */
    private $invoice_status;

    /**
     * @Column(name="fiscal_year", type="string", length=25, nullable=true)
     */
    private $fiscal_year;

    /**
     * @Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @Column(name="quarter", type="integer", length=11, nullable=true)
     */
    private $quarter;

    /**
     * @ManyToOne(targetEntity="Invoice")
     * @JoinColumn(name="invoice_id", referencedColumnName="id", nullable=true)
     */
    private $invoice_id;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="client_id", referencedColumnName="id", nullable=true)
     */
    private $client_id;
    

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
