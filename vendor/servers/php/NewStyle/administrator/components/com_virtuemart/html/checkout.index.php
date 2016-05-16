<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: checkout.index.php,v 1.5.2.3 2006/04/27 19:35:52 soeren_nb Exp $
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
require_once( CLASSPATH . "ps_checkout.php" );

$zone_qty = mosgetparam( $_REQUEST, 'zone_qty');
$ship_to_info_id = mosgetparam( $_REQUEST, 'ship_to_info_id');
$shipping_rate_id = urldecode(mosGetParam( $_REQUEST, "shipping_rate_id", null ));
$payment_method_id = mosgetparam( $_REQUEST, 'payment_method_id');
$Itemid = mosgetparam( $_REQUEST, 'Itemid', null);
$checkout_next_step = mosgetparam( $_REQUEST, 'checkout_next_step', 2);
$checkout_this_step = mosgetparam( $_REQUEST, 'checkout_this_step', 2);
if( empty( $vars["error"] ) ) {
	$checkout_this_step = $checkout_next_step;
}
if( empty( $checkout_this_step )) {
	$checkout_this_step = 2;
}

echo '<h3>'. $VM_LANG->_PHPSHOP_CHECKOUT_TITLE .'</h3>';

/*****************************
** Checkout Bar Feature
**/
if (SHOW_CHECKOUT_BAR == '1') {
    
    // This is the file, where the checkout symbols are displayed
    // 1 - 2 - 3 - 4 , you know ;-)
    include( PAGEPATH . 'checkout_bar.php'); 
    
}
/**
** End Checkout Bar Feature
*****************************/
	
/* Decide, which Checkout Step is the next one 
* $checkout_this_step controls the step thru the checkout process
* we have the following steps

* -CHECK_OUT_GET_SHIPPING_ADDR
* let the user choose a shipto address

* -CHECK_OUT_GET_SHIPPING_METHOD
* let the user choose a shipto metho for the ship to address

* -CHECK_OUT_GET_PAYMENT_METHOD
* let the user choose a payment method

* -CHECK_OUT_GET_FINAL_CONFIRMATION
* shows a total summary including all payments, taxes, fees etc. and let the user confirm
*/
if( $checkout_this_step == CHECK_OUT_GET_SHIPPING_ADDR ) {
    if (CHECKOUT_STYLE == '1')
        $checkout_next_step = CHECK_OUT_GET_SHIPPING_METHOD;
    elseif (CHECKOUT_STYLE == '2') {
        $checkout_next_step = CHECK_OUT_GET_PAYMENT_METHOD;
    }
    elseif (CHECKOUT_STYLE == '3') {
        $checkout_this_step = CHECK_OUT_GET_SHIPPING_METHOD;
        $checkout_next_step = CHECK_OUT_GET_PAYMENT_METHOD;
    }
    elseif (CHECKOUT_STYLE == '4') {
        $checkout_this_step = CHECK_OUT_GET_PAYMENT_METHOD;
        $checkout_next_step = CHECK_OUT_GET_FINAL_CONFIRMATION;
    }
    else
        $checkout_next_step = CHECK_OUT_GET_SHIPPING_METHOD;
}
elseif  ($checkout_this_step == CHECK_OUT_GET_SHIPPING_METHOD) {
      $checkout_next_step = CHECK_OUT_GET_PAYMENT_METHOD;
}
elseif ($checkout_this_step == CHECK_OUT_GET_PAYMENT_METHOD) {
    $checkout_next_step = CHECK_OUT_GET_FINAL_CONFIRMATION;
}
if ($checkout_this_step == CHECK_OUT_GET_FINAL_CONFIRMATION) {
    $checkout_next_step = "";
    include(PAGEPATH . 'ro_basket.php');
}
else {
    if( $my->id > 0 ){
    	$show_basket = true;
    }
    else {
    	$show_basket = false;
    }
        
    include(PAGEPATH . 'basket.php');

}

echo '<br />';
    


if ($checkout) {
    // We have something in the Card so move on
    if ($perm->is_registered_customer($auth['user_id'])) { // user is logged in and a registered customer
            ?>
<form action="<?php echo SECUREURL ?>index.php" method="post" name="adminForm">
    <input type="hidden" name="checkout_next_step" value="<?php echo $checkout_next_step ?>" />
    <input type="hidden" name="checkout_this_step" value="<?php echo $checkout_this_step ?>" />
    <input type="hidden" name="zone_qty" value="<?php echo $zone_qty ?>" />
    <input type="hidden" name="option" value="com_virtuemart" />
    <input type="hidden" name="Itemid" value="<?php echo $Itemid ?>" />
    <input type="hidden" name="user_id" value="<?php echo $my->id ?>" />
    <?php		
	    $label = "\$lbl = \$VM_LANG->_PHPSHOP_CHECKOUT_MSG_$checkout_this_step;";
	    eval($label);
	    echo "<h4>".$lbl."</h4>";
	    /* Set Dynamic Page Title when applicable */
	    $mainframe->setPageTitle( $lbl );

       
        if ($checkout_this_step == CHECK_OUT_GET_SHIPPING_ADDR) {
            // CHECK_OUT_GET_SHIPPING_ADDR
            // let the user choose a shipto address
            include(PAGEPATH . 'checkout.customer_info.php');
            ?>
        <!-- Customer Ship To -->
        <input type="hidden" name="page" value="checkout.index" />
        <input type="hidden" name="func" value="checkoutProcess" />
        <table border="0" cellspacing="0" cellpadding="2" width="100%">
            <tr class="sectiontableheader">
                <th align="left" colspan="2"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUST_SHIPPING_LBL ?> :
                </th>
            </tr>
            <tr>
                <td colspan="2">
                <?php echo $VM_LANG->_PHPSHOP_ADD_SHIPTO_1 ?>
                <a href="<?php $sess->purl(SECUREURL . "index.php?page=account.shipto&next_page=checkout.index");?>">
                <?php echo $VM_LANG->_PHPSHOP_ADD_SHIPTO_2 ?></a>.
                </td>
            </tr>
            <tr>
                <td colspan="2">
                <?php $ps_checkout->ship_to_addresses_radio($auth["user_id"], "ship_to_info_id", $ship_to_info_id);
                ?>
                </td>
            </tr>
        </table>
        <!-- END Customer Ship To -->
        <br />
<?php
        }
            
        elseif ($checkout_this_step == CHECK_OUT_GET_SHIPPING_METHOD) { 
        
            if( empty( $ship_to_info_id )) {
                // Get the Bill to user_info_id
                $database->setQuery( "SELECT user_info_id FROM #__users WHERE id='".$my->id."'" );
                $vars["ship_to_info_id"] = $ship_to_info_id = $database->loadResult();
            }
            $vars["weight"] = $weight_total;
            $i = 0;
            
            foreach( $PSHOP_SHIPPING_MODULES as $shipping_module ) {
            	$vmLogger->debug( 'Starting Shipping module: '.$shipping_module );
                include_once( CLASSPATH. "shipping/".$shipping_module.".php" );
                eval( "\$SHIPPING =& new ".$shipping_module."();");
                $SHIPPING->list_rates( $vars );
                echo "<br/><hr/>";
            }
            ?>
            <input type="hidden" name="page" value="checkout.index" />
            <input type="hidden" name="func" value="checkoutProcess" />
            <input type="hidden" name="ship_to_info_id" value="<?php echo $ship_to_info_id ?>" />
            <?php 
        }
        
        /*** -CHECK_OUT_GET_PAYMENT_METHOD
        * let the user choose a payment method  ***/
        elseif ($checkout_this_step == CHECK_OUT_GET_PAYMENT_METHOD) {

            require_once(CLASSPATH . 'ps_payment_method.php');
            $ps_payment_method = new ps_payment_method;
            include(PAGEPATH . 'checkout.paymentradio.php');  ?>
            
            <input type="hidden" name="page" value="checkout.index" />
            <input type="hidden" name="func" value="checkoutprocess" />
            <input type="hidden" name="ship_to_info_id" value="<?php echo $ship_to_info_id ?>" />
            <input type="hidden" name="shipping_rate_id" value="<?php echo urlencode($shipping_rate_id) ?>" />
            <?php
        } 
        
        
        elseif ($checkout_this_step == CHECK_OUT_GET_FINAL_CONFIRMATION) {
            // -CHECK_OUT_GET_FINAL_CONFIRMATION
            // shows a total summary including all payments, taxes, fees etc. 
            // Now llet the user confirm
            ?>
            <input type="hidden" name="page" value="<?php echo $modulename ?>.thankyou" />
            <input type="hidden" name="func" value="checkoutprocess" />
            <input type="hidden" name="ship_to_info_id" value="<?php echo $ship_to_info_id ?>" />
            <input type="hidden" name="shipping_rate_id" value="<?php echo urlencode($shipping_rate_id) ?>" />
            <input type="hidden" name="payment_method_id" value="<?php echo $payment_method_id ?>" />
            <?php 
            // include(PAGEPATH.'ro_basket.php');
        } 
 ?>
    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr >
            <td><?php 
            if (!defined('_MIN_POV_REACHED')) { ?>
                <div align="center">
                    <script type="text/javascript">alert('<?php echo $VM_LANG->_PHPSHOP_CHECKOUT_ERR_MIN_POV ?>');</script>
                    <strong><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_ERR_MIN_POV ?></strong><br />
                    <strong><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_ERR_MIN_POV2 . " ".$CURRENCY_DISPLAY->getFullValue($_SESSION['minimum_pov']) ?></strong>
                </div><?php
            }
            
            
            elseif ($checkout_this_step == CHECK_OUT_GET_FINAL_CONFIRMATION) { 
                ps_checkout::final_info();
                ?>
			<br /> 
			<div align="center">
	                <?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_NOTE ?>:<br />
	                <textarea title="<?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CUSTOMER_NOTE ?>" cols="50" rows="5" name="customer_note"></textarea>
	                <br />
	                <?php
	                if (PSHOP_AGREE_TO_TOS_ONORDER == '1') { ?>
	                    <br />
	                  	<input type="checkbox" name="agreed" value="1" class="inputbox" />&nbsp;&nbsp;
	                  	<?php 
	                  	$link = $mosConfig_live_site .'/index2.php?option=com_virtuemart&amp;page=shop.tos&amp;pop=1&amp;Itemid='. $_REQUEST['Itemid'];
						$text = $VM_LANG->_PHPSHOP_I_AGREE_TO_TOS;
						echo vmPopupLink( $link, $text );
	                    echo '<br />';
	                }
	                ?>
                </div>
                <?php
                if( @VM_ONCHECKOUT_SHOW_LEGALINFO == '1' ) {
                	$link =  sefRelToAbs('index2.php?option=com_content&amp;task=view&amp;id='.VM_ONCHECKOUT_LEGALINFO_LINK );
                	$jslink = "window.open('$link', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;";
               		if( @VM_ONCHECKOUT_LEGALINFO_SHORTTEXT=='' || !defined('VM_ONCHECKOUT_LEGALINFO_SHORTTEXT')) {
                		$text = $VM_LANG->_VM_LEGALINFO_SHORTTEXT;
                	} else {
                		$text = VM_ONCHECKOUT_LEGALINFO_SHORTTEXT;
                	}
                	?>
	                <div class="legalinfo"><?php
	                	echo sprintf( $text, $link, $jslink );
	                	?>
	                </div><br />
	                <?php
	           	}
	                ?>
                <div align="center">
                <input type="submit" onclick="return( submit_order( this.form ) );" class="button" name="submit" value="<?php echo $VM_LANG->_PHPSHOP_ORDER_CONFIRM_MNU ?>" />
                </div>
            <?php 
            } 
            elseif ($checkout_this_step != CHECK_OUT_GET_FINAL_CONFIRMATION) { ?>
                <div align="center">
                <input type="submit" class="button" name="submit" value="<?php echo $VM_LANG->_PHPSHOP_CHECKOUT_NEXT;?> &gt;&gt;" />
                </div>
            <?php 
            } ?>
            </td>
        </tr>
    </table>
</form>
<!-- Body ends here -->
<?php
            if ($checkout_this_step == CHECK_OUT_GET_FINAL_CONFIRMATION && PSHOP_AGREE_TO_TOS_ONORDER == '1') {
                echo "<script type=\"text/javascript\"><!--
                    function submit_order( form ) {
                        if (!form.agreed.checked) {
                            alert( \"". $VM_LANG->_PHPSHOP_AGREE_TO_TOS ."\" );
                            return false;
                        }
                        else {
                            return true;
                        }
                    }
                    --></script>";
            }
            else {
                echo "<script type=\"text/javascript\"><!--
                    function submit_order( form ) { return true; }
                    --></script>";
            }
        }
        
        else {

          if (!empty($my->id)) {
            // USER IS LOGGED IN, BUT NO REGISTERED CUSTOMER
            // WE NEED SOME ADDITIONAL INFORMATION HERE,
            // SO REDIRECT HIM TO shop/shopper_add
      ?>
            <table width="100%">
            <tr class="sectiontableheader"><th><?php echo $VM_LANG->_PHPSHOP_NO_CUSTOMER ?></th>
            </tr>
            </table>
      <?php 
            include(PAGEPATH. 'checkout_register_form.php');
          }
      
          else { // user is not logged in
  ?>
            <fieldset>
                <legend><span class="sectiontableheader"><?php echo $VM_LANG->_PHPSHOP_RETURN_LOGIN ?></span></legend>
                <br />
            <?php 
                        include(PAGEPATH.'checkout.login_form.php');
            ?>
                <br />
            </fieldset><br />
            <?php
          
          
          ?><br />
            <div class="sectiontableheader"><?php echo $VM_LANG->_PHPSHOP_NEW_CUSTOMER ?></div>
                <br /><?php
          
                include(PAGEPATH. 'checkout_register_form.php');
?>
                <br />
<?php
          }
    }
}
else {
	mosRedirect( $sess->url( $_SERVER['PHP_SELF'].'' ), $VM_LANG->_PHPSHOP_EMPTY_CART);
}

?>
