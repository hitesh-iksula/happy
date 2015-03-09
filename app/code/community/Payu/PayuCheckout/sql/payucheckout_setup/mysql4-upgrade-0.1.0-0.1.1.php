<?php
$installer = $this;
$installer->startSetup();
$installer->run("

ALTER TABLE `{$installer->getTable('sales/quote_payment')}` ADD `payu_payment_method` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `{$installer->getTable('sales/quote_payment')}` ADD `payu_method_option` VARCHAR( 255 ) NOT NULL ;

ALTER TABLE `{$installer->getTable('sales/order_payment')}` ADD `payu_payment_method` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `{$installer->getTable('sales/order_payment')}` ADD `payu_method_option` VARCHAR( 255 ) NOT NULL ;

");
$installer->endSetup();