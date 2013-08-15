<?php

//use Doctrine\ORM\Mapping as ORM;

namespace BL\Entity;

/**
 * RoyaltyReport
 *
 * @Table(name="royalty_reports")
 * @Entity(repositoryClass="BL\Entity\Repository\RoyaltyReportRepository")
 * @author Masud
 */
class RoyaltyReport {

    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", length=11)
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;    
    
    /**
     * @Column(name="year", type="string", length=7, nullable=true)
     */
    private $year;

    /**
     * @Column(name="quarter", type="integer", length=1)
     */
    private $quarter;

    /**
     * @Column(name="status", type="string", length=20, nullable=true)
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
     * @Column(name="email_date" , type="datetime", nullable=true)
     */
    private $email_date;

    /**
     * @Column(name="invoice_num", type="string", length=40, nullable=true)
     */
    private $invoice_num;

    /**
     * @Column(name="invoice_date", type="date", nullable=true)
     */
    private $invoice_date;
    
    /**
     * @Column(name="created_at" , type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @Column(name="updated_at" , type="datetime", nullable=true)
     */
    private $updated_at;   

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumns({
     *   @JoinColumn(name="vendor_id", referencedColumnName="id")
     * })
     */
    private $vendor_id;
    
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