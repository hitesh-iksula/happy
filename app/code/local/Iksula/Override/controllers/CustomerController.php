<?php
require_once 'Mage/Adminhtml/controllers/CustomerController.php';

class Iksula_Override_CustomerController extends Mage_Adminhtml_CustomerController
{

	/**
     * Create new customer action
     */
    public function newAction()
    {
        // echo "here"; exit;
        // Mage::register('creating_new_customer', 'yes');
        // echo Mage::registry('creating_new_customer'); exit;
        $this->_forward('edit');
    }

}