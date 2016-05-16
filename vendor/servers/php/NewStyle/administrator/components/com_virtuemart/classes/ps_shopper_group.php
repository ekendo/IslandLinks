<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_shopper_group.php,v 1.6.2.1 2006/01/15 19:37:05 soeren_nb Exp $
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

class ps_shopper_group {
	var $classname = "ps_shopper_group";
	var $id = "";
	var $error;

	/**************************************************************************
	** name: validate
	** created by:
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function validate_add($d) {
		$db = new ps_DB;
		$ps_vendor_id = $_SESSION["ps_vendor_id"];

		if (empty($d["shopper_group_name"])) {
			$this->error = "ERROR:  You must enter a shopper group name.";
			return False;
		}
		else {
			$q = "SELECT count(*) as num_rows from #__{vm}_shopper_group";
			$q .= " WHERE shopper_group_name='" . $d["shopper_group_name"] . "'";
			$q .= " AND vendor_id='" . $ps_vendor_id . "'";

			$db->query($q);
			$db->next_record();
			if ($db->f("num_rows") > 0) {
				$this->error = "ERROR:  Shopper group already exists for this vendor.";
				return False;
			}
			else {
				return True;
			}
		}
		if (empty($d["shopper_group_discount"])) {
			$d["shopper_group_discount"] = 0;
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

		if (!$d["shopper_group_name"]) {
			$this->error = "ERROR:  You must enter a shopper group name.";
			return False;
		}
		if (empty($d["shopper_group_discount"])) {
			$d["shopper_group_discount"] = 0;
		}

		return True;
	}

	/**************************************************************************
	** name: validate
	** created by:
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function validate_delete( $shopper_group_id, &$d) {

		$db = new ps_DB;

		if (!$shopper_group_id) {
			$d["error"] = "ERROR:  Please select a shopper group to delete.";
			return False;
		}

		$q = "SELECT * FROM #__{vm}_shopper_group WHERE shopper_group_id='";
		$q .= $shopper_group_id . "' AND `default`='1'";
		$db->query($q);
		if ($db->next_record()) {
			$d["error"] = "ERROR:  Cannot delete the default shopper group.";
			return False;
		}

		return True;
	}

	/**************************************************************************
	* name: add()
	* created by:
	* description:
	* parameters:
	* returns:
	**************************************************************************/
	function add(&$d) {
		global $perm;
		$hash_secret = "virtuemart";
		if( $perm->check( "admin" ) ) {
			$vendor_id = $d["vendor_id"];
		}
		else {
			$vendor_id = $_SESSION["ps_vendor_id"];
		}

		$db = new ps_DB;
		$timestamp = time();
		$default = @$d["default"]=="1" ? "1" : "0";

		if ($this->validate_add($d)) {
			$user_id=md5(uniqid($hash_secret));

			$q = "INSERT INTO #__{vm}_shopper_group (shopper_group_name, shopper_group_desc, shopper_group_discount, vendor_id, show_price_including_tax, `default`) ";
			$q .= "VALUES ('";
			$q .= $d["shopper_group_name"] . "','";
			$q .= $d["shopper_group_desc"] . "','";
			$q .= $d["shopper_group_discount"] . "','";
			$q .= $vendor_id."', '";
			$q .= $d["show_price_including_tax"]."','";
			$q .= $default . "')";
			$db->query($q);
			$db->next_record();

			$q = "SELECT * from #__{vm}_shopper_group where";
			$q .= " shopper_group_name='";
			$q .= $d["shopper_group_name"] . "' ";
			$q .= "AND shopper_group_desc='" . $d["shopper_group_desc"] ."'";
			$q .= "AND vendor_id='$vendor_id'";
			$db->query($q);
			$db->next_record();
			return $db->f("shopper_group_id");
		}
		else {
			$d["error"]=$this->error;
			return False;
		}
	}

	/**************************************************************************
	* name: update()
	* created by:
	* description:
	* parameters:
	* returns:
	**************************************************************************/
	function update($d) {
		global $perm;

		if( $perm->check( "admin" ) ) {
			$vendor_id = $d["vendor_id"];
		}
		else {
			$vendor_id = $_SESSION["ps_vendor_id"];
		}
		$db = new ps_DB;
		$timestamp = time();
		$default = @$d["default"]=="1" ? "1" : "0";

		if ($this->validate_update($d)) {

			$q = "UPDATE #__{vm}_shopper_group set shopper_group_name='" . $d["shopper_group_name"] . "', ";
			$q .= "shopper_group_desc='" . $d["shopper_group_desc"] . "', ";
			$q .= "shopper_group_discount='" . $d["shopper_group_discount"] . "', ";
			$q .= "show_price_including_tax='" . $d["show_price_including_tax"] . "', ";
			$q .= "vendor_id='$vendor_id', ";
			$q .= "`default`='" . $default . "' ";
			$q .= "WHERE shopper_group_id='" . $d["shopper_group_id"] . "'";
			$db->query($q);
			$db->next_record();
			if ($default == "1") {
				$q = "UPDATE #__{vm}_shopper_group ";
				$q .= "SET `default`='0' ";
				$q .= "WHERE shopper_group_id != '" . $d["shopper_group_id"] . "' ";
				$q .= "AND vendor_id = '$vendor_id' ";
				$db->query($q);
				$db->next_record();
			}
			return true;
		}
		else {
			$d["error"] = $this->error;
			return False;
		}
	}

	/**
	* Controller for Deleting Records.
	*/
	function delete(&$d) {

		$record_id = $d["shopper_group_id"];

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

		if ($this->validate_delete( $record_id, $d)) {
			$q = "DELETE FROM #__{vm}_shopper_group WHERE shopper_group_id='$record_id'";
			$db->query($q);
			$db->next_record();

			$q = "DELETE FROM #__{vm}_shopper_vendor_xref WHERE shopper_group_id='$record_id'";
			$db->query($q);
			$db->next_record();

			$q = "DELETE FROM #__{vm}_product_price WHERE shopper_group_id='$record_id'";
			$db->query($q);
			$db->next_record();
			return True;
		}
		else {
			return False;
		}
	}

	/**************************************************************************
	** name: list_shopper_groups
	** created by:
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function list_shopper_groups($name,$shopper_group_id='0',$product_id='0') {
		$ps_vendor_id = $_SESSION["ps_vendor_id"];
		global $perm;
		$db = new ps_DB;

		echo "<select class=\"inputbox\" name=\"$name\">\n";


		if( !$perm->check("admin")) {
			$q  = "SELECT shopper_group_id,shopper_group_name,vendor_id,'' AS vendor_name FROM #__{vm}_shopper_group ";
			$q .= "WHERE vendor_id = '$ps_vendor_id' ";
		}
		else {
			$q  = "SELECT shopper_group_id,shopper_group_name,#__{vm}_shopper_group.vendor_id,vendor_name FROM #__{vm}_shopper_group ";
			$q .= ",#__{vm}_vendor WHERE #__{vm}_shopper_group.vendor_id = #__{vm}_vendor.vendor_id ";
		}
		$q .= "ORDER BY shopper_group_name";
		$db->query($q);
		while ($db->next_record()) {
			if ($db->f("shopper_group_id") == $shopper_group_id) {
				$selected= ' selected="selected"';
			}
			else {
				$selected = '';
			}
			echo "<option value=\"" . $db->f("shopper_group_id")  . "\"$selected>";
			echo $db->f("shopper_group_name") . '; '.$db->f('vendor_name').' (Vendor ID: '.$db->f('vendor_id').")</option>\n";
			
		}
		echo "</select>\n";
	}

	/**************************************************************************
	** name: get_field
	** created by: pablo
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function get_field($shopper_group_id, $field_name) {
		$db = new ps_DB;

		$q =  "SELECT $field_name FROM shopper_group ";
		$q .= "WHERE shopper_group_id='$shopper_group_id'";
		$db->query($q);
		if ($db->next_record()) {
			return $db->f($field_name);
		}
		else {
			return False;
		}
	}
	/**************************************************************************
	** name: get_id
	** created by: ekkehard
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function get_id() {
		$auth = $_SESSION['auth'];

		$db = new ps_DB;

		$q =  "SELECT #__{vm}_shopper_group.shopper_group_id FROM #__{vm}_shopper_group,#__{vm}_shopper_vendor_xref ";
		$q .= "WHERE #__{vm}_shopper_vendor_xref.user_id='" . $auth["user_id"] . "' ";
		$q .= "AND #__{vm}_shopper_group.shopper_group_id=#__{vm}_shopper_vendor_xref.shopper_group_id";
		$db->query($q);
		$db->next_record();

		return $db->f("shopper_group_id");


	}

	/**************************************************************************
	** name: get_shoppergroup_by_id
	** created by: ekkehard
	** description:
	** parameters:
	** returns:
	***************************************************************************/
  	function get_shoppergroup_by_id($id, $default_group = false) {
    	global $my;
    	$ps_vendor_id = $_SESSION['ps_vendor_id'];
    	$db = new ps_DB;

    	$q =  "SELECT #__{vm}_shopper_group.shopper_group_id, show_price_including_tax, `default`, shopper_group_discount 
    		FROM `#__{vm}_shopper_group`";
    	if( !empty( $my->id ) && !$default_group) {
      		$q .= ",`#__{vm}_shopper_vendor_xref`";
      		$q .= " WHERE #__{vm}_shopper_vendor_xref.user_id='" . $id . "' AND ";
      		$q .= "#__{vm}_shopper_group.shopper_group_id=#__{vm}_shopper_vendor_xref.shopper_group_id";
    	}
    	else {
    		$q .= " WHERE #__{vm}_shopper_group.vendor_id='$ps_vendor_id' AND `default`='1'";
    	}
    	$db->query($q);
    	if ($db->next_record()){ //not sure that is is filled in database (Steve)
            $group["shopper_group_id"] = $db->f("shopper_group_id");
            $group["shopper_group_discount"] = $db->f("shopper_group_discount");
            $group["show_price_including_tax"] = $db->f("show_price_including_tax");
            $group["default_shopper_group"] = $db->f("default");
        }
        else {
			$q = "SELECT #__{vm}_shopper_group.shopper_group_id, show_price_including_tax, `default`, shopper_group_discount 
    				FROM `#__{vm}_shopper_group`
    				WHERE #__{vm}_shopper_group.vendor_id='$ps_vendor_id' AND `default`='1'";
			$db->query($q);
			$db->next_record();
			$group["shopper_group_id"] = $db->f("shopper_group_id");
            $group["shopper_group_discount"] = $db->f("shopper_group_discount");
            $group["show_price_including_tax"] = $db->f("show_price_including_tax");
            $group["default_shopper_group"] = $db->f("default");
	    	
        }
    	return $group;
  	}
	/**************************************************************************
	** name: get_customer_num
	** created by: soeren
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function get_customer_num($id) {

		$db = new ps_DB;

		$q =  "SELECT customer_number FROM #__{vm}_shopper_vendor_xref ";
		$q .= "WHERE user_id='" . $id . "' ";
		$db->query($q);
		$db->next_record();

		return $db->f("customer_number");

	}


}
$ps_shopper_group = new ps_shopper_group;

?>
