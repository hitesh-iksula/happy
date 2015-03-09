<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

 
$installer = $this;
//$connection = $installer->getConnection();
 
$installer->startSetup();
 
$sql2=<<<SQLTEXT
create table paytm_coupons (
    `id` int not null auto_increment,
    `hu_coupon_code` varchar(100),
    `paytm_coupon_code` varchar(100),
    `status` int(1),
    `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
primary key(id)
);
SQLTEXT;
$installer->run($sql2);
//Extract data from CSV file
try{
$csv = new Varien_File_Csv;
$data = $csv->getData('paytm_coupon_codes.csv');
//var_dump($data);die();

foreach ($data as $temp) {
  // var_dump($temp);die(); 
/*$resultNum = $installer->getConnection()->insertArray(
    $installer->getTable('paytm_coupons'),
    array('hu_coupon_code','paytm_coupon_code','status'),    //column names
    $temp
);*/
    $sql3=<<<SQLTEXT
 INSERT INTO paytm_coupons (`hu_coupon_code`,`paytm_coupon_code`,`status`) VALUES
            ('$temp[0]','$temp[1]','$temp[2]');
SQLTEXT;
   $installer->run($sql3);
}
}catch(exception $e){
 echo $e->getMessage();die();   
}
$installer->endSetup();

?>