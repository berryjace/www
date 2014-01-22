<?php

//use Doctrine\ORM\Mapping as ORM;

namespace BL\Entity;

/**
 *
 * @Table(name="vendor_royalty_report_submissions")
 * @Entity(repositoryClass="BL\Entity\Repository\VendorRoyaltyReportSubmissionsRepository")
 * @author Tanzim
 * @author Mahbub
 */
class VendorRoyaltyReportSubmissions {

    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", length=4)
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(name="year", type="string", length=7, nullable=true)
     */
    private $year;

    /**
     * @Column(name="quarter", type="integer")
     */
    private $quarter;

    /**
     * @Column(name="status", type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @Column(name="submission_type", type="string", length=20, nullable=true)
     */
    private $submission_type;

    /**
     * @Column(name="uploaded_on",type="datetime", nullable=true)
     */
    private $uploaded_on;

    /**
     * @Column(name="file_url", type="string", length=255, nullable=true)
     */
    private $file_url;

    /**
     * @Column(name="product_sold_to", type="string", length=255, nullable=true)
     */
    private $product_sold_to;

    /**
     * @Column(name="invoice_num", type="string", length=40, nullable=true)
     */
    private $invoice_num;

    /**
     * @Column(name="product_desc", type="string", length=255, nullable=true)
     */
    private $product_desc;

    /**
     * @Column(name="invoice_date", type="date", nullable=true)
     */
    private $invoice_date;

    /**
     * @Column(name="quantity", type="integer", length=6,nullable=true)
     */
    private $quantity;

    /**
     * @Column(name="unit_price", type="decimal", length=10, scale=2,nullable=true)
     */
    private $unit_price;

    /**
     * @Column(name="gross_sales", type="decimal", length=10, scale=2,nullable=true)
     */
    private $gross_sales;

    /**
     * @Column(name="sales_revenue", type="decimal", length=12, nullable=true)
     */
    private $sales_revenue;

    /**
     * @Column(name="royalty_commission", type="decimal", length=5, scale=2, nullable=true)
     */
    private $royalty_commission;

    /**
     * @Column(name="annual_advance", type="decimal", length=10, scale=2, nullable=true)
     */
    private $annual_advance;

    /**
     * @Column(name="royalty_before_adv", type="decimal", length=12, scale=2, nullable=true)
     */
    private $royalty_before_adv;

    /**
     * @Column(name="royalty_after_adv", type="decimal", length=12, scale=2, nullable=true)
     */
    private $royalty_after_adv;

    /**
     * @Column(name="submission_hash", type="string", length=50, nullable=true)
     */
    private $submission_hash;

    /**
     * @Column(name="email_date" , type="date" , length=255 , nullable=true)
     */
    private $email_date;
    
    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumns({
     *   @JoinColumn(name="vendor_id", referencedColumnName="id")
     * })
     */
    private $vendor;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumns({
     *   @JoinColumn(name="client_id", referencedColumnName="id",nullable=true)
     * })
     */
    private $client;

    public function __construct() {
        $this->uploaded_on = new \DateTime();
    }

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}