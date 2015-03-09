<?php
$installer = $this;
$installer->startSetup();
$installer = new Mage_Sales_Model_Mysql4_Setup;

$orderSource  = array(
	'type'            => 'text',
	'backend_type'    => 'text',
	'frontend_input'  => 'text',
	'is_user_defined' => true,
	'label'           => 'Order Source',
	'default'         => '',
	'visible'         => true,
	'required'        => false,
	'user_defined'    => false,
	'searchable'      => false,
	'filterable'      => false,
	'comparable'      => false
);

$installer->addAttribute('order', 'order_source', $orderSource);
$installer->addAttribute('quote', 'order_source', $orderSource);

$installer->endSetup();
