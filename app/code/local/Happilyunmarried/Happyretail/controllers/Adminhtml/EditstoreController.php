<?php

class Happilyunmarried_Happyretail_Adminhtml_EditstoreController extends Mage_Adminhtml_Controller_Action {
    // Load form template
	public function indexAction() {
        $this->loadLayout()->renderLayout();
    }
    
	// Function to handle POST action on the form
    public function updateAction() {
        $post = $this->getRequest()->getPost();
		try {
            if (empty($post)) {
                Mage::throwException($this->__('Invalid form data.'));
            }
            
            // Get our posted values
			$values = $post['retailstore'];
			$id = $post['store_id'];
            
            // Create query
			$query = "UPDATE happily_unmarried_retail_multibrand_store SET ";
			foreach($values as $key => $value) {
				$query .= str_replace('-', '_', $key)." = '".addslashes($value)."', ";
			}
			$query = rtrim($query, ', ')." WHERE store_id=".$id;
			
			// Insert values into database
			$db = Mage::getSingleton('core/resource')->getConnection('core_write');
			$db->query($query);
			$db->query("COMMIT");
			
			// Set success message
			$message = 'Store updates successfully';
            Mage::getSingleton('adminhtml/session')->addSuccess($message);
			
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
		
		// Redirect
        $this->_redirect('*/adminhtml_editstore', array('id' => $id));
    }
}