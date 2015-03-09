<?php
$installer = $this;
$installer->startSetup();


$installer->addAttribute("catalog_category", "categorychooser",  array(
    "type"     => "int",
    "backend"  => "",
    "group"    => "General Information",
    "frontend" => "",
    "label"    => "categorychooser",
    "input"    => "select",
    "class"    => "",
    "source"   => "categorydropdown/eav_entity_attribute_source_categoryoptions14114833710",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    "visible"  => true,
    "required" => true,
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
	 