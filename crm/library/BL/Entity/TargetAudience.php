<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * TargetAudience
 *
 * @Table(name="target_audiences")
 * @Entity(repositoryClass="BL\Entity\Repository\TargetAudienceRepository")
 * @author Tanzim
 */
class TargetAudience
{
    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", length=4)
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(name="name", type="string", length=85)
     */
    private $name;

     /**
     * @ManyToMany(targetEntity="License", mappedBy="TargetAudience")
     */
    private $License;


    public function __construct()
    {
        $this->License = new \Doctrine\Common\Collections\ArrayCollection();
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