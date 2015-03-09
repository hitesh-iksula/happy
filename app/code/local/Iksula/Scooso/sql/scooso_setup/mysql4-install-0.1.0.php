<?php

$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$installer->startSetup();

$installer->run(
    "
	    DROP TABLE IF EXISTS `scooso`;
	    CREATE TABLE `scooso` (
	    	`id` int(11) unsigned NOT NULL auto_increment,
	    	`product_id` varchar(255) NOT NULL default '',
	    	`option_ids` varchar(255) NOT NULL default '',
	    	`email_id` varchar(255) NOT NULL default '',
	    	`status` varchar(255) NOT NULL default '',
	    	`additional_field` varchar(255) NOT NULL default '',
	    PRIMARY KEY (`id`)
    );
    "
);

$installer->endSetup();