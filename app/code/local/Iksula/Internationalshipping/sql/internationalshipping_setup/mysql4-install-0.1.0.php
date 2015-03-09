<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table internationalshipping(ship_id int not null auto_increment, name varchar(100), email varchar(100),products varchar(5000),shipping_add varchar(5000),pincode varchar(50), country varchar(100), status tinyint(4) default 0,primary key(ship_id));
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 