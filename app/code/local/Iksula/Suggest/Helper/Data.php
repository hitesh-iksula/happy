<?php
class Iksula_Suggest_Helper_Data extends Mage_Core_Helper_Abstract {

	public function getCalculatedShippingAmount($paymentMethod, $cartPrice) {

		$calculatedShippingAmount = 0;

		$prepaidBase = 50;
		$prepaidRatio = 10;
		$prepaidThreshold = 2500;

		$prepaidRatioAmount = ($cartPrice * $prepaidRatio) / 100;
		if($prepaidRatioAmount > $prepaidBase) {
			$calculatedShippingAmount = $prepaidRatioAmount;
		} else {
			$calculatedShippingAmount = $prepaidBase;
		}

		if($cartPrice > $prepaidThreshold) {
			$calculatedShippingAmount = 0;
		}

		return $calculatedShippingAmount;

	}

	public function OLD__getCalculatedShippingAmount($paymentMethod, $cartPrice) {

		$calculatedShippingAmount = 0;

		$codBase = 75;
		$codRatio = 10;

		$prepaidBase = 50;
		$prepaidRatio = 10;
		$prepaidThreshold = 2500;

		if($paymentMethod == 'ccavenue' OR $paymentMethod == 'wallet') {

			$prepaidRatioAmount = ($cartPrice * $prepaidRatio) / 100;
			if($prepaidRatioAmount > $prepaidBase) {
				$calculatedShippingAmount = $prepaidRatioAmount;
			} else {
				$calculatedShippingAmount = $prepaidBase;
			}

			if($cartPrice > $prepaidThreshold) {
				$calculatedShippingAmount = 0;
			}

		}

		if($paymentMethod == 'cashondelivery') {

			$calculatedShippingAmount = $codBase + (($cartPrice * $codRatio) / 100);

		}

		// $calculatedShippingAmount = 111;
		return $calculatedShippingAmount;

	}

	public function getShippingCalculationRules() {

		//

	}

}
