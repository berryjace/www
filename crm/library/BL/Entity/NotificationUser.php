<?php

//use Doctrine\ORM\Mapping as ORM;

namespace BL\Entity;

/**
 * NotificationUser
 *
 * @Table(name="notifications_users")
 * @Entity(repositoryClass="BL\Entity\Repository\NotificationUserRepository")
 * @author Tanzim
 */
class NotificationUser {

    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var integer $notification_id
     * @ManyToOne(targetEntity="Notification")
     * @JoinColumn(name="notification_id", referencedColumnName="id", nullable=true)
     */
    private $notification_id;
    
    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     *
     */
    private $user_id;

    public function __construct() {
        $this->Notification = new \Doctrine\Common\Collections\ArrayCollection();
        $this->User = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}