<?php

class Happilyunmarried_Happyhome_Adminhtml_HomepageController extends Mage_Adminhtml_Controller_Action {
    // Load form template
	public function indexAction() {
        $this->loadLayout()->renderLayout();
    }
    
	// Function to handle POST action on the form
    public function postAction() {
        $post = $this->getRequest()->getPost();
		try {
            if (empty($post)) {
                Mage::throwException($this->__('Invalid form data.'));
            }
            
            // Get our posted values
			$values = $post['homepage'];
			
			// Check for file uploads
			$image_fields = array('col-1-image-1', 'col-1-image-2', 'col-1-image-3', 'col-2-image-1', 'col-2-image-2', 'col-2-image-3', 'col-3-image-1', 'col-3-image-2', 'col-3-image-3', 'bottom-col-1-image', 'bottom-col-2-image', 'bottom-col-3-image');
			
			foreach($image_fields as $image_field) {
				// Verify if image field exists
				if(isset($_FILES[$image_field])) {
					// File was uploaded
					if($_FILES[$image_field]['name'] != '') {
						// Upload file
						try {    
							 $uploader = new Varien_File_Uploader($image_field);
							 $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
							 $uploader->setAllowRenameFiles(true);
							 $uploader->setFilesDispersion(false);
						 
							 $path = Mage::getBaseDir('media') . DS . 'happily_unmarried' . DS . 'homepage' . DS;
									
							 $uploader->save($path, $_FILES['thumbnail']['name']);
							 
							 // File uploaded, add it to values
							 $values[$image_field] = Mage::getBaseUrl('media').'happily_unmarried/homepage/'.$uploader->getUploadedFileName();
						} catch (Exception $e) {
							  //die('Error in thumbnail upload: '.$e);
						}
					}
				}
			}
            
            // Create query
			$query = "UPDATE happily_unmarried_homepage SET ";
			foreach($values as $key => $value) {
				$query .= str_replace('-', '_', $key)." = '".addslashes($value)."', ";
			}
			$query = rtrim($query, ', ').", updated_at=NOW() WHERE id = 1";
			
			//die($query);
			
			// Insert values into database
			$db = Mage::getSingleton('core/resource')->getConnection('core_write');
			$db->query($query);
			$db->query("COMMIT");
			
			// Set success message
			$message = 'Home page updated successfully';
            Mage::getSingleton('adminhtml/session')->addSuccess($message);
			
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
		
		// Redirect
        $this->_redirect('*/*');
    }
}