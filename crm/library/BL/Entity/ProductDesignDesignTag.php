<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * ProductDesignDesignTag
 *
 * @Table(name="product_designs_design_tags")
 * @Entity(repositoryClass="BL\Entity\Repository\ProductDesignDesignTagRepository")
 * @author Tanzim
 */
class ProductDesignDesignTag
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
     * @ManyToOne(targetEntity="ProductDesign",inversedBy="ProductDesignDesignTag")
     * @JoinColumn(name="product_design_id", referencedColumnName="id", nullable=true)
     */
    private $product_design_id;

    /**
     * @ManyToOne(targetEntity="DesignTag",inversedBy="ProductDesignDesignTag")
     * @JoinColumn(name="design_tag_id", referencedColumnName="id", nullable=true)
     */
    private $design_tag_id;
    
//    public function setProductDesignID($id)
//    {
//        $this->product_design_id = $id;
//    }
//    
//    public function setDesignTagID($id)
//    {
//        $this->design_tag_id = $id;
//    }

    public function __construct()
    {
        $this->ProductDesign = new \Doctrine\Common\Collections\ArrayCollection();
        $this->DesignTag = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }
    
}