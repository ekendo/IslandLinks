<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
* @version $Id: README.php,v 1.5.2.1 2006/03/10 19:27:42 soeren_nb Exp $
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
****************
VirtueMart
Version: 1.0.x
****************
Complete Package for Mambo >= 4.5.1 and Joomla 1.0.x

You can't use this software on an earlier Mambo version than 4.5.1 (e.g. Mambo 4.5 1.0.9) 
without running into serious problems.

Copyright (C) 2004-2006 Soeren Eberhardt. All rights reserved.
License: http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
VirtueMart is free software. This version may have been modified pursuant
to the GNU General Public License, and as distributed it includes or
is derivative of works licensed under the GNU General Public License or
other free or open source software licenses.

Community Home: http://virtuemart.net

****************

##########################
Package Contents:
##########################

 * 1 Component (com_virtuemart_1.0.3.tar.gz)
   INSTALLATION REQUIRED!
   
 * 1 Main module (mod_virtuemart_1.0.3.tar.gz)
   INSTALLATION REQUIRED!
   
 * 8 additional Modules
 
 * 2 Mambots 
   - 1 SearchBot for Integration into the site search (virtuemart.searchbot.tar.gz), 
   - 1 Content Mambot for displaying product details in content items (mosproductsnap.tar.gz)

##########################
   ABOUT
##########################
VirtueMart (formerly known as "mambo-phpShop") is an Online-Shop / Shopping Cart Web-Application.
It's a Component (means plugin / extension) for a Content Management System called Joomla / Mambo
and can't be used without Joomla / Mambo. It installs fairly easy using the automatic installers. 
It's intended for use in small / mid-sized Online businesses / Online-Shops. 
So every user who wants to build up a Online Store can use this component for selling something to customers.

This package is for New Installations and updates from mambo-phpShop. 

You just need a working Joomla/Mambo Installation. 
You can get your copy  of Joomla from http://joomla.org

This package contains some code from the original 0.8.0 Edikon Corp. phpShop distribution available at www.phpshop.org

This package was tested on 
- Mambo 4.5.1a
- Mambo 4.5.2.3
- Mambo 4.5.3h
- Mambo 4.6 beta
- Joomla 1.0.x

 -- IMPORTANT --
Please note that module and component SHOULD be used together! 
The thing is that you can only access all areas of the component via the VirtueMart Main Module links.

You can surely create a new Menu Item linking to VirtueMart, but you must also publish the VirtueMart module.

##########################
   INSTALLATION
##########################
The installation is really easy - 
thanks to the automatic installer!
You don't need to unpack any of the archives in this complete package!

1. If you have unpacked this archive (VirtueMart_x.x_COMPLETE_PACKAGE.zip), 
	you see a lot of other archives.
	- com_virtuemart_x.xxx.tar.gz, 
	- some files beginning with mod_*.tar.gz 
	- 2 other Packages (these are the so-called Mambots).
    
2. Login to the Mambo/Joomla Administrator.
	
	Now go to "Installers" => "Components"
	or - if you are using an older Mambo version: "Components" --> "Install/Uninstall".
	
	You can see an upload form now.
	Select the file 
	- com_virtuemart_x.xx.tar.gz 
	and click 'Upload Component'
	
	If everything is ok, you should see a "Welcome ..." screen.
	Choose you way of installation to finish the component installation.
	
3. Now we have to install the main module, which will help you to browse
	your categories and products.
	Go to "Installers" => "Modules"
	(or - if you're using an older Mambo version: "Modules" => "Install/Uninstall"), 
	and select the file 
	- mod_virtuemart_x.xx.tar.gz 
	and then click 'Upload module'.
    
4. The module is installed, but it still is not published!
	To publish that module on your site, you must go to the list of 
	your all modules. So now head on to 
	"Modules" => "Site Modules".
	You should somewhere see a module entry for "Virtuemart Module"
	with "mod_virtuemart" at the end of that row.
	If necessary, browse to the next page of the module list.
	If you've found the module, please select it's checkbox and click
	on "Edit" in the toolbar.
	Make your settings and don't forget to select "Published? - Yes".

       Note:
       Since unpublished modules and components appear by default on 
       the last page of  the modules listed, you may need to browse component 
       and module pages until you find the VirtueMart module.
       
	Now Save - and: Done.   
    
       Note: As long as the VirtueMart main module is NOT published, VirtueMart can't be used properly.

        IF successful, the installer will have created the following directory structure:
        
         /components/com_virtuemart
         - contains code for non-administrative surfing and ordering
         
         /components/com_virtuemart/shop_image
         /components/com_virtuemart/shop_image/vendor
         - contains the vendor images
         
         /components/com_virtuemart/js
         - contains JavaScript Files
         
         /components/com_virtuemart/shop_image/product
         - contains the product images
         
         /components/com_virtuemart/shop_image/ps_image
         - contains general administration/shop images
         
         /components/com_virtuemart/shop_image/stars
         
         /administrator/components/com_virtuemart
         - contains config files and the main virtuemart-parser
         
         /administrator/components/com_virtuemart/classes
         - contains all the class files
         
         /administrator/components/com_virtuemart/html
         - contains all pages accessible for the shop
         
         /administrator/components/com_virtuemart/html/templates
         
         /administrator/components/com_virtuemart/languages
         - contains the language files
          
         /modules/dtree
         - contains JS files and images for dTree usage.
         
   
##########################
   UNINSTALL
##########################

1. Go to "Installers" => "Components" (or "Modules")
	( or - if you're using Mambo - "Components" ( or "Modules" ) => "Install/Uninstall"
        
2. Now select 'VirtueMart' ( or for the module: 'mod_virtuemart' )
	and click on 'Delete'.
   
    Done.


############################
   DEVELOPER INFORMATION
###########################
For all developers and users who worked with mambo-phpShop, it's important to know that VirtueMart 
is the successor of mambo-phpShop. So you as a developer must know how to update your extensions, 
modules and/or hacks to work in VirtueMart.

The name change mambo-phpShop => VirtueMart requires us to rename files and tables related to the shop.

Filenames cange
---------------------
VirtueMart will be installed into the directories
	/administrator/components/com_virtuemart and
	/components/com_virtuemart

We have provided an easy and fail-safe Update script that

    * copies your images and product-related files to the new directory
    * copies your existing templates to the new directory
    * copies your configuration file (phpshop.cfg.php => virtuemart.cfg.php
    * renames the tables from *_pshop_* to *_vm_*

As you might know, the most modules and mambots include the file
phpshop_parser.php (which builds up the Shop framework). It will still be there to be included, 
but it now just includes the file virtuemart_parser.php, so you don't need to change anything.

Variable Name changes
----------------------------
Most important is the name change for the language object $PHPSHOP_LANG, which has the name $VM_LANG now. 
It's an object of the class vmLanguage which extends the class vmAbstractLanguage (which was formerly handled as 
phpShopLanguage extends mosAbstractLanguage). For compatibility reasons, $PHPSHOP_LANG will still be available in the scripts.

Table Prefixes for shop-related tables
---------------------------------------------
All shop-related tables had the secondary prefix _pshop_.
This prefix will change to _vm_. The tag that is to be replaced by the defineable Table Prefix ( like #_ in Mambo/Joomla, this will be {vm}).

So a table is called using

"SELECT `product_id` FROM `#__{vm}_product` WHERE `product_id`=4 "

You must use the VirtueMart database object! It translates the placeholder
{vm} into the VirtueMart table prefix.

Please update your extensions or hacks or mods to include this.
Although we will catch all queries that still have '_pshop_' in it and replace that by '_vm_', 
we can't do that when your code uses Mambo's/Joomla's database object.


VirtueMart will no longer use Mambo's/Joomla's User Table
---------------------------------------------
mambo-phpShop has always used the table mos_users for storing Customer Information. 
On installation it altered the table structure of that table. This is no longer wanted, because those 
modification sometimes lead to problems with other components like the phpBB integration for Mambo/Joomla.
VirtueMart will instead hold the customer information in the table mos_vm_user_info. 
This table holds an ID (as a foreign key pointing to a user id in the table mos_users).
When updating from mambo-phpShop to VirtueMart, the update script will take care of that 
and copy all necessary information from mos_users to the renamed table mos_vm_user_info.

#
############################
#

For updates / changes / hints please read the ChangeLog!
based on mambo-phpShop 1.2 stable (patch level 3)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


Developers, Documentation Writers, Helpers and Coders are welcome to help us. 
Please contact me: soeren@virtuemart.net

This VirtueMart component can be developed much further...
</pre>