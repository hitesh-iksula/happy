<?php

$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$installer->startSetup();

$installer->run(
    "
    DROP TABLE IF EXISTS `explore_gender`;
    CREATE TABLE `explore_gender` (
    `gender_id` int(11) unsigned NOT NULL auto_increment,
    `gender_name` varchar(255) NOT NULL default '',
    `gender_logo_1` varchar(255) NOT NULL default '',
    `gender_logo_2` varchar(255) NOT NULL default '',
    `status` int(11) unsigned default '1',
    PRIMARY KEY (`gender_id`)
    );
    "
);

$installer->run(
    "
    DROP TABLE IF EXISTS `explore_personality`;
    CREATE TABLE `explore_personality` (
    `personality_id` int(11) unsigned NOT NULL auto_increment,
    `personality_name` varchar(255) NOT NULL default '',
    `personality_logo_1` varchar(255) NOT NULL default '',
    `personality_logo_2` varchar(255) NOT NULL default '',
    `status` int(11) unsigned default '1',
    PRIMARY KEY (`personality_id`)
    );
    "
);

$installer->run(
    "
    DROP TABLE IF EXISTS `explore_occasion`;
    CREATE TABLE `explore_occasion` (
    `occasion_id` int(11) unsigned NOT NULL auto_increment,
    `occasion_name` varchar(255) NOT NULL default '',
    `occasion_logo_1` varchar(255) NOT NULL default '',
    `occasion_logo_2` varchar(255) NOT NULL default '',
    `status` int(11) unsigned default '1',
    PRIMARY KEY (`occasion_id`)
    );
    "
);

$setup->addAttribute('catalog_product', 'explore_gender', array(
    'group'         => 'General',
    'input'         => 'multiselect',
    'type'          => 'text',
    'label'         => 'Gender',
    'source'        => 'explore/entity_attribute_source_gender',
    'backend'       => 'eav/entity_attribute_backend_array',
    'visible'       => 1,
    'required'      => 0,
    'user_defined'  => 1,
    'searchable'    => 1,
    'filterable'    => 1,
    'comparable'    => 1,
    'visible_on_front'              => 1,
    'visible_in_advanced_search'    => 1,
    'is_html_allowed_on_front'      => 0,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$setup->addAttribute('catalog_product', 'explore_personality', array(
    'group'         => 'General',
    'input'         => 'multiselect',
    'type'          => 'text',
    'label'         => 'Personality',
    'source'        => 'explore/entity_attribute_source_personality',
    'backend'       => 'eav/entity_attribute_backend_array',
    'visible'       => 1,
    'required'      => 0,
    'user_defined'  => 1,
    'searchable'    => 1,
    'filterable'    => 1,
    'comparable'    => 1,
    'visible_on_front'              => 1,
    'visible_in_advanced_search'    => 1,
    'is_html_allowed_on_front'      => 0,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$setup->addAttribute('catalog_product', 'explore_occasion', array(
    'group'         => 'General',
    'input'         => 'multiselect',
    'type'          => 'text',
    'label'         => 'Occasion',
	'source'        => 'explore/entity_attribute_source_occasion',
    'backend'       => 'eav/entity_attribute_backend_array',
    'visible'       => 1,
    'required'      => 0,
    'user_defined' 	=> 1,
    'searchable' 	=> 1,
    'filterable' 	=> 1,
    'comparable'    => 1,
    'visible_on_front' 				=> 1,
    'visible_in_advanced_search'  	=> 1,
    'is_html_allowed_on_front' 		=> 0,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));


$installer->endSetup();