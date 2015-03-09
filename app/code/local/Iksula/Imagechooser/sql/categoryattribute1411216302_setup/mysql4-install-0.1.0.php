<?php
$installer = $this;
$installer->startSetup();


$installer->addAttribute("catalog_category", "image_chooser",  array(
    "type"     => "text",
    "backend"  => "",
    "group"    => "General Information",
    "frontend" => "",
    "label"    => "Image Chooser",
    "input"    => "select",
    "class"    => "",
    "source"   => "imagechooser/eav_entity_attribute_source_categoryoptions14112163020",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    "visible"  => true,
    "required" => false,
    "user_defined"  => false,
    "default" => "",
    "searchable" => false,
    "filterable" => false,
    "comparable" => false,
	
    "visible_on_front"  => false,
    "unique"     => false,
    "note"       => ""

	));
$installer->endSetup();
	 