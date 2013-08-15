<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * ClientUsageGuide
 *
 * @Table(name="client_usage_guides")
 * @Entity(repositoryClass="BL\Entity\Repository\ClientUsageGuideRepository")
 * @author Tanzim
 */
class ClientUsageGuide
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
     * @var string $guide_name
     *
     * @Column(name="guide_name", type="string", length=255, nullable=false)
     */
    private $guide_name;

    /**
     * @var string $guide_url
     *
     * @Column(name="guide_url", type="string", length=255, nullable=true)
     */
    private $guide_url;

    /**
     * @var text $guide_content
     *
     * @Column(name="guide_content", type="text", nullable=true)
     */
    private $guide_content;

    /**
     * @var string $guide_type
     *
     * @Column(name="guide_type", type="string", nullable=true)
     */
    private $guide_type;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user_id;

    public function __construct()
    {
        $this->User = new \Doctrine\Common\Collections\ArrayCollection();
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