<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Qcheckout
*/

class Amasty_Audit_Model_Geolocation extends Varien_Object
{
    public function locate($ip)
    {
        if (Mage::getModel('amaudit/import')->isDone() && Mage::getStoreConfig('amaudit/geoip/use') == 1)
        {
            $longIP = $ip;

            $db = Mage::getSingleton('core/resource')->getConnection('core_read');
            $select = $db->select()
                ->from(array('l' => Mage::getSingleton('core/resource')->getTableName('amaudit/location')))
                ->join(
                    array('b' => Mage::getSingleton('core/resource')->getTableName('amaudit/block')),
                    'b.geoip_loc_id = l.geoip_loc_id',
                    array()
                )
                ->where("$longIP between b.start_ip_num and b.end_ip_num")
                ->limit(1)
            ;

            if ($res = $db->fetchRow($select))
                $this->setData($res);
        }

        return $this;
    }

    public function getLocation($ip)
    {
        $location = $this->locate(ip2long($ip));

        $countries = Mage::getModel('directory/country')->getResourceCollection()
            ->loadByStore()->toOptionArray();
        foreach ($countries as $id => $country) {
            if ($country['value'] == $location->getCountry()) {
                $countryLabel = $country['label'];
                $countryId = $country['value'];
            }
        }
        $cityLabel = $location->getCity();
        $locationString = '';
        if ($countryLabel != ' ') {
            $locationString = $countryLabel;
            if ($cityLabel != '') {
                $locationString = $locationString.', '.$cityLabel;
            }
        }
        return array('locationString' => $locationString, 'countryId' => $countryId, 'countryLabel' => $countryLabel);
    }
}
