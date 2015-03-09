<?php
require_once 'Mage/Adminhtml/controllers/Sales/Order/InvoiceController.php';

class Iksula_Suggest_Adminhtml_Suggest_InvoiceController extends Mage_Adminhtml_Sales_Order_InvoiceController {
	
	// public function __construct() {
	// 	// echo "sales order InvoiceController lol";
	// }

	public function pendingsaveAction() {
		// parent::saveAction();

		$data = $this->getRequest()->getPost('invoice');
        $orderId = $this->getRequest()->getParam('order_id');
		
		// echo "<pre>"; print_r($data);

        if (!empty($data['comment_text'])) {
            Mage::getSingleton('adminhtml/session')->setCommentText($data['comment_text']);
        }

        try {
            $invoice = $this->_initInvoice();
            if ($invoice) {
			
				// print_r($invoice->getData()); exit;

                if (!empty($data['capture_case'])) {
                    $invoice->setRequestedCaptureCase($data['capture_case']);
                }

                if (!empty($data['comment_text'])) {
                    $invoice->addComment(
                        $data['comment_text'],
                        isset($data['comment_customer_notify']),
                        isset($data['is_visible_on_front'])
                    );
                }

                $invoice->register();

                if (!empty($data['send_email'])) {
                    $invoice->setEmailSent(true);
                }
				
				// exit;

                $invoice->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
                $invoice->getOrder()->setIsInProcess(true);

                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());
                $shipment = false;
                if (!empty($data['do_shipment']) || (int) $invoice->getOrder()->getForcedDoShipmentWithInvoice()) {
                    $shipment = $this->_prepareShipment($invoice);
                    if ($shipment) {
                        $shipment->setEmailSent($invoice->getEmailSent());
                        $transactionSave->addObject($shipment);
                    }
                }
                $transactionSave->save();

                if (isset($shippingResponse) && $shippingResponse->hasErrors()) {
                    $this->_getSession()->addError($this->__('The invoice and the shipment  have been created. The shipping label cannot be created at the moment.'));
                } elseif (!empty($data['do_shipment'])) {
                    $this->_getSession()->addSuccess($this->__('The invoice and shipment have been created.'));
                } else {
                    $this->_getSession()->addSuccess($this->__('The invoice has been created.'));
                }

                // send invoice/shipment emails
                $comment = '';
                if (isset($data['comment_customer_notify'])) {
                    $comment = $data['comment_text'];
                }
                try {
                    $invoice->sendEmail(!empty($data['send_email']), $comment);
                } catch (Exception $e) {
                    Mage::logException($e);
                    $this->_getSession()->addError($this->__('Unable to send the invoice email.'));
                }
                if ($shipment) {
                    try {
                        $shipment->sendEmail(!empty($data['send_email']));
                    } catch (Exception $e) {
                        Mage::logException($e);
                        $this->_getSession()->addError($this->__('Unable to send the shipment email.'));
                    }
                }
                Mage::getSingleton('adminhtml/session')->getCommentText(true);

                // put a custom status here saying we have created invoice for this order but the payment from the vendor is still due.
                $orderp = Mage::getModel('sales/order')->load($orderId);
                $orderp->setData('state', "pending_payment");
				$orderp->setStatus("vendor_payment_due");
				$history = $orderp->addStatusHistoryComment('The invoice has been created but Vendor Payment Is Due.', false);
				$history->setIsCustomerNotified(false);
				$orderp->save();

                $this->_redirect('adminhtml/sales_order/view', array('order_id' => $orderId));
            } else {
                $this->_redirect('adminhtml/*/new', array('order_id' => $orderId));
            }
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Unable to save the invoice.'));
            Mage::logException($e);
        }
        $this->_redirect('adminhtml/*/new', array('order_id' => $orderId));
	}
}