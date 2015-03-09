<?php

require_once 'Mage/Checkout/controllers/OnepageController.php';
class Iksula_Suggest_OnepageController extends Mage_Checkout_OnepageController
{
    protected $_sectionUpdateFunctions = array(
        'payment-method'  => '_getPaymentMethodsHtml',
        'shipping-method' => '_getShippingMethodsHtml',
        'review'          => '_getReviewHtml',
    );

    /**
     * @return Mage_Checkout_OnepageController
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $this->_preDispatchValidateCustomer();

        $checkoutSessionQuote = Mage::getSingleton('checkout/session')->getQuote();
        if ($checkoutSessionQuote->getIsMultiShipping()) {
            $checkoutSessionQuote->setIsMultiShipping(false);
            $checkoutSessionQuote->removeAllAddresses();
        }

        return $this;
    }

    protected function _ajaxRedirectResponse()
    {
        $this->getResponse()
            ->setHeader('HTTP/1.1', '403 Session Expired')
            ->setHeader('Login-Required', 'true')
            ->sendResponse();
        return $this;
    }

    /**
     * Validate ajax request and redirect on failure
     *
     * @return bool
     */
    protected function _expireAjax()
    {
        if (!$this->getOnepage()->getQuote()->hasItems()
            || $this->getOnepage()->getQuote()->getHasError()
            || $this->getOnepage()->getQuote()->getIsMultiShipping()) {
            $this->_ajaxRedirectResponse();
            return true;
        }
        $action = $this->getRequest()->getActionName();
        if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true)
            && !in_array($action, array('index', 'progress'))) {
            $this->_ajaxRedirectResponse();
            return true;
        }

        return false;
    }

    /**
     * Get shipping method step html
     *
     * @return string
     */
    protected function _getShippingMethodsHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_shippingmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    /**
     * Get payment method step html
     *
     * @return string
     */
    protected function _getPaymentMethodsHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_paymentmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    protected function _getAdditionalHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_additional');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    /**
     * Get order review step html
     *
     * @return string
     */
    protected function _getReviewHtml()
    {
        return $this->getLayout()->getBlock('root')->toHtml();
    }

    /**
     * Get one page checkout model
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    /**
     *
     * BSchool Campaign validate Scheduled Date
     *
     * validate if the entered date is valid or not
     *
     */
    public function validateScheduledDate($scheduledDate) {

        $scheduledDateStamp = strtotime($scheduledDate);
        $todaysStamp = time();
        $daysBetween = ($scheduledDateStamp - $todaysStamp) / (60 * 60 * 24);
        
        if($todaysStamp > $scheduledDateStamp) {

            // entered a day before today
            Mage::getSingleton('core/session')->addError("You have entered a past date. Please provide a proper date.");
            session_write_close();
            $this->_redirect('beforecheckout');
            return;

        } elseif($daysBetween <= 13) {

            // entered date is less than 2 weeks away
            Mage::getSingleton('core/session')->addError("Please enter a date atleast two weeks from today.");
            session_write_close();
            $this->_redirect('beforecheckout');
            return;

        } else {
            
            // entered date is okay
            // save scheduled delivery in session var, allow to go ahead
            $sessionBS = Mage::getSingleton("core/session");
            $sessionBS->setData("scheduled_delivery", $scheduledDateStamp);

        }

    }

    /**
     * Checkout page
     */
    public function indexAction()
    {
        // echo "my"; exit;

        // Custom code for BSchool Campaign. Validation for the entered Scheduled Delivery Date | START
        $session = Mage::getSingleton("core/session");
        $shipmentOptionChosen = $this->getRequest()->getParam('shipment_option');
        if(isset($shipmentOptionChosen)) {
            $session->setData("shipment_option", $shipmentOptionChosen);
        }
        $knowsShippingAddress = $this->getRequest()->getParam('know_shipping_address');
        if(isset($knowsShippingAddress)) {
            $session->setData("know_shipping_address", $knowsShippingAddress);
        }

        $scheduledDate = $this->getRequest()->getParam('scheduled_delivery');
        if($scheduledDate AND $shipmentOptionChosen == '1') {
            $this->validateScheduledDate($scheduledDate);
        } else {
            // check if cart has bundle products
            $sessionCheckout = Mage::getSingleton('checkout/session');
            $cartHasBundleProduct = 0;
            foreach($sessionCheckout->getQuote()->getAllItems() as $item) {
                if($item->getData('product_type') == 'bundle') {
                    $cartHasBundleProduct = 1; break;
                }
            }
            if($cartHasBundleProduct == 0) {
                $session->unsScheduledDelivery();
            }
        }
        // echo $session->getScheduledDelivery(); exit;
        // Custom code for BSchool Campaign. Validation for the entered Scheduled Delivery Date | END

        $vendorId = Mage::getSingleton('customer/session')->getCustomerId();
        if ($vendorId)
        {
            $vendorModel = Mage::getModel('customer/customer')->load($vendorId);
            // echo "<pre>"; print_r($vendorModel->getData());
            
            if ($vendorModel->getData('group_id') == '6' OR $vendorModel->getData('group_id') == '7')
            {
                $customerModel = Mage::getModel('customer/customer');
                $attr = $customerModel->getResource()->getAttribute("vendor_blacklisted");

                if ($attr->usesSource()) {
                    $valueYes = $attr->getSource()->getOptionId('Yes');
                    $valueNo = $attr->getSource()->getOptionId('No');
                }
                
                // $statusArray = array($valueYes => 'Yes', $valueNo => 'No');
                // print_r($statusArray);
                // echo $valueYes;
                
                if ($vendorModel->getData('vendor_blacklisted') == $valueYes)
                {
                    Mage::getSingleton('checkout/session')->addError($this->__('Due to failure to pay your due amount on your previous orders, you have been banned from purchasing from Happily Unmarried.'));
                    $this->_redirect('checkout/cart');
                    return;
                    
                    // echo "in if";
                    
                    // $getVendorAccount = Mage::getModel('customer/customer')->load($vendorId);
                    // $activationStatus = $valueNo;
                    // $getVendorAccount->setVendorBlacklisted($activationStatus)->save();
                    
                    // $getVendorAccount = Mage::getModel('customer/customer')->load($vendorId);
                    // print_r($getVendorAccount->getData());
                }
            }
        }
        // exit;
        
        if (!Mage::helper('checkout')->canOnepageCheckout()) {
            Mage::getSingleton('checkout/session')->addError($this->__('The onepage checkout is disabled.'));
            $this->_redirect('checkout/cart');
            return;
        }
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }
        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_secure'=>true)));
        $this->getOnepage()->initCheckout();
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Checkout'));
        $this->renderLayout();
    }
    
    /**
     * Order success action - Delete Coupon
     */
    public function successAction(){
        $session = $this->getOnepage()->getCheckout();
        if (!$session->getLastSuccessQuoteId()) {
            $this->_redirect('checkout/cart');
            return;
        }

        $lastQuoteId = $session->getLastQuoteId();
        $lastOrderId = $session->getLastOrderId();
        $lastRecurringProfiles = $session->getLastRecurringProfileIds();
        if (!$lastQuoteId || (!$lastOrderId && empty($lastRecurringProfiles))) {
            $this->_redirect('checkout/cart');
            return;
        }


    /*---------------Email grab-----------------*/
        $order = Mage::getSingleton('sales/order')->load($lastOrderId);

        if ($order->getCustomerEmail()) {
            $email      = $order->getCustomerEmail();
            $theme      = Mage::getSingleton('core/design_package')->getTheme('frontend');
            if($theme == 'default'){
               $platform = 'Desktop'; 
            }else{
               $platform = 'Mobile'; 
            }

            $collection = Mage::getModel('hupopup/hupopup')
                      ->getCollection()
                      ->addFieldToFilter('user_email_id', $email)
                      ->getFirstItem();

            $userMail = $collection->getData('user_email_id');

            if($userMail == $email){
                    $id = $collection->getData('hu_popup_id');
                    //$date = now();
                    //$data = array('username'=>'Order Placed','user_email_id'=>$email,'Friend_email_id'=>'NA','verify_link'=>'NA','source'=>'Order Flow','platform'=>$platform,'created_date'=>$date);
                    $data = array('source'=>'Order Flow - Order Placed');

                    $collection->load($id)->addData($data);

                    try {
                        $collection->setId($id)->save();
                        //echo "Data updated successfully.";

                    } catch (Exception $e){
                        echo $e->getMessage(); 
                    }
                    //echo "done";exit;

            }else{
                //echo "okk";exit;
                          // $collection->setUsername('Order Placed');
                          // $collection->setUser_email_id($email);
                          // $collection->setFriend_email_id('NA');
                          // $collection->setVerify_link('NA');
                          // $collection->setSource('Order Flow');
                          // $collection->setPlatform($platform);
                          // $collection->setCreated_date(now());
                          // $collection->save();
                   }
            }
/*--------------------------------*/

        


        
        /* Check if Vendor has any previous Payments Due */

        $vendorId = Mage::getSingleton('customer/session')->getCustomerId();
        $customerModel = Mage::getModel('customer/customer')->load($vendorId);
        $groupId = $customerModel->getData('group_id');
        
        if ($groupId == '6' OR $groupId == '7')
        {
            $orderIdx = $session->getLastOrderId();
            $orderc = Mage::getSingleton('sales/order')->load($orderIdx);
            $orderId = $orderc->getData('increment_id');
            $paymentMethod = $orderc->getPayment()->getData('method');

            $salescollection = Mage::getModel('sales/order')->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('customer_id', array('eq' => $vendorId));


            $currentGrandTotal = $orderc->getData('grand_total');
            $vendorHasArrears = 0;
            foreach ($salescollection as $order) {
                if ($order['increment_id'] != $orderId) {
                    if ($order['status'] == "vendor_payment_due" OR $order['status'] == "pending_neft" OR $order['status'] == "processing_with_arrears" OR $order['status'] == "pending_to_management" OR $order['status'] == "pending_to_finance" OR $order['status'] == "pending_to_marketing") {

                        $vendorHasArrears = 1;
                        $attr = $customerModel->getResource()->getAttribute("credit_limit");
                        $credit_val_1 = 15;
                        $credit_val_2 = 30;
                        $credit_val_3 = 45;

                        if ($attr->usesSource()) {
                            $credit_id_1 = $attr->getSource()->getOptionId(strval($credit_val_1));
                            $credit_id_2 = $attr->getSource()->getOptionId(strval($credit_val_2));
                            $credit_id_3 = $attr->getSource()->getOptionId(strval($credit_val_3));
                        }

                        $days_map = array($credit_id_1 => $credit_val_1, $credit_id_2 => $credit_val_2, $credit_id_3 => $credit_val_3);

                        $creditLimit = $days_map[$customerModel['credit_limit']];
                        
                        $this->previousPaymentsAreDue($order, $creditLimit, $orderId, $paymentMethod, $currentGrandTotal);
                    }
                }
            }

            // vendorcheckout - check out with NEFT
            // checkmo - direct check out using credit limit

            if ($vendorHasArrears == 1) {


                if ($paymentMethod == 'vendorcheckout') {
                    $this->changeStatusToPendingNeft($orderIdx);
                    $this->sendVerifyNeftMail($orderIdx);
                }
                elseif ($paymentMethod == 'checkmo') {
                    
                    if ($currentGrandTotal > "100000") {
                        $this->changeStatusToManagement($orderIdx);
                    }
                    elseif ($currentGrandTotal > "50000" AND $currentGrandTotal < "100000") {
                        $this->changeStatusToFinance($orderIdx);
                    }
                    else {
                        $this->changeStatusToMarketing($orderIdx);
                    }
                }
            }
            else {

                if ($paymentMethod == 'checkmo') {
                    $this->processingWithArrears($orderIdx);
                }
                elseif ($paymentMethod == 'vendorcheckout') {
                    $this->changeStatusToPendingNeft($orderIdx);
                    $this->sendVerifyNeftMail($orderIdx);
                }
            }
            
            if ($paymentMethod == 'ccavenue') {
                // custom status 'b2b_processing' for b2b payments with credit card
                $this->processingWithCC($orderIdx);
            }
        }


        /* Delete Vendor Coupon Code */
        $model = Mage::getModel('salesrule/rule')
            ->getCollection()
            ->addFieldToFilter('name', array('eq'=>sprintf('Vendor_%s Margin', Mage::getSingleton('customer/session')->getCustomerId())))
            ->getFirstItem();
        $model->delete();

        // debug order
        // echo $orderIdx . "<br /><pre>"; print_r($orderc->getData()); print_r($orderc->getPayment()->getData()); exit;

        /* BSCHOOL CAMPAIGN | START */
        $orderId = $lastOrderId;
        $session = Mage::getSingleton('core/session');
        $getBundledcart = $session->getBundledcart();
        $knowsShippingAddress = $session->getData("know_shipping_address");
        $shipmentOptionChosen = $session->getData("shipment_option");
        if($getBundledcart == 'true' AND $shipmentOptionChosen == '1') {
            $orderData = Mage::getSingleton('sales/order')->load($orderId);
            // $orderData->setData('state', "processing");
            if($knowsShippingAddress == '0') {
                $orderData->setStatus("bs_processing_noaddress");
            } else {
                $orderData->setStatus("bs_processing");
            }
            $orderData->save();
            $session->setBundledcart('false');
            $session->unsKnowShippingAddress();
            $session->unsShipmentOption();
            $session->unsScheduledDelivery();
        }
        /* BSCHOOL CAMPAIGN | END */

        $session->clear();

        /* GETIT INTEGRATION Starts */
        $getItData = Mage::getSingleton('core/session')->getGetItData();
        if($getItData['utm_source'] == 'getit' AND $getItData['utm_medium'] == 'affiliate') {
            // load order details
            $orderIdGI = $session->getLastOrderId();
            $orderGI = Mage::getSingleton('sales/order')->load($orderIdGI);

            // create URL to send a CURL request to
            $baseUrl = 'http://interiors.getit.in/best-selling-paymentinfo';
            $param1 .= 'Orderid=' . $orderGI->getData('increment_id');
            $param2 .= 'amount=' . (int)$orderGI->getData('grand_total');
            $param3 .= 'ref_id=9';
            $queryString = '?' . $param1 . "&" . $param2 . "&" . $param3;
            $stringToSend = $baseUrl . $queryString;

            // Set query data with the URL
            $CURL = curl_init();
            curl_setopt($CURL, CURLOPT_URL, $stringToSend);
            curl_setopt($CURL, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($CURL, CURLOPT_TIMEOUT, '4');
            curl_setopt($CURL, CURLOPT_CUSTOMREQUEST, 'GET');
            $result = curl_exec($CURL);
            if(!$result = curl_exec($CURL)) 
            { 
                trigger_error(curl_error($CURL));
            }
            curl_close($CURL);
            
        }
        /* GETIT INTEGRATION Ends */

        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');
        Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($lastOrderId)));
        $this->renderLayout();

    }

    public function changeStatusToManagement($orderId) {
        $order = Mage::getModel('sales/order')->load($orderId);
        // echo "<pre>"; print_r($order->getData()); exit;
        $order->setData('state', "new");
        $order->setStatus("pending_to_management");
        $history = $order->addStatusHistoryComment('Order Transferred to Management for Approval', false);
        $history->setIsCustomerNotified(false);
        $order->save();
    }

    public function changeStatusToFinance($orderId) {
        $order = Mage::getModel('sales/order')->load($orderId);
        // echo "<pre>"; print_r($order->getData()); exit;
        $order->setData('state', "new");
        $order->setStatus("pending_to_finance");
        $history = $order->addStatusHistoryComment('Order Transferred to Finance for Approval', false);
        $history->setIsCustomerNotified(false);
        $order->save();
    }

    public function changeStatusToMarketing($orderId) {
        $order = Mage::getModel('sales/order')->load($orderId);
        // echo "<pre>"; print_r($order->getData()); exit;
        $order->setData('state', "new");
        $order->setStatus("pending_to_marketing");
        $history = $order->addStatusHistoryComment('Order Transferred to Marketing for Approval', false);
        $history->setIsCustomerNotified(false);
        $order->save();
    }

    public function processingWithArrears($orderId) {
        $order = Mage::getModel('sales/order')->load($orderId);
        // echo "<pre>"; print_r($order->getData()); exit;
        $order->setData('state', "processing");
        $order->setStatus("processing_with_arrears");
        $history = $order->addStatusHistoryComment('Processing New Order by Vendor, Has Arrears for this Order', false);
        $history->setIsCustomerNotified(false);
        $order->save();
    }

    public function changeStatusToPendingNeft($orderId) {
        $order = Mage::getModel('sales/order')->load($orderId);
        // echo "<pre>"; print_r($order->getData()); exit;
        $order->setData('state', "new");
        $order->setStatus("pending_neft");
        $history = $order->addStatusHistoryComment('NEFT Verification Pending', false);
        $history->setIsCustomerNotified(false);
        $order->save();
    }

    public function processingWithCC($orderIdx) {
        $order = Mage::getModel('sales/order')->load($orderId);
        // echo "<pre>"; print_r($order->getData()); exit;
        $order->setData('state', "processing");
        $order->setStatus("b2b_processing");
        $history = $order->addStatusHistoryComment('B2B Order placed via Credit Card', false);
        $history->setIsCustomerNotified(false);
        $order->save();
    }

    public function previousPaymentsAreDue($order, $creditLimit, $orderId, $paymentMethod, $currentGrandTotal)
    {
        $ordered_on = strtotime($order['created_at']);
        $vendor_deadline = $ordered_on + ($creditLimit * 24 * 60 * 60);
        $order['vendor_deadline'] = $vendor_deadline;

        $this->sendVendorNotificationMail($order, $orderId);
        $this->sendAdminNotificationMail($order, $orderId, $paymentMethod, $currentGrandTotal);
    }

    // send reminder mail to the vendor and admin
    public function sendVendorNotificationMail($order, $orderId) {
        // send mail to the vendor
        $admin_email = Mage::getStoreConfig('trans_email/ident_custom1/email');
        $vendor_mail = new Zend_Mail();
        $vendor_email = $order['customer_email'];
        $vendor_subject = "Payment For Your Previous Order Is Pending";                            
        $vendor_body = $this->vendorNotificationMailBody($order, $orderId);
        $vendor_mail->setSubject($vendor_subject);
        $vendor_mail->setBodyText($vendor_body);
        $vendor_mail->setFrom($admin_email, "Happily Unmarried");                
        $vendor_mail->addTo($vendor_email);
        $vendor_mail->send();
    }

    public function sendAdminNotificationMail($order, $orderId, $paymentMethod, $currentGrandTotal) {
        // send mail to the admin

        $admin_email = Mage::getStoreConfig('trans_email/ident_custom1/email');
        $admin_email_general = Mage::getStoreConfig('trans_email/ident_general/email');
        if ($paymentMethod == 'vendorcheckout') {
            // send mail to anannya and brijesh
            $admin_email_1 = Mage::getStoreConfig('trans_email/ident_custom1/email');
            $admin_email_2 = Mage::getStoreConfig('trans_email/ident_custom2/email');
            $admin_email = array($admin_email_1, $admin_email_2);
        }
        elseif ($paymentMethod == 'checkmo') {
            
            if ($currentGrandTotal > "100000") {
                // send mail to rajat
                $admin_email = 'hiteshspac@gmail.com';
            }
            elseif ($currentGrandTotal > "50000" AND $currentGrandTotal < "100000") {
                // send mail to brijesh
                $admin_email = Mage::getStoreConfig('trans_email/ident_custom2/email');
            }
            else {
                // send mail to anannya
                $admin_email = Mage::getStoreConfig('trans_email/ident_custom1/email');
            }
        }
        $mail = new Zend_Mail();
        $admin_subject = "New Order Placed while Payment is Pending For A Previous Order";
        $admin_autobody = $this->adminNotificationMailBody($order, $orderId);
        $mail->setSubject($admin_subject);
        $mail->setBodyText($admin_autobody);
        $mail->setFrom($admin_email_general, "Happily Unmarried B2B");
        $mail->addTo($admin_email);
        $mail->send();
    }

    public function vendorNotificationMailBody($order, $orderId) {
        $message = "";
        if ($order['customer_prefix']) {
            $message = "Dear " . $order['customer_prefix'] . " " . $order['customer_firstname'] . " " . $order['customer_lastname'] . " " . "\n\n";
        } else {
            $message = "Dear " . $order['customer_firstname'] . " " . $order['customer_lastname'] . " " . "\n\n";
        }
        $message .= "Congratulations on placing a new order on our website!\n";
        $message .= "Order ID for the new order is: " . $orderId . "\n\n";
        $message .= "Please note, Approval for this order is still pending as you have not paid the amount due on the following order:\n\n";
   
        $message .= "Order No: " . $order['increment_id'] . "\n";
        $message .= "Payment Status: Vendor Payment Due\n";
        $message .= "Order Date: " . $order['created_at'] . "\n";
        $message .= "Order Amount: Rs. " . $order['grand_total'] . "\n\n";
        
        $message .= "Your deadline to pay the full amount for this order is " . date("Y-m-d", $order['vendor_deadline']) . ".\n";
        $message .= "If you fail to pay your due amount before your deadline, your account may get disabled and your pending orders may be cancelled.\n";
        
        return $message;
    }

    public function adminNotificationMailBody($order, $orderId) {
        $message = "";
        if ($order['customer_prefix']) {
            $message = "Vendor Name: " . $order['customer_prefix'] . " " . $order['customer_firstname'] . " " . $order['customer_lastname'] . " " . "\n\n";
        } else {
            $message = "Vendor Name: " . $order['customer_firstname'] . " " . $order['customer_lastname'] . " " . "\n\n";
        }
        $message .= "The vendor has placed a new order. Order ID: " . $orderId . "\n";
        $message .= "Please note, the vendor has not paid the amount due on the following order:\n\n";
   
        $message .= "Order No: " . $order['increment_id'] . "\n";
        $message .= "Payment Status: Vendor Payment Due\n";
        $message .= "Order Date: " . $order['created_at'] . "\n";
        $message .= "Order Amount: Rs. " . $order['grand_total'] . "\n\n";
        
        $message .= "The deadline to pay the full amount for this order is " . date("Y-m-d", $order['vendor_deadline']) . ".\n";
        $message .= "Please approve this order.\n";
        $message .= "If the vendor fails to pay the due amount before this deadline, his account may get disabled and all pending orders may be cancelled.\n";
        
        return $message;
    }

    public function sendVerifyNeftMail($orderId) {
        // send notification to the admin to verify NEFT
        $order = Mage::getModel('sales/order')->load($orderId);
        $payment = $order->getPayment()->getData();
        $orderData = $order->getData();
        // echo "<pre>"; print_r($order->getData()); print_r($payment); exit;
        $mail = new Zend_Mail();
        $admin_email = Mage::getStoreConfig('trans_email/ident_custom1/email');
        $admin_subject = "Approve B2B Order Placed with NEFT";
        $admin_autobody = $this->neftNotificationMailBody($orderData, $payment, $orderId);
        $mail->setSubject($admin_subject);                              
        $mail->setBodyText($admin_autobody);
        $mail->setFrom($admin_email, "Happily Unmarried");                              
        $mail->addTo($admin_email);
        $mail->send();
    }

    public function neftNotificationMailBody($order, $payment, $orderId) {
        $message = "";
        if ($order['customer_prefix']) {
            $message = "Vendor Name: " . $order['customer_prefix'] . " " . $order['customer_firstname'] . " " . $order['customer_lastname'] . " " . "\n\n";
        } else {
            $message = "Vendor Name: " . $order['customer_firstname'] . " " . $order['customer_lastname'] . " " . "\n\n";
        }
        $message .= "The vendor has placed a new order. Order ID: " . $order['increment_id'] . "\n";
        $message .= "Payment for this order has been made through Net Banking (NEFT).:\n\n";
   
        $message .= "Order No: " . $order['increment_id'] . "\n";
        $message .= "Payment Status: Pending (NEFT Verification)\n";
        $message .= "Order Date: " . $order['created_at'] . "\n";
        $message .= "Order Amount: Rs. " . $order['grand_total'] . "\n\n";

        $message .= "Payment Details\n";
        $message .= "Bank Name: " . $payment['vendor_bankname'] . "\n";
        $message .= "NEFT Amount: Rs. " . $payment['vendor_amount'] . "\n";
        $message .= "Transaction / Reference ID: " . $payment['vendor_transactionid'] . "\n\n";
        
        $message .= "Please verify the payment made for this order and approve this order.\n";
        
        return $message;
    }

    /**
     * save checkout billing address
     */
    public function saveBillingAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
//            $postData = $this->getRequest()->getPost('billing', array());
//            $data = $this->_filterPostData($postData);
            $data = $this->getRequest()->getPost('billing', array());
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

            if (isset($data['email'])) {
                $data['email'] = trim($data['email']);
            }
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);

            if (!isset($result['error'])) {
                /* check quote for virtual */
                if ($this->getOnepage()->getQuote()->isVirtual()) {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );

                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';
                } else {
                    $result['goto_section'] = 'shipping';
                }
            }

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Shipping address save action
     */
    public function saveShippingAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

            if (!isset($result['error'])) {
                $result['goto_section'] = 'payment';
                $result['update_section'] = array(
                    'name' => 'payment-method',
                    'html' => $this->_getPaymentMethodsHtml()
                );
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Shipping method save action
     */
    public function saveShippingMethodAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
            $result = $this->getOnepage()->saveShippingMethod($data);
            /*
            $result will have erro data if shipping method is empty
            */
            if(!$result) {
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method',
                        array('request'=>$this->getRequest(),
                            'quote'=>$this->getOnepage()->getQuote()));
                $this->getOnepage()->getQuote()->collectTotals();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));


                // get section and redirect data
                $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
                if (empty($result['error']) && !$redirectUrl) {
                    $this->loadLayout('checkout_onepage_review');
                    
                    $result['goto_section'] = 'review';
                    $result['update_section'] = array(
                        'name' => 'review',
                        'html' => $this->_getReviewHtml()
                    );

                }
                if ($redirectUrl) {
                    $result['redirect'] = $redirectUrl;
                }
            }
            $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }
            $this->getOnepage()->getQuote()->collectTotals()->save();
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Save payment ajax action
     *
     * Sets either redirect or a JSON response
     */
    public function savePaymentAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        try {
            if (!$this->getRequest()->isPost()) {
                $this->_ajaxRedirectResponse();
                return;
            }

            // set payment to quote
            $result = array();
            $data = $this->getRequest()->getPost('payment', array());
            $result = $this->getOnepage()->savePayment($data);

            $baseTotal = Mage::getSingleton('checkout/cart')->getQuote()->getSubtotal();
            $session = Mage::getSingleton('checkout/session');
            $session->setHitesh($baseTotal);
            Mage::register('hitesh', $baseTotal);

            // set shipping amount based on the chosen payment method
            /* $baseTotal = Mage::getSingleton('checkout/cart')->getQuote()->getSubtotal();
            $paymentMethod = $data['method'];
            $shippingAmount = Mage::helper('suggest')->getCalculatedShippingAmount($paymentMethod, $baseTotal);
            $this->getOnepage()->getQuote()->getShippingAddress()->setShippingAmount($shippingAmount)->save();
            $result = $this->getOnepage()->saveShippingMethod('tablerate_bestway'); */

            /*print_r(json_encode(Mage::getSingleton('checkout/cart')->getQuote()->getPayment()->getData('method'))); exit;
            print_r(get_class_methods(Mage::getSingleton('checkout/cart')->getQuote()->getPayment())); exit;*/

            // get section and redirect data
            $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if (empty($result['error']) && !$redirectUrl) {
                $this->loadLayout('checkout_onepage_review');
                
                $result['goto_section'] = 'shipping_method';
                $result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()
                );

            }
            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }
        } catch (Mage_Payment_Exception $e) {
            if ($e->getFields()) {
                $result['fields'] = $e->getFields();
            }
            $result['error'] = $e->getMessage();
        } catch (Mage_Core_Exception $e) {
            $result['error'] = $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            $result['error'] = $this->__('Unable to set Payment Method.');
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    
}