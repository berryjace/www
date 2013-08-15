<?php
/**
 * Adapter class
 *
 * @author blueliner
 */
class BL_Auth_Adapter implements Zend_Auth_Adapter_Interface
{
    /**
     *
     * @var <type>
     */
    protected $user;
    /**
     *
     * @var string
     */
    protected $username = "";
    /**
     *
     * @var string
     */
    protected $password = "";

    public function  __construct($username, $password, $direct_login=false)
    {
        $this->username = $username;
        $this->password = $password;
        $this->direct_login = $direct_login;
    }

    public function  authenticate()
    {

        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $em = $this->doctrineContainer->getEntityManager();
        try {
            if($this->direct_login){
                $this->user = $em->getRepository("BL\Entity\User")->_authenticateUserByUsername($this->username);
            } else {
                $this->user = $em->getRepository("BL\Entity\User")->_authenticateUser($this->username, $this->password);
            }
        } catch(Exception $e){
            if($e->getMessage() == 1){
                return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $this->user, $messages=array());
            } else {
                return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $this->user, $messages=array());
            }
        }
        return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $this->user, $messages=array());
    }
}
?>
