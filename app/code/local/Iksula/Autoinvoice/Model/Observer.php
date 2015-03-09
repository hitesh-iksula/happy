<?php
 
class Iksula_Autoinvoice_Model_Observer {
 
    public $order;//the order...
 
    function afterSalesOrderSaveCommitAfter(&$event) {
        return $this->__process($event);
    }
 
    protected function __process($event) {
        $this->order = $event->getEvent()->getOrder();
        if (!$this->order->getId()) {
            //order is not saved in the database
            return $this;
        }
        else {
            $orderId = $this->order->getId();
            $orderData = Mage::getModel('sales/order')->load($orderId);
            if($orderData->getData('customer_group_id') != '6' AND $orderData->getData('customer_group_id') != '7') {
                $this->createInvoice(); 
            }
        }
    }
 
    protected function createInvoice() {
        $orderState = $this->order->getState();
        if ($orderState === Mage_Sales_Model_Order::STATE_PROCESSING) { // Check for state processing.
            if ($this->order->canInvoice()) {
                $this->order->getPayment()->setSkipTransactionCreation(false);
                $invoice = $this->order->prepareInvoice();
                $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
                $invoice->register();
                Mage::getModel('core/resource_transaction')
                   ->addObject($invoice)
                   ->addObject($this->order)
                   ->save();
            }
            else {
                //we can not invoice it so the process is normal.
            }
        }
    }
}
 
?>