<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: TODO.php,v 1.2.2.2 2006/03/24 18:24:24 soeren_nb Exp $
* @package VirtueMart
* @subpackage core
* @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/
?>
<pre>
~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  VirtueMart To-Do List
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

 Know Bugs
##################

See Bugtracker:
http://virtuemart.net/index.php?option=com_flyspray&Itemid=91

Not logged:

# Redirection to child product fails in Internet Explorer when SEF mode = On


 FEATURES
##################
* add a field "product_packing_unit" to mos_{vm}_product

* XML - Product Data Import/Export
* XML - Order Data Import/Export

* make Orders changeable afterwards
* Multiple-Currency Support 
  * changeable by the Customer in the Frontend
  * fetches Exchange Rates from the Federal Bank of Germany
* Order Reports per Customer
* Shipping Rate per Product
* "Compare Products"
* Tool to Remove all Products
* Add Discount on *all* Products
* Discount for Cart Total (> $100 = 1% discount...)
* Tool to Remove Orphan Images

* Gift- / Wish - List
* allow "Send as Gift" with individual Text on Order

* Modular Checkout
* Changeable Registration Form
  - allow Declaration of new Fields
  - make Fields reorderable
  - make Fields required / not required
  - switch Shopper Group including / excluding tax

 General to-do 
##################

* improve performance
  - Reduce Numbers of SQL Queries
  - Remove doubled function calls
  
* XHTML - Compliance, make it Barrier-free
* clean up checkout

 Open Payment Gateways
######################

* Payfuse
</pre>