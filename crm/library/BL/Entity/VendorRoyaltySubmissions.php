<?php

namespace BL\Entity;

/**
 *
 * @Table(name="vendor_royalty_submissions")
 * @Entity(repositoryClass="BL\Entity\Repository\VendorRoyaltySubmissionsRepository")
 * @author Rashed (Blueliner Marketing)
 */
class VendorRoyaltySubmissions {

    /**
     *
     * @var integer $id
     * @Column(name="id", type="integer",nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
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
     * @Column(name="uploaded_on",type="datetime", nullable=true)
     */
    private $uploaded_on;

    /**
     * @Column(name="file_url", type="string", length=255, nullable=true)
     */
    private $file_url;
    /**
     * @Column(name="type", type="string", length=12, nullable=true)
     */
    private $type;

    /**
     * @Column(name="submission_hash", type="string", length=50, nullable=false)
     */
    private $submission_hash;


    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumns({
     *   @JoinColumn(name="vendor_id", referencedColumnName="id")
     * })
     */
    private $vendor;

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
