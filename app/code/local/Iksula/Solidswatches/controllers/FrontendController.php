<?php

class Iksula_Solidswatches_FrontendController extends Mage_Core_Controller_Front_Action {

	/**
	 * 
	 * 
	 * This Function Gets Called When AJAX Request Is Made. Returns The URL of The Requested Option's Associated Image.
	 * In case an option is associated with >1 image, only the first one will be returned.
	 * 
	 * 
	 */
	public function RetrieveAction() {

		$productId = $this->getRequest()->getParam('product_id');
		$productId = str_replace('product_id_', '', $productId);

		$optionLabel = trim(strtolower($this->getRequest()->getParam('option_label')));

		$_product = Mage::getSingleton('catalog/product')->load($productId);
		$_images = $_product->getMediaGalleryImages();
		foreach($_images as $_image) {
			if(trim(strtolower($_image->getData('label'))) == $optionLabel) {
				echo Mage::helper('catalog/image')->init($_product, 'thumbnail', $_image->getFile())->resize(192); break;
			}
		}

	}

}
