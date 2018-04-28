
INSERT INTO `jos_vm_coupons` VALUES (1, 'test1', 'total', 'gift', 6.00);
INSERT INTO `jos_vm_coupons` VALUES (2, 'test2', 'percent', 'permanent', 15.00);
INSERT INTO `jos_vm_coupons` VALUES (3, 'test3', 'total', 'permanent', 4.00);
INSERT INTO `jos_vm_coupons` VALUES (4, 'test4', 'total', 'gift', 15.00);

INSERT INTO `jos_vm_shipping_carrier` VALUES (1, 'DHL', 0);
INSERT INTO `jos_vm_shipping_carrier` VALUES (2, 'UPS', 1);

INSERT INTO `jos_vm_shipping_rate` VALUES (1,'Inland &gt; 4kg','1','DEU','00000','99999','0.0','4.0','5.62','2','47','0','1');
INSERT INTO `jos_vm_shipping_rate` VALUES (2,'Inland &gt; 8kg','1','DEU','00000','99999','4.0','8.0','6.39','2','47','0','2');
INSERT INTO `jos_vm_shipping_rate` VALUES (3,'Inland &gt; 12kg','1','DEU','00000','99999','8.0','12.0','7.16','2','47','0','3');
INSERT INTO `jos_vm_shipping_rate` VALUES (4,'Inland &gt; 20kg','1','DEU','00000','99999','12.0','20.0','8.69','2','47','0','4');
INSERT INTO `jos_vm_shipping_rate` VALUES (5,'EU+ &gt;  4kg','1','AND;BEL;DNK;FRO;FIN;FRA;GRC;GRL;GBR;IRL;ITA;LIE;LUX;MCO;NLD;AUT;POL;PRT;SMR;SWE;CHE;SVK;ESP;CZE','00000','99999','0.0','4.0','14,57','2','47','0','5');
INSERT INTO `jos_vm_shipping_rate` VALUES (6,'EU+ &gt;  8kg','1','AND;BEL;DNK;FRO;FIN;FRA;GRC;GRL;GBR;IRL;ITA;LIE;LUX;MCO;NLD;AUT;POL;PRT;SMR;SWE;CHE;SVK;ESP;CZE','00000','99999','4.0','8.0','18,66','2','47','0','6');
INSERT INTO `jos_vm_shipping_rate` VALUES (7,'EU+ &gt; 12kg','1','AND;BEL;DNK;FRO;FIN;FRA;GRC;GRL;GBR;IRL;ITA;LIE;LUX;MCO;NLD;AUT;POL;PRT;SMR;SWE;CHE;SVK;ESP;CZE','00000','99999','8.0','12.0','22,57','2','47','0','7');
INSERT INTO `jos_vm_shipping_rate` VALUES (8,'EU+ &gt; 20kg','1','AND;BEL;DNK;FRO;FIN;FRA;GRC;GRL;GBR;IRL;ITA;LIE;LUX;MCO;NLD;AUT;POL;PRT;SMR;SWE;CHE;SVK;ESP;CZE','00000','99999','12.0','20.0','30,93','2','47','0','8');
INSERT INTO `jos_vm_shipping_rate` VALUES (9,'Europe &gt; 4kg','1','ALB;ARM;AZE;BLR;BIH;BGR;EST;GEO;GIB;ISL;YUG;KAZ;HRV;LVA;LTU;MLT;MKD;MDA;NOR;ROM;RUS;SVN;TUR;UKR;HUN;BLR;CYP','00000','99999','0.0','4.0','23,78','2','47','0','9');
INSERT INTO `jos_vm_shipping_rate` VALUES (10,'Europe &gt;  8kg','1','ALB;ARM;AZE;BLR;BIH;BGR;EST;GEO;GIB;ISL;YUG;KAZ;HRV;LVA;LTU;MLT;MKD;MDA;NOR;ROM;RUS;SVN;TUR;UKR;HUN;BLR;CYP','00000','99999','4.0','8.0','29,91','2','47','0','10');
INSERT INTO `jos_vm_shipping_rate` VALUES (11,'Europe &gt; 12kg','1','ALB;ARM;AZE;BLR;BIH;BGR;EST;GEO;GIB;ISL;YUG;KAZ;HRV;LVA;LTU;MLT;MKD;MDA;NOR;ROM;RUS;SVN;TUR;UKR;HUN;BLR;CYP','00000','99999','8.0','12.0','36,05','2','47','0','11');
INSERT INTO `jos_vm_shipping_rate` VALUES (12,'Europe &gt; 20kg','1','ALB;ARM;AZE;BLR;BIH;BGR;EST;GEO;GIB;ISL;YUG;KAZ;HRV;LVA;LTU;MLT;MKD;MDA;NOR;ROM;RUS;SVN;TUR;UKR;HUN;BLR;CYP','00000','99999','12.0','20.0','48,32','2','47','0','12');
INSERT INTO `jos_vm_shipping_rate` VALUES (13,'World_1 &gt;  4kg','1','EGY;DZA;BHR;IRQ;IRN;ISR;YEM;JOR;CAN;QAT;KWT;LBN;LBY;MAR;OMN;SAU;SYR;TUN;ARE;USA','00000','99999','0.0','4.0','26,84','2','47','0','13');
INSERT INTO `jos_vm_shipping_rate` VALUES (14,'World_1 &gt; 8kg','1','EGY;DZA;BHR;IRQ;IRN;ISR;YEM;JOR;CAN;QAT;KWT;LBN;LBY;MAR;OMN;SAU;SYR;TUN;ARE;USA','00000','99999','4.0','8.0','35,02','2','47','0','14');
INSERT INTO `jos_vm_shipping_rate` VALUES (15,'World_1 &gt;12kg','1','EGY;DZA;BHR;IRQ;IRN;ISR;YEM;JOR;CAN;QAT;KWT;LBN;LBY;MAR;OMN;SAU;SYR;TUN;ARE;USA','00000','99999','8.0','12.0','43,20','2','47','0','15');
INSERT INTO `jos_vm_shipping_rate` VALUES (16,'World_1 &gt;20kg','1','EGY;DZA;BHR;IRQ;IRN;ISR;YEM;JOR;CAN;QAT;KWT;LBN;LBY;MAR;OMN;SAU;SYR;TUN;ARE;USA','00000','99999','12.0','20.0','59,57','2','47','0','16');
INSERT INTO `jos_vm_shipping_rate` VALUES (17,'World_2 &gt; 4kg','1','','00000','99999','0.0','4.0','32,98','2','47','0','17');
INSERT INTO `jos_vm_shipping_rate` VALUES (18,'World_2 &gt; 8kg','1','','00000','99999','4.0','8.0','47,29','2','47','0','18');
INSERT INTO `jos_vm_shipping_rate` VALUES (19,'World_2 &gt; 12kg','1','','00000','99999','8.0','12.0','61,61','2','47','0','19');
INSERT INTO `jos_vm_shipping_rate` VALUES (20,'World_2 &gt; 20kg','1','','00000','99999','12.0','20.0','90,24','2','47','0','20');
INSERT INTO `jos_vm_shipping_rate` VALUES (21,'UPS Express','2','AND;BEL;DNK;FRO;FIN;FRA;GRC;GRL;GBR;IRL;ITA;LIE;LUX;MCO;NLD;AUT;POL;PRT;SMR;SWE;CHE;SVK;ESP;CZE','00000','99999','0.0','20.0','5,24','2','47','0','21');

INSERT INTO `jos_vm_zone_shipping` VALUES (1, 'Default', '6.00', '35.00', 'This is the default Shipping Zone. This is the zone information that all countries will use until you assign each individual country to a Zone.', '2');
INSERT INTO `jos_vm_zone_shipping` VALUES (2, 'Zone 1', '1000.00', '10000.00', 'This is a zone example', '2');
INSERT INTO `jos_vm_zone_shipping` VALUES (3, 'Zone 2', '2.00', '22.00', 'This is the second zone. You can use this for notes about this zone', '2');
INSERT INTO `jos_vm_zone_shipping` VALUES (4, 'Zone 3', '11.00', '64.00', 'Another usefull thing might be details about this zone or special instructions.', '2');

INSERT INTO `jos_vm_category` VALUES (1, 1, 'Hand Tools', 'Hand Tools', 'ee024e46399e792cc8ba4bf097d0fa6a.jpg', 'fc2f001413876a374484df36ed9cf775.jpg', 'Y', 950319905, 960304194, 'browse_3', '3', '', '1');
INSERT INTO `jos_vm_category` VALUES (2, 1, 'Power Tools', 'Power Tools', 'fc8802c7eaa1149bde98a541742217de.jpg', 'fe2f63f4c46023e3b33404c80bdd2bfe.jpg', 'Y', 950319916, 960304104, 'browse_4', '4', '', '2');
INSERT INTO `jos_vm_category` VALUES (3, 1, 'Garden Tools', 'Garden Tools', '702168cd91e8b7bbb7a36be56f86e9be.jpg', '756ff6d140e11079caf56955060f1162.jpg', 'Y', 950321122, 960304338, 'browse_2', '2', 'shop.garden_flypage', '3');
INSERT INTO `jos_vm_category` VALUES (4, 1, 'Outdoor Tools', 'Outdoor Tools', NULL, NULL, 'Y', 955626629, 958889528, 'browse_1', '1', NULL, '4');
INSERT INTO `jos_vm_category` VALUES (5, 1, 'Indoor Tools', 'Indoor Tools', NULL, NULL, 'Y', 958892894, 958892894, 'browse_1', '1', NULL, '5');
		
INSERT INTO `jos_vm_category_xref` VALUES ('0', 1, NULL);
INSERT INTO `jos_vm_category_xref` VALUES ('0', 2, NULL);
INSERT INTO `jos_vm_category_xref` VALUES ('0', 3, NULL);
INSERT INTO `jos_vm_category_xref` VALUES (2, 4, NULL);
INSERT INTO `jos_vm_category_xref` VALUES (2, 5, NULL);
			
INSERT INTO `jos_vm_product` VALUES (1, 1, 0, 'G01', '<p>Nice hand shovel to dig with in the yard.</p>\r\n', '\r\n<ul>  <li>Hand crafted handle with maximum grip torque  </li><li>Titanium tipped shovel platter  </li><li>Half degree offset for less accidents  </li><li>Includes HowTo Video narrated by Bob Costas  </li></ul>    <b>Specifications</b><br>  5\" Diameter<br>  Tungsten handle tip with 5 point loft<br>\r\n', '8d886c5855770cc01a3b8a2db57f6600.jpg', 'cca3cd5db813ee6badf6a3598832f2fc.jpg', 'Y', '10.0000', 'pounds', '0.0000', '0.0000', '0.0000', 'inches', '', 10, 1072911600, '48h.gif', 'Y', 1, NULL, 950320117, 1084907592, 'Hand Shovel', 0, '', '', 2, NULL, NULL);
INSERT INTO `jos_vm_product` VALUES (2, 1, 0, 'G02', 'A really long ladder to reach high places.', '\r\n<ul>  <li>Hand crafted handle with maximum grip torque  </li><li>Titanium tipped shovel platter  </li><li>Half degree offset for less accidents  </li><li>Includes HowTo Video narrated by Bob Costas  </li></ul>    <b>Specifications</b><br>  5\" Diameter<br>  Tungsten handle tip with 5 point loft<br>\r\n', 'ffd5d5ace2840232c8c32de59553cd8d.jpg', '8cb8d644ef299639b7eab25829d13dbc.jpg', 'Y', '10.0000', 'pounds', '0.0000', '0.0000', '0.0000', 'inches', '', 76, 1072911600, '3-5d.gif', 'N', 0, NULL, 950320180, 1084907618, 'Ladder', 0, '', '', 2, NULL, NULL);
INSERT INTO `jos_vm_product` VALUES (3, 1, 0, 'G03', 'Nice shovel.  You can dig your way to China with this one.', '\r\n<ul>  <li>Hand crafted handle with maximum grip torque  </li><li>Titanium tipped shovel platter  </li><li>Half degree offset for less accidents  </li><li>Includes HowTo Video narrated by Bob Costas  </li></ul>    <b>Specifications</b><br>  5\" Diameter<br>  Tungsten handle tip with 5 point loft<br>\r\n', '8147a3a9666aec0296525dbd81f9705e.jpg', '520efefd6d7977f91b16fac1149c7438.jpg', 'Y', '10.0000', 'pounds', '0.0000', '0.0000', '0.0000', 'inches', '', 32, 1072911600, '7d.gif', 'N', 0, NULL, 950320243, 1084907765, 'Shovel', 0, 'Size,XL[+1.99],M,S[-2.99];Colour,Red,Green,Yellow,ExpensiveColor[=24.00]', '', 2, NULL, NULL);
INSERT INTO `jos_vm_product` VALUES (4, 1, 0, 'G04', 'This shovel is smaller but you\'ll be able to dig real quick.', '\r\n<ul>  <li>Hand crafted handle with maximum grip torque  </li><li>Titanium tipped shovel platter  </li><li>Half degree offset for less accidents  </li><li>Includes HowTo Video narrated by Bob Costas  </li></ul>    <b>Specifications</b><br>  5\" Diameter<br>  Tungsten handle tip with 5 point loft<br>\r\n', 'a04395a8aefacd9c1659ebca4dbfd4ba.jpg', '1b0c96d67abdbea648cd0ea96fd6abcb.jpg', 'Y', '10.0000', 'pounds', '0.0000', '0.0000', '0.0000', 'inches', '', 98, 1088632800, 'on-order.gif', 'N', 0, NULL, 950320378, 1084907867, 'Smaller Shovel', 0, 'Size,big[+2.99],medium;Color,red[+0.99],green[-0.99]', '', 2, NULL, NULL);
INSERT INTO `jos_vm_product` VALUES (5, 1, 0, 'H01', 'This saw is great for getting cutting through downed limbs.', '\r\n<ul>  <li>Hand crafted handle with maximum grip torque  </li><li>Titanium tipped shovel platter  </li><li>Half degree offset for less accidents  </li><li>Includes HowTo Video narrated by Bob Costas  </li></ul>    <b>Specifications</b><br>  5\" Diameter<br>  Tungsten handle tip with 5 point loft<br>\r\n', '1aa8846d3cfe3504b2ccaf7c23bb748f.jpg', 'e614ba08c3ee0c2adc62fd9e5b9440eb.jpg', 'Y', '10.0000', 'pounds', '0.0000', '0.0000', '0.0000', 'inches', '', 32, 1072911600, '1-4w.gif', 'Y', 2, NULL, 950321256, 1084907669, 'Nice Saw', 0, 'Size,big,small,medium;Power,100W,200W,500W', '', 2, NULL, NULL);
INSERT INTO `jos_vm_product` VALUES (6, 1, 0, 'H02', 'A great hammer to hammer away with.', '\r\n<ul>  <li>Hand crafted handle with maximum grip torque  </li><li>Titanium tipped shovel platter  </li><li>Half degree offset for less accidents  </li><li>Includes HowTo Video narrated by Bob Costas  </li></ul>    <b>Specifications</b><br>  5\" Diameter<br>  Tungsten handle tip with 5 point loft<br>\r\n', 'dccb8223891a17d752bfc1477d320da9.jpg', '578563851019e01264a9b40dcf1c4ab6.jpg', 'Y', '10.0000', 'pounds', '0.0000', '0.0000', '0.0000', 'inches', '', 500, 1072911600, '24h.gif', 'N', 0, NULL, 950321631, 1084907947, 'Hammer', 0, 'Size,big,medium,small;Material,wood and metal,plastic and metal[-0.99]', '', 2, NULL, NULL);
INSERT INTO `jos_vm_product` VALUES (7, 1, 0, 'P01', 'Don\'t do it with an axe.  Get a chain saw.', '\r\n<ul>  <li>Tool-free tensioner for easy, convenient chain adjustment  </li><li>3-Way Auto Stop; stops chain a fraction of a second  </li><li>Automatic chain oiler regulates oil for proper chain lubrication  </li><li>Small radius guide bar reduces kick-back  </li></ul>  <br>  <b>Specifications</b><br>  12.5 AMPS   <br>   16\" Bar Length   <br>   3.5 HP   <br>   8.05 LBS. Weight   <br>\r\n', '8716aefc3b0dce8870360604e6eb8744.jpg', 'c3a5bf074da14f30c849d13a2dd87d2c.jpg', 'Y', '10.0000', 'pounds', '0.0000', '0.0000', '0.0000', 'inches', '', 45, 1088632800, '48h.gif', 'N', 0, NULL, 950321725, 1084907512, 'Chain Saw', 0, '', '', 2, NULL, NULL);
INSERT INTO `jos_vm_product` VALUES (8, 1, 0, 'P02', 'Cut rings around wood.  This saw can handle the most delicate projects.', '\r\n<ul>  <li>Patented Sightline; Window provides maximum visibility for straight cuts  </li><li>Adjustable dust chute for cleaner work area  </li><li>Bail handle for controlled cutting in 90° to 45° applications  </li><li>1-1/2 to 2-1/2 lbs. lighter and 40% less noise than the average circular saw                     </li><li><b>Includes:</b>Carbide blade  </li></ul>  <br>  <b>Specifications</b><br>  10.0 AMPS   <br>   4,300 RPM   <br>   Capacity: 2-1/16\" at 90°, 1-3/4\" at 45°<br>\r\n', 'b4a748303d0d996b29d5a1e1d1112537.jpg', '9a4448bb13e2f7699613b2cfd7cd51ad.jpg', 'Y', '10.0000', 'pounds', '0.0000', '0.0000', '0.0000', 'inches', '', 33, 1072911600, '3-5d.gif', 'Y', 1, NULL, 950321795, 1084907537, 'Circular Saw', 0, 'Size,XL[+1],M,S[-2];Power,strong,middle,poor[=24]', '', 2, NULL, NULL);
INSERT INTO `jos_vm_product` VALUES (9, 1, 0, 'P03', 'Drill through anything.  This drill has the power you need for those demanding hole boring duties.', '\r\n<font color=\"#000000\" size=\"3\"><ul><li>High power motor and double gear reduction for increased durability and improved performance  </li><li>Mid-handle design and two finger trigger for increased balance and comfort  </li><li>Variable speed switch with lock-on button for continuous use  </li><li><b>Includes:</b> Chuck key &amp; holder  </li></ul>  <br>  <b>Specifications</b><br>  4.0 AMPS   <br>   0-1,350 RPM   <br>   Capacity: 3/8\" Steel, 1\" Wood   <br><br>  </font>\r\n', 'c70a3f47baf9a4020aeeee919eb3fda4.jpg', '1ff5f2527907ca86103288e1b7cc3446.jpg', 'Y', '10.0000', 'pounds', '0.0000', '0.0000', '0.0000', 'inches', '', 3, 1072911600, '2-3d.gif', 'N', 0, NULL, 950321879, 1084907557, 'Drill', 0, '', '', 2, NULL, NULL);
INSERT INTO `jos_vm_product` VALUES (10, 1, 0, 'P04', 'Blast away that paint job from the past.  Use this power sander to really show them you mean business.', '\r\n<ul>  <li>Lever activated paper clamps for simple sandpaper changes  </li><li>Dust sealed rocker switch extends product life and keeps dust out of motor  </li><li>Flush sands on three sides to get into corners  </li><li>Front handle for extra control  </li><li>Dust extraction port for cleaner work environment   </li></ul>  <br>  <b>Specifications</b><br>  1.2 AMPS    <br>   10,000 OPM    <br>\r\n', '7a36a05526e93964a086f2ddf17fc609.jpg', '480655b410d98a5cc3bef3927e786866.jpg', 'Y', '10.0000', 'pounds', '0.0000', '0.0000', '0.0000', 'inches', '', 2, 1072911600, '1-2m.gif', 'N', 2, NULL, 950321963, 1084907719, 'Power Sander', 0, 'Size,big,medium,small;Power,100W,200W,300W', '', 2, NULL, NULL);
INSERT INTO `jos_vm_product` VALUES (11, 1, 1, 'G01-01', '', '', '', '', 'Y', '10.0000', 'pounds', '0.0000', '0.0000', '0.0000', 'inches', '', 0, 0, '', '', 0, NULL, 955696949, 960372163, 'Hand Shovel', 0, NULL, '', 0, NULL, NULL);
INSERT INTO `jos_vm_product` VALUES (12, 1, 1, 'G01-02', '', '', '', '', 'Y', '10.0000', '', '0.0000', '0.0000', '0.0000', '', '', 0, 0, '', '', 0, NULL, 955697006, 960372187, 'Hand Shovel', 0, NULL, '', 0, NULL, NULL);
INSERT INTO `jos_vm_product` VALUES (13, 1, 1, 'G01-03', '', '', '', '', 'Y', '10.0000', '', '0.0000', '0.0000', '0.0000', '', '', 0, 0, '', '', 0, NULL, 955697044, 960372206, 'Hand Shovel', 0, NULL, '', 0, NULL, NULL);
INSERT INTO `jos_vm_product` VALUES (14, 1, 2, 'L01', '', '', '', '', 'Y', '10.0000', 'pounds', '0.0000', '0.0000', '0.0000', 'inches', '', 22, 1072911600, '', 'N', 0, NULL, 962351149, 1084902820, 'Metal Ladder', 0, NULL, '', 2, NULL, NULL);
INSERT INTO `jos_vm_product` VALUES (15, 1, 2, 'L02', '', '', '', '', 'Y', '10.0000', 'pounds', '0.0000', '0.0000', '0.0000', 'inches', '', 0, 0, '', '', 0, NULL, 962351165, 962351165, 'Wooden Ladder', 0, NULL, '', 0, NULL, NULL);
INSERT INTO `jos_vm_product` VALUES (16, 1, 2, 'L03', '', '', '', '', 'Y', '10.0000', 'pounds', '0.0000', '0.0000', '0.0000', 'inches', '', 0, 0, '', '', 0, NULL, 962351180, 962351180, 'Plastic Ladder', 0, NULL, '', 0, NULL, NULL);
		 
INSERT INTO `jos_vm_product_attribute` VALUES (11, 'Color', 'Red');
INSERT INTO `jos_vm_product_attribute` VALUES (12, 'Color', 'Green');
INSERT INTO `jos_vm_product_attribute` VALUES (13, 'Color', 'Blue');
INSERT INTO `jos_vm_product_attribute` VALUES (11, 'Size', 'Small');
INSERT INTO `jos_vm_product_attribute` VALUES (12, 'Size', 'Medium');
INSERT INTO `jos_vm_product_attribute` VALUES (13, 'Size', 'Large');
INSERT INTO `jos_vm_product_attribute` VALUES (14, 'Material', 'Metal');
INSERT INTO `jos_vm_product_attribute` VALUES (15, 'Material', 'Wood');
INSERT INTO `jos_vm_product_attribute` VALUES (16, 'Material', 'Plastic');
		
INSERT INTO `jos_vm_product_attribute_sku` VALUES (1, 'Color', 1);
INSERT INTO `jos_vm_product_attribute_sku` VALUES (1, 'Size', 2);
INSERT INTO `jos_vm_product_attribute_sku` VALUES (2, 'Material', 1);
		
INSERT INTO `jos_vm_product_category_xref` VALUES (1, 1, NULL);
INSERT INTO `jos_vm_product_category_xref` VALUES (3, 2, NULL);
INSERT INTO `jos_vm_product_category_xref` VALUES (3, 3, NULL);
INSERT INTO `jos_vm_product_category_xref` VALUES (3, 4, NULL);
INSERT INTO `jos_vm_product_category_xref` VALUES (1, 5, NULL);
INSERT INTO `jos_vm_product_category_xref` VALUES (1, 6, NULL);
INSERT INTO `jos_vm_product_category_xref` VALUES (4, 7, NULL);
INSERT INTO `jos_vm_product_category_xref` VALUES (2, 8, NULL);
INSERT INTO `jos_vm_product_category_xref` VALUES (5, 9, NULL);
INSERT INTO `jos_vm_product_category_xref` VALUES (2, 10, NULL);

INSERT INTO `jos_vm_product_discount` VALUES (1, '20.00', 1, 1097704800, 1101337200);
INSERT INTO `jos_vm_product_discount` VALUES (2, '2.00', 0, 1098655200, 0);
		
INSERT INTO `jos_vm_product_mf_xref` VALUES  ('1', '1');
INSERT INTO `jos_vm_product_mf_xref` VALUES  ('2', '1');
INSERT INTO `jos_vm_product_mf_xref` VALUES  ('3', '1');
INSERT INTO `jos_vm_product_mf_xref` VALUES  ('4', '1');
INSERT INTO `jos_vm_product_mf_xref` VALUES  ('5', '1');
INSERT INTO `jos_vm_product_mf_xref` VALUES  ('6', '1');
INSERT INTO `jos_vm_product_mf_xref` VALUES  ('7', '1');
INSERT INTO `jos_vm_product_mf_xref` VALUES  ('8', '1');
INSERT INTO `jos_vm_product_mf_xref` VALUES  ('9', '1');
INSERT INTO `jos_vm_product_mf_xref` VALUES  ('10', '1');
INSERT INTO `jos_vm_product_mf_xref` VALUES  ('11', '1');
INSERT INTO `jos_vm_product_mf_xref` VALUES  ('12', '1');
INSERT INTO `jos_vm_product_mf_xref` VALUES  ('13', '1');
INSERT INTO `jos_vm_product_mf_xref` VALUES  ('14', '1');
INSERT INTO `jos_vm_product_mf_xref` VALUES  ('15', '1');
INSERT INTO `jos_vm_product_mf_xref` VALUES  ('16', '1');
		
INSERT INTO `jos_vm_product_price` VALUES (1, 5, '24.99', 'USD', 0, 0, 950321309, 950321309, 5, 0, 0);
INSERT INTO `jos_vm_product_price` VALUES (2, 1, '4.99', 'USD', 0, 0, 950321324, 950321324, 5, 0, 0);
INSERT INTO `jos_vm_product_price` VALUES (3, 2, '49.99', 'USD', 0, 0, 950321340, 950321340, 5, 0, 0);
INSERT INTO `jos_vm_product_price` VALUES (4, 3, '24.99', 'USD', 0, 0, 950321368, 950321368, 5, 0, 0);
INSERT INTO `jos_vm_product_price` VALUES (5, 4, '19.99', 'USD', 0, 0, 950321385, 950321385, 5, 0, 0);
INSERT INTO `jos_vm_product_price` VALUES (6, 6, '1.00', 'USD', 0, 0, 950321686, 963808699, 5, 0, 0);
INSERT INTO `jos_vm_product_price` VALUES (7, 7, '149.99', 'USD', 0, 0, 950321754, 966506270, 5, 0, 0);
INSERT INTO `jos_vm_product_price` VALUES (8, 8, '220.90', 'USD', 0, 0, 950321833, 955614388, 5, 0, 0);
INSERT INTO `jos_vm_product_price` VALUES (9, 9, '48.12', 'USD', 0, 0, 950321933, 950321933, 5, 0, 0);
INSERT INTO `jos_vm_product_price` VALUES (10, 10, '74.99', 'USD', 0, 0, 950322005, 950322005, 5, 0, 0);
INSERT INTO `jos_vm_product_price` VALUES (11, 1, '2.99', 'USD', 0, 0, 955626841, 955626841, 6, 0, 0);
INSERT INTO `jos_vm_product_price` VALUES (12, 13, '14.99', 'USD', 0, 0, 955697213, 955697213, 5, 0, 0);
INSERT INTO `jos_vm_product_price` VALUES (13, 14, '79.99', 'USD', 0, 0, 962351197, 962351271, 5, 0, 0);
INSERT INTO `jos_vm_product_price` VALUES (14, 15, '49.99', 'USD', 0, 0, 962351233, 962351233, 5, 0, 0);
INSERT INTO `jos_vm_product_price` VALUES (15, 16, '59.99', 'USD', 0, 0, 962351259, 962351259, 5, 0, 0);
INSERT INTO `jos_vm_product_price` VALUES (16, 7, '2.99', 'USD', 0, 0, 966589140, 966589140, 6, 0, 0);
