<?php

class Iksula_Storelocator_Adminhtml_StateController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("storelocator/state")->_addBreadcrumb(Mage::helper("adminhtml")->__("State  Manager"),Mage::helper("adminhtml")->__("State Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Storelocator"));
			    $this->_title($this->__("Manager State"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Storelocator"));
				$this->_title($this->__("State"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("storelocator/state")->load($id);
				if ($model->getId()) {
					Mage::register("state_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("storelocator/state");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("State Manager"), Mage::helper("adminhtml")->__("State Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("State Description"), Mage::helper("adminhtml")->__("State Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("storelocator/adminhtml_state_edit"))->_addLeft($this->getLayout()->createBlock("storelocator/adminhtml_state_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("storelocator")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Storelocator"));
		$this->_title($this->__("State"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("storelocator/state")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("state_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("storelocator/state");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("State Manager"), Mage::helper("adminhtml")->__("State Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("State Description"), Mage::helper("adminhtml")->__("State Description"));


		$this->_addContent($this->getLayout()->createBlock("storelocator/adminhtml_state_edit"))->_addLeft($this->getLayout()->createBlock("storelocator/adminhtml_state_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("storelocator/state")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("State was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setStateData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setStateData($this->getRequest()->getPost());
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
						$model = Mage::getModel("storelocator/state");
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
				$ids = $this->getRequest()->getPost('state_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("storelocator/state");
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
			$fileName   = 'state.csv';
			$grid       = $this->getLayout()->createBlock('storelocator/adminhtml_state_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'state.xml';
			$grid       = $this->getLayout()->createBlock('storelocator/adminhtml_state_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
