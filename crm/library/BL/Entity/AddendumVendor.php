<?php
//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;


/**
 * @Table(name="addendums_vendors")
 * @Entity(repositoryClass="BL\Entity\Repository\AddendumVendorRepository")
 * @author Masud
 */
class AddendumVendor
{
    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", length=8)
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var integer $addendum_id
     * @ManyToOne(targetEntity="Addendum")
     * @JoinColumn(name="addendum_id", referencedColumnName="id", nullable=true)
     *
     */     
    private $addendum_id;

    /**
     * @var integer $vendor_id
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="vendor_id", referencedColumnName="id", nullable=true)
     *
     */    
    private $vendor_id;
    
    /**
     * @var $signator_name
     * @Column(name="signator_name", type="text", length=50, nullable=true)
     */
    private $signator_name;
    
    /**
     * @var $signator_title
     * @Column(name="signator_title", type="text", length=50, nullable=true)
     */
    private $signator_title;
    
    /**
     * @var $is_agreed
     * @Column(name="is_agreed", type="boolean", nullable=true)
     */
    private $is_agreed;
    
    /**
     * @var $sign_date
     * @Column(name="sign_date", type="datetime", nullable=false)
     */
    private $sign_date;  
            
    public function __construct() {
        $this->Addendum = new \Doctrine\Common\Collections\ArrayCollection();
        $this->User = new \Doctrine\Common\Collections\ArrayCollection();
        $this->create_date = new \DateTime(date("Y-m-d H:i:s"));
    }
    
    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property,$value)
    {
        $this->$property = $value;
    }
}