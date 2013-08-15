<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * UserCardInfo
 *
 * @Table(name="user_card_info")
 * @Entity(repositoryClass="BL\Entity\Repository\UserCardInfoRepository")
 * @author Tanzim
 */
class UserCardInfo
{
    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", length=8)
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var integer $card_number
     *
     * @Column(name="card_number", type="integer", length=4)
     */
    private $card_number;

    /**
     * @var integer $user_id
     *
     * @Column(name="user_id", type="integer", length=8)
     */
    private $user_id;

    /**
     * @OneToMany(targetEntity="User",mappedBy="UserCardInfo")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="id", onDelete=true)
     * })
     */
    private $User;

    public function __construct()
    {
        $this->User = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
}