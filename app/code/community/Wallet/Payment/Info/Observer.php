<?php

class Wallet_Payment_Info_Observer {

    public function check_status($observer) {
      /*  $session = Mage::getSingleton('wallet/session');
        $payment = $observer->getPayment();
        $payment_method = $observer->getPayment()->getMethodInstance();
        $orderId = $payment->getOrder()->getIncrementId();
       // if ($payment_method->getCode() == 'wallet') {
            // check in session cache
            //$status = $session->getTransactStatus($orderId);            
            //if ($status == null) {
                // get the status here
                //$status = $payment_method->checkStatus($payment);
                //$session->setTransactStatus($orderId, "SUCCESS");
           // }
            //$observer->getTransport()->setData('Latest Wallet Status', $status);            
       // }
        return $this; */
    } 

}