<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * Product
 *
 * @Table(name="products")
 * @Entity(repositoryClass="BL\Entity\Repository\ProductRepository")
 * @author Tanzim
 */
class Product
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
     * @Column(name="product_name", type="string", length=70)
     */
    private $product_name;

    /**
     * @ManyToOne(targetEntity="ProductCategory")
     * @JoinColumn(name="product_category_id", referencedColumnName="id", nullable=true)
     */
    private $product_category_id;

    

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
    
}