<?php 
class Manoj_Pincode_Model_Mycustomoptions
{
    public function toOptionArray()
    {
     
       $payments = Mage::getSingleton('payment/config')->getActiveMethods();
       $methods = array(array('value'=>'', 'label'=>Mage::helper('adminhtml')->__('--Please Select--')));
       foreach ($payments as $paymentCode=>$paymentModel) {
        $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
        $methods[$paymentCode] = array(
            'label'   => $paymentTitle,
            'value' => $paymentCode,
            );
    }
    return $methods;
    }

     public function shippingCarrierArray()
    {
     
       $payments = Mage::getSingleton('payment/config')->getActiveMethods();
       $methods = array(array('value'=>'', 'label'=>Mage::helper('adminhtml')->__('--Please Select--')));
       foreach ($payments as $paymentCode=>$paymentModel) {
        $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
        $methods[$paymentCode] = array(
            'label'   => $paymentTitle,
            'value' => $paymentCode,
            );
    }
    return $methods;
    }

}
