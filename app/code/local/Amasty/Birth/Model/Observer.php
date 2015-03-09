<?php
/**
 * @author Amasty
 */
class Amasty_Birth_Model_Observer
{
    
    public function send()
    {
        if (Mage::getStoreConfig('ambirth/general/enabled')){
            $this->_sendBirthdayCoupon();
            $this->_sendRegistrationCoupon();
        }
        
        $this->_removeOldCoupons();
			        
        return $this;
    }  
    
    protected function _getCollection()
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->setPageSize(200)
            ->setCurPage(1);
            
        return $collection;
    }

    protected function _sendBirthdayCoupon()
    {
        $days = intVal(Mage::getStoreConfig('ambirth/general/birth_days'));
        $time = time(); 
        if ($days > 0) // afer birthday
            $time = strtotime("-$days days");
        else {
            $days = abs($days);
            $time = strtotime("+$days days");
        }
        
        $collection = $this->_getCollection()
            ->addAttributeToSelect('dob')
            ->addAttributeToFilter('dob', array('field_expr'=>"DATE_FORMAT(#?, '%m-%d')", 'eq'=>date('m-d', $time)))             
            ->load();
        
        foreach ($collection as $customer){
		    $this->_emailToCustomer($customer, 'birth');
        }  
    }
    
    protected function _sendRegistrationCoupon()
    {
        $days = intVal(Mage::getStoreConfig('ambirth/general/days'));
        if ($days < 0)
            return;
            
        $collection = $this->_getCollection()
            ->addFieldToFilter('created_at', array('field_expr'=>"DATE_FORMAT(#?, '%Y-%m-%d')", 'eq'=>date('Y-m-d', strtotime("-$days days"))))             
            ->load();
        
        foreach ($collection as $customer){
		    $this->_emailToCustomer($customer, 'reg');
        }  
    }
    
    protected function _createCoupon($store)
    {
      	$couponData = array();
        $couponData['name']      = 'Birthday Coupon ' . date('Y-m-d');
        $couponData['is_active'] = 1;
        // all websites here:
        $couponData['website_ids'] =  array_keys(Mage::app()->getWebsites(true));
        
        $couponData['coupon_type'] = 2;  // for mahento 1.4.1.1
        $couponData['coupon_code'] = strtoupper(uniqid()); 
        
        $maxUses = intVal(Mage::getStoreConfig('ambirth/general/coupon_uses'));
        $couponData['uses_per_coupon']   = $maxUses;
        $couponData['uses_per_customer'] = $maxUses;
        
        $couponData['from_date'] = ''; //current date

        $days = Mage::getStoreConfig('ambirth/general/coupon_days', $store);
        //$date = Mage::helper('core')->formatDate(date('Y-m-d', time() + $days*24*3600));
        
        $date = date('Y-m-d', time() + $days*24*3600);
        $couponData['to_date'] = $date;
        
        $couponData['simple_action']   = Mage::getStoreConfig('ambirth/general/coupon_type', $store);
        $couponData['discount_amount'] = Mage::getStoreConfig('ambirth/general/coupon_amount', $store);
        $couponData['conditions'] = array(
            1 => array(
                'type'       => 'salesrule/rule_condition_combine',
                'aggregator' => 'all',
                'value'      => 1,
                'new_child'  =>'', 
            )
        );
        
        $couponData['actions'] = array(
            1 => array(
                'type'       => 'salesrule/rule_condition_product_combine',
                'aggregator' => 'all',
                'value'      => 1,
                'new_child'  =>'', 
            )
        );
        
        //create for all customer groups
        $couponData['customer_group_ids'] = array();
        //compatibility with aitoc's individ promo module
        $couponData['customer_individ_ids'] = array();
        
        $customerGroups = Mage::getResourceModel('customer/group_collection')
            ->load();

        $found = false;
        foreach ($customerGroups as $group) {
            if (0 == $group->getId()) {
                $found = true;
            }
            $couponData['customer_group_ids'][] = $group->getId();
        }
        if (!$found) {
            $couponData['customer_group_ids'][] = 0;
        }
        
        try { 
            Mage::getModel('salesrule/rule')
                ->loadPost($couponData)
                ->save();      
        } 
        catch (Exception $e){
            //print_r($e); exit;
            $couponData['coupon_code'] = '';   
        }
        return $couponData['coupon_code'];

    }   

    protected function _emailToCustomer($customer, $type)
    {
		$logCollection = Mage::getResourceModel('ambirth/log_collection')
			->addFieldToFilter('type', $type)
			->addFieldToFilter('customer_id', $customer->getId());
			
	    if ('birth' == $type)
	       $logCollection->addFieldToFilter('y', date('Y'));
			
		if ($logCollection->getSize() > 0)
		  return;
        
        $tplName = ('birth' == $type ? 'template' : 'template2');
        
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        
        $store = Mage::app()->getStore($customer->getStoreId());
        $tpl = Mage::getModel('core/email_template');
        $tpl->setDesignConfig(array('area'=>'frontend', 'store'=>$store->getId()))
            ->sendTransactional(
                Mage::getStoreConfig('ambirth/general/' . $tplName, $store),
                Mage::getStoreConfig('ambirth/general/identity', $store),
                $customer->getEmail(),
                $customer->getName(),
                array(
                    'website_name'  => $store->getWebsite()->getName(),
                    'group_name'    => $store->getGroup()->getName(),
                    'store_name'    => $store->getName(), 
                    'coupon'        => $this->_createCoupon($store), 
                    'coupon_days'   => Mage::getStoreConfig('ambirth/general/coupon_days', $store), 
                    'customer_name' => $customer->getName(),
                )
        ); 
        $logModel = Mage::getModel('ambirth/log')
			->setY(date('Y'))
			->setType($type)
			->setCustomerId($customer->getId())
			->setSentDate(date('Y-m-d H:i:s'));
	    $logModel->save();	      
        
        $translate->setTranslateInline(true);
    }  
     
    protected function _removeOldCoupons()
    {
        $days = intVal(Mage::getStoreConfig('ambirth/general/remove_days'));
        if ($days <= 0)
            return;
            
        $rules = Mage::getResourceModel('salesrule/rule_collection')
            ->addFieldToFilter('name', array('like'=>'Birthday Coupon%'))
            ->addFieldToFilter('from_date', array('lt' => date('Y-m-d', strtotime("-$days days"))))
            ;
         
        $errors = '';       
        foreach ($rules as $rule){
            try {
                $rule->delete();
            } 
            catch (Exception $e) {
                $errors .= "\r\nError when deleting rule #" . $rule->getId() . ' : ' . $e->getMessage();    
            }
        }
    }
    
}
