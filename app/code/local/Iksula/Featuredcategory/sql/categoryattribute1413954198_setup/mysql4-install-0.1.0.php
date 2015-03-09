<?php
$installer = $this;
$installer->startSetup();


$installer->addAttribute("catalog_category", "featured_category",  array(
    "type"     => "int",
    "backend"  => "",
    'group'    => 'Featured Category',
    "frontend" => "",
    "label"    => "Enable Featured Category",
    "input"    => "select",
    "class"    => "",
    "source"   => "eav/entity_attribute_source_boolean",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    "visible"  => true,
    "required" => false,
    "user_defined"  => false,
    "default" => "",
    "searchable" => false,
    "filterable" => false,
    "comparable" => false,
	
    "visible_on_front"  => true,
    "unique"     => false,
    "note"       => ""

	));

$installer->addAttribute("catalog_category", "featured_category_image",  array(
    "type"     => "varchar",
    "backend"  => "catalog/category_attribute_backend_image",
    'group'    => 'Featured Category',
    "frontend" => "",
    "label"    => "Image",
    "input"    => "image",
    "class"    => "",
    "source"   => "",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    "visible"  => true,
    "required" => false,
    "user_defined"  => false,
    "default" => "",
    "searchable" => false,
    "filterable" => false,
    "comparable" => false,
    
    "visible_on_front"  => true,
    "unique"     => false,
    "note"       => ""

    ));

$installer->addAttribute("catalog_category", "featured_category_parent",  array(
    "type"     => "int",
    "backend"  => "",
    'group'    => 'Featured Category',
    "frontend" => "",
    "label"    => "Parent Featured Category",
    "input"    => "select",
    "class"    => "",
    "source"   => "eav/entity_attribute_source_boolean",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    "visible"  => true,
    "required" => false,
    "user_defined"  => false,
    "default" => "",
    "searchable" => false,
    "filterable" => false,
    "comparable" => false,
    
    "visible_on_front"  => true,
    "unique"     => false,
    "note"       => ""

    ));


$installer->addAttribute("catalog_category", "featured_category_child",  array(
    "type"     => "int",
    "backend"  => "",
    'group'    => 'Featured Category',
    "frontend" => "",
    "label"    => "Child Featured Category",
    "input"    => "select",
    "class"    => "",
    "source"   => "eav/entity_attribute_source_boolean",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    "visible"  => true,
    "required" => false,
    "user_defined"  => false,
    "default" => "",
    "searchable" => false,
    "filterable" => false,
    "comparable" => false,
    
    "visible_on_front"  => true,
    "unique"     => false,
    "note"       => ""

    ));



$installer->endSetup();
	 