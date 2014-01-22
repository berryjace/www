<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * ClientArtwork
 *
 * @Table(name="client_artworks")
 * @Entity(repositoryClass="BL\Entity\Repository\ClientArtworkRepository")
 * @author Tanzim
 */
  class ClientArtwork
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
     * @Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @Column(name="file_url", type="string", length=255)
     */
    private $file_url;

    /**
     * @Column(name="file_extension", type="string", length=255)
     */
    private $file_extension;

    /**
     * @Column(name="description", type="text", length=800, nullable=true)
     */
    private $description;

    /**
     * @Column(type="datetime", nullable=true)
     */
    private $upload_date;
    
    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $User;
    
    public function __construct()
    {
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
