<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_manufacturer.php,v 1.4.2.2 2006/03/14 18:42:11 soeren_nb Exp $
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
* ps_manufacturer
*
* The class is is used to manage the manufacturers in your store.
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
class ps_manufacturer {
	var $classname = "ps_manufacturer";
	var $error;

	/**************************************************************************
	** name: validate_add()
	** created by: soeren
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function validate_add($d) {

		$db = new ps_DB;

		if (!$d["mf_name"]) {
			$this->error = "ERROR:  You must enter a name for the manufacturer.";
			return False;
		}
		else {
			$q = "SELECT count(*) as rowcnt from #__{vm}_manufacturer where";
			$q .= " mf_name='" .  $d["mf_name"] . "'";
			$db->setQuery($q);
			$db->query();
			$db->next_record();
			if ($db->f("rowcnt") > 0) {
				$this->error = "The given manufacturer name already exists.";
				return False;
			}
		}
		return True;
        }

        /**************************************************************************
        ** name: validate_update
        ** created by: soeren
        ** description:
        ** parameters:
        ** returns:
        ***************************************************************************/
        function validate_update($d) {

                if (!$d["mf_name"]) {
                        $this->error = "ERROR:  You must enter a name for the manufacturer.";
                        return False;
                }

                return true;
        }

        /**************************************************************************
        ** name: validate_delete()
        ** created by: soeren
        ** description:
        ** parameters:
        ** returns:
        ***************************************************************************/
        function validate_delete($mf_id) {
                global $db;

                if (empty( $mf_id )) {
                        $this->error = "ERROR:  Please select a manufacturer to delete.";
                        return False;
                }
                $db->query( "SELECT jos_vm_product.product_id, manufacturer_id
                                                FROM jos_vm_product, jos_vm_product_mf_xref
                                                WHERE manufacturer_id =".intval($mf_id)."
                                                AND jos_vm_product.product_id = jos_vm_product_mf_xref.product_id" );
                if( $db->num_rows() > 0 ) {
                        $this->error = "Error: This Manufacturer still has products assigned to it.";
                        return false;
                }
                return True;

        }


	/**************************************************************************
	* name: add()
	* created by: soeren
	* description: creates a new manufacturer record
	* parameters:
	* returns:
	**************************************************************************/
	function add(&$d) {

                $db = new ps_DB;
                
                $GLOBALS['vmInputFilter']->safeSQL( $d );
                
                if (!$this->validate_add($d)) {
                        $d["error"] = $this->error;
                        return false;
		}
		$q = "INSERT INTO #__{vm}_manufacturer (mf_name, mf_email, mf_desc, mf_category_id, mf_url)";
		$q .= " VALUES ('";
		$q .= $d["mf_name"] . "','";
		$q .= $d["mf_email"] . "','";
		$q .= $d["mf_desc"] . "','";
		$q .= $d["mf_category_id"] . "','";
		$q .= $d["mf_url"] . "')";
		$db->setQuery($q);
		$db->query();
		$db->next_record();
		return True;

	}

	/**************************************************************************
	* name: update()
	* created by: soeren
	* description: updates manufacturer information
	* parameters:
	* returns:
	**************************************************************************/
	function update(&$d) {
                $db = new ps_DB;
                $timestamp = time();

                $GLOBALS['vmInputFilter']->safeSQL( $d );
                
                if (!$this->validate_update($d)) {
                        $d["error"] = $this->error;
                        return False;
		}
		$q = "UPDATE #__{vm}_manufacturer set ";
		$q .= "mf_name='" . $d["mf_name"]."',";
		$q .= "mf_email='" .$d["mf_email"] . "',";
		$q .= "mf_desc='" .$d["mf_desc"] . "',";
		$q .= "mf_category_id='" .$d["mf_category_id"] . "',";
		$q .= "mf_url='" .$d["mf_url"] . "' ";
		$q .= "WHERE manufacturer_id='".$d["manufacturer_id"]."'";
		$db->setQuery($q);
		$db->query();
		$db->next_record();
		return True;
	}

	/**
	* Controller for Deleting Records.
	*/
	function delete(&$d) {

		$record_id = $d["manufacturer_id"];

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
                $q = "DELETE from #__{vm}_product_mf_xref WHERE manufacturer_id='$record_id'";
                $db->query($q);
                $q = "DELETE from #__{vm}_manufacturer WHERE manufacturer_id='$record_id'";
                $db->query($q);
                return True;
        }

}

?>