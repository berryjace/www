<?php

namespace BL\Entity;

/**
 * ClientNote
 *
 * @Table(name="client_notes")
 * @Entity(repositoryClass="BL\Entity\Repository\ClientNoteRepository")
 * @author Sukhon
 */
class ClientNote {

    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", length=4)
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     *
     * @Column(name="note", type="text")
     */
    private $note;

    /**
     *
     * @Column(name="update_date", type="datetime")
     */
    private $update_date;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="client_id", referencedColumnName="id", nullable=true)
     */
    private $client_id;

    public function __construct() {
        $this->update_date = new \DateTime(date("Y-m-d H:i:s"));
    }

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}
