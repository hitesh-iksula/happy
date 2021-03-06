<?php
class Magestore_Giftvoucher_Block_Order_Totals extends Mage_Core_Block_Template
{
	public function initTotals(){
		$orderTotalsBlock = $this->getParentBlock();
		$order = $orderTotalsBlock->getOrder();
		if ($order->getGiftVoucherDiscount()){
			$orderTotalsBlock->addTotal(new Varien_Object(array(
				'code'	=> 'giftvoucher',
				'label'	=> $this->__('Gift Voucher (%s)',$order->getGiftCodes()),
				'value'	=> -$order->getGiftVoucherDiscount(),
			)),'subtotal');
		}
	}
}