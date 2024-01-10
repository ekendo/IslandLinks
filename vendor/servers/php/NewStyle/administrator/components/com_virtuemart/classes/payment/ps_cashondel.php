<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
* @version $Id: ps_cashondelpay.php,v 1.4 2005/05/27 19:33:57 ei
*
* a special type of 'cash on delivey':
* its fee depend on total sum
*
* @version $Id: ps_cashondel.php,v 1.3 2005/09/29 20:02:18 soeren_nb Exp $
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

class ps_cashondel {

    var $classname = "ps_cashondel";
    var $payment_code = "PU";
    
    /**
    * Show all configuration parameters for this payment method
    * @returns boolean False when the Payment method has no configration
    */
    function show_configuration() {
        global $VM_LANG;
        
        /** Read current Configuration ***/
        require_once(CLASSPATH ."payment/".$this->classname.".cfg.php");
		echo $VM_LANG->_PHPSHOP_SPEC_CASH_ON_DELIVER_RATES;
        ?>
        <table>
          <tr>
          <td align="center"><b><?=$VM_LANG->_PHPSHOP_ORDER_PRINT_TOTAL?></b></td>
          <td align="center"><b><?=$VM_LANG->_PHPSHOP_PAYMENT_METHOD_LIST_FEE?></b></td>
          </tr>
          <tr>
          <td><strong>5000</strong> =></td>
              <td>
                  <input type="text" name="CASH_ON_DEL_5000" class="inputbox" value="<?  echo CASH_ON_DEL_5000 ?>" />
              </td>
          </tr> 
          <tr>
          <td><strong>10000</strong> =></td>
              <td>
                  <input type="text" name="CASH_ON_DEL_10000" class="inputbox" value="<?  echo CASH_ON_DEL_10000 ?>" />
              </td>
          </tr>
          <tr>
          <td><strong>20000</strong> =></td>
              <td>
                  <input type="text" name="CASH_ON_DEL_20000" class="inputbox" value="<?  echo CASH_ON_DEL_20000 ?>" />
              </td>
          </tr>
          <tr>
          <td><strong>30000</strong> =></td>
              <td>
                  <input type="text" name="CASH_ON_DEL_30000" class="inputbox" value="<?  echo CASH_ON_DEL_30000 ?>" />
              </td>
          </tr>
          <tr>
          <td><strong>40000</strong> =></td>
              <td>
                  <input type="text" name="CASH_ON_DEL_40000" class="inputbox" value="<?  echo CASH_ON_DEL_40000 ?>" />
              </td>
          </tr>
          <tr>
          <td><strong>50000</strong> =></td>
              <td>
                  <input type="text" name="CASH_ON_DEL_50000" class="inputbox" value="<?  echo CASH_ON_DEL_50000 ?>" />
              </td>
          </tr>
          <tr>
          <td><strong>100000</strong> =></td>
              <td>
                  <input type="text" name="CASH_ON_DEL_100000" class="inputbox" value="<?  echo CASH_ON_DEL_100000 ?>" />
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
      
      $my_config_array = array("CASH_ON_DEL_5000" => $d['CASH_ON_DEL_5000'],
                               "CASH_ON_DEL_10000" => $d['CASH_ON_DEL_10000'],
                               "CASH_ON_DEL_20000" => $d['CASH_ON_DEL_20000'],
                               "CASH_ON_DEL_30000" => $d['CASH_ON_DEL_30000'],
                               "CASH_ON_DEL_40000" => $d['CASH_ON_DEL_40000'],
                               "CASH_ON_DEL_50000" => $d['CASH_ON_DEL_50000'],
                               "CASH_ON_DEL_100000" => $d['CASH_ON_DEL_100000'],
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

function get_payment_rate($sum)
{
  /*** Get the Configuration File  ***/
  require_once(CLASSPATH ."payment/".$this->classname.".cfg.php");

  if ($sum < 5000)
    return -(CASH_ON_DEL_5000);
  elseif ($sum < 10000)
    return -(CASH_ON_DEL_10000);
  elseif ($sum < 20000)
    return -(CASH_ON_DEL_20000);
  elseif ($sum < 30000)
    return -(CASH_ON_DEL_30000);
  elseif ($sum < 40000)
    return -(CASH_ON_DEL_40000);
  elseif ($sum < 50000)
    return -(CASH_ON_DEL_50000);
  elseif ($sum < 100000)
    return -(CASH_ON_DEL_100000);
  else
    return -(CASH_ON_DEL_100000);

//	return -($sum * 0.10);
}

  /**************************************************************************
  ** name: process_payment()
  ** returns: 
  ***************************************************************************/
   function process_payment($order_number, $order_total, &$d) {
//echo "process_payment $order_number $order_total ";
        return true;
    }
   
}
