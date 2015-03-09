<?php

class Manoj_Pincode_Adminhtml_PincodeController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("pincode/pincode")->_addBreadcrumb(Mage::helper("adminhtml")->__("Pincode  Manager"),Mage::helper("adminhtml")->__("Pincode Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Pincode"));
			    $this->_title($this->__("Manager Pincode"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Pincode"));
				$this->_title($this->__("Pincode"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("pincode/pincode")->load($id);
				if ($model->getId()) {
					Mage::register("pincode_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("pincode/pincode");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Pincode Manager"), Mage::helper("adminhtml")->__("Pincode Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Pincode Description"), Mage::helper("adminhtml")->__("Pincode Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("pincode/adminhtml_pincode_edit"))->_addLeft($this->getLayout()->createBlock("pincode/adminhtml_pincode_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("pincode")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Pincode"));
		$this->_title($this->__("Pincode"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("pincode/pincode")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("pincode_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("pincode/pincode");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Pincode Manager"), Mage::helper("adminhtml")->__("Pincode Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Pincode Description"), Mage::helper("adminhtml")->__("Pincode Description"));


		$this->_addContent($this->getLayout()->createBlock("pincode/adminhtml_pincode_edit"))->_addLeft($this->getLayout()->createBlock("pincode/adminhtml_pincode_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{
			$post_data=$this->getRequest()->getPost();
			// print_r($post_data);exit;
			if ($post_data) {

				try {
					try{
						if((bool)$post_data['fileupload']['delete']==1) {
							$post_data['fileupload']='';
						}
						else {
							unset($post_data['fileupload']);
							if (isset($_FILES)){

								if ($_FILES['fileupload']['name']) {

									if($this->getRequest()->getParam("id")){
										$model = Mage::getModel("pincode/pincode")->load($this->getRequest()->getParam("id"));
										if($model->getData('fileupload')){
											$io = new Varien_Io_File();
											$io->rm(Mage::getBaseDir('media').DS.implode(DS,explode('/',$model->getData('fileupload'))));	
										}
									}
									$path = Mage::getBaseDir('media') . DS . 'pincode' . DS .'pincode'.DS;
									$uploader = new Varien_File_Uploader('fileupload');
									$uploader->setAllowedExtensions(array('jpg','png','gif','csv'));
									$uploader->setAllowRenameFiles(false);
									$uploader->setFilesDispersion(false);
									$destFile = $path.$_FILES['fileupload']['name'];
									$filename = $uploader->getNewFileName($destFile);
									$uploader->save($path, $filename);
									$post_data['fileupload']='pincode/pincode/'.$filename;
									$csv = new Varien_File_Csv();
									$dataCollection = $csv->getData($path.$_FILES['fileupload']['name']); //path to csv
									array_shift($dataCollection);

								}
							}
						}

					} 
					catch (Exception $e) {
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
						return;
					}

				$model_pincode = Mage::getModel("pincode/pincode");
				foreach($dataCollection as $_data){

					$new_data['pincode'] = $_data[1];
					$new_data['area'] = $_data[2];
					$new_data['location'] = $_data[3];
					$new_data['city'] = $_data[4];
					$new_data['state'] = $_data[5];
					$new_data['code'] = $_data[6];
					$new_data['zone'] = $_data[7];
					$new_data['cod'] = $_data[8];
					$new_data['prepaid'] = $_data[9];
					$new_data['status'] = $_data[10];
					$model_pincode->setData($new_data);
					$model_pincode->save();
				}
				$new_post_data = array();
				if($this->getRequest()->getParam("pincode")!=''){
					// print_r($post_data['']);
					
					$new_post_data['pincode'] = $post_data['pincode'];
					$new_post_data['area'] = $post_data['area'];
					$new_post_data['location'] = $post_data['location'];
					$new_post_data['city'] = $post_data['city'];
					$new_post_data['state'] = $post_data['state'];
					$new_post_data['code'] = $post_data['code'];
					$new_post_data['zone'] = $post_data['zone'];
					$new_post_data['cod'] = implode(',', $post_data['cod']);
					$new_post_data['prepaid'] = implode(',', $post_data['prepaid']);
					$new_post_data['status'] = $post_data['status'];
					// print_r($new_post_data);
					// exit;
						$model = Mage::getModel("pincode/pincode")
						->addData($new_post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();
				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Pincode was successfully saved"));
				Mage::getSingleton("adminhtml/session")->setPincodeData(false);

				if ($this->getRequest()->getParam("back")) {
					$this->_redirect("*/*/edit", array("id" => $model->getId()));
					return;
				}
				$this->_redirect("*/*/");
				return;
		} 
		catch (Exception $e) {
			Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			Mage::getSingleton("adminhtml/session")->setPincodeData($this->getRequest()->getPost());
			$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
			return;
		}

	}
				$this->_redirect("*/*/");
		}




		public function deleteAction()
		{
				if( $this->getRequest()->getParam("id") > 0 ) {
					try {
						$model = Mage::getModel("pincode/pincode");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
						$this->_redirect("*/*/");
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					}
				}
				$this->_redirect("*/*/");
		}

		
		public function massRemoveAction()
		{
			try {
				$ids = $this->getRequest()->getPost('ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("pincode/pincode");
					  $model->setId($id)->delete();
				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}
			
		/**
		 * Export order grid to CSV format
		 */
		public function exportCsvAction()
		{
			$fileName   = 'pincode.csv';
			$grid       = $this->getLayout()->createBlock('pincode/adminhtml_pincode_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'pincode.xml';
			$grid       = $this->getLayout()->createBlock('pincode/adminhtml_pincode_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
