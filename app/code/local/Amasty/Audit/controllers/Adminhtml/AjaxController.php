<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2013 Amasty (http://www.amasty.com)
* @package Amasty_Audit 
*/
class Amasty_Audit_Adminhtml_AjaxController extends Mage_Adminhtml_Controller_Action
{
    public function ajaxAction() 
    {
        $idItem = Mage::app()->getRequest()->getParam('idItem');
        Mage::register('current_log', Mage::getModel('amaudit/log')->load($idItem));
        $block = $this->getLayout()->createBlock('amaudit/adminhtml_userlog_edit_tab_view_details');
        $this->getResponse()->setBody($block->toHtml());
    }

    public function unlockAction(){
        $admUsername = Mage::app()->getRequest()->getParam('admin_username', '');
        if($admUsername == '') {
            return false;
        }

        $user = Mage::getModel('admin/user')->loadByUsername($admUsername);
        $userLock = Mage::helper('amaudit')->getLockUser($user->getUserId());

        try
        {
            $userLock->setData('count', 0);
            $userLock->setData('time_lock', null);
            $userLock->save();

            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $write->beginTransaction();
            $condition = array(
                $write->quoteInto('username=?', $admUsername),
                $write->quoteInto('status=?', 2)
            );
            $write->delete('amasty_audit_data', $condition);
            $write->commit();
        }
        catch (Exception $e)
        {
            Mage::logException($e);
            Mage::log($e->getMessage());
        }
    }
}