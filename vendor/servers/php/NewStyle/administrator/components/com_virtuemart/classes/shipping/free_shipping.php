<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: free_shipping.php,v 1.3 2005/09/29 20:02:18 soeren_nb Exp $
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
* Just a dummy class for "Free SHIPPING"
* You can set the Cart Total for Free Shipping in the Store Form (Edit Store)
*/
class free_shipping {

  var $classname = "free_shipping";
  
  /**************************************************************************
  ** name: list_rates( $d )
  ** created by: soeren
  ***************************************************************************/  
  function list_rates( &$d ) {
      global $vendor_name, $VM_LANG;
      
      $html = "<strong>".$VM_LANG->_PHPSHOP_FREE_SHIPPING_CUSTOMER_TEXT."</strong><br/>\n";
      $html .= "<input type=\"hidden\" name=\"shipping_rate_id\" value=\"free_shipping|$vendor_name|".$VM_LANG->_PHPSHOP_FREE_SHIPPING."|0|1\" />";
      
      echo $html;
      return true;
    }
    
  /**************************************************************************
  ** name: get_rate( $d )
  ** created by: soeren
  ***************************************************************************/
   function get_rate( &$d ) {
   
      return 0;
      
   } 
   
  /**************************************************************************
  ** name:  get_tax_rate() {( $d )
  ** created by: soeren
  ***************************************************************************/
   function get_tax_rate() {
   
      return 0;
      
   }
   /**************************************************************************
   * name: validate()
   * created by: soeren
   * parameters: $vars
   * returns: true
   **************************************************************************/
  function validate( &$d ) {
    global $vendor_freeshipping;
    
    return true;
  }
}
?>
