<?php

$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$installer->startSetup();

$installer->run(
    "
	    DROP TABLE IF EXISTS `solidswatches`;
	    CREATE TABLE `solidswatches` (
	    	`swatch_id` int(11) unsigned NOT NULL auto_increment,
	    	`attribute_id` varchar(255) NOT NULL default '',
	    	`option_id` varchar(255) NOT NULL default '',
	    	`color_1` varchar(255) NOT NULL default '',
	    	`color_2` varchar(255) NOT NULL default '',
	    	`additional_field` varchar(255) NOT NULL default '',
	    	`status` int(11) unsigned default '1',
	    PRIMARY KEY (`swatch_id`)
    );
    "
);

$installer->endSetup();