<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_product.php,v 1.24.2.14 2006/04/21 17:05:17 soeren_nb Exp $
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
 * The class is is used to manage product repository.
 * @package virtuemart
 * @author pablo, jep, gday, soeren
 * 
 */
class ps_product extends vmAbstractObject {
	var $classname = "ps_product";

	/**
	 * Validates product fields and uploaded image files.
	 *
	 * @param array $d The input vars
	 * @return boolean True when validation successful, false when not
	 */
	function validate(&$d) {
		global $vmLogger, $database, $perm;
		$valid = true;
		$db = new ps_DB;
		$ps_vendor_id = $_SESSION["ps_vendor_id"];
		if( $perm->check( 'admin' )) {
			$vendor_id = $d['vendor_id'];
		}
		else {
			$vendor_id = $ps_vendor_id;
		}

		$q = "SELECT product_id,product_thumb_image,product_full_image FROM #__{vm}_product WHERE product_sku='";
		$q .= $d["product_sku"] . "'";
		$db->setQuery($q); $db->query();
		if ($db->next_record()&&($db->f("product_id") != $d["product_id"])) {
			$vmLogger->err( "A Product with the SKU ".$d['product_sku']." already exists." );
			$valid = false;
		}
		if( !empty( $d['product_discount_id'] )) {
			if( $d['product_discount_id'] == "override" ) {

				$d['is_percent'] = "0";
				$d['amount'] = (float)$d['product_price_incl_tax'] - (float)$d['discounted_price_override'];

				require_once( CLASSPATH. 'ps_product_discount.php' );
				$ps_product_discount = new ps_product_discount;
				$ps_product_discount->add( $d );
				$d['product_discount_id'] = $database->insertid();
			}
		}
		if (empty($d['manufacturer_id'])) {
			$d['manufacturer_id'] = "1";
		}
		if (empty( $d["product_sku"])) {
			$vmLogger->err( "A Product Sku must be entered." );
			$valid = false;
		}
		if (!$d["product_name"]) {
			$vmLogger->err( "A product name must be entered." );
			$valid = false;
		}
		if (!$d["product_available_date"]) {
			$vmLogger->err( "You must provide an availability date." );
			$valid = false;
		}
		else {
			$day = substr ( $d["product_available_date"], 8, 2);
			$month= substr ( $d["product_available_date"], 5, 2);
			$year =substr ( $d["product_available_date"], 0, 4);
			$d["product_available_date_timestamp"] = mktime(0,0,0,$month, $day, $year);
		}

		/** Validate Product Specific Fields **/
		if (!$d["product_parent_id"]) {
			if (sizeof(@$d["product_categories"]) < 1) {
				$vmLogger->err( "A Category must be selected." );
				$valid = false;
			}
		}
		if( !empty($d['downloadable']) && (empty($_FILES['file_upload']['name'] ) && empty($d['filename']))) {
			$vmLogger->err( "Please specify a Product File for Download!" );
			$valid =  false;
		}

		/** Image Upload Validation **/

		// do we have an image URL or an image File Upload?
		if (!empty( $d['product_thumb_image_url'] )) {
			// Image URL
			if (substr( $d['product_thumb_image_url'], 0, 4) != "http") {
				$vmLogger->err( "Image URL must begin with http." );
				$valid =  false;
			}

			// if we have an uploaded image file, prepare this one for deleting.
			if( $db->f("product_thumb_image") && substr( $db->f("product_thumb_image"), 0, 4) != "http") {
				$_REQUEST["product_thumb_image_curr"] = $db->f("product_thumb_image");
				$d["product_thumb_image_action"] = "delete";
				if (!validate_image($d,"product_thumb_image","product")) {
					return false;
				}
			}
			$d["product_thumb_image"] = $d['product_thumb_image_url'];
		}
		else {
			// File Upload
			if (!validate_image($d,"product_thumb_image","product")) {
				$valid = false;
			}
		}

		if (!empty( $d['product_full_image_url'] )) {
			// Image URL
			if (substr( $d['product_full_image_url'], 0, 4) != "http") {
				$vmLogger->err( "Image URL must begin with http." );
				return false;
			}
			// if we have an uploaded image file, prepare this one for deleting.
			if( $db->f("product_full_image") && substr( $db->f("product_thumb_image"), 0, 4) != "http") {
				$_REQUEST["product_full_image_curr"] = $db->f("product_full_image");
				$d["product_full_image_action"] = "delete";
				if (!validate_image($d,"product_full_image","product")) {
					return false;
				}
			}
			$d["product_full_image"] = $d['product_full_image_url'];
		}
		else {
			// File Upload
			if (!validate_image($d,"product_full_image","product")) {
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
	 * Validates that a product can be deleted
	 *
	 * @param array $d The input vars
	 * @return boolean Validation sucessful?
	 */
	function validate_delete( $product_id, &$d ) {
		global $vmLogger;
		/* Check that ps_vendor_id and product_id match
		if (!$this->check_vendor($d)) {
			$d["error"] = "ERROR: Cannot delete product. Wrong product or vendor." ;
			return false;
		}*/
		if (empty($product_id)) {
			$vmLogger->err( "Please specify a Product to delete!" );
			return false;
		}
		/* Get the image filenames from the database */
		$db = new ps_DB;
		$q  = "SELECT product_thumb_image,product_full_image ";
		$q .= "FROM #__{vm}_product ";
		$q .= "WHERE product_id='$product_id'";
		$db->setQuery($q); $db->query();
		$db->next_record();

		/* Prepare product_thumb_image for Deleting */
		if( !stristr( $db->f("product_thumb_image"), "http") ) {
			$_REQUEST["product_thumb_image_curr"] = $db->f("product_thumb_image");
			$d["product_thumb_image_action"] = "delete";
			if (!validate_image($d,"product_thumb_image","product")) {
				$vmLogger->err( "Failed deleting Product Images!" );
				return false;
			}
		}
		/* Prepare product_full_image for Deleting */
		if( !stristr( $db->f("product_full_image"), "http") ) {
			$_REQUEST["product_full_image_curr"] = $db->f("product_full_image");
			$d["product_full_image_action"] = "delete";
			if (!validate_image($d,"product_full_image","product")) {
				return false;
			}
		}
		return true;

	}

	/**
	 * Function to add a new product into the product table
	 *
	 * @param array $d The input vars
	 * @return boolean True, when the product was added, false when not
	 */
	function add( &$d ) {
		global $perm, $vmLogger;
		$database = new ps_DB();
		
		if (!$this->validate($d)) {
			return false;
		}

		if (!process_images($d)) {
			return false;
		}

		$timestamp = time();
		$db = new ps_DB;

		if (empty($d["product_publish"])) {
			$d["product_publish"] = "N";
		}

		if (empty($d["clone_product"])) {
			$d["clone_product"] = "N";
		}

		// added for advanced attribute modification
		// strips the trailing semi-colon from an attribute
		if (';' == substr($d["product_advanced_attribute"], strlen($d["product_advanced_attribute"])-1,1) ) {
			$d["product_advanced_attribute"] =substr($d["product_advanced_attribute"], 0, strlen($d["product_advanced_attribute"])-1);
		}
		// added for custom attribute modification
		// strips the trailing semi-colon from an attribute
		if (';' == substr($d["product_custom_attribute"], strlen($d["product_custom_attribute"])-1,1) ) {
			$d["product_custom_attribute"] =substr($d["product_custom_attribute"], 0, strlen($d["product_custom_attribute"])-1);
		}
		$d["product_special"] = empty($d["product_special"]) ? "N" : "Y";

		$q  = "INSERT INTO #__{vm}_product (vendor_id,product_parent_id,product_sku,";
		$q .= "product_name,product_desc,product_s_desc,";
		$q .= "product_thumb_image,product_full_image,";
		$q .= "product_publish,product_weight,product_weight_uom,";
		$q .= "product_length,product_width,product_height,product_lwh_uom,";
		$q .= "product_unit,product_packaging,"; // Changed Packaging - Added
		$q .= "product_url,product_in_stock,";
		$q .= "attribute,custom_attribute,";
		$q .= "product_available_date,product_availability,product_special,product_discount_id,";
		$q .= "cdate,mdate,product_tax_id) ";
		$q .= "VALUES ('";
		$q .= $d['vendor_id'] . "','" . $d["product_parent_id"] . "','";
		$q .= $d["product_sku"] . "','" . $d["product_name"] . "','";
		$q .= $d["product_desc"] . "','" . $d["product_s_desc"] . "','";
		$q .= $d["product_thumb_image"] . "','";
		$q .= $d["product_full_image"] . "','" . $d["product_publish"] . "','";
		$q .= $d["product_weight"] . "','" . $d["product_weight_uom"] . "','";
		$q .= $d["product_length"] . "','" . $d["product_width"] . "','";
		$q .= $d["product_height"] . "','" . $d["product_lwh_uom"] . "','";
		$q .= $d["product_unit"] . "','" . (($d["product_box"] << 16) | ($d["product_packaging"]&0xFFFF)) . "','"; // Changed Packaging - Added
		$q .= $d["product_url"] . "','" . $d["product_in_stock"] . "','";
		$q .= $d["product_advanced_attribute"]."','";
		$q .= $d["product_custom_attribute"]."','";
		$q .= $d["product_available_date_timestamp"] . "','";
		$q .= $d["product_availability"] . "','";
		$q .= $d["product_special"] . "','";
		$q .= $d["product_discount_id"] . "','$timestamp','$timestamp','".$d["product_tax_id"]."')";

		$db->setQuery($q); $db->query();

		$d["product_id"] = $db->last_insert_id();

		// If is Item, add attributes from parent //
		if ($d["product_parent_id"]) {
			$q  = "SELECT attribute_name FROM #__{vm}_product_attribute_sku ";
			$q .= "WHERE product_id='" . $d["product_parent_id"] . "' ";
			$q .= "ORDER BY attribute_list,attribute_name";

			$db->setQuery($q); $db->query();

			$db2 = new ps_DB;
			$i = 0;
			while($db->next_record()) {
				$i++;
				$q = "INSERT INTO #__{vm}_product_attribute VALUES ";
				$q .= "('".$d["product_id"]."', '".$db->f("attribute_name")."', '".$d["attribute_$i"]."')";
				$db2->query( $q );
			}
		}
		else {
			/* If is Product, Insert category ids */
			foreach( $d["product_categories"] as $category_id ) {
				$q  = "INSERT INTO #__{vm}_product_category_xref ";
				$q .= "(category_id,product_id) ";
				$q .= "VALUES ('$category_id','". $d["product_id"] . "')";
				$db->setQuery($q); $db->query();
			}
		}
		$q = "INSERT INTO #__{vm}_product_mf_xref VALUES (";
		$q .= "'".$d['product_id']."', '".$d['manufacturer_id']."')";
		$db->setQuery($q); $db->query();

		// Handle "Downloadable Product" Queries and File copying
		if (@$d['downloadable'] == "Y") {
			if( !empty( $_FILES['file_upload']['name'] )) {
				require_once(  CLASSPATH.'ps_product_files.php' );
				$ps_product_files =& new ps_product_files();
				// Set file-add values
				$d["file_published"] = "1";
				$d["upload_dir"] = "DOWNLOADPATH";
				$d["file_title"] = $_FILES['file_upload']['name'];
				$d["file_url"] = "";
				if( !$ps_product_files->add( $d ) ) {
					$d['error'] = 'Error: Failed to upload the downloadable file.';
				}
			}
			else {
				$d["file_title"] = $d["filename"];
			}
			// Insert an attribute called "download", attribute_value: filename
			$q2  = "INSERT INTO #__{vm}_product_attribute ";
			$q2 .= "(product_id,attribute_name,attribute_value) ";
			$q2 .= "VALUES ('" . $d["product_id"] . "','download','".$d["file_title"]."')";
			$db->setQuery($q2);
			$db->query();
		}
		if( !empty($d["related_products"])) {
			/* Insert Pipe separated Related Product IDs */
			$related_products = implode( "|", $d["related_products"] );

			$q  = "INSERT INTO #__{vm}_product_relations ";
			$q .= "(product_id, related_products) ";
			$q .= "VALUES ('".$d["product_id"]."','$related_products')";
			$db->setQuery($q); $db->query();

		}
		// ADD A PRICE, IF NOT EMPTY ADD 0
		if (!empty($d['product_price'])) {

			if(empty($d['product_currency']))
			$d['product_currency'] = $_SESSION['vendor_currency'];

			$d["price_quantity_start"] = 0;
			$d["price_quantity_end"] = "";
			require_once ( CLASSPATH. 'ps_product_price.php');
			$my_price = new ps_product_price;
			$my_price->add($d);
		}

		// CLONE PRODUCT additional code
		if( $d["clone_product"] == "Y" ) {

			// Clone Parent Product's Attributes
			$q  = "INSERT INTO #__{vm}_product_attribute_sku
              SELECT '".$d["product_id"]."', attribute_name, attribute_list 
              FROM #__{vm}_product_attribute_sku WHERE product_id='" . $d["old_product_id"] . "' ";
			$db->query( $q );
			if( !empty( $d["child_items"] )) {

				$database->query( "SHOW COLUMNS FROM #__{vm}_product" );
				$rows = $database->record;
				while(list(,$Field) = each( $rows) ) {
					$product_fields[$Field->Field] = $Field->Field;
				}
				// Change the Field Names
				// leave empty for auto_increment
				$product_fields["product_id"] = "''";
				// Update Product Parent ID to the new one
				$product_fields["product_parent_id"] = "'".$d["product_id"]."'";
				// Rename the SKU
				$product_fields["product_sku"] = "CONCAT(product_sku,'_".$d["product_id"]."')";

				$rows = Array();
				$database->query( "SHOW COLUMNS FROM #__{vm}_product_price" );
				$rows = $database->record;
				while(list(,$Field) = each( $rows) ) {
					$price_fields[$Field->Field] = $Field->Field;
				}

				foreach( $d["child_items"] as $child_id ) {
					$q = "INSERT INTO #__{vm}_product ";
					$q .= "SELECT ".implode(",", $product_fields )." FROM #__{vm}_product WHERE product_id='$child_id'";
					$db->query( $q );
					$new_product_id = $db->last_insert_id();

					$q = "INSERT INTO #__{vm}_product_attribute
                  SELECT '$new_product_id', attribute_name, attribute_value
                  FROM #__{vm}_product_attribute WHERE product_id='$child_id'";
					$db->query( $q );

					$price_fields["product_price_id"] = "''";
					$price_fields["product_id"] = "'$new_product_id'";

					$q = "INSERT INTO #__{vm}_product_price ";
					$q .= "SELECT ".implode(",", $price_fields )." FROM #__{vm}_product_price WHERE product_id='$child_id'";
					$db->query( $q );
				}
			}

			// End Cloning
			
		}
		if( $d['clone_product'] == 'Y') {
			$vmLogger->info( "Product was successfully cloned." );
		}
		else {
			$vmLogger->info( "Product was successfully added." );
		}
		return true;
	}

	/**
	 * Function to update product $d['product_id'] in the product table
	 *
	 * @param array $d The input vars
	 * @return boolean True, when the product was updated, false when not
	 */
	function update( &$d ) {
		global $vmLogger;
		
		if (!$this->validate($d)) {
			return false;
		}

		if (!process_images($d)) {
			return false;
		}

		$timestamp = time();
		$db = new ps_DB;

		// added for the advanced attribute hack
		// strips the trailing semi-colon from an attribute
		if (';' == substr($d["product_advanced_attribute"], strlen($d["product_advanced_attribute"])-1,1) ) {
			$d["product_advanced_attribute"] =substr($d["product_advanced_attribute"], 0, strlen($d["product_advanced_attribute"])-1);
		}
		// added for the custom attribute hack
		// strips the trailing semi-colon from an attribute
		if (';' == substr($d["product_custom_attribute"], strlen($d["product_custom_attribute"])-1,1) ) {
			$d["product_custom_attribute"] =substr($d["product_custom_attribute"], 0, strlen($d["product_custom_attribute"])-1);
		}

		if (empty($d["product_special"])) $d["product_special"] = "N";
		if (empty($d["product_publish"])) $d["product_publish"] = "N";

		$q  = "UPDATE #__{vm}_product SET ";
		$q .= "product_sku='" . $d["product_sku"] . "',";
		$q .= "vendor_id='" . $d["vendor_id"] . "',";
		$q .= "product_name='" . $d["product_name"] . "',";
		$q .= "product_s_desc='" . $d["product_s_desc"] . "',";
		$q .= "product_desc='" . $d["product_desc"] . "',";
		$q .= "product_publish='" . $d["product_publish"] . "',";
		$q .= "product_weight='" . $d["product_weight"] . "',";
		$q .= "product_weight_uom='" . $d["product_weight_uom"] . "',";
		$q .= "product_length='" . $d["product_length"] . "',";
		$q .= "product_width='" . $d["product_width"] . "',";
		$q .= "product_height='" . $d["product_height"] . "',";
		$q .= "product_lwh_uom='" . $d["product_lwh_uom"] . "',";
		$q .= "product_unit='" . $d["product_unit"] . "',"; // Changed Packaging - Added
		$q .= "product_packaging='" . (($d["product_box"]<<16) | ($d["product_packaging"] & 0xFFFF)) . "',"; // Changed Packaging - Added
		$q .= "product_url='" . $d["product_url"] . "',";
		$q .= "product_in_stock='" . $d["product_in_stock"] . "',";
		$q .= "product_available_date='";
		$q .= $d["product_available_date_timestamp"] . "',";
		$q .= "product_availability='" . $d["product_availability"] . "',";
		$q .= "product_special='" . $d["product_special"] . "',";
		$q .= "product_discount_id='" . $d["product_discount_id"] . "',";
		$q .= "product_thumb_image='" . $d["product_thumb_image"] . "',";
		$q .= "product_full_image='" . $d["product_full_image"] . "',";
		$q .= "attribute='".$d["product_advanced_attribute"]."',";
		$q .= "custom_attribute='".$d["product_custom_attribute"]."',";
		$q .= "product_tax_id='".$d["product_tax_id"]."',";
		$q .= "mdate='$timestamp' ";
		$q .= "WHERE product_id='" . $d["product_id"] . "'";
		//$q .= "AND vendor_id='" . $d['vendor_id'] . "'";

		$db->setQuery($q); $db->query();

		/* notify the shoppers that the product is here */
		/* see zw_waiting_list */
		if ($d["product_in_stock"] > "0") {
			require_once( CLASSPATH . 'zw_waiting_list.php');
			$zw_waiting_list = new zw_waiting_list;
			$zw_waiting_list->notify_list($d["product_id"]);
		}

		// Check for download
		$q_dl = "SELECT attribute_name,attribute_value FROM #__{vm}_product_attribute WHERE ";
		$q_dl .= "product_id='".$d["product_id"]."' AND attribute_name='download' ";
		$db->query($q_dl);
		$db->next_record();
		if ($db->num_rows() > 0) { // found one
			$q_dl = "SELECT file_id from #__{vm}_product_files WHERE ";
			$q_dl .= "file_product_id='".$d["product_id"]."' AND file_title='".$GLOBALS['vmInputFilter']->safeSQL( $db->f("attribute_value"))."'";
			$db->query($q_dl);
			$db->next_record();
			$d["file_id"] = $db->f("file_id");
	
			if ( @$d['downloadable'] != "Y" ) {
	
				// delete the attribute
				$q_del = "DELETE FROM #__{vm}_product_attribute WHERE ";
				$q_del .= "product_id='".$d["product_id"]."' AND attribute_name='download'";
				$db->query($q_del);
	
				if( !empty($d["file_id"])) {
					require_once(  CLASSPATH.'ps_product_files.php' );
					$ps_product_files =& new ps_product_files();
					// Delete the existing file entry
					$ps_product_files->delete( $d );
				}
			}
			else { // update the attribute
		
				require_once(  CLASSPATH.'ps_product_files.php' );
				$ps_product_files =& new ps_product_files();
		
				if( !empty($_FILES['file_upload']['name'])) {
					// Set file-add values
					$d["file_published"] = "1";
					$d["upload_dir"] = "DOWNLOADPATH";
					$d["file_title"] = $_FILES['file_upload']['name'];
					$d["file_url"] = "";
		
					$ps_product_files->add( $d );
					$qu = "UPDATE #__{vm}_product_attribute ";
					$qu.= "SET attribute_value = '". $d["file_title"] ."' ";
					$qu .= "WHERE product_id='".$d["product_id"]."' AND attribute_name='download'";
					$db->query($qu);
				}
				else {
					$d["file_id"] = "";
					$qu = "UPDATE #__{vm}_product_attribute ";
					$qu.= "SET attribute_value = '". $d['filename'] ."' ";
					$qu .= "WHERE product_id='".$d["product_id"]."' AND attribute_name='download'";
					$db->query($qu);
				}
		
				if( !empty($d["file_id"])) {
					// Now: Delete the existing file entry
					$ps_product_files->delete( $d );
				}
	
			}
		}
		else {  // found none
			require_once(  CLASSPATH.'ps_product_files.php' );
			$ps_product_files =& new ps_product_files();
			if ( @$d['downloadable'] == "Y" && !empty($_FILES['file_upload']['name'])) {
				// Set file-add values
				$d["file_published"] = "1";
				$d["upload_dir"] = "DOWNLOADPATH";
				$d["file_title"] = $_FILES['file_upload']['name'];
				$d["file_url"] = "";
				$ps_product_files->add( $d );
	
				// Insert an attribute called "download", attribute_value: filename
				$q2  = "INSERT INTO #__{vm}_product_attribute ";
				$q2 .= "(product_id,attribute_name,attribute_value) ";
				$q2 .= "VALUES ('" . $d["product_id"] . "','download','".$d["file_title"]."')";
				$db->setQuery($q2);
				$db->query();
			}
			elseif ( @$d['downloadable'] == "Y" ) {
				// Insert an attribute called "download", attribute_value: filename
				$q2  = "INSERT INTO #__{vm}_product_attribute ";
				$q2 .= "(product_id,attribute_name,attribute_value) ";
				$q2 .= "VALUES ('" . $d["product_id"] . "','download','".$d["filename"]."')";
				$db->setQuery($q2);
				$db->query();
			}
		}
		// End download check

		$q = "UPDATE #__{vm}_product_mf_xref SET ";
		$q .= "manufacturer_id='".$d['manufacturer_id']."' ";
		$q .= "WHERE product_id = '".$d['product_id']."'";
		$db->setQuery($q); $db->query();


		/* If is Item, update attributes */
		if ($d["product_parent_id"]) {
			$q  = "SELECT attribute_name FROM #__{vm}_product_attribute_sku ";
			$q .= "WHERE product_id='" . $d["product_parent_id"] . "' ";
			$q .= "ORDER BY attribute_list,attribute_name";

			$db->setQuery($q); $db->query();

			$db2 = new ps_DB;
			$i = 0;
			while($db->next_record()) {
				$i++;
				$q2  = "UPDATE #__{vm}_product_attribute SET ";
				$q2 .= "attribute_value='" . $d["attribute_$i"] . "' ";
				$q2 .= "WHERE product_id = '" . $d["product_id"] . "' ";
				$q2 .= "AND attribute_name = '" . $db->f("attribute_name") . "' ";
				$db2->setQuery($q2); $db2->query();
			}
			/* If it is a Product, update Category */
		}
		else {
			// DELETE ALL OLD CATEGORY_XREF ENTRIES!
			$q  = "DELETE FROM #__{vm}_product_category_xref ";
			$q .= "WHERE product_id = '" . $d["product_id"] . "' ";
			$db->setQuery($q);
			$db->query();

			// NOW Re-Insert
			foreach( $d["product_categories"] as $category_id ) {
				$q  = "INSERT INTO #__{vm}_product_category_xref ";
				$q .= "(category_id,product_id) ";
				$q .= "VALUES ('$category_id','". $d["product_id"] . "')";
				$db->setQuery($q); $db->query();
			}
		}

		if( !empty($d["related_products"])) {
			/* Insert Pipe separated Related Product IDs */
			$related_products = implode( "|", $d["related_products"] );

			$q  = "REPLACE INTO #__{vm}_product_relations (product_id, related_products)";
			$q .= " VALUES( '".$d["product_id"]."', '$related_products') ";
			$db->setQuery($q); $db->query();

		}
		else{
			$q  = "DELETE FROM #__{vm}_product_relations ";
			$q .= " WHERE product_id='".$d["product_id"]."'";
			$db->setQuery($q); $db->query();
		}

		// UPDATE THE PRICE, IF EMPTY ADD 0
		if(empty($d['product_currency'])) {
			$d['product_currency'] = $_SESSION['vendor_currency'];
		}

		// look if we have a price for this product
		$q = "SELECT product_price_id, price_quantity_start, price_quantity_end FROM #__{vm}_product_price ";
		$q .= "WHERE shopper_group_id = '" . $d["shopper_group_id"] ."' ";
		$q .= "AND product_id = '" . $d["product_id"] ."'";
		$db->query($q);


		if ($db->next_record()) {
			
			$d["product_price_id"] = $db->f("product_price_id");
			require_once ( CLASSPATH. 'ps_product_price.php');
			$my_price = new ps_product_price;
				
			if (@$d['product_price'] != '') {
				// update prices
				$d["price_quantity_start"] = $db->f("price_quantity_start");
				$d["price_quantity_end"] = $db->f("price_quantity_end");
				
				$my_price->update($d);
			}
			else {
				// delete the price
				$my_price->delete( $d );	
			}
		}
		else {
			if ( $d['product_price'] != '' ) {
				// add the price
				$d["price_quantity_start"] = 0;
				$d["price_quantity_end"] = "";
				require_once ( CLASSPATH. 'ps_product_price.php');
				$my_price = new ps_product_price;
				$my_price->add($d);
			}
		}
		
		/** Product Type - Begin */
		$product_id=$d["product_id"];

		$q  = "SELECT * FROM #__{vm}_product_product_type_xref WHERE ";
		$q .= "product_id='$product_id' ";
		$db->query($q);

		$dbpt = new ps_DB;
		$dbp = new ps_DB;

		// For every Product Type
		while ($db->next_record()) {
			$product_type_id = $db->f("product_type_id");

			$q  = "SELECT * FROM #__{vm}_product_type_parameter WHERE ";
			$q .= "product_type_id='$product_type_id' ";
			$q .= "ORDER BY parameter_list_order";
			$dbpt->query($q);

			/*      $q  = "SELECT * FROM #__{vm}_product_type_$product_type_id WHERE ";
			$q .= "product_id='$product_id'";
			$dbp->query($q);
			if (!$dbp->next_record()) {  // Add record if not exist (Items)
			$q  = "INSERT INTO #__{vm}_product_type_$product_type_id (product_id) ";
			$q .= "VALUES ('$product_id')";
			$dbp->setQuery($q); $dbp->query();
			}*/

			// Update record
			$q  = "UPDATE #__{vm}_product_type_$product_type_id SET ";
			$q .= "product_id='$product_id'";
			while ($dbpt->next_record()) {
				if ($dbpt->f("parameter_type")!="B") { // if it is not breaker
				$value=$d["product_type_".$product_type_id."_".$dbpt->f("parameter_name")];
				if ($dbpt->f("parameter_type")=="V" && is_array($value))
				$value = join(",",$value);
				if ($value=="") {
					$value="NULL"; }
					else { $value="'$value'"; }
					$q .= ",`".$dbpt->f("parameter_name")."`=".$value;
				}
			}
			$q .= " WHERE product_id = '".$d['product_id']."'";
			$dbp->setQuery($q); $dbp->query();
		}
		/** Product Type - End */
		$vmLogger->info( "Product was successfully updated" );
		return true;
	}


	/**
	 * Function to delete product(s) $d['product_id'] from the product table
	 *
	 * @param array $d The input vars
	 * @return boolean True, when the product was deleted, false when not
	 */
	function delete(&$d) {

		$product_id = $d["product_id"];

		if( is_array( $product_id)) {
			foreach( $product_id as $product) {
				if( !$this->delete_product( $product, $d ))
				return false;
			}
			return true;
		}
		else {
			return $this->delete_product( $product_id, $d );
		}
	}
	
	/**
	 * The function that holds the code for deleting
	 * one product from the database and all related tables
	 * plus deleting files related to the product
	 *
	 * @param int $product_id
	 * @param array $d The input vars
	 * @return boolean True on success, false on error
	 */
	function delete_product( $product_id, &$d ) {
		global $db, $vmLogger;

		if (!$this->validate_delete($product_id, $d)) {
			return false;
		}
		/* If is Product */
		if ($this->is_product($product_id)) {
			/* Delete all items first */
			$q  = "SELECT product_id FROM #__{vm}_product WHERE product_parent_id='$product_id'";
			$db->setQuery($q); $db->query();
			while($db->next_record()) {
				$d2["product_id"] = $db->f("product_id");
				if (!$this->delete($d2)) {
					return false;
				}
			}

			/* Delete attributes */
			$q  = "DELETE FROM #__{vm}_product_attribute_sku WHERE product_id='$product_id' ";
			$db->setQuery($q); $db->query();

			/* Delete categories xref */
			$q  = "DELETE FROM #__{vm}_product_category_xref WHERE product_id = '$product_id' ";
			$db->setQuery($q); $db->query();
		}
		/* If is Item */
		else {
			/* Delete attribute values */
			$q  = "DELETE FROM #__{vm}_product_attribute WHERE product_id='$product_id'";
			$db->setQuery($q); $db->query();
		}
		/* For both Product and Item */

		/* Delete product - manufacturer xref */
		$q = "DELETE FROM #__{vm}_product_mf_xref WHERE product_id='$product_id'";
		$db->setQuery($q); $db->query();

		/* Delete Product - ProductType Relations */
		$q  = "DELETE FROM `#__{vm}_product_product_type_xref` WHERE `product_id`=$product_id";
		$db->setQuery($q); $db->query();
		
		/* Delete product votes */
		$q  = "DELETE FROM #__{vm}_product_votes WHERE product_id='$product_id'";
		$db->setQuery($q); $db->query();

		/* Delete product reviews */
		$q = "DELETE FROM #__{vm}_product_reviews WHERE product_id='$product_id'";
		$db->setQuery($q); $db->query();

		/* Delete Image files */
		if (!process_images($d)) {
			return false;
		}
		/* Delete other Files and Images files */
		require_once(  CLASSPATH.'ps_product_files.php' );
		$ps_product_files =& new ps_product_files();

		$db->query( "SELECT file_id FROM #__{vm}_product_files WHERE file_product_id='$product_id'" );
		while($db->next_record()) {
			$d["file_id"] = $db->f("file_id");
			$ps_product_files->delete( $d );
		}

		/* Delete Product Relations */
		$q  = "DELETE FROM #__{vm}_product_relations WHERE product_id = '$product_id'";
		$db->setQuery($q); $db->query();

		/* Delete Prices */
		$q  = "DELETE FROM #__{vm}_product_price WHERE product_id = '$product_id'";
		$db->setQuery($q); $db->query();

		/* Delete entry FROM #__{vm}_product table */
		$q  = "DELETE FROM #__{vm}_product WHERE product_id = '$product_id'";
		$db->setQuery($q); $db->query();

		/* If only deleting an item, go to the parent product page after
		** the deletion. This had to be done here because the product id
		** of the item to be deleted had to be passed as product_id */
		if (!empty($d["product_parent_id"])) {
			$d["product_id"] = $d["product_parent_id"];
			$d["product_parent_id"] = "";
		}
		$vmLogger->info( "Deleted Product ID: $product_id" );
		return true;
	}

	/**
	 * Function to check if the vendor_id of the product
	 * $d['product_id'] matches the vendor_id associated with the
	 * user that calls this function
	 *
	 * @param array $d
	 * @return boolean True, when vendor_id matches, false when not
	 */
	function check_vendor($d) {

		$ps_vendor_id = $_SESSION["ps_vendor_id"];

		$db = new ps_DB;
		$q  = "SELECT vendor_id  FROM #__{vm}_product ";
		$q .= "WHERE vendor_id = '$ps_vendor_id' ";
		$q .= "AND product_id = '" . $d["product_id"] . "' ";
		$db->setQuery($q); $db->query();
		if ($db->next_record()) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * Function to create a ps_DB object holding the data of product $d['product_id']
	 * from the table #__{vm}_product
	 *
	 * @param int $product_id
	 * @return ps_DB DB object holding all data for product $product_id
	 */
	function sql($product_id) {
		$db = new ps_DB;

		$q  = "SELECT * FROM #__{vm}_product WHERE product_id='$product_id' ";

		$db->setQuery($q); $db->query();
		return $db;
	}
	
	/**
	 * Function to create a db object holding the data of all child items of
	 * product $product_id
	 *
	 * @param int $product_id
	 * @return ps_DB object that holds all items of product $product_id
	 */
	function items_sql($product_id) {
		$db = new ps_DB;

		$q  = "SELECT * FROM #__{vm}_product ";
		$q .= "WHERE product_parent_id='$product_id' ";
		$q .= "ORDER BY product_name";

		$db->setQuery($q); $db->query();
		return $db;
	}

	/**
	 * Function to check whether a product is a parent product or not
	 *
	 * @param int $product_id
	 * @return boolean True when the product is a parent product, false when product is a child item
	 */
	function is_product($product_id) {
		$db = new ps_DB;

		$q  = "SELECT product_parent_id FROM #__{vm}_product ";
		$q .= "WHERE product_id='$product_id' ";

		$db->setQuery($q); $db->query();
		$db->next_record();
		if ($db->f("product_parent_id") == 0) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Function to create a DB object that holds all information
	 * from the attribute tables about item $item_id AND/OR product $product_id
	 *
	 * @param int $item_id The product_id of the item
	 * @param int $product_id The product_id of the parent product
	 * @param string $attribute_name The name of the attribute to filter
	 * @return ps_DB The db object...
	 */
	function attribute_sql($item_id="",$product_id="",$attribute_name="") {
		$db = new ps_DB;
		if ($item_id and $product_id) {
			$q  = "SELECT * FROM #__{vm}_product_attribute,#__{vm}_product_attribute_sku ";
			$q .= "WHERE #__{vm}_product_attribute.product_id = '$item_id' ";
			$q .= "AND #__{vm}_product_attribute_sku.product_id ='$product_id' ";
			if ($attribute_name) {
				$q .= "AND #__{vm}_product_attribute.attribute_name = $attribute_name ";
			}
			$q .= "AND #__{vm}_product_attribute.attribute_name = ";
			$q .=     "#__{vm}_product_attribute_sku.attribute_name ";
			$q .= "ORDER BY attribute_list,#__{vm}_product_attribute.attribute_name";
		} elseif ($item_id) {
			$q  = "SELECT * FROM #__{vm}_product_attribute ";
			$q .= "WHERE product_id=$item_id ";
			if ($attribute_name) {
				$q .= "AND attribute_name = '$attribute_name' ";
			}
		} elseif ($product_id) {
			$q  = "SELECT * FROM #__{vm}_product_attribute_sku ";
			$q .= "WHERE product_id ='$product_id' ";
			if ($attribute_name) {
				$q .= "AND #__{vm}_product_attribute.attribute_name = $attribute_name ";
			}
			$q .= "ORDER BY attribute_list,attribute_name";
		} else {
			/* Error: no arguments were provided. */
			return 0;
		}

		$db->setQuery($q); $db->query();

		return $db;
	}

	/**
	 * Function to return the product ids of all child items of product $pid
	 *
	 * @param int $pid The ID of the parent product
	 * @return array $list
	 */
	function get_child_product_ids($pid) {
		$db = new ps_DB;
		$q  = "SELECT product_id FROM #__{vm}_product ";
		$q .= "WHERE product_parent_id='$pid' ";

		$db->setQuery($q); $db->query();

		$i = 0;
		$list = Array();
		while($db->next_record()) {
			$list[$i] = $db->f("product_id");
			$i++;
		}
		return $list;
	}

	/**
	 * Function to quickly check whether a product has child products or not
	 *
	 * @param int $pid The id of the product to check
	 * @return boolean True when the product has childs, false when not
	 */
	function parent_has_children($pid) {
		$db = new ps_DB;
		if( empty($GLOBALS['product_info'][$pid]["parent_has_children"] )) {
			$q  = "SELECT product_id as num_rows FROM #__{vm}_product WHERE product_parent_id='$pid' ";
			$db->setQuery($q); $db->query();
			if ($db->next_record()) {
				$GLOBALS['product_info'][$pid]["parent_has_children"] = True;
			}
			else {
				$GLOBALS['product_info'][$pid]["parent_has_children"] = False;
			}
		}
		return $GLOBALS['product_info'][$pid]["parent_has_children"];
	}

	/**
	 * Function to quickly check whether a product has attributes or not
	 *
	 * @param int $pid The id of the product to check
	 * @return boolean True when the product has attributes, false when not
	 */
	function product_has_attributes($pid) {
		if( is_array($pid)) {
			return false;
		}
		$db = new ps_DB;
		if( empty($GLOBALS['product_info'][$pid]["product_has_attributes"] )) {
			$q  = "SELECT product_id FROM #__{vm}_product_attribute_sku WHERE product_id='$pid' ";
			$db->setQuery($q); $db->query();
			if ($db->next_record()) {
				$GLOBALS['product_info'][$pid]["product_has_attributes"] = True;
			}
			else {
				$GLOBALS['product_info'][$pid]["product_has_attributes"] = False;
			}
		}
		return $GLOBALS['product_info'][$pid]["product_has_attributes"];
	}

	/**
	 * Get the value of the field $field_name for product $product_id from the product table
	 *
	 * @param int $product_id
	 * @param string $field_name
	 * @return string The value of the field $field_name for that product
	 */
	function get_field( $product_id, $field_name ) {
		$db = new ps_DB;
		if( empty($GLOBALS['product_info'][$product_id][$field_name] )) {
			$q = "SELECT  product_id, `$field_name` FROM #__{vm}_product WHERE product_id='$product_id'";
			$db->query($q);
			if ($db->next_record()) {
				$GLOBALS['product_info'][$product_id][$field_name] = $db->f($field_name);
			}
			else {
				$GLOBALS['product_info'][$product_id][$field_name] = false;
			}
		}
		return $GLOBALS['product_info'][$product_id][$field_name];
	}

	/**
	 * Sets a global value for a fieldname for a specific product
	 * Is to be used by other scripts to populate a field value for a prodct
	 * that was already fetched from the database - so it doesn't need to e fetched again
	 * Can be also used to override a value
	 *
	 * @param int $product_id
	 * @param string $field_name
	 * @param mixed $value
	 */
	function set_field( $product_id, $field_name, $value ) {

		$GLOBALS['product_info'][$product_id][$field_name] = $value;

	}

	/**
	 * This is a very time consuming function. 
	 * It fetches the category flypage for a specific product id
	 *
	 * @param int $product_id
	 * @return string The flypage value for that product
	 */
        function get_flypage($product_id) {
 
            if( empty( $_SESSION['product_sess'][$product_id]['flypage'] )) {
                    $db = new ps_DB;
                    $productParentId = $product_id;
                    do {
                        $q = "SELECT
                                `#__{vm}_product`.`product_parent_id` AS product_parent_id,
                                `#__{vm}_category`.`category_flypage`
                        FROM
                                `#__{vm}_product`

                        LEFT JOIN `#__{vm}_product_category_xref` ON `#__{vm}_product_category_xref`.`product_id` = `#__{vm}_product`.`product_id`
                        LEFT JOIN `#__{vm}_category` ON `#__{vm}_product_category_xref`.`category_id` = `#__{vm}_category`.`category_id`

                        WHERE `#__{vm}_product`.`product_id`='$productParentId'
                        ";

                        $db->query($q);
                        $db->next_record();
                        $productParentId = $db->f("product_parent_id");
                    } 
                    while( $db->f("product_parent_id") && !$db->f("category_flypage"));

                    if ($db->f("category_flypage")) {
                            $_SESSION['product_sess'][$product_id]['flypage'] = $db->f("category_flypage");
                    } else {
                            $_SESSION['product_sess'][$product_id]['flypage'] = FLYPAGE;
                    }
            }
            return $_SESSION['product_sess'][$product_id]['flypage'];
        }
	
	/**
	 * Function to get the name of the vendor the product is associated with
	 *
	 * @param int $product_id
	 * @return string The name of the vendor
	 */
	function get_vendorname($product_id) {
		$db = new ps_DB;

		$q = "SELECT #__{vm}_vendor.vendor_name FROM #__{vm}_product, #__{vm}_vendor ";
		$q .= "WHERE #__{vm}_product.product_id='$product_id' ";
		$q .= "AND #__{vm}_vendor.vendor_id=#__{vm}_product.vendor_id";

		$db->query($q);
		$db->next_record();
		if ($db->f("vendor_name")) {
			return $db->f("vendor_name");
		}
		else {
			return "";
		}
	}

	/**
	 * Function to get the name of a vendor by its id
	 * @author pablo
	 * @param int $vendor_id
	 * @return string The name of the vendor
	 */
	function get_vend_idname($vendor_id) {
		$db = new ps_DB;

		$q = "SELECT vendor_name,vendor_id FROM #__{vm}_vendor ";
		$q .= "WHERE vendor_id='$vendor_id'";

		$db->query($q);
		$db->next_record();
		if ($db->f("vendor_name")) {
			return $db->f("vendor_name");
		}
		else {
			return "";
		}
	}

	/**
	 * Function to get the vendor_id of a product
	 * @author pablo
	 * @param int $product_id
	 * @return int The vendor id
	 */
	function get_vendor_id($product_id) {
		$db = new ps_DB;
		if( empty( $_SESSION['product_sess'][$product_id]['vendor_id'] )) {
			$q = "SELECT vendor_id FROM #__{vm}_product ";
			$q .= "WHERE product_id='$product_id' ";

			$db->query($q);
			$db->next_record();
			if ($db->f("vendor_id")) {
				$_SESSION['product_sess'][$product_id]['vendor_id'] = $db->f("vendor_id");
			}
			else {
				$_SESSION['product_sess'][$product_id]['vendor_id'] = "";
			}
		}
		return $_SESSION['product_sess'][$product_id]['vendor_id'];
	}

	/**
	 * Function to get the manufacturer id the product $product_id is assigned to
	 * @author soeren
	 * @param int $product_id
	 * @return int The manufacturer id
	 */
	function get_manufacturer_id($product_id) {
		$db = new ps_DB;

		$q = "SELECT manufacturer_id FROM #__{vm}_product_mf_xref ";
		$q .= "WHERE product_id='$product_id' ";

		$db->query($q);
		$db->next_record();
		if ($db->f("manufacturer_id")) {
			return $db->f("manufacturer_id");
		}
		else {
			return false;
		}
	}

	/**
	 * Functon to get the name of the manufacturer this product is assigned to
	 *
	 * @param int $product_id
	 * @return string the manufacturer name
	 */
	function get_mf_name($product_id) {
		$db = new ps_DB;

		$q = "SELECT mf_name,#__{vm}_manufacturer.manufacturer_id FROM #__{vm}_product_mf_xref,#__{vm}_manufacturer ";
		$q .= "WHERE product_id='$product_id' ";
		$q .= "AND #__{vm}_manufacturer.manufacturer_id=#__{vm}_product_mf_xref.manufacturer_id";

		$db->query($q);
		$db->next_record();
		if ($db->f("mf_name")) {
			return $db->f("mf_name");
		}
		else {
			return "";
		}
	}
	/**
	 * Prints the img tag for the given product image
	 *
	 * @param string $image The name of the imahe OR the full URL to the image
	 * @param string $args Additional attributes for the img tag
	 * @param int $resize 
	 * (1 = resize the image by using height and width attributes, 
	 * 0 = do not resize the image)
	 * @param string $path_appendix The path to be appended to IMAGEURL / IMAGEPATH
	 */
	function show_image($image, $args="", $resize=1, $path_appendix="product") {
		echo $this->image_tag($image, $args, $resize, $path_appendix);
	}

	/**
	 * Returns the img tag for the given product image
	 *
	 * @param string $image The name of the imahe OR the full URL to the image
	 * @param string $args Additional attributes for the img tag
	 * @param int $resize 
	 * (1 = resize the image by using height and width attributes, 
	 * 0 = do not resize the image)
	 * @param string $path_appendix The path to be appended to IMAGEURL / IMAGEPATH
	 * @return The HTML code of the img tag
	 */
	function image_tag($image, $args="", $resize=1, $path_appendix="product") {
		global $mosConfig_live_site;

		$border="";
		if( !strpos( $args, "border=" ))
		$border="border=\"0\"";

		if ($image != "") {
			// URL
			if( substr( $image, 0, 4) == "http" )
			$url = $image;

			// local image file
			else {
				if(PSHOP_IMG_RESIZE_ENABLE == '1' && $resize==1)
				$url = $mosConfig_live_site."/components/com_virtuemart/show_image_in_imgtag.php?filename=".urlencode($image)."&newxsize=".PSHOP_IMG_WIDTH."&newysize=".PSHOP_IMG_HEIGHT."&fileout=";
				else
				$url = IMAGEURL.$path_appendix."/".$image;
			}
		}
		else {
			$url = IMAGEURL.NO_IMAGE;
		}
		$html_height_width = "";
		$height_greater = false;
		if( file_exists(IMAGEPATH.$path_appendix."/".$image)) {
			$arr = @getimagesize( IMAGEPATH.$path_appendix."/".$image );
			$html_height_width = $arr[3];
			$height_greater = $arr[0] < $arr[1];
			if( (PSHOP_IMG_WIDTH < $arr[0] || PSHOP_IMG_HEIGHT < $arr[1]) && $resize != 0 ) {
				if( $height_greater )
				$html_height_width = " height=\"".PSHOP_IMG_HEIGHT."\"";
				else
				$html_height_width = " width=\"".PSHOP_IMG_WIDTH."\"";
			}
		}
		if((PSHOP_IMG_RESIZE_ENABLE != '1') && ($resize==1) ) {
			if( $height_greater )
			$html_height_width = " height=\"".PSHOP_IMG_HEIGHT."\"";
			else
			$html_height_width = " width=\"".PSHOP_IMG_WIDTH."\"";
		}

		return "<img src=\"$url\" $html_height_width $args $border />";

	}

	/**
	 * Get the tax rate...
	 * @author soeren
	 * @return int The tax rate found
	 */
	function get_taxrate() {

		$ps_vendor_id = $_SESSION["ps_vendor_id"];
		$auth = $_SESSION['auth'];

		if( !defined('_PSHOP_ADMIN' )) {

			$db = new ps_DB;

			if ($auth["show_price_including_tax"] == 1) {

				if (TAX_MODE == '0') {
					if( $auth["user_id"] > 0 ) {
						
						$q = "SELECT state, country FROM #__{vm}_user_info WHERE user_id='". $auth["user_id"] . "'";
						$db->query($q);

						$db->next_record();
						$state = $db->f("state");
						$country = $db->f("country");

						$q = "SELECT tax_rate FROM #__{vm}_tax_rate WHERE tax_country='$country' ";
						if( !empty($state)) {
							$q .= "AND tax_state='$state'";
						}
						$db->query($q);
						if ($db->next_record()) {
							$_SESSION['taxrate'][$ps_vendor_id] = $db->f("tax_rate");
						}
						else {
							$_SESSION['taxrate'][$ps_vendor_id] = 0;
						}
					}
					else {
						$_SESSION['taxrate'][$ps_vendor_id] = 0;
					}

				}
				elseif (TAX_MODE == '1') {
					if( empty( $_SESSION['taxrate'][$ps_vendor_id] )) {
						// let's get the store's tax rate
						$q = "SELECT tax_rate FROM #__{vm}_vendor, #__{vm}_tax_rate ";
						$q .= "WHERE tax_country=vendor_country AND #__{vm}_vendor.vendor_id='1' ";
						$q .= "ORDER BY `tax_rate` DESC";
						$db->query($q);
						if ($db->next_record()) {
							$_SESSION['taxrate'][$ps_vendor_id] = $db->f("tax_rate");
						}
						else {
							$_SESSION['taxrate'][$ps_vendor_id] = 0;
						}
					}
					return $_SESSION['taxrate'][$ps_vendor_id];
				}

			}
			else {
				$_SESSION['taxrate'][$ps_vendor_id] = 0;
			}

			return $_SESSION['taxrate'][$ps_vendor_id];
		}
		else {
			return 0;
		}
	}

	/**
	 * Function to get the tax rate of product $product_id
	 * If not found, it uses get_taxrate()
	 *
	 * @param int $product_id
	 * @param int $weight_subtotal (tax virtual/zero-weight items?)
	 * @return int The tax rate for the product
	 */
	function get_product_taxrate( $product_id, $weight_subtotal=0 ) {

		if (($weight_subtotal != 0 or TAX_VIRTUAL=='1') && TAX_MODE =='0') {
			$_SESSION['product_sess'][$product_id]['tax_rate'] = $this->get_taxrate();
			return $_SESSION['product_sess'][$product_id]['tax_rate'];
		}
		elseif( ($weight_subtotal == 0 or TAX_VIRTUAL != '1' ) && TAX_MODE =='0') {
			$_SESSION['product_sess'][$product_id]['tax_rate'] = 0;
			return $_SESSION['product_sess'][$product_id]['tax_rate'];
		}

		elseif( TAX_MODE == '1' ) {
			
			if( empty( $_SESSION['product_sess'][$product_id]['tax_rate'] ) ) {
				$db = new ps_DB;
				// Product's tax rate id has priority!
				$q = "SELECT product_weight, tax_rate FROM #__{vm}_product, #__{vm}_tax_rate ";
				$q .= "WHERE product_tax_id=tax_rate_id AND product_id='$product_id'";
				$db->query($q);
				if ($db->next_record()) {
					$rate = $db->f("tax_rate");
					$product_weight = $db->f('product_weight');
					if( $weight_subtotal == 0 && $product_weight > 0 ) {
						$weight_subtotal = $product_weight;
					}
				}
				else {
					// if we didn't find a product tax rate id, let's get the store's tax rate
					$rate = $this->get_taxrate();
				}
				if ($weight_subtotal != 0 or TAX_VIRTUAL=='1') {
					$_SESSION['product_sess'][$product_id]['tax_rate'] = $rate;
					return $rate;
				}
				else {
					$_SESSION['product_sess'][$product_id]['tax_rate'] = 0;
					return 0;
				}
			}
			else {
				return $_SESSION['product_sess'][$product_id]['tax_rate'];
			}
		}
		return 0;
	}
	
	/**
	 * Function to get the "pure" undiscounted and untaxed price 
	 * of product $product_id. Used by the administration section.
	 *
	 * @param int $product_id
	 * @return array The product price information
	 */
	function get_retail_price($product_id) {

		$db = new ps_DB;
		// Get the vendor id for this product.
		$q = "SELECT vendor_id FROM #__{vm}_product WHERE product_id='$product_id'";
		$db->setQuery($q); $db->query();
		$db->next_record();
		$vendor_id = $db->f("vendor_id");

		// Get the default shopper group id for this product and user
		$q = "SELECT shopper_group_id FROM #__{vm}_shopper_group WHERE `vendor_id`='$vendor_id' AND `default`='1'";
		$db->setQuery($q); $db->query();
		$db->next_record();
		$default_shopper_group_id = $db->f("shopper_group_id");

		$q = "SELECT product_price,product_currency FROM #__{vm}_product_price WHERE product_id='$product_id' AND ";
		$q .= "shopper_group_id='$default_shopper_group_id'";
		$db->setQuery($q); $db->query();
		if ($db->next_record()) {
			$price_info["product_price"]= $db->f("product_price");
			$price_info["product_currency"]=$db->f("product_currency");
		}
		else {
			$price_info["product_price"]= "";
			$price_info["product_currency"] = $_SESSION['vendor_currency'];
		}
		return $price_info;
	}

	/**
	 * Get the price of product $product_id for the shopper group associated
	 * with $auth['user_id'] - including shopper group discounts
	 *
	 * @param int $product_id
	 * @param boolean $check_multiple_prices Check if the product has more than one price for that shopper group?
	 * @return array The product price information
	 */
	function get_price($product_id, $check_multiple_prices=false) {
		$auth = $_SESSION['auth'];
		$cart = $_SESSION['cart'];

		if( empty( $GLOBALS['product_info'][$product_id]['price'] )
		|| !empty($GLOBALS['product_info'][$product_id]['price']["product_has_multiple_prices"])
		|| $check_multiple_prices) {
			$db = new ps_DB;

			if( empty( $_SESSION['product_sess'][$product_id]['vendor_id'] )) {

				// Get the vendor id for this product.
				$q = "SELECT vendor_id FROM #__{vm}_product WHERE product_id='$product_id'";
				$db->setQuery($q); $db->query();
				$db->next_record();
				$_SESSION['product_sess'][$product_id]['vendor_id'] = $vendor_id = $db->f("vendor_id");
			}
			else {
				$vendor_id = $_SESSION['product_sess'][$product_id]['vendor_id'];
			}

			$shopper_group_id = $auth["shopper_group_id"];
			$shopper_group_discount = $auth["shopper_group_discount"];

			if( empty($GLOBALS['vendor_info'][$vendor_id]['default_shopper_group_id']) ) {
				// Get the default shopper group id for this vendor
				$q = "SELECT shopper_group_id,shopper_group_discount FROM #__{vm}_shopper_group WHERE ";
				$q .= "vendor_id='$vendor_id' AND `default`='1'";
				$db->setQuery($q); $db->query();
				$db->next_record();
				$GLOBALS['vendor_info'][$vendor_id]['default_shopper_group_id'] = $default_shopper_group_id = $db->f("shopper_group_id");
				$GLOBALS['vendor_info'][$vendor_id]['default_shopper_group_discount']= $default_shopper_group_discount = $db->f("shopper_group_discount");
			}
			else {
				$default_shopper_group_id = $GLOBALS['vendor_info'][$vendor_id]['default_shopper_group_id'];
				$default_shopper_group_discount = $GLOBALS['vendor_info'][$vendor_id]['default_shopper_group_discount'];
			}
			// Get the product_parent_id for this product/item
			$product_parent_id = $this->get_field($product_id, "product_parent_id");

			$price_info = Array();
			if( !$check_multiple_prices ) {
				/* Added for Volume based prices */
				// This is an important decision: we add up all product quantities with the same product_id,
				// regardless to attributes. This gives "real" volume based discount, because our simple attributes
				// depend on one and the same product_id
				$quantity = 0;
				for ($i=0;$i<$cart["idx"];$i++) {
					if ($cart[$i]["product_id"] == $product_id) {
						$quantity  += $cart[$i]["quantity"];
					}
				}

				$volume_quantity_sql = " AND (('$quantity' >= price_quantity_start AND '$quantity' <= price_quantity_end)
                                OR (price_quantity_end='0') OR ('$quantity' > price_quantity_end)) ORDER BY price_quantity_end DESC";
				/* End Addition */
			}
			else {
				$volume_quantity_sql = " ORDER BY price_quantity_start";
			}

			// Getting prices
			//
			// If the shopper group has a price then show it, otherwise
			// show the default price.
			if( !empty($shopper_group_id) ) {
				$q = "SELECT product_price, product_price_id, product_currency FROM #__{vm}_product_price WHERE product_id='$product_id' AND ";
				$q .= "shopper_group_id='$shopper_group_id' $volume_quantity_sql";
				$db->setQuery($q); $db->query();
				if ($db->next_record()) {
					$price_info["product_price"]= $db->f("product_price");
					if( $check_multiple_prices ) {
						$price_info["product_base_price"]= $db->f("product_price");
						$price_info["product_has_multiple_prices"] = $db->num_rows() > 1;
					}
					$price_info["product_price_id"]=$db->f("product_price_id");
					$price_info["product_currency"]=$db->f("product_currency");
					$price_info["item"]=true;
					$GLOBALS['product_info'][$product_id]['price'] = $price_info;
					return $GLOBALS['product_info'][$product_id]['price'];
				}
			}
			// Get default price
			$q = "SELECT product_price, product_price_id, product_currency FROM #__{vm}_product_price WHERE product_id='$product_id' AND ";
			$q .= "shopper_group_id='$default_shopper_group_id' $volume_quantity_sql";
			$db->setQuery($q); $db->query();
			if ($db->next_record()) {
				$price_info["product_price"]=$db->f("product_price") * ((100 - $shopper_group_discount)/100);
				if( $check_multiple_prices ) {
					$price_info["product_base_price"]= $price_info["product_price"];
					$price_info["product_has_multiple_prices"] = $db->num_rows() > 1;
				}
				$price_info["product_price_id"]=$db->f("product_price_id");
				$price_info["product_currency"] = $db->f("product_currency");
				$price_info["item"] = true;
				$GLOBALS['product_info'][$product_id]['price'] = $price_info;
				return $GLOBALS['product_info'][$product_id]['price'];
			}

			// Maybe its an item with no price, check again with product_parent_id
			if( !empty($shopper_group_id) ) {
				$q = "SELECT product_price, product_price_id, product_currency FROM #__{vm}_product_price WHERE product_id='$product_parent_id' AND ";
				$q .= "shopper_group_id='$shopper_group_id' $volume_quantity_sql";
				$db->setQuery($q); $db->query();
				if ($db->next_record()) {
					$price_info["product_price"]=$db->f("product_price");
					if( $check_multiple_prices ) {
						$price_info["product_base_price"]= $db->f("product_price");
						$price_info["product_has_multiple_prices"] = $db->num_rows() > 1;
					}
					$price_info["product_price_id"]=$db->f("product_price_id");
					$price_info["product_currency"] = $db->f("product_currency");
					$GLOBALS['product_info'][$product_id]['price'] = $price_info;
					return $GLOBALS['product_info'][$product_id]['price'];
				}
			}
			$q = "SELECT product_price, product_price_id, product_currency FROM #__{vm}_product_price WHERE product_id='$product_parent_id' AND ";
			$q .= "shopper_group_id='$default_shopper_group_id' $volume_quantity_sql";
			$db->setQuery($q); $db->query();
			if ($db->next_record()) {
				$price_info["product_price"]=$db->f("product_price") * ((100 - $shopper_group_discount)/100);
				if( $check_multiple_prices ) {
					$price_info["product_base_price"]= $price_info["product_price"];
					$price_info["product_has_multiple_prices"] = $db->num_rows() > 1;
				}
				$price_info["product_price_id"]=$db->f("product_price_id");
				$price_info["product_currency"] = $db->f("product_currency");
				$GLOBALS['product_info'][$product_id]['price'] = $price_info;
				return $GLOBALS['product_info'][$product_id]['price'];
			}
			// No price found
			$GLOBALS['product_info'][$product_id]['price'] = false;
			return $GLOBALS['product_info'][$product_id]['price'];
		}
		else {
			return $GLOBALS['product_info'][$product_id]['price'];
		}
	}

	/**
	 * Adjusts the price from get_price for the selected attributes
	 * @author Nathan Hyde <nhyde@bigDrift.com>
	 * @author curlyroger from his post at <http://www.phpshop.org/phpbb/viewtopic.php?t=3052>
	 *
	 * @param int $product_id
	 * @param string $description
	 * @return array The adjusted price information
	 */
	function get_adjusted_attribute_price ($product_id, $description='') {

		global $mosConfig_secret;
		$auth = $_SESSION['auth'];
		$price = $this->get_price($product_id);

		$base_price = $price["product_price"];
		$setprice = 0;
		$set_price = false;
		$adjustment = 0;

		// We must care for custom attribute fields! Their value can be freely given
		// by the customer, so we mustn't include them into the price calculation
		// Thanks to AryGroup@ua.fm for the good advice
		if( empty( $_REQUEST["custom_attribute_fields"] )) {
			if( !empty( $_SESSION["custom_attribute_fields"] )) {
				$custom_attribute_fields = mosGetParam( $_SESSION, "custom_attribute_fields", Array() );
				$custom_attribute_fields_check = mosGetParam( $_SESSION, "custom_attribute_fields_check", Array() );
			}
			else
			$custom_attribute_fields = $custom_attribute_fields_check = Array();
		}
		else {
			$custom_attribute_fields = $_SESSION["custom_attribute_fields"] = mosGetParam( $_REQUEST, "custom_attribute_fields", Array() );
			$custom_attribute_fields_check = $_SESSION["custom_attribute_fields_check"]= mosGetParam( $_REQUEST, "custom_attribute_fields_check", Array() );
		}

		// if we've been given a description to deal with, get the adjusted price
		if ($description != '') { // description is safe to use at this point cause it's set to ''

		$attribute_keys = explode( ";", $description );

		foreach( $attribute_keys as $temp_desc ) {

			$temp_desc = trim( $temp_desc );
			// Get the key name (e.g. "Color" )
			$this_key = substr( $temp_desc, 0, strpos($temp_desc, ":") );

			if( in_array( $this_key, $custom_attribute_fields )) {
				if( @$custom_attribute_fields_check[$this_key] == md5( $mosConfig_secret.$this_key )) {
					// the passed value is valid, don't use it for calculating prices
					continue;
				}
			}

			$i = 0;

			$start = strpos($temp_desc, "[");
			$finish = strpos($temp_desc,"]", $start);

			$o = substr_count ($temp_desc, "[");
			$c = substr_count ($temp_desc, "]");
			//echo "open: $o<br>close: $c<br>\n";


			// check to see if we have a bracket
			if (True == is_int($finish) ) {
				$length = $finish-$start;

				// We found a pair of brackets (price modifier?)
				if ($length > 1) {
					$my_mod=substr($temp_desc, $start+1, $length-1);
					//echo "before: ".$my_mod."<br>\n";
					if ($o != $c) { // skip the tests if we don't have to process the string
						if ($o < $c ) {
							$char = "]";
							$offset = $start;
						}
						else {
							$char = "[";
							$offset = $finish;
						}
						$s = substr_count($my_mod, $char);
						for ($r=1;$r<$s;$r++) {
							$pos = strrpos($my_mod, $char);
							$my_mod = substr($my_mod, $pos+1);
						}
					}
					$oper=substr($my_mod,0,1);

					$my_mod=substr($my_mod,1);


					// if we have a number, allow the adjustment
					if (true == is_numeric($my_mod) ) {
						// Now add or sub the modifier on
						if ($oper=="+") {
							$adjustment += $my_mod;
						}
						else if ($oper=="-") {
							$adjustment -= $my_mod;
						}
						else if ($oper=='=') {
							// NOTE: the +=, so if we have 2 sets they get added
							// this could be moded to say, if we have a set_price, then
							// calc the diff from the base price and start from there if we encounter
							// another set price... just a thought.

							$setprice += $my_mod;
							$set_price = true;
						}
					}
					$temp_desc = substr($temp_desc, $finish+1);
					$start = strpos($temp_desc, "[");
					$finish = strpos($temp_desc,"]");
				}
			}
			$i++; // not necessary, but perhaps interesting? ;)
		}
		}

		// no set price was set from the attribs
		if ($set_price == false) {
			$price["product_price"] = $base_price + $adjustment;
		}
		else { 
			// otherwise, set the price
			// add the base price to the price set in the attributes
			// then subtract the adjustment amount
			// we could also just add the set_price to the adjustment... not sure on that one.
			// $setprice += $adjustment;
			$setprice *= 1 - ($auth["shopper_group_discount"]/100);
			$price["product_price"] = $setprice;
		}

		// don't let negative prices get by, set to 0
		if ($price["product_price"] < 0) {
			$price["product_price"] = 0;
		}
		// Get the DISCOUNT AMOUNT
		$discount_info = $this->get_discount( $product_id );

		$my_taxrate = $this->get_product_taxrate($product_id);

		if( !empty($discount_info["amount"])) {
			if( $auth["show_price_including_tax"] == 1 ) {
				switch( $discount_info["is_percent"] ) {
					case 0: $price["product_price"] = (($price["product_price"]*($my_taxrate+1))-$discount_info["amount"])/($my_taxrate+1); break;
					//case 1: $price["product_price"] = ($price["product_price"]*($my_taxrate+1) - $discount_info["amount"]/100*$price["product_price"])/($my_taxrate+1); break;
					case 1: $price["product_price"] = ($price["product_price"] - $discount_info["amount"]/100*$price["product_price"]); break;
				}
			}
			else {
				switch( $discount_info["is_percent"] ) {
					case 0: $price["product_price"] = (($price["product_price"])-$discount_info["amount"]); break;
					case 1: $price["product_price"] = ($price["product_price"] - ($discount_info["amount"]/100)*$price["product_price"]); break;
				}
			}
		}

		return $price;
	}
	
	/**
	 * This function can parse an "advanced / custom attribute"
	 * description like
	 * Size:big[+2.99]; Color:red[+0.99]
	 * and return the same string with values, tax added
	 * Size: big (+3.47), Color: red (+1.15)
	 * 
	 * @param string $description
	 * @param int $product_id
	 * @return string The reformatted description
	 */
	function getDescriptionWithTax( $description, $product_id=0 ) {
		global $CURRENCY_DISPLAY, $mosConfig_secret;
		$auth = $_SESSION['auth'];
		$description = stripslashes($description);
		// if we've been given a description to deal with, get the adjusted price
		if ($description != '' && stristr( $description, "[" ) && $product_id != 0 ) {
			if( $auth["show_price_including_tax"] == 1) {
				$my_taxrate = $this->get_product_taxrate($product_id);
			}
			else {
				$my_taxrate = 0.00;
			}

			// We must care for custom attribute fields! Their value can be freely given
			// by the customer, so we mustn't include them into the price calculation
			// Thanks to AryGroup@ua.fm for the good advice
			if( empty( $_REQUEST["custom_attribute_fields"] )) {
				if( !empty( $_SESSION["custom_attribute_fields"] )) {
					$custom_attribute_fields = mosGetParam( $_SESSION, "custom_attribute_fields", Array() );
					$custom_attribute_fields_check = mosGetParam( $_SESSION, "custom_attribute_fields_check", Array() );
				}
				else {
					$custom_attribute_fields = $custom_attribute_fields_check = Array();
				}
			}
			else {
				$custom_attribute_fields = $_SESSION["custom_attribute_fields"] = mosGetParam( $_REQUEST, "custom_attribute_fields", Array() );
				$custom_attribute_fields_check = $_SESSION["custom_attribute_fields_check"]= mosGetParam( $_REQUEST, "custom_attribute_fields_check", Array() );
			}

			$attribute_keys = explode( ";", $description );
			
			foreach( $attribute_keys as $temp_desc ) {

				$temp_desc = trim( $temp_desc );
				// Get the key name (e.g. "Color" )
				$this_key = substr( $temp_desc, 0, strpos($temp_desc, ":") );

				if( in_array( $this_key, $custom_attribute_fields )) {
					if( @$custom_attribute_fields_check[$this_key] == md5( $mosConfig_secret.$this_key )) {
						// the passed value is valid, don't use it for calculating prices
						continue;
					}
				}
				$i = 0;

				$start = strpos($temp_desc, "[");
				$finish = strpos($temp_desc,"]", $start);

				$o = substr_count ($temp_desc, "[");
				$c = substr_count ($temp_desc, "]");

				// check to see if we have a bracket
				if (True == is_int($finish) ) {
					$length = $finish-$start;

					// We found a pair of brackets (price modifier?)
					if ($length > 1) {
						$my_mod=substr($temp_desc, $start+1, $length-1);
						
						//echo "before: ".$my_mod."<br>\n";
						if ($o != $c) { // skip the tests if we don't have to process the string
							if ($o < $c ) {
								$char = "]";
								$offset = $start;
							}
							else {
								$char = "[";
								$offset = $finish;
							}
							$s = substr_count($my_mod, $char);
							for ($r=1;$r<$s;$r++) {
								$pos = strrpos($my_mod, $char);
								$my_mod = substr($my_mod, $pos+1);
							}
						}
						
						$value_notax = substr($my_mod,1);
						
						if( abs($value_notax) >0 ) {
							$value_taxed = $value_notax * ($my_taxrate+1);
							
							$description = str_replace( $value_notax, $CURRENCY_DISPLAY->getFullValue( $value_taxed ), $description);
						}
						elseif( $my_mod === "+0" || $my_mod === '-0') {
							$description = str_replace( "[".$my_mod."]", '', $description);
						}
						$temp_desc = substr($temp_desc, $finish+1);
						$start = strpos($temp_desc, "[");
						$finish = strpos($temp_desc,"]");
					}
				}
				$i++; // not necessary, but perhaps interesting? ;)
			}
		}
		$description = str_replace( $CURRENCY_DISPLAY->symbol, '@saved@', $description );
		$description = str_replace( "[", " (", $description );
		$description = str_replace( "]", ")", $description );
		$description = str_replace( ":", ": ", $description );
		$description = str_replace( ";", "<br/>", $description );
		$description = str_replace( '@saved@', $CURRENCY_DISPLAY->symbol, $description );
		
		return $description;
	}
	/**************************************************************************
	** name: show_price
	** created by: soeren
	** description: display a Price, formatted and with Discounts
	** parameters: int product_id
	** returns:
	***************************************************************************/
	/**
	 * Function to calculate the price, apply discounts from the discount table
	 * and reformat the price
	 *
	 * @param int $product_id
	 * @param boolean $hide_tax Wether to show the text "(including X.X% tax)" or not
	 * @return string The formatted price
	 */
	function show_price( $product_id, $hide_tax = false ) {
		global $VM_LANG, $CURRENCY_DISPLAY,$vendor_mail;
		$auth = $_SESSION['auth'];

		$product_name = htmlentities( $this->get_field($product_id, 'product_name'), ENT_QUOTES );
		$no_price_html = "&nbsp;<a href=\"mailto:$vendor_mail?subject=".$VM_LANG->_PHPSHOP_PRODUCT_CALL.": $product_name\">".$VM_LANG->_PHPSHOP_PRODUCT_CALL."</a>";

		if( $auth['show_prices'] ) {
			// Get the DISCOUNT AMOUNT
			$discount_info = $this->get_discount( $product_id );

			// Get the Price according to the quantity in the Cart
			$price_info = $this->get_price( $product_id );
			// Get the Base Price of the Product
			$base_price_info = $this->get_price($product_id, true );
			if( $price_info === false ) {
				$price_info = $base_price_info;
			}
			$html = "";
			$undiscounted_price = 0;
			if (isset($price_info["product_price_id"])) {

				$base_price = $base_price_info["product_price"];
				$price = $price_info["product_price"];

				if ($auth["show_price_including_tax"] == 1) {
					$my_taxrate = $this->get_product_taxrate($product_id);
					$base_price += ($my_taxrate * $base_price);
				}
				else {
					$my_taxrate = 0;
				}
				// Calculate discount
				if( !empty($discount_info["amount"])) {
					$undiscounted_price = $base_price;
					switch( $discount_info["is_percent"] ) {
						case 0: $base_price -= $discount_info["amount"]; break;
						case 1: $base_price *= (100 - $discount_info["amount"])/100; break;
					}
				}
				$text_including_tax = "";
				if (!empty($my_taxrate)) {
					$tax = $my_taxrate * 100;
					// only show "including x % tax" when it shall
					// not be hidden
					if( !$hide_tax && $auth["show_price_including_tax"] == 1 && VM_PRICE_SHOW_INCLUDINGTAX) {
						$text_including_tax = $VM_LANG->_PHPSHOP_INCLUDING_TAX;
						eval ("\$text_including_tax = \"$text_including_tax\";");

					}
				}
				if(!empty($discount_info["amount"])) {
					$html .= "<span style=\"color:red;\">\n<strike>";
					$html .= $CURRENCY_DISPLAY->getFullValue($undiscounted_price);
					$html .= "</strike> $text_including_tax</span>\n<br/>";
				}

				$html .= "<span style=\"font-weight:bold\">\n";
				$html .= $CURRENCY_DISPLAY->getFullValue($base_price);
				$html .= "</span>\n ";

				$html .= $text_including_tax;

				if(!empty($discount_info["amount"])) {
					$html .= "<br />";
					$html .= $VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_SAVE.": ";
					if($discount_info["is_percent"]==1)
					$html .= $discount_info["amount"]."%";
					else
					$html .= $CURRENCY_DISPLAY->getFullValue($discount_info["amount"]);
				}

				// Check if we need to display a Table with all Quantity <=> Price Relationships
				if( $base_price_info["product_has_multiple_prices"] && !$hide_tax ) {
					$db = new ps_DB;
					// Quantity Discount Table
					$q = "SELECT product_price, price_quantity_start, price_quantity_end FROM #__{vm}_product_price
				  WHERE product_id='$product_id' AND shopper_group_id='".$auth["shopper_group_id"]."' ORDER BY price_quantity_start";
					$db->query( $q );

					//         $prices_table = "<table align=\"right\">
					$prices_table = "<table width=\"100%\">
					  <thead><tr class=\"sectiontableheader\">
					  <th>".$VM_LANG->_PHPSHOP_CART_QUANTITY."</th>
					  <th>".$VM_LANG->_PHPSHOP_CART_PRICE."</th>
					  </tr></thead>
					  <tbody>";
					$i = 1;
					while( $db->next_record() ) {

						$prices_table .= "<tr class=\"sectiontableentry$i\"><td>".$db->f("price_quantity_start")." - ".$db->f("price_quantity_end")."</td>";
						$prices_table .= "<td>";
						if (!empty($my_taxrate))
						$prices_table .= $CURRENCY_DISPLAY->getFullValue( ($my_taxrate+1)*$db->f("product_price") );
						else
						$prices_table .= $CURRENCY_DISPLAY->getFullValue( $db->f("product_price") );
						$prices_table .= "</td></tr>";
						$i == 1 ? $i++ : $i--;
					}
					$prices_table .= "</tbody></table>";
					if( @$_REQUEST['page'] == "shop.browse" ) {
						$html .= mm_ToolTip( $prices_table );
					}
					else
					$html .= $prices_table;
				}
			}
			// No price, so display "Call for pricing"
			else {
				$html = $no_price_html;
			}
			return $html;
		}
		else
		return $no_price_html;
	}

	/**************************************************************************
	** name: get_discount
	** created by: soeren
	** description: display a Price, formatted and with Discounts
	** parameters: int product_id
	** returns:
	***************************************************************************/
	/**
	 * Get the information about the discount for a product
	 *
	 * @param int $product_id
	 * @return array The discount information
	 */
	function get_discount( $product_id ) {
		global $mosConfig_lifetime;

		// We use the Session now to store the discount info for
		// each product. But this info can change regularly,
		// so we check if the session time has expired
		if( empty( $_SESSION['product_sess'][$product_id]['discount_info'] )
		|| (time() - $_SESSION['product_sess'][$product_id]['discount_info']['create_time'] )>$mosConfig_lifetime) {
			$db = new ps_DB;
			$starttime = time();
			$year = date('Y');
			$month = date('n');
			$day = date('j');
			// get the beginning time of today
			$endofday = mktime(0, 0, 0, $month, $day, $year) - 1440;

			// Get the DISCOUNT AMOUNT
			$q = "SELECT amount,is_percent FROM #__{vm}_product,#__{vm}_product_discount ";
			$q .= "WHERE product_id='$product_id' AND (start_date<='$starttime' OR start_date=0) AND (end_date>='$endofday' OR end_date=0) ";
			$q .= "AND product_discount_id=discount_id";
			$db->query( $q );
			if( $db->next_record() ) {
				$discount_info["amount"] = $db->f("amount");
				$discount_info["is_percent"] = $db->f("is_percent");
			}
			else {
				$discount_info["amount"] = 0;
				$discount_info["is_percent"] = 0;
			}
			$discount_info['create_time'] = time();
			$_SESSION['product_sess'][$product_id]['discount_info'] = $discount_info;
			return $discount_info;
		}
		else
		return $_SESSION['product_sess'][$product_id]['discount_info'];
	}

	/**
	 * display a snapshot of a product based on the product sku.
	 * This was written to provide a quick way to display a product inside of modules
	 *
	 * @param string $product_sku The SKU identifying the product
	 * @param boolean $show_price Show the product price?
	 * @param boolean $show_addtocart Show the add-to-cart link?
	 */
	function show_snapshot($product_sku, $show_price=true, $show_addtocart=true ) {

		echo $this->product_snapshot($product_sku, $show_price, $show_addtocart);

	}
	/**
	 * Returns HTML code for a snapshot of a product based on the product sku.
	 * This was written to provide a quick way to display a product inside of modules
	 *
	 * @param string $product_sku The SKU identifying the product
	 * @param boolean $show_price Show the product price?
	 * @param boolean $show_addtocart Show the add-to-cart link?
	 */
	function product_snapshot( $product_sku, $show_price=true, $show_addtocart=true ) {

		global $sess, $VM_LANG, $mm_action_url;

		$db = new ps_DB;

		require_once(CLASSPATH.'ps_product_category.php');
		$ps_product_category = new ps_product_category;

		$q = "SELECT product_id, product_name, product_parent_id, product_thumb_image FROM #__{vm}_product WHERE product_sku='$product_sku'";
		$db->query( $q );
		$html = "";
		if ($db->next_record()) {

			$cid = $ps_product_category->get_cid( $db->f("product_id" ) );

			$html .= "<span style=\"font-weight:bold;\">".$db->f("product_name")."</span>\n";
			$html .= "<br />\n";
			
			if ($db->f("product_parent_id")) {
				$url = "?page=shop.product_details&category_id=$cid&flypage=".$this->get_flypage($db->f("product_parent_id"));
				$url .= "&product_id=" . $db->f("product_parent_id");
			} else {
				$url = "?page=shop.product_details&category_id=$cid&flypage=".$this->get_flypage($db->f("product_id"));
				$url .= "&product_id=" . $db->f("product_id");
			}
			$html .= "<a title=\"".$db->f("product_name")."\" href=\"". $sess->url($mm_action_url. "index.php" . $url)."\">";
			$html .= $this->image_tag($db->f("product_thumb_image"), "alt=\"".$db->f("product_name")."\"");
			$html .= "</a><br />\n";

			if (_SHOW_PRICES == '1' && $show_price) {
				// Show price, but without "including X% tax"
				$html .= $this->show_price( $db->f("product_id"), true );
			}
			if (USE_AS_CATALOGUE != 1 && $show_addtocart && !strstr( $html, $VM_LANG->_PHPSHOP_PRODUCT_CALL)) {
				$html .= "<br />\n";
				$url = "?page=shop.cart&func=cartAdd&product_id=" .  $db->f("product_id");
				$html .= "<a title=\"".$VM_LANG->_PHPSHOP_CART_ADD_TO.": ".$db->f("product_name")."\" href=\"". $sess->url($mm_action_url . "index.php" . $url)."\">".$VM_LANG->_PHPSHOP_CART_ADD_TO."</a><br />\n";
			}
		}

		return $html;
	}

	/**
	 * Prints a drop-down list of vendor names and their ids.
	 *
	 * @param int $vendor_id
	 */
	function list_vendor($vendor_id='1') {

		$db = new ps_DB;

		$q = "SELECT vendor_id,vendor_name FROM #__{vm}_vendor ORDER BY vendor_name";
		$db->query($q);
		$db->next_record();

		// If only one vendor do not show list
		if ($db->num_rows() == 1) {
			echo "<input type=\"hidden\" name=\"vendor_id\" value=\"";
			echo $db->f("vendor_id");
			echo "\" />";
			echo $db->f("vendor_name");
		}
		elseif($db->num_rows() > 1) {
			$db->reset();
			$code = "<select name=\"vendor_id\">\n";
			while ($db->next_record()) {
				$code .= "  <option value=\"" . $db->f("vendor_id") . "\"";
				if ($db->f("vendor_id") == $vendor_id) {
					$code .= " selected=\"selected\"";
				}
				$code .= ">" . $db->f("vendor_name") . "</option>\n";
			}
			$code .= "</select><br />\n";
			echo $code;
		}
	}

	/**
	 * Print the name of vendor $vend_id
	 *
	 * @param int $vend_id
	 */
	function show_vendorname($vend_id) {

		echo $this->getVendorName( $vend_id );

	}
	/**
	 * Return the name of vendor $id
	 *
	 * @param unknown_type $id
	 * @return unknown
	 */
	function getVendorName( $id ) {

		$db = new ps_DB;

		$q = "SELECT vendor_name FROM #__{vm}_vendor WHERE vendor_id='$id'";
		$db->query($q);
		$db->next_record();
		return $db->f("vendor_name");

	}

	/**
	 * Prints a drop-down list of manufacturer names and their ids.
	 *
	 * @param int $manufacturer_id
	 */
	function list_manufacturer($manufacturer_id='0') {

		$db = new ps_DB;

		$q = "SELECT manufacturer_id,mf_name FROM #__{vm}_manufacturer ORDER BY mf_name";
		$db->query($q);
		$db->next_record();

		// If only one vendor do not show list
		if ($db->num_rows() == 1) {

			echo "<input type=\"hidden\" name=\"manufacturer_id\" value=\"";
			echo $db->f("manufacturer_id");
			echo "\" />";
			echo $db->f("mf_name");
		}
		elseif( $db->num_rows() > 1) {
			$db->reset();
			$code = "<select name=\"manufacturer_id\">\n";
			while ($db->next_record()) {
				$code .= "  <option value=\"" . $db->f("manufacturer_id") . "\"";
				if ($db->f("manufacturer_id") == $manufacturer_id) {
					$code .= " selected=\"selected\"";
				}
				$code .= ">" . $db->f("mf_name") . "</option>\n";
			}
			$code .= "</select><br />\n";
			echo $code;
		}
		else  {
			echo "<input type=\"hidden\" name=\"manufacturer_id\" value=\"1\" />Please create at least one Manufacturer!!";
		}
	}

	/**
	 * Use this function if you need the weight of a product
	 *
	 * @param int $prod_id
	 * @return int The weight of the product
	 */
	function get_weight($prod_id) {

		$db = new ps_DB;

		$q = "SELECT product_weight FROM #__{vm}_product WHERE product_id='". $prod_id ."'";
		$db->query($q);
		$db->next_record();
		return $db->f("product_weight");

	}
	/**
	 * Print the availability HTML code for product $prod_id
	 *
	 * @param int $prod_id
	 */
	function show_availability($prod_id) {
		echo $this->get_availability($prod_id);
	}

	/**
	 * Returns the availability information as HTML code
	 * @author soeren
	 * @param unknown_type $prod_id
	 * @return unknown
	 */
	function get_availability($prod_id) {
		global $VM_LANG;

		$html = "";

		$is_parent = $this->parent_has_children( $prod_id );
		if( !$is_parent ) {
			$db = new ps_DB;

			$q = "SELECT product_available_date,product_availability,product_in_stock  FROM #__{vm}_product WHERE ";
			$q .= "product_id='". $prod_id ."'";

			$db->query($q);
			$db->next_record();
			$pad = $db->f("product_available_date");
			$pav = $db->f("product_availability");

			$heading = "<div style=\"text-decoration:underline;font-weight:bold;\">".$VM_LANG->_PHPSHOP_AVAILABILITY."</div><br />";

			if (CHECK_STOCK == '1') {
				if ($db->f("product_in_stock") < 1) {
					$html .= $VM_LANG->_PHPSHOP_CURRENTLY_NOT_AVAILABLE."<br />";
					if($pad > time()) {
						$html .= $VM_LANG->_PHPSHOP_PRODUCT_AVAILABLE_AGAIN;
						$html .= date("d.m.Y", $pad). "<br /><br />";
						define('_PHSHOP_PRODUCT_NOT_AVAILABLE', '1');
					}
				}
				elseif($pad > time()) {
					$html .= $VM_LANG->_PHPSHOP_CURRENTLY_NOT_AVAILABLE."<br />";
					$html .= $VM_LANG->_PHPSHOP_PRODUCT_AVAILABLE_AGAIN;
					$html .= date("d.m.Y", $pad). "<br /><br />";
					define('_PHSHOP_PRODUCT_NOT_AVAILABLE', '1');
				}
				else {
					$html .= "<span style=\"font-weight:bold;\">".$VM_LANG->_PHPSHOP_PRODUCT_FORM_IN_STOCK.": </span>".$db->f("product_in_stock")."<br /><br />";
				}
			}
			if (!empty($pav)) {
				if (stristr($pav, "gif") || stristr($pav, "jpg") || stristr($pav, "png")) {
					// we think it's a pic then...
					$html .= "<span style=\"font-weight:bold;\">".$VM_LANG->_PHPSHOP_DELIVERY_TIME.": </span><br /><br />";
					$html .= "<img align=\"middle\" src=\"".IMAGEURL."availability/".$pav."\" border=\"0\" alt=\"$pav\" />";
				}
				else {
					$html .= "<span style=\"font-weight:bold;\">".$VM_LANG->_PHPSHOP_DELIVERY_TIME.": </span>";
					$html .= $pav;
				}
			}
			if (!empty($html)) {
				$html = $heading.$html;
			}
		}
		return $html;

	}

	/**
	 * Modifies the product_publish field and toggles it from Y to N or N to Y
	 * for product $d['product_id']
	 * @deprecated 
	 * @param int $d $d['task'] must be "publish" or "unpublish"
	 * @return unknown
	 */
	function product_publish( &$d ) {
		global $db, $vmLogger;

		$this->handlePublishState( $d );
		return;
	}

}  // ENd of CLASS ps_product

?>
