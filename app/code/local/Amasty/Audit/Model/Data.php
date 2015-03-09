<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2013 Amasty (http://www.amasty.com)
 * @package Amasty_Audit
 */
class Amasty_Audit_Model_Data extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('amaudit/data', 'entity_id');
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        if (!Mage::helper('core')->isModuleEnabled('Mage_Directory')) {
            return parent::_beforeSave();
        }
        $senderEmail = Mage::getStoreConfig('amaudit/suspicious_log_mailing/send_to_mail');
        $receiveUnsuccessfulEmail = Mage::getStoreConfig('amaudit/unsuccessful_log_mailing/send_to_mail');
        $receiveSuspiciousEmail = Mage::getStoreConfig('amaudit/suspicious_log_mailing/send_to_mail');
        $ip = $this->getIp();
//        $ip = '197.157.244.0';//somali
//        $ip = '213.184.225.37';//minsk
//        $ip = '208.122.53.131';//NY
        $locationModel = Mage::getSingleton('amaudit/geolocation');
        $location = $locationModel->getLocation($ip);
        $locationString = isset($location['locationString']) ? $location['locationString'] : null;
        $countryLabel = ($location['countryLabel'] != ' ') ? $location['countryLabel'] : 'Unknown';
        $countryId = isset($location['countryId']) ? $location['countryId'] : null;
        $this->addData(array('location' => $locationString, 'country_id' => $countryId));
        if ($senderEmail != '' && $receiveUnsuccessfulEmail != '' && $receiveSuspiciousEmail != '') {
            $dataCollection = Mage::getModel('amaudit/data')->getCollection();
            $time = Mage::getModel('core/date')->timestamp();
            $to = date('Y-m-d H:i:s', $time);
            $lastTime = $time - 604800; // 60*60*24*7
            $from = date('Y-m-d H:i:s', $lastTime);
            $dataCountCollection = Mage::getModel('amaudit/data')->getCollection();
            $dataCountCollection
                ->addFieldToFilter('date_time', array('from' => $from, 'to' => $to))
                ->addFieldToFilter('status', 1)
            ;
            $dataCollection
                ->addFieldToFilter('date_time', array('from' => $from, 'to' => $to))
                ->addFieldToFilter('country_id', $countryId)
            ;
            $allDataCount = $dataCountCollection->count();
            $domainName = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
            $dataCount = $dataCollection->count();
            if ($dataCount == 0 && $allDataCount >= 5) {
                /*Emailing suspicious logins to admin*/
                if (Mage::getStoreConfig('amaudit/suspicious_log_mailing/enabled')) {
                    $translate = Mage::getSingleton('core/translate');
                    $translate->setTranslateInline(false);

                    $vars = array(
                        'domain_name' => $domainName,
                        'country'     => $countryLabel,
                        'date_n_time' => Mage::getModel('core/date')->date('Y-m-d H:i:s'),
                        'curr_ip'     => $ip,
                        'username'    => $this->username,
                        'fullname'    => $this->name,
                    );

                    $sender = array(
                        'name'  => Mage::getStoreConfig('trans_email/ident_general/name', Mage::app()->getStore()->getId()),
                        'email' => Mage::getStoreConfig('trans_email/ident_general/email', Mage::app()->getStore()->getId()),
                    );
                    $receiveEmail = Mage::getStoreConfig('amaudit/suspicious_log_mailing/send_to_mail');
                    $receiveName = Mage::getStoreConfig('amaudit/suspicious_log_mailing/send_to_mail');

                    $store = Mage::app()->getStore();
                    $tpl = Mage::getModel('core/email_template');
                    $tpl->setDesignConfig(array('area' => 'frontend', 'store' => $store->getId()))
                        ->sendTransactional(
                            Mage::getStoreConfig('amaudit/suspicious_log_mailing/template'),
                            $sender,
                            $receiveEmail,
                            $receiveName,
                            $vars
                        )
                    ;

                    $translate->setTranslateInline(true);
                }
            }

            $latestSending = Mage::getStoreConfig('amaudit/unsuccessful_log_mailing/latest_sending');
            $hours = 1;
            $duration = $hours * 3600;
            if (($time - $latestSending) > $duration) {
                $unsuccessfulDataCollection = Mage::getModel('amaudit/data')->getCollection();
                $lastHour = $time - $duration;
                $fromHour = date('Y-m-d H:i:s', $lastHour);
                $unsuccessfulDataCollection
                    ->addFieldToFilter('date_time', array('from' => $fromHour, 'to' => $to))
                    ->addFieldToFilter('status', 0)
                ;

                $unsuccessLoginCount = 5;
                if ($unsuccessfulDataCollection->count() >= $unsuccessLoginCount) {
                    /*Emailing unsuccessful logins to admin*/
                    if (Mage::getStoreConfig('amaudit/unsuccessful_log_mailing/enabled')) {
                        $translate = Mage::getSingleton('core/translate');
                        $translate->setTranslateInline(false);

                        $vars = array(
                            'domain_name'           => $domainName,
                            'unsuccess_login_count' => $unsuccessLoginCount,
                            'hours'                 => $hours,
                            'hour_template'         => ($hours > 1) ? 'hours' : 'hour',
                        );

                        $sender = array(
                            'name'  => Mage::getStoreConfig('trans_email/ident_general/name', Mage::app()->getStore()->getId()),
                            'email' => Mage::getStoreConfig('trans_email/ident_general/email', Mage::app()->getStore()->getId()),
                        );
                        $receiveEmail = Mage::getStoreConfig('amaudit/unsuccessful_log_mailing/send_to_mail');
                        $receiveName = Mage::getStoreConfig('amaudit/unsuccessful_log_mailing/send_to_mail');

                        $store = Mage::app()->getStore();
                        $tpl = Mage::getModel('core/email_template');
                        $tpl->setDesignConfig(array('area' => 'frontend', 'store' => $store->getId()))
                            ->sendTransactional(
                                Mage::getStoreConfig('amaudit/unsuccessful_log_mailing/template'),
                                $sender,
                                $receiveEmail,
                                $receiveName,
                                $vars
                            )
                        ;

                        $translate->setTranslateInline(true);

                        Mage::getConfig()->saveConfig('amaudit/unsuccessful_log_mailing/latest_sending', $time);

                        Mage::getConfig()->cleanCache();

                    }
                }
            }
        }

        return parent::_beforeSave();
    }


}