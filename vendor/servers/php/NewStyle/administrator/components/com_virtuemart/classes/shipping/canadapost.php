<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.'); 
/**
*
* @version $Id: canadapost.php,v 1.6 2005/11/17 09:31:13 codename-matrix Exp $
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
require_once(CLASSPATH."shipping/minixml/minixml.inc.php" );
/**
*/
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
function	fetchArray( &$xmldoc, $path, $tag, $fields ){
	$response =& $xmldoc->getElementByPath( $path );
	if( ! is_object($response) ) return array() ;
	
	$children =& $response->getAllChildren();
	
	$count = 0 ;
	$array = array();
	for( $i = 0; $i < $response->numChildren(); $i++){
		if( $tag == $children[$i]->name() ){;
			foreach( $fields as $field ){
				$name = $children[$i]->getElement($field) ;
				$array[$count][$field] =$name->getValue();
			}
			$count ++ ;
		}
	}
	
	return $array ;
}	

function	fetchValue( &$xmldoc, $path ){
	$e = $xmldoc->getElementByPath( $path );
	return is_object($e) ? $e->getValue() : "";
}


class canadapost {
	var $classname = "canadapost",

		$debug = false ,

//		$server = "206.191.4.228",
//		$port = 30000,
//		$merchant_cpcid = "CPC_DEMO_XML",
		
		$error = false,
		$err_msg = "",
		$xml_request = "",
		$xml_response = "",
		$fp,  // socket handle

		$xml_response_tree = array(),
		$shipping_methods = array(),
		$shipping_comment = "" ,
		
		$to_city = "",
		$to_provState = "",
		$to_country = "",
		$to_postal_code = "" ;
	
	function	CanadaPost(){
        require_once(CLASSPATH ."shipping/".$this->classname.".cfg.php");
		
		if( defined('CP_SERVER') ) $this->server = CP_SERVER ;
		if( defined('CP_PORT') ) $this->port = CP_PORT ;
		if( defined('MERCHANT_CPCID') ) $this->merchant_cpcid = MERCHANT_CPCID ;
		$this->_initRequestXML();
	}

	function addItem( $quantity, $weight, $length, $width, $height, $description )	{
		$this->xml_request .= 
"
		<item>
			<quantity>" . htmlspecialchars($quantity) . "</quantity>
			<weight>" . htmlspecialchars($weight) . "</weight>
			<length>" . htmlspecialchars($length) . "</length>
			<width>" . htmlspecialchars($width) . "</width>
			<height>" . htmlspecialchars($height) . "</height>
			<description>" . htmlspecialchars($description) . "</description>
		</item>
";
	}
	
	function	getQuote( $city, $provstate, $country, $postal_code ){
		$this->_shipTo( $city, $provstate, $country, $postal_code ) ;
		$this->_sendRequestXML();
		$this->_getResponseXML();
		$this->_xmlToQuote() ;
	}
	
	function 	_initRequestXML(){
      global $VM_LANG;

		$this->xml_request = 
"<?phpxml version=\"1.0\" ?>
<eparcel>
	<language>".$VM_LANG->_PHPSHOP_CPOST_SEND_LANGUAGE_CODE."</language>
	<ratesAndServicesRequest>
		<merchantCPCID>" . $this->merchant_cpcid . "</merchantCPCID>
		<lineItems>" ;
//					<itemsPrice>" . $p->price * $qty . "</itemsPrice>
	}
	
	// if no Postal Code input, Canada Post will return statusCode 5000 and statusMessage "XML parsing error ".
	function  _shipTo( $city, $provstate, $country, $postal_code ){
		$this->to_city = $city ;
		$this->to_provState = $provstate;
		$this->to_country = $country ;
		$this->to_postal_code = $postal_code ;

		$this->xml_request .= 
"
		</lineItems>
"	.
( strlen($this->to_city) > 0  ? "<city>" . htmlspecialchars($this->to_city) . "</city>\n" : "" ) . 
( strlen($this->to_provState) > 0  ? "		<provOrState>" . htmlspecialchars($this->to_provState) . "</provOrState>\n" : "		<provOrState> </provOrState>\n" ) . 
( strlen($this->to_country) > 0  ? "		<country>" . htmlspecialchars($this->to_country) . "</country>\n" : "" ) . 
( strlen($this->to_postal_code) > 0  ? "		<postalCode>" . htmlspecialchars($this->to_postal_code) . "</postalCode>\n" : "		<postalCode> </postalCode>\n" ) . 
"
	</ratesAndServicesRequest>
</eparcel>
" ;
	}

	function	_sendRequestXML(){
		$this->fp = fsockopen ( $this->server, $this->port, $errno, $errstr, 30 );
		if (!$this->fp) {
    			die("Open Socket Error: $errstr ($errno)<br>\n");
				$this->error = true ;
				$this->error_msg = $errstr ;
		} else
			fwrite( $this->fp, $this->xml_request );
	}
	
	function	_getResponseXML(){
		if (!$this->fp) return ;
		while(!feof ($this->fp))
			$this->xml_response .= fgets( $this->fp, 4096 );
   		fclose($this->fp);
	}
	
	function	_xmlToQuote(){
		$xd = new MiniXMLDoc( $this->xml_response );

		$startTag = 'eparcel/error/' ;
		$this->statusCode = fetchValue( $xd, $startTag . 'statusCode' ) ;
		if ($this->statusCode != "") {
			$this->error = true;
			$this->error_msg = fetchValue( $xd, $startTag . 'statusMessage' );
		}
		else {
			$this->error = false;
			$startTag = 'eparcel/ratesAndServicesResponse/';
			$this->shipping_comment = fetchValue( $xd, $startTag . 'comment' );
			$shipping_fields = array( "name", "rate", "shippingDate", "deliveryDate", "deliveryDayOfWeek",  "nextDayAM", "packingID");
			$this->shipping_methods = fetchArray( $xd, $startTag, 'product', $shipping_fields );
		}
	}



	
	function list_rates( &$d ) {	
      global $VM_LANG, $CURRENCY_DISPLAY;
	  
	  $d["ship_to_info_id"] = mosGetParam( $_REQUEST, "ship_to_info_id" );
      /** Read current Configuration ***/
      require_once(CLASSPATH ."shipping/".$this->classname.".cfg.php");
	  
	  $dbst = new ps_DB;
	  $q  = "SELECT * from #__{vm}_user_info, #__{vm}_country WHERE user_info_id='" . $d["ship_to_info_id"]."' AND ( country=country_2_code OR country=country_3_code)";
	  $dbst->query($q);
	  $dbst->next_record();

     $cart = $_SESSION['cart'];
     $dboi = new ps_DB;
     for($i = 0; $i < $cart["idx"]; $i++) {
        $r = "SELECT product_id,product_name,product_weight,product_length,product_width ";
        $r .= "FROM #__{vm}_product WHERE product_id='".$cart[$i]["product_id"]."'";
        $dboi->query($r);
        $dboi->next_record();
		
//		echo ($cart[$i]["quantity"]." ".$dboi->f("product_weight")." ".$dboi->f("product_length")." ".$dboi->f("product_width")." ".$dboi->f("product_height")." ".$dboi->f("product_name"));
		$this->addItem( $cart[$i]["quantity"],
						$dboi->f("product_weight") ? $dboi->f("product_weight") : 0,
						$dboi->f("product_length") ? $dboi->f("product_length") : 0,
						$dboi->f("product_width") ? $dboi->f("product_width") : 0,
						$dboi->f("product_height") ? $dboi->f("product_height") : 0,
						$dboi->f("product_name")) ;
//		$this->addItem( $cart[$i]["quantity"], $dboi->f("product_weight"), 10, 10, 10, $dboi->f("product_name")) ;
	  } 

	  $this->getQuote( 	urlencode($dbst->f("city")),
	  					urlencode($dbst->f("country_2_code")=="US" ? $dbst->f("state") : ""),
						$dbst->f("country_2_code"),
						$dbst->f("zip") );

	  $shipping_rate_id = urlencode(mosGetParam( $_REQUEST, "shipping_rate_id" ));
	  $i=0;
	  if( !$this->error ){
	  ?>
      <table width="100%"><tr class="sectiontableheader">
	  <th>&nbsp;</th>
	  <th><?php echo $VM_LANG->_PHPSHOP_ISSHIP_LIST_CARRIER_LBL ?></th>
 	  <th><?php echo $VM_LANG->_PHPSHOP_CPOST_FORM_HANDLING_DATE ?><sup>1</sup></th>
	  <th><?php echo $VM_LANG->_PHPSHOP_CPOST_FORM_HANDLING_LBL ?><sup>2</sup></th>
      </tr>
      <?php
	  	foreach( $this->shipping_methods as $m ){

			$value = urlencode($this->classname."|".$m["name"]."|".$m["deliveryDate"]."|".$m["rate"]);
			$_SESSION[urlencode($this->classname."|".$m["name"]."|".$m["deliveryDate"]."|".$m["rate"])] = 1;
		
        	if ($i++ % 2)
				$class="sectiontableentry1";
        	else
            	$class="sectiontableentry2";

			$checked = ($shipping_rate_id == $value) ? "checked=\"checked\"" : ""; 
			
			// formatting of the shipping date returned by Canada Post
			$str = $m["deliveryDate"];
			if (($timestamp = strtotime($str)) === -1) {
			   $str = html_entity_decode($m["deliveryDate"]);
			} else {
				if ($VM_LANG->_PHPSHOP_CPOST_SEND_LANGUAGE_CODE == "FR") {
					setlocale(LC_ALL, 'fr');		   
					$str = strftime('%A %d %B %Y',$timestamp);
				} else {
					setlocale(LC_ALL, 'en');		   
					$str = strftime('%A, %B %d %Y',$timestamp);
				}
			}
			
			// Adding taxes to the rates returned by Canada Post
			// First : add the federal tax (FT) to the shipping rate -> R * (1+FT%) = R1
			// Second : add the provincial tax (PT) to the rate R1 -> R1 * (1+PT%) = R2
			$R1 = $m["rate"] * (1+(CP_FEDERAL_TAX/100));
			$R2 = $R1 * (1+(CP_PROVINCIAL_TAX/100));
?>
			<tr class=<?php echo $class; ?> >
				<td><?php print "<input type=\"radio\" name=\"shipping_rate_id\" $checked value=\"$value\" />\n"; ?></td>
				<td><?php print html_entity_decode($m["name"]); ?></td>
				<td align="center"><?php print $str; ?></td>
				<td align="right"><?php echo $CURRENCY_DISPLAY->getFullValue( $R2 ) ; ?></td>
			</tr>
			<tr>
				<td colspan="4" bgcolor="#cccccc"><img src="/pics/blank.gif" width="1" height="1" border="0"></td>
			</tr>
<?php
		} // foreach

// print "<hr>\n\n\n" ;
// print "Request XML:<br><form action='http://" . CP_SERVER . ":" . CP_PORT . "' method='post' target='_blank' ><textarea name='XMLRequest' style='width:100%;height:400px;background-color:#f2f2f2'>\n" . $this->xml_request . "\n\n</textarea><br><input type='submit' value='Send to Canada Post'></form>";
// print "<br><br>Return XML:<br><form><textarea style='width:100%;height:400px;background-color:#f2f2f2'>\n" . $this->xml_response . "\n\n</textarea></form>";
?>
		<td colspan="4">
		<?php echo "<SUP>1</SUP>La date de livraison est calculée en ajoutant les normes de livraison de Postes Canada au délai d’exécution des commandes.<BR>"; ?>
		<?php echo "<SUP>2</SUP>Les frais d’expédition sont calculés en ajoutant les services de Postes Canada aux coûts de manutention. Taxes incluses.<BR>"; ?>
		</td>
<?php
		return True;
		
	  }
	  else {
		// Switch to StandardShipping on Error !!!
		echo html_entity_decode($this->error_msg)."<br><br>";
// print "<hr>\n\n\n" ;
// print "Request XML:<br><form action='http://" . CP_SERVER . ":" . CP_PORT . "' method='post' target='_blank' ><textarea name='XMLRequest' style='width:100%;height:400px;background-color:#f2f2f2'>\n" . $this->xml_request . "\n\n</textarea><br><input type='submit' value='Send to Canada Post'></form>";
// print "<br><br>Return XML:<br><form><textarea style='width:100%;height:400px;background-color:#f2f2f2'>\n" . $this->xml_response . "\n\n</textarea></form>";
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
	  global $vars;
	  // We have to do a trick here, because there are two tax rates
	  $total_amount = $this->get_rate( $vars );	
	  $R2 = $total_amount / (1+(CP_PROVINCIAL_TAX/100));
	  $R1 = $R2 / (1+(CP_FEDERAL_TAX/100));
	  $tax_amount = $total_amount - $R1;
	  $tax_rate = $tax_amount / $total_amount;
	  
	  return $tax_rate;
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
		  <td width="20%"><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_CANADAPOST_MERCHANT_CPCID ?></strong>:</td>
		  <td colspan="3" width="80%">
			  <input type="text" name="MERCHANT_CPCID" class="inputbox" value="<?php echo MERCHANT_CPCID ?>" />
			  <?php echo mm_ToolTip($VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_CANADAPOST_MERCHANT_CPCID_EXPLAIN) ?>
		  </td>
		</tr>
		<tr>
			<td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_CANADAPOST_CP_SERVER ?></strong>:
			</td>
			<td colspan="3">
				<input type="text" name="CP_SERVER" class="inputbox" value="<?php echo CP_SERVER ?>" />
			  <?php echo mm_ToolTip($VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_CANADAPOST_CP_SERVER_EXPLAIN) ?>
			</td>
		</tr>
		<tr>
			<td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_CANADAPOST_CP_PORT ?></strong>:
			</td>
			<td colspan="3">
				<input type="text" name="CP_PORT" class="inputbox" value="<?php echo CP_PORT ?>" />
				<?php echo mm_ToolTip($VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_CANADAPOST_CP_PORT_EXPLAIN) ?>
			</td>
		</tr>
		<tr>
			<td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_CANADAPOST_CP_FEDERAL_TAX ?></strong>:
			</td>
			<td colspan="3">
				<input type="text" name="CP_FEDERAL_TAX" class="inputbox" value="<?php echo CP_FEDERAL_TAX ?>" />
				<?php echo mm_ToolTip($VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_CANADAPOST_CP_FEDERAL_TAX_EXPLAIN) ?>
			</td>
		</tr>
		<tr>
			<td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_CANADAPOST_CP_PROVINCIAL_TAX ?></strong>:
			</td>
			<td colspan="3">
				<input type="text" name="CP_PROVINCIAL_TAX" class="inputbox" value="<?php echo CP_PROVINCIAL_TAX ?>" />
				<?php echo mm_ToolTip($VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_CANADAPOST_CP_PROVINCIAL_TAX_EXPLAIN) ?>
			</td>
		</tr>
		<tr>
			<td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_CANADAPOST_ARRIVAL_DATE_EXPLAIN ?></strong>:
			</td>
			<td colspan="3">
				<textarea name="CP_ARRIVAL_DATE_EXPLAIN" class="inputbox" cols="50" rows="5" ><?php echo CP_ARRIVAL_DATE_EXPLAIN ?></textarea>
				<?php echo mm_ToolTip($VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_CANADAPOST_ARRIVAL_DATE_EXPLAIN_I) ?>
			</td>
		</tr>
		<tr>
			<td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_CANADAPOST_HANDLING_CHARGE_EXPLAIN ?></strong>:
			</td>
			<td colspan="3">
				<textarea name="CP_HANDLING_CHARGE_EXPLAIN" class="inputbox" cols="50" rows="5" ><?php echo CP_HANDLING_CHARGE_EXPLAIN ?></textarea>
				<?php echo mm_ToolTip($VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_CANADAPOST_HANDLING_CHARGE_EXPLAIN_I) ?>
			</td>
		</tr>
		<tr>
		  <td colspan="4"><hr /></td>
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
      
      $my_config_array = array("MERCHANT_CPCID" => $d['MERCHANT_CPCID'],
							  "CP_SERVER" => $d['CP_SERVER'],
							  "CP_PORT" => $d['CP_PORT'],
							  "CP_FEDERAL_TAX" => $d['CP_FEDERAL_TAX'],
							  "CP_PROVINCIAL_TAX" => $d['CP_PROVINCIAL_TAX'], 
							  "CP_ARRIVAL_DATE_EXPLAIN" => $d['CP_ARRIVAL_DATE_EXPLAIN'],
							  "CP_HANDLING_CHARGE_EXPLAIN" => $d['CP_HANDLING_CHARGE_EXPLAIN']
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
