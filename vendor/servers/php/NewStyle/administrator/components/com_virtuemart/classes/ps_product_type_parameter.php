<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_product_type_parameter.php,v 1.5.2.1 2006/02/27 19:41:42 soeren_nb Exp $
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
* ps_product_type_parameter
*************************************************************************/
class ps_product_type_parameter {
	var $classname = "ps_product_type_parameter";


	/**************************************************************************
	** name: validate_add_parameter()
	** created by: Zdenek Dvorak
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function validate_add_parameter(&$d) {

		if (!$d["parameter_name"]) {
			$d["error"] = "ERROR:  You must enter a name for the Parameter.";
			return False;
		}
		if (!$d["parameter_label"]) {
			if ($d["parameter_type"]=="B") { // Break line
				$d["parameter_label"]=$d["parameter_name"];
			}
			else {
				$d["error"] = "ERROR:  You must enter a label for the Parameter.";
				return False;
			}
		}

		// field Value:
		if( @$d["parameter_multiselect"]=="Y" && $d["parameter_values"]=="" ) {
			$d["error"] = "ERROR:  If You checked Multiple select you must enter a Possible Values.";
			return False;
		}

		$db = new ps_DB;

		// find if there is not a column with the same name
		$q  = "SELECT COUNT(*) AS count FROM #__{vm}_product_type_parameter ";
		$q .= "WHERE product_type_id='".$d["product_type_id"]."' ";
		$q .= "AND parameter_name='".$d["parameter_name"]."'";
		$db->query( $q );
		$db->next_record();
		if ($db->f("count") != 0) {
			$d["error"] = "ERROR:  The Parameter with this name in this Product Type already exist.";
			return False;
		}

		return True;
	}

	/**************************************************************************
	** name: validate_delete_parameter()
	** created by: Zdenek Dvorak
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function validate_delete_parameter(&$d) {

		$db = new ps_DB;

		if (!$d["product_type_id"] || !$d["parameter_name"]) {
			$d["error"] = "ERROR:  Please select a Parameter to delete.";
			return False;
		}

		return True;
	}

	/**************************************************************************
	** name: validate_update_parameter()
	** created by: Zdenek Dvorak
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function validate_update_parameter(&$d) {

		if (!$d["parameter_name"]) {
			$d["error"] = "ERROR:  You must enter a name for the Parameter.";
			return False;
		}
		elseif (!$d["parameter_label"]) {
			$d["error"] = "ERROR:  You must enter a label for the Parameter.";
			return False;
		}
		// field Value:
		elseif( @$d["parameter_multiselect"]=="Y" && $d["parameter_values"]=="" ) {
			$d["error"] = "ERROR:  If You checked Multiple select you must enter a Possible Values.";
			return False;
		}

		elseif ( $d["parameter_name"] != $d["parameter_old_name"] ) {

			$db = new ps_DB;

			// find if there is not a column with the same name
			$q  = "SELECT COUNT(*) AS count FROM #__{vm}_product_type_parameter ";
			$q .= "WHERE product_type_id='".$d["product_type_id"]."' ";
			$q .= "AND parameter_name='".$d["parameter_name"]."'";
			$db->query( $q );
			$db->next_record();
			if ($db->f("count") != 0) {
				$d["error"] = "ERROR:  A Parameter with this name in this Product Type already exist.";
				return False;
			}
		}
		return True;
	}


	/**************************************************************************
	** name: add_parameter()
	** created by: Zdenek Dvorak
	** description: creates a new parameter of Product Type
	** parameters:
	** returns:
	***************************************************************************/
	function add_parameter(&$d) {
		$db = new ps_DB;

		if ($this->validate_add_parameter($d)) {
			foreach ($d as $key => $value) {
				if (!is_array($value))
				$d[$key] = addslashes($value);
			}
			// Let's find out the last product_type
			$q = "SELECT MAX(parameter_list_order) AS list_order FROM #__{vm}_product_type_parameter ";
			$q .="WHERE product_type_id='".$d["product_type_id"]."';";
			$db->query( $q );
			$db->next_record();
			$list_order = intval($db->f("list_order"))+1;

			// added for custom parameter modification
			// strips the trailing semi-colon from an values
			if (';' == substr($d["parameter_values"], strlen($d["parameter_values"])-1,1) ) {
				$d["parameter_values"] =substr($d["parameter_values"], 0, strlen($d["parameter_values"])-1);
			}
			if (empty($d["parameter_multiselect"])) {
				$d["parameter_multiselect"] = "N";
			}
			// delete "\n" from field parameter_description
			$d["parameter_description"] = str_replace("\r\n","",$d["parameter_description"]);
			$d["parameter_description"] = str_replace("\n","",$d["parameter_description"]);

			$q = "INSERT into #__{vm}_product_type_parameter (product_type_id, parameter_name, parameter_label, ";
			$q .= "parameter_description, parameter_list_order, parameter_type, parameter_values, parameter_multiselect, parameter_default, ";
			$q .= "parameter_unit) VALUES ('";
			$q .= $d["product_type_id"] . "','";
			$q .= $d["parameter_name"] . "','";
			$q .= $d["parameter_label"] . "','";
			$q .= $d["parameter_description"] . "','";
			$q .= $list_order . "','";
			$q .= $d["parameter_type"] . "','";
			$q .= $d["parameter_values"] . "','";
			$q .= $d["parameter_multiselect"] . "','";
			$q .= $d["parameter_default"] . "','";
			$q .= $d["parameter_unit"] . "')";
			$db->setQuery($q);
			$db->query();

			if ($d["parameter_type"]!="B") { // != Break Line
				// Make new column in table product_type_<id>
				$q = "ALTER TABLE `#__{vm}_product_type_";
				$q .= $d["product_type_id"] . "` ADD `";
				$q .= $d["parameter_name"]."` ";
				switch( $d["parameter_type"] ) {
					case "I": $q .= "int(11) "; break;	// Integer
					case "T": $q .= "text "; break; 	// Text
					case "S": $q .= "varchar(255) "; break; // Short Text
					case "F": $q .= "float "; break; 	// Float
					case "C": $q .= "char(1) "; break; 	// Char
					case "D": $q .= "datetime "; break; 	// Date & Time
					case "A": $q .= "date "; break; 	// Date
					case "V": $q .= "varchar(255) "; break; // Multiple Value
					case "M": $q .= "time "; break; 	// Time
					default: $q .= "varchar(255) ";		// Default type Short Text
				}
				if ($d["parameter_default"]!="" && $d["parameter_type"]!="T") {
					$q .= "DEFAULT '".$d["parameter_default"]."' NOT NULL;";
				}
				$db->setQuery($q);
				$db->query();

				// Make index for this column
				if ($d["parameter_type"]=="T") {
					$q  = "ALTER TABLE `#__{vm}_product_type_";
					$q .= $d["product_type_id"]."` ADD FULLTEXT `idx_product_type_".$d["product_type_id"]."_";
					$q .= $d["parameter_name"]."` (`".$d["parameter_name"]."`);";
					$db->setQuery($q);
					$db->query();
				}
				else {
					$q  = "ALTER TABLE `#__{vm}_product_type_";
					$q .= $d["product_type_id"]."` ADD KEY `idx_product_type_".$d["product_type_id"]."_";
					$q .= $d["parameter_name"]."` (`".$d["parameter_name"]."`);";
					$db->setQuery($q);
					$db->query();
				}
			}

			return true;
		}
		else {
			return False;
		}

	}

	/**************************************************************************
	** name: update_parameter()
	** created by: Zdenek Dvorak
	** description: updates Parameter information
	** parameters:
	** returns:
	***************************************************************************/
	function update_parameter(&$d) {
		$db = new ps_DB;

		if ($this->validate_update_parameter($d)) {
			if ($d["parameter_old_type"] == "B") {
				// delete record and call add_parameter()
				$q  = "DELETE FROM #__{vm}_product_type_parameter WHERE product_type_id='" . $d["product_type_id"] . "' ";
				$q .= "AND parameter_name='".$d["parameter_name"]."'";
				$db->setQuery($q);   $db->query();
				return $this->add_parameter($d);
			}
			foreach ($d as $key => $value) {
				if (!is_array($value))
				$d[$key] = addslashes($value);
			}
			// added for custom parameter modification
			// strips the trailing semi-colon from an values
			if (';' == substr($d["parameter_values"], strlen($d["parameter_values"])-1,1) ) {
				$d["parameter_values"] =substr($d["parameter_values"], 0, strlen($d["parameter_values"])-1);
			}
			if (empty($d["parameter_multiselect"])) {
				$d["parameter_multiselect"] = "N";
			}
			// delete "\n" from field parameter_description
			$d["parameter_description"] = str_replace("\r\n","",$d["parameter_description"]);
			$d["parameter_description"] = str_replace("\n","",$d["parameter_description"]);

			$q  = "UPDATE `#__{vm}_product_type_parameter` SET ";
			$q .= "`parameter_name`='".$d["parameter_name"]."',";
			$q .= "`parameter_label`='".$d["parameter_label"]."',";
			$q .= "`parameter_description`='".$d["parameter_description"]."',";
			$q .= "`parameter_list_order`='".$d["list_order"]."',";
			$q .= "`parameter_type`='".$d["parameter_type"]."',";
			$q .= "`parameter_values`='".$d["parameter_values"]."',";
			$q .= "`parameter_multiselect`='".$d["parameter_multiselect"]."',";
			$q .= "`parameter_default`='".$d["parameter_default"]."',";
			$q .= "`parameter_unit`='".$d["parameter_unit"]."' ";
			$q .= "WHERE `product_type_id`='" . $d["product_type_id"] . "' ";
			$q .= "AND `parameter_name`='".$d["parameter_old_name"]."';";
			$db->setQuery($q);
			$db->query();

			/* Re-Order the Parameter table IF the list_order has been changed */
			if( intval($d['list_order']) != intval($d['currentpos'])) {
				$dbu = new ps_DB;

				/* Moved UP in the list order */
				if( intval($d['list_order']) < intval($d['currentpos']) ) {

					$q  = "SELECT product_type_id,parameter_name FROM #__{vm}_product_type_parameter WHERE ";
					$q .= "product_type_id=' ".$d["product_type_id"];
					$q .= "' AND parameter_name <> '" . $d["parameter_name"];
					$q .= "' AND parameter_list_order >= '" . intval($d["list_order"]) . "'";
					$db->query( $q );

					while( $db->next_record() ) {
						$dbu->query("UPDATE #__{vm}_product_type_parameter SET parameter_list_order=parameter_list_order+1 WHERE product_type_id='".$db->f("product_type_id")."' AND parameter_name='".$db->f("parameter_name")."'");
					}
				}
				/* Moved DOWN in the list order */
				else {

					$q = "SELECT product_type_id,parameter_name FROM #__{vm}_product_type_parameter WHERE ";
					$q .= "product_type_id='" . $d["product_type_id"];
					$q .= "' AND parameter_name <> '".$d["parameter_name"];
					$q .= "' AND parameter_list_order > '" . intval($d["currentpos"]);
					$q .= "' AND parameter_list_order <= '" . intval($d["list_order"]) . "'";
					$db->query( $q );

					while( $db->next_record() ) {
						$dbu->query("UPDATE #__{vm}_product_type_parameter SET parameter_list_order=parameter_list_order-1 WHERE product_type_id='".$db->f("product_type_id")."' AND parameter_name='".$db->f("parameter_name")."'");
					}

				}
			} /* END Re-Ordering */

			if ($d["parameter_type"]!="B") { // != Break Line
				// Delete old index
				$q  = "ALTER TABLE `#__{vm}_product_type_";
				$q .= $d["product_type_id"]."` DROP INDEX `idx_product_type_".$d["product_type_id"]."_";
				$q .= $d["parameter_old_name"]."`;";
				$db->setQuery($q);
				$db->query();

				// Update column in table product_type_<id>
				$q  = "ALTER TABLE `#__{vm}_product_type_";
				$q .= $d["product_type_id"] . "` CHANGE `";
				$q .= $d["parameter_old_name"] . "` `";
				$q .= $d["parameter_name"]."` ";
				switch( $d["parameter_type"] ) {
					case "I": $q .= "int(11) "; break;	// Integer
					case "T": $q .= "text "; break; 	// Text
					case "S": $q .= "varchar(255) "; break; // Short Text
					case "F": $q .= "float "; break; 	// Float
					case "C": $q .= "char(1) "; break; 	// Char
					case "D": $q .= "datetime "; break; 	// Date & Time
					case "A": $q .= "date "; break; 	// Date
					case "V": $q .= "varchar(255) "; break; // Multiple Value
					case "M": $q .= "time "; break; 	// Time
					default: $q .= "varchar(255) ";		// Default type Short Text
				}
				if ($d["parameter_default"]!="" && $d["parameter_type"]!="T") {
					$q .= "DEFAULT '".$d["parameter_default"]."' NOT NULL;";
				}
				$db->setQuery($q);
				$db->query();

				// Make index for this column
				if ($d["parameter_type"]=="T") {
					$q  = "ALTER TABLE `#__{vm}_product_type_";
					$q .= $d["product_type_id"]."` ADD FULLTEXT `idx_product_type_".$d["product_type_id"]."_";
					$q .= $d["parameter_name"]."` (`".$d["parameter_name"]."`);";
					$db->setQuery($q);
					$db->query();
				}
				else {
					$q  = "ALTER TABLE `#__{vm}_product_type_";
					$q .= $d["product_type_id"]."` ADD KEY `idx_product_type_".$d["product_type_id"]."_";
					$q .= $d["parameter_name"]."` (`".$d["parameter_name"]."`);";
					$db->setQuery($q);
					$db->query();
				}
			}
			return True;
		}
		else {
			return False;
		}
	}

	/**
	* Controller for Deleting Records.
	*/
	function delete_parameter(&$d) {

		if (!$this->validate_delete_parameter($d)) {
			return False;
		}
		$record_id = $d["parameter_name"];

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
	* Should delete a Parameter form Product Type 
	* and drop column from table product_type_<id>
	*/
	function delete_record( $record_id, &$d ) {
		$db = new ps_DB;


		/** Find parameter_type of deleted parameter */
		$q  = "SELECT parameter_type FROM #__{vm}_product_type_parameter";
		$q2 = " WHERE product_type_id='" . $d["product_type_id"] . "' AND parameter_name='$record_id'";
		$db->query($q.$q2);
		if( $db->next_record() )
		$parameter_type = $db->f("parameter_type");
		else
		$parameter_type = "B"; // Error - dont delete (maybe nonexisted) column from #__{vm}_product_type_XX

		$q  = "DELETE FROM #__{vm}_product_type_parameter";
		$db->setQuery($q.$q2);   $db->query();

		// Delete index - deleted automaticaly
		/*		$q  = "ALTER TABLE `#__{vm}_product_type_";
		$q .= $d["product_type_id"]."` DROP INDEX `idx_product_type_".$d["product_type_id"]."_";
		$q .= $d["parameter_name"]."`;";
		$db->setQuery($q);   $db->query();*/

		if ($parameter_type!="B") { // != Break Line
			// Delete column
			$q  = "ALTER TABLE #__{vm}_product_type_".$d["product_type_id"]." DROP `$record_id`";
			$db->setQuery($q);   $db->query();
		}

		return True;
	}


	/**************************************************************************
	** name: list_order_parameter()
	** created by: Zdenek Dvorak
	** description: lists all Parameters of Product Type
	** parameters:
	** returns:
	***************************************************************************/
	function list_order_parameter( $product_type_id='0', $parameter_name='', $list_order=0 ) {

		$db = new ps_DB;
		if (!$parameter_name) {
			return _CMN_NEW_ITEM_LAST;
		}
		else {

			$q  = "SELECT parameter_list_order,parameter_label,parameter_name FROM #__{vm}_product_type_parameter ";
			$q .= "ORDER BY parameter_list_order ASC";
			$db->query( $q );

			$html = "<select class=\"inputbox\" name=\"list_order\">\n";
			while( $db->next_record() ) {
				if( $list_order == $db->f("parameter_list_order") )
				$selected = "selected=\"selected\"";
				else
				$selected = "";
				$html .= "<option value=\"".$db->f("parameter_list_order")."\" $selected>"
				.$db->f("parameter_list_order").". ".$db->f("parameter_label")
				." (".$db->f("parameter_name").")"
				."</option>\n";
			}
			$html .= "</select>\n";
			return $html;
		}
	}


	/**************************************************************************
	** name: reorder_parameter()
	** created by: Zdenek Dvorak
	** description: Changes the parameter List Order
	** parameters: product_type_id
	** returns: true if the category has childs; false, if not !!!!!!!!!!!!!!!!!!!
	***************************************************************************/
	function reorder_parameter( &$d ) {
		$cb = mosGetParam( $_POST, 'parameter_name', array(0) );
		$product_type_id = mosGetParam( $_POST, 'product_type_id', 0 );

		$db = new ps_DB;
		switch( $d["task"] ) {
			case "orderup":
				$q = "SELECT parameter_list_order FROM #__{vm}_product_type_parameter ";
				$q .= "WHERE product_type_id='".$product_type_id."' ";
				$q .= "AND parameter_name='".$cb[0]."'";
				$db->query($q);
				$db->next_record();
				$currentpos = $db->f("parameter_list_order");

				// Get the (former) predecessor and update it
				$q  = "SELECT parameter_list_order,parameter_name FROM #__{vm}_product_type_parameter WHERE ";
				$q .= "parameter_list_order<'". $currentpos . "' ";
				$q .= "ORDER BY parameter_list_order DESC";
				$db->query($q);
				$db->next_record();
				$pred = $db->f("parameter_name");
				$pred_pos = $db->f("parameter_list_order");

				// Update the product_type and decrease the list_order
				$q = "UPDATE #__{vm}_product_type_parameter ";
				$q .= "SET parameter_list_order='".$pred_pos."' ";
				$q .= "WHERE product_type_id='".$product_type_id."' ";
				$q .= "AND parameter_name='".$cb[0]."'";
				$db->query($q);

				$q = "UPDATE #__{vm}_product_type_parameter ";
				$q .= "SET parameter_list_order='".intval($pred_pos + 1)."' ";
				$q .= "WHERE product_type_id='".$product_type_id."' ";
				$q .= "AND parameter_name='$pred'";
				$db->query($q);

				break;

			case "orderdown":
				$q = "SELECT parameter_list_order FROM #__{vm}_product_type_parameter ";
				$q .= "WHERE product_type_id='".$product_type_id."' ";
				$q .= "AND parameter_name='".$cb[0]."'";
				$db->query($q);
				$db->next_record();
				$currentpos = $db->f("parameter_list_order");

				// Get the (former) successor and update it
				$q  = "SELECT parameter_list_order,parameter_name FROM #__{vm}_product_type_parameter WHERE ";
				$q .= "parameter_list_order>'". $currentpos . "' ";
				$q .= "ORDER BY parameter_list_order";
				$db->query($q);
				$db->next_record();
				$succ = $db->f("parameter_name");
				$succ_pos = $db->f("parameter_list_order");

				$q = "UPDATE #__{vm}_product_type_parameter ";
				$q .= "SET parameter_list_order='".$succ_pos."' ";
				$q .= "WHERE product_type_id='".$product_type_id."' ";
				$q .= "AND parameter_name='".$cb[0]."'";
				$db->query($q);

				$q = "UPDATE #__{vm}_product_type_parameter ";
				$q .= "SET parameter_list_order='".intval($succ_pos - 1)."' ";
				$q .= "WHERE product_type_id='".$product_type_id."' ";
				$q .= "AND parameter_name='$succ'";
				$db->query($q);

				break;
		}

	}

}
/** Changed Product Type - End*/
?>
