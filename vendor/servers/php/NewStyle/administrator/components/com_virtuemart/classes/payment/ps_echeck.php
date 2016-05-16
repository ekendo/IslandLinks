<?php
/**
* The ps_echeck class, containing the payment processing code
*  for eCheck.net transactions with authorize.net 
*
* @version $Id: ps_echeck.php,v 1.5 2005/11/16 14:43:32 codename-matrix Exp $
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
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class ps_echeck {

    var $payment_code = "ECK";
    var $classname = "ps_echeck";
  
    /**
    * Show all configuration parameters for this payment method
    * @returns boolean False when the Payment method has no configration
    */
    function show_configuration() { 
    
      global $VM_LANG, $sess;
      $payment_method_id = mosGetParam( $_REQUEST, 'payment_method_id', null );
      /** Read current Configuration ***/
      require_once(CLASSPATH ."payment/".$this->classname.".cfg.php");    ?>
      <table>
        <tr>
            <td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ENABLE_AUTORIZENET_TESTMODE ?></strong></td>
            <td>
                <select name="ECK_TEST_REQUEST" class="inputbox" >
                <option <?php if (ECK_TEST_REQUEST == 'TRUE') echo "selected=\"selected\""; ?> value="TRUE"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_YES ?></option>
                <option <?php if (ECK_TEST_REQUEST == 'FALSE') echo "selected=\"selected\""; ?> value="FALSE"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_NO ?></option>
                </select>
            </td>
            <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ENABLE_AUTORIZENET_TESTMODE_EXPLAIN ?>
            </td>
        </tr>
        <tr>
            <td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_AUTORIZENET_USERNAME ?></strong></td>
            <td>
                <input type="text" name="ECK_LOGIN" class="inputbox" value="<? echo ECK_LOGIN ?>" />
            </td>
            <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_AUTORIZENET_USERNAME_EXPLAIN ?>
            </td>
        </tr>
        <tr>
            <td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_AUTORIZENET_KEY ?></strong></td>
            <td>
                <a id="changekey" href="<?php $sess->purl($_SERVER['PHP_SELF']."?page=store.payment_method_keychange&pshop_mode=admin&payment_method_id=$payment_method_id") ?>" >
                <input onclick="document.location=document.getElementById('changekey').href" type="button" name="" value="<?php echo $VM_LANG->_PHPSHOP_CHANGE_TRANSACTION_KEY ?>" /><a/>
            </td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><strong><?php echo $VM_LANG->_PHPSHOP_PAYMENT_AN_RECURRING ?></strong></td>
            <td>
                <select name="ECK_RECURRING" class="inputbox">
                <option <?php if (ECK_RECURRING == 'YES') echo "selected=\"selected\""; ?> value="YES">
                <?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_YES ?></option>
                <option <?php if (ECK_RECURRING == 'NO') echo "selected=\"selected\""; ?> value="NO">
                <?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_NO ?></option>
                </select>
            </td>
            <td><?php echo $VM_LANG->_PHPSHOP_PAYMENT_AN_RECURRING_TOOLTIP ?>
            </td>
        </tr>
        <tr>
            <td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_AUTORIZENET_AUTENTICATIONTYPE ?></strong></td>
            <td>
              <select name="ECK_TYPE" class="inputbox">
                <option <?php if (ECK_TYPE == 'AUTH_CAPTURE') echo "selected=\"selected\""; ?> value="AUTH_CAPTURE">AUTH_CAPTURE</option>
                <option <?php if (ECK_TYPE == 'CREDIT') echo "selected=\"selected\""; ?> value="CREDIT">CREDIT</option>
              </select>
            </td>
            <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_AUTORIZENET_AUTENTICATIONTYPE_EXPLAIN ?>
            </td>
        </tr>
        <tr>
            <td><strong>eCheck.net Transaction Type</strong></td>
            <td>
              <select name="ECK_ECHECK_TYPE" class="inputbox">
                <option <?php if (ECK_ECHECK_TYPE == 'CCD') echo "selected=\"selected\""; ?> value="CCD">CCD</option>
                <option <?php if (ECK_ECHECK_TYPE == 'PPD') echo "selected=\"selected\""; ?> value="PPD">PPD</option>
                <option <?php if (ECK_ECHECK_TYPE == 'TEL') echo "selected=\"selected\""; ?> value="TEL">TEL</option>
                <option <?php if (ECK_ECHECK_TYPE == 'WEB') echo "selected=\"selected\""; ?> value="WEB">WEB</option>
              </select>
            </td>
            <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_AUTORIZENET_AUTENTICATIONTYPE_EXPLAIN ?>
            </td>
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
      
      $my_config_array = array("ECK_TEST_REQUEST" => $d['ECK_TEST_REQUEST'],
                              "ECK_LOGIN" => $d['ECK_LOGIN'],
                              "ECK_TYPE" => $d['ECK_TYPE'],
                              "ECK_ECHECK_TYPE" => $d['ECK_ECHECK_TYPE'],
                              "ECK_RECURRING" => $d['ECK_RECURRING']
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
  ** created by: jep
  ** description: process transaction authorize.net
  ** parameters: $order_number, the number of the order, we're processing here
  **            $order_total, the total $ of the order
  ** returns: 
  ***************************************************************************/
   function process_payment($order_number, $order_total, &$d) {
        
        global $vendor_mail, $vendor_currency, $VM_LAN, $vmLogger;
        $database = new ps_DB();
      
        $ps_vendor_id = $_SESSION["ps_vendor_id"];
        $auth = $_SESSION['auth'];
        $ps_checkout = new ps_checkout;
      
        /*** Get the Configuration File for authorize.net ***/
        require_once(CLASSPATH ."payment/".$this->classname.".cfg.php");

        // Get the Transaction Key securely from the database
        $database->query( "SELECT DECODE(payment_passkey,'".ENCODE_KEY."') as passkey FROM #__{vm}_payment_method WHERE payment_class='".$this->classname."'" );
        $transaction = $database->record[0];
        if( empty($transaction->passkey)) {
            $vmLogger->err($VM_LANG->_PHPSHOP_PAYMENT_ERROR);
            return false;
        }
        
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

        $host = "secure.authorize.net";
        $port = 443;
        $path = "/gateway/transact.dll";  

        //Authnet vars to send
        $formdata = array (
            'x_version' => '3.1',
            'x_login' => ECK_LOGIN,
            'x_tran_key' => $transaction->passkey,
            'x_test_request' => ECK_TEST_REQUEST,
            
            'x_delim_data' => 'TRUE',
            'x_delim_char' => '|',
            'x_relay_response' => 'FALSE',
            
            'x_first_name' => substr($dbbt->f("first_name"), 0, 50),
            'x_last_name' => substr($dbbt->f("last_name"), 0, 50),
            'x_company' => substr($dbbt->f("company"), 0, 50),
            'x_address' => substr($dbbt->f("address_1"), 0, 60),
            'x_city' => substr($dbbt->f("city"), 0, 40),
            'x_state' => substr($dbbt->f("state"), 0, 40),
            'x_zip' => substr($dbbt->f("zip"), 0, 20),
            'x_country' => substr($dbbt->f("country"), 0, 60),
            'x_phone' => substr($dbbt->f("phone_1"), 0, 25),
            'x_fax' => substr($dbbt->f("fax"), 0, 25),
            
            'x_ship_to_first_name' => substr($dbst->f("first_name"), 0, 50),
            'x_ship_to_last_name' => substr($dbst->f("last_name"), 0, 50),
            'x_ship_to_company' => substr($dbst->f("company"), 0, 50),
            'x_ship_to_address' => substr($dbst->f("address_1"), 0, 60),
            'x_ship_to_city' => substr($dbst->f("city"), 0, 40),
            'x_ship_to_state' => substr($dbst->f("state"), 0, 40),
            'x_ship_to_zip' => substr($dbst->f("zip"), 0, 20),
            'x_ship_to_country' => substr($dbst->f("country"), 0, 60),            
            
            'x_cust_id' => $auth['user_id'],
            'x_customer_ip' => $_SERVER["REMOTE_ADDR"],         
            'x_customer_tax_id' => $dbbt->f("tax_id"),         
            
            'x_email' => $dbbt->f("email"),
            'x_email_customer' => 'True',         
            'x_merchant_email' => $vendor_mail,   
            
            'x_invoice_num' => substr($order_number, 0, 20),
            'x_description' => '',
            
            'x_amount' => $order_total,
            'x_currency_code' => $vendor_currency,
            'x_method' => 'ECHECK',
            'x_type' => ECK_TYPE,
            'x_echeck_type' => ECK_ECHECK_TYPE,

            'x_recurring_billing' => ECK_RECURRING,

            'x_bank_aba_code' => $dbbt->f("bank_iban"),
            'x_bank_acct_num' => $dbbt->f("bank_account_nr"),
            'x_bank_acct_type' => $dbbt->f("bank_account_type"),
            'x_bank_name' => $dbbt->f("bank_name"),
            'x_bank_acct_name' => $dbbt->f("bank_account_holder"),
            
            // Level 2 data
            'x_po_num' => substr($order_number, 0, 20),
            'x_tax' => substr($d['order_tax'], 0, 15),
            'x_tax_exempt' => "FALSE",
            'x_freight' => $d['order_shipping'],
            'x_duty' => 0
        );

        //build the post string
        $poststring = '';
        foreach($formdata AS $key => $val){
            $poststring .= urlencode($key) . "=" . urlencode($val) . "&";
        }
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
              $vmLogger->err( curl_error( $CR ) );
              $html = "<br/><span class=\"message\">".$VM_LANG->_PHPSHOP_PAYMENT_INTERNAL_ERROR." authorize.net</span>";
              return false;
            }
            else {
                //echo $result; exit();
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
        $response = explode("|", $result);
        
        // Approved - Success!
        if ($response[0] == '1') {
           $d["order_payment_log"] = $VM_LANG->_PHPSHOP_PAYMENT_TRANSACTION_SUCCESS.": ";
           $d["order_payment_log"] .= $response[3];
           return True;
        } 
        // Payment Declined
        elseif ($response[0] == '2') {
           $vmLogger->err($response[3]);
           $d["order_payment_log"] = $response[3];
           return False;
        }
        // Transaction Error
        elseif ($response[0] == '3') {
           $vmLogger->err($response[3]);
           $d["order_payment_log"] = $response[3];
           return False;
        }
   }
   

   
}
