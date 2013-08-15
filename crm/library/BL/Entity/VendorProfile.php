<?php

namespace BL\Entity;

/**
 *
 * @Table(name="vendor_profiles")
 * @Entity(repositoryClass="BL\Entity\Repository\VendorProfileRepository")
 * @author Rashed (Blueliner Marketing)
 */
class VendorProfile {

    /**
     *
     * @var integer $id
     * @Column(name="id", type="integer",nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @Column(type="string",length=254, nullable=true)
     */
    private $organization_name;

    /**
     * @Column(type="string",length=254, nullable=true)
     */
    private $address1;

    /**
     * @Column(type="string",length=254, nullable=true)
     */
    private $address2;

    /**
     * @Column(type="string",length=254, nullable=true)
     */
    private $city;

    /**
     * @Column(type="string",length=254, nullable=true)
     */
    private $state;

    /**
     * @Column(type="string",length=254, nullable=true)
     */
    private $zip;

    /**
     * @Column(type="string",length=254, nullable=true)
     */
    private $email;

    /**
     * @Column(type="string",length=254, nullable=true)
     */
    private $phone1;

    /**
     * @Column(type="string",length=254, nullable=true)
     */
    private $phone2;

    /**
     * @Column(type="string",length=254, nullable=true)
     */
    private $fax;

    /**
     * @Column(type="string",length=254, nullable=true)
     */
    private $web_page;

    /**
     * @Column(type="string", length=200, nullable=true)
     */
    private $logo_url;

    /**
     * @Column(type="string", length=20, nullable=true)
     */
    private $logo_extension;

    /**
     * @Column(name="product_offered", type="text", nullable=true)
     */
    private $product_offered;

    /**
     * @Column(name="company_discripction", type="text", nullable=true)
     */
    private $company_discripction;

    /**
     * @Column(name="update_date", type="datetime", nullable=true)
     */
    private $update_date;

    /**
     * @OneToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id",nullable=true)
     */
    private $user_id;

    /**
     * @Column(name="active", type="integer", length=2, nullable=true)
     */
    private $active;

    /**
     * @Column(type="text",nullable=true)
     */
    private $reason_for_declining;

    /**
     * @var string $vendor_abstract
     * @Column(name="vendor_abstract", type="string", length=255 , nullable=true)
     */
    private $vendor_abstract;

    /**
     * @var string $vendor_type
     * @Column(name="vendor_type", type="string", length=255 , nullable=true)
     */
    private $vendor_type;

    /**
     * @var string $vendor_manufacturer
     * @Column(name="vendor_manufacturer", type="string", length=50 , nullable=true)
     */
    private $vendor_manufacturer;

    /**
     * @var string $vendor_travel
     * @Column(name="vendor_travel", type="string", length=50 , nullable=true)
     */
    private $vendor_travel;

    /**
     * @var string $vendor_retail
     * @Column(name="vendor_retail", type="string", length=50 , nullable=true)
     */
    private $vendor_retail;

    /**
     * @var string $vendor_licensed
     * @Column(name="vendor_licensed", type="string", length=50 , nullable=true)
     */
    private $vendor_licensed;

    public function __construct() {
        $this->update_date = new \DateTime(date("Y-m-d H:i:s"));
    }

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}
