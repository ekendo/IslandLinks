ALTER TABLE `mos_pshop_orders` ADD `customer_note` TEXT NOT NULL ;
ALTER TABLE `mos_pshop_orders` ADD `ip_address` VARCHAR( 15 ) NOT NULL ;

CREATE TABLE `mos_pshop_product_download` (
`product_id` int( 11 ) DEFAULT '0' NOT NULL ,
`user_id` varchar( 255 ) DEFAULT '' NOT NULL ,
`order_id` varchar( 255 ) DEFAULT '' NOT NULL ,
`end_date` varchar( 255 ) DEFAULT '' NOT NULL ,
`download_max` varchar( 255 ) DEFAULT '' NOT NULL ,
`download_id` varchar( 255 ) DEFAULT '' NOT NULL ,
`file_name` varchar( 255 ) DEFAULT '' NOT NULL ,
PRIMARY KEY ( `product_id` ) 
) ;
INSERT INTO `mos_pshop_function` ( `function_id` , `module_id` , `function_name` , `function_class` , `function_method` , `function_description` , `function_perms` ) 
VALUES (
'', '5', 'downloadRequest', 'ps_order', 'download_request', 'This checks if the download request is valid and sends the file to the browser as file download if the request was successful, otherwise echoes an error', 'admin,storeadmin,shopper'
);
