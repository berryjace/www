<?php
namespace BL\Entity;

/**
 *
 * @Table(name="vendor_operations")
 * @Entity(repositoryClass="BL\Entity\Repository\VendorOperationRepository")
 * @author Sukhon (Blueliner Marketing)
 */
class VendorOperation {

    /**
     *
     * @var integer $id
     * @Column(name="id", type="integer",nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
    * @Column(type="date", nullable=true)
    */
    private $insurance_received;
    /**
     * @Column(type="date", nullable=true)
     */
    private $insurance_expire;
    /**
     * @Column(type="string",length=200, nullable=true)
     */
    private $insurance_contact;
    /**
     * @Column(type="string",length=200, nullable=true)
     */
    private $insurance_company;
    /**
     * @Column(type="string",length=80, nullable=true)
     */
    private $insurance_phone;
    /**
     * @Column(type="boolean", nullable=true)
     */
    private $have_late_fee;
    /**
     * @Column(type="boolean", nullable=true)
     */
    private $is_retail;
    /**
     * @Column(type="boolean", nullable=true)
     */
    private $is_wholesale;
    /**
     * @Column(type="boolean", nullable=true)
     */
    private $is_travel;
    
    /**
     * @Column(type="string",length=254, nullable=true)
     */
    private $vendor_type;
    /**
     * @Column(type="string",length=254, nullable=true)
     */
    private $vendor_royalty_structure;
    /**
     * @Column(type="text", nullable=true)
     */
    private $vendor_products;
    /**
     * @Column(type="text", nullable=true)
     */
    private $vendor_recommendation_to_client;
    /**
     * @Column(type="text", nullable=true)
     */
    private $default_note_to_vendor;
    
    /**
     * @Column(type="boolean", nullable=true)
     */
    private $vendor_sale_online;
    
    /**
     * @Column(type="boolean", nullable=true)
     */
    private $vendor_have_storefont;

     /**
     * @Column(type="string",length=254, nullable=true)
     */
    private $vendor_reporting_type;
    
    /**
     * @OneToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id",nullable=false)
     */
    private $user_id;

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}
