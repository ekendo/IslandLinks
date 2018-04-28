<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: account.index.php,v 1.7.2.1 2006/03/10 15:55:15 soeren_nb Exp $
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

require_once(CLASSPATH.'ps_order.php');
$ps_order = new ps_order;

/* Set Dynamic Page Title when applicable */
$mainframe->setPageTitle( $VM_LANG->_PHPSHOP_ACCOUNT_TITLE );
    
if ($perm->is_registered_customer($auth['user_id'])) {

?>
  <strong><?php echo $VM_LANG->_PHPSHOP_ACC_CUSTOMER_ACCOUNT ?></strong>
  <?php  echo $auth["first_name"] . " " . $auth["last_name"] . "<br />";?>
  <br />
  <table border="0" cellspacing="0" cellpadding="10" width="100%" align="center">
  
          
    <tr>
      <td>
      <strong><a href="<?php $sess->purl(SECUREURL . "index.php?page=account.billing") ?>">
          <?php 
          echo "<img src=\"".IMAGEURL."ps_image/identity.png\" align=\"middle\" height=\"48\" width=\"48\" border=\"0\" alt=\"".$VM_LANG->_PHPSHOP_ACCOUNT_TITLE."\" />&nbsp;";
          echo $VM_LANG->_PHPSHOP_ACC_ACCOUNT_INFO ?></a></strong>
          <br /><?php echo $VM_LANG->_PHPSHOP_ACC_UPD_BILL ?>
      </td>
    </tr>
    <?php
    if(CHECKOUT_STYLE == 1 || CHECKOUT_STYLE == 2) {
	?>
		<tr><td>&nbsp;</td></tr>
		
		<tr>
		  <td><hr />
		  <strong><a href="<?php $sess->purl(SECUREURL . "index.php?page=account.shipping") ?>"><?php
		  echo "<img src=\"".IMAGEURL."ps_image/web.png\" align=\"middle\" border=\"0\" height=\"32\" width=\"32\" alt=\"".$VM_LANG->_PHPSHOP_ACC_SHIP_INFO."\" />&nbsp;&nbsp;&nbsp;";
		  echo $VM_LANG->_PHPSHOP_ACC_SHIP_INFO ?></a></strong>
			<br />
			<?php echo $VM_LANG->_PHPSHOP_ACC_UPD_SHIP ?>
		  </td>
		</tr>
		<?php
	}
	?>
    <tr><td>&nbsp;</td></tr>
    
    <tr>
      <td>
      	<hr />
      	<strong><?php 
	      echo "<img src=\"".IMAGEURL."ps_image/package.png\" align=\"middle\" height=\"32\" width=\"32\" border=\"0\" alt=\"".$VM_LANG->_PHPSHOP_ACC_ORDER_INFO."\" />&nbsp;&nbsp;&nbsp;";
	      echo $VM_LANG->_PHPSHOP_ACC_ORDER_INFO ?>
	    </strong>
        <?php $ps_order->list_order("A", "1" ); ?>
      </td>
    </tr>
    
</table>
<!-- Body ends here -->
<?php } 
else { 
    echo _LOGIN_TEXT .'<br/><br/><br/>';
    include(PAGEPATH.'checkout.login_form.php');
} ?>
