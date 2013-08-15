<?php
namespace BL\Entity;
/**
 *
 * @Table(name="permissions")
 * @Entity(repositoryClass="BL\Entity\Repository\PermissionRepository")
 * @author noman
 */
class Permission
{
    /**
     *
     * @var integer $id
     * @Column(name="id", type="integer",nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     **/
    private $id;

    /**
     *
     * @var resource_id
     * @ManyToOne(targetEntity="Resource")
     * @JoinColumns({@JoinColumn(name="resource_id", referencedColumnName="id")})
     */
    private $resource_id;

    /**
     * @Column(type="string",length=100,nullable=true)
     * @var string
     */
    private $permission;

    /**
     * @ManyToMany(targetEntity="Role", cascade={"persist,remove"})
     * @JoinTable(name="permissions_roles",
     *      joinColumns={@JoinColumn(name="permission_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     */
     private $roles;

    public function __construct() {
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
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