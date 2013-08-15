<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * VendorService
 *
 * @Table(name="vendor_services")
 * @Entity(repositoryClass="BL\Entity\Repository\VendorServiceRepository")
 * @author Rashed
 */
class VendorService
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
     * @ManyToOne(targetEntity="Service")
     * @JoinColumn(name="service_id", referencedColumnName="id")
     */
    private $service_id;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="vendor_id", referencedColumnName="id")
     */
    private $vendor_id;

    public function __construct()
    {
        
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