<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_paypal.php,v 1.3.2.2 2006/05/06 10:05:27 soeren_nb Exp $
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
* This class implements the configuration panel for paypal
* If you want to change something "internal", you must modify the 'payment extra info'
* in the payment method form of the PayPal payment method
*/
class ps_paypal {

    var $classname = "ps_paypal";
    var $payment_code = "PAYPAL";
    
    /**
    * Show all configuration parameters for this payment method
    * @returns boolean False when the Payment method has no configration
    */
    function show_configuration() {
        global $VM_LANG;
        $db = new ps_DB();
        
        /** Read current Configuration ***/
        include_once(CLASSPATH ."payment/".$this->classname.".cfg.php");
    ?>
    <table class="adminform">
        <tr>
        <td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ENABLE_AUTORIZENET_TESTMODE ?></strong></td>
            <td>
                <select name="PAYPAL_DEBUG" class="inputbox" >
                <option <?php if (@PAYPAL_DEBUG == '1') echo "selected=\"selected\""; ?> value="1"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_YES ?></option>
                <option <?php if (@PAYPAL_DEBUG != '1') echo "selected=\"selected\""; ?> value="0"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_NO ?></option>
                </select>
            </td>
            <td>When enabled, let's you test your Notify Script File 
            <pre><?php echo COMPONENTURL."notify.php" ?></pre>
            through this Testing Form: <a href="http://www.eliteweaver.co.uk/testing/ipntest.php" target="_blank">www.eliteweaver.co.uk/testing/ipntest.php</a>
            </td>
        </tr>
        <tr>
        <td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PAYPAL_EMAIL ?></strong></td>
            <td>
                <input type="text" name="PAYPAL_EMAIL" class="inputbox" value="<?  echo PAYPAL_EMAIL ?>" />
            </td>
            <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PAYPAL_EMAIL_EXPLAIN ?>
            </td>
        </tr>
        <tr>
            <td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PAYPAL_STATUS_SUCCESS ?></strong></td>
            <td>
                <select name="PAYPAL_VERIFIED_STATUS" class="inputbox" >
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
                      if (PAYPAL_VERIFIED_STATUS == $order_status_code[$i]) 
                         echo "\" selected=\"selected\">";
                      else
                         echo "\">";
                      echo $order_status_name[$i] . "</option>\n";
                    }?>
                    </select>
            </td>
            <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PAYPAL_STATUS_SUCCESS_EXPLAIN ?>
            </td>
        </tr>
        <tr>
            <td><strong>Order Status for Pending Payments</strong></td>
            <td>
                <select name="PAYPAL_PENDING_STATUS" class="inputbox" >
                <?php
                    for ($i = 0; $i < sizeof($order_status_code); $i++) {
                      echo "<option value=\"" . $order_status_code[$i];
                      if (PAYPAL_PENDING_STATUS == $order_status_code[$i]) 
                         echo "\" selected=\"selected\">";
                      else
                         echo "\">";
                      echo $order_status_name[$i] . "</option>\n";
                    } ?>
                    </select>
            </td>
            <td>The order Status to which Orders are set, which have no completed Payment Transaction. 
            The transaction was not cancelled in this case, but it is just pending and waiting for completion.</td>
        </tr>

        <tr>
        <td><strong>Accept only verified buyers?</strong></td>
            <td>
                <select name="PAYPAL_VERIFIED_ONLY" class="inputbox" >
	                <option <?php if (@PAYPAL_VERIFIED_ONLY != '1') echo "selected=\"selected\""; ?> value="0"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_NO ?></option>
	                <option <?php if (@PAYPAL_VERIFIED_ONLY == '1') echo "selected=\"selected\""; ?> value="1"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_YES ?></option>
                </select>
            </td>
            <td>Here you can choose if you only want to accept payments from buyers with a
            <strong>verified</strong> PayPal account (when an account is not verified, PayPal does transfer the funds, but they do not fully guarantee the validity of the sale).
            </td>
        </tr>
        </tr>
            <tr>
            <td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PAYPAL_STATUS_FAILED ?></strong></td>
            <td>
                <select name="PAYPAL_INVALID_STATUS" class="inputbox" >
                <?php
                    for ($i = 0; $i < sizeof($order_status_code); $i++) {
                      echo "<option value=\"" . $order_status_code[$i];
                      if (PAYPAL_INVALID_STATUS == $order_status_code[$i]) 
                         echo "\" selected=\"selected\">";
                      else
                         echo "\">";
                      echo $order_status_name[$i] . "</option>\n";
                    } ?>
                    </select>
            </td>
            <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PAYPAL_STATUS_FAILED_EXPLAIN ?>
            </td>
        </tr>
      </table>
    <?php
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
                              "PAYPAL_DEBUG" => $d['PAYPAL_DEBUG'],
                              "PAYPAL_EMAIL" => $d['PAYPAL_EMAIL'],
                              "PAYPAL_VERIFIED_ONLY" => $d['PAYPAL_VERIFIED_ONLY'],
                              "PAYPAL_VERIFIED_STATUS" => $d['PAYPAL_VERIFIED_STATUS'],
                              "PAYPAL_PENDING_STATUS" => $d['PAYPAL_PENDING_STATUS'],
                              "PAYPAL_INVALID_STATUS" => $d['PAYPAL_INVALID_STATUS']
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
  ** returns: 
  ***************************************************************************/
   function process_payment($order_number, $order_total, &$d) {
        return true;
    }
   
}

?>