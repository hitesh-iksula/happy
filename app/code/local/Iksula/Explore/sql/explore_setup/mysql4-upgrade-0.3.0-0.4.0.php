<?php
$installer = $this;
$installer->startSetup();

$installer->run("CREATE TABLE IF NOT EXISTS `contact_us` (
	`id` int(11) NOT NULL auto_increment,
	`name` varchar(100) default NULL,
	`email` varchar(100) default NULL,
	`comment` varchar(255) default NULL,
	`date_registered` DATETIME DEFAULT NULL,
	`data` varchar(255) default NULL,
	PRIMARY KEY (`id`)
)");

$installer->endSetup();

