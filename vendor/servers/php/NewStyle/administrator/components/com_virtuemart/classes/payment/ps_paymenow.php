<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_paymenow.php,v 1.4 2005/11/16 14:43:32 codename-matrix Exp $
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

class ps_paymenow {

    var $payment_code = "PN";
    var $classname = "ps_paymenow";
  
    /**
    * Show all configuration parameters for this payment method
    * @returns boolean False when the Payment method has no configration
    */
    function show_configuration() { 
    
      global $VM_LANG;
      $db =& new ps_DB;
      /** Read current Configuration ***/
      require_once(CLASSPATH ."payment/".$this->classname.".cfg.php");
    ?>
      <table>
        <tr>
            <td><strong>PayMeNow ID</strong></td>
            <td>
                <input type="text" name="PN_LOGIN" class="inputbox" value="<? echo PN_LOGIN ?>" />
            </td>
            <td>The PayMeNow Login/Account ID</td>
        </tr>
        <tr>
            <td><strong><?php echo $VM_LANG->_PHPSHOP_PAYMENT_CVV2 ?></strong></td>
            <td>
                <select name="PN_CHECK_CARD_CODE" class="inputbox">
                <option <?php if (PN_CHECK_CARD_CODE == 'YES') echo "selected=\"selected\""; ?> value="YES">
                <?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_YES ?></option>
                <option <?php if (PN_CHECK_CARD_CODE == 'NO') echo "selected=\"selected\""; ?> value="NO">
                <?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_NO ?></option>
                </select>
            </td>
            <td><?php echo $VM_LANG->_PHPSHOP_PAYMENT_CVV2_TOOLTIP ?></td>
        </tr>
        <tr>
            <td><strong>Order Status for successful transactions</strong></td>
            <td>
                <select name="PN_VERIFIED_STATUS" class="inputbox" >
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
                      if (PN_VERIFIED_STATUS == $order_status_code[$i]) 
                         echo "\" selected=\"selected\">";
                      else
                         echo "\">";
                      echo $order_status_name[$i] . "</option>\n";
                    }?>
                    </select>
            </td>
            <td>Select the order status to which the actual order is set, if the PayMeNow Transaction was successful. 
            If using download selling options: select the status which enables the download (then the customer is instantly notified about the download via e-mail).
            </td>
        </tr>
            <tr>
            <td><strong>Order Status for failed transactions</strong></td>
            <td>
                <select name="PN_INVALID_STATUS" class="inputbox" >
                <?php
                    for ($i = 0; $i < sizeof($order_status_code); $i++) {
                      echo "<option value=\"" . $order_status_code[$i];
                      if (PN_INVALID_STATUS == $order_status_code[$i]) 
                         echo "\" selected=\"selected\">";
                      else
                         echo "\">";
                      echo $order_status_name[$i] . "</option>\n";
                    } ?>
                    </select>
            </td>
            <td>Select an order status for failed PayMeNow transactions.</td>
        </tr>
      </table>
   <?php
      // return false if there's no configuration
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
      
      $my_config_array = array("PN_LOGIN" => $d['PN_LOGIN'],
                                "PN_CHECK_CARD_CODE" => $d['PN_CHECK_CARD_CODE'],
                                "PN_VERIFIED_STATUS" => $d['PN_VERIFIED_STATUS'],
                                "PN_INVALID_STATUS" => $d['PN_INVALID_STATUS']
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
     else
        return false;
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
        global $vmLogger;
        
        $vars = array(
             "action" => "ns_quicksale_cc",
             "ecxid"  => PN_LOGIN,
             "amount" => "$order_total",
             "ccname" => $_SESSION['ccdata']['order_payment_name'],
             "ccnum"  => $_SESSION['ccdata']['order_payment_number'],
             "expmon" => $_SESSION['ccdata']['order_payment_expire_month'],
             "expyear"=> $_SESSION['ccdata']['order_payment_expire_year']
        );
        $results = http_post("trans.atsbank.com", 443, "/cgi-bin/trans.cgi",$vars);
        
        if (stristr($results, "Accepted")) {
            #Clean up the cart, send out the emails, and display thankyyou page.
            return true;
        }
        else {
            if ($reason = stristr($results, "Declined"))
            {
            $vmLogger->err( "The transaction was declined because of: <strong>$reason</strong><br />" );
            }
            else
            {
            $vmLogger->err( "FATAL ERROR! Declined for an unknown reason, possibly a server misconfiguration error.<br/>$results" );
            }
            return false;
        }
        
        #echo $results;

   }
   
}

function http_post($server, $port, $url, $vars) {
    // example:
    //  http_post(
    //	"www.fat.com",
    //	80, 
    //	"/weightloss.pl", 
    //	array("name" => "obese bob", "age" => "20")
    //	);
	$user_agent = "Mozilla/4.0 (compatible; MSIE 5.5; Windows 98)";


	$urlencoded = "";
	while (list($key,$value) = each($vars))
		$urlencoded.= urlencode($key) . "=" . urlencode($value) . "&";
	$urlencoded = substr($urlencoded,0,-1);	

	$content_length = strlen($urlencoded);

	$headers = "POST $url HTTP/1.1
        Accept: */*
        Accept-Language: en-au
        Content-Type: application/x-www-form-urlencoded
        User-Agent: $user_agent
        Host: $server
        Connection: Keep-Alive
        Cache-Control: no-cache
        Content-Length: $content_length
        
        ";
    #$fp = fsockopen($host, $port, $errno, $errstr, $timeout = 60);	
	$fp = fsockopen("ssl://".$server, $port, $errno, $errstr);
	if (!$fp) {
	#	return false;
	}
	fputs($fp, $headers);
	fputs($fp, $urlencoded);
	
	$ret = "";
    error_reporting(0);
	while (!feof($fp)) {
		$ret.= fgets($fp, 4096);
    }
    error_reporting(E_ALL ^ E_NOTICE);
	fclose($fp);
    #$ret = stristr($ret, 'html');	
	return $ret;
}
