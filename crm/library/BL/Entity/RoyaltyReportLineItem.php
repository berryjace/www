<?php

//use Doctrine\ORM\Mapping as ORM;

namespace BL\Entity;

/**
 * RoyaltyReportLineItem
 *
 * @Table(name="royalty_report_lineitems")
 * @Entity(repositoryClass="BL\Entity\Repository\RoyaltyReportLineItemRepository")
 * @author Masud
 */
class RoyaltyReportLineItem {

    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", length=11)
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(name="product_sold_to", type="string", length=100)
     */
    private $product_sold_to;

    /**
     * @Column(name="product_desc", type="string", length=255, nullable=true)
     */
    private $product_desc;

    /**
     * @Column(name="quantity", type="integer", length=6, nullable=true)
     */
    private $quantity;

    /**
     * @Column(name="unit_price", type="decimal", length=10, scale=2, nullable=true)
     */
    private $unit_price;      
    
    /**
     * @Column(name="gross_sales", type="decimal", length=10, scale=2, nullable=true)
     */
    private $gross_sales;

    /**
     * @Column(name="created_at" , type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @Column(name="updated_at" , type="datetime", nullable=true)
     */
    private $updated_at; 
    
    /**
     * @ManyToOne(targetEntity="RoyaltyReport")
     * @JoinColumns({
     *   @JoinColumn(name="royalty_id", referencedColumnName="id")
     * })
     */
    private $royalty_id;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumns({
     *   @JoinColumn(name="client_id", referencedColumnName="id")
     * })
     */
    private $client_id;

    public function __construct() {        
        $this->updated_at = new \DateTime(date("Y-m-d H:i:s"));
    }

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}