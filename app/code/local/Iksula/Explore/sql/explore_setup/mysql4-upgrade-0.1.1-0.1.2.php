<?php
$installer = $this;
$installer->startSetup();

$home_tagline  = array(
    'type' => 'text',
    'label'=> 'Homepage Tagline',
    'input' => 'text',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => "",
    'group' => "General Information"
);
$installer->addAttribute('catalog_category', 'home_tagline', $home_tagline);

$subcategory_colors  = array(
    'type' => 'text',
    'label'=> 'Subcategory Colors',
    'input' => 'text',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => "",
    'group' => "General Information",
    'note'  => "Comma Separated Tagline Color, Subcategory Label Colors When Inactive, Hovered and Active (HEX Code)"
);
$installer->addAttribute('catalog_category', 'subcategory_colors', $subcategory_colors);

$installer->endSetup();