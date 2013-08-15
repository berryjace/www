<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * ProductDesign
 *
 * @Table(name="product_designs")
 * @Entity(repositoryClass="BL\Entity\Repository\ProductDesignRepository")
 * @author Tanzim
 */
class ProductDesign
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
     * @var string $file_url
     *
     * @Column(name="file_url", type="string", length=254)
     */
    private $file_url;

    /**
     * @var string $description
     *
     * @Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var text $note_from_admin
     *
     * @Column(name="note_from_admin", type="text", nullable=true)
     */
    private $note_from_admin;

    /**
     * @var string $is_approved
     * pending=0, approved=1, rejected=2, resubmitted=3
     * @Column(name="is_approved", type="string", length=20, nullable=true)
     */
    private $is_approved;

    /**
     * @Column(type="datetime", nullable=true)
     */
    private $upload_date;

    /**
     * @ManyToOne(targetEntity="Product")
     * @JoinColumns({
     *   @JoinColumn(name="product_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $product;
    
    /**
     * @ManyToMany(targetEntity="User", cascade={"persist,remove"})
     * @JoinTable(name="product_designs_users",
     *      joinColumns={@JoinColumn(name="product_design_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="client_id", referencedColumnName="id")})
     */
    
    private $Client;
    
    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner_id;
    
    /**
     * @OneToMany(targetEntity="ProductDesignDesignTag",mappedBy="product_design_id",cascade={"persist,remove"})
     */
    private $Tags;

    public function __construct()
    {
        $this->Tags = new \Doctrine\Common\Collections\ArrayCollection();
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