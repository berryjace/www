<?php

namespace BL\Entity;

/**
 * Class BankInfo to manage vendors bank information
 *
 * @Table(name="bank_infos")
 * @Entity(repositoryClass="BL\Entity\Repository\BankInfoRepository")
 * @author Masud
 */
class BankInfo {

    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", length=11)
     * @Id         
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(name="account_number" , type="string" , length=20 , nullable=false)
     */
    private $account_number;

    /**
     * @Column(name="routing_number" , type="string" , length=20 , nullable=false)
     */
    private $routing_number;

    /**
     * @Column(name="created_at" , type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @Column(name="updated_at" , type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @OneToOne(targetEntity="User")
     * @JoinColumn(name="vendor_id", referencedColumnName="id", nullable=false)
     */
    private $vendor;

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
