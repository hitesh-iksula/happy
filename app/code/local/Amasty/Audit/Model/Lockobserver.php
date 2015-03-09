<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2013 Amasty (http://www.amasty.com)
* @package Amasty_Audit
  
Observer for Login Attempts code
*/
class Amasty_Audit_Model_Lockobserver
{
    private $_userData = array();    
    
    //listen admin_session_user_login_success event
    public function onAdminSessionUserLoginSuccess($observer)   
    {
        if(Mage::getStoreConfig('amaudit/general/enableLock')){
            $this->_userData['username'] = $observer->getUser()->getUsername();
            $this->_userData['date_time'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
            $this->_userData['name'] = $observer->getUser()->getFirstname() . ' ' . $observer->getUser()->getLastname();
            if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
		        $this->_userData['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
	        }
            else{
		        $this->_userData['ip'] = $_SERVER['REMOTE_ADDR'];
            }
            $this->_userData['status'] = 1;        

            $user = Mage::getModel('admin/user')->loadByUsername($observer->getUser()->getUsername());
            $userLock = Mage::helper('amaudit')->getLockUser($user->getUserId());
            if($userLock){
                $time = intval($userLock->getTimeLock());
                $lockStatus = true;
                if($userLock->getCount() >= Mage::getStoreConfig('amaudit/general/numberFailed') && $time){
                    $locktime = time() - $time;
                    if( $locktime <= Mage::getStoreConfig('amaudit/general/time') || Mage::getStoreConfig('amaudit/general/time') == 0 || Mage::getStoreConfig('amaudit/general/time') == ""){
                        $auth = Mage::getSingleton('admin/session')->unsetAll();
                        Mage::getSingleton('adminhtml/session')->unsetAll();
                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Invalid Username or Password.'));
                        $this->_userData['status'] = 2;
                        $lockStatus = false;
                    }
                }
                if($lockStatus){
                    try
                    {
                         $userLock->setData('count', 0);
                         $userLock->setData('time_lock', null);
                         $userLock->save();
                    }
                    catch (Exception $e)
                    {
                       Mage::logException($e);
                       Mage::log($e->getMessage());
                    }
                }

                if(Mage::getStoreConfig('amaudit/log_mailing/enabled') && !($userLock->getCount() >= Mage::getStoreConfig('amaudit/general/numberFailed') && $time)){
                    $translate = Mage::getSingleton('core/translate');
                    $translate->setTranslateInline(false);
                    $store = Mage::app()->getStore();

                    $vars = array(
                        'username' => $this->_userData['username'],
                        'curr_ip' => $this->_userData['ip'],
                        'date_n_time' => $this->_userData['date_time'],
                        'fullname' => $this->_userData['name']
                    );

                    $sender = array(
                        'name' => Mage::getStoreConfig('trans_email/ident_general/name', $store->getId()),
                        'email' => Mage::getStoreConfig('trans_email/ident_general/email', $store->getId()),
                    );
                    $receiveEmail = Mage::getStoreConfig('amaudit/log_mailing/send_to_mail');
                    $receiveName = Mage::getStoreConfig('amaudit/log_mailing/send_to_mail');

                    $tpl = Mage::getModel('core/email_template');
                    $tpl->setDesignConfig(array('area'=>'frontend', 'store'=>$store->getId()))
                        ->sendTransactional(
                            Mage::getStoreConfig('amaudit/log_mailing/template'),
                            $sender,
                            $receiveEmail,
                            $receiveName,
                            $vars
                        );

                    $translate->setTranslateInline(true);
                }
                /* */
            }
            $this->saveAudit();
        }
    } 
    
    //listen admin_session_user_login_failed event 
    public function onAdminSessionUserLoginFailed($observer)  
    {
        Mage::app()->getResponse()->setHttpResponseCode(403);
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $row = $read->fetchRow("SELECT firstname, lastname FROM admin_user WHERE username = '" . $observer->getUserName() . "'");

        $this->_userData['username'] = $observer->getUserName();
        $this->_userData['date_time'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
        $this->_userData['name'] = $row['firstname'] . ' ' . $row['lastname'];
        $this->_userData['ip'] = $_SERVER['REMOTE_ADDR'];
        $this->_userData['status'] = 0;
        
        if(Mage::getStoreConfig('amaudit/general/enableLock')){
             $user = Mage::getModel('admin/user')->loadByUsername($observer -> getUserName());
             if($user && 0 != $user->getUserId()){
                 $this->saveLock($user->getUserId()); 
             }
        }
        $this->saveAudit();
    }
    
    private function saveAudit()  
    {
        try
        {
             $dataModel = Mage::getModel('amaudit/data');
             $dataModel->setData($this->_userData);
             $dataModel->save();   
        }
        catch (Exception $e) 
        {
             Mage::logException($e);
             Mage::log($e->getMessage());
        }
    } 
    
    private function saveLock($userId)  
    {
        try
        {
             $lockModel = Mage::getModel('amaudit/lock');
             $collection = $lockModel->getCollection();
             $count = 0;
             foreach($collection as $item){
                 if($userId == $item->getUserId()){
                     $count = $item->getCount();
                     $user = Mage::getModel('amaudit/lock')->load($item->getEntityId());
                     if($user) {
                        if($count >= Mage::getStoreConfig('amaudit/general/numberFailed') - 1) {
                            $user->setData('time_lock', time());
                        }
                        $count++;
                        $count = $count >  Mage::getStoreConfig('amaudit/general/numberFailed') ? Mage::getStoreConfig('amaudit/general/numberFailed') : $count; 
                        $user->setData('count', $count); 
                        $user->save();      
                     }
                     break;
                 }
             }
             if($count == 0){
                 $count++;
                 $lockModel->setData('user_id', $userId);
                 $lockModel->setData('count', $count);
                 $lockModel->save();      
             }
        }
        catch (Exception $e) 
        {
            Mage::logException($e);
            Mage::log($e->getMessage());
        }
    }
}