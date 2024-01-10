<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: ps_currency.php,v 1.4.2.1 2005/12/01 20:00:32 soeren_nb Exp $
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
* CLASS DESCRIPTION
*                   
* ps_currency
*
* The class is is used to manage the currencies in your store.
*
*************************************************************************/
 class ps_currency {
   var $classname = "ps_currency";
   var $error;
   
   function validate_add($d) {
     
     $db = new ps_DB;
     
     if (!$d["currency_name"]) {
       $this->error = "ERROR:  You must enter a name for the currency.";
       return False;	
     }
     if (!$d["currency_code"]) {
       $this->error = "ERROR:  You must enter a code for the currency.";
       return False;	
     }

     if ($d["currency_name"]) {
       $q = "SELECT count(*) as rowcnt from #__{vm}_currency where";
       $q .= " currency_name='" .  $d["currency_name"] . "'";
       $db->setQuery($q);
       $db->query();
       $db->next_record();
       if ($db->f("rowcnt") > 0) {
	 $this->error = "The given currency name already exists.";
	 return False;
       }      
     }
     return True;    
   }
  
  /**************************************************************************
  ** name: validate_delete()
  ** created by: soeren
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/   
  function validate_delete($d) {
    
    if (!$d["currency_id"]) {
      $this->error = "ERROR:  Please select a currency to delete.";
      return False;
    }
    else {
      return True;
    }
  }
  
  /**************************************************************************
  ** name: validate_update
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/   
  function validate_update($d) {
    
    if (!$d["currency_name"]) {
      $this->error = "ERROR:  You must enter a name for the currency.";
      return False;	
    }
    if (!$d["currency_code"]) {
      $this->error = "ERROR:  You must enter a code for the currency.";
      return False;	
    }
  
   return true;
  }
  
  
  /**************************************************************************
   * name: add()
   * created by: soeren
   * description: creates a new currency record
   * parameters:
   * returns:
   **************************************************************************/
  function add(&$d) {
    $hash_secret="PHPShopIsCool";
    $db = new ps_DB;
    $timestamp = time();
    
    if (!$this->validate_add($d)) {
      $d["error"] = $this->error;
      return False;
    }
    $q = "INSERT INTO #__{vm}_currency (currency_name, currency_code)";
    $q .= " VALUES ('";
    $q .= $d["currency_name"] . "','";
    $q .= $d["currency_code"] . "')";
    $db->setQuery($q);
    $db->query();
    $db->next_record();
    return True;

  }
  
  /**************************************************************************
   * name: update()
   * created by: soeren
   * description: updates currency information
   * parameters:
   * returns:
   **************************************************************************/
  function update(&$d) {
    $db = new ps_DB;
    $timestamp = time();

    if (!$this->validate_update($d)) {
      $d["error"] = $this->error;
      return False;	
    }
    $q = "UPDATE #__{vm}_currency set ";
    $q .= "currency_name='" . $d["currency_name"];
    $q .= "',currency_code='" . $d["currency_code"]."' ";
    $q .= "WHERE currency_id='".$d["currency_id"]."'";
    $db->setQuery($q);
    $db->query();
    $db->next_record();
    return True;
  }

  	/**
	* Controller for Deleting Records.
	*/
	function delete(&$d) {
    
		if (!$this->validate_delete($d)) {
			$d["error"]=$this->error;
			return False;
		}	
		$record_id = $d["currency_id"];
		
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
	
		$q = "DELETE from #__{vm}_currency where currency_id='$record_id'";
		$db->query($q);
		return True;
  }
  
  

}

?>