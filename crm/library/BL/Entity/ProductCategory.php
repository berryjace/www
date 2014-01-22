<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * ProductCategory
 *
 * @Table(name="product_categories")
 * @Entity(repositoryClass="BL\Entity\Repository\ProductCategoryRepository")
 * @author Tanzim
 */
class ProductCategory
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
     * @Column(name="cat_name", type="string", length=70)
     */
    private $cat_name;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }

}