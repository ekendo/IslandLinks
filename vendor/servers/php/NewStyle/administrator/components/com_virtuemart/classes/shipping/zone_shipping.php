<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: zone_shipping.php,v 1.5 2005/11/16 14:43:32 codename-matrix Exp $
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
* Welcome To The Shipping Zone =]
* @copyright (C) 2000 - 2004 devcompany.com  All rights reserved.
* @author Mike Wattier - geek@devcompany.com
*/
class zone_shipping {

  var $classname = "zone_shipping";
  
  /**************************************************************************
  ** name: list_rates($d)
  ** created by: mwattier <geek@devcompany.com>
  ** description:  Get the rate according to what is in the basket AND
  **               the zone charge unless it hits the limit, then return that
  **               
  ** parameters: $ship_to_info_id - Where are we shipping to
  **             $zone_qty - This is what we use to see if we need to apply
  **             the limit or a per item cost
  ** returns: the cost to ship this order
  ***************************************************************************/  
  function list_rates( &$d ) {
      global $CURRENCY_DISPLAY;
      $db = new ps_DB;
      
      $q = "SELECT country FROM #__{vm}_user_info WHERE ";
      $q .= "user_info_id='". $d["ship_to_info_id"] . "'";
      $db->query($q);
      $db->next_record(); 
      $country = $db->f("country");
      
      $q2 = "SELECT country_name, zone_id FROM #__{vm}_country WHERE country_3_code='$country' ";
      $db->query($q2);
      $db->next_record(); 
      $the_zone = $db->f("zone_id");
      $country_name = $db->f("country_name");

      if ( $_SESSION['auth']['show_price_including_tax'] != 1 ) {
          $taxrate = 1;
      }
      else {
          $taxrate = $this->get_tax_rate( $the_zone ) + 1;
      }
      
      $q3 = "SELECT * FROM #__{vm}_zone_shipping WHERE zone_id ='$the_zone' ";
      $db->query($q3);
      $db->next_record(); 

      $cost_low = $db->f("zone_cost") * $d["zone_qty"];

      if($cost_low < $db->f("zone_limit")) {
         $rate = $cost_low;
      } 
      else {
         $rate = $db->f("zone_limit");
      }
      $rate *= $taxrate;
      
      // THE ORDER OF THOSE VALUES IS IMPORTANT:
      // carrier_name|rate_name|totalshippingcosts|rate_id
      $value = urlencode($this->classname."|".$the_zone."|".$country."|".$rate."|".$the_zone);
      
      $_SESSION[$value] = "1";
      $string = "<input type=\"radio\" checked=\"checked\" name=\"shipping_rate_id\" value=\"$value\" />";
      $string .= "Zone Shipping $country_name: <strong>". $CURRENCY_DISPLAY->getFullValue($rate )."</strong>";
      
      echo $string;
    }
    
  function get_rate( &$d ) {	
  
	  $shipping_rate_id = $_REQUEST["shipping_rate_id"];
	  $zone_arr = explode("|", urldecode(urldecode($shipping_rate_id)) );
	  $order_shipping = $zone_arr[3];
	  
	  return $order_shipping;
  }

	
  function get_tax_rate( $zone_id=0 ) {
      $db = new ps_DB();
      
	  if( $zone_id == 0 ) {
          $shipping_rate_id = $_REQUEST["shipping_rate_id"];
          $zone_arr = explode("|", urldecode(urldecode($shipping_rate_id)) );
          $zone_id = $zone_arr[4];
      }
	  $db->query( "SELECT tax_rate FROM #__{vm}_zone_shipping,#__{vm}_tax_rate WHERE zone_id='$zone_id' AND zone_tax_rate=tax_rate_id" );
      $db->next_record();
	  if( $db->f('tax_rate') ) 
        return $db->f('tax_rate');
      else
        return 0;
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

}
?>
