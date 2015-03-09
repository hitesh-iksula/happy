<?php
require_once 'Mage/Adminhtml/controllers/Sales/Order/ShipmentController.php';

class Iksula_Override_Sales_Order_ShipmentController extends Mage_Adminhtml_Sales_Order_ShipmentController {

	public function getPreviousOrderStatus($orderId)
	{
		$order = Mage::getModel('sales/order')->load($orderId);
        $status = $order->getData('status');
		
		return $status;
	}
	
	public function setCustomOrderStatus($orderId, $previousStatus)
	{
		$order = Mage::getModel('sales/order')->load($orderId);
        $status = $order->getData('status');
		$customerGroupId = $order->getData('customer_group_id');
		
		if ($customerGroupId == '6' OR $customerGroupId == '7')
		{
			if ($previousStatus == 'processing_with_arrears')
			{
				$order->setData('state', "pending_payment");
				$order->setStatus("vendor_payment_due");
				$history = $order->addStatusHistoryComment('Shipment Created for Vendor, Vendor Payment is Pending', false);
				$history->setIsCustomerNotified(false);
				$order->save();
			}
			if ($previousStatus == 'processing')
			{
				$order->setData('state', "complete");
				$order->setStatus("complete");
				$history = $order->addStatusHistoryComment('Shipment Created for Vendor, Vendor Payment received via NEFT', false);
				$history->setIsCustomerNotified(false);
				$order->save();
			}
		}
	}
	
	/**
     * Save shipment
     * We can save only new shipment. Existing shipments are not editable
     *
     * @return null
     */
    public function saveAction()
    {
		$data = $this->getRequest()->getPost('shipment');
        if (!empty($data['comment_text'])) {
            Mage::getSingleton('adminhtml/session')->setCommentText($data['comment_text']);
        }
        
		// print_r($data);
		// echo "working"; exit;

        try {
            $shipment = $this->_initShipment();
            if (!$shipment) {
                $this->_forward('noRoute');
                return;
            }
			
			$shipment->register();
            $comment = '';
            if (!empty($data['comment_text'])) {
                $shipment->addComment(
                    $data['comment_text'],
                    isset($data['comment_customer_notify']),
                    isset($data['is_visible_on_front'])
                );
                if (isset($data['comment_customer_notify'])) {
                    $comment = $data['comment_text'];
                }
            }

            if (!empty($data['send_email'])) {
                $shipment->setEmailSent(true);
            }
			
			$shipment->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
            $responseAjax = new Varien_Object();
            $isNeedCreateLabel = isset($data['create_shipping_label']) && $data['create_shipping_label'];
            if ($isNeedCreateLabel) {
                if ($this->_createShippingLabel($shipment)) {
                    $this->_getSession()
                        ->addSuccess($this->__('The shipment has been created. The shipping label has been created.'));
                    $responseAjax->setOk(true);
                }
            } else {
                $this->_getSession()
                    ->addSuccess($this->__('The shipment has been created.'));
            }
			
			$orderIda = $shipment->getData('order_id');
			$previousStatus = $this->getPreviousOrderStatus($orderIda);
			
            $this->_saveShipment($shipment);
			
			$this->setCustomOrderStatus($orderIda, $previousStatus);
			
            $shipment->sendEmail(!empty($data['send_email']), $comment);
            Mage::getSingleton('adminhtml/session')->getCommentText(true);
        } catch (Mage_Core_Exception $e) {
            if ($isNeedCreateLabel) {
                $responseAjax->setError(true);
                $responseAjax->setMessage($e->getMessage());
            } else {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/new', array('order_id' => $this->getRequest()->getParam('order_id')));
            }
        } catch (Exception $e) {
            Mage::logException($e);
            if ($isNeedCreateLabel) {
                $responseAjax->setError(true);
                $responseAjax->setMessage(Mage::helper('sales')->__('An error occurred while creating shipping label.'));
            } else {
                $this->_getSession()->addError($this->__('Cannot save shipment.'));
                $this->_redirect('*/*/new', array('order_id' => $this->getRequest()->getParam('order_id')));
            }

        }
        if ($isNeedCreateLabel) {
            $this->getResponse()->setBody($responseAjax->toJson());
        } else {
            $this->_redirect('*/sales_order/view', array('order_id' => $shipment->getOrderId()));
        }
    }

}