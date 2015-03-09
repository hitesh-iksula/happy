<?php

class Iksula_Folofo_IndexController extends Mage_Core_Controller_Front_Action{

	public function IndexAction() {

		$this->loadLayout();
		$this->getLayout()->getBlock("head")->setTitle($this->__("About Folofo"));

		/*$breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");

		$breadcrumbs->addCrumb("home", array(
			"label" => $this->__("Home Page"),
			"title" => $this->__("Home Page"),
			"link"  => Mage::getBaseUrl()
		));

		$breadcrumbs->addCrumb("about folofo", array(
			"label" => $this->__("About Folofo"),
			"title" => $this->__("About Folofo")
		));*/

		$this->renderLayout();

	}

}
