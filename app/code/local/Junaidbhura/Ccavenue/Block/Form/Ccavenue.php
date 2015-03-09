<?php
class Junaidbhura_Ccavenue_Block_Form_Ccavenue extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ccavenue/form/options.phtml');
    }
}