<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
* @version $Id: ps_montrada.php,v 1.4 2005/11/17 09:31:13 codename-matrix Exp $
* @package mambo-phpShop
* @subpackage Payment
* @copyright (C) 2005 Benjamin Schirmer
*
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* mambo-phpShop is Free Software.
* mambo-phpShop comes with absolute no warranty.
*
* www.mambo-phpshop.net

* The ps_montrada class, containing the payment processing code
*  for transactions with montrada.de
 */

class ps_montrada {

    var $debug = false;

    var $payment_code = "MO";
    var $classname = "ps_montrada";
  
    /**
    * Show all configuration parameters for this payment method
    * @returns boolean False when the Payment method has no configration
    */
    function show_configuration() { 
    
      global $PHPSHOP_LANG, $sess;
      $db =& new ps_DB;
      $payment_method_id = mosGetParam( $_REQUEST, 'payment_method_id', null );
      /** Read current Configuration ***/
      require_once(CLASSPATH ."payment/".$this->classname.".cfg.php");
    ?>
      <table>
        <tr><td colspan="3"><hr/></td></tr>
        <tr>
            <td><strong><?php echo $PHPSHOP_LANG->_PHPSHOP_PAYMENT_CVV2 ?></strong></td>
            <td>
                <select name="MO_CHECK_CARD_CODE" class="inputbox">
                <option <?php if (MO_CHECK_CARD_CODE == 'YES') echo "selected=\"selected\""; ?> value="YES">
                <?php echo $PHPSHOP_LANG->_PHPSHOP_ADMIN_CFG_YES ?></option>
                <option <?php if (MO_CHECK_CARD_CODE == 'NO') echo "selected=\"selected\""; ?> value="NO">
                <?php echo $PHPSHOP_LANG->_PHPSHOP_ADMIN_CFG_NO ?></option>
                </select>
            </td>
            <td><?php echo $PHPSHOP_LANG->_PHPSHOP_PAYMENT_CVV2_TOOLTIP ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $PHPSHOP_LANG->_PHPSHOP_ADMIN_CFG_MONTRADA_USERNAME ?></strong></td>
            <td>
                <input type="text" name="MO_USERNAME" class="inputbox" value="<?php echo MO_USERNAME ?>" />
            </td>
        </tr>
        <tr>
            <td><strong><?php echo $PHPSHOP_LANG->_PHPSHOP_ADMIN_CFG_MONTRADA_PASSWORD ?></strong></td>
            <td>
                <input type="text" name="MO_PASSWORD" class="inputbox" value="<?php echo MO_PASSWORD ?>" />
            </td>
        </tr>        
        <tr>
            <td><strong>Order Status for successful transactions</strong></td>
            <td>
                <select name="MO_VERIFIED_STATUS" class="inputbox" >
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
                      if (MO_VERIFIED_STATUS == $order_status_code[$i]) 
                         echo "\" selected=\"selected\">";
                      else
                         echo "\">";
                      echo $order_status_name[$i] . "</option>\n";
                    }?>
                    </select>
            </td>
            <td>Select the order status to which the actual order is set, if the Montrada.de Transaction was successful. 
            If using download selling options: select the status which enables the download (then the customer is instantly notified about the download via e-mail).
            </td>
        </tr>
        <tr>
            <td><strong>Order Status for failed transactions</strong></td>
            <td>
                <select name="MO_INVALID_STATUS" class="inputbox" >
                <?php
                    for ($i = 0; $i < sizeof($order_status_code); $i++) {
                      echo "<option value=\"" . $order_status_code[$i];
                      if (MO_INVALID_STATUS == $order_status_code[$i]) 
                         echo "\" selected=\"selected\">";
                      else
                         echo "\">";
                      echo $order_status_name[$i] . "</option>\n";
                    } ?>
                    </select>
            </td>
            <td>Select an order status for failed Montrada.de transactions.</td>
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
      
      $my_config_array = array(
                              "MO_CHECK_CARD_CODE" => $d['MO_CHECK_CARD_CODE'],
                              "MO_VERIFIED_STATUS" => $d['MO_VERIFIED_STATUS'],
                              "MO_INVALID_STATUS" => $d['MO_INVALID_STATUS'],
                              "MO_USERNAME" => $d['MO_USERNAME'],
                              "MO_PASSWORD" => $d['MO_PASSWORD']
                                                           
                            );
      $config = "<?php\n";
      $config .= "defined('_VALID_MOS') or die('Direct Access to this location is not allowed.'); \n\n";
      foreach( $my_config_array as $key => $value ) {
        $config .= "define ('$key', '$value');\n";
      }
      
      $config .= "?".">";
  
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
  ** created by: Benjamin Schirmer
  ** description: process transaction with Montrada GmbH
  ** parameters: $order_number, the number of the order, we're processing here
  **            $order_total, the total $ of the order
  ** returns: 
  ***************************************************************************/
   function process_payment($order_number, $order_total, &$d) {
        
        global $vendor_mail, $vendor_currency, $VM_LANG, $vmLogger;
        
        $ps_vendor_id = $_SESSION["ps_vendor_id"];
        $auth = $_SESSION['auth'];
        $ps_checkout = new ps_checkout;
      
        /*** Get the Configuration File for authorize.net ***/
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

        $host = "posh.montrada.de";
        $port = 443;
        $path = "/posh/cmd/posh/tpl/txn_result.tpl";  

        //Montrada vars to send
        $formdata = array (
            'command' => 'authorization',
            'orderid' => substr($order_number, 0, 20),
            'creditc' => $_SESSION['ccdata']['order_payment_number'],
            'expdat' => substr($_SESSION['ccdata']['order_payment_expire_year'], 2, 2).$_SESSION['ccdata']['order_payment_expire_month'],
            'currency' => $vendor_currency,
            'amount' => $order_total*100,
            'cvcode' => $_SESSION['ccdata']['credit_card_code']
        );
        
        //build the post string
        $poststring = '';
        foreach($formdata AS $key => $val){
            $poststring .= urlencode($key) . "=" . urlencode($val) . "&";
        }
        // strip off trailing ampersand
        $poststring = substr($poststring, 0, -1);
        
        /* DEBUG Message */
        if ($this->debug)
        {
            $vmLogger->debug( wordwrap($poststring, 60, "<br/>", 1) );
        }
        
        if( function_exists( "curl_init" )) {
        
            $CR = curl_init();
            curl_setopt($CR, CURLOPT_URL, "https://".$host.$path);
            curl_setopt($CR, CURLOPT_POST, 1);
            curl_setopt($CR, CURLOPT_FAILONERROR, true); 
            curl_setopt($CR, CURLOPT_POSTFIELDS, $poststring);
            curl_setopt($CR, CURLOPT_USERPWD, MO_USERNAME.":".MO_PASSWORD);
            curl_setopt($CR, CURLOPT_RETURNTRANSFER, 1);
             
            // No PEER certificate validation...as we don't have 
            // a certificate file for it to authenticate the host www.ups.com against!
            curl_setopt($CR, CURLOPT_SSL_VERIFYPEER, 0);
            //curl_setopt($CR, CURLOPT_SSLCERT , "/usr/locale/xxxx/clientcertificate.pem");
            
            $result = curl_exec( $CR );
            
            $error = curl_error( $CR );
            if( !empty( $error )) {
              $vmLogger->err( curl_error( $CR )
                              ."<br/><span class=\"message\">".$VM_LANG->_PHPSHOP_PAYMENT_INTERNAL_ERROR." authorize.net</span>" );
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
                fputs($fp, "Authorization: Basic ".base64_encode(MO_USERNAME.":".MO_PASSWORD)."\r\n");                
                fputs($fp, "Connection: close\r\n\r\n");
                fputs($fp, $poststring . "\r\n\r\n");
                
                //Get the response header from the server

                $data = "";
                while (!feof($fp)) {
                   $data .= fgets ($fp, 1024);
                }
                $data = explode("\r\n\r\n", $data);
                $result = trim( $data[1] );
                
          }
        }

        /* DEBUG Message */
        if ($this->debug)
            $vmLogger->debug( wordwrap( urldecode($result), 60, "<br/>", 1) );
        
        // Split Response-Data
        $data = explode("&", $result);
        foreach ($data as $var)
        {
           $var = explode("=", $var);
           $key = urldecode( $var[0] );
           $value = urldecode( $var[1] );
           
           $response[$key] = $value;
        }
        
        // Array of posherr values that get displayed
        $posherr1 = array("0", "100", "2014", "2016", "2018", "2040", "2042", "2048", "2090".
                          "2092", "2094", "2202", "2204");
        /* Display these error messages (ordered by id)
            0	(Transaktion erfolgreich abgeschlossen)
            100	(Transaktion ohne Erfolg abgeschlossen)
            2014	(Kartennummer, Parameter 'creditc' falsch)
            2016	(Gültigkeitsdatum, Parameter 'expdat' falsch)
            2018	(Kartenprüfwert, Parameter 'cvcode' falsch)
            2040	(Anfang oder Länge der Kartennummer falsch)
            2042	(Prüfsumme der Kartennummer falsch)
            2048	(Karte abgelaufen)
            2090	(Bankleitzahl, Parameter 'bankcode' falsch)
            2092	(Kontonummer, Parameter 'account' falsch)
            2094	(Name, Parameter 'cname' falsch)
            2202	(Bankleitzahl unbekannt)
            2204	(Kontonummer paSst nicht zur Bankleitzahl)        
        */        
        // Array of rc values that get display if posherr=100
        $rc1 = array("000", "005", "033", "091", "096");
        // Approved - Success!
        if (isset($response['posherr']) && ($response['posherr'] == 0)) {
           $d["order_payment_log"] = $PHPSHOP_LANG->_PHPSHOP_PAYMENT_TRANSACTION_SUCCESS.": ";
           $d["order_payment_log"] .= $response['rmsg'];
           // Catch Transaction ID
           $d["order_payment_trans_id"] = $response['trefnum'];

           return True;
           
           $db = new ps_DB;
           $q = "UPDATE #__{vm}_order_payment SET order_payment_code='',order_payment_number='',order_payment_expire='' WHERE order_id=$order_number";
           $db->query($q);
           $db->next_record();
        } 
        else
        {
           if ($response['posherr'] = "") $response['posherr'] = -1;
           $vmLogger->err( $VM_LANG->_PHPSHOP_PAYMENT_ERROR." ($response[posherr])" );
           
           if (in_array($response['posherr'], $posherr1))
           {
                 if ($response['posherr'] == 100)
                 {
                        if (in_array($response['rc'], $rc1))
                               $vmLogger->err( $response['rmsg'] );
                 } else {
                 $vmLogger->err( $response['rmsg'] );
                 }
           }
           $d["order_payment_log"] = $response['rmsg'];
           // Catch Transaction ID
           $d["order_payment_trans_id"] = $response['retrefnr'];
           return False;
        }
   }   
}
