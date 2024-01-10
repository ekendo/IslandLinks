<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.'); 
/**
*
* @version $Id: intershipper.php,v 1.6 2005/11/17 09:31:13 codename-matrix Exp $
* @package VirtueMart
* @subpackage shipping
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
class intershipper {
	var $classname = "intershipper";
	
	function list_rates( &$d ) {	
	  global $weight_total, $CURRENCY_DISPLAY;
	  $d["ship_to_info_id"] = mosGetParam( $_REQUEST, "ship_to_info_id" );
      /** Read current Configuration ***/
      require_once(CLASSPATH ."shipping/".$this->classname.".cfg.php");
	  
	  $dbv = new ps_DB;
	  $q  = "SELECT * from #__{vm}_vendor, #__{vm}_country WHERE vendor_id='" . $_SESSION["ps_vendor_id"] . "' AND (vendor_country=country_2_code OR vendor_country=country_3_code)";
	  $dbv->query($q);
	  $dbv->next_record();
	  
	  $dbst = new ps_DB;
	  $q  = "SELECT * from #__{vm}_user_info, #__{vm}_country WHERE user_info_id='" . $d["ship_to_info_id"]."' AND ( country=country_2_code OR country=country_3_code)";
	  $dbst->query($q);
	  $dbst->next_record();
	  
	  $carrier_arr = Array();
	  $i = 0;
	  if(CARRIER1_NAME!="") {
		$carrier_arr[$i]["name"] = CARRIER1_NAME;
		$carrier_arr[$i]["invoice"] = CARRIER1_INVOICE;
		$carrier_arr[$i]["account"] = CARRIER1_ACCOUNT;
		$i++;
	  }
	  if(CARRIER2_NAME!="") {
		$carrier_arr[$i]["name"] = CARRIER2_NAME;
		$carrier_arr[$i]["invoice"] = CARRIER2_INVOICE;
		$carrier_arr[$i]["account"] = CARRIER2_ACCOUNT;
		$i++;
	  }
	  if(CARRIER3_NAME!="") {
		$carrier_arr[$i]["name"] = CARRIER3_NAME;
		$carrier_arr[$i]["invoice"] = CARRIER3_INVOICE;
		$carrier_arr[$i]["account"] = CARRIER3_ACCOUNT;
		$i++;
	  }
	  if(CARRIER4_NAME!="") {
		$carrier_arr[$i]["name"] = CARRIER4_NAME;
		$carrier_arr[$i]["invoice"] = CARRIER4_INVOICE;
		$carrier_arr[$i]["account"] = CARRIER4_ACCOUNT;
		$i++;
	  }
	  if(CARRIER5_NAME!="") {
		$carrier_arr[$i]["name"] = CARRIER5_NAME;
		$carrier_arr[$i]["invoice"] = CARRIER5_INVOICE;
		$carrier_arr[$i]["account"] = CARRIER5_ACCOUNT;
		$i++;
	  }
	  $i = 0;
	  $class_arr = Array();
	  if(SERVICE_CLASS1 != "") {
		$class_arr[$i] = SERVICE_CLASS1;
		$i++;
	  }
	  if(SERVICE_CLASS2 != "") {
		$class_arr[$i] = SERVICE_CLASS2;
		$i++;
	  }
	  if(SERVICE_CLASS3 != "") {
		$class_arr[$i] = SERVICE_CLASS3;
		$i++;
	  }
	  if(SERVICE_CLASS4 != "") {
		$class_arr[$i] = SERVICE_CLASS4;
		$i++;
	  }
	  //Set your username and password.
	  $username = IS_USERNAME;
	  $password = IS_PASSWORD;
	  
	  // Build the query string to be sent to the IS server.
	  //http://intershipper.com/Shipping/Intershipper/Website/MainPage.jsp?Page=Integrate
	  // for additional information
	  // for additional information
	  
	  $url = 'www.intershipper.com';
	  $uri = '/Interface/Intershipper/XML/v2.0/HTTP.jsp?'.
		  'Username=' . $username . 
		  '&Password=' . $password . 
		  '&Version=' . '2.0.0.0' .
		  '&ShipmentID=' . '1234' . 
		  '&QueryID=' . '23456' . 
		  '&TotalCarriers=' . count( $carrier_arr );
		  $i = 1;
		  foreach( $carrier_arr as $carrier ) {
			$uri .= "&CarrierCode$i=" . $carrier["name"].
					"&CarrierInvoiced$i=" . $carrier["invoice"] .
					"&CarrierAccount$i=" . $carrier["account"];
			$i++;
		  }
		  $uri .= '&TotalClasses=' . count( $class_arr );
		  $i = 1;
		  foreach( $class_arr as $k => $v ) {
			$uri .= "&ClassCode$i=" . $v;
			$i++;
		  }
		  $uri .= '&DeliveryType=' . 'COM' . 
		  '&ShipMethod=' . 'DRP' . 
		  '&OriginationName=' . urlencode($dbv->f("contact_first_name").'%20'.$dbv->f("contact_last_name")) . 
		  '&OriginationAddress1=' . urlencode($dbv->f("vendor_address_1")) . 
		  '&OriginationCity=' . urlencode($dbv->f("vendor_city")) . 
		  '&OriginationState=' . urlencode($dbv->f("vendor_state")) . 
		  '&OriginationPostal=' . $dbv->f("vendor_zip") . 
		  '&OriginationCountry=' . $dbv->f("country_2_code") . 
		  '&DestinationName=' . urlencode($dbst->f("first_name").'%20'.$dbst->f("last_name")) . 
		  '&DestinationAddress1=' . urlencode($dbst->f("address_1")) . 
		  '&DestinationCity=' . urlencode($dbst->f("city")) . 
		  '&DestinationState=' . urlencode($dbst->f("state")) . 
		  '&DestinationPostal=' . $dbst->f("zip") . 
		  '&DestinationCountry=' . $dbst->f("country_2_code") . 
		  '&Currency=' . $_SESSION['vendor_currency'] . 
		  '&TotalPackages=' . '1' . 
		  '&BoxID1=' . '1' . 
		  '&Weight1=' . $weight_total . 
		  '&WeightUnit1=' . WEIGHT_UOM . 
		  '&Length1=' . '10' . 
		  '&Width1=' . '10' . 
		  '&Height1=' . '10' . 
		  '&DimensionalUnit1=' . 'IN' . 
		  '&Packaging1=' . 'BOX' . 
		  '&Contents1=' . 'OTR' . 
		  '&Cod1=' . '0' . 
		  '&Insurance1=' . '0' . 
		  '&TotalOptions=' . '1' . 
		  '&OptionCode1=' . 'SDD';
		  
	  //Define some global vars for later use
	  
	  $state = array();
	  global $state;
	  $quote = array();
	  global $quote;
	  $quotes = array();
	  global $quotes;
	  global $package_id;
	  global $boxID;
	  
	  // funtion to handle the start elements for the XML data
	  function startElement(&$Parser, &$Elem, $Attr) {
		  global $state;
		  if(!is_array( $state ) )
			$state = array();
		  array_push ($state, $Elem);
		  $states = join (' ',$state);
		  //check what state we are in
		  if ($states == "SHIPMENT PACKAGE") {
			  global $package_id;
			  $package_id = $Attr['ID'];
		  }
		  //check what state we are in 
		  elseif ($states == "SHIPMENT PACKAGE QUOTE") {
			  global $package_id;
			  global $quote;
			  $quote = array ( 'package_id' => $package_id, 'id' => $Attr['ID']);
		  }
	  }
	  
	  //funtion to parse the XML data. The routine does a series of conditional
	  //checks on the data to determine where in the XML stack "we" are.
	  //
	  function characterData($Parser, $Line) {  			
		  global $state;
		  $states = join (' ',$state);	
		  if ($states == "SHIPPMENT ERROR") {
			  $error = $Line;
		  } 
		  elseif ($states == "SHIPMENT PACKAGE BOXID") {
			  global $boxID;
			  $boxID = $Line;
		  } 
		  elseif ($states == "SHIPMENT PACKAGE QUOTE CARRIER NAME") {
			  global $quote;
			  $quote["carrier_name"] = $Line;
		  } 
		  elseif ($states == "SHIPMENT PACKAGE QUOTE CARRIER CODE") {
			  global $quote;
			  $quote["carrier_code"] = $Line;
		  } 
		  elseif ($states == "SHIPMENT PACKAGE QUOTE CLASS NAME") {
			  global $quote;
			  $quote["class_name"] = $Line;
		  } 
		  elseif ($states == "SHIPMENT PACKAGE QUOTE CLASS CODE") {
			  global $quote;
			  $quote["class_code"] = $Line;
		  } 
		  elseif ($states == "SHIPMENT PACKAGE QUOTE SERVICE NAME") {
			  global $quote;
			  $quote["service_name"] = $Line;
		  } 
		  elseif ($states == "SHIPMENT PACKAGE QUOTE SERVICE CODE") {
			  global $quote;
			  $quote["service_code"] = $Line;
		  } 
		  elseif ($states == "SHIPMENT PACKAGE QUOTE RATE AMOUNT") {
			  global $quote;
			  $quote['amount'] = $Line;
		  }
	  }
	  
	  // this function handles the end elements.
	  // once encountered it sticks the quote into the hash $quotes
	  // for easy access later
	  function endElement($Parser, $Elem) {
		  global $state;	
		  $states = join (' ',$state);	
		  if ($states == "SHIPMENT PACKAGE QUOTE") {
			  global $quote;
			  global $boxID;
			  global $quotes;
			  unset ($quote['id']);
			  unset ($quote['package_id']);
			  // the $key is a combo of the carrier_code and service_code
			  // this is the logical way to key each quote returned 
			  $key = $quote['carrier_code'] . ' ' . $quote['service_code'];
			  $quotes[$boxID][$key] = $quote;
		  }
		  array_pop($state);
	  }
	  
	  
	  //Send the socket request with the uri/url
	  $fp = fsockopen ("www.intershipper.com", 80, $errno, $errstr, 30);
	  if (!$fp) {
		  $html = "Error: $errstr ($errno)<br>\n";
		  $error = true;
	  } 
	  else {
		  //echo "<a href=\"http://".$url.$uri."\">URL</a>";
		  $depth = array();
		  fputs($fp, "GET $uri HTTP/1.0\r\nHost: $url\r\n\r\n");
		  //define the XML parsing routines/functions to call
		  //based on the handler state
		  $xml_parser = xml_parser_create();
		  xml_set_element_handler($xml_parser, "startElement", "endElement");
		  xml_set_character_data_handler($xml_parser, "characterData");	
		  //now lets roll through the data
		  $error = false;
		  while ($data = fread($fp, 8192)) {
			  
			  $newdata = $data;
			  /*fsockopen returns more infomation than we'd like. here we 
				  remove the excess data. */
			  $newdata = preg_replace('/\r\n\r\n/', "", $newdata);
			  $newdata = preg_replace('/HTTP.*\r\n/', "", $newdata);
			  $newdata = preg_replace('/Date.*\r\n/', "", $newdata);
			  $newdata = preg_replace('/Server.*\r\n/', "", $newdata);
			  $newdata = preg_replace('/Via.*/', "", $newdata);
			  $newdata = preg_replace('/Con.*/', "", $newdata);
			  $newdata = preg_replace('/Set.*/', "", $newdata);
			  $newdata = preg_replace('/\r/', "", $newdata);
			  $newdata = preg_replace('/\n/', "", $newdata);
			  if(strstr($newdata, "error")) {
				$html = $newdata;
				$error = true;
			  }
			  /* if we properl cleaned up the XML stream/data we can now hand it off 
			  to an XML parser without error */
			  if (!xml_parse($xml_parser, $newdata, feof($fp))) {
				  die(sprintf("XML error: %s at line %d",
				  xml_error_string(xml_get_error_code($xml_parser)),
				  xml_get_current_line_number($xml_parser)));
			  }
		  }
		  //clean up the parser object
		  xml_parser_free($xml_parser);
	  }
	  
	  /* Here we build a drop down menu list (as an example).
	  print_r $quotes
	  can help you debug or use the $quotes hash we built above.
	  a variety of info is included but mostly we probably want amount, carrier_name,
	  service_name. */
	  $shipping_rate_id = urlencode(mosGetParam( $_REQUEST, "shipping_rate_id" ));
	  if( !$error ) {
	  
		if ( $_SESSION['auth']['show_price_including_tax'] != 1 ) {
		  $taxrate = 1;
		}
		else {
		  $taxrate = $this->get_tax_rate() + 1;
		}
		
		while(list($quotedata, $boxID)=each($quotes)){
		  while(list($key, $bar)=each($boxID)){
			  if(isset($carrier)) {
				if( $carrier!=$boxID[$key]['carrier_name'])
				  echo "<br /><strong>".$boxID[$key]['carrier_name']."</strong><br />";
				  $carrier = $boxID[$key]['carrier_name'];
			  }
			  else {
				  echo "<br /><strong>".$boxID[$key]['carrier_name']."</strong><br />";
				  $carrier = $boxID[$key]['carrier_name'];
			  }
			  echo ($carrier==$boxID[$key]['carrier_name']) ? "" : $carrier;
			  $boxID[$key]['amount'] = ($boxID[$key]['amount'] / 100) * $taxrate;
			  $boxID[$key]['amount']= number_format($boxID[$key]['amount'], 2, '.', ' ');
			  $value = urlencode($this->classname."|".$key."|".$boxID[$key]['service_name']."|".$boxID[$key]['amount']);
			  $checked = ($shipping_rate_id == $value) ? "checked=\"checked\"" : "";
			  print "\n<input type=\"radio\" name=\"shipping_rate_id\" $checked value=\"$value\" />\n";
			  
			  $_SESSION[urlencode($this->classname."|".$key."|".$boxID[$key]['service_name']."|".$boxID[$key]['amount'])] = 1;
			  
			  print $boxID[$key]['service_name']." ";
			  print "<strong>".$CURRENCY_DISPLAY->getFullValue($boxID[$key]['amount'])."</strong>";
			  print "<br />";
		  }
		}
	  }
	  else {
		// Switch to StandardShipping on Error !!!
		$vmLogger->err( $html );
		require_once( CLASSPATH . 'shipping/standard_shipping.php' );
		$shipping =& new standard_shipping();
		$shipping->list_rates( $d );
		return;
	  }
	}
	
	function get_rate( &$d ) {	
	  $shipping_rate_id = $_REQUEST["shipping_rate_id"];
	  $is_arr = explode("|", urldecode(urldecode($shipping_rate_id)) );
	  $order_shipping = $is_arr[3];
	  
	  return $order_shipping;
	}

	
  function get_tax_rate() {
	
    /** Read current Configuration ***/
	require_once(CLASSPATH ."shipping/".$this->classname.".cfg.php");
	
	if( intval(IS_TAX_CLASS)== 0 )
	  return( 0 );
	else {
	  require_once( CLASSPATH. "ps_tax.php" );
	  $tax_rate = ps_tax::get_taxrate_by_id( intval(IS_TAX_CLASS) );
	  return $tax_rate;
	}
  }
  
	/**
    * Validate this Shipping method by checking if the SESSION contains the key
    * @returns boolean False when the Shipping method is not in the SESSION
    */
	function validate( $d ) {
	  $shipping_rate_id = $_REQUEST["shipping_rate_id"];
	  
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
    
      global $VM_LANG;
      /** Read current Configuration ***/
      require_once(CLASSPATH ."shipping/".$this->classname.".cfg.php");
	  
    ?>
      <table border="0" cellspacing="0" cellpadding="0" width="100%">
        <tr>
		  <td width="20%"><strong>Intershipper Username</strong>:</td>
		  <td colspan="3" width="80%">
			  <input type="text" name="IS_USERNAME" class="inputbox" value="<? echo IS_USERNAME ?>" />
			<?php echo mm_ToolTip('The InterShipper Username') ?>
		  </td>
		</tr>
		<tr>
			<td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_INTERSHIPPER_PASSWORD ?></strong>:
			</td>
			<td colspan="3">
				<input type="text" name="IS_PASSWORD" class="inputbox" value="<? echo IS_PASSWORD ?>" />
			  <?php echo mm_ToolTip($VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_INTERSHIPPER_PASSWORD_EXPLAIN) ?>
			</td>
		</tr>
		<tr>
			<td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_INTERSHIPPER_EMAIL ?></strong>:
			</td>
			<td colspan="3">
				<input type="text" name="IS_EMAIL" class="inputbox" value="<? echo IS_EMAIL ?>" />
				<?php echo mm_ToolTip($VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_INTERSHIPPER_EMAIL_EXPLAIN) ?>
			</td>
		</tr>
	  <tr>
		<td><strong><?php echo $VM_LANG->_PHPSHOP_UPS_TAX_CLASS ?></strong></td>
		<td>
		  <?php
		  require_once(CLASSPATH.'ps_tax.php');
		  ps_tax::list_tax_value("IS_TAX_CLASS", IS_TAX_CLASS) ?>
		</td>
		<td><?php echo mm_ToolTip($VM_LANG->_PHPSHOP_UPS_TAX_CLASS_TOOLTIP) ?><td>
	  </tr>	
		<tr>
		  <td colspan="4"><hr /></td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td style="text-indent:20px;font-weight:bold;">Name
		  <?php echo mm_ToolTip("Specify the carriers which shall return their shipping rates."); ?></td>
		  <td style="text-indent:20px;font-weight:bold;">Invoice
		  <?php echo mm_ToolTip("Specifies whether or not you are invoiced directly from the carrier"); ?></td>
		  <td style="text-indent:20px;font-weight:bold;">Account No. (optional)
		  <?php echo mm_ToolTip("Your carrier account number -> to take advantage of any special discounts or offers"); ?></td>
		</tr>
		<tr>
		  <td style="float:right;">Carrier 1:</td>
		  <td><select class="inputbox" name="carrier1_name">
			<option <?php if(CARRIER1_NAME=="UPS") echo "selected=\"selected\"" ?> value="UPS">UPS</option>
			<option <?php if(CARRIER1_NAME=="FDX") echo "selected=\"selected\"" ?> value="FDX">FedEx</option>
			<option <?php if(CARRIER1_NAME=="DHL") echo "selected=\"selected\"" ?> value="DHL">DHL Worldwide Express</option>
			<option <?php if(CARRIER1_NAME=="USP") echo "selected=\"selected\"" ?> value="USP">US Postal</option>
			<option <?php if(CARRIER1_NAME=="ARB") echo "selected=\"selected\"" ?> value="ARB">AirBorne</option>
			</select>
		  </td>
		  <td>
		  <input class="inputbox" <?php if(CARRIER1_INVOICE=="0") echo "checked=\"checked\"" ?> type="radio" name="carrier1_invoice" value="0" />No
		  <input class="inputbox" <?php if(CARRIER1_INVOICE=="1") echo "checked=\"checked\"" ?> type="radio" name="carrier1_invoice" value="1" />Yes
		  </td>
		  <td>
			<input class="inputbox" type="text" name="carrier1_account" value="<?php echo CARRIER1_ACCOUNT ?>" />
		  </td>
		</tr>
		<tr>
		  <td style="float:right;">Carrier 2:</td>
		  <td>
			<select class="inputbox" name="carrier2_name">
			<option value="">none</option>
			<option <?php if(CARRIER2_NAME=="UPS") echo "selected=\"selected\"" ?> value="UPS">UPS</option>
			<option <?php if(CARRIER2_NAME=="FDX") echo "selected=\"selected\"" ?> value="FDX">FedEx</option>
			<option <?php if(CARRIER2_NAME=="DHL") echo "selected=\"selected\"" ?> value="DHL">DHL Worldwide Express</option>
			<option <?php if(CARRIER2_NAME=="USP") echo "selected=\"selected\"" ?> value="USP">US Postal</option>
			<option <?php if(CARRIER2_NAME=="ARB") echo "selected=\"selected\"" ?> value="ARB">AirBorne</option>
			</select>
		  </td>
		  <td>
		  <input class="inputbox" <?php if(CARRIER2_INVOICE=="0") echo "checked=\"checked\"" ?> type="radio" name="carrier2_invoice" value="0" />No
		  <input class="inputbox" <?php if(CARRIER2_INVOICE=="1") echo "checked=\"checked\"" ?> type="radio" name="carrier2_invoice" value="1" />Yes
		  </td>
		  <td>
			<input class="inputbox" type="text" name="carrier2_account" value="<?php echo CARRIER2_ACCOUNT ?>" />
		  </td>
		</tr>
		<tr>
		  <td style="float:right;">Carrier 3:</td>
		  <td>
			<select class="inputbox" name="carrier3_name">
			<option value="">none</option>
			<option <?php if(CARRIER3_NAME=="UPS") echo "selected=\"selected\"" ?> value="UPS">UPS</option>
			<option <?php if(CARRIER3_NAME=="FDX") echo "selected=\"selected\"" ?> value="FDX">FedEx</option>
			<option <?php if(CARRIER3_NAME=="DHL") echo "selected=\"selected\"" ?> value="DHL">DHL Worldwide Express</option>
			<option <?php if(CARRIER3_NAME=="USP") echo "selected=\"selected\"" ?> value="USP">US Postal</option>
			<option <?php if(CARRIER3_NAME=="ARB") echo "selected=\"selected\"" ?> value="ARB">AirBorne</option>
			</select>
		  </td>
		  <td>
		  <input class="inputbox" <?php if(CARRIER3_INVOICE=="0") echo "checked=\"checked\"" ?> type="radio" name="carrier3_invoice" value="0" />No
		  <input class="inputbox" <?php if(CARRIER3_INVOICE=="1") echo "checked=\"checked\"" ?> type="radio" name="carrier3_invoice" value="1" />Yes
		  </td>
		  <td>
			<input class="inputbox" type="text" name="carrier3_account" value="<?php echo CARRIER3_ACCOUNT ?>" />
		  </td>
		</tr>
		<tr>
		  <td style="float:right;">Carrier 4:</td>
		  <td>
			<select class="inputbox" name="carrier4_name">
			<option value="">none</option>
			<option <?php if(CARRIER4_NAME=="UPS") echo "selected=\"selected\"" ?> value="UPS">UPS</option>
			<option <?php if(CARRIER4_NAME=="FDX") echo "selected=\"selected\"" ?> value="FDX">FedEx</option>
			<option <?php if(CARRIER4_NAME=="DHL") echo "selected=\"selected\"" ?> value="DHL">DHL Worldwide Express</option>
			<option <?php if(CARRIER4_NAME=="USP") echo "selected=\"selected\"" ?> value="USP">US Postal</option>
			<option <?php if(CARRIER4_NAME=="ARB") echo "selected=\"selected\"" ?> value="ARB">AirBorne</option>
			</select>
		  </td>
		  <td>
		  <input class="inputbox" <?php if(CARRIER4_INVOICE=="0") echo "checked=\"checked\"" ?> type="radio" name="carrier4_invoice" value="0" />No
		  <input class="inputbox" <?php if(CARRIER4_INVOICE=="1") echo "checked=\"checked\"" ?> type="radio" name="carrier4_invoice" value="1" />Yes
		  </td>
		  <td>
			<input class="inputbox" type="text" name="carrier4_account" value="<?php echo CARRIER4_ACCOUNT ?>" />
		  </td>
		</tr>
		<tr>
		  <td style="float:right;">Carrier 5:</td>
		  <td>
			<select class="inputbox" name="carrier5_name">
			<option value="">none</option>
			<option <?php if(CARRIER5_NAME=="UPS") echo "selected=\"selected\"" ?> value="UPS">UPS</option>
			<option <?php if(CARRIER5_NAME=="FDX") echo "selected=\"selected\"" ?> value="FDX">FedEx</option>
			<option <?php if(CARRIER5_NAME=="DHL") echo "selected=\"selected\"" ?> value="DHL">DHL Worldwide Express</option>
			<option <?php if(CARRIER5_NAME=="USP") echo "selected=\"selected\"" ?> value="USP">US Postal</option>
			<option <?php if(CARRIER5_NAME=="ARB") echo "selected=\"selected\"" ?> value="ARB">AirBorne</option>
			</select>
		  </td>
		  <td>
		  <input class="inputbox" <?php if(CARRIER5_INVOICE=="0") echo "checked=\"checked\"" ?> type="radio" name="carrier5_invoice" value="0" />No
		  <input class="inputbox" <?php if(CARRIER5_INVOICE=="1") echo "checked=\"checked\"" ?> type="radio" name="carrier5_invoice" value="1" />Yes
		  </td>
		  <td>
			<input class="inputbox" type="text" name="carrier5_account" value="<?php echo CARRIER5_ACCOUNT ?>" />
		  </td>
		</tr>
		<tr>
		  <td colspan="4"><hr /></td>
		</tr>
		<tr>
		<td style="float:right;font-weight:bold;">Classes of Service:</td>
		<td colspan="3">
		  <input type="checkbox" <?php if(SERVICE_CLASS1=="1DY") echo "checked=\"checked\"" ?> name="service_class1" value="1DY" />1st Day<br />
		  <input type="checkbox" <?php if(SERVICE_CLASS2=="2DY") echo "checked=\"checked\"" ?> name="service_class2" value="2DY" />2nd Day<br />
		  <input type="checkbox" <?php if(SERVICE_CLASS3=="3DY") echo "checked=\"checked\"" ?> name="service_class3" value="3DY" />3rd Day<br />
		  <input type="checkbox" <?php if(SERVICE_CLASS4=="GND") echo "checked=\"checked\"" ?> name="service_class4" value="GND" />Ground <br />
		</td>
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
      
      $my_config_array = array("IS_USERNAME" => $d['IS_USERNAME'],
							  "IS_PASSWORD" => $d['IS_PASSWORD'],
							  "IS_EMAIL" => $d['IS_EMAIL'],
							  "IS_TAX_CLASS" => $d['IS_TAX_CLASS'],
							  "CARRIER1_NAME" => @$d['carrier1_name'],
							  "CARRIER1_INVOICE" => @$d['carrier1_invoice'],
							  "CARRIER1_ACCOUNT" => @$d['carrier1_account'],
							  "CARRIER2_NAME" => @$d['carrier2_name'],
							  "CARRIER2_INVOICE" => @$d['carrier2_invoice'],
							  "CARRIER2_ACCOUNT" => @$d['carrier2_account'],
							  "CARRIER3_NAME" => @$d['carrier3_name'],
							  "CARRIER3_INVOICE" => @$d['carrier3_invoice'],
							  "CARRIER3_ACCOUNT" => @$d['carrier3_account'],
							  "CARRIER4_NAME" => @$d['carrier4_name'],
							  "CARRIER4_INVOICE" => @$d['carrier4_invoice'],
							  "CARRIER4_ACCOUNT" => @$d['carrier4_account'],
							  "CARRIER5_NAME" => @$d['carrier5_name'],
							  "CARRIER5_INVOICE" => @$d['carrier5_invoice'],
							  "CARRIER5_ACCOUNT" => @$d['carrier5_account'],
							  "SERVICE_CLASS1" => @$d['service_class1'],
							  "SERVICE_CLASS2" => @$d['service_class2'],
							  "SERVICE_CLASS3" => @$d['service_class3'],
							  "SERVICE_CLASS4" => @$d['service_class4'],
							  
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
