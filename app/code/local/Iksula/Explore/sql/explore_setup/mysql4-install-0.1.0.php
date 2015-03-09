<?php

$installer = $this;
$installer->startSetup();

$inactive_thumbnail  = array(
    'type'          => 'varchar',
    'label'         => 'Inactive Thumb Image',
    'input'         => 'image',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       => 1,
    'required'      => 0,
    'user_defined'  => 1,
    'default'       => '',
    'group'         => 'General Information',
    'backend'       => 'catalog/category_attribute_backend_image',
);
$installer->addAttribute('catalog_category', 'inactive_thumbnail', $inactive_thumbnail);

$active_thumbnail  = array(
    'type'          => 'varchar',
    'label'         => 'Active Thumb Image',
    'input'         => 'image',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       => 1,
    'required'      => 0,
    'user_defined'  => 1,
    'default'       => '',
    'group'         => 'General Information',
    'backend'       => 'catalog/category_attribute_backend_image',
);
$installer->addAttribute('catalog_category', 'active_thumbnail', $active_thumbnail);

$hover_thumbnail  = array(
    'type'          => 'varchar',
    'label'         => 'Hover Thumb Image',
    'input'         => 'image',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       => 1,
    'required'      => 0,
    'user_defined'  => 1,
    'default'       => '',
    'group'         => 'General Information',
    'backend'       => 'catalog/category_attribute_backend_image',
);
$installer->addAttribute('catalog_category', 'hover_thumbnail', $hover_thumbnail);

$installer->endSetup();
