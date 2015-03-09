<?php
require_once 'Mage/Adminhtml/controllers/Sales/Order/ShipmentController.php';

class Iksula_Suggest_Adminhtml_Suggest_ShipmentController extends Mage_Adminhtml_Sales_Order_ShipmentController {
	
	// public function __construct() {
	// 	// echo "sales order InvoiceController lol";
	// }
	
	public function pendingsaveAction() {
		
		// echo "here"; exit;
		
		$data = $this->getRequest()->getPost('shipment');
		
		echo "<pre>"; print_r($data); exit;
		
        if (!empty($data['comment_text'])) {
            Mage::getSingleton('adminhtml/session')->setCommentText($data['comment_text']);
        }

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
            $this->_saveShipment($shipment);
            $shipment->sendEmail(!empty($data['send_email']), $comment);
            Mage::getSingleton('adminhtml/session')->getCommentText(true);
			
			// put a custom status here saying we have created shipment for this order but the payment from the vendor is still due.
			$orderp = Mage::getModel('sales/order')->load($orderId);
			$orderp->setData('state', "pending_payment");
			$orderp->setStatus("vendor_payment_due");
			$history = $orderp->addStatusHistoryComment('Shipment has been created but Vendor Payment Is Due.', false);
			$history->setIsCustomerNotified(false);
			$orderp->save();
			
        } catch (Mage_Core_Exception $e) {
            if ($isNeedCreateLabel) {
                $responseAjax->setError(true);
                $responseAjax->setMessage($e->getMessage());
            } else {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('adminhtml/sales_order/new', array('order_id' => $this->getRequest()->getParam('order_id')));
            }
        } catch (Exception $e) {
            Mage::logException($e);
            if ($isNeedCreateLabel) {
                $responseAjax->setError(true);
                $responseAjax->setMessage(Mage::helper('sales')->__('An error occurred while creating shipping label.'));
            } else {
                $this->_getSession()->addError($this->__('Cannot save shipment.'));
                $this->_redirect('adminhtml/sales_order/new', array('order_id' => $this->getRequest()->getParam('order_id')));
            }

        }
        if ($isNeedCreateLabel) {
            $this->getResponse()->setBody($responseAjax->toJson());
        } else {
            $this->_redirect('*/sales_order/view', array('order_id' => $shipment->getOrderId()));
        }
		
	}
}