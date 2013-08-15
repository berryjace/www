<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * VendorSpecificFile
 *
 * @Table(name="vendor_specific_files")
 * @Entity(repositoryClass="BL\Entity\Repository\VendorSpecificFileRepository")
 * @author Tanzim
 */
class VendorSpecificFile
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
     * @Column(name="title", type="string", length=50)
     */
    private $title;

    /**
     * @var string $file_url
     *
     * @Column(name="file_url", type="string", length=200)
     */
    private $file_url;

    /**
     * @var string $file_extension
     *
     * @Column(name="file_extension", type="string", length=20)
     */
    private $file_extension;

    /**
     * @Column(type="datetime", nullable=true)
     */
    private $upload_date;


    /**
     * @ManyToOne(targetEntity="License")
     * @JoinColumns({
     *   @JoinColumn(name="license_id", referencedColumnName="id")
     * })
     */
    private $License;

    public function __construct()
    {
        $this->License = new \Doctrine\Common\Collections\ArrayCollection();
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