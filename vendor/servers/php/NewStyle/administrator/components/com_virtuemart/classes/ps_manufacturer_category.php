<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_manufacturer_category.php,v 1.4.2.1 2006/03/14 18:42:11 soeren_nb Exp $
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
* ps_manufacturer_category
*
* The class is is used to manage the manufacturer categories in your store.
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
class ps_manufacturer_category {
	var $classname = "ps_manufacturer_category";
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

		if (!$d["mf_category_name"]) {
			$this->error = "ERROR:  You must enter a name for the manufacturer category.";
			return False;
		}

		else {
			$q = "SELECT count(*) as rowcnt from #__{vm}_manufacturer_category where";
			$q .= " mf_category_name='" .  $d["mf_category_name"] . "'";
			$db->setQuery($q);
			$db->query();
			$db->next_record();
			if ($db->f("rowcnt") > 0) {
				$this->error = "The given manufacturer category name already exists.";
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

		if (!$d["mf_category_id"]) {
			$this->error = "ERROR:  Please select a manufacturer category to delete.";
			return False;
		}
		else {
			return True;
		}
	}

	/**************************************************************************
	** name: validate_update
	** created by: soeren
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function validate_update($d) {

		if (!$d["mf_category_name"]) {
			$this->error = "ERROR:  You must enter a name for the manufacturer category.";
			return false;
		}

		return true;
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
		$q = "INSERT INTO #__{vm}_manufacturer_category (mf_category_name, mf_category_desc)";
		$q .= " VALUES ('";
		$q .= $d["mf_category_name"] . "','";
		$q .= $d["mf_category_desc"]. "')";
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
		$q = "UPDATE #__{vm}_manufacturer_category set ";
		$q .= "mf_category_name='" . $d["mf_category_name"]."',";
		$q .= "mf_category_desc='" .$d["mf_category_desc"] . "' ";
		$q .= "WHERE mf_category_id='".$d["mf_category_id"]."'";
		$db->setQuery($q);
		$db->query();
		$db->next_record();
		return True;
	}

	/**************************************************************************
	* name: delete()
	* created by: soeren
	* description: Should delete a manufacturer record.
	* parameters:
	* returns:
	**************************************************************************/
	function delete(&$d) {

		if (!$this->validate_delete($d)) {
			$d["error"]=$this->error;
			return False;
		}


		$record_id = $d["mf_category_id"];

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

		$q = "DELETE from #__{vm}_manufacturer_category where mf_category_id='$record_id'";
		$db->query($q);
		return True;
	}

	/**************************************************************************
	* name: list_category()
	* created by: soeren
	* description: Creates a list of Manufacturer Categories to be used in a drop down list
	* parameters:
	* returns: array of values
	**************************************************************************/
	function list_category($mf_category_id='0') {
		global $VM_LANG;

		$db = new ps_DB;

		$q = "SELECT count(*) as rowcnt FROM #__{vm}_manufacturer_category ORDER BY mf_category_name";
		$db->query($q);
		$db->next_record();
		$rowcnt = $db->f("rowcnt");


		$q = "SELECT * FROM #__{vm}_manufacturer_category ORDER BY mf_category_name";
		$db->query($q);
		$code = "<select name=\"mf_category_id\" class=\"inputbox\">\n";
		if ( $rowcnt > 1) {
			$code .= "<option value=\"0\">".$VM_LANG->_PHPSHOP_SELECT."</option>\n";
		}
		while ($db->next_record()) {
			$code .= "  <option value=\"" . $db->f("mf_category_id") . "\"";
			if ($db->f("mf_category_id") == $mf_category_id) {
				$code .= " selected=\"selected\" ";
			}
			$code .= ">" . $db->f("mf_category_name") . "</option>\n";
		}
		$code .= "</select>\n";

		echo $code;
	}

}

?>