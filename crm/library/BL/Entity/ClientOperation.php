<?php
namespace BL\Entity;

/**
 *
 * @Table(name="client_operations")
 * @Entity(repositoryClass="BL\Entity\Repository\ClientOperationRepository")
 * @author Sukhon (Blueliner Marketing)
 */
class ClientOperation {

    /**
     *
     * @var integer $id
     * @Column(name="id", type="integer",nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
    * @Column(type="string",length=200, nullable=true)
    */
    private $letter_signee;
    /**
    * @Column(type="string",length=200, nullable=true)
    */
    private $letter_signee2;
    /**
     * @Column(type="string",length=254, nullable=true)
     */
    private $circulation_size;
    /**
     * @Column(type="string",length=80, nullable=true)
     */
    private $frequency_of_mag;
    /**
     * @Column(type="boolean", nullable=true)
     */
    private $accept_advertising;
    /**
     * @Column(type="decimal", nullable=true)
     */
    private $advertising_rates;
    /**
     * @Column(type="date", nullable=true)
     */
    private $advertising_deadlines;
    /**
     * @Column(type="string", length=254, nullable=true)
     */
    private $workshop_names;
    /**
     * @Column(type="string", length=200, nullable=true)
     */
    private $workshop_type;
    /**
     * @Column(type="date", nullable=true)
     */
    private $workshop_dates;
    /**
     * @Column(type="string", length=200, nullable=true)
     */
    private $convention_name;
    /**
     * @Column(type="string", length=200, nullable=true)
     */
    private $convention_site;
    /**
     * @Column(type="date", nullable=true)
     */
    private $convention_date;
    /**
     * @Column(type="string", length=200, nullable=true)
     */
    private $pms_color_1;

    /**
     * @Column(type="string", length=200, nullable=true)
     */
    private $pms_color_2;

    /**
     * @Column(type="string", length=200, nullable=true)
     */
    private $pms_color_3;

    /**
     * @Column(type="string", length=200, nullable=true)
     */
    private $pms_color_4;

    /**
     * @Column(type="text", nullable=true)
     */
    private $notes;

    /**
     * @OneToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id",nullable=false)
     */
    private $user_id;
    /**
     * @Column(type="date", nullable=true)
     */
    private $commission_start_date;
    /**
     * @Column(type="text", nullable=true)
     */
    private $commission_per;
    /**
     * @var string $sharing
     *
     * @Column(name="sharing", type="string", nullable=true)
     */
    private $sharing;

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}
