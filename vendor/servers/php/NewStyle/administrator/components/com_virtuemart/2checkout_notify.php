<?php 
/**
* We need this file to redirect from 2Checkout 
* to our Order Confirmation Handler
*
* @version $Id: 2checkout_notify.php,v 1.2 2005/09/27 17:48:50 soeren_nb Exp $
* @package VirtueMart
* @subpackage payment
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

$_REQUEST['option'] = "com_virtuemart";
$_REQUEST['page'] = "checkout.2Checkout_result";
$_REQUEST['Itemid'] = 1;
require_once("index.php");
?>
