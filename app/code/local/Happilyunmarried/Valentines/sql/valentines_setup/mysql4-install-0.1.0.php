<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table if not exists valentines(valentines_id int not null auto_increment, name varchar(100),message text,email varchar(100), primary key(valentines_id));
  
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 
