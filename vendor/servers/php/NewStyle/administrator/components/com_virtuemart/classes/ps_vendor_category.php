<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: ps_vendor_category.php,v 1.4 2005/09/29 20:01:14 soeren_nb Exp $
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

class ps_vendor_category {

  var $classname = "ps_vendor_category";
  var $error;


  /**************************************************************************
  ** name: validate
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/  
  function validate_add($d) {
    
    if (!$d["vendor_category_name"]) {
      $d["error"] = "You must enter a name for the vendor category.";
      return False;	
    }
    else {
      return True;
    }
  }
  
  /**************************************************************************
  ** name: validate
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/  
  function validate_delete($d) {

    if (!$d["vendor_category_id"]) {
      $d["error"] = "Please select a vendor category to delete.";
      return False;
    }
    else {
      return True;
    }
  }

  /**************************************************************************
  ** name: validate
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/  
  function validate_update($d) {
    
    if (!$d["vendor_category_name"]) {
      $d["error"] = "You must enter a name for the vendor category.";
      return False;	
    }
    else {
      return True;
    }
  }



  /**************************************************************************
   * name: add()
   * created by:
   * description:
   * parameters:
   * returns:
   **************************************************************************/
  function add(&$d) {
    $db = new ps_DB;

    if (!$this->validate_add($d)) {
      return False;
    }
    foreach ($d as $key => $value)
        $d[$key] = addslashes($value);
        
    $q = "INSERT INTO #__{vm}_vendor_category (";
    $q .= "vendor_category_name,";
    $q .= "vendor_category_desc) VALUES ('";
    $q .= $d["vendor_category_name"] . "','";
    $q .= $d["vendor_category_desc"] . "')";
    $db->query($q);
    $db->next_record();
    return True;
    
  }
  
  /**************************************************************************
   * name: update()
   * created by:
   * description:
   * parameters:
   * returns:
   **************************************************************************/
  function update(&$d) {
    $db = new ps_DB;
    
    if (!$this->validate_update($d)) {
      return False;
    }
    
    foreach ($d as $key => $value)
        $d[$key] = addslashes($value);
        
    $q = "UPDATE #__{vm}_vendor_category ";
    $q .= "set vendor_category_name='" . $d["vendor_category_name"] . "',";
    $q .= "vendor_category_desc='" . $d["vendor_category_desc"];
    $q .= "' WHERE vendor_category_id='" . $d["vendor_category_id"] . "'";
    $db->query($q);
    $db->next_record();    
    return True;
  }
  
	/**
	* Controller for Deleting Records.
	*/
	function delete(&$d) {
	
		$record_id = $d["vendor_category_id"];
		
		if (!$this->validate_delete($d)) {
		  return False;
		}
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
		
		$q = "DELETE FROM #__{vm}_vendor_category WHERE vendor_category_id='$record_id'";
		$db->query($q);
		$db->next_record();
		return True;
	}


  /**************************************************************************
   * name: list_category()
   * created by: jep
   * description: Creates a list of Vendor Categories to be used in SELECT.
   * parameters:
   * returns: array of values
   **************************************************************************/
  function list_category($vendor_category_id=0) {
    
    // Create an array for a form drop down list using OOHFORMS
    $db = new ps_DB;
    // Creates a form drop down list and prints it
    $db = new ps_DB;
    
    $q = "SELECT count(*) as rowcnt FROM #__{vm}_vendor_category ORDER BY vendor_category_name";
    $db->query($q);
    $db->next_record();
    $rowcnt = $db->f("rowcnt");


    $q = "SELECT * FROM #__{vm}_vendor_category ORDER BY vendor_category_name";
    $db->query($q);                                                                                     
    $code = "<select name=vendor_category_id>\n";
    if ( $rowcnt > 1) {
      $code .= "<option value=\"0\">Please Select</option>\n";      
    }   
    while ($db->next_record()) {
      $code .= "  <option value=\"" . $db->f("vendor_category_id") . "\"";
      if ($db->f("vendor_category_id") == $vendor_category_id) { 
        $code .= " selected"; 
      }
      $code .= ">" . $db->f("vendor_category_name") . "</option>\n";
    }
    $code .= "</select>\n";
    print $code;
  }
  
}
$ps_vendor_category = new ps_vendor_category;
?>
