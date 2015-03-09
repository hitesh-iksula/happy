<?php

class Iksula_Solidswatches_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action {

	// Loads Index Page
	public function IndexAction() {

		$this->loadLayout();
		$this->_setActiveMenu('explore/solidswatches/solidswatches_manage');
		$this->renderLayout();

	}

	// Save Action: Edit and Save for already existing entries and Add New Entries
	public function SaveAction() {

		$postData = $this->getRequest()->getParams();
		
		foreach($postData as $key=>$code) {

			if($key != 'key' AND $key != 'form_key') {

				$code = str_replace("#", "", $code);
				
				$allIds = explode("_", $key);
				$attributeId = $allIds[1];
				$optionId = $allIds[2];
				$entryToAdd = array(
					'attribute_id' => $attributeId,
					'option_id'    => $optionId,
					'color_1'      => $code
				);

				$swatchModel = Mage::getModel('solidswatches/solidswatches')
					->getCollection()
					->addFieldToFilter('option_id', array('eq' => $optionId))->getFirstItem();

				if($swatchModel->getData()) {
					$swatchModel->setData('color_1', $code);
					$swatchModel->save();
				} elseif(isset($code) AND $code != '') {
					$swatchModel = Mage::getModel('solidswatches/solidswatches');
					$swatchModel->addData($entryToAdd);
					$swatchModel->save();
				}

			}

		}

		$this->_redirect('*/*/index');
		
	}

}