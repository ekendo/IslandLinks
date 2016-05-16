<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_product_attribute.php,v 1.7.2.2 2006/04/23 19:40:07 soeren_nb Exp $
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


/**
 * The class is is used to manage the product attributes.
 *
 */
class ps_product_attribute {
	var $classname = "ps_product_attribute";

	/**
	 * Validates that all variables for adding, updating an attribute
	 * are correct
	 *
	 * @param array $d
	 * @return boolean True when successful, false when not
	 */
	function validate(&$d) {
		$valid = true;
		if ($d["attribute_name"] == "") {
			$d["error"] .= "An attribute name must be entered.";
			$valid = false;
		}
		elseif ($d["old_attribute_name"] != $d["attribute_name"]) {
			$db = new ps_DB;
			$q  = "SELECT attribute_name FROM #__{vm}_product_attribute_sku ";
			$q .= "WHERE attribute_name = '" . $d["attribute_name"] . "'";
			$q .= "AND product_id = '" . $d["product_id"] . "'";
			$db->setQuery($q);  $db->query();
			if ($db->next_record()) {
				$d["error"] .= "A unique attribute name must be entered.";
				$valid = false;
			}
		}
		foreach ($d as $key => $value) {
			if (!is_array($value))
			$d[$key] = addslashes($value);
		}
		return $valid;
	}

	/**
	 * Validates all variables for deleting an attribute
	 *
	 * @param array $d
	 * @return boolean True when successful, false when not
	 */
	function validate_delete(&$d) {
		require_once(CLASSPATH. 'ps_product.php' );

		$ps_product = new ps_product;

		$db = new ps_DB;
		$q  = "SELECT product_id FROM #__{vm}_product_attribute_sku WHERE product_id = '" . $d["product_id"] . "' ";
		$db->setQuery($q);  $db->query();
		if ($db->num_rows() == 1 and
		$ps_product->parent_has_children($d["product_id"])) {
			$d["error"] .= "ERROR: Cannot delete last attribute while product has ";
			$d["error"] .= "Items. Delete all Items first.";
			return false;
		}

		return true;

	}
	/**
	 * Adds an attribute record
	 *
	 * @param array $d
	 * @return boolean True when successful, false when not
	 */
	function add(&$d) {
		if (!$this->validate($d)) {
			return false;
		}

		$db = new ps_DB;
		$q  = "INSERT INTO #__{vm}_product_attribute_sku (product_id,attribute_name,";
		$q .= "attribute_list) VALUES ('" . $d["product_id"] . "','";
		$q .= $d["attribute_name"] . "','" . $d["attribute_list"] . "')";

		$db->setQuery($q);  $db->query();

		/** Insert new Attribute Name into child table **/
		$ps_product = new ps_product;
		$child_pid = $ps_product->get_child_product_ids($d["product_id"]);

		for($i = 0; $i < count($child_pid); $i++) {
			$q  = "INSERT INTO #__{vm}_product_attribute (product_id,attribute_name) ";
			$q .= "VALUES ('$child_pid[$i]','" . $d["attribute_name"] . "')";
			$db->setQuery($q);  $db->query();
		}

		return true;
	}

	/**
	 * Updates an attribute record
	 *
	 * @param array $d
	 * @return boolean True when successful, false when not
	 */
	function update(&$d) {
		if (!$this->validate($d)) {
			return false;
		}

		$db = new ps_DB;

		$q  = "UPDATE #__{vm}_product_attribute_sku SET ";
		$q .= "attribute_name='" . $d["attribute_name"] . "',";
		$q .= "attribute_list='" . $d["attribute_list"] . "' ";
		$q .= "WHERE product_id='" . $d["product_id"] . "' ";
		$q .= "AND attribute_name='" . $d["old_attribute_name"] . "' ";

		$db->setQuery($q);  $db->query();

		if ($d["old_attribute_name"] != $d["attribute_name"]) {
			$ps_product = new ps_product;
			$child_pid = $ps_product->get_child_product_ids($d["product_id"]);

			for($i = 0; $i < count($child_pid); $i++) {
				$q  = "UPDATE #__{vm}_product_attribute SET ";
				$q .= "attribute_name='" . $d["attribute_name"] . "' ";
				$q .= "WHERE product_id='$child_pid[$i]' ";
				$q .= "AND attribute_name='" . $d["old_attribute_name"] . "' ";
				$db->setQuery($q);  $db->query();
			}
		}
		return true;
	}

	/**
	* Controller for Deleting Records.
	*/
	function delete(&$d) {

		$record_id = $d["attribute_name"];

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

		if (!$this->validate_delete($d)) {
			return false;
		}

		$q  = "DELETE FROM #__{vm}_product_attribute_sku ";
		$q .= "WHERE product_id = '" . $d["product_id"] . "' ";
		$q .= "AND attribute_name = '$record_id'";

		$db->setQuery($q);  $db->query();
		$ps_product = new ps_product;
		$child_pid = $ps_product->get_child_product_ids($d["product_id"]);

		for($i = 0; $i < count($child_pid); $i++) {
			$q  = "DELETE FROM #__{vm}_product_attribute ";
			$q .= "WHERE product_id = '$child_pid[$i]' ";
			$q .= "AND attribute_name = '$record_id' ";
			$db->setQuery($q);  $db->query();
		}
		return True;
	}

	/**
	 * Lists all child/sister products of the given product
	 *
	 * @param int $product_id
	 * @return string HTML code with Items, attributes & price
	 */
	function list_attribute($product_id) {

		global $VM_LANG, $CURRENCY_DISPLAY;

		require_once (CLASSPATH . 'ps_product.php' );
		$ps_product = new ps_product;
		$Itemid = mosGetParam( $_REQUEST, 'Itemid', "" );
		$category_id = mosGetParam( $_REQUEST, 'category_id', "" );
		$db = new ps_DB;
		$db_sku = new ps_DB;
		$db_item = new ps_DB;

		$html = "";
		// Get list of children
		$q = "SELECT product_id,product_name FROM #__{vm}_product WHERE product_parent_id='$product_id' AND product_publish='Y'";
		$db->setQuery($q);
		$db->query();
		if( $db->num_rows() < 1 ) {
			// Try to Get list of sisters & brothers
			$q = "SELECT product_parent_id FROM #__{vm}_product WHERE product_id='$product_id'";
			$db->setQuery($q);
			$db->query();
			$child_id = $product_id;
			$product_id = $db->f("product_parent_id")!="0" ? $db->f("product_parent_id") : $product_id;
			$q = "SELECT product_id,product_name FROM #__{vm}_product WHERE product_parent_id='".$db->f("product_parent_id")."' AND product_parent_id<>0 AND product_publish='Y'";
			$db->setQuery($q);
			$db->query();
		}
		if( $db->num_rows() > 0 ) {
			$flypage = $ps_product->get_flypage( $product_id );
			$html .= "<label for=\"product_id_field\">".$VM_LANG->_PHPSHOP_PLEASE_SEL_ITEM."</label>: <br />";
			$html .= "<select class=\"inputbox\" onchange=\"var id = document.getElementById('addtocart').product_id[selectedIndex].value; if(id != '') {window.location='".$_SERVER['PHP_SELF']."?option=com_virtuemart&page=shop.product_details&flypage=$flypage&Itemid=$Itemid&category_id=$category_id&product_id=' + id } ;\" id=\"product_id_field\" name=\"product_id\">\n";
			$html .= "<option value=\"$product_id\">".$VM_LANG->_PHPSHOP_SELECT."</option>";
			while ($db->next_record()) {
				$selected = isset($child_id) ? ($db->f("product_id")==$child_id ? "selected=\"selected\"" : "") : "";

				// Start row for this child
				$html .= "<option value=\"" . $db->f("product_id") . "\" $selected>";
				$html .= $db->f("product_name") . " - ";

				// For each child get attribute values by looping through attribute list
				$q = "SELECT product_id, attribute_name FROM #__{vm}_product_attribute_sku ";
				$q .= "WHERE product_id='$product_id' ORDER BY attribute_list ASC";
				$db_sku->setQuery($q);  $db_sku->query();

				while ($db_sku->next_record()) {
					$q = "SELECT attribute_name, attribute_value, product_id ";
					$q .= "FROM #__{vm}_product_attribute WHERE ";
					$q .= "product_id='" . $db->f("product_id") . "' AND ";
					$q .= "attribute_name='" . $db_sku->f("attribute_name") . "'";
					$db_item->setQuery($q);  $db_item->query();
					while ($db_item->next_record()) {
						$html .= $db_item->f("attribute_name") . " ";
						$html .= "(" . $db_item->f("attribute_value") . ")";
						if( !$db_sku->is_last_record() )
						$html .= '; ';
					}
				}
				// Attributes for this item are done.
				// Now get item price
				if( $_SESSION['auth']['show_prices'] ) {
					$price = $ps_product->get_price($db->f("product_id"));
					if( $_SESSION["auth"]["show_price_including_tax"] == 1 ) {
						$tax_rate = 1 + $ps_product->get_product_taxrate($db->f("product_id"));
						$price['product_price'] *= $tax_rate;
					}
					$html .= ' - '.$CURRENCY_DISPLAY->getFullValue($price["product_price"]);
				}
				$html .= "</option>\n";
			}
			$html .= "</select>\n";
		}
		else {
			$html = "<input type=\"hidden\" name=\"product_id\" value=\"$product_id\" />\n";
		}

		return $html;
	}


	/**
	 * Creates drop-down boxes from advanced attribute format.
	 * @author Sean Tobin (byrdhuntr@hotmail.com)
	 * @param int $product_id
	 * @return string HTML code containing Drop Down Lists with Labels
	 */
	function list_advanced_attribute($product_id) {
		global $CURRENCY_DISPLAY;
		$db = new ps_DB;
		$auth = $_SESSION['auth'];
		
		$q = "SELECT product_id, attribute FROM #__{vm}_product WHERE product_id='$product_id'";
		$db->query($q);
		$db->next_record();

		$advanced_attribute_list=$db->f("attribute");
		if ($advanced_attribute_list) {
			$has_advanced_attributes=1;
			$fields=explode(";",$advanced_attribute_list);
			$html = "";
			foreach($fields as $field) {

				$base=explode(",",$field);
				$title=array_shift($base);
				$titlevar=str_replace(" ","_",$title);
				$html .= "<div style=\"width:30%;float:left;text-align:right;margin:3px;\">";
				$html .= "<label for=\"".$titlevar."_field\">$title</label>:</div>";
				$html .= "<div style=\"width:60%;float:left;margin:3px;\"><select class=\"inputbox\" id=\"".$titlevar."_field\" name=\"$titlevar\">";
				foreach ($base as $base_value) {
					// the Option Text
					$attribtxt=substr($base_value,0,strrpos($base_value, '['));
					if( $attribtxt != "") {
						$vorzeichen=substr($base_value,strrpos($base_value, '[')+1,1); // negative, equal or positive?
						if( $_SESSION["auth"]["show_price_including_tax"] == 1 ) {
							$price = floatval(substr($base_value,strrpos($base_value, '[')+2))*(1+ @$_SESSION['product_sess'][$product_id]['tax_rate']); // calculate Tax
						}
						else {
							$price = floatval(substr($base_value,strrpos($base_value, '[')+2));
						}
						// Apply shopper group discount
						$price *= 1 - ($auth["shopper_group_discount"]/100);
						
						if ($price=="0") {
							$attribut_hint = "test";
						}
						$base_var=str_replace(" ","_",$base_value);
						$html.="<option value=\"$base_var\">$attribtxt";
						if( $_SESSION['auth']['show_prices'] ) {
							$html .= "&nbsp;(&nbsp;".$vorzeichen."&nbsp;".$CURRENCY_DISPLAY->getFullValue($price)."&nbsp;)";
						}
						$html .= "</option>";
					}
					else {
						$base_var=str_replace(" ","_",$base_value);
						$html.="<option value=\"$base_var\">$base_value</option>";
					}
				}
				$html.="</select></div><br style=\"clear:both;\" />\n";
			}
			//$html.="</table>";
		}

		if ($advanced_attribute_list) {
			return $html;
		}
	}
	
	/**
	 * Creates textfields for customizable products from the custom attribute format
	 * @author Denie van Kleef (denievk@in2sports)
	 * @param unknown_type $product_id
	 * @return unknown
	 */
	function list_custom_attribute($product_id) {
		global $mosConfig_secret;
		$db = new ps_DB;

		$q = "SELECT product_id, custom_attribute from #__{vm}_product WHERE product_id='$product_id'";
		$db->query($q);
		$db->next_record();

		$custom_attr_list=$db->f("custom_attribute");
		if ($custom_attr_list) {
			$has_custom_attributes=1;
			$fields=explode(";",$custom_attr_list);
			$html = "";
			foreach($fields as $field)
			{
				$titlevar=str_replace(" ","_",$field);
				$title=ucfirst($field);
				$html .= "<div style=\"width:30%;float:left;text-align:right;margin:3px;\">";
				$html .= "<label for=\"".$titlevar."_field\">$title</label>:</div>";
				$html .= "<div style=\"width:60%;float:left;margin:3px;\">";
				$html .= "<input type=\"text\" class=\"inputbox\" id=\"".$titlevar."_field\" size=\"30\" name=\"$titlevar\" />";
				$html.="</div>\n";
				$html .= "<input type=\"hidden\" name=\"custom_attribute_fields[]\" value=\"$titlevar\" />\n";
				$html .= "<input type=\"hidden\" name=\"custom_attribute_fields_check[$titlevar]\" value=\"".md5($mosConfig_secret. $titlevar )."\" />\n";
			}
		}

		if ($custom_attr_list) {
			return $html;
		}
	}
	/**
   * This checks if attributes value were chosen by the user
   * onCartAdd
   *
   * @param array $d
   * @return array $result
   */
	function cartGetAttributes( &$d ) {

		global $db;
		
		// added for the advanced attributes modification
		//get listing of titles for attributes (Sean Tobin)
		$attributes = array();
		
		$q = "SELECT product_id, attribute, custom_attribute FROM #__{vm}_product WHERE product_id='".$d["product_id"]."'";
		$db->query($q);
		$db->next_record();
		$advanced_attribute_list=$db->f("attribute");
		if ($advanced_attribute_list) {
			$fields=explode(";",$advanced_attribute_list);
			foreach($fields as $field) {
				$base=explode(",",$field);
				$title=array_shift($base);
				array_push($attributes,$title);
			}
		}

		$description="";
		$attribute_given = false;
		foreach($attributes as $a) {
			$pagevar=str_replace(" ","_",$a);
			if (!empty($d[$pagevar])) {
				$attribute_given = true;
			}
			if ($description!='') {
				$description.="; ";
			}
			$description.=$a.":";
			$description .= empty($d[$pagevar]) ? '' : $d[$pagevar];
		}
		rtrim($description);
		$d["description"] = $description;
		// end advanced attributes modification addition
		
		// added for custom fields by denie van kleef
		$custom_attribute_list=$db->f("custom_attribute");
		$custom_attribute_given = false;
		if ($custom_attribute_list) {
			$fields=explode(";",$custom_attribute_list);

			$description=$d["description"];
			foreach($fields as $field)
			{
				$pagevar=str_replace(" ","_",$field);
				if (!empty($d[$pagevar])) {
					$custom_attribute_given = true;
				}
				if ($description!='') {
					$description.="; ";
				}
				$description.=$field.":";
				$description .= empty($d[$pagevar]) ? '' : $d[$pagevar];
			}
			rtrim($description);
			$d["description"] = $description;
			// END add for custom fields by denie van kleef

		}

		$result['attribute_given'] = $attribute_given;
		$result['advanced_attribute_list'] = $advanced_attribute_list;
		$result['custom_attribute_given'] = $custom_attribute_given;
		$result['custom_attribute_list'] = $custom_attribute_list;
		
		return $result;
	}
}
?>
