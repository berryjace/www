<?php

namespace BL\Entity;
/**
 * Description of LicensePayment
 *
 * @Table(name="license_payments")
 * @Entity(repositoryClass="BL\Entity\Repository\LicensePaymentRepository")
 * @author Masud
 */
class LicensePayment {
    
    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", length=11)
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var decimal $amount
     * @Column(name="amount", type="decimal", length=18, scale=2, nullable=true)
     */
    private $amount;
    
    /**
     * @var integer $bank_account
     * @Column(name="bank_account", type="integer", length=17)
     */
    private $bank_account;
    
    /**
     * @var integer $bank_routing
     * @Column(name="bank_routing", type="integer", length=9)
     */
    private $bank_routing;
    
    /**
     * @var string $invoice_number
     * @Column(name="invoice_number", type="string", length=16)
     */
    private $invoice_number;    
    
    /**
     * @OneToOne(targetEntity="License")
     * @JoinColumns({
     *   @JoinColumn(name="license_id", referencedColumnName="id", unique=true)
     * })
     */
    private $license_id;
    
    public function __construct() {
        $this->license_id = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }    
}
