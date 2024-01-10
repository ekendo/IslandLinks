<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
* @version $Id: INSTALLATION.php,v 1.2 2005/11/16 19:33:55 soeren_nb Exp $
* @package VirtueMart
* @subpackage core
* @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*
* http://virtuemart.net
*/
?>
<pre>
The easy & automatic installation procedure is explained in the file
README.php of the COMPLETE PACKAGE.

You shouldn't use these instructions, when you have downloaded the
COMPLETE PACKAGE.

You should only use this file, when you have safe mode problems
or other problems with the Joomla / Mambo installer and all
efforts fail to use the upload installer.


Safe Mode = On ? Manual Installation is your saviour.

MANUAL INSTALLATION OF VIRTUEMART

ABOUT THIS FILE
-----------------
This file is meant to provide hints on the 
Manual Installation Procedure for VirtueMart.


ABOUT VirtueMart
------------------------------
VirtueMart is an Open Source Online-Shop plugin for Joomla and Mambo.



MANUAL INSTALLATION PROCEDURE
--------------------------------------------------
  
1. Unpack the archive file (the one which contains this file as "README.txt"):

	VirtueMart_Manual-Installation-Package_1.x.tar.gz
	
	You should now see some directories:
	  * /administrator
	  * /components
	  * /modules
	  * /mambots
	
	The directory structure in those directories is the
	same as in your Joomla/Mambo site.
	
2.  Open up an FTP Connection to your site and upload
	the directories to your Joomla/Mambo site.
	
	/components
	to
	  /path-to-site-root/components/

	/administrator
	to
	  /path-to-site-root/administrator/
	  
	/modules
	to
	  /path-to-site-root/modules/

	/mambots
	to
	  /path-to-site-root/mambots/
	  
	You will probably have to confirm overwriting some existing files 
	in these directories.	
	An existing configuration file will not be overwritten.


3.  Login in to the Joomla / Mambo Administration (the so-called Backend).

	  http://www.xxxxxx.com/administrator/
	
	* When having logged in, you see this URL in the address bar:
	
	  http://www.xxxxxx.com/administrator/index2.php
	
	* Now just add "?option=com_virtuemart" after index2.php, so it looks like this
	  in your browser's address bar:
	  
		http://www.xxxxxx.com/administrator/index2.php?option=com_virtuemart
	
	  and submit (press Enter).
	
	* You should now see the "Installation was successful..." Screen.
	  There you can click on 
		"GO TO THE SHOP >>" 
	  or on
		"INSTALL SAMPLE DATA" (when you want to have some sample products and categories in your Shop).

	* That's it.	  

Modules and Mambots.
-----------------------

4. The Modules and Mambots in the archive are add-ons, but most important is the VirtueMart Main Module,
	which consists of two files:
	/modules/mod_virtuemart.php
	/modules/mod_virtuemart.xml
	
	You need this module to be able to access and browse your shop
	like a customer.

	Browse to phpMyAdmin and select your database.
	Select "SQL" in the toolbar on the middle top.
	
	Now paste these queries when you're running Joomla with a jos_ prefix:
	
####
INSERT IGNORE INTO `jos_modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES ( 'VirtueMart Module', '', 99, 'left', 0, '0000-00-00 00:00:00', 1, 'mod_virtuemart', 0, 0, 1, '', 0, 0);
INSERT IGNORE INTO `jos_modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES ( 'VirtueMart TopTen products Module', '', 99, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_virtuemart_topten', 0, 0, 1, '', 0, 0);
INSERT IGNORE INTO `jos_modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES ( 'Product Scroller', '', 99, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_productscroller', 0, 0, 1, '', 0, 0);
INSERT IGNORE INTO `jos_modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES ( 'Product Categories Module', '', 99, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_product_categories', 0, 0, 1, '', 0, 0);
INSERT IGNORE INTO `jos_modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES ( 'All-In-One New/Featured/TopTen/Random Products Module', '', 99, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_virtuemart_allinone', 0, 0, 1, '', 0, 0);
INSERT IGNORE INTO `jos_modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES ( 'Your Cart Module', '', 99, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_virtuemart_cart', 0, 0, 1, '', 0, 0);
INSERT IGNORE INTO `jos_modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES ( 'Featured Products', '', 99, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_virtuemart_featureprod', 0, 0, 1, '', 0, 0);
INSERT IGNORE INTO `jos_modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES ( 'Latest products', '', 99, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_virtuemart_latestprod', 0, 0, 1, '', 0, 0);
INSERT IGNORE INTO `jos_modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES ( 'Manufacturer Module', '', 99, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_virtuemart_manufacturers', 0, 0, 1, '', 0, 0);
INSERT IGNORE INTO `jos_modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES ( 'Random Products Module', '', 99, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_virtuemart_randomprod', 0, 0, 1, '', 0, 0);
INSERT IGNORE INTO `jos_modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES ( 'VirtueMart Search Module', '', 99, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_virtuemart_search', 0, 0, 1, '', 0, 0);

INSERT IGNORE INTO `jos_mambots` (`id`, `name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) VALUES (21, 'mosproductsnap', 'mosproductsnap', 'content', 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', '');
INSERT IGNORE INTO `jos_mambots` (`id`, `name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) VALUES (22, 'ProductSearchBot', 'virtuemart.searchbot', 'search', 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', '');
####

	or these queries when you run Mambo with a mos_ table prefix.

####
INSERT IGNORE INTO `mos_modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES ( 'VirtueMart Module', '', 99, 'left', 0, '0000-00-00 00:00:00', 1, 'mod_virtuemart', 0, 0, 1, '', 0, 0);
INSERT IGNORE INTO `mos_modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES ( 'VirtueMart TopTen products Module', '', 99, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_virtuemart_topten', 0, 0, 1, '', 0, 0);
INSERT IGNORE INTO `mos_modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES ( 'Product Scroller', '', 99, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_productscroller', 0, 0, 1, '', 0, 0);
INSERT IGNORE INTO `mos_modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES ( 'Product Categories Module', '', 99, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_product_categories', 0, 0, 1, '', 0, 0);
INSERT IGNORE INTO `mos_modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES ( 'All-In-One New/Featured/TopTen/Random Products Module', '', 99, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_virtuemart_allinone', 0, 0, 1, '', 0, 0);
INSERT IGNORE INTO `mos_modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES ( 'Your Cart Module', '', 99, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_virtuemart_cart', 0, 0, 1, '', 0, 0);
INSERT IGNORE INTO `mos_modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES ( 'Featured Products', '', 99, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_virtuemart_featureprod', 0, 0, 1, '', 0, 0);
INSERT IGNORE INTO `mos_modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES ( 'Latest products', '', 99, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_virtuemart_latestprod', 0, 0, 1, '', 0, 0);
INSERT IGNORE INTO `mos_modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES ( 'Manufacturer Module', '', 99, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_virtuemart_manufacturers', 0, 0, 1, '', 0, 0);
INSERT IGNORE INTO `mos_modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES ( 'Random Products Module', '', 99, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_virtuemart_randomprod', 0, 0, 1, '', 0, 0);
INSERT IGNORE INTO `mos_modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES ( 'VirtueMart Search Module', '', 99, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_virtuemart_search', 0, 0, 1, '', 0, 0);

INSERT IGNORE INTO `mos_mambots` (`id`, `name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) VALUES (21, 'mosproductsnap', 'mosproductsnap', 'content', 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', '');
INSERT IGNORE INTO `mos_mambots` (`id`, `name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) VALUES (22, 'ProductSearchBot', 'virtuemart.searchbot', 'search', 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', '');
####


5.  In the Joomla / Mambo backend browse to "Modules" => "Site Modules" and
	search for VirtueMart modules.
	Select each of them step-by-step and set them to "published".
	Please note that you MUST PUBLISH THE 
		"VirtueMart Module"

		
</pre>