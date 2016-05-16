<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_skipjack.php,v 1.5 2005/11/16 14:43:32 codename-matrix Exp $
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
*
* The ps_skipjack class, containing the payment processing code
*  for transactions with Skipjack.com
 * @copyright (C) 2005 Matthew Schick
*/
class ps_skipjack {

    var $payment_code = "SKJ";
    var $classname = "ps_skipjack";
    var $error_codes = array("-35" => "Invalid credit card number",
    			"-37" => "Failed communication",
			"-39" => "Length serial number",
			"-51" => "Length zip code",
			"-52" => "Length shipto zip code",
			"-53" => "Length expiration date",
			"-54" => "Length account number date",
			"-55" => "Length street address",
			"-56" => "Length shipto street address",
			"-57" => "Length transaction amount",
			"-58" => "Length name",
			"-59" => "Length location",
			"-60" => "Length state",
			"-61" => "Length shipto state",
			"-62" => "Length order string",
			"-64" => "Invalid phone number",
			"-65" => "Empty name",
			"-66" => "Empty email",
			"-67" => "Empty street address",
			"-68" => "Empty city",
			"-69" => "Empty state",
			"-79" => "Length customer name",
			"-80" => "Length shipto customer name",
			"-81" => "Length customer location",
			"-82" => "Length customer state",
			"-83" => "Length shipto phone",
			"-84" => "Duplicate ordernumber",
			"-91" => "CVV2",
			"-92" => "Error Approval Code",
			"-93" => "Blind Credits Not Allowed",
			"-94" => "Blind Credits Failed",
			"-95" => "Voice Authorizations Not Allowed" );
  
    /**
    * Show all configuration parameters for this payment method
    * @returns boolean False when the Payment method has no configration
    */
    function show_configuration() { 
    
      global $VM_LANG, $sess;
      $payment_method_id = mosGetParam( $_REQUEST, 'payment_method_id', null );
      $db =& new ps_DB;
      /** Read current Configuration ***/
      require_once(CLASSPATH ."payment/".$this->classname.".cfg.php");
    ?>
      <table>
        <tr>
            <td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ENABLE_SKJ_TESTMODE ?></strong></td>
            <td>
                <select name="SKJ_TEST_REQUEST" class="inputbox" >
                <option <?php if (SKJ_TEST_REQUEST == 'TRUE') echo "selected=\"selected\""; ?> value="TRUE"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_YES ?></option>
                <option <?php if (SKJ_TEST_REQUEST == 'FALSE') echo "selected=\"selected\""; ?> value="FALSE"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_NO ?></option>
                </select>
            </td>
            <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ENABLE_SKJ_TESTMODE_EXPLAIN ?>
            </td>
        </tr>
        <tr>
            <td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_SKJ_SERIAL ?></strong></td>
            <td>
		    <input type="text" name="SKJ_SERIAL" class="inputbox" value="<?php echo SKJ_SERIAL ?>" />
            </td>
	    <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_SKJ_SERIAL_EXPLAIN ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $VM_LANG->_PHPSHOP_PAYMENT_CVV2 ?></strong></td>
            <td>
                <select name="SKJ_CHECK_CARD_CODE" class="inputbox">
                <option <?php if (SKJ_CHECK_CARD_CODE == 'YES') echo "selected=\"selected\""; ?> value="YES">
                <?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_YES ?></option>
                <option <?php if (SKJ_CHECK_CARD_CODE == 'NO') echo "selected=\"selected\""; ?> value="NO">
                <?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_NO ?></option>
                </select>
            </td>
            <td><?php echo $VM_LANG->_PHPSHOP_PAYMENT_CVV2_TOOLTIP ?></td>
        </tr>
        <tr><td colspan="3"><hr/></td></tr>
        <tr>
            <td><strong>Order Status for successful transactions</strong></td>
            <td>
                <select name="SKJ_VERIFIED_STATUS" class="inputbox" >
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
                      if (SKJ_VERIFIED_STATUS == $order_status_code[$i]) 
                         echo "\" selected=\"selected\">";
                      else
                         echo "\">";
                      echo $order_status_name[$i] . "</option>\n";
                    }?>
                    </select>
            </td>
            <td>Select the order status to which the actual order is set, if the Skipjack Transaction was successful. 
            If using download selling options: select the status which enables the download (then the customer is instantly notified about the download via e-mail).
            </td>
        </tr>
            <tr>
            <td><strong>Order Status for failed transactions</strong></td>
            <td>
                <select name="SKJ_INVALID_STATUS" class="inputbox" >
                <?php
                    for ($i = 0; $i < sizeof($order_status_code); $i++) {
                      echo "<option value=\"" . $order_status_code[$i];
                      if (SKJ_INVALID_STATUS == $order_status_code[$i]) 
                         echo "\" selected=\"selected\">";
                      else
                         echo "\">";
                      echo $order_status_name[$i] . "</option>\n";
                    } ?>
                    </select>
            </td>
            <td>Select an order status for failed Skipjack transactions.</td>
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
      
      $my_config_array = array("SKJ_TEST_REQUEST" => $d['SKJ_TEST_REQUEST'],
                              "SKJ_SERIAL" => $d['SKJ_SERIAL'],
                              "SKJ_INVALID_STATUS" => $d['SKJ_INVALID_STATUS'],
                              "SKJ_VERIFIED_STATUS" => $d['SKJ_VERIFIED_STATUS'],
                              "SKJ_CHECK_CARD_CODE" => $d['SKJ_CHECK_CARD_CODE']
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
  ** created by: Matthew Schick
  ** description: process transaction Skipjack.com
  ** parameters: $order_number, the number of the order, we're processing here
  **            $order_total, the total $ of the order
  ** returns: 
  ***************************************************************************/
   function process_payment($order_number, $order_total, &$d) {
        
        global $vendor_mail, $vendor_currency, $VM_LANG, $vmLogger;
      
        $ps_vendor_id = $_SESSION["ps_vendor_id"];
        $auth = $_SESSION['auth'];
        $ps_checkout = new ps_checkout;
      
        /*** Get the Configuration File for Skipjack.com ***/
        require_once(CLASSPATH ."payment/".$this->classname.".cfg.php");
        
        // Get user billing information
        $dbbt = new ps_DB;
        $qt = "SELECT * FROM #__{vm}_user_info WHERE user_id='".$auth["user_id"]."' AND address_type='BT'";
        $dbbt->query($qt);
        $dbbt->next_record();
        $user_info_id = $dbbt->f("user_info_id");
        if( $user_info_id != $d["ship_to_info_id"]) {
            // Get user billing information
            $dbst =& new ps_DB;
            $qt = "SELECT * FROM #__{vm}_user_info WHERE user_info_id='".$d["ship_to_info_id"]."' AND address_type='ST'";
            $dbst->query($qt);
            $dbst->next_record();
        }
        else {
            $dbst = $dbbt;
        }

	if(SKJ_TEST_REQUEST == 'TRUE') {
		$host = "developer.skipjackic.com";
	}
	else {
		$host = "www.skipjackic.com";
	}
        $port = 443;
        $path = "/scripts/evolvcc.dll?AuthorizeAPI";  
	// echo "Host path : ".$host.$path."\n";

        //Skipjack vars to send
	$bill_full_name = $dbbt->f("first_name") . " " . $dbbt->f("last_name");
	$ship_full_name = $dbst->f("first_name") . " " . $dbst->f("last_name");
	if (!$dbbt->f("phone_1")) {
		$phone = '123-456-7890';
	}
	else {
		$phone = $dbbt->f("phone_1");
	}
        $formdata = array (
            
            // SkipJack required fields
            'sjname' => substr($bill_full_name, 0, 50),
            'Email' => $dbbt->f("email"),
            'Streetaddress' => substr($dbbt->f("address_1"), 0, 40),
            'City' => substr($dbbt->f("city"), 0, 40),
            'State' => substr($dbbt->f("state"), 0, 40),
            'Zipcode' => substr($dbbt->f("zip"), 0, 9),
            'Ordernumber' => $order_number,
            'Accountnumber' => $_SESSION['ccdata']['order_payment_number'],
            'Month' => ($_SESSION['ccdata']['order_payment_expire_month']),
	        'Year' => ($_SESSION['ccdata']['order_payment_expire_year']),
            'Serialnumber' => SKJ_SERIAL,
            'Transactionamount' => $order_total,
	        //FIXME - Needs order details to be compliant
	        'Orderstring' => "1~1~0.00~1~N~||",
            'Shiptophone' => $phone,
            
            // Customer Shipping Address
            'Shiptoname' => substr($ship_full_name, 0, 50),
            'Shiptostreetaddress' => substr($dbst->f("address_1"), 0, 40),
            'Shiptocity' => substr($dbst->f("city"), 0, 40),
            'Shiptostate' => substr($dbst->f("state"), 0, 40),
            'Shiptozipcode' => substr($dbst->f("zip"), 0, 20),
            'Shiptocountry' => substr($dbst->f("country"), 0, 60),            
            
            // Additional Customer Data
            'Country' => substr($dbbt->f("country"), 0, 40),
            
            'cvv2' => $_SESSION['ccdata']['credit_card_code']
            
        );
        
        //build the post string
        $poststring = '';
        foreach($formdata AS $key => $val){
            $poststring .= urlencode($key) . "=" . urlencode($val) . "&";
        }
	// echo "Poststring: ".$poststring."\n";
        // strip off trailing ampersand
        $poststring = substr($poststring, 0, -1);
        
        if( function_exists( "curl_init" )) {
        
            $CR = curl_init();
            curl_setopt($CR, CURLOPT_URL, "https://".$host.$path);
            curl_setopt($CR, CURLOPT_POST, 1);
            curl_setopt($CR, CURLOPT_FAILONERROR, true); 
            curl_setopt($CR, CURLOPT_POSTFIELDS, $poststring);
            curl_setopt($CR, CURLOPT_RETURNTRANSFER, 1);
             
            // No PEER certificate validation...as we don't have 
            // a certificate file for it to authenticate the host www.ups.com against!
            curl_setopt($CR, CURLOPT_SSL_VERIFYPEER, 0);
            //curl_setopt($CR, CURLOPT_SSLCERT , "/usr/locale/xxxx/clientcertificate.pem");
            
            $result = curl_exec( $CR );
            
            $error = curl_error( $CR );
            if( !empty( $error )) {
              $vmLogger->err( "curl error: ".curl_error( $CR ) );
              $html = "<br/><span class=\"message\">".$VM_LANG->_PHPSHOP_PAYMENT_INTERNAL_ERROR." Skipjack.com</span>";
              return false;
            }
            else {
                // echo "result: " . $result;
            }
            curl_close( $CR );
        }
        else {
        
            $fp = fsockopen("ssl://".$host, $port, $errno, $errstr, $timeout = 60);
            if(!$fp){
                //error tell us
                $vmLogger->err( "$errstr ($errno)" );
            }
            else {
    
                //send the server request
                fputs($fp, "POST $path HTTP/1.1\r\n");
                fputs($fp, "Host: $host\r\n");
                fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
                fputs($fp, "Content-length: ".strlen($poststring)."\r\n");
                fputs($fp, "Connection: close\r\n\r\n");
                fputs($fp, $poststring . "\r\n\r\n");
                
                //Get the response header from the server
                $str = '';
                while(!feof($fp) && !stristr($str, 'content-length')) {
                   $str = fgets($fp, 4096);
                }
                // If didnt get content-lenght, something is wrong, return false.
                if (!stristr($str, 'content-length')) {
                   return false;
                
                }
                $data = "";
                while (!feof($fp)) {
                   $data .= fgets ($fp, 1024);
                }
                $result = trim( $data );
                 /*
                 // Get length of data to be received.
                 $length = trim(substr($str,strpos($str,'content-length') + 15));
                 // Get buffer (blank data before real data)
                 fgets($fp, 4096);
                 // Get real data
                 $data = fgets($fp, $length);
                 fclose($fp);*/
                 
          }
        }
        $result_lines = explode("\n", $result);
        // echo "Line 2: "	. $result_lines[1] . "\n";
        $response = explode("\",\"", $result_lines[1]);

        // Approved - Success!
        if ($response[8] == '1') {
           $d["order_payment_log"] = $VM_LANG->_PHPSHOP_PAYMENT_TRANSACTION_SUCCESS.": ";
           // $d["order_payment_log"] .= $response[3];
           // Catch Transaction ID
           $d["order_payment_trans_id"] = $response[8];

           return True;
        } 
        // Payment Declined
        elseif ($response[8] == '0') {
           $vmLogger->err( $response[3] );
           $d["order_payment_log"] = $response[3];
           // Catch Transaction ID
           $d["order_payment_trans_id"] = $response[8];
           return False;
        }
        // Transaction Error
        elseif ($response[0] == '0') {
           $vmLogger->err( $response[3] );
           $d["order_payment_log"] = $response[3];
           // Catch Transaction ID
           $d["order_payment_trans_id"] = $response[8];
           return False;
        }
   }
   
}
