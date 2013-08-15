<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * VendorSampleFile
 *
 * @Table(name="vendor_product_infos")
 * @Entity(repositoryClass="BL\Entity\Repository\VendorProductInfoRepository")
 * @author Tanzim
 */
class VendorProductInfo
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
     * @var string $supplier_name
     *
     * @Column(name="supplier_name", type="string", length=250, nullable=true)
     */
    private $supplier_name;

    /**
     * @var string $other_desc
     *
     * @Column(name="other_desc", type="string", length=250, nullable=true)
     */
    private $other_desc;
    
    /**
     * @OneToOne(targetEntity="User")
     * @JoinColumn(name="vendor_id", referencedColumnName="id",nullable=false)
     */
    private $vendor_id;

    public function __construct()
    {
//        $this->Vendor = new \Doctrine\Common\Collections\ArrayCollection();
//        $this->upload_date = new \DateTime(date("Y-m-d H:i:s"));
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