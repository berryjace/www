<?php

//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * LicenseTemplate
 *
 * @Table(name="license_templates")
 * @Entity(repositoryClass="BL\Entity\Repository\LicenseTemplateRepository")
 * @author Sukhon
 */
class LicenseTemplate
{
    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", length=8)
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(name="template", type="text")
     */
    private $template;

    /**
     * @Column(name="notes", type="text", length=1000, nullable=true)
     */
    private $notes;

    /**
     * @Column(name="create_date", type="datetime", length=20)
     */
    private $create_date;

    /**
     * @Column(name="has_insurance", type="boolean", nullable=true)
    */
    private $has_insurance;

    /**
     * @Column(name="user_id", type="integer", length=8)
     */
    private $user_id;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $client;

    public function __construct()
    {
        $this->client = new \Doctrine\Common\Collections\ArrayCollection();
        $this->create_date = new \DateTime(date("Y-m-d H:i:s"));
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