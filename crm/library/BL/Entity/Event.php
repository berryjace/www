<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * Event
 *
 * @Table(name="events")
 * @Entity(repositoryClass="BL\Entity\Repository\EventRepository")
 * @author Tanzim
 */
class Event
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
     * @var string $title
     *
     * @Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var text $message
     *
     * @Column(name="message", type="text")
     */
    private $message;

    /**
     * @var string $location
     *
     * @Column(name="location", type="string", length=500)
     */
    private $location;

    /**
     * @var string $for_user_type
     *
     * @Column(name="for_user_type", type="string")
     */
    private $for_user_type;

    /**
     * @var datetime $start_time
     *
     * @Column(name="start_time", type="datetime")
     */
    private $start_time;

    /**
     * @var datetime $end_time
     *
     * @Column(name="end_time", type="datetime")
     */
    private $end_time;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    private $created_by;

    public function __construct()
    {
        //$this->User = new \Doctrine\Common\Collections\ArrayCollection();
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