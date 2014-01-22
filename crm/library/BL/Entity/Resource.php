<?php
namespace BL\Entity;
/**
 *
 * @Table(name="resources")
 * @Entity(repositoryClass="BL\Entity\Repository\ResourceRepository")
 * @author noman
 */
class Resource
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
     * @Column(type="string",length=100,nullable=true)
     */
    private $resource;


    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property,$value)
    {
        $this->$property = $value;
    }

}