<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_product_category.php,v 1.14.2.5 2006/04/05 18:16:53 soeren_nb Exp $
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
 * The class to manage and show product categories
 *
 */
class ps_product_category extends vmAbstractObject {
	var $classname = "ps_product_category";

	/**
	 * Validates all product category fields and uploaded image files
	 * on category creation.
	 *
	 * @param array $d The input vars
	 * @return boolean True when validation successful, false when not
	 */
	function validate_add(&$d) {
		global $vmLogger;
		$valid = true;
		if (!$d["category_name"]) {
			$vmLogger->err( "You must enter a name for the category.");
			$valid = False;
		}

		/** Image Upload Validation **/

		// do we have an image URL or an image File Upload?
		if (!empty( $d['category_thumb_image_url'] )) {
			// Image URL
			if (substr( $d['category_thumb_image_url'], 0, 4) != "http") {
				$vmLogger->err( "Image URL must begin with http." );
				$valid =  false;
			}

			$d["category_thumb_image"] = $d['category_thumb_image_url'];
		}
		else {
			// File Upload
			if (!validate_image( $d, "category_thumb_image", "category")) {
				$valid = false;
			}
		}

		if (!empty( $d['category_full_image_url'] )) {
			// Image URL
			if (substr( $d['category_full_image_url'], 0, 4) != "http") {
				$vmLogger->err( "Image URL must begin with http." );
				return false;
			}
			$d["category_full_image"] = $d['category_full_image_url'];
		}
		else {
			// File Upload
			if (!validate_image( $d, "category_full_image", "category")) {
				$valid = false;
			}
		}

		return $valid;

	}

	/**
	 * Validates all product category fields and uploaded image files
	 * on category update.
	 *
	 * @param array $d The input vars
	 * @return boolean True when validation successful, false when not
	 */
	function validate_update(&$d) {
		global $vmLogger;
		$valid = true;

		if (!$d["category_name"]) {
			$vmLogger->err( "You must enter a name for the category." );
			$valid = False;
		}
		elseif ($d["category_id"] == $d["category_parent_id"]) {
			$vmLogger->err( "Category parent cannot be same category." );
			$valid = False;
		}
		$db =& new ps_DB;
		$q = "SELECT category_thumb_image,category_full_image FROM #__{vm}_category WHERE category_id='". $d["category_id"] . "'";
		$db->query( $q );
		$db->next_record();

		/** Image Upload Validation **/

		// do we have an image URL or an image File Upload?
		if (!empty( $d['category_thumb_image_url'] )) {
			// Image URL
			if (substr( $d['category_thumb_image_url'], 0, 4) != "http") {
				$vmLogger->err( "An Image URL must begin with 'http'." );
				$valid =  false;
			}

			// if we have an uploaded image file, prepare this one for deleting.
			if( $db->f("category_thumb_image") && substr( $db->f("category_thumb_image"), 0, 4) != "http") {
				$_REQUEST["category_thumb_image_curr"] = $db->f("product_thumb_image");
				$d["category_thumb_image_action"] = "delete";
				if (!validate_image( $d, "product_thumb_image", "category")) {
					return false;
				}
			}
			$d["category_thumb_image"] = $d['category_thumb_image_url'];
		}
		else {
			// File Upload
			if (!validate_image( $d, "category_thumb_image", "category")) {
				$valid = false;
			}
		}

		if (!empty( $d['category_full_image_url'] )) {
			// Image URL
			if (substr( $d['category_full_image_url'], 0, 4) != "http") {
				$vmLogger->err( "Image URL must begin with 'http'." );
				return false;
			}
			// if we have an uploaded image file, prepare this one for deleting.
			if( $db->f("category_full_image") && substr( $db->f("category_thumb_image"), 0, 4) != "http") {
				$_REQUEST["category_full_image_curr"] = $db->f("category_full_image");
				$d["category_full_image_action"] = "delete";
				if (!validate_image( $d, "category_full_image", "category")) {
					return false;
				}
			}
			$d["category_full_image"] = $d['category_full_image_url'];
		}
		else {
			// File Upload
			if (!validate_image( $d, "category_full_image", "category")) {
				$valid = false;
			}
		}

		return $valid;

	}

	/**
	 * Validates all product category fields and uploaded image files
	 * on category deletion.
	 *
	 * @param mixed $category_id The category_id (or IDs when it's an array)
	 * @param array $d The input vars
	 * @return boolean True when validation successful, false when not
	 */
	function validate_delete( $category_id, &$d) {
		global $vmLogger;
		$db = new ps_DB;

		if (empty( $category_id )) {
			$vmLogger->err( "Please select a category to delete." );
			return False;
		}

		// Check for children
		$q  = "SELECT * FROM #__{vm}_category_xref where category_parent_id='$category_id'";
		$db->setQuery($q);   $db->query();
		if ($db->next_record()) {
			$vmLogger->err( "This category has children - please delete those children first.");
			return False;
		}
		$q = "SELECT category_thumb_image,category_full_image FROM #__{vm}_category WHERE category_id='$category_id'";
		$db->query( $q );
		$db->next_record();

		/* Prepare category_thumb_image for Deleting */
		if( !stristr( $db->f("category_thumb_image"), "http") ) {
			$_REQUEST["category_thumb_image_curr"] = $db->f("category_thumb_image");
			$d["category_thumb_image_action"] = "delete";
			if (!validate_image($d,"category_thumb_image","category")) {
				$vmLogger->err( "Failed deleting Category Images!" );
				return false;
			}
		}
		/* Prepare product_full_image for Deleting */
		if( !stristr( $db->f("category_full_image"), "http") ) {
			$_REQUEST["category_full_image_curr"] = $db->f("category_full_image");
			$d["category_full_image_action"] = "delete";
			if (!validate_image($d,"category_full_image","category")) {
				return false;
			}
		}
		return True;
	}

	/**
	 * Creates a new category record and a category_xref record
	 * with the appropriate parent and child ids
	 * @author pablo
	 * @author soeren
	 * 
	 * @param array $d
	 * @return mixed - int category_id on success, false on error
	 */
	function add( &$d ) {
		global $vmLogger;
		$ps_vendor_id = $_SESSION["ps_vendor_id"];
		
		$db = new ps_DB;
		$timestamp = time();

		if ($this->validate_add($d)) {

			if (!process_images($d)) {
				return false;
			}

			while(list($key,$value)= each($d)) {
				if (!is_array($value))
				$d[$key] = addslashes($value);
			}
			// Let's find out the last category in
			// the level of the new category
			$q = "SELECT MAX(list_order) AS list_order FROM #__{vm}_category_xref,#__{vm}_category ";
			$q .= "WHERE category_parent_id='".$d["parent_category_id"]."' ";
			$q .= "AND category_child_id=category_id ";
			$db->query( $q );
			$db->next_record();

			$list_order = intval($db->f("list_order"))+1;

			if (empty($d["category_publish"])) {
				$d["category_publish"] = "N";
			}

			$q = "INSERT into #__{vm}_category (vendor_id, category_name, ";
			$q .= "category_publish, category_description, category_browsepage, products_per_row, ";
			$q .= "category_flypage, category_thumb_image, category_full_image, cdate, mdate, list_order) ";
			$q .= "VALUES ('$ps_vendor_id','";
			$q .= $d["category_name"] . "','";
			if ($d["category_publish"] != "Y") {
				$d["category_publish"] = "N";
			}
			$q .= $d["category_publish"] . "','";
			$q .= $d["category_description"] . "','";
			$q .= $d["category_browsepage"] . "','";
			$q .= $d["products_per_row"] . "','";
			$q .= $d["category_flypage"] . "','";
			$q .= $d["category_thumb_image"] . "','";
			$q .= $d["category_full_image"] . "','";
			$q .= $timestamp . "','";
			$q .= $timestamp . "', '";
			$q .= $list_order . "')";
			$db->setQuery($q);
			$db->query();

			$category_id = $db->last_insert_id();

			$q = "INSERT into #__{vm}_category_xref ";
			$q .= "(category_parent_id, category_child_id) ";
			$q .= "VALUES ('";
			$q .= $d["parent_category_id"] . "','";
			$q .= $category_id . "')";
			$db->setQuery($q);
			$db->query();
			
			$vmLogger->info( "Successfully added new category: ".$d['category_name'].'.');
			return $category_id;
		}
		else {
			return False;
		}

	}

	/**
	 * Updates a category record and its category_xref record
	 * 
	 * @author pablo
	 * @author soeren
	 * 
	 * @param array $d
	 * @return boolean true on success, false on error
	 */
	function update(&$d) {
		global $vmLogger;
		$ps_vendor_id = $_SESSION["ps_vendor_id"];

		$db = new ps_DB;

		$timestamp = time();

		foreach ($d as $key => $value) {
			if (!is_array($value))
			$d[$key] = addslashes($value);
		}
		if ($this->validate_update($d)) {
			if (!process_images($d)) {
				return false;
			}
			$q = "UPDATE #__{vm}_category SET ";
			$q .= "category_name='" . $d["category_name"];
			if (!isset($d["category_publish"])) {
				$d["category_publish"] = "N";
			}
			$q .= "',category_publish='" . $d["category_publish"];
			$q .= "',category_description='" . $d["category_description"];
			$q .= "',category_browsepage='" . $d["category_browsepage"];
			$q .= "',products_per_row='" . $d["products_per_row"];
			$q .= "',category_flypage='" . $d["category_flypage"];
			$q .= "',category_thumb_image='" . $d["category_thumb_image"];
			$q .= "',category_full_image='" . $d["category_full_image"];
			$q .= "', mdate='$timestamp";
			$q .= "', list_order='" . $d["list_order"]."'";
			$q .= " WHERE category_id='" . $d["category_id"] . "' ";
			$q .= "AND vendor_id='$ps_vendor_id' ";
			$db->setQuery($q);
			$db->query();

			/*
			** update #__{vm}_category x-reference table with parent-child relationship
			*/
			$q = "UPDATE #__{vm}_category_xref SET ";
			$q .= "category_parent_id='" . $d["category_parent_id"];
			$q .= "' WHERE category_child_id='" . $d["category_id"] . "'";
			$db->setQuery($q);
			$db->query();

			/* Re-Order the category table IF the list_order has been changed */
			if( intval($d['list_order']) != intval($d['currentpos'])) {
				$dbu = new ps_DB;

				/* Moved UP in the list order */
				if( intval($d['list_order']) < intval($d['currentpos']) ) {

					$q = "SELECT category_id FROM #__{vm}_category_xref,#__{vm}_category ";
					$q .= "WHERE category_parent_id='".$d["category_parent_id"]."' ";
					$q .= "AND category_child_id=category_id ";
					$q .= "AND category_id <> '" . $d["category_id"] . "' ";
					$q .= "AND list_order >= '" . intval($d["list_order"]) . "'";
					$db->query( $q );

					while( $db->next_record() ) {
						$dbu->query("UPDATE #__{vm}_category SET list_order=list_order+1 WHERE category_id='".$db->f("category_id")."'");
					}
				}
				/* Moved DOWN in the list order */
				else {

					$q = "SELECT category_id FROM #__{vm}_category_xref,#__{vm}_category ";
					$q .= "WHERE category_parent_id='".$d["category_parent_id"]."' ";
					$q .= "AND category_child_id=category_id ";
					$q .= "AND category_id <> '" . $d["category_id"] . "' ";
					$q .= "AND list_order > '" . intval($d["currentpos"]) . "'";
					$q .= "AND list_order <= '" . intval($d["list_order"]) . "'";
					$db->query( $q );

					while( $db->next_record() ) {
						$dbu->query("UPDATE #__{vm}_category SET list_order=list_order-1 WHERE category_id='".$db->f("category_id")."'");
					}

				}
			} /* END Re-Ordering */

			// Problem: When the parent id has changed, the category is
			// in a new level. We now need to change the list order value
			// of the category to the value: recent MAXIMUM + 1
			if( $d["category_parent_id"] != $d["current_parent_id"] ) {
				// Let's find out the last category in
				// the new level of the category
				$q = "SELECT MAX(list_order) AS list_order FROM #__{vm}_category_xref,#__{vm}_category ";
				$q .= "WHERE category_parent_id='".$d["category_parent_id"]."' ";
				$q .= "AND category_child_id=category_id ";
				$q .= "AND category_id <> '".$d["category_id"]."'";
				$db->query( $q );
				$db->next_record();

				$q = "UPDATE #__{vm}_category SET list_order=".$db->f("list_order")."+1 WHERE category_id='".$d["category_id"]."'";
				$db->query( $q );
			}
			
			$vmLogger->info( "Successfully updated category: ".$d['category_name'].'.' );
			
			return True;
		}
		else {
			return False;
		}
	}

	/**
	* Controller for Deleting Records.
	* @param $d Holds the category_id(s) of the category(/ies) to be deleted
	*/
	function delete( &$d ) {

		$record_id = $d["category_id"];

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
		global $ps_product, $db, $vmLogger;

		if (!$this->validate_delete($record_id, $d)) {
			return False;
		}
		// Delete all products from that category
		// We must filter out those products that are in more than one category!

		// Case 1: Products are assigned to more than on category
		// so let's only delete the __{vm}_product_category_xref entry
		$q = "CREATE TEMPORARY TABLE IF NOT EXISTS `#__tmp_prod` AS
            (SELECT * FROM `#__{vm}_product_category_xref` 
            WHERE `category_id`='$record_id');";
		$db->query( $q );
		$q = "SELECT #__{vm}_product_category_xref.product_id
          FROM `#__{vm}_product_category_xref`, `#__tmp_prod` 
          WHERE #__{vm}_product_category_xref.product_id=#__tmp_prod.product_id 
            AND #__{vm}_product_category_xref.category_id!='$record_id';";
		$db->query( $q );
		if( $db->num_rows() > 0 ) {
			$i = 0;
			$q = "DELETE FROM #__{vm}_product_category_xref WHERE product_id IN (";
			while( $db->next_record() ) {
				$q .= "'".$db->f("product_id")."'";
				if( $i++ < $db->num_rows()-1 )
				$q .= ",";
			}
			$q .= ") AND category_id='$record_id'";
			$db->query( $q );
		}
		else {
			// Case 2: Products are assigned to this category only
			$q = "SELECT product_id FROM `#__{vm}_product_category_xref` WHERE `category_id`='$record_id';";
			$db->query ( $q );
			$d['product_id'] = Array();
			while( $db->next_record() ) {
				$d['product_id'][] = $db->f("product_id");
			}
			$ps_product->delete( $d );
		}

		$q = "DELETE FROM #__{vm}_category WHERE category_id='$record_id'";
		$db->setQuery($q);   $db->query();

		$q  = "DELETE FROM #__{vm}_category_xref WHERE category_child_id='$record_id'";
		$db->setQuery($q);   $db->query();

		/* Delete Image files */
		if (!process_images($d)) {
			return false;
		}
		$vmLogger->info( "Successfully deleted category ID: $record_id." );
		return True;
	}
	/**
	* This function is repsonsible for returning an array containing category information
	* @param boolean Show only published products?
	* @param string the keyword to filter categories
	*/
	function getCategoryTreeArray( $only_published=true, $keyword = "" ) {

		$db = new ps_DB;
		if( empty( $GLOBALS['category_info']['category_tree'])) {

			// Get only published categories
			$query  = "SELECT category_id, category_description, category_name,category_child_id as cid, category_parent_id as pid,list_order, category_publish
						FROM #__{vm}_category, #__{vm}_category_xref WHERE ";
			if( $only_published ) {
				$query .= "#__{vm}_category.category_publish='Y' AND ";
			}
			$query .= "#__{vm}_category.category_id=#__{vm}_category_xref.category_child_id ";
			if( !empty( $keyword )) {
				$query .= "AND ( category_name LIKE '%$keyword%' ";
				$query .= "OR category_description LIKE '%$keyword%' ";
				$query .= ") ";
			}
			$query .= "ORDER BY #__{vm}_category.list_order ASC, #__{vm}_category.category_name ASC";

			// initialise the query in the $database connector
			// this translates the '#__' prefix into the real database prefix
			$db->query( $query );

			$categories = Array();
			// Transfer the Result into a searchable Array

			while( $db->next_record() ) {
				$categories[$db->f("cid")]["category_child_id"] = $db->f("cid");
				$categories[$db->f("cid")]["category_parent_id"] = $db->f("pid");
				$categories[$db->f("cid")]["category_name"] = $db->f("category_name");
				$categories[$db->f("cid")]["category_description"] = $db->f("category_description");
				$categories[$db->f("cid")]["list_order"] = $db->f("list_order");
				$categories[$db->f("cid")]["category_publish"] = $db->f("category_publish");
			}

			$GLOBALS['category_info']['category_tree'] = $categories;
			return $GLOBALS['category_info']['category_tree'];
		}
		else {
			return $GLOBALS['category_info']['category_tree'];
		}
	}

	/**
	 * This function is used for the frontend to display a
	 * complete link list of top-level categories
	 * 
	 * @param int $category_id The category to be highlighted
	 * @param string $links_css_class The css class that marks mainlevel links
	 * @param string $list_css_class (deprecated)
	 * @param string $highlighted_style The css styles that format the hightlighted category
	 * @return string HTML code with the link list
	 */
	function get_category_tree( $category_id=0,
								$links_css_class="mainlevel",
								$list_css_class="mm123",
								$highlighted_style="font-style:italic;" ) {
		global $sess;

		$categories = ps_product_category::getCategoryTreeArray();

		// Copy the Array into an Array with auto_incrementing Indexes
		$key = array_keys($categories);
		$size = sizeOf($key);
		$category_tmp = Array();
		for ($i=0; $i<$size; $i++)
		$category_tmp[$i] = &$categories[$key[$i]];

		$html = "";
		/** FIRST STEP
	    * Order the Category Array and build a Tree of it
	    **/
		$nrows = count( $category_tmp );

		$id_list = array();
		$row_list = array();
		$depth_list = array();

		for($n = 0 ; $n < $nrows ; $n++)
		if($category_tmp[$n]["category_parent_id"] == 0)
		{ array_push($id_list,$category_tmp[$n]["category_child_id"]);
		array_push($row_list,$n);
		array_push($depth_list,0);
		}

		$loop_count = 0;
		while(count($id_list) < $nrows) {
			if( $loop_count > $nrows )
			break;
			$id_temp = array();
			$row_temp = array();
			$depth_temp = array();
			for($i = 0 ; $i < count($id_list) ; $i++) {
				$id = $id_list[$i];
				$row = $row_list[$i];
				$depth = $depth_list[$i];
				array_push($id_temp,$id);
				array_push($row_temp,$row);
				array_push($depth_temp,$depth);
				for($j = 0 ; $j < $nrows ; $j++)
				if(($category_tmp[$j]["category_parent_id"] == $id)
				&& (array_search($category_tmp[$j]["category_child_id"],$id_list) == NULL))
				{ array_push($id_temp,$category_tmp[$j]["category_child_id"]);
				array_push($row_temp,$j);
				array_push($depth_temp,$depth + 1);
				}
				if( empty( $categories[@$category_tmp[$j]["category_parent_id"]] )) {

					array_push($id_temp,"");
					array_push($row_temp,"");
					array_push($depth_temp,"");
				}
			}
			$id_list = $id_temp;
			$row_list = $row_temp;
			$depth_list = $depth_temp;
			$loop_count++;
		}

		/** SECOND STEP
		* Find out if we have subcategories to display
		**/
		$allowed_subcategories = Array();
		if( !empty( $categories[$category_id]["category_parent_id"] ) ) {
			// Find the Root Category of this category
			$root = $categories[$category_id];
			$allowed_subcategories[] = $categories[$category_id]["category_parent_id"];
			// Loop through the Tree up to the root
			while( !empty( $root["category_parent_id"] )) {
				$allowed_subcategories[] = $categories[$root["category_child_id"]]["category_child_id"];
				$root = $categories[$root["category_parent_id"]];
			}
		}
		// Fix the empty Array Fields
		if( $nrows < count( $row_list ) ) {
			$nrows = count( $row_list );
		}

		// Now show the categories
		for($n = 0 ; $n < $nrows ; $n++) {

			if( !isset( $row_list[$n] ) || !isset( $category_tmp[$row_list[$n]]["category_child_id"] ) )
				continue;
			if( $category_id == $category_tmp[$row_list[$n]]["category_child_id"] )
				$style = $highlighted_style;
			else
				$style = "";

			$allowed = false;
			if( $depth_list[$n] > 0 ) {
				// Subcategory!
				if( isset( $root ) && in_array( $category_tmp[$row_list[$n]]["category_child_id"], $allowed_subcategories )
				|| $category_tmp[$row_list[$n]]["category_parent_id"] == $category_id
				|| $category_tmp[$row_list[$n]]["category_parent_id"] == @$categories[$category_id]["category_parent_id"]) {
					$allowed = true;

				}
			}
			else
				$allowed = true;
			$append = "";
			if( $allowed ) {
				if( $style == $highlighted_style ) {
					$append = 'id="active_menu"';
				}
				if( $depth_list[$n] > 0 )
					$css_class = "sublevel";
				else
					$css_class = $links_css_class;

				$catname = shopMakeHtmlSafe( $category_tmp[$row_list[$n]]["category_name"] );

				$html .= '
          <a title="'.$catname.'" style="display:block;'.$style.'" class="'. $css_class .'" href="'. $sess->url(URL."index.php?page=shop.browse&amp;category_id=".$category_tmp[$row_list[$n]]["category_child_id"]) .'" '.$append.'>'
				. str_repeat("&nbsp;&nbsp;&nbsp;",$depth_list[$n]) . $catname
				. ps_product_category::products_in_category( $category_tmp[$row_list[$n]]["category_child_id"] )
				.'</a>';
			}
		}

		return $html;
	}

	/**
	 * Function to print a table containing all categories sorted and structured
	 * It goes through the category table and establishes
	 * the category tree based on the parent-child relationships
	 * defnied in the category_xref table.
	 * This is VERY recursive...
	 * @deprecated 
	 * 
	 * @param unknown_type $class
	 * @param unknown_type $category_id
	 * @param unknown_type $level
	 */
	function traverse_tree_down($class="",$category_id="0", $level="0") {
		static $ibg = 0;
		global $sess, $mosConfig_live_site, $VM_LANG;
		$ps_vendor_id = $_SESSION["ps_vendor_id"];
		$db = new ps_DB;
		$class = "maintext";

		$level++;

		$q = "SELECT * FROM #__{vm}_category,#__{vm}_category_xref ";
		$q .= "WHERE #__{vm}_category_xref.category_parent_id='";
		$q .= $category_id . "' AND ";
		$q .= "#__{vm}_category.category_id=#__{vm}_category_xref.category_child_id ";
		$q .= "AND #__{vm}_category.vendor_id='$ps_vendor_id' ";
		$q .= "ORDER BY list_order asc ";
		$db->setQuery($q);
		$db->query();

		while ($db->next_record()) {
			$product_count = $this->product_count($db->f("category_child_id"));
			if ($level % 2)
				$bgcolor=SEARCH_COLOR_1;
			else
				$bgcolor=SEARCH_COLOR_2;
			$ibg++;
			echo "<tr bgcolor=\"$bgcolor\">\n";
			echo "<td><input style=\"display:none;\" id=\"cb$ibg\" name=\"cb[]\" value=\"".$db->f("category_id")."\" type=\"checkbox\" />&nbsp;$ibg</td><td>";
			for ($i=0; $i<$level; $i++) {
				echo "&nbsp;&nbsp;&nbsp;";
			}
			echo "&#095;&#095;|$level|&nbsp;";
			echo "<a href=\"" ;
			echo $_SERVER['PHP_SELF'] . "?option=com_virtuemart&page=product.product_category_form&category_id=" . $db->f("category_child_id"). "&category_parent_id=" . $db->f("category_parent_id");
			echo "\">";
			echo $db->f("category_name") . "</a></td>\n";
			echo "<td>&nbsp;&nbsp;" . $db->f("category_description");
			echo "</td>\n<td>".$product_count ." ". $VM_LANG->_PHPSHOP_PRODUCTS_LBL."&nbsp;<a href=\"";
			echo $_SERVER['PHP_SELF'] . "?page=product.product_list&category_id=" . $db->f("category_child_id")."&option=com_virtuemart";
			echo "\">[ ".$VM_LANG->_PHPSHOP_SHOW." ]</a>\n</td>\n";
			//echo "<td>". $db->f("list_order")."</td>";
			echo "<td>";
			if ($db->f("category_publish")=='N') {
				echo "<img src=\"". $mosConfig_live_site ."/administrator/images/publish_x.png\" border=\"0\" />";
			}
			else {
				echo "<img src=\"". $mosConfig_live_site ."/administrator/images/tick.png\" border=\"0\" />\n";
			}
			echo "<td width=\"5%\"><div align=\"center\">\n";
			echo mShop_orderUpIcon( $db->row, $db->num_rows(), $ibg ) . "\n&nbsp;" . mShop_orderDownIcon( $db->row, $db->num_rows(), $ibg );
			echo "</div></td>\n";
			echo "<td width=\"5%\">";
			echo "<a class=\"toolbar\" href=\"".$_SERVER['PHP_SELF']."?option=com_virtuemart&page=".$_REQUEST['page'] ."&func= productCategoryDelete&category_id=". $db->f("category_id") ."\"";
			echo " onclick=\"return confirm('". $VM_LANG->_PHPSHOP_DELETE_MSG ."');\" onmouseout=\"MM_swapImgRestore();\"  onmouseover=\"MM_swapImage('Delete$ibg','','". IMAGEURL ."ps_image/delete_f2.gif',1);\">";
			echo "<img src=\"". IMAGEURL ."ps_image/delete.gif\" alt=\"Delete this record\" name=\"delete$ibg\" align=\"middle\" border=\"0\" /></a></td>\n";
			$this->traverse_tree_down($class, $db->f("category_child_id"), $level);
		}
	}

	/**
	 * Function to calculate and return the number of products in category $category_id
	 * @author pablo
	 * @author soeren
	 * 
	 * @param int $category_id
	 * @return int The number of products found
	 */
	function product_count($category_id) {
		global $perm;
		$ps_vendor_id = $_SESSION["ps_vendor_id"];

		$db = new ps_DB;
		if( !isset($GLOBALS['category_info'][$category_id]['product_count'] )) {

			$count  = "SELECT count(#__{vm}_product.product_id) as num_rows from #__{vm}_product,#__{vm}_product_category_xref, #__{vm}_category WHERE ";
			$q = "";
			if (defined('_PSHOP_ADMIN' )) {
				if (!$perm->check( "admin,storeadmin")) {
					$q .= "#__{vm}_product.vendor_id = '$ps_vendor_id' AND ";
				}
			}
			$q .= "#__{vm}_product_category_xref.category_id='$category_id' ";
			$q .= "AND #__{vm}_category.category_id=#__{vm}_product_category_xref.category_id ";
			$q .= "AND #__{vm}_product.product_id=#__{vm}_product_category_xref.product_id ";
			//$q .= "AND #__{vm}_product.product_parent_id='' ";
			if( !$perm->check("admin,storeadmin") ) {
				$q .= " AND product_publish='Y'";
				if( CHECK_STOCK && PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS != "1") {
					$q .= " AND product_in_stock > 0 ";
				}
			}
			$count .= $q;
			$db->query($count);
			$db->next_record();
			$GLOBALS['category_info'][$category_id]['product_count'] = $db->f("num_rows");
		}
		return $GLOBALS['category_info'][$category_id]['product_count'];
	}

	/**
	 * Prints a drop-down list with all categories sorted and structured
	 * @author pablo
	 * @param int $category_id
	 * @param int $level
	 */
	function traverse_tree_up($category_id, $level=0) {
		$ps_vendor_id = $_SESSION["ps_vendor_id"];
		$db = new ps_DB;

		$level++;
		$q = "SELECT #__{vm}_category.category_name,category_child_id,category_parent_id FROM #__{vm}_category, #__{vm}_category_xref ";
		$q .= "WHERE #__{vm}_category_xref.category_child_id=' ";
		$q .= "$category_id' AND ";
		$q .= "#__{vm}_category.category_id=#__{vm}_category_xref.category_parent_id ";
		$q .= "AND #__{vm}_category.vendor_id = $ps_vendor_id ";
		$db->setQuery($q);   $db->query();
		while ($db->next_record()) {
			if ($level == 1) {
				echo "<option selected=\"selected\" value=\"" . $db->f("category_child_id");
			}
			else {
				echo "<option value=\"" . $db->f("category_child_id");
			}
			echo "\">" . $db->f("category_name") . "</option>";

			$this->traverse_tree_up($db->f("category_parent_id"), $level);
		}
	}

	/**
	 * Prints a drop-down list with all categories. The category $category_id 
	 * with the given product_id is preselected.
	 * @author pablo
	 * @param int $product_id
	 * @param int $category_id
	 * @param string $name The name of the select element
	 */
	function list_category($product_id="",$category_id="",$name = "category_id") {
		$db = new ps_DB;
		global $VM_LANG;

		echo "<select class=\"inputbox\" name=$name>\n";

		if ($product_id and !$category_id) {
			$q = "SELECT category_id from #__{vm}_product_category_xref WHERE product_id='$product_id'";
			$db->setQuery($q);   $db->query();
			$db->next_record();
			if (!$db->f("category_id")) {
				echo "<option value=\"0\">".$VM_LANG->_PHPSHOP_SELECT."</option>\n";
			}
			$this->list_tree($db->f("category_id"));
		}
		elseif ($category_id) {
			echo "<option value=\"0\">".$VM_LANG->_PHPSHOP_SELECT."</option>\n";
			$this->list_tree($category_id);
		}
		else {
			echo "<option value=\"0\">".$VM_LANG->_PHPSHOP_SELECT."</option>\n";
			$this->list_tree();
		}

		echo "</select>\n";

		return True;
	}


	/**
	 * Prints a bulleted of the childen of this category if they exist.
	 * @author soeren
	 * @param unknown_type $category_id
	 */
	function print_child_list($category_id) {
		echo $this->get_child_list($category_id);
	}

	/**
	 * creates a bulleted of the childen of this category if they exist
	 * @author pablo
	 * @param int $category_id
	 * @return string The HTML code
	 */
	function get_child_list($category_id) {
		global $sess, $ps_product, $VM_LANG;
		$ps_vendor_id = $_SESSION["ps_vendor_id"];
		$db = new ps_DB;

		$html = "";

		$q = "SELECT category_id, category_thumb_image, category_child_id,category_name FROM #__{vm}_category,#__{vm}_category_xref ";
		$q .= "WHERE #__{vm}_category_xref.category_parent_id='$category_id' ";
		$q .= "AND #__{vm}_category.category_id=#__{vm}_category_xref.category_child_id ";
		$q .= "AND #__{vm}_category.vendor_id='$ps_vendor_id' ";
		$q .= "AND #__{vm}_category.category_publish='Y' ";
		$q .= "ORDER BY #__{vm}_category.list_order, #__{vm}_category.category_name ASC";
		$db->setQuery($q);
		$db->query();

		if( $db->num_rows() > 0 ) {
			$iCol = 1;
			$categories_per_row = 4;
			$cellwidth = intval( 100 / $categories_per_row );
			$html .= $VM_LANG->_PHPSHOP_MORE_CATEGORIES.'<br/>';
			$html .= '<table width="100%" cellspacing="0" cellpadding="0">';

			while($db->next_record()) {
				if ($iCol == 1) {
					$html.= "<tr>\n";
				}
				$html.= '<td align="center" width="'. $cellwidth .'%" ><br/>
            <a title="'. $db->f("category_name").'" href="'. $sess->url(URL."index.php?option=com_virtuemart&amp;page=shop.browse&amp;category_id=".$db->f("category_id")) .'">'; 

				if ( $db->f("category_thumb_image") ) {
					$html.= $ps_product->image_tag( $db->f("category_thumb_image"), "alt=\"".$db->f("category_name")."\"", 0, "category");
					$html.= "<br /><br/>";
				}
				$html.= $db->f("category_name");
				$html.= ps_product_category::products_in_category( $db->f("category_id") );

				$html.= "</a><br/>\n";
				$html .= "</td>\n";

				if ($iCol == $categories_per_row) {
					$html.= "</tr>\n";
					$iCol = 1;
				}
				else {
					$iCol++;
				}
			}
			if ($db->num_rows() < $categories_per_row) {
				$html.= "</tr>\n";
			}
			$html.= "</table>";
		}

		return $html;

	}

	/**
	 * Prints the result of get_subcategory
	 *
	 * @param unknown_type $category_id
	 * @param unknown_type $css_class
	 */
	function print_subcategory($category_id, $css_class = "") {
		echo $this->get_subcategory( $category_id, $css_class );
	}
	/**
	 * Creates a link list to subcategories of category $category_id
	 *
	 * @param int $category_id
	 * @param string $css_class The CSS to be applied to the link
	 * @return string HTML code
	 */
	function get_subcategory( $category_id, $css_class = "" ) {
		global $sess;
		$ps_vendor_id = $_SESSION["ps_vendor_id"];

		if( $css_class != "" ) {
			$class= "class=\"$css_class\"";
		}
		else
		$class = "";

		$db = new ps_DB;

		$q = "SELECT category_id, category_child_id,category_name FROM #__{vm}_category,#__{vm}_category_xref ";
		$q .= "WHERE #__{vm}_category_xref.category_parent_id='$category_id' ";
		$q .= "AND #__{vm}_category.category_id=#__{vm}_category_xref.category_child_id ";
		//$q .= "AND #__{vm}_category.vendor_id='$ps_vendor_id' ";
		$q .= "AND #__{vm}_category.category_publish='Y' ";
		$q .= "ORDER BY #__{vm}_category.list_order, #__{vm}_category.category_name ASC";
		$db->setQuery($q);
		$db->query();
		$html = "";
		$nbsp = "&nbsp;&nbsp;&nbsp;";
		while($db->next_record()) {
			$html .= "<a style=\"display:block;\" class=\"sublevel\" title=\"".$db->f("category_name")."\" href=\"";
			$html .= $sess->url(URL . "index.php?page=shop.browse&root=$category_id&category_id=" .$db->f("category_child_id"));
			$html .= "\" $class>$nbsp".$db->f("category_name");
			$html .= ps_product_category::products_in_category( $db->f("category_child_id") );
			$html .= "</a>\n";
		}

		return $html;
	}
	
	/**
	 * Shows the Number of Products in category $category_id
	 *
	 * @param int $category_id
	 * @return string The number in brackets
	 */
	function products_in_category( $category_id ) {
		if( PSHOP_SHOW_PRODUCTS_IN_CATEGORY == '1' ) {
			$num = ps_product_category::product_count($category_id);
			if( empty($num) && ps_product_category::has_childs( $category_id )) {
				$db = new ps_DB;
				$q = "SELECT category_child_id FROM #__{vm}_category_xref ";
				$q .= "WHERE category_parent_id='$category_id' ";
				$db->query($q);
				while( $db->next_record() ) {
					$num += ps_product_category::product_count($db->f("category_child_id"));
				}
			}

			return " ($num) ";
		}
		else
		return ( "" );

	}

	/**
	 * tests for template/default pathway arrow separator
	 * @author FTW Stroker
	 * @return string The separator for the pathway breadcrumbs
	 */
	function pathway_separator() {
		global $mainframe, $mosConfig_absolute_path, $mosConfig_live_site;
		$imgPath =  'templates/' . $mainframe->getTemplate() . '/images/arrow.png';
		if (file_exists( "$mosConfig_absolute_path/$imgPath" )){
			$img = '<img src="' . $mosConfig_live_site . '/' . $imgPath . '" height="9" width="9" border="0" alt="arrow" />';
		} else {
			$imgPath = '/images/M_images/arrow.png';
			if (file_exists( $mosConfig_absolute_path . $imgPath )){
				$img = '<img src="' . $mosConfig_live_site . '/images/M_images/arrow.png" height="9" width="9" alt="arrow" />';
			} else {
				$img = '&gt;';
			}
		}
		return $img;
	}

	/**
	 * Lists all categories in a drop-down list
	 * 
	 * @param string $name The name of the select element
	 * @param int $category_id The category ID
	 * @param array $selected_categories The ids of the categories to be pre-selected
	 * @param int $size The size of the select element
	 * @param boolean $toplevel List only top-level categories?
	 * @param boolean $multiple Allow multiple selections?
	 */
	function list_all($name, $category_id, $selected_categories=Array(), $size=1, $toplevel=true, $multiple=false) {

		$db = new ps_DB;

		$q  = "SELECT category_parent_id FROM #__{vm}_category_xref ";
		if( $category_id )
		$q .= "WHERE category_child_id='$category_id'";
		$db->setQuery($q);   $db->query();
		$db->next_record();
		$category_id=$db->f("category_parent_id");
		$multiple = $multiple ? "multiple=\"multiple\"" : "";

		echo "<select class=\"inputbox\" size=\"$size\" $multiple name=\"$name\">\n";
		if( $toplevel ) {
			echo "<option value=\"0\">Default-Top Level</option>\n";
		}
		$this->list_tree($category_id, '0', '0', $selected_categories);
		echo "</select>\n";
	}

	/**
	 * Returns a drop-down list with all child categories of a given category $category_parent_id
	 *
	 * @param int $category_parent_id
	 * @param int $category_id When not empty, a drop-down list is created
	 * @param int $list_order The pre-selected list element
	 * @return string HTML code of a select list
	 */
	function list_level( $category_parent_id, $category_id='0', $list_order=0 ) {

		$db = new ps_DB;
		if (!$category_id) {
			return _CMN_NEW_ITEM_LAST;
		}
		else {

			$q  = "SELECT list_order,category_id,category_name,category_child_id FROM #__{vm}_category, #__{vm}_category_xref ";
			$q .= "WHERE category_parent_id='$category_parent_id' ";
			$q .= "AND category_child_id=category_id ";
			$q .= "ORDER BY list_order ASC";
			$db->query( $q );

			$html = "<select class=\"inputbox\" name=\"list_order\">\n";
			while( $db->next_record() ) {
				if( $list_order == $db->f("list_order") ) {
					$selected = "selected=\"selected\"";
				}
				else {
					$selected = "";
				}
				$html .= "<option value=\"".$db->f("list_order")."\" $selected>"
					.$db->f("list_order").". ".$db->f("category_name")
					."</option>\n";
			}
			$html .= "</select>\n";
			return $html;
		}
	}

	/**
	 * Creates structured option fields for all categories
	 *
	 * @param int $category_id A single category to be pre-selected
	 * @param int $cid Internally used for recursion
	 * @param int $level Internally used for recursion
	 * @param array $selected_categories All category IDs that will be pre-selected
	 */
	function list_tree($category_id="", $cid='0', $level='0', $selected_categories=Array() ) {

		$ps_vendor_id = $_SESSION["ps_vendor_id"];
		$db = new ps_DB;

		$level++;

		$q = "SELECT category_id, category_child_id,category_name FROM #__{vm}_category,#__{vm}_category_xref ";
		$q .= "WHERE #__{vm}_category_xref.category_parent_id='$cid' ";
		$q .= "AND #__{vm}_category.category_id=#__{vm}_category_xref.category_child_id ";
		$q .= "AND #__{vm}_category.vendor_id ='$ps_vendor_id' ";
		$q .= "ORDER BY #__{vm}_category.list_order, #__{vm}_category.category_name ASC";
		$db->setQuery($q);   $db->query();

		while ($db->next_record()) {
			$child_id = $db->f("category_child_id");
			if ($child_id != $cid) {
				$selected = ($child_id == $category_id) ? "selected=\"selected\"" : "";
				if( $selected == "" && @$selected_categories[$child_id] == "1") {
					$selected = "selected=\"selected\"";
				}
				echo "<option $selected value=\"$child_id\">\n";
			}
			for ($i=0;$i<$level;$i++) {
				echo "&#151;";
			}
			echo "|$level|";
			echo "&nbsp;" . $db->f("category_name") . "</option>";
			$this->list_tree($category_id, $child_id, $level, $selected_categories);
		}
	}
	/**
	 * Returns the category name of the first category product $product_id is assigned
	 *
	 * @param int $product_id
	 * @return string The categotry name
	 */
	function get_name($product_id) {
		$db = new ps_DB;

		$q = "SELECT #__{vm}_category.category_id, category_name FROM #__{vm}_category,#__{vm}_product_category_xref ";
		$q .= "WHERE product_id='$product_id' ";
		$q .= "AND #__{vm}_category.category_id = #__{vm}_product_category_xref.category_id ";
		$db->setQuery($q);   $db->query();

		$db->next_record();

		return $db->f("category_name");
	}
	/**
	* Returns the category ID of the first category
	* assigned to the given product ID
	* @param int $product_id The product id
	* @return int The category id
  	*/
	function get_cid($product_id) {
		$db = new ps_DB;

		$q = "SELECT #__{vm}_category.category_id FROM #__{vm}_category,#__{vm}_product_category_xref ";
		$q .= "WHERE product_id='$product_id' ";
		$q .= "AND #__{vm}_category.category_id = #__{vm}_product_category_xref.category_id ";
		$db->query( $q );
		$db->next_record();

		return (int)$db->f('category_id');
	}

	/**
	 * Returns the category description.
	 * @author soeren
	 * @param int $category_id
	 * @return string The category description
	 */
	function get_description($category_id) {
		$db = new ps_DB;

		$q = "SELECT category_id, category_description FROM #__{vm}_category ";
		$q .= "WHERE category_id='$category_id' ";
		$db->setQuery($q);   $db->query();

		$db->next_record();

		return $db->f("category_description");
	}

	/**
	 * Checks for childs of the category $category_id
	 *
	 * @param int $category_id
	 * @return boolean True when the category has childs, false when not
	 */
	function has_childs($category_id) {
		$db = new ps_DB;
		if( empty( $GLOBALS['category_info'][$category_id]['has_childs'] )) {
			$q = "SELECT category_child_id FROM #__{vm}_category_xref ";
			$q .= "WHERE category_parent_id='$category_id' ";
			$db->setQuery($q);   $db->query();

			if ($db->num_rows() > 0)
			$GLOBALS['category_info'][$category_id]['has_childs'] = true;
			else
			$GLOBALS['category_info'][$category_id]['has_childs'] = false;
		}
		return $GLOBALS['category_info'][$category_id]['has_childs'];
	}
	/**
	 * Prints a navigation list (=breadcrumbs) to be used in the pathway
	 *
	 * @param int $category_id
	 */
	function navigation_list($category_id) {
		echo $this->get_navigation_list($category_id);
	}
	
	/**
	 * Creates navigation list of categories
	 * @author pablo
	 * @author soeren
	 * @param int $category_id
	 */
	function get_navigation_list($category_id) {
		global $sess, $mosConfig_live_site;
		$db = new ps_DB;

		static $i=0;
		static $html = "";
		$q = "SELECT category_id, category_name,category_parent_id FROM #__{vm}_category, #__{vm}_category_xref WHERE ";
		$q .= "#__{vm}_category_xref.category_child_id='$category_id' ";
		$q .= "AND #__{vm}_category.category_id='$category_id'";
		$db->setQuery($q);   $db->query();
		$db->next_record();
		if ($db->f("category_parent_id")) {
			$link = "<a class=\"pathway\" href=\"";
			$link .= $sess->url($_SERVER['PHP_SELF'] . "?page=shop.browse&amp;category_id=$category_id");
			$link .= "\">";
			$link .= $db->f("category_name");
			$link .= "</a>";
			$category_list[$i++] = " ".$this->pathway_separator()." ". $link;
			$this->get_navigation_list($db->f("category_parent_id"));
		}
		else {
			$link = "<a class=\"pathway\" href=\"";
			$link .= $sess->url($_SERVER['PHP_SELF'] . "?page=shop.browse&amp;category_id=$category_id");
			$link .= "\">";
			$link .= $db->f("category_name");
			$link .= "</a>";
			$category_list[$i++] = $link;

		}
		while (list(, $value) = each($category_list)) {
			$html .= $value;
		}

		return $html;
	}
	
	/**
	 * Changes the category List Order
	 * @author soeren
	 * 
	 * @param unknown_type $d
	 * @return unknown
	 */
	function reorder( &$d ) {
		global $db;

		if( !empty( $d['category_id'] )) {
			$cid = $d['category_id'][0];

			switch( $d["task"] ) {
				case "orderup":
				$q = "SELECT list_order,category_parent_id FROM #__{vm}_category,#__{vm}_category_xref ";
				$q .= "WHERE category_id='".$cid[0]."' ";
				$q .= "AND category_child_id='".$cid[0]."' ";
				$db->query($q);
				$db->next_record();
				$currentpos = $db->f("list_order");
				$category_parent_id = $db->f("category_parent_id");

				// Get the (former) predecessor and update it
				$q = "SELECT list_order,#__{vm}_category.category_id FROM #__{vm}_category, #__{vm}_category_xref ";
				$q .= "WHERE #__{vm}_category_xref.category_parent_id='$category_parent_id' ";
				$q .= "AND #__{vm}_category_xref.category_child_id=#__{vm}_category.category_id ";
				$q .= "AND list_order='". intval($currentpos - 1) . "'";
				$db->query($q);
				$db->next_record();
				$pred = $db->f("category_id");

				// Update the category and decrease the list_order
				$q = "UPDATE #__{vm}_category ";
				$q .= "SET list_order=list_order-1 ";
				$q .= "WHERE category_id='".$cid[0]."'";
				$db->query($q);

				$q = "UPDATE #__{vm}_category ";
				$q .= "SET list_order=list_order+1 ";
				$q .= "WHERE category_id='$pred'";
				$db->query($q);

				break;

				case "orderdown":
				$q = "SELECT list_order,category_parent_id FROM #__{vm}_category,#__{vm}_category_xref ";
				$q .= "WHERE category_id='".$cid[0]."' ";
				$q .= "AND category_child_id='".$cid[0]."' ";
				$db->query($q);
				$db->next_record();
				$currentpos = $db->f("list_order");
				$category_parent_id = $db->f("category_parent_id");

				// Get the (former) successor and update it
				$q = "SELECT list_order,#__{vm}_category.category_id FROM #__{vm}_category, #__{vm}_category_xref ";
				$q .= "WHERE #__{vm}_category_xref.category_parent_id='$category_parent_id' ";
				$q .= "AND #__{vm}_category_xref.category_child_id=#__{vm}_category.category_id ";
				$q .= "AND list_order='". intval($currentpos + 1) . "'";
				$db->query($q);
				$db->next_record();
				$succ = $db->f("category_id");

				$q = "UPDATE #__{vm}_category ";
				$q .= "SET list_order=list_order+1 ";
				$q .= "WHERE category_id='".$cid[0]."' ";
				$db->query($q);

				$q = "UPDATE #__{vm}_category ";
				$q .= "SET list_order=list_order-1 ";
				$q .= "WHERE category_id='$succ'";
				$db->query($q);

				break;
				case "saveorder":
				$i = 0;
				foreach( $d['category_id'] as $category_id ) {
					if( !is_numeric( $d['order'][$i] ) ) {
						$d['error'] = "Error: Please use numbers only for ordering the list!";
						return false;
					}
					$i++;
				}
				$i = 0;
				foreach( $d['category_id'] as $category_id ) {
					$q = "UPDATE #__{vm}_category ";
					$q .= "SET list_order= ".$d['order'][$i];
					$q .= " WHERE category_id='".$category_id."' ";
					$db->query($q);
					$i++;
				}
				break;
			}
		}
		return true;
	}

}
?>
