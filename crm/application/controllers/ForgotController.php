<?php

class ForgotController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
    }

    public function indexAction() {
        $form = new Application_Form_ForgotPassword();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $validator = new Zend_Validate_Db_RecordExists(array('table' => 'users', 'field' => 'email'));
                if (false === $validator->isValid($form->getValue('email'))) {
                    $form->getElement('email')->addError("Email was not found in the system");
                } else {
                    $user = $this->em->getRepository("BL\Entity\User")->findOneBy(array('email' => $form->getValue('email')));
                    /**
                     * Now we also don't want to send passwords whose accont is not active yet.
                     */
                    if ($user->reg_status == "activated") {
                        /**
                         * Let's reset the password and send it the user.
                         */
                        $new_password = $this->genPassword(8);
                        $user->password = md5($new_password);
                        $this->em->persist($user);
                        $this->em->flush();

                        $this->view->user = $user;
                        $this->view->new_password = $new_password;

                        $this->_helper->BUtilities->send_mail(array(
                            'to' => $user->email,
                            'to_name' => $user->organization_name,
                            'from' => 'noreply@greeklicensing.com',
                            'from_name' => 'Greek Licensing',
                            'subject' => 'Password Reset Request : Greek Licensing',
                            'body' => $this->view->render('emails/password-reset.phtml')
                        ));

                        $this->_helper->flashMessenger("Password Reset successful and was emailed!", "Info");
                        $this->_redirect('forgot/thank-you');
                    } else {
                        $form->getElement('email')->addError("Your account is not activated yet");
                    }
                }
            }
        }
    }

    /**
     * Function to Show up a thank you page after successful password reset
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function thankYouAction() {
        // Contents only in view
    }

    private function genPassword($length) {

        srand((double) microtime() * 1000000);

        $vowels = array("a", "e", "i", "o", "u");
        $cons = array("b", "c", "d", "g", "h", "j", "k", "l", "m", "n", "p", "r", "s", "t", "u", "v", "w", "tr",
            "cr", "br", "fr", "th", "dr", "ch", "ph", "wr", "st", "sp", "sw", "pr", "sl", "cl");

        $num_vowels = count($vowels);
        $num_cons = count($cons);
        $password = "";

        for ($i = 0; $i < $length; $i++) {
            $password .= $cons[rand(0, $num_cons - 1)] . $vowels[rand(0, $num_vowels - 1)];
        }

        return substr($password, 0, $length);
    }

}

