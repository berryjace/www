<?php

namespace BL\Entity;
/**
 * Class to store online payment information
 *
 * @Table(name="online_payments")
 * @Entity(repositoryClass="BL\Entity\Repository\OnlinePaymentRepository")
 * @author Masud
 */
class OnlinePayment {
    
    /**
     * @var integer
     *
     * @Column(name="id", type="integer", length=11)
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var decimal
     * @Column(name="amount", type="decimal", length=18, scale=2, nullable=true)
     */
    private $amount;
    
    /**
     * @var integer
     * @Column(name="bank_account", type="integer", length=17)
     */
    private $bank_account;
    
    /**
     * @var integer
     * @Column(name="bank_routing", type="integer", length=9)
     */
    private $bank_routing;        
    
    /**     
     * @var integer
     * @ManyToOne(targetEntity="User")
     * @JoinColumns({
     *  @JoinColumn(name="vendor_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $vendor;
    
    /**
     * @var integer
     * @ManyToOne(targetEntity="Invoice")
     * @JoinColumns({
     *   @JoinColumn(name="invoice_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $invoice;
    
    public function __construct() {
        $this->vendor = new \Doctrine\Common\Collections\ArrayCollection();
        $this->invoce = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }    
}
