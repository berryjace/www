<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * EventUser
 *
 * @Table(name="events_users")
 * @Entity(repositoryClass="BL\Entity\Repository\EventUserRepository")
 * @author Tanzim
 */
class EventUser
{
    /**
     * @var integer $event_id
     *
     * @Column(name="event_id", type="integer", length=4)
     */
    private $event_id;

    /**
     * @var integer $user_id
     *
     * @Column(name="user_id", type="integer", length=8)
     */
    private $user_id;

    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
  

    public function __construct()
    {
        
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