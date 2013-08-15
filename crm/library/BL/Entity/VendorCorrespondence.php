<?php
namespace BL\Entity;

/**
 * VendorCorrespondence
 *
 * @Table(name="vendor_correspondence")
 * @Entity(repositoryClass="BL\Entity\Repository\VendorCorrespondenceRepository")
 * @author Mahbub
 */
  class VendorCorrespondence
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
     *
     * @Column(name="subject", type="string",length=255, nullable=true)
     */
    private $subject;
    /**
     *
     * @Column(name="note", type="text",nullable=true)
     */
    private $note;

    /**
     *
     * @Column(name="note_time", type="datetime")
     */
    private $note_time;

    /**
     *
     * @Column(name="note_code", type="string", length=55,nullable=true)
     */
    private $note_code;

    /**
     *
     * @Column(name="create_date", type="datetime")
     */
    private $create_date;

    /**
     *
     * @Column(name="update_date", type="datetime")
     */
    private $update_date;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="vendor_id", referencedColumnName="id", nullable=true)
     */
    private $vendor_id;
    
    public function __construct()
    {
        $this->create_date = new \DateTime(date("Y-m-d H:i:s"));
        $this->update_date = new \DateTime(date("Y-m-d H:i:s"));
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
