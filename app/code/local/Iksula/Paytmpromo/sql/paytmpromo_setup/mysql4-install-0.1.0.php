<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table paytm_promotion_entries (
    `id` int not null auto_increment,
    `order_id` varchar(100),
    `name` varchar(100),
    `email_id` varchar(100),
    `phone_number` varchar(100),
    `amount` varchar(100),
    `allotted_coupon_code` varchar(100),
    `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `txnid` varchar(100),
    `status` varchar(100),
    `respcode` varchar(100),
    `respmsg` varchar(100),
    `txndate` varchar(100),
primary key(id)
);
SQLTEXT;

$installer->run($sql);

$installer->endSetup();
	 