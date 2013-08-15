<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * DesignTag
 *
 * @Table(name="design_tags")
 * @Entity(repositoryClass="BL\Entity\Repository\DesignTagRepository")
 * @author Tanzim
 */
class DesignTag
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
     * @var string $tag_name
     *
     * @Column(name="tag_name", type="string", length=254)
     */
    private $tag_name;

    /**
     * @OneToMany(targetEntity="ProductDesignDesignTag",mappedBy="design_tag_id",cascade={"persist,remove"})
     */
    private $Design;

    public function __construct()
    {
        $this->Design = new \Doctrine\Common\Collections\ArrayCollection();
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