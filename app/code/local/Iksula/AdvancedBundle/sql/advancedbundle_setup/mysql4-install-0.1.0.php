<?php
$installer = $this;
$installer->startSetup();
$installer = new Mage_Sales_Model_Mysql4_Setup;

$scheduled_delivery  = array(
'type'          => 'text',
'backend_type'  => 'text',
'frontend_input'=> 'text',
'is_user_defined'  => true,
'label'         => 'Schedule Delivery On',
'default'       => '',
'visible'       => true,
'required'      => false,
'user_defined'  => false,
'searchable'    => false,
'filterable'    => false,
'comparable'    => false );

$installer->addAttribute('order', 'scheduled_delivery', $scheduled_delivery);
$installer->addAttribute('quote', 'scheduled_delivery', $scheduled_delivery);

$installer->endSetup();