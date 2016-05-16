<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.'); 
/*
* @version $Id: shipvalue.php,v .1 2005/09  r_lewis
* @package Mambo_4.5.1 tested on Version: Mambo 4.5.1.3 Stable [Three For Rum Reassigned]
* @subpackage mambo-phpShop
* @copyright (C) 2005 Rhys Lewis with due respect to Micah Shawn and Bret (allbloodrunsred)

* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Based on mambo-phpShop by Soeren Eberhardt.  Thank you Soeren!
* mambo-phpShop is Free Software.
* mambo-phpShop comes with absolute no warranty.
* www.mambo-phpshop.net
******************************************************************************
* 
* This class will charge a fixed shipping rate based on the total order value
* up to 10 thresholds for  total order value can be set in admin>store>shipping module list>shipvalue
* 
*******************************************************************************
*/
class shipvalue {



  var $classname = "shipvalue";
  
  function list_rates( &$d ) {
	global $total, $tax_total, $CURRENCY_DISPLAY;
	$db =& new ps_DB;
	$dbv =& new ps_DB;
	
	$cart = $_SESSION['cart'];

    /** Read current Configuration ***/
	require_once(CLASSPATH ."shipping/".$this->classname.".cfg.php");
	
	if ( $_SESSION['auth']['show_price_including_tax'] != 1 ) {
	  $taxrate = 1;
	  $order_total = $total + $tax_total;
	}
	else {
	  $taxrate = $this->get_tax_rate() + 1;
	  $order_total = $total;  
	}
		
	//Define shipping value breaks
	$base_ship1 = BASE_SHIP1;
	$base_ship2 = BASE_SHIP2;
	$base_ship3 = BASE_SHIP3;
	$base_ship4 = BASE_SHIP4;
	$base_ship5 = BASE_SHIP5;
	$base_ship6 = BASE_SHIP6;
	$base_ship7 = BASE_SHIP7;
	$base_ship8 = BASE_SHIP8;
	$base_ship9 = BASE_SHIP9;
	$base_ship10 = BASE_SHIP10;

	//Flat rate shipping charge up to minimum value
	$flat_charge1 = BASE_CHARGE1;
	$flat_charge2 = BASE_CHARGE2;
	$flat_charge3 = BASE_CHARGE3;
	$flat_charge4 = BASE_CHARGE4;
	$flat_charge5 = BASE_CHARGE5;
	$flat_charge6 = BASE_CHARGE6;
	$flat_charge7 = BASE_CHARGE7;
	$flat_charge8 = BASE_CHARGE8;
	$flat_charge9 = BASE_CHARGE9;
	$flat_charge10 = BASE_CHARGE10;
	  

	 if($order_total < $base_ship1) {
	  $flat_charge *= $taxrate;
	  $shipping_rate_id = urlencode($this->classname."|STD|Standard Shipping under ".$base_ship1."|".$flat_charge1);
	  $html = "";
	  $html .= "\n<input type=\"radio\" name=\"shipping_rate_id\" checked=\"checked\" value=\"$shipping_rate_id\" />\n";
	  $html .= "Standard Shipping: ".$CURRENCY_DISPLAY->getFullValue($flat_charge1);
	  $_SESSION[$shipping_rate_id] = 1;
	}
else if($order_total < $base_ship2) {
	  $flat_charge *= $taxrate;
	  $shipping_rate_id = urlencode($this->classname."|STD|Standard Shipping under ".$base_ship2."|".$flat_charge2);
  $html = "";
  $html .= "\n<input type=\"radio\" name=\"shipping_rate_id\" checked=\"checked\" value=\"$shipping_rate_id\" />\n";
  $html .= "Standard Shipping: ".$CURRENCY_DISPLAY->getFullValue($flat_charge2);
  $_SESSION[$shipping_rate_id] = 1;
}
else if($order_total < $base_ship3) {
	  $flat_charge *= $taxrate;
	  $shipping_rate_id = urlencode($this->classname."|STD|Standard Shipping under ".$base_ship3."|".$flat_charge3);
  $html = "";
  $html .= "\n<input type=\"radio\" name=\"shipping_rate_id\" checked=\"checked\" value=\"$shipping_rate_id\" />\n";
  $html .= "Standard Shipping: ".$CURRENCY_DISPLAY->getFullValue($flat_charge3);
  $_SESSION[$shipping_rate_id] = 1;
}
else if($order_total < $base_ship4) {
	  $flat_charge *= $taxrate;
	  $shipping_rate_id = urlencode($this->classname."|STD|Standard Shipping under ".$base_ship4."|".$flat_charge4);
  $html = "";
  $html .= "\n<input type=\"radio\" name=\"shipping_rate_id\" checked=\"checked\" value=\"$shipping_rate_id\" />\n";
  $html .= "Standard Shipping: ".$CURRENCY_DISPLAY->getFullValue($flat_charge4);
  $_SESSION[$shipping_rate_id] = 1;
}
else if($order_total < $base_ship5) {
	  $flat_charge *= $taxrate;
	  $shipping_rate_id = urlencode($this->classname."|STD|Standard Shipping under ".$base_ship5."|".$flat_charge5);
  $html = "";
  $html .= "\n<input type=\"radio\" name=\"shipping_rate_id\" checked=\"checked\" value=\"$shipping_rate_id\" />\n";
  $html .= "Standard Shipping: ".$CURRENCY_DISPLAY->getFullValue($flat_charge5);
  $_SESSION[$shipping_rate_id] = 1;
}
else if($order_total < $base_ship6) {
	  $flat_charge *= $taxrate;
	  $shipping_rate_id = urlencode($this->classname."|STD|Standard Shipping under ".$base_ship6."|".$flat_charge6);
  $html = "";
  $html .= "\n<input type=\"radio\" name=\"shipping_rate_id\" checked=\"checked\" value=\"$shipping_rate_id\" />\n";
  $html .= "Standard Shipping: ".$CURRENCY_DISPLAY->getFullValue($flat_charge6);
  $_SESSION[$shipping_rate_id] = 1;
}
else if($order_total < $base_ship7) {
	  $flat_charge *= $taxrate;
	  $shipping_rate_id = urlencode($this->classname."|STD|Standard Shipping under ".$base_ship7."|".$flat_charge7);
  $html = "";
  $html .= "\n<input type=\"radio\" name=\"shipping_rate_id\" checked=\"checked\" value=\"$shipping_rate_id\" />\n";
  $html .= "Standard Shipping: ".$CURRENCY_DISPLAY->getFullValue($flat_charge7);
  $_SESSION[$shipping_rate_id] = 1;
}
else if($order_total < $base_ship8) {
	  $flat_charge *= $taxrate;
	  $shipping_rate_id = urlencode($this->classname."|STD|Standard Shipping under ".$base_ship8."|".$flat_charge8);
  $html = "";
  $html .= "\n<input type=\"radio\" name=\"shipping_rate_id\" checked=\"checked\" value=\"$shipping_rate_id\" />\n";
  $html .= "Standard Shipping: ".$CURRENCY_DISPLAY->getFullValue($flat_charge8);
  $_SESSION[$shipping_rate_id] = 1;
}
else if($order_total < $base_ship9) {
	  $flat_charge *= $taxrate;
	  $shipping_rate_id = urlencode($this->classname."|STD|Standard Shipping under ".$base_ship9."|".$flat_charge9);
  $html = "";
  $html .= "\n<input type=\"radio\" name=\"shipping_rate_id\" checked=\"checked\" value=\"$shipping_rate_id\" />\n";
  $html .= "Standard Shipping: ".$CURRENCY_DISPLAY->getFullValue($flat_charge9);
  $_SESSION[$shipping_rate_id] = 1;
}
else if($order_total < $base_ship10) {
	  $flat_charge *= $taxrate;
	  $shipping_rate_id = urlencode($this->classname."|STD|Standard Shipping under ".$base_ship10."|".$flat_charge10);
  $html = "";
  $html .= "\n<input type=\"radio\" name=\"shipping_rate_id\" checked=\"checked\" value=\"$shipping_rate_id\" />\n";
  $html .= "Standard Shipping: ".$CURRENCY_DISPLAY->getFullValue($flat_charge10);
  $_SESSION[$shipping_rate_id] = 1;
}
	
	echo $html;
	return true;
  

  }
	
  function get_rate( &$d ) {	
  
	$shipping_rate_id = $d["shipping_rate_id"];
	$is_arr = explode("|", urldecode(urldecode($shipping_rate_id)) );
	$order_shipping = $is_arr[3];
	
	return $order_shipping;
	
  }
  
	
  function get_tax_rate() {
	
    /** Read current Configuration ***/
	require_once(CLASSPATH ."shipping/".$this->classname.".cfg.php");
	
	if( intval(SHIPVALUE_TAX_CLASS)== 0 )
	  return( 0 );
	else {
	  require_once( CLASSPATH. "ps_tax.php" );
	  $tax_rate = ps_tax::get_taxrate_by_id( intval(SHIPVALUE_TAX_CLASS) );
	  return $tax_rate;
	}
  }
  
	/* Validate this Shipping method by checking if the SESSION contains the key
    * @returns boolean False when the Shipping method is not in the SESSION
    */
	function validate( $d ) {
	
	  $shipping_rate_id = $d["shipping_rate_id"];
	  
	  if( array_key_exists( $shipping_rate_id, $_SESSION ))
		return true;
	  else
		return false;
	}
	/**
    * Show all configuration parameters for this Shipping method
    * @returns boolean False when the Shipping method has no configration
    */
    function show_configuration() { 
		global $PHPSHOP_LANG;
      /** Read current Configuration ***/
      require_once(CLASSPATH ."shipping/".$this->classname.".cfg.php");
    ?>
      <table>
    <tr>
        <td><strong>Order total value 1:</strong></td>
		<td>
            <input type="text" name="BASE_SHIP1" class="inputbox" value="<?php echo BASE_SHIP1 ?>" />
		</td>
        <td><strong>Shipping charge 1:</strong></td>
		<td>
            <input type="text" name="BASE_CHARGE1" class="inputbox" value="<?php echo BASE_CHARGE1 ?>" />
		</td>
		<td>
        <?php echo mosToolTip("Shipping charge 1 will apply to order values less than Order total value 1.") ?>
        </td>
    </tr>
    <tr>
        <td><strong>Order total value 2:</strong></td>
		<td>
            <input type="text" name="BASE_SHIP2" class="inputbox" value="<?php echo BASE_SHIP2 ?>" />
		</td>
        <td><strong>Shipping charge 2:</strong></td>
		<td>
            <input type="text" name="BASE_CHARGE2" class="inputbox" value="<?php echo BASE_CHARGE2 ?>" />
		</td>
		<td>
        <?php echo mosToolTip("Shipping charge 2 will apply to order values less than Order total value 2.") ?>
        </td>
    </tr>
    <tr>
        <td><strong>Order total value 3:</strong></td>
		<td>
            <input type="text" name="BASE_SHIP3" class="inputbox" value="<?php echo BASE_SHIP3 ?>" />
		</td>
        <td><strong>Shipping charge 3:</strong></td>
		<td>
            <input type="text" name="BASE_CHARGE3" class="inputbox" value="<?php echo BASE_CHARGE3 ?>" />
		</td>
		<td>
        <?php echo mosToolTip("Shipping charge 3 will apply to order values less than Order total value 3.") ?>
        </td>
    </tr>
    <tr>
        <td><strong>Order total value 4:</strong></td>
		<td>
            <input type="text" name="BASE_SHIP4" class="inputbox" value="<?php echo BASE_SHIP4 ?>" />
		</td>
        <td><strong>Shipping charge 4:</strong></td>
		<td>
            <input type="text" name="BASE_CHARGE4" class="inputbox" value="<?php echo BASE_CHARGE4 ?>" />
		</td>
		<td>
        <?php echo mosToolTip("Shipping charge 4 will apply to order values less than Order total value 4.") ?>
        </td>
    </tr>
    <tr>
        <td><strong>Order total value 5:</strong></td>
		<td>
            <input type="text" name="BASE_SHIP5" class="inputbox" value="<?php echo BASE_SHIP5 ?>" />
		</td>
        <td><strong>Shipping charge 5:</strong></td>
		<td>
            <input type="text" name="BASE_CHARGE5" class="inputbox" value="<?php echo BASE_CHARGE5 ?>" />
		</td>
		<td>
        <?php echo mosToolTip("Shipping charge 5 will apply to order values less than Order total value 5.") ?>
        </td>
    </tr>
    <tr>
        <td><strong>Order total value 6:</strong></td>
		<td>
            <input type="text" name="BASE_SHIP6" class="inputbox" value="<?php echo BASE_SHIP6 ?>" />
		</td>
        <td><strong>Shipping charge 6:</strong></td>
		<td>
            <input type="text" name="BASE_CHARGE6" class="inputbox" value="<?php echo BASE_CHARGE6 ?>" />
		</td>
		<td>
        <?php echo mosToolTip("Shipping charge 6 will apply to order values less than Order total value 6.") ?>
        </td>
    </tr>
    <tr>
        <td><strong>Order total value 7:</strong></td>
		<td>
            <input type="text" name="BASE_SHIP7" class="inputbox" value="<?php echo BASE_SHIP7 ?>" />
		</td>
        <td><strong>Shipping charge 7:</strong></td>
		<td>
            <input type="text" name="BASE_CHARGE7" class="inputbox" value="<?php echo BASE_CHARGE7 ?>" />
		</td>
		<td>
        <?php echo mosToolTip("Shipping charge 7 will apply to order values less than Order total value 7.") ?>
        </td>
    </tr>
    <tr>
        <td><strong>Order total value 8:</strong></td>
		<td>
            <input type="text" name="BASE_SHIP8" class="inputbox" value="<?php echo BASE_SHIP8 ?>" />
		</td>
        <td><strong>Shipping charge 8:</strong></td>
		<td>
            <input type="text" name="BASE_CHARGE8" class="inputbox" value="<?php echo BASE_CHARGE8 ?>" />
		</td>
		<td>
        <?php echo mosToolTip("Shipping charge 8 will apply to order values less than Order total value 8.") ?>
        </td>
    </tr>
    <tr>
        <td><strong>Order total value 9:</strong></td>
		<td>
            <input type="text" name="BASE_SHIP9" class="inputbox" value="<?php echo BASE_SHIP9 ?>" />
		</td>
        <td><strong>Shipping charge 9:</strong></td>
		<td>
            <input type="text" name="BASE_CHARGE9" class="inputbox" value="<?php echo BASE_CHARGE9 ?>" />
		</td>
		<td>
        <?php echo mosToolTip("Shipping charge 9 will apply to order values less than Order total value 9.") ?>
        </td>
    </tr>
    <tr>
        <td><strong>Order total value 10:</strong></td>
		<td>
            <input type="text" name="BASE_SHIP10" class="inputbox" value="<?php echo BASE_SHIP10 ?>" />
		</td>
        <td><strong>Shipping charge 10:</strong></td>
		<td>
            <input type="text" name="BASE_CHARGE10" class="inputbox" value="<?php echo BASE_CHARGE10 ?>" />
		</td>
		<td>
        <?php echo mosToolTip("Shipping charge 10 will apply to order values less than Order total value 10.") ?>
        </td>
    </tr>

	  <tr>
		<td><strong><?php echo $PHPSHOP_LANG->_PHPSHOP_UPS_TAX_CLASS ?></strong></td>
		<td>
		  <?php
		  require_once(CLASSPATH.'ps_tax.php');
		  ps_tax::list_tax_value("SHIPVALUE_TAX_CLASS", SHIPVALUE_TAX_CLASS) ?>
		</td>
		<td colspan="3"><?php echo mosToolTip("Use the following tax class on the shipping charge.  The shipping charge values above will then be inclusive of this tax rate.") ?><td>
	  </tr>	

	</table>
   <?php
      // return false if there's no configuration
      return true;
   }
  /**
  * Returns the "is_writeable" status of the configuration file
  * @param void
  * @returns boolean True when the configuration file is writeable, false when not
  */
   function configfile_writeable() {
      return is_writeable( CLASSPATH."shipping/".$this->classname.".cfg.php" );
   }
   
	/**
	* Writes the configuration file for this shipping method
	* @param array An array of objects
	* @returns boolean True when writing was successful
	*/
   function write_configuration( &$d ) {
      
      $my_config_array = array("BASE_SHIP1" => $d['BASE_SHIP1'],
							  "BASE_SHIP2" => $d['BASE_SHIP2'],
							  "BASE_SHIP3" => $d['BASE_SHIP3'],
							  "BASE_SHIP4" => $d['BASE_SHIP4'],
							  "BASE_SHIP5" => $d['BASE_SHIP5'],
							  "BASE_SHIP6" => $d['BASE_SHIP6'],
							  "BASE_SHIP7" => $d['BASE_SHIP7'],
							  "BASE_SHIP8" => $d['BASE_SHIP8'],
							  "BASE_SHIP9" => $d['BASE_SHIP9'],
							  "BASE_SHIP10" => $d['BASE_SHIP10'],
							  "BASE_CHARGE1" => $d['BASE_CHARGE1'],
							  "BASE_CHARGE2" => $d['BASE_CHARGE2'],
							  "BASE_CHARGE3" => $d['BASE_CHARGE3'],
							  "BASE_CHARGE4" => $d['BASE_CHARGE4'],
							  "BASE_CHARGE5" => $d['BASE_CHARGE5'],
							  "BASE_CHARGE6" => $d['BASE_CHARGE6'],
							  "BASE_CHARGE7" => $d['BASE_CHARGE7'],
							  "BASE_CHARGE8" => $d['BASE_CHARGE8'],
							  "BASE_CHARGE9" => $d['BASE_CHARGE9'],
							  "BASE_CHARGE10" => $d['BASE_SHIP10'],
							  "SHIPVALUE_TAX_CLASS" => $d['SHIPVALUE_TAX_CLASS']
							  );
      $config = "<?php\n";
      $config .= "defined('_VALID_MOS') or die('Direct Access to this location is not allowed.'); \n\n";
      foreach( $my_config_array as $key => $value ) {
        $config .= "define ('$key', '$value');\n";
      }
      
      $config .= "?>";
  
      if ($fp = fopen(CLASSPATH ."shipping/".$this->classname.".cfg.php", "w")) {
          fputs($fp, $config, strlen($config));
          fclose ($fp);
          return true;
     }
     else {
		$vmLogger->err( "Error writing to configuration file" );
        return false;
	 }
   }
}
	

?>
