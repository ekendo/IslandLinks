<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: shop.basket_short.php,v 1.4 2005/11/18 16:43:50 soeren_nb Exp $
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

require_once(CLASSPATH. 'ps_product.php' );
$ps_product =& new ps_product;
require_once(CLASSPATH. 'ps_shipping_method.php' );
require_once(CLASSPATH. 'ps_checkout.php' );
$ps_checkout =& new ps_checkout;

global $CURRENCY_DISPLAY, $VM_LANG, $vars;

$cart = $_SESSION['cart'];
$auth = $_SESSION['auth'];
  if ($cart["idx"] == 0) {
     echo $VM_LANG->_PHPSHOP_EMPTY_CART;
     $checkout = false;
  }
  else {
    $checkout = True;

    $total = $order_taxable = $order_tax = 0;
    $amount = 0;
    $weight_total = 0;
    
    for ($i=0;$i<$cart["idx"];$i++) {

      $price = $ps_product->get_adjusted_attribute_price($cart[$i]["product_id"],$cart[$i]["description"]);
      $amount += $cart[$i]["quantity"];

      if (@$auth["show_price_including_tax"] == 1) {
        $my_taxrate = $ps_product->get_product_taxrate($cart[$i]["product_id"] );
        $price["product_price"] *= ($my_taxrate+1);
      }
      $subtotal = round( $price["product_price"], 2 ) * $cart[$i]["quantity"];
      $total += $subtotal;

      $weight_subtotal = ps_shipping_method::get_weight($cart[$i]["product_id"]) * $cart[$i]["quantity"];
      $weight_total += $weight_subtotal;
    }
    
    if( !empty($_SESSION['coupon_discount']) ) {
        $total -= $_SESSION['coupon_discount'];
    }
    
    if ($amount > 1) 
      echo $amount ." ". $VM_LANG->_PHPSHOP_PRODUCTS_LBL;
    else
      echo $amount ." ". $VM_LANG->_PHPSHOP_PRODUCT_LBL;
    
    echo ",<br /> ";
    
    echo $CURRENCY_DISPLAY->getFullValue( $total );
  }
?>
