<?php
class Iksula_Suggest_Model_Observer
{
	public function __construct()
	{
		//
	}

	public function customerLogout(Varien_Event_Observer $observer)
    {
		// echo "the observer"; exit;
		
		/* Delete Vendor Coupon Code */
		$model = Mage::getModel('salesrule/rule')
			->getCollection()
			->addFieldToFilter('name', array('eq'=>sprintf('Vendor_%s Margin', Mage::getSingleton('customer/session')->getCustomerId())))
			->getFirstItem();
		$model->delete();
    }

    public function customerSaveAfter(Varien_Event_Observer $observer)
    {
    	// echo "inside my observer"; exit;
    	// $block = $observer->getEvent()->getBlock();
    	$group = $observer->getCustomer();
    	// echo "<pre>"; print_r($group->getData()); exit;
    	$details = $group->getData();

    	// echo Mage::registry('creating_new_customer');
    	// echo "here"; exit;

    	// check if customer is a b2b customer
    	if($group->getData('group_id') == 6 OR $group->getData('group_id') == 7) {
    		// check if a new profile is created or existing is modified
    		if($group->getData('created_at') == $group->getData('updated_at')) {
    			// send an email to the marketing team upon new profile creation
    			// send mail to the vendor
    			$admin_email = Mage::getStoreConfig('trans_email/ident_general/email');
				$registration_mail = new Zend_Mail();
				$registration_email = Mage::getStoreConfig('trans_email/ident_custom1/email');
				$registration_subject = "New Vendor Profile Registered";							
				$registration_body = $this->vendorRegistrationAdminMailBody($details);
				$registration_mail->setSubject($registration_subject);
				$registration_mail->setBodyText($registration_body);				
				$registration_mail->setFrom($admin_email, "Happily Unmarried");
				$registration_mail->addTo($registration_email);
				$registration_mail->send();
    		}
    	}
    }

    public function vendorRegistrationAdminMailBody($details)
    {
    	$customerModel = Mage::getModel('customer/customer');
    	$attr = $customerModel->getResource()->getAttribute("credit_limit");

		$credit_val_0 = "No Credit Limit";
		$credit_val_1 = 15;
		$credit_val_2 = 30;
		$credit_val_3 = 45;

		if ($attr->usesSource()) {
			$credit_id_0 = $attr->getSource()->getOptionId(strval($credit_val_0));
			$credit_id_1 = $attr->getSource()->getOptionId(strval($credit_val_1));
			$credit_id_2 = $attr->getSource()->getOptionId(strval($credit_val_2));
			$credit_id_3 = $attr->getSource()->getOptionId(strval($credit_val_3));
		}

		$days_map = array($credit_id_0 => $credit_val_0, $credit_id_1 => $credit_val_1, $credit_id_2 => $credit_val_2, $credit_id_3 => $credit_val_3);

    	$message = "";

    	$message .= "Dear Admin, you have successfully created a profile for " . $details['vendor_store_name'] . ".\n\n";
    	$message .= "Details:\n";

    	$message .= "Store Name: " . $details['vendor_store_name'] . "\n";
    	$message .= "Point of Contact: " . $details['firstname'] . " " . $details['lastname'] . "\n";
    	$message .= "Email ID: " . $details['email'] . "\n";
    	if ($details['group_id'] == 6) {
    		$message .= "Vendor Type: Vendor (General)\n";
    	} elseif ($details['group_id'] == 7) {
    		$message .= "Vendor Type: Vendor (With C Form)\n";
    	}
    	$message .= "Credit Limit (In Days): " . $days_map[$details['credit_limit']] . "\n";
    	$message .= "Margin (In Percentage): " . $details['margin'] . "\n";
    	// $message .= "" .  . "\n";

    	return $message;
    }
	
	public function adminSalesOrder(Varien_Event_Observer $observer) 
    {
        $block = $observer->getEvent()->getBlock();

        // Sales > Order > View
        if(get_class($block) =='Mage_Adminhtml_Block_Sales_Order_View'
            && $block->getRequest()->getControllerName() == 'sales_order') 
        {
            // $block->addItem('suggest', array(
                // 'label' => 'Export to .csv file',
                // 'url' => Mage::app()->getStore()->getUrl('suggest/export_order/csvexport'),
            // ));
			// echo "<pre>"; print_r(get_class_methods($block));
			// echo "observer working"; exit;
			$action = $block->getRequest()->getActionName();
			if ($action == 'view') {
				// echo "<pre>"; print_r(get_class_methods($block));
				// echo "observer working"; exit;
				// echo "<pre>"; print_r($block->getOrder()->getData()); exit;
				$groupID = $block->getOrder()->getData('customer_group_id');
				// print_r($block->getOrder()->getData());
				$orderState = $block->getOrder()->getData('state');
				$orderStatus = $block->getOrder()->getData('status');
				// echo "<pre>"; print_r($block->getOrder()->getData()); exit;
				
				if ($groupID == '6' OR $groupID == '7') {
					if ($orderState == 'new') {
						if ($orderStatus == 'pending_neft') {
							$this->setProcessingStatus($block);
						}
						elseif ($orderStatus == 'pending_to_marketing' OR $orderStatus == 'pending_to_finance' OR $orderStatus == 'pending_to_management') {
							$this->setProcessingWithArrears($block);
						}
						//$this->setOrderDirectives($block);
					}
					if ($orderState == 'processing') {
						// $this->vendorPaymentPendingSet($block);
					}
					if ($orderStatus == 'vendor_payment_due') {
						$this->vendorPaymentReceived($block);
					}
				}
			}
        }

        // Sales > Order > Create Invoice
        // print_r(get_class($block));
        /*if (get_class($block) =='Mage_Adminhtml_Block_Sales_Order_Invoice_Create'
            AND $block->getRequest()->getControllerName() == 'sales_order_invoice')
        {
        	// echo "hello"; exit;
        	$action = $block->getRequest()->getActionName();
        	// echo "<pre>"; print_r($action); exit;
			$currentPage = $block->getRequest()->getControllerName();
        	if ($action == "new") {
				$this->vendorPendingPaymentSaveInvoice($block);
        	}
        }
		if (get_class($block) =='Mage_Adminhtml_Block_Sales_Order_Shipment_Create'
            AND $block->getRequest()->getControllerName() == 'sales_order_shipment')
        {
        	// echo "hello"; exit;
        	$action = $block->getRequest()->getActionName();
        	// echo "<pre>"; print_r($action); exit;
			$currentPage = $block->getRequest()->getControllerName();
        	if ($action == "new") {
				// $this->vendorPendingPaymentSubmitShipment($block);
        	}
        }*/
    }
	
	public function setOrderDirectives($block)
	{
		$orderId = $block->getOrderId();
		$pathFinance = Mage::getModel('adminhtml/url')->getUrl('suggest/adminhtml_suggest/tofinancestatus');
		$pathManagement = Mage::getModel('adminhtml/url')->getUrl('suggest/adminhtml_suggest/tomanagementstatus');
		// echo $path; exit;
		$toFinanceLink = $pathFinance . "order_id/" . $orderId;
		$toManagementLink = $pathManagement . "order_id/" . $orderId;
		$block->addButton('to_finance', array(
			'label' => Mage::helper('suggest')->__('To Finance'),
			'onclick' => "setLocation('$toFinanceLink')",
		));
		$block->addButton('to_management', array(
			'label' => Mage::helper('suggest')->__('To Management'),
			'onclick' => "setLocation('$toManagementLink')",
		));
	}
	
	public function setProcessingStatus($block)
	{
		$orderId = $block->getOrderId();
		$pathProcessing = Mage::getModel('adminhtml/url')->getUrl('suggest/adminhtml_suggest/toprocessingstatus') . "order_id/" . $orderId;
		// echo $path; exit;
		$block->addButton('to_processing', array(
			'label' => Mage::helper('suggest')->__('Approve Order - NEFT Verified'),
			'onclick' => "setLocation('$pathProcessing')",
		));
	}
	
	public function setProcessingWithArrears($block)
	{
		$orderId = $block->getOrderId();
		$pathProcessingWithArrears = Mage::getModel('adminhtml/url')->getUrl('suggest/adminhtml_suggest/toprocessingwitharrearsstatus') . "order_id/" . $orderId;
		// echo $path; exit;
		$block->addButton('to_processing_with_arrears', array(
			'label' => Mage::helper('suggest')->__('Approve Order - Allow Arrears'),
			'onclick' => "setLocation('$pathProcessingWithArrears')",
		));
	}

	public function vendorPaymentReceived($block)
	{
		$orderId = $block->getOrderId();
		$pathPaid = Mage::getModel('adminhtml/url')->getUrl('suggest/adminhtml_suggest/vendorpaymentreceived') . "order_id/" . $orderId;
		$block->addButton('vendorpaymentreceived', array(
			'label' => Mage::helper('suggest')->__('Vendor Payment Received'),
			'onclick' => "setLocation('$pathPaid')",
		));
	}

	public function vendorPaymentPendingSet($block)
	{
		$orderId = $block->getOrderId();
		$pathPending = Mage::getModel('adminhtml/url')->getUrl('suggest/adminhtml_suggest/vendorpaymentpendingset') . "order_id/" . $orderId;
		$block->addButton('vendorpaymentpendingset', array(
			'label' => Mage::helper('suggest')->__('Vendor Payment Pending'),
			'onclick' => "setLocation('$pathPending')",
		));
	}

	public function vendorPendingPaymentSaveInvoice($block)
	{
		// echo "<pre>"; print_r(get_class_methods($block)); exit;
		$orderId = $block->getRequest()->getParam('order_id');
		$createInvoicePaymentDue = Mage::getModel('adminhtml/url')->getUrl('suggest/adminhtml_suggest_invoice/pendingsave') . "order_id/" . $orderId;
		$label = "Create Invoice For Vendor (Payment Pending)";
		
		$block->addButton('create_without_payment', array(
			'label' => Mage::helper('suggest')->__($label),
			'onclick' => "setLocation('$createInvoicePaymentDue')",
		));
		
		/* $block->setChild('priceupdate_deactivate_button',
			$block->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
				'label'     => Mage::helper('suggest')->__('Duplicate'),
				'onclick'   => 'setLocation(\''.$block->getUrl('suggest/admin/sales_order_invoice', array('_current'=>true)).'\')',
				'class' => ''
			))
        ); */

	}

	public function vendorPendingPaymentSubmitShipment($block)
	{
		// echo "<pre>"; print_r(get_class_methods($block)); exit;
		$orderId = $block->getRequest()->getParam('order_id');
		$createShipmentPaymentDue = Mage::getModel('adminhtml/url')->getUrl('suggest/adminhtml_suggest_shipment/pendingsave') . "order_id/" . $orderId;
		$label = "Submit Shipment For Vendor (Payment Pending)";
		
		$block->addButton('create_without_payment', array(
			'label' => Mage::helper('suggest')->__($label),
			'onclick' => "setLocation('$createShipmentPaymentDue')",
		));
	}
	
	public function karmaPolice() {
        // Mage::log("hitesh WORKS!");
		
		try {
			$myFile = Mage::getBaseDir() . "/cronlog.txt";
			$fh = fopen($myFile, 'a');
			$stringData = date('l jS \of F Y h:i:s A') . "\n";
			fwrite($fh, $stringData);
			fclose($fh);

			// $this->checkVendorPayments();

		} catch (Exception $e) {
			Mage::printException($e);
		}
    }

    public function checkVendorPayments() {

    	// get a list of all vendors
	    $vendorcollection = Mage::getModel('customer/customer')->getCollection()
			->addAttributeToFilter('group_id', array('in' => array(6, 7)));
		//print_r($vendorcollection->getData()); exit;

		$customerModel = Mage::getModel('customer/customer');
		$attr = $customerModel->getResource()->getAttribute("credit_limit");

		$credit_val_0 = "No Credit Limit";
		$credit_val_1 = 15;
		$credit_val_2 = 30;
		$credit_val_3 = 45;

		if ($attr->usesSource()) {
			$credit_id_0 = $attr->getSource()->getOptionId(strval($credit_val_0));
			$credit_id_1 = $attr->getSource()->getOptionId(strval($credit_val_1));
			$credit_id_2 = $attr->getSource()->getOptionId(strval($credit_val_2));
			$credit_id_3 = $attr->getSource()->getOptionId(strval($credit_val_3));
		}

		$days_map = array($credit_id_0 => $credit_val_0, $credit_id_1 => $credit_val_1, $credit_id_2 => $credit_val_2, $credit_id_3 => $credit_val_3);
		$time_array = $this->generateTimestamps();

		$adminReportReminders = array();
		// $adminCount = 0;

		$customerModel = Mage::getModel('customer/customer');
		$attr2 = $customerModel->getResource()->getAttribute("vendor_blacklisted");

		if ($attr2->usesSource()) {
			$valueYes = $attr2->getSource()->getOptionId(strval('Yes'));
			// $valueNo = $attr->getSource()->getOptionId(strval('No'));
			// echo $valueYes;
		}

		foreach ($vendorcollection as $vendor) {

			// for a vendor
			$vendorId = $vendor['entity_id'];
			$vendorDetails = Mage::getModel('customer/customer')->load($vendorId);
			// print_r($vendorDetails->getData()); exit;
			$credit_limit = $days_map[$vendorDetails['credit_limit']];
			
			// remove blacklisted customers from this check
			if($vendorDetails['vendor_blacklisted'] != $valueYes) {

				// $credit_limit = 14;
				// echo $vendorId . "<br />";
				$salescollection = Mage::getModel('sales/order')->getCollection()
					->addAttributeToSelect('*')
					->addAttributeToFilter('customer_id', array('eq' => $vendorId));
				// echo $salescollection->getSelect();
				// print_r($salescollection->getData()); exit;
				
				// $i = 0;
				
				// $first = 1;
				$processSuccessive = 0;
				foreach($salescollection as $order) {

					// vendor's each order
					$pendingorders = array();
					if ($order['status'] == "vendor_payment_due" OR $order['status'] == "processing_with_arrears") {
						// print_r($order->getData());
						
						// if ($first == 1) {
							
							// get order time
							// echo $order['created_at'] . "<br />" . strtotime($order['created_at']) . "<br />";
							
							// $roundofftonext = date("Y-m-d", strtotime($order['created_at']) + (1 * 24 * 60 * 60));
							
							// print_r(getdate(strtotime($order['created_at'])));
							// echo $roundofftonext . "<br />" . strtotime($roundofftonext) . "<br />";
							// print_r(getdate(strtotime($roundofftonext))); exit;
							
							// $ordered_on = strtotime($order['created_at']);
							$ordered_on = strtotime($order['created_at']);
							
							// compute order time + credit limit of the vendor
							$vendor_deadline = $ordered_on + ($credit_limit * 24 * 60 * 60);
							
							$status = $this->checkCreditLimitStatus($vendor_deadline, $time_array);
							
							if ($status != 8) {
								// prepare data for sending mails to the vendor and admin
								// $first = 0;
								// $processSuccessive = 1;
								
								$pendingorders['email'] = $vendorDetails['email'];
								$pendingorders['prefix'] = $vendorDetails['prefix'];
								$pendingorders['firstname'] = $vendorDetails['firstname'];
								$pendingorders['lastname'] = $vendorDetails['lastname'];
								$pendingorders['vendor_store_name'] = $vendorDetails['vendor_store_name'];
								$pendingorders['date'] = $order['created_at'];
								$pendingorders['vendor_deadline'] = $vendor_deadline;
								$pendingorders['vendor_status'] = $status;
								$pendingorders['vendor_id'] = $vendorId;
								
								$pendingorders['increment_id'] = $order['increment_id'];
								$pendingorders['order_status'] = $order['status'];
								$pendingorders['grand_total'] = $order['grand_total'];
								$pendingorders['created_at'] = $order['created_at'];
								
								// $i++;

								if ($status == 0) {
									$this->sendCancellationMail($pendingorders);
								}
								else {
									$this->sendReminderMail($pendingorders);
									$orderIncrementId = $order['increment_id'];
									if(!array_key_exists($vendorId, $adminReportReminders)) {
										$adminReportReminders[$vendorId]['details']['email'] = $vendorDetails['email'];
										$adminReportReminders[$vendorId]['details']['prefix'] = $vendorDetails['prefix'];
										$adminReportReminders[$vendorId]['details']['firstname'] = $vendorDetails['firstname'];
										$adminReportReminders[$vendorId]['details']['lastname'] = $vendorDetails['lastname'];
										$adminReportReminders[$vendorId]['details']['vendor_store_name'] = $vendorDetails['vendor_store_name'];
									}
									$adminReportReminders[$vendorId]['orders'][$orderIncrementId]['increment_id'] = $order['increment_id'];
									$adminReportReminders[$vendorId]['orders'][$orderIncrementId]['order_status'] = $order['status'];
									$adminReportReminders[$vendorId]['orders'][$orderIncrementId]['grand_total'] = $order['grand_total'];
									$adminReportReminders[$vendorId]['orders'][$orderIncrementId]['created_at'] = $order['created_at'];
									$adminReportReminders[$vendorId]['orders'][$orderIncrementId]['vendor_deadline'] = $vendor_deadline;
									$adminReportReminders[$vendorId]['orders'][$orderIncrementId]['vendor_status'] = $status;
									// $adminCount++;
								}
							}
						// }
						/*elseif ($processSuccessive == 1) {
							$pendingorders['orders'][$i]['increment_id'] = $order['increment_id'];
							$pendingorders['orders'][$i]['order_status'] = $order['status'];
							$pendingorders['orders'][$i]['grand_total'] = $order['grand_total'];
							$pendingorders['orders'][$i]['created_at'] = $order['created_at'];
							
							$i++;
						}*/
					}
					if ($order['status'] == "pending_to_marketing" OR $order['status'] == "pending_to_finance" OR $order['status'] == "pending_to_management") {
						// need to cancel these orders if 4 days have passed since they're in this state
						// $roundofftonext = date("Y-m-d", strtotime($order['created_at']) + (1 * 24 * 60 * 60));
						$ordered_on = strtotime($order['created_at']);

						// compute order time + 4 days of the vendor
						$vendor_deadline = $ordered_on + (4 * 24 * 60 * 60);

						$status = $this->checkCreditLimitStatus($vendor_deadline, $time_array);

						if ($status == 0) {
							// 4 days have passed since this order is pending
							$pendingorders['email'] = $vendorDetails['email'];
							$pendingorders['prefix'] = $vendorDetails['prefix'];
							$pendingorders['firstname'] = $vendorDetails['firstname'];
							$pendingorders['lastname'] = $vendorDetails['lastname'];
							$pendingorders['vendor_store_name'] = $vendorDetails['vendor_store_name'];
							$pendingorders['date'] = $order['created_at'];
							$pendingorders['vendor_deadline'] = $vendor_deadline;
							$pendingorders['vendor_status'] = $status;
							$pendingorders['vendor_id'] = $vendorId;
							
							$pendingorders['increment_id'] = $order['increment_id'];
							$pendingorders['order_status'] = $order['status'];
							$pendingorders['grand_total'] = $order['grand_total'];
							$pendingorders['created_at'] = $order['created_at'];

							$this->cancelSubsequentPendingOrder($pendingorders);
						}
					}

					// $pendingorders = accumulateTotal($pendingorders);
					// print_r($pendingorders);

				}
			
			}
			
		}

		// print_r($adminReportReminders);
		// adminReminderMailBody($adminReportReminders);
		$this->sendAdminReminderSummaryMail($adminReportReminders);
		// $adminCount = 0;
    }

	// function to generate timestamps
	public function generateTimestamps() {
		// generate timestamps for days and return an array
		$sevendays_time = time() + (7 * 24 * 60 * 60);
		$sixdays_time = time() + (6 * 24 * 60 * 60);
		$fivedays_time = time() + (5 * 24 * 60 * 60);
		$fourdays_time = time() + (4 * 24 * 60 * 60);
		$threedays_time = time() + (3 * 24 * 60 * 60);
		$twodays_time = time() + (2 * 24 * 60 * 60);
		$onedays_time = time() + (1 * 24 * 60 * 60);
		$todays_time = time();
		
		$time_array = array('sevendays_time' => $sevendays_time , 'sixdays_time' => $sixdays_time , 'fivedays_time' => $fivedays_time , 'fourdays_time' => $fourdays_time , 'threedays_time' => $threedays_time , 'twodays_time' => $twodays_time , 'onedays_time' => $onedays_time , 'todays_time' => $todays_time);
		
		return $time_array;
	}

	// function to check credit limit status for the current order by a vendor
	public function checkCreditLimitStatus($vendor_deadline, $time_array) {
		// check if vendor deadline is approaching
		if ($vendor_deadline > $time_array['sevendays_time']) {
			// vendor deadline is more than a week away
			$status = 8;
		}
		else {
			if ($vendor_deadline <= $time_array['todays_time']) {
				// vendor's deadline is crossed.
				$status = 0;
			}
			elseif ($vendor_deadline <= $time_array['onedays_time']) {
				// vendor has one day left.
				$status = 1;
			}
			elseif ($vendor_deadline <= $time_array['twodays_time']) {
				// vendor has two days left.
				$status = 2;
			}
			elseif ($vendor_deadline <= $time_array['threedays_time']) {
				// vendor has three days left.
				$status = 3;
			}
			elseif ($vendor_deadline <= $time_array['fourdays_time']) {
				// vendor has four days left.
				$status = 4;
			}
			elseif ($vendor_deadline <= $time_array['fivedays_time']) {
				// vendor has five days left.
				$status = 5;
			}
			elseif ($vendor_deadline <= $time_array['sixdays_time']) {
				// vendor has five days left.
				$status = 6;
			}
			elseif ($vendor_deadline <= $time_array['sevendays_time'] AND $vendor_deadline >= $time_array['sixdays_time']) {
				// vendor has seven days left.
				$status = 7;
			}
			else {
				// five days left, not sending a mail on this day
				$status = 8;
			}
		}
		return $status;
	}

	// generate accumulated pending orders data
	public function accumulateTotal($pendingorders) {
		$completetotal = 0;
		foreach($pendingorders['orders'] as $pendingorder) {
			$completetotal = $completetotal + $pendingorder['grand_total'];
		}
		$pendingorders['completetotal'] = $completetotal;
		return $pendingorders;
	}

	// send a reminder mail to the vendor and admin
	public function sendReminderMail($details) {
		// send mail to the vendor
		$admin_email = Mage::getStoreConfig('trans_email/ident_general/email');
		$vendor_mail = new Zend_Mail();
		$vendor_email = $details['email'];
		$vendor_subject = "Your Order Reminder";							
		$vendor_body = $this->vendorReminderMailBody($details);
		$vendor_mail->setSubject($vendor_subject);
		$vendor_mail->setBodyText($vendor_body);
		$vendor_mail->setFrom($admin_email, "Happily Unmarried");				
		$vendor_mail->addTo($vendor_email);
		$vendor_mail->send();
	}

	// instead of spamming admin with a lot of mails, one mail sending a daily report would suffice
	public function sendAdminReminderSummaryMail($fullSummary) {
		// send mail to the admin
		$mail = new Zend_Mail();
		$admin_email = Mage::getStoreConfig('trans_email/ident_general/email');
		$admin_subject = "Credit Limit Reminders Summary";
		$admin_autobody = $this->adminReminderMailBody($fullSummary);
		$mail->setSubject($admin_subject);							    
		$mail->setBodyText($admin_autobody);
		$mail->setFrom($admin_email, "Happily Unmarried");							   
		$mail->addTo($admin_email);
		$mail->send();
	}

	// send cancellation mail to the vendor when he has failed to pay and his credit limit has expired
	public function sendCancellationMail($details) {
		
		// cancel any existing orders
		$orderIncrementId = $details["increment_id"];
		$orderLoad = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
		// echo "<pre>"; print_r($order->getData()); exit;
		$orderLoad->setData('state', "canceled");
		$orderLoad->setStatus("canceled_payment_failed");
		$history = $orderLoad->addStatusHistoryComment('Vendor failed to pay before his credit limit expired', false);
		$history->setIsCustomerNotified(false);
		$orderLoad->save();
		
		// deactivate vendor account
		$customerModel = Mage::getModel('customer/customer');
		$attr = $customerModel->getResource()->getAttribute("vendor_blacklisted");

		if ($attr->usesSource()) {
			$valueYes = $attr->getSource()->getOptionId(strval('Yes'));
			$valueNo = $attr->getSource()->getOptionId(strval('No'));
		}
			
		$vendorId = $details['vendor_id'];
		$vendorAccount = Mage::getModel('customer/customer')->load($vendorId);
		$activationStatus = $valueYes;
		$vendorAccount->setVendorBlacklisted($activationStatus)->save();
		// $activationStatus = '0';
		// $vendorAccount->setCustomerActivated($activationStatus)->save();
		
		// send mail to the admin
		$mail = new Zend_Mail();
		$admin_email = Mage::getStoreConfig('trans_email/ident_general/email');
		$admin_subject = "Credit Limit Expired: " . $details['firstname'] . " " . $details['lastname'];
		$admin_autobody = $this->adminCancellationMailBody($details);
		$mail->setSubject($admin_subject);							    
		$mail->setBodyText($admin_autobody);
		$mail->setFrom($admin_email, "Happily Unmarried");							   
		$mail->addTo($admin_email);
		$mail->send();
		
		// send mail to the vendor
		$vendor_mail = new Zend_Mail();
		$vendor_email = $details['email'];
		$vendor_subject = "Your Account has been Deactivated due to Failure of Payment";							
		$vendor_body = $this->vendorCencellationMailBody($details);
		$vendor_mail->setSubject($vendor_subject);
		$vendor_mail->setBodyText($vendor_body);
		$vendor_mail->setFrom($admin_email, "Happily Unmarried");				
		$vendor_mail->addTo($vendor_email);
		$vendor_mail->send();
	}

	// cancel order pending with marketing / finance / management for over 4 days
	public function cancelSubsequentPendingOrder($details) {
		
		$orderIncrementId = $details["increment_id"];
		$orderLoad = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
		// echo "<pre>"; print_r($order->getData()); exit;
		$orderLoad->setData('state', "canceled");
		$orderLoad->setStatus("canceled_automatic");
		$history = $orderLoad->addStatusHistoryComment('Order approval pending for more than 4 days, Order cancelled automatically', false);
		$history->setIsCustomerNotified(false);
		$orderLoad->save();
		
		// send mail to the admin
		$mail = new Zend_Mail();
		$admin_email = Mage::getStoreConfig('trans_email/ident_general/email');
		$admin_subject = "Pending order without approval cancelled: " . $details['vendor_store_name'];
		$admin_autobody = $this->adminPendingAutomaticCancellationMailBody($details);
		$mail->setSubject($admin_subject);							    
		$mail->setBodyText($admin_autobody);
		$mail->setFrom($admin_email, "Happily Unmarried");							   
		$mail->addTo($admin_email);
		$mail->send();

		// send mail to the vendor
		$vendor_mail = new Zend_Mail();
		$vendor_email = $details['email'];
		$vendor_subject = "Your order has been cancelled";							
		$vendor_body = $this->vendorPendingAutomaticCencellationMailBody($details);
		$vendor_mail->setSubject($vendor_subject);
		$vendor_mail->setBodyText($vendor_body);
		$vendor_mail->setFrom($admin_email, "Happily Unmarried");				
		$vendor_mail->addTo($vendor_email);
		$vendor_mail->send();
	}

	// reminder mail body (vendor)
	public function vendorReminderMailBody($details) {
		$message = "";
		$message .= $details['vendor_store_name'] . "\n\n";
		if ($details['prefix']) {
			$message = "Dear " . $details['prefix'] . " " . $details['firstname'] . " " . $details['lastname'] . " " . "\n";
		} else {
			$message = "Dear " . $details['firstname'] . " " . $details['lastname'] . " " . "\n";
		}
		$message .= "You have " . $details['vendor_status'] . " days remaining to pay your credit amount on your following Order\n\n";
		$message .= "Details:\n\n";
		
		// loop this for all orders
		// foreach ($details['orders'] as $order) {
			$message .= "Order No: " . $details['increment_id'] . "\n";
			$message .= "Payment Status: " . $details['order_status'] . "\n";
			$message .= "Order Date: " . $details['created_at'] . "\n";
			$message .= "Order Amount: Rs. " . $details['grand_total'] . "\n\n";
		// }
		
		// $message .= "Total Amount Pending: Rs. " . $details['completetotal'] . "\n\n";
		
		$message .= "Please note, your deadline to pay the full amount for your orders is " . date("Y-m-d", $details['vendor_deadline']) . ", after which your account may be dicontinued and all your pending orders may be cancelled.\n";
		
		return $message;
	}

	// reminder mail body (admin)
	/* public function adminReminderMailBody($fullSummary) {
		$message = "";
		foreach($fullSummary as $details) {
			if ($details['prefix']) {
				$message .= "Vendor Name: " . $details['prefix'] . " " . $details['firstname'] . " " . $details['lastname'] . " " . "\n";
			} else {
				$message .= "Vendor Name: " . $details['firstname'] . " " . $details['lastname'] . " " . "\n";
			}
			$message .= "Vendor has " . $details['vendor_status'] . " days remaining to pay the credit amount on their following order-\n\n";
			$message .= "Details:\n\n";
			
			// loop this for all orders
			// foreach ($details['orders'] as $order) {
				$message .= "Order No: " . $details['increment_id'] . "\n";
				$message .= "Payment Status: " . $details['order_status'] . "\n";
				$message .= "Order Date: " . $details['created_at'] . "\n";
				$message .= "Order Amount: Rs. " . $details['grand_total'] . "\n\n";
			// }
			
			$message .= "Total Amount Pending: Rs. " . $details['grand_total'] . "\n\n";
			
			$message .= "Vendor's deadline to pay the full amount for his orders is " . date("Y-m-d", $details['vendor_deadline']) . "\n\n\n";
			
		}
		return $message;
	} */

	// reminder mail body (admin)
	public function adminReminderMailBody($fullSummary) {
		
		// $adminReportReminders[$vendorId]['details']['email'] = $vendorDetails['email'];
		// $adminReportReminders[$vendorId]['details']['prefix'] = $vendorDetails['prefix'];
		// $adminReportReminders[$vendorId]['details']['firstname'] = $vendorDetails['firstname'];
		// $adminReportReminders[$vendorId]['details']['lastname'] = $vendorDetails['lastname'];
		
		// $adminReportReminders[$vendorId]['orders'][$orderIncrementId]['increment_id'] = $order['increment_id'];
		// $adminReportReminders[$vendorId]['orders'][$orderIncrementId]['order_status'] = $order['status'];
		// $adminReportReminders[$vendorId]['orders'][$orderIncrementId]['grand_total'] = $order['grand_total'];
		// $adminReportReminders[$vendorId]['orders'][$orderIncrementId]['created_at'] = $order['created_at'];
		// $adminReportReminders[$vendorId]['orders'][$orderIncrementId]['vendor_deadline'] = $vendor_deadline;
		// $adminReportReminders[$vendorId]['orders'][$orderIncrementId]['vendor_status'] = $status;

		$message = "";
		foreach($fullSummary as $vendor) {
			$message .= $vendor['details']['vendor_store_name'] . "\n\n";
			if ($vendor['details']['prefix']) {
				$message .= "Vendor Name: " . $vendor['details']['prefix'] . " " . $vendor['details']['firstname'] . " " . $vendor['details']['lastname'] . " " . "\n\n";
			} else {
				$message .= "Vendor Name: " . $vendor['details']['firstname'] . " " . $vendor['details']['lastname'] . " " . "\n\n";
			}

			$completeTotal = 0;
			foreach($vendor['orders'] as $order) {
				$message .= "Vendor has " . $order['vendor_status'] . " days remaining to pay the credit amount on their following order-\n";
				$message .= "Details:\n\n";
				
				$message .= "Order No: " . $order['increment_id'] . "\n";
				$message .= "Payment Status: " . $order['order_status'] . "\n";
				$message .= "Order Date: " . $order['created_at'] . "\n";
				$message .= "Order Amount: Rs. " . $order['grand_total'] . "\n\n";
				
				$message .= "Vendor's deadline to pay the full amount for this order is " . date("Y-m-d", $order['vendor_deadline']) . "\n\n";

				$completeTotal = $completeTotal + $order['grand_total'];
			}
			$message .= "\n" . "Total Amount Pending: Rs. " . $completeTotal . "\n\n";
			
		}
		return $message;
	}

	// alarm mail body (vendor)
	public function vendorCencellationMailBody($details) {
		$message = "";
		$message .= $details['vendor_store_name'] . "\n\n";
		if ($details['prefix']) {
			$message = "Dear " . $details['prefix'] . " " . $details['firstname'] . " " . $details['lastname'] . " " . "\n";
		} else {
			$message = "Dear " . $details['firstname'] . " " . $details['lastname'] . " " . "\n";
		}
		$message .= "You have failed to pay your credit amount on your following Order-\n\n";
		$message .= "Details:\n\n";
		
		// loop this for all orders
		// foreach ($details['orders'] as $order) {
			$message .= "Order No: " . $details['increment_id'] . "\n";
			$message .= "Payment Status: " . $details['order_status'] . "\n";
			$message .= "Order Date: " . $details['created_at'] . "\n";
			$message .= "Order Amount: Rs. " . $details['grand_total'] . "\n\n";
		// }
		
		// $message .= "Total Amount: Rs. " . $details['completetotal'] . "\n\n";
		
		$message .= "Your account has been deactivated and all your pending orders have been cancelled. Your deadline was: " . date("Y-m-d", $details['vendor_deadline']) . "\n";
		
		return $message;
	}

	// alarm mail body (admin)
	public function adminCancellationMailBody($details) {
		$message = "";
		$message .= $details['vendor_store_name'] . "\n\n";
		if ($details['prefix']) {
			$message = "Vendor Name: " . $details['prefix'] . " " . $details['firstname'] . " " . $details['lastname'] . " " . "\n";
		} else {
			$message = "Vendor Name: " . $details['firstname'] . " " . $details['lastname'] . " " . "\n";
		}
		$message .= "Vendor has failed to pay his credit amount on his following Order\n\n";
		$message .= "Details:\n\n";
		
		// loop this for all orders
		// foreach ($details['orders'] as $order) {
			$message .= "Order No: " . $details['increment_id'] . "\n";
			$message .= "Payment Status: " . $details['order_status'] . "\n";
			$message .= "Order Date: " . $details['created_at'] . "\n";
			$message .= "Order Amount: Rs. " . $details['grand_total'] . "\n\n";
		// }
		
		// $message .= "Total Amount: Rs. " . $details['completetotal'] . "\n\n";
		
		$message .= "The vendor's account has been deactivated and all his pending orders have been cancelled. Vendor's deadline was: " . date("Y-m-d", $details['vendor_deadline']) . "\n";
		
		return $message;
	}

	// pending cancellation mail body (vendor)
	public function vendorPendingAutomaticCencellationMailBody($details) {
		$message = "";
		$message .= $details['vendor_store_name'] . "\n\n";
		if ($details['prefix']) {
			$message = "Dear " . $details['prefix'] . " " . $details['firstname'] . " " . $details['lastname'] . " " . "\n";
		} else {
			$message = "Dear " . $details['firstname'] . " " . $details['lastname'] . " " . "\n";
		}
		$message .= "Your following Order has been cancelled as it was not approved within the stipulated time period-\n\n";
		$message .= "Details:\n\n";
		
		// loop this for all orders
		// foreach ($details['orders'] as $order) {
			$message .= "Order No: " . $details['increment_id'] . "\n";
			$message .= "Payment Status: " . $details['order_status'] . "\n";
			$message .= "Order Date: " . $details['created_at'] . "\n";
			$message .= "Order Amount: Rs. " . $details['grand_total'] . "\n\n";
		// }
		
		// $message .= "Total Amount: Rs. " . $details['completetotal'] . "\n\n";
		
		$message .= "Order was automatically cancelled." . "\n";
		
		return $message;
	}

	// pending cancellation mail body (admin)
	public function adminPendingAutomaticCancellationMailBody($details) {
		$message = "";
		$message .= $details['vendor_store_name'] . "\n\n";
		if ($details['prefix']) {
			$message = "Vendor Name: " . $details['prefix'] . " " . $details['firstname'] . " " . $details['lastname'] . " " . "\n";
		} else {
			$message = "Vendor Name: " . $details['firstname'] . " " . $details['lastname'] . " " . "\n";
		}
		$message .= "Vendor had placed the following Order\n\n";
		$message .= "Details:\n\n";
		
		// loop this for all orders
		// foreach ($details['orders'] as $order) {
			$message .= "Order No: " . $details['increment_id'] . "\n";
			$message .= "Payment Status: " . $details['order_status'] . "\n";
			$message .= "Order Date: " . $details['created_at'] . "\n";
			$message .= "Order Amount: Rs. " . $details['grand_total'] . "\n\n";
		// }
		
		// $message .= "Total Amount: Rs. " . $details['completetotal'] . "\n\n";
		
		$message .= "This order was pending approval. Order has been automatically cancelled as the order was not approved within 4 days." . "\n";
		
		return $message;
	}
	
}