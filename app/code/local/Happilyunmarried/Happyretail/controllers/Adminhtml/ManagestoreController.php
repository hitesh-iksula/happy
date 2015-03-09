<?php

class Happilyunmarried_Happyretail_Adminhtml_ManagestoreController extends Mage_Adminhtml_Controller_Action {
    // Load form template
	public function indexAction() {
        $this->loadLayout()->renderLayout();
    }
	
	// Delete action
	public function deleteAction() {		
		// Create delete query
		$query = "DELETE FROM happily_unmarried_retail_multibrand_store WHERE store_id=".$this->getRequest()->id;
		
		// Execute query
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$db->query($query);
		$db->query("COMMIT");
		
		Mage::getSingleton('adminhtml/session')->addSuccess('Store deleted successfully');
		
		$this->_redirect('*/adminhtml_managestore');
	}
}