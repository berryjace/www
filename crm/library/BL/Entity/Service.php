<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * VendorService
 *
 * @Table(name="services")
 * @Entity(repositoryClass="BL\Entity\Repository\ServiceRepository")
 * @author Rashed
 */
class Service
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
    * @Column(type="string",length=254, nullable=true)
    */
    private $title;

    public function __construct()
    {
        //$this->Vendor = new \Doctrine\Common\Collections\ArrayCollection();
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