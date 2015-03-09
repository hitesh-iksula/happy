<?php

class Iksula_Xxxmas_Adminhtml_BannerController extends Mage_Adminhtml_Controller_Action
{

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
            
				// Check for file uploads
				if(isset($_FILES['banner_image'])) {
					// File was uploaded
					if($_FILES['banner_image']['name'] != '') {
						// Upload file
						try {    
							 $uploader = new Varien_File_Uploader($_FILES['banner_image']);
							 $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
							 $uploader->setAllowRenameFiles(true);
							 $uploader->setFilesDispersion(false);
						 
							 $path = Mage::getBaseDir('media') . DS . 'happily_unmarried' . DS . 'homepage' . DS;
									
							 $uploader->save($path,$_FILES['banner_image']['name']);
							 
							 // File uploaded, add it to values
							 $image_banner = Mage::getBaseUrl('media').'happily_unmarried/homepage/'.$uploader->getUploadedFileName();
						} catch (Exception $e) {
							  //die('Error in thumbnail upload: '.$e);
						}
					}
				}

            exit;
            // Create query
			$query = "UPDATE xxxmas_banner SET  banner_image=".$image_banner." WHERE banner_id = 1";
			
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
