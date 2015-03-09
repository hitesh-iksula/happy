<?php
class Iksula_Vendorcheckout_Block_Form_Vendorcheckout extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('vendorcheckout/form/vendorcheckout.phtml');
    }
}