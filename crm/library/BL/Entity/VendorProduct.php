<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * VendorProduct
 *
 * @Table(name="vendor_products")
 * @Entity(repositoryClass="BL\Entity\Repository\VendorProductRepository")
 * @author Tanzim
 */
class VendorProduct
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
     * 
     * @ManyToOne(targetEntity="Product")
     * @JoinColumns({
     *   @JoinColumn(name="product_id", referencedColumnName="id")
     * })
     */
    private $product_id;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumns({
     *   @JoinColumn(name="vendor_id", referencedColumnName="id")
     * })
     */
    private $vendor_id;

    /**
     * @ManyToOne(targetEntity="License")
     * @JoinColumns({
     *   @JoinColumn(name="license_id", referencedColumnName="id")
     * })
     */
    private $license_id;

    public function __construct()
    {
        $this->product_id = new \Doctrine\Common\Collections\ArrayCollection();
        $this->vendor_id = new \Doctrine\Common\Collections\ArrayCollection();
        $this->license_id = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }
    
}