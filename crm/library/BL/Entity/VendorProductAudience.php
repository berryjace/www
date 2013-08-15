<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * VendorService
 *
 * @Table(name="vendor_product_audiences")
 * @Entity(repositoryClass="BL\Entity\Repository\VendorProductAudienceRepository")
 * @author Rashed
 */
class VendorProductAudience
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
     * @ManyToOne(targetEntity="TargetAudience")
     * @JoinColumn(name="audience_id", referencedColumnName="id")
     */
    private $audience_id;

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