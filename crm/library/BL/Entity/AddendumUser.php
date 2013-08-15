<?php
//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;


/**
 * @Table(name="addendums_users")
 * @Entity(repositoryClass="BL\Entity\Repository\AddendumUserRepository")
 * @author Sukhon
 */
class AddendumUser
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
     * @var integer $addendum_id
     * @ManyToOne(targetEntity="Addendum")
     * @JoinColumn(name="addendum_id", referencedColumnName="id", nullable=true)
     *
     */     
    private $addendum_id;

    /**
     * @var integer $user_id
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     *
     */    
    private $user_id;
    
    /**
     * @Column(name="is_agreed", type="boolean", nullable=true)
     */
    private $is_agreed;
            
    public function __construct() {
        $this->Addendum = new \Doctrine\Common\Collections\ArrayCollection();
        $this->User = new \Doctrine\Common\Collections\ArrayCollection();
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