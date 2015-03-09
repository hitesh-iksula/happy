<?php
$installer = $this;
$installer->startSetup();
$installer->run("CREATE TABLE IF NOT EXISTS `suggest` (
  `suggest_id` int(11) NOT NULL auto_increment,
  `name` varchar(100) default NULL,
  `email_id` varchar(100) default NULL,
  `mob_no` varchar(255) default NULL,
  `date_registered` DATETIME DEFAULT NULL,
  `verified` int(11) default '0',
  `created_account` int(11) default '0',
  PRIMARY KEY (`suggest_id`)
)");
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 