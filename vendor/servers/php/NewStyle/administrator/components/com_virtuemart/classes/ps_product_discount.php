<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: ps_product_discount.php,v 1.5.2.1 2006/03/06 20:28:48 soeren_nb Exp $
* @package VirtueMart
* @subpackage classes
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

/****************************************************************************
*
* CLASS DESCRIPTION
*                   
* ps_product_discount
*
* The class is is used to manage the discounts in your store.
* 
*	
*
*************************************************************************/
 class ps_product_discount {
   var $classname = "ps_product_discount";
   var $error;
   
  /**************************************************************************
  ** name: validate_add()
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/  
   function validate_add($d) {
     
     $db = new ps_DB;
     
     if (!$d["amount"]) {
       $this->error = "ERROR:  You must enter an amount for the Discount.";
       return False;	
     }
     if( $d["is_percent"]=="" ) {
       $this->error = "ERROR:  You must select a Discount type.";
       return False;	
     }
     return True;    
   }

  /**************************************************************************
  ** name: validate_update
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/   
  function validate_update($d) {
    
    if (!$d["amount"]) {
      $this->error = "ERROR:  You must enter an amount for the Discount.";
      return False;	
    }
    if( $d["is_percent"]=="" ) {
      $this->error = "ERROR:  You must enter an amount type for the Discount.";
      return False;	
    }
    if (!$d["discount_id"]) {
      $this->error = "ERROR:  You must specifiy a discount to Update.";
      return False;	
    }
   return true;
  }
    
  /**************************************************************************
  ** name: validate_delete()
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/   
  function validate_delete($discount_id) {
    
    if (!$discount_id) {
      $this->error = "ERROR:  Please select a Discount to delete.";
      return False;
    }
    $db = new ps_DB;
	$db->query( "SELECT product_id FROM #__{vm}_product WHERE product_discount_id=".intval($discount_id) );
	if( $db->num_rows() > 0 ) {
		$this->error = "Error: This discount still has products assigned to it!";
		return false;
	}
	
	return True;
    
  }
  
  
  /**************************************************************************
   * name: add()
   * created by: pablo
   * description: creates a new discount record
   * parameters:
   * returns:
   **************************************************************************/
  function add(&$d) {

    $db = new ps_DB;
    
    if (!$this->validate_add($d)) {
      $d["error"] = $this->error;
      return False;
    }
	
    if (!empty($d["start_date"])) {
        $day = substr ( $d["start_date"], 8, 2);
        $month= substr ( $d["start_date"], 5, 2);
        $year =substr ( $d["start_date"], 0, 4);
        $d["start_date"] = mktime(0,0,0,$month, $day, $year);
    }
    else {
      $d["start_date"] = "";
    }
    if (!empty($d["end_date"])) {
        $day = substr ( $d["end_date"], 8, 2);
        $month= substr ( $d["end_date"], 5, 2);
        $year =substr ( $d["end_date"], 0, 4);
        $d["end_date"] = mktime(0,0,0,$month, $day, $year);
    }
    else {
      $d["end_date"] = "";
    }
    
    $q = "INSERT INTO #__{vm}_product_discount (amount, is_percent, start_date, end_date)";
    $q .= " VALUES ('";
    $q .= $d["amount"] . "','";
    $q .= $d["is_percent"] . "','";
    $q .= $d["start_date"] . "','";
    $q .= $d["end_date"] . "')";
    $db->setQuery($q);
    $db->query();
    
    return True;

  }
  
  /**************************************************************************
   * name: update()
   * created by: pablo
   * description: updates discount information
   * parameters:
   * returns:
   **************************************************************************/
  function update(&$d) {
    $db = new ps_DB;


    if (!$this->validate_update($d)) {
      $d["error"] = $this->error;
      return False;	
    }
    if (!empty($d["start_date"])) {
        $day = substr ( $d["start_date"], 8, 2);
        $month= substr ( $d["start_date"], 5, 2);
        $year =substr ( $d["start_date"], 0, 4);
        $d["start_date"] = mktime(0,0,0,$month, $day, $year);
    }
    else {
      $d["start_date"] = "";
    }
    if (!empty($d["end_date"])) {
        $day = substr ( $d["end_date"], 8, 2);
        $month= substr ( $d["end_date"], 5, 2);
        $year =substr ( $d["end_date"], 0, 4);
        $d["end_date"] = mktime(0,0,0,$month, $day, $year);
    }
    else {
      $d["end_date"] = "";
    }
    
    $q = "UPDATE #__{vm}_product_discount SET ";
    $q .= "amount='" . $d["amount"]."',";
    $q .= "is_percent='" . $d["is_percent"]."',";
    $q .= "start_date='" . $d["start_date"]."', ";
    $q .= "end_date='" . $d["end_date"]."' ";
    $q .= "WHERE discount_id='".$d["discount_id"]."'";
    $db->setQuery($q);
    $db->query();
    
    return True;
  }

	/**
	* Controller for Deleting Records.
	*/
	function delete(&$d) {
		
		$record_id = $d["discount_id"];
		
		if( is_array( $record_id)) {
			foreach( $record_id as $record) {
				if( !$this->delete_record( $record, $d ))
					return false;
			}
			return true;
		}
		else {
			return $this->delete_record( $record_id, $d );
		}
	}
	/**
	* Deletes one Record.
	*/
	function delete_record( $record_id, &$d ) {
		global $db;
		
		if (!$this->validate_delete($record_id)) {
			$d["error"]=$this->error;
			return False;
		}
		$q = "DELETE FROM #__{vm}_product_discount WHERE discount_id='$record_id'";
		$db->query($q);
		
		return True;
	}
  
  /**************************************************************************
   * name: discount_list()
   * created by: soeren
   * description: Builds a select list of all discount records.
   * parameters: 
   * returns:
   **************************************************************************/
  function discount_list( $discount_id='' ) {
    global $VM_LANG, $option;
    $db = new ps_DB;
    $html = "";
    $db->query( "SELECT * FROM #__{vm}_product_discount" );
    
    if($db->num_rows() > 0) {
      $html = "<select name=\"product_discount_id\" class=\"inputbox\" onchange=\"updateDiscountedPrice();\">\n";
      $html .= "<option id=\"*1\" value=\"0\">".$VM_LANG->_PHPSHOP_INFO_MSG_VAT_ZERO_LBL."</option>\n";
      while( $db->next_record() ) {
		if( $db->f("is_percent") ) {
			$id = "*".(100-$db->f("amount"))/100;
		}
		else
			$id = "-".$db->f("amount");
        $selected = $db->f("discount_id") == $discount_id ? "selected=\"selected\"" : "";
        $html .= "<option id=\"$id\" value=\"".$db->f("discount_id")."\" $selected>".$db->f("amount");
        $html .= $db->f("is_percent")=="1" ? "%" : $_SESSION['vendor_currency'];
        $html .= "</option>\n";
      }
	  $html .= "<option value=\"override\">Override</option>\n";
      $html .= "</select>\n";
    }
    else {
      $html = "<input type=\"hidden\" name=\"product_discount_id\" value=\"0\" />\n
      <a href=\"".$_SERVER['PHP_SELF']."?option=$option&page=product.product_discount_form\" target=\"_blank\">".$VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_ADDDISCOUNT_TIP."</a>";
    }
    return $html;
  }
}

?>
