<?php

//use Doctrine\ORM\Mapping as ORM;

namespace BL\Entity;

/**
 * Notification
 *
 * @Table(name="notifications")
 * @Entity(repositoryClass="BL\Entity\Repository\NotificationRepository")
 * @author Tanzim
 */
class Notification {

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
     * @var string $send_via
     *
     * @Column(name="send_via", type="string")
     */
    private $send_via;
    /**
     * @var string $for_user_type
     *
     * @Column(name="for_user_type", type="string")
     */
    private $for_user_type;
    /**
     * @var datetime $time
     *
     * @Column(name="time", type="datetime")
     */
    private $time;
    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    private $created_by;


    public function __construct() {
        $this->User = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}