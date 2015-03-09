<?php

class Iksula_Explore_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('explore')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('explore')->__('Disabled')
        );
    }
}