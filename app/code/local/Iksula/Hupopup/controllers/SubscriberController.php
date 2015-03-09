<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Newsletter
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Newsletter subscribe controller
 *
 * @category    Mage
 * @package     Mage_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
require_once Mage::getModuleDir('controllers', 'Mage_Newsletter') . DS . 'SubscriberController.php';
class Iksula_Hupopup_SubscriberController extends Mage_Newsletter_SubscriberController
{
    /**
      * New subscription action
      */
    public function newAction()
    {
        //echo "override done";exit;
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $session            = Mage::getSingleton('core/session');
            $customerSession    = Mage::getSingleton('customer/session');
            $email              = (string) $this->getRequest()->getPost('email');
           
            $theme      = Mage::getSingleton('core/design_package')->getTheme('frontend');
            if($theme == 'default'){
               $platform = 'Desktop'; 
            }else{
               $platform = 'Mobile'; 
            }

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
                          $collection->setSource('Subscription Footer Form');
                          $collection->setPlatform($platform);
                          $collection->setCreated_date(now());
                          $collection->save();
                   }

            try {
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    Mage::throwException($this->__('Please enter a valid email address.'));
                }

                if (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 && 
                    !$customerSession->isLoggedIn()) {
                    Mage::throwException($this->__('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.', Mage::helper('customer')->getRegisterUrl()));
                }

                $ownerId = Mage::getModel('customer/customer')
                        ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                        ->loadByEmail($email)
                        ->getId();
                if ($ownerId !== null && $ownerId != $customerSession->getId()) {
                    Mage::throwException($this->__('This email address is already assigned to another user.'));
                }

                $status = Mage::getModel('newsletter/subscriber')->subscribe($email);
                if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
                    $session->addSuccess($this->__('Confirmation request has been sent.'));
                }
                else {
                    $session->addSuccess($this->__('Thank you for your subscription.'));
                }
            }
            catch (Mage_Core_Exception $e) {
                $session->addException($e, $this->__('There was a problem with the subscription: %s', $e->getMessage()));
            }
            catch (Exception $e) {
                $session->addException($e, $this->__('There was a problem with the subscription.'));
            }
        }
        $this->_redirectReferer();
    }

    public function orderflowAction()
    { 
        if ($_REQUEST['email']) {
            $email      = $_REQUEST['email'];
            $theme      = Mage::getSingleton('core/design_package')->getTheme('frontend');
            if($theme == 'default'){
               $platform = 'Desktop'; 
            }else{
               $platform = 'Mobile'; 
            }

            $collection = Mage::getModel('hupopup/hupopup')
                      ->getCollection()
                      ->addFieldToFilter('user_email_id', $email)
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
                          $collection->setSource('Order Flow - First Step');
                          $collection->setPlatform($platform);
                          $collection->setCreated_date(now());
                          $collection->save();
                   }
            }

    }


//     public function orderflowplacedAction()
//     { 
//         if ($_REQUEST['email']) {
//             $email      = $_REQUEST['email'];
//             $theme      = Mage::getSingleton('core/design_package')->getTheme('frontend');
//             if($theme == 'default'){
//                $platform = 'Desktop'; 
//             }else{
//                $platform = 'Mobile'; 
//             }

//             $collection = Mage::getModel('hupopup/hupopup')
//                       ->getCollection()
//                       ->addFieldToFilter('user_email_id', $params['ur-mail'])
//                       ->getFirstItem();

//             $userMail = $collection->getData('user_email_id');
// echo "first";
//             if($userMail == $email){
// echo "second";exit;
// // $id = $this->getRequest()->getParam('id');
// $id = 31;
// $data = array('username'=>'Order Placed');
// $model = Mage::getModel('hupopup/hupopup')->load($id)->addData($data);
// try {
//     $model->setId($id)->save();
//     echo "Data updated successfully.";

// } catch (Exception $e){
//     echo $e->getMessage(); 
// }

//             }else{
// echo "third";exit;

//                 //echo "okk";exit;
//                           $collection->setUsername('Order Placed');
//                           $collection->setUser_email_id($email);
//                           $collection->setFriend_email_id('NA');
//                           $collection->setVerify_link('NA');
//                           $collection->setSource('Order Flow');
//                           $collection->setPlatform($platform);
//                           $collection->setCreated_date(now());
//                           $collection->save();
//                    }
//             }

//     }


}
