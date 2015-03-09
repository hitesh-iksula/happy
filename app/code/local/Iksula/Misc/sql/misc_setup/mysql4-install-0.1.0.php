<?php
$installer = $this;
$installer->startSetup();

$installer->run("CREATE TABLE IF NOT EXISTS `product_minilog` (
  `id` int(11) NOT NULL auto_increment,
  `product_id` varchar(100) default NULL,
  `product_sku` varchar(100) default NULL,
  `date_updated` varchar(100) default NULL,
  PRIMARY KEY (`id`)
)");

$installer->endSetup();