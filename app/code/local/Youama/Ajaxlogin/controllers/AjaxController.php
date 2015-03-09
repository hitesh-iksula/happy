<?php

/**
 * YouAMA.com
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled with this package
 * on http://youama.com/freemodule-license.txt.
 *
 /****************************************************************************
 *                      MAGENTO EDITION USAGE NOTICE                         *
 ****************************************************************************/
 /* This package designed for Magento Community edition. Developer(s) of
 * YouAMA.com does not guarantee correct work of this extension on any other
 * Magento edition except Magento Community edition. YouAMA.com does not 
 * provide extension support in case of incorrect edition usage.
 /****************************************************************************
 *                               DISCLAIMER                                  *
 ****************************************************************************/
 /* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *****************************************************
 * @category   Youama
 * @package    Youama_Ajaxlogin
 * @copyright  Copyright (c) 2012-2013 YouAMA.com (http://www.youama.com)
 * @license    http://youama.com/freemodule-license.txt
 */

class Youama_Ajaxlogin_AjaxController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {             
        if (isset($_POST['ajax']))
        {
            if ($_POST['ajax'] == 'login' && Mage::helper('customer')->isLoggedIn() != true)
            {
                $login = Mage::getSingleton('youama_ajaxlogin/ajaxlogin');
                echo $login->getResult();
            }
            elseif ($_POST['ajax'] == 'register' && Mage::helper('customer')->isLoggedIn() != true)
            {
                $register = Mage::getSingleton('youama_ajaxlogin/ajaxregister');
                echo $register->getResult();

                    $platform   = (string) $this->getRequest()->getPost('platforms');
                    $email      = (string) $this->getRequest()->getPost('email');
                    $register = Mage::getSingleton('youama_ajaxlogin/ajaxregister');
                    $collection = Mage::getModel('hupopup/hupopup')
                          ->getCollection()
                          ->addFieldToFilter('user_email_id', $params['ur-mail'])
                          ->getFirstItem();

                    $userMail = $collection->getData('user_email_id');

                    if($userMail == $email){
                                  //$msg = 'Email id already exists';
                    }else{
                        //echo "okk";exit;
                          $collection->setUsername('NA');
                          $collection->setUser_email_id($email);
                          $collection->setFriend_email_id('NA');
                          $collection->setVerify_link('NA');
                          $collection->setSource('Proper-Registeration');
                          $collection->setPlatform($platform);
                          $collection->setCreated_date(now());
                          $collection->save();
                   }
            }
        }
    }
    
    public function viewAction()
    {
        // 
    }

    public function forgotpasswordAction()
    {
        $session = Mage::getSingleton('customer/session');

        if ($session->isLoggedIn()) {
            return;
        }

        $email = $this->getRequest()->getPost('email');
        $result = array('success' => false);
        
        if (!$email)
        {
            $result['error'] = Mage::helper('core')->__('Please enter your email.');
        }
        else
        {
            if (!Zend_Validate::is($email, 'EmailAddress'))
            {
                $session->setForgottenEmail($email);
                $result['error'] = Mage::helper('core')->__('Invalid email address.');
            }
            else
            {
                $customer = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($email);
                if(!$customer->getId())
                {
                    $session->setForgottenEmail($email);
                    $result['error'] = Mage::helper('core')->__('This email address was not found in our records.');
                }
                else
                {
                    try
                    {
                        $new_pass = $customer->generatePassword();
                        $customer->changePassword($new_pass, false);
                        $customer->sendPasswordReminderEmail();
                        $result['success'] = true;
                        $result['message'] = Mage::helper('core')->__('A new password has been sent to your Email ID.');
                    }
                    catch (Exception $e)
                    {
                        $result['error'] = $e->getMessage();
                    }
                }
            }
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}

?>