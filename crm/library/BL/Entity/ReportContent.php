<?php

//use Doctrine\ORM\Mapping as ORM;

namespace BL\Entity;

/**
 * ReportContent
 *
 * @Table(name="report_contents")
 * @Entity(repositoryClass="BL\Entity\Repository\ReportContentRepository")
 * @author Tanzim
 */
class ReportContent {

    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", length=4)
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(name="product_sold_to", type="string", length=255)
     */
    private $product_sold_to;

    /**
     * @Column(name="invoice_date", type="date")
     */
    private $invoice_date;

    /**
     * @Column(name="invoice_number", type="string", length=45)
     */
    private $invoice_number;

    /**
     * @Column(name="product_description", type="text")
     */
    private $product_description;

    /**
     * @Column(name="quantity", type="integer", length=4)
     */
    private $quantity;

    /**
     * @Column(name="price_per_unit", type="decimal", length=10, scale=2)
     */
    private $price_per_unit;

    /**
     * @Column(name="gross_sales", type="decimal", length=10, scale=2)
     */
    private $gross_sales;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumns({
     *   @JoinColumn(name="vendor_id", referencedColumnName="id")
     * })
     */
    private $vendor;

    public function __construct() {
        $this->report = new \Doctrine\Common\Collections\ArrayCollection();
        $this->user = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}