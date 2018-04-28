<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: store.index.php,v 1.11.2.2 2006/01/17 19:04:14 soeren_nb Exp $
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

// Number of customers
$db->query('SELECT count(*) as num_rows FROM #__{vm}_user_info WHERE address_type = \'BT\'');
$db->next_record();
$customers = $db->f('num_rows') ? $db->f('num_rows') : 0;

// Number of active products
$db->query('SELECT count(*) as num_rows FROM #__{vm}_product WHERE vendor_id='.$ps_vendor_id.' AND product_publish="Y"');
$db->next_record();
$active_products = $db->f('num_rows') ? $db->f('num_rows') : 0;

// Number of inactive products
$db->query('SELECT count(*) as num_rows FROM #__{vm}_product WHERE vendor_id='.$ps_vendor_id.' AND product_publish="N"');
$db->next_record();
$inactive_products = $db->f('num_rows') ? $db->f('num_rows') : 0;

// Number of featured products
$db->query('SELECT count(*) as num_rows FROM #__{vm}_product WHERE vendor_id='.$ps_vendor_id.' AND product_special="Y"');
$db->next_record();
$special_products = $db->f('num_rows') ? $db->f('num_rows') : 0;

// 5 last orders
$new_orders= Array();
$db->query('SELECT order_id,order_total FROM #__{vm}_orders WHERE vendor_id='.$ps_vendor_id.' ORDER BY cdate desc limit 5');
while($db->next_record()) {
  $new_orders[$db->f('order_id')] = $db->f('order_total');
}

$db_order_status = new ps_DB;
$db_order_status->query('SELECT order_status_code,order_status_name FROM #__{vm}_order_status');

$orders = Array();
$sum = 0;
while($db_order_status->next_record()) {
  // Number of orders with status...
  $db->query('SELECT count(*) as num_rows FROM #__{vm}_orders WHERE order_status="'.$db_order_status->f("order_status_code").'"');
  $db->next_record();
  $orders[$db_order_status->f("order_status_name")] = $db->f('num_rows') ? $db->f('num_rows') : 0;
  $order_status_code[] = $db_order_status->f("order_status_code");
  $sum += $db->f('num_rows');
}

// last 5 new customers
$new_customers = Array();
$db->query('SELECT id,first_name, last_name, username FROM #__users, #__{vm}_user_info 
              WHERE address_type = \'BT\' AND perms <> \'admin\' 
              AND perms <> \'storeadmin\' 
              AND INSTR(usertype,\'administrator\') = 0 
              AND INSTR(usertype,\'Administrator\') = 0 
              AND id = user_id
              ORDER BY cdate DESC LIMIT 5');

while($db->next_record())
  $new_customers[$db->f("id")] = $db->f('username') ." (" . $db->f('first_name')." ".$db->f('last_name').")";

if( defined( '_PSHOP_ADMIN' ) && !defined( '_RELEASE' )) echo "</td></tr></table>";
?>
<div class="main">
	<div class="sectionname">
              <img src="<?php echo IMAGEURL ?>ps_image/Desktop.gif" width="48px" height="48px" align="center" alt="Desktop" border="0"/>
              <?php echo $VM_LANG->_PHPSHOP_YOUR_STORE."::".$VM_LANG->_PHPSHOP_CONTROL_PANEL; ?>
	</div>
	<br style="clear:both;" />
      <table width="100%" class="adminlist">
        <tr>
          <td width="60%" valign="top">
				<div id="cpanel">
                <?php
				$link = $sess->url($_SERVER['PHP_SELF']."?pshop_mode=admin&page=product.product_list");
				$image = IMAGEURL .'ps_image/shop_products.png';
				$text = $VM_LANG->_PHPSHOP_PRODUCT_LIST_LBL;
				$ps_html->writePanelIcon( $image, $link, $text );
				
				$link = $sess->url($_SERVER['PHP_SELF']."?pshop_mode=admin&page=product.product_category_list");
				$image = IMAGEURL .'ps_image/shop_categories.png';
				$text = $VM_LANG->_PHPSHOP_CATEGORY_LIST_LBL;
				$ps_html->writePanelIcon( $image, $link, $text );
		
				
				$link = $sess->url($_SERVER['PHP_SELF']."?pshop_mode=admin&page=order.order_list");
				$image = IMAGEURL .'ps_image/shop_orders.png';
				$text = $VM_LANG->_PHPSHOP_ORDER_MOD;
				$ps_html->writePanelIcon( $image, $link, $text );
				
				$link = $sess->url($_SERVER['PHP_SELF']."?pshop_mode=admin&page=store.payment_method_list");
				$image = IMAGEURL .'ps_image/shop_payment.png';
				$text = $VM_LANG->_PHPSHOP_PAYMENT_METHOD_LIST_MNU;
				$ps_html->writePanelIcon( $image, $link, $text );
		              
		        if (defined( "_PSHOP_ADMIN" ) ) {
				    $link = $sess->url($_SERVER['PHP_SELF']."?pshop_mode=admin&page=vendor.vendor_list");
				    $image = IMAGEURL .'ps_image/shop_vendors.png';
		            $text =  $VM_LANG->_PHPSHOP_VENDOR_MOD;
				    $ps_html->writePanelIcon( $image, $link, $text );
		        }
		                
				if (defined( "_PSHOP_ADMIN" ) ) { 
					$link = $sess->url($_SERVER['PHP_SELF']."?pshop_mode=admin&page=admin.user_list");
					$image = IMAGEURL .'ps_image/shop_users.png';
					$text = $VM_LANG->_PHPSHOP_USERS;
					$ps_html->writePanelIcon( $image, $link, $text );
				}
		        
		        if (defined( "_PSHOP_ADMIN" ) ) {    
					$link = $sess->url($_SERVER['PHP_SELF']."?pshop_mode=admin&page=admin.show_cfg");
					$image = IMAGEURL .'ps_image/shop_configuration.png';
					$text = $VM_LANG->_PHPSHOP_CONFIG;
					$ps_html->writePanelIcon( $image, $link, $text );
				}
		                
				$link = $sess->url($_SERVER['PHP_SELF']."?pshop_mode=admin&page=store.store_form");
				$image = IMAGEURL .'ps_image/shop_mart.png';
				$text = $VM_LANG->_PHPSHOP_STORE_FORM_MNU;
				$ps_html->writePanelIcon( $image, $link, $text );
		                
				$link = 'http://virtuemart.net/index.php?option=com_content&task=view&id=103&Itemid=67';
				$image = IMAGEURL .'ps_image/shop_help.png';
				$text = $VM_LANG->_PHPSHOP_HELP_MOD;
				$ps_html->writePanelIcon( $image, $link, $text );
				
				?>
			</div>
		</td>
		<td width="40%" valign="top">
		    <?php
		        $tabs = new mShopTabs(0, 1, "_main");
		        $tabs->startPane("content-pane");
		        $tabs->startTab( $VM_LANG->_PHPSHOP_STATISTIC_STATISTICS, "statistic-page");
		    ?>
			<table style="width:95%;">
				<tr> 
				  <th colspan="2" class="title"><?php echo $VM_LANG->_PHPSHOP_STATISTIC_STATISTICS ?></th>
				</tr>
				<tr> 
				  <td width="50%"><?php 
					  echo "<a href=\"".$_SERVER['PHP_SELF']."?option=com_virtuemart&page=admin.user_list\">"
							  .  $VM_LANG->_PHPSHOP_STATISTIC_CUSTOMERS ?></a>:</td>
				  <td width="50%"> <?php echo $customers ?></td>
				</tr>
				<tr> 
				  <td width="50%"><?php 
					  echo "<a href=\"".$_SERVER['PHP_SELF']."?option=com_virtuemart&page=product.product_list\">"
							  .  $VM_LANG->_PHPSHOP_STATISTIC_ACTIVE_PRODUCTS ?></a>:</td>
				  <td width="50%"> <?php echo $active_products ?> </td>
				</tr>
				<tr> 
				  <td width="50%"><?php echo $VM_LANG->_PHPSHOP_STATISTIC_INACTIVE_PRODUCTS ?>:</td>
				  <td width="50%"> <?php  echo $inactive_products ?></td>
				</tr>
				<tr> 
				  <td width="50%"><?php 
					  echo "<a href=\"".$_SERVER['PHP_SELF']."?option=com_virtuemart&page=product.specialprod&filter=featured\">"
							  .  $VM_LANG->_PHPSHOP_SHOW_FEATURED ?></a>:</td>
				  <td width="50%"><?php echo $special_products ?></td>
				</tr>
			</table>
		<?php
		$tabs->endTab();
		$tabs->startTab( $VM_LANG->_PHPSHOP_ORDER_MOD, "order-page");
		?>
			<table style="width:95%;">
				<tr> 
				  <th colspan="2" class="title"><?php 
					  echo "<a href=\"".$_SERVER['PHP_SELF']."?option=com_virtuemart&page=order.order_list\">"
							  .  $VM_LANG->_PHPSHOP_ORDER_MOD ?></a>:</th>
				</tr>
				<?php 
				$i = 0;
				foreach($orders as $order_status_name => $order_count) { ?>
				<tr>
				  <td width="50%"><?php 
					echo "<a href=\"".$_SERVER['PHP_SELF']."?option=com_virtuemart&page=order.order_list&show=".$order_status_code[$i++]."\">";
					echo $order_status_name ."</a>" ?>:</td>
				  <td width="50%"> <?php echo $order_count ?></td>
				</tr>
				<?php } ?>
				<tr> 
				  <td width="50%"><strong><?php echo $VM_LANG->_PHPSHOP_STATISTIC_SUM ?>:</strong></td>
				  <td width="50%"><strong><?php echo $sum ?></strong></td>
				</tr>
			</table>
		<?php
		$tabs->endTab();
		$tabs->startTab( $VM_LANG->_PHPSHOP_STATISTIC_NEW_ORDERS, "neworder-page");
		?>
			<table style="width:95%;">
				<tr>
					<th colspan="2" class="title"><?php echo $VM_LANG->_PHPSHOP_STATISTIC_NEW_ORDERS ?></th>
				</tr>
		<?php 
			foreach($new_orders as $order_id => $total) { ?>
				  <tr>
					<td width="50%"><?php 
					  echo "<a href=\"".$_SERVER['PHP_SELF']."?option=com_virtuemart&page=order.order_print&order_id=$order_id\">";
					  echo $VM_LANG->_PHPSHOP_ORDER_LIST_ID." ". $order_id ."</a>" ?>:</td>
					<td width="50%">(<?php echo $total ." ".$_SESSION['vendor_currency'] ?>)</td>
				</tr>
				<?php 
			} ?>
			</table>
		<?php
		$tabs->endTab();
		
		if (defined( "_PSHOP_ADMIN" ) ) {
		$tabs->startTab( $VM_LANG->_PHPSHOP_STATISTIC_NEW_CUSTOMERS, "newcustomer-page");
		?>
			<table style="width:95%;">
				<tr> 
				  <th colspan="2" class="title"><?php echo $VM_LANG->_PHPSHOP_STATISTIC_NEW_CUSTOMERS ?></th>
				</tr>
				<?php 
				foreach($new_customers as $id => $name) { ?>
				<tr>
				  <td colspan="2">
					  <a href="<?php $sess->purl( $_SERVER['PHP_SELF'] .'?page=admin.user_form&user_id='. $id ); ?>">
					  <?php echo $name ?></a></td>
				</tr>
				<?php 
				} ?>
			</table>
		<?php
		$tabs->endTab();
		}
		$tabs->endPane();
		?> 
		</td>
	</tr>
</table>
</div>
