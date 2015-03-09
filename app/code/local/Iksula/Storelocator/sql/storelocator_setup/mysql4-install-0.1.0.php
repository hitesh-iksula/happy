<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table stores(store_id int not null auto_increment, type tinyint(4), country varchar(100) ,state varchar(100),city varchar(100),address varchar(1000),contact varchar(50),person varchar(100),enabled tinyint(4) default 1,primary key(store_id));
create table country(country_id int not null auto_increment, country varchar(100),primary key(country_id));
create table state(state_id int not null auto_increment, state varchar(100),country_id int , primary key(state_id),FOREIGN key (country_id) REFERENCES country(country_id));
create table city(city_id int not null auto_increment, city varchar(100),state_id int,primary key(city_id), FOREIGN key (state_id) REFERENCES state(state_id));
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 