<?php

//use Doctrine\ORM\Mapping as ORM;

namespace BL\Entity;

/**
 * NotificationLastView
 *
 * @Table(name="notification_last_view")
 * @Entity(repositoryClass="BL\Entity\Repository\NotificationLastViewRepository")
 * @author zea
 */
class NotificationLastView {

    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @OneToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user_id;

    /**
     * @var datetime $time
     *
     * @Column(name="time", type="datetime")
     */
    private $time;

    public function __construct() {        
        $this->time = new \DateTime(date("Y-m-d H:i:s"));        
    }
    
    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}