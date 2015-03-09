<?php
/**
 *
 * BSchool Campaign
 * 
 * Index Action for the Intermediate Page between Cart and Checkout
 * 
 * @ IndexAction() -> Load layout as laid by the handle in advancedbundle.xml
 * 
 * 
 * 
 */
?>

<?php

class Iksula_AdvancedBundle_IndexController extends Mage_Core_Controller_Front_Action {

	public function IndexAction() {

		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('Shopping Cart'));

        $this->renderLayout();

	}

}