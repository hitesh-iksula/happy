<?php
class Iksula_Mih_Helper_Data extends Mage_Core_Helper_Abstract
{

	public $_mihProductId = 1226;

	public function getMihId() {
		return $this->_mihProductId;
	}

	public function getRateText($title) {
		$html = '';
		if($title == 'two to a room') {
			$html .= '<div class="rate-row-1">';
			$html .= '<span class="WebRupee">Rs.</span>';
			$html .= '26,500';
			$html .= '</div>';
			$html .= '<div class="rate-row-2">';
			$html .= '<span class="WebRupee">Rs.</span>13,250 (per guest) X 2';
			$html .= '</div>';
		} elseif($title == 'three to a room') {
			$html .= '<div class="rate-row-1">';
			$html .= '<span class="WebRupee">Rs.</span>';
			$html .= '37,500';
			$html .= '</div>';
			$html .= '<div class="rate-row-2">';
			$html .= '<span class="WebRupee">Rs.</span>12,500 (per guest) X 3';
			$html .= '</div>';
		} elseif($title == 'kids between 5 - 10') {
			$html .= '<div class="rate-row-1">';
			$html .= '<span class="WebRupee">Rs.</span>';
			$html .= '5,000';
			$html .= '</div>';
		}
		return $html;
	}
	
	public function getOptionInformationText($title) {
		$twoInARoom = "2 guests in a nice comfortable room. Now multiply this into the number of rooms that you require. Quick tip: don't forget your kids. If you have kids, then select the number from the kids section below and remember to take them back after the festival.";
		$threeInARoom = "Mih is best enjoyed with your gang,  You’re a group of 6 people, take 2 rooms of 3 people each. We will try to give you rooms close by but no promises here.";
		$kids = "We mean age not number. Choose the exact number of kids above 5 that you have and let website do the math. Remember, God is watching you and so are the hotel authorities.  So please don't try to pass your 7 year old as the one under 5. We told you MiH is fun.";
		$description = '';
		if($title == 'two to a room') {
			$description = $twoInARoom;
		} elseif($title == 'three to a room') {
			$description = $threeInARoom;
		} elseif($title == 'kids between 5 - 10') {
			$description = $kids;
		}
		return $description;
	}

	public function isMihInCart() {
		$cartItems = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
		foreach($cartItems as $cartItem) {
			if($cartItem->getProductId() == $this->getMihId()) {
				return true;
			}
		}
		return false;
	}

}
	 