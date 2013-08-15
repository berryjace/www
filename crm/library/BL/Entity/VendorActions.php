<?php

namespace BL\Entity;

/**
 * VendorActions
 *
 * @Table(name="vendor_actions")
 * @Entity(repositoryClass="BL\Entity\Repository\VendorActionsRepository")
 * @author Mahbub
 */
class VendorActions {

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
     * @Column(name="action", type="string",length=255)
     */
    private $action;
    /**
     *
     * @Column(name="assignment_date", type="datetime")
     */
    private $assignment_date;
    /**
     *
     * @Column(name="resolution", type="text")
     */
    private $resolution;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="client_id", referencedColumnName="id", nullable=true)
     */
    private $client_id;
    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="vendor_id", referencedColumnName="id", nullable=true)
     */
    private $vendor_id;

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
