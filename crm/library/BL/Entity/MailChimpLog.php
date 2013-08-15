<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * MailChimpLog
 *
 * @Table(name="mail_chimp_log")
 * @Entity(repositoryClass="BL\Entity\Repository\MailChimpLogRepository")
 * @author Tanzim
 */
class MailChimpLog
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
     * @var integer $notification_id
     *
     * @Column(name="notification_id", type="integer", length=8)
     */
    private $notification_id;

    /**
     * @var datetime $mail_chimp_list_id
     *
     * @Column(name="mail_chimp_list_id", type="datetime")
     */
    private $mail_chimp_list_id;

    /**
     * @var Notification
     *
     * @OneToMany(targetEntity="Notification", mappedBy="NotificationMailLog")
     * @JoinColumns({
     *   @JoinColumn(name="notification_id", referencedColumnName="id", onDelete=true)
     * })
     */
    private $NotificationMail;

    /**
     * @var MailChimpList
     *
     * @OneToMany(targetEntity="MailChimpList", mappedBy="MailLog")
     * @JoinColumns({
     *   @JoinColumn(name="mail_chimp_list_id", referencedColumnName="id", onDelete=true)
     * })
     */
    private $MailList;

    public function __construct()
    {
        $this->NotificationMail = new \Doctrine\Common\Collections\ArrayCollection();
    $this->MailList = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
}