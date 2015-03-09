<?php

class Iksula_Internationalshipping_Adminhtml_InternationalshippingController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("internationalshipping/internationalshipping")->_addBreadcrumb(Mage::helper("adminhtml")->__("Internationalshipping  Manager"),Mage::helper("adminhtml")->__("Internationalshipping Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Internationalshipping"));
			    $this->_title($this->__("Manager Internationalshipping"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Internationalshipping"));
				$this->_title($this->__("Internationalshipping"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("internationalshipping/internationalshipping")->load($id);
				if ($model->getId()) {
					Mage::register("internationalshipping_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("internationalshipping/internationalshipping");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Internationalshipping Manager"), Mage::helper("adminhtml")->__("Internationalshipping Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Internationalshipping Description"), Mage::helper("adminhtml")->__("Internationalshipping Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("internationalshipping/adminhtml_internationalshipping_edit"))->_addLeft($this->getLayout()->createBlock("internationalshipping/adminhtml_internationalshipping_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("internationalshipping")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Internationalshipping"));
		$this->_title($this->__("Internationalshipping"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("internationalshipping/internationalshipping")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("internationalshipping_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("internationalshipping/internationalshipping");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Internationalshipping Manager"), Mage::helper("adminhtml")->__("Internationalshipping Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Internationalshipping Description"), Mage::helper("adminhtml")->__("Internationalshipping Description"));


		$this->_addContent($this->getLayout()->createBlock("internationalshipping/adminhtml_internationalshipping_edit"))->_addLeft($this->getLayout()->createBlock("internationalshipping/adminhtml_internationalshipping_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {
						$model = Mage::getModel("internationalshipping/internationalshipping")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Internationalshipping was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setInternationalshippingData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setInternationalshippingData($this->getRequest()->getPost());
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
						$model = Mage::getModel("internationalshipping/internationalshipping");
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
				$ids = $this->getRequest()->getPost('ship_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("internationalshipping/internationalshipping");
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
			$fileName   = 'internationalshipping.csv';
			$grid       = $this->getLayout()->createBlock('internationalshipping/adminhtml_internationalshipping_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		}
}
