<?php

//use Doctrine\ORM\Mapping as ORM;

namespace BL\Entity;

/**
 * MailChimpList
 *
 * @Table(name="mail_chimp_List")
 * @Entity(repositoryClass="BL\Entity\Repository\MailChimpListRepository")
 * @author Tanzim
 */
class MailChimpList {

    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", length=8)
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var string $list_id
     *
     * @Column(name="list_id", type="string", length=255, unique=true)
     */
    private $list_id;
    /**
     * @var datetime $sent_time
     *
     * @Column(name="sent_time", type="datetime")
     */
    private $sent_time;

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}