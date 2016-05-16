<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: ps_module.php,v 1.6.2.4 2006/03/14 18:42:11 soeren_nb Exp $
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

class ps_module {
  var $classname = "ps_module";
  var $error;
  

/**************************************************************************
   * name: validate_add()
   * created by: pablo
   * description: validate the given data before adding a function record
   * parameters:
   * returns:
**************************************************************************/

  function validate_add(&$d) {
    global $db;
    
    if (!$d[ 'module_name' ]) {
      $this->error = "ERROR:  You must enter a name for the module.";
      return False;	
    }
    if ($d[ 'module_name' ]) {
      $q = "SELECT count(*) as rowcnt from #__{vm}_module where module_name='" .  $d[ 'module_name' ] . "'";
      $db->setQuery($q);
      $db->next_record();
      if ($db->f("rowcnt") > 0) {
	$this->error = "The given module name already exists.";
	return False;
      }      
    }
    
    if (!$d[ 'module_perms' ]) {
      $this->error = "ERROR:  You must enter permissions for the module.";
      return false;	
    }
    if (!$d[ 'list_order' ]) {
      $d[ 'list_order' ] = "99";
    }
    return True;    
  }

/**************************************************************************
   * name: validate_delete()
   * created by: pablo
   * description: validate the given data before deleting a function record
   * parameters:
   * returns:
**************************************************************************/

  function validate_delete($module_id) {
    global $db;
	
    if (empty($module_id)) {
      $this->error = "ERROR:  Please select a module to delete.";
      return False;
    }
	
    $db->query( "SELECT module_name FROM #__{vm}_module WHERE module_id='$module_id'" );
	$db->next_record();
	$name = $db->f("module_name");
	if( $name == "shop" || $name == "vendor" || $name == "product" || $name == "store" || $name == "order" || $name == "admin"
		|| $name == "checkout" || $name == "account" ) {
		$this->error = "Error: The module $name is a core module. It cannot be deleted.";
		return false;
	}
	return True;
    
  }
  
  
/**************************************************************************
   * name: validate_update()
   * created by: pablo
   * description: validate the given data before updating a function record
   * parameters:
   * returns:
**************************************************************************/

  function validate_update(&$d) {
    
    if (!$d[ 'module_name' ]) {
      $this->error = "ERROR:  You must enter a name for the module.";
      return False;	
    }
    if (!$d[ 'module_perms' ]) {
      $this->error = "ERROR:  You must enter permissions for the module.";
      return False;	
    }
    if (!$d[ 'list_order' ]) {
      $d[ 'list_order' ] = "99";
    }
    return True;
  }
  
  
  /**************************************************************************
   * name: add()
   * created by: pablo
   * description: creates a new function record
   * parameters:
   * returns:
   **************************************************************************/
  function add(&$d) {
	global $db;
  
    $hash_secret="PHPShopIsCool";
    
    $timestamp = time();
    
    if (!$this->validate_add($d)) {
      $d[ 'error' ] = $this->error;
      return False;
    }
    
    foreach ($d as $key => $value)
        $d[$key] = addslashes($value);
        
    $q = "INSERT INTO #__{vm}_module (module_name, module_description, ";
    $q .= "module_perms, ";
    $q .= "module_publish, list_order) ";
    $q .= " VALUES ('";
    $q .= $d[ 'module_name' ] . "','";
    $q .= $d[ 'module_description' ] . "','";
    $q .= $d[ 'module_perms' ] . "','";
    $q .= $d[ 'module_publish' ] . "','";
    $q .= $d[ 'list_order' ] . "' )";
    
    $db->setQuery($q);
    $db->query();
    return True;

  }
  
  /**************************************************************************
   * name: update()
   * created by: pablo
   * description: updates function information
   * parameters:
   * returns:
   **************************************************************************/
  function update(&$d) {
    global $db;
    
    $timestamp = time();

    if (!$this->validate_update($d)) {
      $d[ 'error' ] = $this->error;
      return False;	
    }
    
    foreach ($d as $key => $value) {
        if (!is_array($value))
          $d[$key] = addslashes($value);
    }
        
    $q = "UPDATE #__{vm}_module SET ";
    $q .= "module_name='" . $d[ 'module_name' ];
    $q .= "',module_perms='" . $d[ 'module_perms' ];
    $q .= "',module_description='" . $d[ 'module_description' ];
    $q .= "',module_publish='" . $d[ 'module_publish' ];
    $q .= "',list_order='" . $d[ 'list_order' ];
    $q .= "' WHERE module_id='" . $d[ 'module_id' ] . "'";
    
    $db->setQuery($q);
    
    $db->query();
    
    return true;
  }

	/**
	* Controller for Deleting Records.
	*/
	function delete(&$d) {
		
		$record_id = $d["module_id"];
		
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
			$d[ 'error' ]=$this->error;
			return False;
		}
		
		$q = "DELETE from #__{vm}_function WHERE module_id='$record_id'";
		$db->query($q);
	
		$q = "DELETE FROM #__{vm}_module where module_id='$record_id'";
		$db->query($q);
		return true;
	
	}

  /**************************************************************************
   * name: get_dir()
   * created by: pablo
   * description: 
   * parameters: 
   * returns:
   **************************************************************************/
  function get_dir($basename) {
    $datab = new ps_DB;
    
    $results = array();
    
    $q = "SELECT module_perms FROM #__{vm}_module where module_name='".$basename."'";
    $datab->query($q);
    
    if ($datab->next_record()) {
      $results[ 'perms' ] = $datab->f("module_perms");
      return $results;
    }
    else {
      return false;
    }
  }
  
  function checkModulePermissions( $calledPage ) {
	
	global $page, $VM_LANG, $error_type, $vmLogger, $perm;
        
        // "shop.browse" => module: shop, page: browse
    $my_page= explode ( '.', $page );
        if( empty( $my_page[1] )) {
                return false;
        }
    $modulename = $my_page[0];
    $pagename = $my_page[1];
    
    
    $dir_list = $this->get_dir($modulename);
    
    if ($dir_list) {
		
		// Load MODULE-specific CLASS-FILES
        include_class( $modulename );
		
        if ($perm->check( $dir_list[ 'perms' ]) ) {
		
            if ( !file_exists(PAGEPATH.$modulename.".".$pagename.".php") ) {
            	define( '_VM_PAGE_NOT_FOUND', 1 );
                $error = $VM_LANG->_PHPSHOP_PAGE_404_1;
                $error .= ' '.$VM_LANG->_PHPSHOP_PAGE_404_2 ;
                $error .= ' "'.$modulename.".".$pagename.'.php"';
                $vmLogger->err( $error );
				return false;
            }
			return true;
        }
        else {
        	define( '_VM_PAGE_NOT_AUTH', 1 );
            $vmLogger->err( $VM_LANG->_PHPSHOP_MOD_NO_AUTH );
            return false;
        }
    }
    else {
        $error = $VM_LANG->_PHPSHOP_MOD_NOT_REG;
        $error .= '"'.$modulename .'" '. $VM_LANG->_PHPSHOP_MOD_ISNO_REG;
        $vmLogger->err( $error );
        return false;
    }
  
  }

}

?>