<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table IF NOT EXISTS pincode(id int not null auto_increment, 
pincode varchar(100),
area varchar(100),
location varchar(100),
city varchar(100),
state varchar(100),
code varchar(100),
zone varchar(100),
cod varchar(100),
prepaid varchar(100),
status int(10),
primary key(id));
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 