<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_pbs.php,v 1.4 2005/11/16 14:43:32 codename-matrix Exp $
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

/**
* The ps_pbs class, containing the payment processing code
*  for transactions with PBS supported Payment Gateways 
* @author soeren
 */
class ps_pbs {

    var $payment_code = "PBS";
    var $classname = "ps_pbs";
  
    /**
    * Show all configuration parameters for this payment method
    * @returns boolean False when the Payment method has no configration
    */
    function show_configuration() {
    
      global $VM_LANG, $mosConfig_live_site;
      $db =& new ps_DB;
      /** Read current Configuration ***/
      require_once(CLASSPATH ."payment/".$this->classname.".cfg.php");
    ?>
      <table>
        <tr>
            <td><strong>PBS Merchant ID</strong></td>
            <td>
                <input type="text" name="PBS_MERCHANT_ID" class="inputbox" value="<?php echo PBS_MERCHANT_ID ?>" />
            </td>
            <td>The Merchant ID you have reveived from PBS</td>
        </tr>
        <tr>
            <td><strong>Payment Gateway</strong></td>
            <td>
                <select name="PBS_GATEWAY" onchange="updateExtraInfo();" class="inputbox">
                    <option <?php if (PBS_GATEWAY == 'freeway.dk') echo "selected=\"selected\""; ?> value="freeway.dk">freeway.dk</option>
                    <option <?php if (PBS_GATEWAY == 'danhost.dk') echo "selected=\"selected\""; ?> value="danhost.dk">danhost.dk</option>
                    <!--<option <?php if (PBS_GATEWAY == 'webhosting.dk') echo "selected=\"selected\""; ?> value="webhosting.dk">webhosting.dk</option>-->
                    <!--<option <?php if (PBS_GATEWAY == 'interpay.dk') echo "selected=\"selected\""; ?> value="interpay.dk">interpay.dk</option>-->
                    <option <?php if (PBS_GATEWAY == 'wannafind.dk') echo "selected=\"selected\""; ?> value="wannafind.dk">wannafind.dk</option>
                    <option <?php if (PBS_GATEWAY == 'dandomain.dk') echo "selected=\"selected\""; ?> value="dandomain.dk">dandomain.dk</option>
                </select>
            </td>
            <td>The Payment Gateway you are using for Payment Transactions.</td>
        </tr>
        <tr>
            <td><strong>Shop ID</strong></td>
            <td>
                <input type="text" name="PBS_SHOP_ID" class="inputbox" value="<?php echo PBS_SHOP_ID ?>" />
            </td>
            <td>The Shop ID (Only if you are using Webhosting.dk, Danhost.dk or Wannafind.dk Payment Gateway)</td>
        </tr>
        <tr>
            <td><strong>Order Status for successful transactions</strong></td>
            <td>
                <select name="PBS_VERIFIED_STATUS" class="inputbox" >
                <?php
                    $q = "SELECT order_status_name,order_status_code FROM #__{vm}_order_status ORDER BY list_order";
                    $db->query($q);
                    $order_status_code = Array();
                    $order_status_name = Array();
                    
                    while ($db->next_record()) {
                      $order_status_code[] = $db->f("order_status_code");
                      $order_status_name[] =  $db->f("order_status_name");
                    }
                    for ($i = 0; $i < sizeof($order_status_code); $i++) {
                      echo "<option value=\"" . $order_status_code[$i];
                      if (PBS_VERIFIED_STATUS == $order_status_code[$i]) 
                         echo "\" selected=\"selected\">";
                      else
                         echo "\">";
                      echo $order_status_name[$i] . "</option>\n";
                    }?>
                    </select>
            </td>
            <td>Select the order status to which the actual order is set, if the Transaction was successful. 
            If using download selling options: select the status which enables the download (then the customer is instantly notified about the download via e-mail).
            </td>
        </tr>
            <tr>
            <td><strong>Order Status for failed transactions</strong></td>
            <td>
                <select name="PBS_INVALID_STATUS" class="inputbox" >
                <?php
                    for ($i = 0; $i < sizeof($order_status_code); $i++) {
                      echo "<option value=\"" . $order_status_code[$i];
                      if (PBS_INVALID_STATUS == $order_status_code[$i]) 
                         echo "\" selected=\"selected\">";
                      else
                         echo "\">";
                      echo $order_status_name[$i] . "</option>\n";
                    } ?>
                    </select>
            </td>
            <td>Select an order status for failed transactions.</td>
        </tr>
        <tr>
            <td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ENABLE_AUTORIZENET_TESTMODE ?></strong></td>
            <td>
                <select name="PBS_TEST_MODE" class="inputbox" >
                <option <?php if (PBS_TEST_MODE == '1') echo "selected=\"selected\""; ?> value="1"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_YES ?></option>
                <option <?php if (PBS_TEST_MODE == '0') echo "selected=\"selected\""; ?> value="0"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_NO ?></option>
                </select>
            </td>
            <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ENABLE_AUTORIZENET_TESTMODE_EXPLAIN ?>
            </td>
        </tr>
      </table>
      <script type="text/javascript">
      function updateExtraInfo() {
        var form = document.adminForm;
        switch( form.PBS_GATEWAY.selectedIndex ) {
            // FreeWay.dk
            case 0:
                form.payment_extrainfo.value = '<'+'?php\n'
                        +'// This is the Session ID\n'
                        +'// It contains the Order ID, the VirtueMart Session ID, Mambo\'s SessionCookie and an md5 HASH CheckCode\n'
                        +'$sessionid = sprintf("%08d", $order_id). $_COOKIE[\'virtuemart\'].md5($_COOKIE[\'sessioncookie\'].$_SERVER[\'REMOTE_ADDR\']);\n'
                        +'$sessionid .= md5( $sessionid . $mosConfig_secret . ENCODE_KEY);\n'
                        +'$sessionid = base64_encode( $sessionid );\n'
                        +'?>\n'
                        +'<form action="https://pay.freeway.dk/payform/relay.asp/<?php echo PBS_MERCHANT_ID ?>?sessionid=<'+'?php echo $sessionid ?>" method="post" name="paymentform">\n'
                        +'<input type="image" src="components/com_virtuemart/shop_image/ps_image/payment.gif" name="submit" alt="Pay your Order now - Click here!" align="center" border="0" />\n'
                        +'</form>'
                        +'<a href="#" onclick="document.paymentform.submit();">Pay your Order now - Click here!</a>\n';
                break;
            // DanHost.dk
            case 1:
                form.payment_extrainfo.value = '<'+'?php\n'
                        +'// This is the Session ID\n'
                        +'// It contains the Order ID, the VirtueMart Session ID, Mambo\'s SessionCookie and an md5 HASH CheckCode\n'
                        +'$sessionid = sprintf("%08d", $order_id). $_COOKIE[\'virtuemart\'].md5($_COOKIE[\'sessioncookie\'].$_SERVER[\'REMOTE_ADDR\']);\n'
                        +'$sessionid .= md5( $sessionid . $mosConfig_secret . ENCODE_KEY);\n'
                        +'$sessionid = base64_encode( $sessionid );\n'
                        +'?>\n'
                        +'<form action="https://gateway.fuzion.dk/" method="post">\n'
                        +'<input type="image" src="components/com_virtuemart/shop_image/ps_image/payment.gif" name="submit" alt="Pay your Order now - Click here!" align="center" border="0" />\n'
                        +'<input type="hidden" name="shopid" value="<'+'?php echo PBS_SHOP_ID ?>" />\n'
                        +'<input type="hidden" name="sessionid" value="<'+'?php echo $sessionid ?>" />\n'
                        +'<input type="hidden" name="shop_orderid" value="<'+'?php echo $order_id ?>" />\n'
                        +'</form>'
                        +'<a href="#" onclick="document.paymentform.submit();">Pay your Order now - Click here!</a>\n';
                break;
            // Webhosting.dk
            case 2:
                form.payment_extrainfo.value = '<'+'?php\n'
                        +'// This is the Session ID\n'
                        +'// It contains the Order ID, the VirtueMart Session ID, Mambo\'s SessionCookie and an md5 HASH CheckCode\n'
                        +'$sessionid = sprintf("%08d", $order_id). $_COOKIE[\'virtuemart\'].md5($_COOKIE[\'sessioncookie\'].$_SERVER[\'REMOTE_ADDR\']);\n'
                        +'$sessionid .= md5( $sessionid . $mosConfig_secret . ENCODE_KEY);\n'
                        +'$sessionid = base64_encode( $sessionid );\n'
                        +'?>\n'
                        +'<form action="https://secure.webhosting.dk/pbsgateway/index.php" method="post" name="paymentform">\n'
                        +'<input type="image" src="components/com_virtuemart/shop_image/ps_image/payment.gif" name="submit" alt="Pay your Order now - Click here!" align="center" border="0" />\n'
                        +'<input type="hidden" name="shopid" value="<'+'?php echo PBS_SHOP_ID ?>" />\n'
                        +'<input type="hidden" name="sessionid" value="<'+'?php echo $sessionid ?>" />\n'
                        +'<input type="hidden" name="orderid" value="<'+'?php echo $order_id ?>" />\n'
                        +'<input type="hidden" name="currencycode" value="208" />\n'
                        +'<input type="hidden" name="amount" value="<'+'?php echo $db->f("order_total") ?>" />\n'
                        +'</form>'
                        +'<a href="#" onclick="document.paymentform.submit();">Pay your Order now - Click here!</a>\n';
                break;
            // Interpay.dk
            case 3:
                form.payment_extrainfo.value = '<'+'?php\n'
                        +'// This is the Session ID\n'
                        +'// It contains the Order ID, the VirtueMart Session ID, Mambo\'s SessionCookie and an md5 HASH CheckCode\n'
                        +'$sessionid = sprintf("%08d", $order_id). $_COOKIE[\'virtuemart\'].md5($_COOKIE[\'sessioncookie\'].$_SERVER[\'REMOTE_ADDR\']);\n'
                        +'$sessionid .= md5( $sessionid . $mosConfig_secret . ENCODE_KEY);\n'
                        +'$sessionid = base64_encode( $sessionid );\n'
                        +'?>\n'
                        +'<form action="https://pbs.interpay.dk/?sessionid=<'+'?php echo $sessionid ?>&amount=<'+'?php $db->p("order_total") ?>" method="post" name="paymentform">\n'
                        +'<input type="image" src="components/com_virtuemart/shop_image/ps_image/payment.gif" name="submit" alt="Pay your Order now - Click here!" align="center" border="0" />\n'
                        +'</form>'
                        +'<a href="#" onclick="document.paymentform.submit();">Pay your Order now - Click here!</a>\n';
                break;
            // WannaFind.dk
            case 4:
                form.payment_extrainfo.value = '<'+'?php\n'
                        +'// This is the Session ID\n'
                        +'// It contains the Order ID, the VirtueMart Session ID, Mambo\'s SessionCookie and an md5 HASH CheckCode\n'
                        +'$sessionid = sprintf("%08d", $order_id). $_COOKIE[\'virtuemart\'].md5($_COOKIE[\'sessioncookie\'].$_SERVER[\'REMOTE_ADDR\']);\n'
                        +'$sessionid .= md5( $sessionid . $mosConfig_secret . ENCODE_KEY);\n'
                        +'$sessionid = base64_encode( $sessionid );\n'
                        +'?>\n'
                        +'<form action="https://betaling.wannafind.dk/proxy/p.php/<?php echo $mosConfig_live_site ?>/index.php?option=com_virtuemart&page=checkout.wannafind_cc_form&sessionid=<'+'?php echo $sessionid ?>" method="post" name="paymentform">\n'
                        +'<input type="image" src="components/com_virtuemart/shop_image/ps_image/payment.gif" name="submit" alt="Pay your Order now - Click here!" align="center" border="0" />\n'
                        +'<input type="hidden" name="shopid" value="<'+'?php echo PBS_SHOP_ID ?>" />\n'
                        +'<input type="hidden" name="orderid" value="<'+'?php echo $order_id ?>" />\n'
                        +'</form>'
                        +'<a href="#" onclick="document.paymentform.submit();">Pay your Order now - Click here!</a>\n';
                break;
            // DanDomain.dk
            case 5:
                form.payment_extrainfo.value = '<'+'?php\n'
                        +'// This is the Session ID\n'
                        +'// It contains the Order ID, the VirtueMart Session ID, Mambo\'s SessionCookie and an md5 HASH CheckCode\n'
                        +'$sessionid = sprintf("%08d", $order_id). $_COOKIE[\'virtuemart\'].md5($_COOKIE[\'sessioncookie\'].$_SERVER[\'REMOTE_ADDR\']);\n'
                        +'$sessionid .= md5( $sessionid . $mosConfig_secret . ENCODE_KEY);\n'
                        +'$sessionid = base64_encode( $sessionid );\n'
                        +'?>\n'
                        +'<form action="https://pay.dandomain.dk/securetunnel.asp" method="post" name="paymentform">\n'
                        +'<input type="image" src="components/com_virtuemart/shop_image/ps_image/payment.gif" name="submit" alt="Pay your Order now - Click here!" align="center" border="0" />\n'
                        +'<input type="hidden" name="MerchantNumber" value="<'+'?php echo PBS_MERCHANT_ID ?>" />\n'
                        +'<input type="hidden" name="TunnelURL" value="<'+'?php echo $mosConfig_live_site ?>/index.php?option=com_virtuemart&page=checkout.dandomain_cc_form&sessionid=<'+'?php echo $sessionid ?>" />\n'
                        +'<input type="hidden" name="shopid" value="<'+'?php echo PBS_SHOP_ID ?>" />\n'
                        +'<input type="hidden" name="OrderID" value="<'+'?php echo $order_id ?>" />\n'
                        +'<input type="hidden" name="Amount" value="<'+'?php echo str_replace(".", ",", $db->f("order_total")) ?>" />\n'
                        +'<input type="hidden" name="CurrencyID" value="208" />\n'
                        +'</form>'
                        +'<a href="#" onclick="document.paymentform.submit();">Pay your Order now - Click here!</a>\n';
                break;
        }
      }
      </script>
   <?php
      // return false if there\'s no configuration
      return true;
   }
   
    function has_configuration() {
      // return false if there's no configuration
      return true;
   }
   
  /**
	* Returns the "is_writeable" status of the configuration file
	* @param void
	* @returns boolean True when the configuration file is writeable, false when not
	*/
   function configfile_writeable() {
      return is_writeable( CLASSPATH."payment/".$this->classname.".cfg.php" );
   }
   
  /**
	* Returns the "is_readable" status of the configuration file
	* @param void
	* @returns boolean True when the configuration file is writeable, false when not
	*/
   function configfile_readable() {
      return is_readable( CLASSPATH."payment/".$this->classname.".cfg.php" );
   }   
  /**
	* Writes the configuration file for this payment method
	* @param array An array of objects
	* @returns boolean True when writing was successful
	*/
   function write_configuration( &$d ) {
      global $vmLogger;
      
      $my_config_array = array("PBS_MERCHANT_ID" => $d['PBS_MERCHANT_ID'],
                                "PBS_GATEWAY" => $d['PBS_GATEWAY'],
                                "PBS_SHOP_ID" => $d['PBS_SHOP_ID'],
                                "PBS_VERIFIED_STATUS" => $d['PBS_VERIFIED_STATUS'],
                                "PBS_INVALID_STATUS" => $d['PBS_INVALID_STATUS'],
                                "PBS_TEST_MODE" => $d['PBS_TEST_MODE']
                        );
      $config = "<?php\n";
      $config .= "defined('_VALID_MOS') or die('Direct Access to this location is not allowed.'); \n\n";
      foreach( $my_config_array as $key => $value ) {
        $config .= "define ('$key', '$value');\n";
      }
      
      $config .= "?>";
  
      if ($fp = fopen(CLASSPATH ."payment/".$this->classname.".cfg.php", "w")) {
          fputs($fp, $config, strlen($config));
          fclose ($fp);
          return true;
     }
     else {
        $vmLogger->err( "Could not write to configuration file ".CLASSPATH ."payment/".$this->classname.".cfg.php" );
        return false;
     }
   }
   
  /**************************************************************************
  ** name: process_payment()
  ** created by: ryan
  ** description: process transaction for PayMeNow
  ** parameters: $order_number, the number of the order, we're processing here
  **            $order_total, the total $ of the order
  ** returns: 
  ***************************************************************************/
   function process_payment($order_number, $order_total, &$d) {
    return true;
    }
}
