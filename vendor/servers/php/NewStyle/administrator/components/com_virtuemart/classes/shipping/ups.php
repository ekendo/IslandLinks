<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.'); 
/**
* MODIFIED BY DENEB
* @version $Id: ups.php,v 1.5.2.1 2006/05/06 10:05:27 soeren_nb Exp $
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

/**
* This is the Shipping class for 
* using a part of the UPS Online(R) Tools:
* = Rates and Service Selection =
*
* UPS OnLine(R) is a registered trademark of United Parcel Service of America. 
*
*/
class ups {

  var $classname = "ups";
  
  function list_rates( &$d ) {
	global $vendor_country_2_code, $vendor_currency, $vmLogger; 
	global $VM_LANG, $CURRENCY_DISPLAY, $mosConfig_absolute_path;
	$db =& new ps_DB;
	$dbv =& new ps_DB;
	
	$cart = $_SESSION['cart'];
	
	/** Read current Configuration ***/
	require_once(CLASSPATH ."shipping/".$this->classname.".cfg.php");

    $q  = "SELECT * FROM #__{vm}_user_info, #__{vm}_country WHERE user_info_id='" . $d["ship_to_info_id"]."' AND ( country=country_2_code OR country=country_3_code)";
	$db->query($q);
	
	$q  = "SELECT * FROM #__{vm}_vendor WHERE vendor_id='".$_SESSION['ps_vendor_id']."'";
	$dbv->query($q);
	$dbv->next_record();  
	
	$order_weight = $d['weight'];  

	if($order_weight > 0) {
	  // BEGIN CUSTOM CODE - added by deneb
	  if( $order_weight < 1 ) {
	  	$order_weight = 1;
	  }
	  // END CUSTOM CODE - added by deneb
	  
	  if( $order_weight > 150.00 ) {
	  	$order_weight = 150.00;
	  }
	  		
	  //Access code for online tools at ups.com
	  $ups_access_code = UPS_ACCESS_CODE;
		
	  //Username from registering for online tools at ups.com
	  $ups_user_id = UPS_USER_ID;
		
	  //Password from registering for online tools at ups.com
	  $ups_user_password = UPS_PASSWORD;
		
	  //Title for your request
	  $request_title = "Shipping Estimate";
		
	  //The zip that you are shipping from
	  
	  // BEGIN CUSTOM CODE - added by deneb
	  // Add ability to override vendor zip code as source ship from...
	  if (Override_Source_Zip != "" OR Override_Source_Zip != NULL) {
	  	$source_zip = Override_Source_Zip;
	  }
	  else {
	    $source_zip = $dbv->f("vendor_zip"); //original code
	  }
	  // END CUSTOM CODE - added by deneb

	  //The zip that you are shipping to
	  $dest_country = $db->f("country_2_code");
	  $dest_zip = $db->f("zip");
	   
	  //LBS  = Pounds
	  //KGS  = Kilograms
	  $weight_measure = (WEIGHT_UOM == 'KG') ? "KGS" : "LBS";
  
	  // The XML that will be posted to UPS
	  $xmlPost  = "<?xml version=\"1.0\"?>";
	  $xmlPost .= "<AccessRequest xml:lang=\"en-US\">";
	  $xmlPost .= " <AccessLicenseNumber>".$ups_access_code."</AccessLicenseNumber>";
	  $xmlPost .= " <UserId>".$ups_user_id."</UserId>";
	  $xmlPost .= " <Password>".$ups_user_password."</Password>";
	  $xmlPost .= "</AccessRequest>";
	  $xmlPost .= "<?xml version=\"1.0\"?>";
	  $xmlPost .= "<RatingServiceSelectionRequest xml:lang=\"en-US\">";
	  $xmlPost .= " <Request>";
	  $xmlPost .= "  <TransactionReference>";
	  $xmlPost .= "  <CustomerContext>".$request_title."</CustomerContext>";
	  $xmlPost .= "  <XpciVersion>1.0001</XpciVersion>";
	  $xmlPost .= "  </TransactionReference>";
	  $xmlPost .= "  <RequestAction>rate</RequestAction>";
	  $xmlPost .= "  <RequestOption>shop</RequestOption>";
	  $xmlPost .= " </Request>";
	  $xmlPost .= " <PickupType>";
	  $xmlPost .= "  <Code>".UPS_PICKUP_TYPE."</Code>";
	  $xmlPost .= " </PickupType>";
	  $xmlPost .= " <Shipment>";
	  $xmlPost .= "  <Shipper>";
	  $xmlPost .= "   <Address>";
	  $xmlPost .= "    <PostalCode>".$source_zip."</PostalCode>";
	  $xmlPost .= "    <CountryCode>$vendor_country_2_code</CountryCode>";
	  $xmlPost .= "   </Address>";
	  $xmlPost .= "  </Shipper>";
	  $xmlPost .= "  <ShipTo>";
	  $xmlPost .= "   <Address>";
	  $xmlPost .= "    <PostalCode>".$dest_zip."</PostalCode>";
	  $xmlPost .= "    <CountryCode>$dest_country</CountryCode>";
	  if( UPS_RESIDENTIAL=="yes" )
		$xmlPost .= "    <ResidentialAddressIndicator/>";
	  $xmlPost .= "   </Address>";
	  $xmlPost .= "  </ShipTo>";
	  $xmlPost .= "  <ShipFrom>";
	  $xmlPost .= "   <Address>";
	  $xmlPost .= "    <PostalCode>".$source_zip."</PostalCode>";
	  $xmlPost .= "    <CountryCode>$vendor_country_2_code</CountryCode>";
	  $xmlPost .= "   </Address>";
	  $xmlPost .= "  </ShipFrom>";
	  
	  // Service is only required, if the Tag "RequestOption" contains the value "rate"
	  // We don't want a specific servive, but ALL Rates
	  //$xmlPost .= "  <Service>";
	  //$xmlPost .= "   <Code>".$shipping_type."</Code>";
	  //$xmlPost .= "  </Service>";
	  
	  $xmlPost .= "  <Package>";
	  $xmlPost .= "   <PackagingType>";
	  $xmlPost .= "    <Code>".UPS_PACKAGE_TYPE."</Code>";
	  $xmlPost .= "   </PackagingType>";
	  $xmlPost .= "   <PackageWeight>";
	  $xmlPost .= "    <UnitOfMeasurement>";
	  $xmlPost .= "     <Code>".$weight_measure."</Code>";
	  $xmlPost .= "    </UnitOfMeasurement>";
	  $xmlPost .= "    <Weight>".$order_weight."</Weight>"; //modded by deneb
	  $xmlPost .= "   </PackageWeight>";
	  $xmlPost .= "  </Package>";
	  $xmlPost .= " </Shipment>";
	  $xmlPost .= "</RatingServiceSelectionRequest>";
	   
	  // echo htmlentities( $xmlPost );
	  $host = "www.ups.com";
	  $path = "/ups.app/xml/Rate";
	  $port = 443;
	  $protocol = "https";

	  // Using cURL is Up-To-Date and easier!!
	  if( function_exists( "curl_init" )) {
		$CR = curl_init();
		curl_setopt($CR, CURLOPT_URL, $protocol."://".$host.$path);
		curl_setopt($CR, CURLOPT_POST, 1);
		curl_setopt($CR, CURLOPT_FAILONERROR, true); 
		curl_setopt($CR, CURLOPT_POSTFIELDS, $xmlPost);
		curl_setopt($CR, CURLOPT_RETURNTRANSFER, 1);
		 
		// No PEER certificate validation...as we don't have 
		// a certificate file for it to authenticate the host www.ups.com against!
		curl_setopt($CR, CURLOPT_SSL_VERIFYPEER, 0);
		//curl_setopt($CR, CURLOPT_SSLCERT , "/usr/locale/xxxx/clientcertificate.pem");
		
		$xmlResult = curl_exec( $CR );
		
		$error = curl_error( $CR );
		if( !empty( $error )) {
		  $vmLogger->err( curl_error( $CR ) );
		  $html = "<br/><span class=\"message\">".$VM_LANG->_PHPSHOP_INTERNAL_ERROR." UPS.com</span>";
		  $error = true;
		}
		else {
		  /* XML Parsing */
		  require_once( $mosConfig_absolute_path. '/includes/domit/xml_domit_lite_include.php' );
		  $xmlDoc =& new DOMIT_Lite_Document();
		  $xmlDoc->parseXML( $xmlResult, false, true );
		  
		  /* Let's check wether the response from UPS is Success or Failure ! */
		  if( strstr( $xmlResult, "Failure" ) ) {
			$error = true;
			$html = "<span class=\"message\">".$VM_LANG->_PHPSHOP_UPS_RESPONSE_ERROR."</span><br/>";
			$error_code = $xmlDoc->getElementsByTagName( "ErrorCode" );
			$error_code = $error_code->item(0);
			$error_code = $error_code->getText();
			$html .= $VM_LANG->_PHPSHOP_ERROR_CODE.": ".$error_code."<br/>";
			
			$error_desc = $xmlDoc->getElementsByTagName( "ErrorDescription" );
			$error_desc = $error_desc->item(0);
			$error_desc = $error_desc->getText();
			$html .= $VM_LANG->_PHPSHOP_ERROR_DESC.": ".$error_desc."<br/>";
			
		  }

		}
		curl_close( $CR );
	   
	  }
	  else {

		$protocol = "ssl";
		$fp = fsockopen("$protocol://".$host, $port, $errno, $errstr, $timeout = 60);
		if( !$fp ) {
		  $html = $VM_LANG->_PHPSHOP_INTERNAL_ERROR.": $errstr ($errno)";
		}
		else {
		  //send the server request
		  fputs($fp, "POST $path HTTP/1.1\r\n");
		  fputs($fp, "Host: $host\r\n");
		  fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
		  fputs($fp, "Content-length: ".strlen($xmlPost)."\r\n");
		  fputs($fp, "Connection: close\r\n\r\n");
		  fputs($fp, $xmlPost . "\r\n\r\n");
		   
		  $xmlResult = '';
		  while(!feof($fp)) {
			$xmlResult .= fgets($fp, 4096);
		  }
		  // Cut off the HTTP Header and get the Body
		  $xmlResult = trim(substr( $xmlResult, strpos( $xmlResult, "\r\n\r\n" )+2 ));
		  
		  if( stristr( $xmlResult, "Success" )) {		  
			/* XML Parsing */
			require_once( $mosConfig_absolute_path. '/includes/domit/xml_domit_lite_include.php' );
			$xmlDoc =& new DOMIT_Lite_Document();
			if( $xmlDoc->parseXML( $xmlResult, false, true ))
			  $error = false;
			else {
			  $error = true;
			  $html = "Error parsing the XML Response from UPS.com";
			}
		  }
		  else {
			$html = "Error processing the Request to UPS.com";
			$error = true;
		  }
		}
		
	  }
	  if( $error ) {
		// comment out, if you don't want the Errors to be shown!!
		//$vmLogger->err( $html );
		// Switch to StandardShipping on Error !!!
		require_once( CLASSPATH . 'shipping/standard_shipping.php' );
		$shipping =& new standard_shipping();
		$shipping->list_rates( $d );
		return;
	  }
	  // retrieve the list of all "RatedShipment" Elements
	  $rate_list =& $xmlDoc->getElementsByTagName( "RatedShipment" );
		// BEGIN CUSTOM CODE - added by deneb		 
		$allservicecodes = array("UPS_Next_Day_Air",
							  "UPS_2nd_Day_Air",
							  "UPS_Ground",
							  "UPS_Worldwide_Express_SM",
							  "UPS_Worldwide_Expedited_SM",
							  "UPS_Standard",
							  "UPS_3_Day_Select",
							  "UPS_Next_Day_Air_Saver",
							  "UPS_Next_Day_Air_Early_AM",
							  "UPS_Worldwide_Express_Plus_SM",
							  "UPS_2nd_Day_Air_AM",
							  "UPS_Express_Saver",
							  "na");
		$myservicecodes = array();
		foreach ($allservicecodes as $servicecode){
			if (constant($servicecode) != '' || constant($servicecode) != 0) {
				$myservicecodes[] = constant($servicecode);
			}
		}
		if ($neuspeed_ecu) {
			$myservicecodes = array(UPS_Next_Day_Air_Saver);
		}
	  $html = "";
	  if (DEBUG){
	  	echo "Cart Contents: ".$order_weight. " ".$weight_measure."<br><br>\n";
		echo "XML Post: <br>";
		echo "<textarea cols='80'>".$xmlPost."</textarea>";
		echo "<br>";
		echo "XML Result: <br>";
		echo "<textarea cols='80' rows='10'>".$xmlResult."</textarea>";
		echo "<br>";
	  }
		// END CUSTOM CODE - added by deneb	
		
	  // Loop through the rate List
	  for ($i = 0; $i < $rate_list->getLength(); $i++) {
		  $currNode =& $rate_list->item($i);
		  
		  if ( in_array($currNode->childNodes[0]->getText(),$myservicecodes) )  {
			  // Test for any shipper warnings first!
			  // if warning is present, we must use a different childNode numbering
			  $test_warning = $currNode->childNodes[1]->getText();
			  if( stristr( $test_warning, "LBS" ) || stristr( $test_warning, "KGS" )) {
				// Second Element: BillingWeight
				$shipment[$i]["BillingWeight"] = $test_warning;
				$warning = 0;
			  }
			  else {
				$shipment[$i]["RatedShipmentWarning"] = $currNode->childNodes[1]->getText();
				$warning = 1;
			  }
			  // First Element: Service Code
			  	$shipment[$i]["ServiceCode"] = $currNode->childNodes[0]->getText();
			  
			  if ($warning == 1) {
			  	// Second Element: BillingWeight
				$shipment[$i]["BillingWeight"] = $currNode->childNodes[2];
				
				// Third Element: TransportationCharges
				$shipment[$i]["TransportationCharges"] = $currNode->childNodes[3];
				$shipment[$i]["TransportationCharges"] = $shipment[$i]["TransportationCharges"]->getElementsByTagName("MonetaryValue");
				$shipment[$i]["TransportationCharges"] = $shipment[$i]["TransportationCharges"]->item(0);
				$shipment[$i]["TransportationCharges"] = $shipment[$i]["TransportationCharges"]->getText();
				
				// Fourth Element: ServiceOptionsCharges
				$shipment[$i]["ServiceOptionsCharges"] = $currNode->childNodes[4];
				
				// Fifth Element: TotalCharges
				$shipment[$i]["TotalCharges"] = $currNode->childNodes[5];
				
				// Sixth Element: GuarenteedDaysToDelivery
				$shipment[$i]["GuaranteedDaysToDelivery"] = $currNode->childNodes[6]->getText();
				
				// Seventh Element: ScheduledDeliveryTime
				$shipment[$i]["ScheduledDeliveryTime"] = $currNode->childNodes[7]->getText();
				
				// Eighth Element: RatedPackage
				$shipment[$i]["RatedPackage"] = $currNode->childNodes[8];
			  }
			  else {
			  
			    // Third Element: TransportationCharges
				$shipment[$i]["TransportationCharges"] = $currNode->childNodes[2];
				$shipment[$i]["TransportationCharges"] = $shipment[$i]["TransportationCharges"]->getElementsByTagName("MonetaryValue");
				$shipment[$i]["TransportationCharges"] = $shipment[$i]["TransportationCharges"]->item(0);
				$shipment[$i]["TransportationCharges"] = $shipment[$i]["TransportationCharges"]->getText();
				
				// Fourth Element: ServiceOptionsCharges
				$shipment[$i]["ServiceOptionsCharges"] = $currNode->childNodes[3];
				
				// Fifth Element: TotalCharges
				$shipment[$i]["TotalCharges"] = $currNode->childNodes[4];
				
				// Sixth Element: GuarenteedDaysToDelivery
				$shipment[$i]["GuaranteedDaysToDelivery"] = $currNode->childNodes[5]->getText();
				
				// Seventh Element: ScheduledDeliveryTime
				$shipment[$i]["ScheduledDeliveryTime"] = $currNode->childNodes[6]->getText();
				
				// Eighth Element: RatedPackage
				$shipment[$i]["RatedPackage"] = $currNode->childNodes[7];
			  }
			  
			  // map ServiceCode to ServiceName
			  switch( $shipment[$i]["ServiceCode"] ) {
	
				  case "01": $shipment[$i]["ServiceName"] = "UPS Next Day Air"; break;
				  case "02": $shipment[$i]["ServiceName"] = "UPS 2nd Day Air"; break;
				  case "03": $shipment[$i]["ServiceName"] = "UPS Ground"; break;
				  case "07": $shipment[$i]["ServiceName"] = "UPS Worldwide Express SM"; break;
				  case "08": $shipment[$i]["ServiceName"] = "UPS Worldwide Expedited SM"; break;
				  case "11": $shipment[$i]["ServiceName"] = "UPS Standard"; break;
				  case "12": $shipment[$i]["ServiceName"] = "UPS 3 Day Select"; break;
				  case "13": $shipment[$i]["ServiceName"] = "UPS Next Day Air Saver"; break;
				  case "14": $shipment[$i]["ServiceName"] = "UPS Next Day Air Early A.M."; break;
				  case "54": $shipment[$i]["ServiceName"] = "UPS Worldwide Express Plus SM"; break;
				  case "59": $shipment[$i]["ServiceName"] = "UPS 2nd Day Air A.M."; break;
				  case "64": $shipment[$i]["ServiceName"] = "n/a"; break;
				  case "65": $shipment[$i]["ServiceName"] = "UPS Express Saver"; break;
				  
			  }
			  unset( $currNode );
		  }
	  }
	  // BEGIN CUSTOM CODE - added by deneb
	  if (!$shipment ) {
		  //$vmLogger->err( "Error processing the Request to UPS.com" );
		  /*$vmLogger->err( "We could not find a UPS shipping rate. 
						Please make sure you have entered a valid shipping address. 
						Or choose a rate below." );
		  // Switch to StandardShipping on Error !!!
		  require_once( CLASSPATH . 'shipping/standard_shipping.php' );
		  $shipping =& new standard_shipping();
		  $shipping->list_rates( $d );*/
		  return;
	  } 
	  // END CUSTOM CODE - added by deneb
	  
	  // UPS returns Charges in USD ONLY. 
	  // So we have to convert from USD to Vendor Currency if necessary
	  if( $_SESSION['vendor_currency'] != "USD" ) {
		
		require_once( CLASSPATH. "currency_convert.php" );
		$convert = true;
	  }
	  else
		$convert = false;
		
	  if ( $_SESSION['auth']['show_price_including_tax'] != 1 ) {
		$taxrate = 1;
	  }
	  else {
		$taxrate = $this->get_tax_rate() + 1;
	  }
	  
	  foreach( $shipment as $key => $value ) {
		// BEGIN CUSTOM CODE - added by deneb
		//Get the Fuel SurCharge rate, defined in config.
		$fsc = $value['ServiceName']."_FSC";
		$fsc = str_replace(" ","_",str_replace(".","",str_replace("/","",$fsc)));
		$fsc = constant($fsc);
		if( $fsc == 0 )
	  		$fsc_rate = 1;
		else {
	  		$fsc_rate = $fsc / 100;
	  		$fsc_rate = $fsc_rate + 1;
		}
		// END CUSTOM CODE - added by deneb
		
		  if( $convert ) {
			$tmp = convertECB( $value['TransportationCharges'], "USD", $vendor_currency );
			
			// tmp is empty when the Vendor Currency could not be converted!!!!
			if( !empty( $tmp )) {
			  $charge = $tmp;
			  // add Fuel SurCharge
			  $charge *= $fsc_rate;
			  // add Handling Fee
			  $charge += UPS_HANDLING_FEE;
			  $charge *= $taxrate;
			  $value['TransportationCharges'] = $CURRENCY_DISPLAY->getFullValue($charge);
			}
			// So let's show the value in $$$$
			else {
			  $charge = $value['TransportationCharges'];
			  // add Fuel SurCharge
			  $charge *= $fsc_rate;
			  // add Handling Fee
			  $charge += UPS_HANDLING_FEE;
			  $charge *= $taxrate;
			  $value['TransportationCharges'] = $CURRENCY_DISPLAY->getFullValue($charge);
			  //could not convert, so show in USD
			  $value['TransportationCharges'] = $value['TransportationCharges']. " USD";
			}
		  }
		  else {
			$charge = $charge_unrated = $value['TransportationCharges'];
			// add Fuel SurCharge
			$charge *= $fsc_rate;
			// add Handling Fee
			$charge += UPS_HANDLING_FEE;
			$charge *= $taxrate;
			$value['TransportationCharges'] = $CURRENCY_DISPLAY->getFullValue($charge);
		  }
		  $shipping_rate_id = urlencode($this->classname."|UPS|".$value['ServiceName']."|".$charge);
		  $checked = (@$d["shipping_rate_id"] == $value) ? "checked=\"checked\"" : "";
		  
		  // BEGIN CUSTOM CODE - added by deneb
		  if (count($shipment) == 1 ) {
		  $checked = "checked=\"checked\"";
		  }
		  // END CUSTOM CODE - added by deneb
		  
		  $html .= "\n<input type=\"radio\" name=\"shipping_rate_id\" $checked value=\"$shipping_rate_id\" />\n";
		  
		  $_SESSION[$shipping_rate_id] = 1;
		  
		  $html .= $value['ServiceName']." ";
		  $html .= "<strong>(".$value['TransportationCharges'].")</strong>";
		  if (DEBUG) {
		  	$html .= " - ".$VM_LANG->_PHPSHOP_PRODUCT_FORM_WEIGHT.": ".$order_weight." ". $weight_measure.
					 ", ".$VM_LANG->_PHPSHOP_RATE_FORM_VALUE.": [[".$charge_unrated."(".$fsc_rate.")]+".UPS_HANDLING_FEE."](".$taxrate.")]";
		  }
		  // DELIVERY QUOTE
		  if (Show_Delivery_Days_Quote == 1) {
			  if( !empty($value['GuaranteedDaysToDelivery'])) {
				$html .= "&nbsp;&nbsp;-&nbsp;&nbsp;".$value['GuaranteedDaysToDelivery']." ".$VM_LANG->_PHPSHOP_UPS_SHIPPING_GUARANTEED_DAYS;
			  }
		  }
		  if (Show_Delivery_ETA_Quote == 1) {
			  if( !empty($value['ScheduledDeliveryTime'])) {
				$html .= "&nbsp;(ETA:&nbsp;".$value['ScheduledDeliveryTime'].")";
			  }
		  }		  
		  if (Show_Delivery_Warning == 1 && !empty($value['RatedShipmentWarning'])) {
			  $html .= "<br>\n&nbsp;&nbsp;&nbsp;*&nbsp;<em>".$value['RatedShipmentWarning']."</em>\n";
		  }
		  $html .= "<br />\n";
	  }
	}
	echo $html;
	//DEBUG
		  if (DEBUG){
		  	/*
			  echo "My Services: <br>";
			  print_r($myservicecodes); 
			  echo "<br>";
			  echo "All Services: <br>";
			  print_r($allservicecodes);
			  echo "<br>";
			  echo "XML Result: <br>";
			  echo "<textarea cols='80' rows='10'>".$xmlResult."</textarea>";
			  echo "<br>";
			  */
		  }
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
	
	if( intval(UPS_TAX_CLASS)== 0 )
	  return( 0 );
	else {
	  require_once( CLASSPATH. "ps_tax.php" );
	  $tax_rate = ps_tax::get_taxrate_by_id( intval(UPS_TAX_CLASS) );
	  return $tax_rate;
	}
  }
  
	/**
    * Validate this Shipping method by checking if the SESSION contains the key
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
    
      global $VM_LANG;
      /** Read current Configuration ***/
      require_once(CLASSPATH ."shipping/".$this->classname.".cfg.php");
    ?>
      <table class="adminform">
    <tr class="row0">
        <td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_UPS_ACCESS_CODE ?></strong></td>
		<td>
            <input type="text" name="UPS_ACCESS_CODE" class="inputbox" value="<? echo UPS_ACCESS_CODE ?>" />
		</td>
		<td>
          <?php echo mm_ToolTip($VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_UPS_ACCESS_CODE_EXPLAIN) ?>
        </td>
    </tr>
    <tr class="row1">
        <td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_UPS_USER_ID ?></strong>
		</td>
		<td>
            <input type="text" name="UPS_USER_ID" class="inputbox" value="<? echo UPS_USER_ID ?>" />
		</td>
		<td>
            <?php echo mm_ToolTip($VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_UPS_USER_ID_EXPLAIN) ?>
        </td>
    </tr>
    <tr class="row0">
        <td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_UPS_PASSWORD ?></strong>
		</td>
		<td>
            <input type="text" name="UPS_PASSWORD" class="inputbox" value="<? echo UPS_PASSWORD ?>" />
		</td>
		<td>
            <?php echo mm_ToolTip($VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_UPS_PASSWORD_EXPLAIN) ?>
        </td>
    </tr>
	<tr class="row1">
	  <td><strong><?php echo $VM_LANG->_PHPSHOP_UPS_PICKUP_METHOD ?></strong></td>
	  <td>
		<select class="inputbox" name="pickup_type">
		  <option <?php if(UPS_PICKUP_TYPE=="01") echo "selected=\"selected\"" ?> value="01">Daily Pickup</option>
		  <option <?php if(UPS_PICKUP_TYPE=="03") echo "selected=\"selected\"" ?> value="03">Customer Counter</option>
		  <option <?php if(UPS_PICKUP_TYPE=="06") echo "selected=\"selected\"" ?> value="06">One Time Pickup</option>
		  <option <?php if(UPS_PICKUP_TYPE=="07") echo "selected=\"selected\"" ?> value="07">On Call Air Pickup</option>
		  <option <?php if(UPS_PICKUP_TYPE=="19") echo "selected=\"selected\"" ?> value="19">Letter Center</option>
		  <option <?php if(UPS_PICKUP_TYPE=="20") echo "selected=\"selected\"" ?> value="20">Air Service Center</option>
		</select>
	  </td>
	  <td><?php echo mm_ToolTip($VM_LANG->_PHPSHOP_UPS_PICKUP_METHOD_TOOLTIP) ?></td>
	</tr>
	  <td><strong><?php echo $VM_LANG->_PHPSHOP_UPS_PACKAGE_TYPE ?></strong></td>
	  <td>
		<select class="inputbox" name="package_type">
		  <option <?php if(UPS_PACKAGE_TYPE=="00") echo "selected=\"selected\"" ?> value="00">Unknown
		  <option <?php if(UPS_PACKAGE_TYPE=="01") echo "selected=\"selected\"" ?> value="01">UPS letter</option>
		  <option <?php if(UPS_PACKAGE_TYPE=="02") echo "selected=\"selected\"" ?> value="02">Package</option>
		  <option <?php if(UPS_PACKAGE_TYPE=="03") echo "selected=\"selected\"" ?> value="03">UPS Tube</option>
		  <option <?php if(UPS_PACKAGE_TYPE=="04") echo "selected=\"selected\"" ?> value="04">UPS Pak</option>
		  <option <?php if(UPS_PACKAGE_TYPE=="21") echo "selected=\"selected\"" ?> value="21">UPS Express Box</option>
		  <option <?php if(UPS_PACKAGE_TYPE=="24") echo "selected=\"selected\"" ?> value="24">UPS 25Kg Box</option>
		  <option <?php if(UPS_PACKAGE_TYPE=="25") echo "selected=\"selected\"" ?> value="25">UPS 10Kg Box</option>
		</select>
	  </td>
	  <td><?php echo mm_ToolTip($VM_LANG->_PHPSHOP_UPS_PACKAGE_TYPE_TOOLTIP) ?></td>
	</tr>
	<tr class="row0">
	  <td><strong><?php echo $VM_LANG->_PHPSHOP_UPS_TYPE_RESIDENTIAL ?></strong></td>
	  <td>
		<select class="inputbox" name="residential">
			<option <?php if(UPS_RESIDENTIAL=="yes") echo "selected=\"selected\"" ?> value="yes"><?php echo $VM_LANG->_PHPSHOP_UPS_RESIDENTIAL ?></option>
			<option <?php if(UPS_RESIDENTIAL=="no") echo "selected=\"selected\"" ?> value="no"><?php echo $VM_LANG->_PHPSHOP_UPS_COMMERCIAL ?></option>
		</select>
	  </td>
	  <td><?php echo mm_ToolTip($VM_LANG->_PHPSHOP_UPS_RESIDENTIAL_TOOLTIP) ?></td>
	</tr>
	<tr class="row1">
	  <td><strong><?php echo $VM_LANG->_PHPSHOP_UPS_HANDLING_FEE ?></strong></td>
	  <td><input class="inputbox" type="text" name="handling_fee" value="<?php echo UPS_HANDLING_FEE ?>" /></td>
	  <td><?php echo mm_ToolTip($VM_LANG->_PHPSHOP_UPS_HANDLING_FEE_TOOLTIP) ?></td>
	</tr>
	<tr class="row0">
	  <td><strong><?php echo $VM_LANG->_PHPSHOP_UPS_TAX_CLASS ?></strong></td>
	  <td>
        <?php
        require_once(CLASSPATH.'ps_tax.php');
        ps_tax::list_tax_value("tax_class", UPS_TAX_CLASS) ?>
	  </td>
	  <td><?php echo mm_ToolTip($VM_LANG->_PHPSHOP_UPS_TAX_CLASS_TOOLTIP) ?></td>
	</tr>
<?php // BEGIN CUSTOM CODE ?>
	<tr class="row1">
	  <td><strong>Ship From Zip Code</strong></td>
	  <td><input class="inputbox" type="text" name="Override_Source_Zip" value="<?php echo Override_Source_Zip ?>" /></td>
	  <td><?php echo mm_ToolTip("Enter a zip code to override the Vendor ship from zip code") ?></td>
	</tr>
	<tr class="row0">
	  <td><strong>Show Delivery Days Quote?</strong></td>
	  <td><input class="inputbox" type="checkbox" name="Show_Delivery_Days_Quote" <?php if (Show_Delivery_Days_Quote == 1) echo "checked=\"checked\""; ?> value="1" /></td>
	  <td><?php echo mm_ToolTip("Enable the Quote-to-Delivery Note next to each Shipping Method that shows the days.") ?></td>
	</tr>
	<tr class="row1">
	  <td><strong>Show Delivery ETA Quote?</strong></td>
	  <td><input class="inputbox" type="checkbox" name="Show_Delivery_ETA_Quote" <?php if (Show_Delivery_ETA_Quote == 1) echo "checked=\"checked\""; ?> value="1" /></td>
	  <td><?php echo mm_ToolTip("Enable the Quote-to-Delivery Note next to each Shipping Method that shows the ETA, or Estimated Time of Arrival.") ?></td>
	</tr>
	<tr class="row0">
	  <td><strong>Show Delivery Warning?</strong></td>
	  <td><input class="inputbox" type="checkbox" name="Show_Delivery_Warning" <?php if (Show_Delivery_Warning == 1) echo "checked=\"checked\""; ?> value="1" /></td>
	  <td><?php echo mm_ToolTip("Enable the Quote-to-Delivery Warning under each Shipping Method that shows the message from the shipper.") ?></td>
	</tr>
	<tr class="row1">
	  <td colspan="3">
	  	<table>
			<tr class="row0">
			  <td colspan="2"><strong>Select Authorized Shipping Methods</strong></td>
			  <td><?php echo mm_ToolTip("Enable each UPS shipping method you would like to offer to customers. Then enter a Fuel Surcharge Rate in percent. (ex. 12.50%)") ?></td>
			</tr>
			<tr class="row1">
			  <td><div align="left"><strong>Shipping Method</strong></div></td>
			  <td><div align="left"><strong>Enable?</strong></div></td>
			  <td><div align="left"><strong>Fuel SurCharge Rate(%)</strong><?php echo mm_ToolTip("A percent of the base charge for each method is added for extra fuel charges. Leave blank or zero to remove the surcharge.") ?></div></td>
			</tr>
			<tr class="row0">
			  <td>UPS Next Day Air</td>
			  <td>
			    <div align="center">
			      <input type="checkbox" name="UPS_Next_Day_Air" class="inputbox" <?php if (UPS_Next_Day_Air == 01) echo "checked=\"checked\""; ?> value="01" />
			      </div></td>
			  <td><input class="inputbox" type="text" name="UPS_Next_Day_Air_FSC" value="<?php echo UPS_Next_Day_Air_FSC; ?>" />
			  </td>
			</tr>
			<tr class="row1">
			  <td>UPS 2nd Day Air</td>
			  <td>
				<div align="center">
				  <input type="checkbox" name="UPS_2nd_Day_Air" class="inputbox" <?php if (UPS_2nd_Day_Air == 02) echo "checked=\"checked\""; ?> value="02" />
		  	    </div></td>
			  <td><input class="inputbox" type="text" name="UPS_2nd_Day_Air_FSC" value="<?php echo UPS_2nd_Day_Air_FSC; ?>" />
			  </td>
			</tr>
			<tr class="row0">
			  <td>UPS Ground</td>
			  <td>
			    <div align="center">
			      <input type="checkbox" name="UPS_Ground" class="inputbox" <?php if (UPS_Ground == 03) echo "checked=\"checked\""; ?> value="03" />
			      </div></td>
			  <td><input class="inputbox" type="text" name="UPS_Ground_FSC" value="<?php echo UPS_Ground_FSC; ?>" />
			  </td>
			</tr>
			<tr class="row1">
			  <td>UPS Worldwide Express SM</td>
			  <td>
			    <div align="center">
			      <input type="checkbox" name="UPS_Worldwide_Express_SM" class="inputbox" <?php if (UPS_Worldwide_Express_SM == 07) echo "checked=\"checked\""; ?> value="07" />
			      </div></td>
			  <td><input class="inputbox" type="text" name="UPS_Worldwide_Express_SM_FSC" value="<?php echo UPS_Worldwide_Express_SM_FSC; ?>" />
			  </td>
			</tr>
			<tr class="row0">
			  <td>UPS Worldwide Expedited SM</td>
			  <td>
			    <div align="center">
			      <input type="checkbox" name="UPS_Worldwide_Expedited_SM" class="inputbox" <?php if (UPS_Worldwide_Expedited_SM == '08') echo "checked=\"checked\""; ?> value="08" />
			      </div></td>
			  <td><input class="inputbox" type="text" name="UPS_Worldwide_Expedited_SM_FSC" value="<?php echo UPS_Worldwide_Expedited_SM_FSC; ?>" />
			  </td>
			</tr>
			<tr class="row1">
			  <td>UPS Standard </td>
			  <td>
			    <div align="center">
			      <input type="checkbox" name="UPS_Standard" class="inputbox" <?php if (UPS_Standard == 11) echo "checked=\"checked\""; ?> value="11" />
			      </div></td>
			  <td><input class="inputbox" type="text" name="UPS_Standard_FSC" value="<?php echo UPS_Standard_FSC; ?>" />
			  </td>
			</tr>
			<tr class="row0">
			  <td>UPS 3 Day Select </td>
			  <td>
			    <div align="center">
			      <input type="checkbox" name="UPS_3_Day_Select" class="inputbox" <?php if (UPS_3_Day_Select == 12) echo "checked=\"checked\""; ?> value="12" />
			      </div></td>
			  <td><input class="inputbox" type="text" name="UPS_3_Day_Select_FSC" value="<?php echo UPS_3_Day_Select_FSC; ?>" />
			  </td>
			</tr>
			<tr class="row1">
			  <td>UPS Next Day Air Saver</td>
			  <td>
			    <div align="center">
			      <input type="checkbox" name="UPS_Next_Day_Air_Saver" class="inputbox" <?php if (UPS_Next_Day_Air_Saver == 13) echo "checked=\"checked\""; ?> value="13" />
			      </div></td>
			  <td><input class="inputbox" type="text" name="UPS_Next_Day_Air_Saver_FSC" value="<?php echo UPS_Next_Day_Air_Saver_FSC; ?>" />
			  </td>
			</tr>
			<tr class="row0">
			  <td>UPS Next Day Air Early A.M. </td>
			  <td>
			    <div align="center">
			      <input type="checkbox" name="UPS_Next_Day_Air_Early_AM" class="inputbox" <?php if (UPS_Next_Day_Air_Early_AM == 14) echo "checked=\"checked\""; ?> value="14" />
			      </div></td>
			  <td><input class="inputbox" type="text" name="UPS_Next_Day_Air_Early_AM_FSC" value="<?php echo UPS_Next_Day_Air_Early_AM_FSC; ?>" />
			  </td>
			</tr>
			<tr class="row1">
			  <td>UPS Worldwide Express Plus SM</td>
			  <td>
			    <div align="center">
			      <input type="checkbox" name="UPS_Worldwide_Express_Plus_SM" class="inputbox" <?php if (UPS_Worldwide_Express_Plus_SM == 54) echo "checked=\"checked\""; ?> value="54" />
			      </div></td>
			  <td><input class="inputbox" type="text" name="UPS_Worldwide_Express_Plus_SM_FSC" value="<?php echo UPS_Worldwide_Express_Plus_SM_FSC; ?>" />
			  </td>
			</tr>
			<tr class="row0">
			  <td>UPS 2nd Day Air A.M</td>
			  <td>
				<div align="center">
				  <input type="checkbox" name="UPS_2nd_Day_Air_AM" class="inputbox" <?php if (UPS_2nd_Day_Air_AM == 59) echo "checked=\"checked\""; ?> value="59" />		
			    </div></td>
			  <td><input class="inputbox" type="text" name="UPS_2nd_Day_Air_AM_FSC" value="<?php echo UPS_2nd_Day_Air_AM_FSC; ?>" />
			  </td>
			</tr>
			<tr class="row1">
			  <td>UPS Express Saver</td>
			  <td>
			    <div align="center">
			      <input type="checkbox" name="UPS_Express_Saver" class="inputbox" <?php if (UPS_Express_Saver == 65) echo "checked=\"checked\""; ?> value="65" />
			      </div></td>
			  <td><input class="inputbox" type="text" name="UPS_Express_Saver_FSC" value="<?php echo UPS_Express_Saver_FSC; ?>" />
			  </td>
			</tr>
			<tr class="row0">
			  <td>n/a </td>
			  <td>
			    <div align="center">
			      <input type="checkbox" name="na" class="inputbox" <?php if (na == 64) echo "checked=\"checked\""; ?> value="64" />
			      </div></td>
			  <td>&nbsp;
			  </td>
			</tr>
		  </table>
	  </td>
	</tr>
<? // END CUSTOM CODE ?>		
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
      
      $my_config_array = array("UPS_ACCESS_CODE" => $d['UPS_ACCESS_CODE'],
							  "UPS_USER_ID" => $d['UPS_USER_ID'],
							  "UPS_PASSWORD" => $d['UPS_PASSWORD'],
							  "UPS_PICKUP_TYPE" => $d['pickup_type'],
							  "UPS_PACKAGE_TYPE" => $d['package_type'],
							  "UPS_RESIDENTIAL" => $d['residential'],
							  "UPS_HANDLING_FEE" => $d['handling_fee'],
							  "UPS_TAX_CLASS" => $d['tax_class']
// BEGIN CUSTOM CODE							  
							  ,"Override_Source_Zip" => $d['Override_Source_Zip'],
							  "Show_Delivery_Days_Quote" => $d['Show_Delivery_Days_Quote'],
							  "Show_Delivery_ETA_Quote" => $d['Show_Delivery_ETA_Quote'],
							  "Show_Delivery_Warning" => $d['Show_Delivery_Warning'],
							  "UPS_Next_Day_Air" => $d['UPS_Next_Day_Air'],
							  "UPS_Next_Day_Air_FSC" => $d['UPS_Next_Day_Air_FSC'],
							  "UPS_2nd_Day_Air" => $d['UPS_2nd_Day_Air'],
							  "UPS_2nd_Day_Air_FSC" => $d['UPS_2nd_Day_Air_FSC'],
							  "UPS_Ground" => $d['UPS_Ground'],
							  "UPS_Ground_FSC" => $d['UPS_Ground_FSC'],
							  "UPS_Worldwide_Express_SM" => $d['UPS_Worldwide_Express_SM'],
							  "UPS_Worldwide_Express_SM_FSC" => $d['UPS_Worldwide_Express_SM_FSC'],
							  "UPS_Worldwide_Expedited_SM" => $d['UPS_Worldwide_Expedited_SM'],
							  "UPS_Worldwide_Expedited_SM_FSC" => $d['UPS_Worldwide_Expedited_SM_FSC'],
							  "UPS_Standard" => $d['UPS_Standard'],
							  "UPS_Standard_FSC" => $d['UPS_Standard_FSC'],
							  "UPS_3_Day_Select" => $d['UPS_3_Day_Select'],
							  "UPS_3_Day_Select_FSC" => $d['UPS_3_Day_Select_FSC'],
							  "UPS_Next_Day_Air_Saver" => $d['UPS_Next_Day_Air_Saver'],
							  "UPS_Next_Day_Air_Saver_FSC" => $d['UPS_Next_Day_Air_Saver_FSC'],
							  "UPS_Next_Day_Air_Early_AM" => $d['UPS_Next_Day_Air_Early_AM'],
							  "UPS_Next_Day_Air_Early_AM_FSC" => $d['UPS_Next_Day_Air_Early_AM_FSC'],
							  "UPS_Worldwide_Express_Plus_SM" => $d['UPS_Worldwide_Express_Plus_SM'],
							  "UPS_Worldwide_Express_Plus_SM_FSC" => $d['UPS_Worldwide_Express_Plus_SM_FSC'],
							  "UPS_2nd_Day_Air_AM" => $d['UPS_2nd_Day_Air_AM'],
							  "UPS_2nd_Day_Air_AM_FSC" => $d['UPS_2nd_Day_Air_AM_FSC'],
							  "UPS_Express_Saver" => $d['UPS_Express_Saver'],
							  "UPS_Express_Saver_FSC" => $d['UPS_Express_Saver_FSC'],
							  "na" => $d['na']					  
							  
// END CUSTOM CODE							 
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
