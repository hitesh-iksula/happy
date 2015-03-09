<?php

class Iksula_Suggest_Adminhtml_SuggestController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction()
	{
		$this->loadLayout()->_setActiveMenu("suggest/suggest")->_addBreadcrumb(Mage::helper("adminhtml")->__("Suggest Manager"),Mage::helper("adminhtml")->__("Suggest Manager"));
		return $this;
	}
	public function indexAction() 
	{
		$this->_initAction();
		$this->renderLayout();
	}
	public function editAction()
	{
		$brandsId = $this->getRequest()->getParam("id");
		$brandsModel = Mage::getModel("suggest/suggest")->load($brandsId);
		if ($brandsModel->getId() || $brandsId == 0) {
			Mage::register("suggest_data", $brandsModel);
			$this->loadLayout();
			$this->_setActiveMenu("suggest/suggest");
			$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Suggest Manager"), Mage::helper("adminhtml")->__("Suggest Manager"));
			$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Suggest Description"), Mage::helper("adminhtml")->__("Suggest Description"));
			$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock("suggest/adminhtml_suggest_edit"))->_addLeft($this->getLayout()->createBlock("suggest/adminhtml_suggest_edit_tabs"));
			$this->renderLayout();
		} 
		else {
			Mage::getSingleton("adminhtml/session")->addError(Mage::helper("suggest")->__("Item does not exist."));
			$this->_redirect("*/*/");
		}
	}

	public function newAction()
	{

		$id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("suggest/suggest")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("suggest_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("suggest/suggest");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Suggest Manager"), Mage::helper("adminhtml")->__("Suggest Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Suggest Description"), Mage::helper("adminhtml")->__("Suggest Description"));


		$this->_addContent($this->getLayout()->createBlock("suggest/adminhtml_suggest_edit"))->_addLeft($this->getLayout()->createBlock("suggest/adminhtml_suggest_edit_tabs"));

		$this->renderLayout();

		// $this->_forward("edit");
	}
	public function saveAction()
	{

		$post_data=$this->getRequest()->getPost();

		if ($post_data) {

			try {

				$brandsModel = Mage::getModel("suggest/suggest")
				->addData($post_data)
				->setId($this->getRequest()->getParam("id"))
				->save();

				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Vendor Information was successfully saved"));
				Mage::getSingleton("adminhtml/session")->setSuggestData(false);

				if ($this->getRequest()->getParam("back")) {
					$this->_redirect("*/*/edit", array("id" => $brandsModel->getId()));
					return;
				}
				$this->_redirect("*/*/");
				return;
			} 
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
				Mage::getSingleton("adminhtml/session")->setSuggestData($this->getRequest()->getPost());
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
				$brandsModel = Mage::getModel("suggest/suggest");
				$brandsModel->setId($this->getRequest()->getParam("id"))->delete();
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
	
	public function massDeleteAction()
	{
		$deleteIds = $this->getRequest()->getParam('delete_id');      // $this->getMassactionBlock()->setFormFieldName('tax_id'); from Mage_Adminhtml_Block_Tax_Rate_Grid
		if(!is_array($deleteIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('suggest')->__('Please select Vendor Entries'));
		} else {
			try {
				$rateModel = Mage::getModel('suggest/suggest');
				foreach ($deleteIds as $deleteId) {
				$rateModel->load($deleteId)->delete();
			}
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('suggest')->__('Total of %d record(s) were deleted.', count($deleteIds)));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
	
	public function tofinancestatusAction()
	{
		$orderId = $this->getRequest()->getParam('order_id');
		// echo "in suggest controller " . $orderId; exit;
		$order = Mage::getModel('sales/order')->load($orderId);
		// echo "<pre>"; print_r($order->getData()); exit;
		$order->setData('state', "new");
		$order->setStatus("pending_to_finance");
		$history = $order->addStatusHistoryComment('Order transferred to Finance Department for Approval', false);
		$history->setIsCustomerNotified(false);
		$order->save();
		// $path = Mage::getModel('adminhtml/url')->getUrl('adminhtml/sales_order/index');
		$this->_redirect('adminhtml/sales_order/index');
	}
	
	public function tomanagementstatusAction()
	{
		$orderId = $this->getRequest()->getParam('order_id');
		// echo "in suggest controller " . $orderId; exit;
		$order = Mage::getModel('sales/order')->load($orderId);
		// echo "<pre>"; print_r($order->getData()); exit;
		$order->setData('state', "new");
		$order->setStatus("pending_to_management");
		$history = $order->addStatusHistoryComment('Order transferred to Management Department for Approval', false);
		$history->setIsCustomerNotified(false);
		$order->save();
		// $path = Mage::getModel('adminhtml/url')->getUrl('adminhtml/sales_order/index');
		$this->_redirect('adminhtml/sales_order/index');
	}
	
	public function toprocessingstatusAction ()
	{
		$orderId = $this->getRequest()->getParam('order_id');
		// echo "in suggest controller " . $orderId; exit;
		$order = Mage::getModel('sales/order')->load($orderId);
		// echo "<pre>"; print_r($order->getData()); exit;
		$order->setData('state', "processing");
		$order->setStatus("b2b_processing");
		$history = $order->addStatusHistoryComment('Vendor NEFT Approved, Order is in Processing state', false);
		$history->setIsCustomerNotified(false);
		$order->save();
		// $path = Mage::getModel('adminhtml/url')->getUrl('adminhtml/sales_order/index');
		$this->_redirect('adminhtml/sales_order/index');
	}
	
	public function toprocessingwitharrearsstatusAction ()
	{
		$orderId = $this->getRequest()->getParam('order_id');
		// echo "in suggest controller " . $orderId; exit;
		$order = Mage::getModel('sales/order')->load($orderId);
		// echo "<pre>"; print_r($order->getData()); exit;
		$order->setData('state', "processing");
		$order->setStatus("processing_with_arrears");
		$history = $order->addStatusHistoryComment('Order has been Approved, Processing with Arrears', false);
		$history->setIsCustomerNotified(false);
		$order->save();
		// $path = Mage::getModel('adminhtml/url')->getUrl('adminhtml/sales_order/index');
		$this->_redirect('adminhtml/sales_order/index');
	}

	public function vendorpaymentreceivedAction()
	{
		$orderId = $this->getRequest()->getParam('order_id');
		// echo "in suggest controller " . $orderId; exit;
		$order = Mage::getModel('sales/order')->load($orderId);
		// echo "<pre>"; print_r($order->getData()); exit;
		$order->setData('state', "complete");
		$order->setStatus("b2b_complete");
		$history = $order->addStatusHistoryComment('Payment Received in Full by the Vendor. Order Complete', false);
		$history->setIsCustomerNotified(false);
		$order->save();
		// $path = Mage::getModel('adminhtml/url')->getUrl('adminhtml/sales_order/index');
		$this->_redirect('adminhtml/sales_order/index');
	}

	public function vendorpaymentpendingsetAction()
	{
		$orderId = $this->getRequest()->getParam('order_id');
		// echo "in suggest controller " . $orderId; exit;
		$order = Mage::getModel('sales/order')->load($orderId);
		// echo "<pre>"; print_r($order->getData()); exit;
		$order->setData('state', "pending_payment");
		$order->setStatus("vendor_payment_due");
		$history = $order->addStatusHistoryComment('Shipment Created for Vendor, Payment Pending', false);
		$history->setIsCustomerNotified(false);
		$order->save();
		// $path = Mage::getModel('adminhtml/url')->getUrl('adminhtml/sales_order/index');
		$this->_redirect('adminhtml/sales_order/index');
	}
}
