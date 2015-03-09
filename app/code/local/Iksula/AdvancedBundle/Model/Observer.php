<?php
/**
 *
 * BSchool Campaign
 * 
 * Observer File
 * 
 * @ saveScheduledDeliveryDate($event)  -> Save Scheduled Delivery date in Order
 * @ runEveryday($event)                -> Cron job function that runs everyday to check which orders are a week away from delivery
 * @ sendShippingAddressEmail($orderId) -> Sends Emails to Customers who haven't given their shipping address details
 * @ encryptForUrl($oid, $cid)          -> MCRYPT data to be sent via URL
 * @ updateStatusReadyToShip($orderId)  -> Change order status to "BS Ready To Ship" from "BS Processing" when order delivery date is a week away.
 *                                         Also send notification Emails to the customer and admin
 * +                                       @ sendCustomerReminderEmail($emailParameters, $addressLines) -> To the customer
 * +                                       @ sendAdminReminderEmail($emailParameters) -> To the admin
 * 
 * 
 * 
 */
?>

<?php

class Iksula_AdvancedBundle_Model_Observer {

    // Function to save Scheduled Delivery in Order Data if given
    public function saveScheduledDeliveryDate($event) {
        $quote = $event->getEvent()->getOrder();
        $session = Mage::getSingleton("core/session");
        $deliveryDate = $session->getData("scheduled_delivery");
        if(isset($deliveryDate)) {
        	$quote->setData('scheduled_delivery', $deliveryDate);
        }

        return $this;
    }

    // Function to check delivery dates of orders
    public function runEveryday($event) {
        
        // if the customer has not provided his shipping address, ask him to do it when his delivery date is less than 7 days away. check this everyday
        $orderCollection = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('increment_id')
            ->addAttributeToSelect('scheduled_delivery')
            ->addAttributeToFilter('status', array('eq' => 'bs_processing_noaddress'));
            
        $currentTime = time();
        foreach($orderCollection as $order) {

            $orderId = $order->getData('increment_id');
            if($order->getData('scheduled_delivery')) {
                $secondsLeft = $order->getData('scheduled_delivery') - $currentTime;
                $daysLeft = ceil($secondsLeft / (60 * 60 * 24));
                if($daysLeft <= 7 AND $daysLeft >= 0) {
                    
                    // send shipping address update email to this person
                    $this->sendShippingAddressEmail($orderId);

                }

            }

        }

        // if the customer has scheduled a delivery and it's 7 days away from today
        $orderCollection = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('increment_id')
            ->addAttributeToSelect('scheduled_delivery')
            ->addAttributeToFilter('status', array('eq' => 'bs_processing'));
            
        $currentTime = time();
        foreach($orderCollection as $order) {

            $orderId = $order->getData('increment_id');
            if($order->getData('scheduled_delivery')) {
                $secondsLeft = $order->getData('scheduled_delivery') - $currentTime;
                $daysLeft = ceil($secondsLeft / (60 * 60 * 24));
                if($daysLeft == 7) {
                    
                    // update status of this order and send emails to this person and the admin
                    $this->updateStatusReadyToShip($orderId);

                }
                
            }

        }

    }

    // function that sends Emails to Customers who haven't given their shipping address details
    public function sendShippingAddressEmail($orderId) {

        // Load order details
        $order = Mage::getSingleton('sales/order')->loadByIncrementId($orderId);

        // Transactional Email Template's ID
        $templateId = 7;

        // Set sender information
        $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
        $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
        $sender = array(
            'name' => $senderName,
            'email' => $senderEmail
        );

        // Set recepient information
        $recepientName = $order->getData('customer_firstname') . " " . $order->getData('customer_lastname');
        $recepientEmail = $order->getData('customer_email');
        $recepientEmail = 'hitesh.pachpor@iksula.com';

        // Get Store ID
        $storeId = Mage::app()->getStore()->getId();

        // Set variables that can be used in email template
        if($order->getData('customer_id') == '') {
            $customerId = 'Guest';
        } else {
            $customerId = $order->getData('customer_id');
        }

        // encode URL parameters before sending them
        $encryptedData = $this->encryptForUrl($orderId, $customerId);

        $vars = array(
            'customerName' => $order->getData('customer_firstname') . " " . $order->getData('customer_lastname'),
            'customerId' => $customerId,
            'orderId' => $orderId,
            'customerEmail' => $order->getData('customer_email'),
            'encryptedCustomerId' => $encryptedData['customerId'],
            'encryptedOrderId' => $encryptedData['orderId'],
            'theKey' => $encryptedData['iv']
        );

        $translate = Mage::getSingleton('core/translate');

        // Send Transactional Email
        Mage::getModel('core/email_template')
            ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
        $translate->setTranslateInline(true);

    }

    // function to MCRYPT data to be sent via URL
    public function encryptForUrl($oid, $cid) {

        $key = "spOngEbObsqUArEpAnts";

        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        $encOID = mcrypt_encrypt(MCRYPT_BLOWFISH, $key, $oid, MCRYPT_MODE_ECB, $iv);
        $encCID = mcrypt_encrypt(MCRYPT_BLOWFISH, $key, $cid, MCRYPT_MODE_ECB, $iv);

        $iv = rawurlencode(base64_encode($iv));
        $encOID = rawurlencode(base64_encode($encOID));
        $encCID = rawurlencode(base64_encode($encCID));

        $returnParameters = array('iv' => $iv, 'orderId' => $encOID, 'customerId' => $encCID);

        return $returnParameters;

    }

    // function to change order status to BS Ready To Ship when order delivery date nears. also send Emails
    public function updateStatusReadyToShip($orderId) {

        // Load order details
        $order = Mage::getSingleton('sales/order')->loadByIncrementId($orderId);
        $order->setStatus("bs_ready_to_ship")->save();

        // Load email parameters
        $orderShipmentData = $order->getShippingAddress();
        $scheduledDelivery = date("d - M - Y", $order->getData('scheduled_delivery'));
        $emailParameters = array(
            'customerName' => $order->getData('customer_firstname') . " " . $order->getData('customer_lastname'),
            'orderId' => $orderId,
            'deliveryDate' => $scheduledDelivery,
        );
        $addressLines = array(
            1 => $orderShipmentData->getData('firstname') . " " . $orderShipmentData->getData('lastname'),
            2 => $orderShipmentData->getData('street'),
            3 => $orderShipmentData->getData('city') . ", " . $orderShipmentData->getData('region'),
            4 => $orderShipmentData->getData('country_id') . ", " . $orderShipmentData->getData('postcode'),
        );

        // Send Emails
        $this->sendCustomerReminderEmail($emailParameters, $addressLines);
        $this->sendAdminReminderEmail($emailParameters);
    }

    // function to send customer a reminder mail regarding his order delivery
    public function sendCustomerReminderEmail($emailParameters, $addressLines) {

        // Load order details
        $orderId = $emailParameters['orderId'];
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

        // Transactional Email Template's ID
        $templateId = 11;

        // Set sender information
        $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
        $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
        $sender = array(
            'name' => $senderName,
            'email' => $senderEmail
        );

        // Set recepient information
        $recepientName = $order->getData('customer_firstname') . " " . $order->getData('customer_lastname');
        $recepientEmail = $order->getData('customer_email');
        $recepientEmail = 'hitesh.pachpor@iksula.com';

        // Get Store ID
        $storeId = Mage::app()->getStore()->getId();

        $vars = array(
            'customerName' => $emailParameters['customerName'],
            'orderId' => $orderId,
            'deliveryDate' => $emailParameters['deliveryDate'],
            'addressLine1' => $addressLines[1],
            'addressLine2' => $addressLines[2],
            'addressLine3' => $addressLines[3],
            'addressLine4' => $addressLines[4]
        );

        $translate = Mage::getSingleton('core/translate');

        // Send Transactional Email
        Mage::getModel('core/email_template')
            ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
        $translate->setTranslateInline(true);

    }

    // function to send admin a reminder mail regarding a scheduled order delivery
    public function sendAdminReminderEmail($emailParameters) {

        // Load order details
        $orderId = $emailParameters['orderId'];
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

        // Transactional Email Template's ID
        $templateId = 10;

        // Set sender information
        $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
        $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
        $sender = array(
            'name' => $senderName,
            'email' => $senderEmail
        );

        // Set recepient information
        // $recepientEmail = $order->getData('customer_email');
        $recepientName = Mage::getStoreConfig('trans_email/ident_support/name');
        $recepientEmail = Mage::getStoreConfig('trans_email/ident_support/email');

        // Get Store ID
        $storeId = Mage::app()->getStore()->getId();

        $vars = array(
            'customerName' => $emailParameters['customerName'],
            'orderId' => $orderId,
            'deliveryDate' => $emailParameters['deliveryDate']
        );

        $translate = Mage::getSingleton('core/translate');

        // Send Transactional Email
        Mage::getModel('core/email_template')
            ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
        $translate->setTranslateInline(true);

    }

    // function to decrypt data received from URL. not needed here.
    /*public function decryptFromUrl($encryptedData) {

        $key = "spOngEbObsqUArEpAnts";

        $iv = base64_decode(rawurldecode($encryptedData['iv']));
        $encOID = base64_decode(rawurldecode($encryptedData['orderId']));
        $encCID = base64_decode(rawurldecode($encryptedData['customerId']));

        $crypttextOID = mcrypt_decrypt(MCRYPT_BLOWFISH, $key, $encOID, MCRYPT_MODE_ECB, $iv);
        $crypttextCID = mcrypt_decrypt(MCRYPT_BLOWFISH, $key, $encCID, MCRYPT_MODE_ECB, $iv);

        $returnParameters = array('orderId' => $crypttextOID, 'customerId' => $crypttextCID);

        return $returnParameters;

    }*/
}

?>