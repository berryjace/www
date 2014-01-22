<?php
//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * Addendum
 *
 * @Table(name="addendums")
 * @Entity(repositoryClass="BL\Entity\Repository\AddendumRepository")
 * @author Sukhon
 */
class Addendum
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
     * @Column(name="reason", type="string", length=250)
     */
    private $reason;

    /**
     * @Column(name="content", type="text")
     */
    private $content;

    /**
     * @Column(name="is_draft", type="boolean")
     */
    private $is_draft;

    /**
     * @Column(name="create_date", type="datetime")
     */
    private $create_date;    
    
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