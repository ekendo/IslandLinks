<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: store.payment_method_keychange.php,v 1.4 2005/11/03 21:01:32 soeren_nb Exp $
* @package VirtueMart
* @subpackage html
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
mm_showMyFileName( __FILE__ );

$payment_method_id = mosGetParam( $_REQUEST, 'payment_method_id', null );
$passkey = mosGetParam( $_POST, 'passkey', null );
$Itemid = mosGetParam( $_REQUEST, 'Itemid', null );
$task = mosGetParam( $_POST, 'task', null );

if ( $payment_method_id ) {
  echo "<h2>".$VM_LANG->_PHPSHOP_CHANGE_PASSKEY_FORM."</h2>";
  
  // Get the Transaction Key securely from the database
  $db->query( "SELECT DECODE(payment_passkey,'".ENCODE_KEY."') AS `passkey` FROM #__{vm}_payment_method WHERE payment_method_id='$payment_method_id'" );
  $db->next_record();
	
  // authenticated. Show "Change Key" and "Password" Form
  if( mShop_checkpass() && empty( $passkey ) ) {
	
	echo "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
	echo "<table class=\"adminForm\">\n";
	echo "<tr><td>".$VM_LANG->_PHPSHOP_CURRENT_TRANSACTION_KEY.":</td>\n";
	echo "<td><input type=\"text\" name=\"passkey\" value=\"". $db->f('passkey') ."\" /></td></tr>\n";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td></tr>\n";
	echo "<tr><td>".$VM_LANG->_PHPSHOP_TYPE_PASSWORD.":</td>\n";
	echo "<td><input type=\"password\" name=\"passwd\" value=\"\" /></td></tr>\n";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td></tr>\n";
	echo "<tr><td>&nbsp;</td><td><input name=\"submit\" type=\"submit\" /></td></tr>\n";
	echo "</table>\n";
	echo "<input type=\"hidden\" name=\"option\" value=\"com_virtuemart\" />\n";
	echo "<input type=\"hidden\" name=\"Itemid\" value=\"$Itemid\" />\n";
	echo "<input type=\"hidden\" name=\"payment_method_id\" value=\"$payment_method_id\" />\n";
	echo "<input type=\"hidden\" name=\"task\" value=\"changekey\" />\n";
	echo "<input type=\"hidden\" name=\"pshop_mode\" value=\"admin\" />\n";
	echo "<input type=\"hidden\" name=\"page\" value=\"store.payment_method_keychange\" />\n";
	echo "</form>\n";	
	
  }
  // authenticated
  elseif ( mShop_checkpass() && !empty($passkey) && $task == "changekey") {
  
	$q = "UPDATE #__{vm}_payment_method ";
	$q .= "SET payment_passkey = ENCODE('$passkey','" . ENCODE_KEY . "')\n";
	$q .= "WHERE payment_method_id='$payment_method_id';";
	$db->query( $q );
	mosRedirect( $sess->url($_SERVER['PHP_SELF']."?page=store.payment_method_form&payment_method_id=$payment_method_id"), $VM_LANG->_PHPSHOP_CHANGE_PASSKEY_SUCCESS);
  }
  // not authenticated
  else {	
	require_once( CLASSPATH. "ps_checkout.php" );
	echo "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
	echo "<table class=\"adminForm\">\n";
	echo "<tr><td>".$VM_LANG->_PHPSHOP_CURRENT_TRANSACTION_KEY.":</td><td>". ps_checkout::asterisk_pad( $db->f('passkey'), 4 )."</td></tr>\n";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td></tr>\n";
	echo "<tr><td>".$VM_LANG->_PHPSHOP_TYPE_PASSWORD.":</td>\n";
	echo "<td><input type=\"password\" name=\"passwd\" value=\"\" /></td></tr>\n";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td></tr>\n";
	echo "<tr><td>&nbsp;</td><td><input name=\"submit\" type=\"submit\" /></td></tr>\n";
	echo "</table>\n";
	echo "<input type=\"hidden\" name=\"option\" value=\"com_virtuemart\" />\n";
	echo "<input type=\"hidden\" name=\"Itemid\" value=\"$Itemid\" />\n";
	echo "<input type=\"hidden\" name=\"pshop_mode\" value=\"admin\" />\n";
	echo "<input type=\"hidden\" name=\"payment_method_id\" value=\"$payment_method_id\" />\n";
	echo "<input type=\"hidden\" name=\"page\" value=\"store.payment_method_keychange\" />\n";
	echo "</form>\n";
	
  }
}
else {
  echo "<script>alert(\"Error: Payment Method ID was not provided.\"); window.history.go(-1); </script>\n";
}


?>
