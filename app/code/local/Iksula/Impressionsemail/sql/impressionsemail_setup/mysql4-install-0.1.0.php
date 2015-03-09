<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table emailpopup_impressions(impressions_id int not null auto_increment, impressions int, source varchar(20),created_date DATETIME, primary key(impressions_id));

		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 