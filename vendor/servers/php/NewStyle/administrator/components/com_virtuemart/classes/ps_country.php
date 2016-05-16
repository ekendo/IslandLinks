<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: ps_country.php,v 1.4 2005/09/29 20:01:13 soeren_nb Exp $
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
* ps_country
*
* The class is is used to manage the countries in your store.
* 
* properties:  
* 	
*       error - the error message returned by validation if any
* methods:
*       validate_add()
*	validate_delete()
*	validate_update()
*       add()
*       update()
*       delete()
*	
*
*************************************************************************/
 class ps_country {
   var $classname = "ps_country";
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
     
     if (!$d["country_name"]) {
       $this->error = "ERROR:  You must enter a name for the country.";
       return False;	
     }
     if (!$d["country_2_code"]) {
       $this->error = "ERROR:  You must enter a 2 symbol code for the country.";
       return False;	
     }
    if (!$d["country_3_code"]) {
      $this->error = "ERROR:  You must enter a 3 symbol code for the country.";
      return False;	
    }
    
     if ($d["country_name"]) {
       $q = "SELECT count(*) as rowcnt from #__{vm}_country where";
       $q .= " country_name='" .  $d["country_name"] . "'";
       $db->setQuery($q);
       $db->query();
       $db->next_record();
       if ($db->f("rowcnt") > 0) {
	 $this->error = "The given country name already exists.";
	 return False;
       }      
     }
     return True;    
   }
  
  /**************************************************************************
  ** name: validate_delete()
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/   
  function validate_delete($d) {
    
    if (!$d["country_id"]) {
      $this->error = "ERROR:  Please select a country to delete.";
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
    
    if (!$d["country_name"]) {
      $this->error = "ERROR:  You must enter a name for the country.";
      return False;	
    }
    if (!$d["country_2_code"]) {
      $this->error = "ERROR:  You must enter a 2 symbol code for the country.";
      return False;	
    }
      if (!$d["country_3_code"]) {
      $this->error = "ERROR:  You must enter a 3 symbol code for the country.";
      return False;	
    }
   return true;
  }
  
  
  /**************************************************************************
   * name: add()
   * created by: pablo
   * description: creates a new country record
   * parameters:
   * returns:
   **************************************************************************/
  function add(&$d) {
    
    $db = new ps_DB;
    
    if (!$this->validate_add($d)) {
      $d["error"] = $this->error;
      return False;
    }
    $q = "INSERT INTO #__{vm}_country (country_name, zone_id, country_3_code, country_2_code)";
    $q .= " VALUES ('";
    $q .= $d["country_name"] . "','";
    $q .= $d["zone_id"] . "','";
    $q .= $d["country_3_code"] . "','";
    $q .= $d["country_2_code"] . "')";
    $db->setQuery($q);
    $db->query();
    $db->next_record();
    return True;

  }
  
  /**************************************************************************
   * name: update()
   * created by: pablo
   * description: updates country information
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
    $q = "UPDATE #__{vm}_country set ";
    $q .= "country_name='" . $d["country_name"]."',";
    $q .= "zone_id='" . $d["zone_id"]."',";
    $q .= "country_3_code='" . $d["country_3_code"]."', ";
    $q .= "country_2_code='" . $d["country_2_code"]."' ";
    $q .= "WHERE country_id='".$d["country_id"]."'";
    $db->setQuery($q);
    $db->query();
    $db->next_record();
    return True;
  }

  /**************************************************************************
   * name: delete()
   * created by: pablo
   * description: Should delete a country record.
   * parameters: 
   * returns:
   **************************************************************************/
  function delete(&$d) {
    $db = new ps_DB;
    
    if (!$this->validate_delete($d)) {
      $d["error"]=$this->error;
      return False;
    }
	if( is_array( $d["country_id"])) {
		foreach($d["country_id"] as $country ) {
			$q = "DELETE FROM #__{vm}_country WHERE country_id='$country'";
			$db->query($q);
		}
	}
	else {
		$q = "DELETE FROM #__{vm}_country WHERE country_id='" . $d["country_id"] . "'";
		$db->query($q);
	}
    return True;
  }
  
  function addState( &$d ) {
    
    $db = new ps_DB;
    if ( empty($d['country_id']) ) {
      $d["error"] = "Error: No country was selected for this State";
      return False;
    }
    $q = "INSERT INTO #__{vm}_state (state_name, country_id, state_3_code, state_2_code)";
    $q .= " VALUES ('";
    $q .= $d["state_name"] . "','";
    $q .= $d["country_id"] . "','";
    $q .= $d["state_3_code"] . "','";
    $q .= $d["state_2_code"] . "')";
    $db->query( $q );
    
    return True;
    
  }

  function updateState( &$d ) {
    $db = new ps_DB;

    if (empty($d['state_id']) ||empty($d['country_id']) ) {
      $d["error"] = "Please select a state or country for update!";
      return False;	
    }
    $q = "UPDATE #__{vm}_state SET ";
    $q .= "state_name='" . $d["state_name"]."',";
    $q .= "state_3_code='" . $d["state_3_code"]."', ";
    $q .= "state_2_code='" . $d["state_2_code"]."' ";
    $q .= "WHERE state_id='".$d["state_id"]."'";
    $db->query( $q );
    
    return True;
  
  }
  
  function deleteState( &$d ) {
  
    $db = new ps_DB;
    
    if (empty( $d['state_id'])) {
      $d["error"]= "Please select a state to delete!";
      return false;
    }
    $q = "DELETE FROM #__{vm}_state where state_id='" . $d["state_id"] . "' LIMIT 1";
    $db->query($q);
    
    return True;
  }
  
}

?>
