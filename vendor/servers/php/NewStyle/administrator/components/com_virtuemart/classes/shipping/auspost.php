<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
/**
*
* @version $Id: auspost.php,v 1.1.2.1 2006/01/17 19:04:14 soeren_nb Exp $
* @package VirtueMart
* @subpackage shipping
* @copyright Copyright (C) 2006 Ben Wilson. All rights reserved.
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
* This class will charge a shipping rate determined by passing parameters to 
* Australia Post eDeliver Calculator located at http://drc.edeliver.com.au/ 
* @copyright (C) 2006 Ben Wilson, ben@diversionware.com.au
* 
*******************************************************************************
*/
class auspost {

	var $classname = "auspost";

	function list_rates( &$d ) {
		global $total, $tax_total, $CURRENCY_DISPLAY;

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

		//Create DB User Object for Current User
		$dbu = new ps_DB;
		$q  = "SELECT country,zip FROM #__{vm}_user_info WHERE user_info_id = '". $d["ship_to_info_id"] . "'";
		$dbu->query($q);
		if (!$dbu->next_record()) {
			/*$vmLogger->err( $VM_LANG->_PHPSHOP_CHECKOUT_ERR_SHIPTO_NOT_FOUND );
			return False;*/
		}

		//Create DB Vendor Object for Shop Vendor
		$dbv = new ps_DB;
		$q  = "SELECT * from #__{vm}_vendor, #__{vm}_country WHERE vendor_id='" . $_SESSION["ps_vendor_id"] . "' AND (vendor_country=country_2_code OR vendor_country=country_3_code)";
		$dbv->query($q);
		$dbv->next_record();

		//$dbv = new ps_DB
		//$q  = "SELECT * FROM #__{vm}_vendor WHERE vendor_id='".$_SESSION['ps_vendor_id']."'";
		//$dbv->query($q);
		//$dbv->next_record();

		//set up the variables for Australia Post Query

		//Postcode of the pick-up address (e.g. 3015)
		//$Order_Pickup_Postcode = '2615';
		//$Order_Pickup_Postcode = Pickup_Postcode;
		$Order_Pickup_Postcode = $dbv->f("vendor_zip");

		//Postcode of the delivery destination (e.g. 2615)
		//$Order_Destination_Postcode = '2001';
		$Order_Destination_Postcode = $dbu->f("zip");

		//The country of delivery destination designated by two alpha characters. For example, AU stands for Australia
		$Order_Country = 'AU';

		//The weight of the parcel or item measured in grams (g)
		//$Order_Weight = '10000';
		$Order_WeightKG = $d['weight'] ;
		$Order_Weight = $Order_WeightKG * 1000;


		//The type of servive, available types are "Standard", "Express", "Air", "Sea", and "Economy"
		//$Order_Service_Type = Service_Type;
		$Order_Service_Type = 'STANDARD';

		//The length of the item or parcel in millimetres (mm)
		//Auspost returns same value so long as this is valid ie between 100 and 500, so we use a fixed 250 as a placeholder
		$Order_Length = '250';

		//The width of the item or parcel in millimetres (mm)
		$Order_Width = '250';

		//The height of the item or parcel in millimetres (mm)
		$Order_Height = '250';
		
		//This is the quantity of items for which the customer is estimating the delivery charges
		//Always set to one, as virtuemart does the multiplying for us based on quantity in cart
		$Order_Quantity = '1';

		//Fee for packaging and handling, added to the delivery costs returned by auspost
		$Order_Handling_Fee = Handling_Fee;

	    // Collect variables into the query URI for Australia Post
		$myfile=file('http://drc.edeliver.com.au/ratecalc.asp?Pickup_Postcode='.$Order_Pickup_Postcode.'&Destination_Postcode='.$Order_Destination_Postcode.'&Country='.$Order_Country.'&Weight='.$Order_Weight.'&Service_Type='.$Order_Service_Type.'&Length='.$Order_Length.'&Width='.$Order_Width.'&Height='.$Order_Height.'&Quantity='.$Order_Quantity);

		// Get Australia Post charge value separate to 'charge='
		$APchargeArray = split('=',$myfile[0]);
		$APcharge = $APchargeArray[1];

		// Get Australia Post Time separate to 'days='
		$APtimeArray = split('=',$myfile[1]);
		$APtime = $APtimeArray[1];

		// error message
		$APerrorArray = split('=',$myfile[2]);
		$APerrorMessage = $APerrorArray[1];
		(string) $strAPerrorMessage = $APerrorMessage;  //necessary to type cast this to a string otherwise below comparator doesn't work ???

		if(substr($strAPerrorMessage,0,2) === "OK")
		{
			$Total_Shipping_Handling = $APcharge + $Order_Handling_Fee;

			$_SESSION[$shipping_rate_id] = 1;

			// THE ORDER OF THOSE VALUES IS IMPORTANT:
			// ShippingClassName|carrier_name|rate_name|totalshippingcosts|rate_id
			$shipping_rate_id = urlencode( $this->classname."|auspost|standard|".number_format($Total_Shipping_Handling,2));

			$html .= "\n<input type=\"radio\" name=\"shipping_rate_id\" id=\"auspost\" checked=\"checked\" value=\"$shipping_rate_id\" />\n";
			$html .= "<label for=\"auspost_shipping_rate\">Australia Post: ";
			$html .= $CURRENCY_DISPLAY->getFullValue($Total_Shipping_Handling);
			$html .= " (".$Order_WeightKG." kg)";
			$html .= "</label>";

			$_SESSION[$shipping_rate_id] = 1;

			echo $html;
			return true;
		}
		else
		{
			$html .= "<label>Australia Post shipping calculator failed, reason: ".$APerrorMessage;
			echo $html;
			return false;
		}
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

		if( intval(AUSPOST_TAX_CLASS)== 0 )
		return( 0 );
		else {
			require_once( CLASSPATH. "ps_tax.php" );
			$tax_rate = ps_tax::get_taxrate_by_id( intval(AUSPOST_TAX_CLASS) );
			return $tax_rate;
		}
	}

	/* Validate this Shipping method by checking if the SESSION contains the key
	* @returns boolean False when the Shipping method is not in the SESSION
	*/
	function validate( $d ) {

		$shipping_rate_id = $d["shipping_rate_id"];

		if( array_key_exists( $shipping_rate_id, $_SESSION )) {
			
			return true;
		}
		else {
			return false;
		}
	}
	/**
    * Show all configuration parameters for this Shipping method
    * @returns boolean False when the Shipping method has no configration
    */
	function show_configuration() {
		global $VM_LANG;
		/** Read current Configuration ***/
		require_once(CLASSPATH ."shipping/".$this->classname.".cfg.php");
    ?>
      <table>
    <tr>
        <td><img src="http://drc.edeliver.com.au/bt_aphome.gif" alt="AusPost Logo"></td>
    </tr>
    <tr>
        <td><strong>Packing and Handling Fee:</strong>
		</td>
		<td>
            <input type="text" name="Handling_Fee" class="inputbox" value="<?php echo Handling_Fee ?>" />
		</td>
		<td>
            <?php echo mm_ToolTip("This is your fee for packaging and handling, and is added to the delivery costs returned by auspost") ?>
        </td>
    </tr>
	  <tr>
		<td><strong><?php echo $VM_LANG->_PHPSHOP_UPS_TAX_CLASS ?></strong></td>
		<td>
		  <?php
		  require_once(CLASSPATH.'ps_tax.php');
		  ps_tax::list_tax_value("AUSPOST_TAX_CLASS", AUSPOST_TAX_CLASS) ?>
		</td>
		<td><?php echo mm_ToolTip($VM_LANG->_PHPSHOP_UPS_TAX_CLASS_TOOLTIP) ?><td>
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
	    global $vmLogger;

		$my_config_array = array("Handling_Fee" => $d['Handling_Fee'],
		"AUSPOST_TAX_CLASS" => $d['AUSPOST_TAX_CLASS']
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
