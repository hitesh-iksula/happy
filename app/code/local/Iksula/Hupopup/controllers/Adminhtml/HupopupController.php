<?php

class Iksula_Hupopup_Adminhtml_HupopupController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("hupopup/hupopup")->_addBreadcrumb(Mage::helper("adminhtml")->__("Hupopup  Manager"),Mage::helper("adminhtml")->__("Hupopup Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Hupopup"));
			    $this->_title($this->__("Manager Hupopup"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Hupopup"));
				$this->_title($this->__("Hupopup"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("hupopup/hupopup")->load($id);
				if ($model->getId()) {
					Mage::register("hupopup_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("hupopup/hupopup");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Hupopup Manager"), Mage::helper("adminhtml")->__("Hupopup Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Hupopup Description"), Mage::helper("adminhtml")->__("Hupopup Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("hupopup/adminhtml_hupopup_edit"))->_addLeft($this->getLayout()->createBlock("hupopup/adminhtml_hupopup_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("hupopup")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Hupopup"));
		$this->_title($this->__("Hupopup"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("hupopup/hupopup")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("hupopup_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("hupopup/hupopup");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Hupopup Manager"), Mage::helper("adminhtml")->__("Hupopup Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Hupopup Description"), Mage::helper("adminhtml")->__("Hupopup Description"));


		$this->_addContent($this->getLayout()->createBlock("hupopup/adminhtml_hupopup_edit"))->_addLeft($this->getLayout()->createBlock("hupopup/adminhtml_hupopup_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("hupopup/hupopup")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Hupopup was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setHupopupData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setHupopupData($this->getRequest()->getPost());
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
						$model = Mage::getModel("hupopup/hupopup");
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
				$ids = $this->getRequest()->getPost('hu_popup_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("hupopup/hupopup");
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
			$fileName   = 'hupopup.csv';
			$grid       = $this->getLayout()->createBlock('hupopup/adminhtml_hupopup_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		}
}
