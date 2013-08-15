<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * VendorSampleFile
 *
 * @Table(name="vendor_sample_files")
 * @Entity(repositoryClass="BL\Entity\Repository\VendorSampleFileRepository")
 * @author Tanzim
 */
class VendorSampleFile
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
     * @var string $title
     *
     * @Column(name="title", type="string", length=50, nullable=true)
     */
    private $title;

    /**
     * @var string $file_url
     *
     * @Column(name="file_url", type="string", length=200, nullable=true)
     */
    private $file_url;

    /**
     * @var string $file_extension
     *
     * @Column(name="file_extension", type="string", length=20, nullable=true)
     */
    private $file_extension;

    /**
     * @Column(type="datetime", nullable=true)
     */
    private $upload_date;

    /**
     * @Column(name="active", type="integer", length=2, nullable=true)
     */
    private $active;

    /**
     * @var string $use_for
     *
     * @Column(name="use_for", type="string", length=50, nullable=true)
     */
    private $use_for;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumns({
     *   @JoinColumn(name="vendor_id", referencedColumnName="id")
     * })
     */
    private $Vendor;

    /**
     * @ManyToOne(targetEntity="Product")
     * @JoinColumns({
     *   @JoinColumn(name="product_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $product_id;

    public function __construct()
    {
        $this->Vendor = new \Doctrine\Common\Collections\ArrayCollection();
        $this->upload_date = new \DateTime(date("Y-m-d H:i:s"));
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