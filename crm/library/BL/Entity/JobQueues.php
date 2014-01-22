<?php

namespace BL\Entity;

/**
 *
 * @Table(name="job_queues")
 * @Entity(repositoryClass="BL\Entity\Repository\JobQueuesRepository")
 * @author Mahbub
 */
class JobQueues {

    /**
     * @Column(name="id", type="integer",nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @Column(type="integer",length=11,nullable=true)
     */
    private $process_id;

    /**
     * @Column(type="string",length=20,nullable=true)
     */
    private $status;

    /**
     * @Column(type="string",length=100,nullable=true)
     */
    private $function_name;

    /**
     * @Column(type="float",nullable=true)
     */
    private $percent_done;

    /**
     * @Column(type="text",nullable=true)
     */
    private $process_details;

    /**
     * @Column(type="datetime",nullable=true)
     */
    private $process_started;

    public function __construct() {
        $this->process_started = new \DateTime(date("Y-m-d H:i:s"));
    }

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}