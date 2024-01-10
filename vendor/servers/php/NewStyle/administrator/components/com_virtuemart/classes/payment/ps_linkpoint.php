<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_linkpoint.php,v 1.4.2.2 2006/03/21 19:38:22 soeren_nb Exp $
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
* The ps_linkpoint class, containing the payment processing code
* for transactions with linkpoint.net or yourpay.com
* contains code for Recurring billing an/or PreAuth Options
*
* Installation:  You must have the linkpoint/yourpay.com API  file (lphp.php) in the
* current working directory, or your php includes directory.
* you also should have your public key file provided by linkpoint/yourpay.com secured
* in a directory outside of the webroot, but readable by the webserver daemon owner (ie; nobody)
*
* In the administrator console of VirtueMart -> Payment Method List -> Creditcard LP -> Configuration
* you can insert your store number, and public key location.
*
* Any questions, email jimmy@freshstation.org
* @copyright (C) 2005 James McMillan
*/
class ps_linkpoint {

    var $payment_code = "LP";
    var $classname = "ps_linkpoint";

    /**
	* Most of this top configuration code was stripped and hacked from the authorize.net payment class
    * Show all configuration parameters for this payment method
    * @returns boolean False when the Payment method has no configration
    */
    function show_configuration() {

      global $VM_LANG, $sess;
      $payment_method_id = mosGetParam( $_REQUEST, 'payment_method_id', null );
      /** Read current Configuration ***/
      require_once(CLASSPATH ."payment/".$this->classname.".cfg.php");
    ?>
      <table>
        <tr>
            <td><strong><?php echo "Linkpoint Store ID" ?></strong></td>
            <td>
                <input type="text" name="LP_LOGIN" class="inputbox" value="<? echo LP_LOGIN ?>" />
            </td>
            <td><?php echo "This is your Link Point Store Name" ?>
            </td>
        </tr>
        <tr>
            <td><strong><?php echo "Location Of Public Keyfile" ?></strong></td>
            <td>
                <input type="text" name="LP_KEYFILE" class="inputbox" value="<? echo LP_KEYFILE ?>" />
            </td>
            <td><?php echo "This is the full path of your LinkPoint Keyfile.  Example: /etc/linkpoint/mykey.pem" ?>
            </td>
        </tr>
        <tr>
            <td><strong><?php echo $VM_LANG->_PHPSHOP_PAYMENT_CVV2 ?></strong></td>
            <td>
                <select name="LP_CHECK_CARD_CODE" class="inputbox">
                <option <?php if (LP_CHECK_CARD_CODE == 'YES') echo "selected=\"selected\""; ?> value="YES">
                <?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_YES ?></option>
                <option <?php if (LP_CHECK_CARD_CODE == 'NO') echo "selected=\"selected\""; ?> value="NO">
                <?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_NO ?></option>
                </select>
            </td>
            <td><?php echo $VM_LANG->_PHPSHOP_PAYMENT_CVV2_TOOLTIP ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $VM_LANG->_PHPSHOP_PAYMENT_AN_RECURRING ?></strong></td>
            <td>
                <select name="LP_RECURRING" class="inputbox">
                <option <?php if (LP_RECURRING == 'YES') echo "selected=\"selected\""; ?> value="YES">
                <?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_YES ?></option>
                <option <?php if (LP_RECURRING == 'NO') echo "selected=\"selected\""; ?> value="NO">
                <?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_NO ?></option>
                </select>
            </td>
            <td><?php echo $VM_LANG->_PHPSHOP_PAYMENT_AN_RECURRING_TOOLTIP ?>
            </td>
        </tr>
        <tr>
            <td><strong><?php echo "Pre Auth for Recurring Billing?" ?></strong></td>
            <td>
                <select name="LP_PREAUTH" class="inputbox">
                <option <?php if (LP_PREAUTH == 'YES') echo "selected=\"selected\""; ?> value="YES">
                <?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_YES ?></option>
                <option <?php if (LP_PREAUTH == 'NO') echo "selected=\"selected\""; ?> value="NO">
                <?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_NO ?></option>
                </select>
            </td>
            <td><?php echo "Select yes, is billing is not processed immediately.  (ie; Free Trials)" ?>
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

      $my_config_array = array("LP_TEST_REQUEST" => $d['LP_TEST_REQUEST'],
                              "LP_LOGIN" => $d['LP_LOGIN'],
                              "LP_TYPE" => $d['LP_TYPE'],
                              "LP_KEYFILE" => $d['LP_KEYFILE'],
                              "LP_CHECK_CARD_CODE" => $d['LP_CHECK_CARD_CODE'],
                              "LP_RECURRING" => $d['LP_RECURRING'],
							  "LP_PREAUTH" => $d['LP_PREAUTH']
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
  ** created by: James McMillan
  ** description: process transaction linkpoint.net
  ** parameters: $order_number, the number of the order, we're processing here
  **            $order_total, the total $ of the order
  ** returns: T/F
  ***************************************************************************/
   function process_payment($order_number, $order_total, &$d) {
      global $vmLogger;

	  // We must include the yourpay/linkpoint api file. 
	  require( CLASSPATH . "payment/lphp.php" );
	  
	  // Declare new linkpoint php class
	  $mylphp =& new lphp();

        global $vendor_mail, $vendor_currency, $VM_LANG, $database;

        $ps_vendor_id = $_SESSION["ps_vendor_id"];
        $auth = $_SESSION['auth'];
        $ps_checkout = new ps_checkout;

        require_once(CLASSPATH ."payment/".$this->classname.".cfg.php");


        // Get user billing information
        $dbbt = new ps_DB;
        $qt = "SELECT * FROM `#__{vm}_user_info` WHERE user_id='".$auth["user_id"]."' AND address_type='BT'";
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

// Start gathering the information needed for the XML transaction
        $cuname = substr($dbbt->f("first_name"), 0, 25) . " " . substr($dbbt->f("last_name"), 0, 25);
// The following should be static for linkpoint, if not, change to the specified host
        $myorder["host"]       = "secure.linkpt.net";
        $myorder["port"]       = "1129";
        $myorder["keyfile"]    = LP_KEYFILE;
        $myorder["configfile"] = LP_LOGIN; 

        $myorder["name"]     = $cuname;
        $myorder["company"]  = substr($dbbt->f("company"), 0, 50);
        $myorder["address1"] = substr($dbbt->f("address_1"), 0, 60);
        $myorder["address2"] = substr($dbbt->f("address_2"), 0, 60);
        $myorder["city"]     = substr($dbbt->f("city"), 0, 40);
        $myorder["state"]    = substr($dbbt->f("state"), 0, 40);
        $myorder["zip"]      = substr($dbbt->f("zip"), 0, 20);
        $myorder["country"]  = substr($dbbt->f("country"), 0, 60);
        $myorder["phone"]    = substr($dbbt->f("phone_1"), 0, 25);
        $myorder["fax"]      = substr($dbbt->f("fax"), 0, 25);
        $myorder["email"]    = $dbbt->f("email");

        $myorder["cardnumber"]    = $_SESSION['ccdata']['order_payment_number'];
        $myorder["cardexpmonth"]  = $_SESSION['ccdata']['order_payment_expire_month'];
        $myorder["cardexpyear"]   = substr($_SESSION['ccdata']['order_payment_expire_year'],2,2);
        $myorder["cvmindicator"]  = "provided";
        $myorder["cvmvalue"]      = $_SESSION['ccdata']['credit_card_code'];
        $myorder["chargetotal"]   = $order_total;

  // Working on a fix for this orderid, this process seems to send "Duplicate transaction"
  // if the user made a typo the first time they entered their card number.  All in all, it works
  // but their could ba a change.
        $myorder["oid"]           = $order_number; // need to clean this up, no offence Soeren, but those order numbers are a mess.


        // Let me see the output.
        //$myorder["debugging"]="true";


	  if (LP_RECURRING == "YES") {

	//if we are doing recurring billing, and the payments are not processed imedeately, we should run a Pre-Auth
	// This is mostly if you are offering a customer x ammount of free days for a service, if you are charging the card
	// at this time, you can uncomment the following 2 lines Pre-Auth part .
		if (LP_PREAUTH == "YES") {
  
		  $myorder["ordertype"]     = "PREAUTH";
		  // Process the PREAUTH
		  $result = $mylphp->curl_process($myorder);
  
		  if ($result["r_approved"] != "APPROVED") {   // transaction failed, print the reason
			$vmLogger->err( $result["r_error"] );
			$d["order_payment_log"] = $result["r_error"];
			$d["order_payment_log"] .= $result["r_message"];
			$d["order_payment_trans_id"] = $result["r_ordernum"];
			return False;
		  }
		}
  
		$myorder["action"] = "SUBMIT";
		$myorder["installments"] = -1;
		$myorder["periodicity"] = monthly;
		// We will give them 30 days free.
		$myorder["startdate"] =  date(Ymd,time()+2592000);
		$myorder["threshold"] =  3;
		$myorder["ordertype"]     = "SALE";
		
		// If everything worked out fine, then process the order here and leave the class.  Saved by the Bell
		$result = $mylphp->curl_process($myorder);
		
		if ($result["r_approved"] != "SUBMITTED")    // transaction failed, print the reason
		{
		   $vmLogger->err( $result["r_error"] );
		   $d["order_payment_log"] = $result["r_error"];
		   $d["order_payment_log"] .= $result["r_message"];
		   $d["order_payment_trans_id"] = $result["r_ordernum"];
		   return False;
		}
		else    // Success, let's return
		{
		   $d["order_payment_log"] = $VM_LANG->_PHPSHOP_PAYMENT_TRANSACTION_SUCCESS.": ";
		   $d["order_payment_log"] = $result["r_approved"];
		   // Catch Transaction ID
		   $d["order_payment_trans_id"] = $result["r_ordernum"];
		   return True;
		}
	  }
	  else{
		// Not recurring, just plain old sale.
		$myorder["ordertype"]     = "SALE";
	  }

// If everything worked out fine, then process the order.
	  $result = $mylphp->curl_process($myorder);

	  if ($result["r_approved"] != "APPROVED")    // transaction failed, print the reason
	  {
		 $vmLogger->err( $result["r_error"] );
		 $d["order_payment_log"] = $result["r_error"];
		 $d["order_payment_log"] .= $result["r_message"];
		 $d["order_payment_trans_id"] = $result["r_ordernum"];
		 return False;
	  }
	  else    // Success, let's return
	  {
		 $d["order_payment_log"] = $VM_LANG->_PHPSHOP_PAYMENT_TRANSACTION_SUCCESS.": ";
		 $d["order_payment_log"] = $result["r_approved"];
		 // Catch Transaction ID
		 $d["order_payment_trans_id"] = $result["r_ordernum"];
		 return True;
	  }

   }

}
