<?php
//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * MasterTemplate
 *
 * @Table(name="master_templates")
 * @Entity(repositoryClass="BL\Entity\Repository\MasterTemplateRepository")
 * @author Sukhon
 */
class MasterTemplate
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
     * @Column(name="has_insurance", type="boolean", nullable=true)
     * @comments(null/0=without insurance, 1=with insurance, 2=both)
     */
    private $has_insurance;

    /**
     * @Column(name="create_date", type="datetime")
     */
    private $create_date;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user_id;
    
    public function __construct() {
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