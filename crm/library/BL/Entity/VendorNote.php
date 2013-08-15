<?php

namespace BL\Entity;

/**
 * VendorNote
 *
 * @Table(name="vendor_notes")
 * @Entity(repositoryClass="BL\Entity\Repository\VendorNoteRepository")
 * @author Sukhon
 */
class VendorNote {

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
     * @var datetime
     * @Column(name="created_at", type="datetime")
     */
    private $created_at;
    
    /**
     * 
     * @Column(name="updated_at", type="datetime")
     */
    private $updated_at;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="vendor_id", referencedColumnName="id", nullable=true)
     */
    private $vendor;

    public function __construct() {
        $this->created_at = new \DateTime(date("Y-m-d H:i:s"));
        $this->updated_at = new \DateTime(date("Y-m-d H:i:s"));
    }

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}
