<?php
class Iksula_Impressionsemail_Model_Mysql4_Impressionsemail extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("impressionsemail/impressionsemail", "impressions_id");
    }
}