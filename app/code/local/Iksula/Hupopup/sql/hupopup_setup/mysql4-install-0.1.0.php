<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table hu_popup(
hu_popup_id int not null auto_increment, 
username varchar(100), 
user_email_id varchar(30),
friend_email_id varchar(30),
source varchar(40),
platform varchar(40),
verify_link varchar(30), 
created_date DATETIME, 
primary key(hu_popup_id));

create table hu_popup_master(email_id_source varchar(30));

SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 