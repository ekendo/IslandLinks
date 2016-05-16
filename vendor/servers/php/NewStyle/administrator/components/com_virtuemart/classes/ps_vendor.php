<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_vendor.php,v 1.6 2005/11/24 06:25:40 soeren_nb Exp $
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

class ps_vendor {
	var $classname = "ps_vendor";
	var $error;


	/**************************************************************************
	** name: validate_add()
	** created by:
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function validate_add(&$d) {
		global $vmLogger;
		
		$db = new ps_DB;

		if (!validate_image($d,"vendor_thumb_image","vendor")) {
			return false;
		}
		if (!validate_image($d,"vendor_full_image","vendor")) {
			return false;
		}
		if (!$d["vendor_name"]) {
			$vmLogger->err( 'You must enter a name for the vendor.' );
			return False;
		}
		if (!$d["contact_email"]) {
			$vmLogger->err( 'You must enter an email address for the vendor contact.');
			return False;
		}
		if (!mShop_validateEmail($d["contact_email"])) {
			$vmLogger->err( 'Please provide a valide email address for the vendor contact.' );
			return False;
		}
		else {
			return True;
		}
	}

	/**************************************************************************
	** name: validate_delete()
	** created by:
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function validate_delete( $vendor_id, &$d) {
		global $vmLogger;
		$db = new ps_DB;

		if (!$d["vendor_id"]) {
			$vmLogger->err( 'Please select a vendor to delete.' );
			return False;
		}

		$q = "SELECT vendor_id FROM #__{vm}_product where vendor_id='$vendor_id'";
		$db->query($q);
		if ($db->next_record()) {
			$vmLogger->err( 'This vendor still has products. Delete all products first.' );
			return False;
		}

		/* Get the image filenames from the database */
		$db = new ps_DB;
		$q  = "SELECT vendor_thumb_image,vendor_full_image ";
		$q .= "FROM #__{vm}_vendor ";
		$q .= "WHERE vendor_id='$vendor_id'";
		$db->query($q);
		$db->next_record();

		/* Validate vendor_thumb_image */
		$d["vendor_thumb_image_curr"] = $db->f("vendor_thumb_image");
		$d["vendor_thumb_image_name"] = "none";
		if (!validate_image($d,"vendor_thumb_image","vendor")) {
			return false;
		}

		/* Validate vendor_full_image */
		$d["vendor_full_image_curr"] = $db->f("vendor_full_image");
		$d["vendor_full_image_name"] = "none";
		if (!validate_image($d,"vendor_full_image","vendor")) {
			return false;
		}

		return True;
	}

	/**************************************************************************
	** name: validate_update()
	** created by:
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function validate_update(&$d) {
		global $vmLogger;

		if (!validate_image($d,"vendor_thumb_image","vendor")) {
			return false;
		}
		if (!validate_image($d,"vendor_full_image","vendor")) {
			return false;
		}

		// convert all "," in prices to decimal points.
		if (stristr($d["vendor_min_pov"],",")) {
			$d["vendor_min_pov"] = str_replace(',', '.', $d["vendor_min_pov"]);
		}

		if (!$d["vendor_name"]) {
			$vmLogger->err( 'You must enter a name for the vendor.' );
			return False;
		}
		if (!$d["contact_email"]) {
			$vmLogger->err( 'You must enter an email address for the vendor contact.');
			return False;
		}
		if (!mShop_validateEmail($d["contact_email"])) {
			$vmLogger->err( 'Please provide a valide email address for the vendor contact.' );
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
		$db = new ps_DB;
		$timestamp = time();

		if (!$this->validate_add($d)) {
			return False;
		}

		if (!process_images($d)) {
			return false;
		}
		$d['display_style'][1] = ps_vendor::checkCurrencySymbol( $d['display_style'][1] );
		
		$d['display_style'] = implode("|", $d['display_style'] );

		$q = "INSERT INTO #__{vm}_vendor (";
		$q .= "vendor_name,contact_last_name,contact_first_name,";
		$q .= "contact_middle_name,contact_title,contact_phone_1,";
		$q .= "contact_phone_2,contact_fax,contact_email,";
		$q .= "vendor_phone,vendor_address_1,vendor_address_2,";
		$q .= "vendor_city,vendor_state,vendor_country,vendor_zip,";
		$q .= "vendor_store_name,vendor_store_desc,vendor_category_id,";
		$q .= "vendor_image_path,vendor_thumb_image,vendor_full_image,";
		$q .= "vendor_currency,cdate,mdate,vendor_terms_of_service,";
		$q .= "vendor_url,vendor_currency_display_style, vendor_freeshipping) VALUES ('";
		$q .= $d["vendor_name"] . "','";
		$q .= $d["contact_last_name"] . "','";
		$q .= $d["contact_first_name"] . "','";
		$q .= $d["contact_middle_name"] . "','";
		$q .= $d["contact_title"] . "','";
		$q .= $d["contact_phone_1"] . "','";
		$q .= $d["contact_phone_2"] . "','";
		$q .= $d["contact_fax"] . "','";
		$q .= $d["contact_email"] . "','";
		$q .= $d["vendor_phone"] . "','";
		$q .= $d["vendor_address_1"] . "','";
		$q .= $d["vendor_address_2"] . "','";
		$q .= $d["vendor_city"] . "','";
		$q .= $d["vendor_state"] . "','";
		$q .= $d["vendor_country"] . "','";
		$q .= $d["vendor_zip"] . "','";
		$q .= $d["vendor_store_name"] . "','";
		$q .= $d["vendor_store_desc"] . "','";
		$q .= $d["vendor_category_id"] . "','";
		$q .= $d["vendor_image_path"] . "','";
		$q .= $d["vendor_thumb_image"] . "','";
		$q .= $d["vendor_full_image"] . "','";
		$q .= $d["vendor_currency"] . "','";
		$q .= "$timestamp','$timestamp','";
		$q .= $d['vendor_terms_of_service']."','";
		$q .= $d['vendor_url']."','";
		$q .= $d['display_style']."','".$d['vendor_freeshipping']."')";
		$db->query($q);
		$db->next_record();

		// Get the assigned vendor_id //
		$q  = "SELECT vendor_id FROM #__{vm}_vendor ";
		$q .= "WHERE vendor_name = '" . $d["vendor_name"] . "' ";
		$q .= "AND cdate = $timestamp";
		addslashes($q);
		$db->query($q);
		$db->next_record();
		$d["vendor_id"] = $db->f("vendor_id");

		/* Insert default- shopper group */
		$q = "INSERT INTO #__{vm}_shopper_group (";
		$q .= "`vendor_id`,";
		$q .= "`shopper_group_name`,";
		$q .= "`shopper_group_desc`,`default`) VALUES ('";
		$q .= $d["vendor_id"] . "',";
		$q .= "'-default-',";
		$q .= "'Default shopper group for ".$d["vendor_name"]."','1')";
		$db->query($q);

		return True;
	}

	/**************************************************************************
	* name: update()
	* created by:
	* description:
	* parameters:
	* returns:
	**************************************************************************/
	function update(&$d) {
		$db = new ps_DB;
		$timestamp = time();

		if (!$this->validate_update($d)) {
			return False;
		}

		if (!process_images($d)) {
			return false;
		}
		foreach ($d as $key => $value) {
			if (!is_array($value))
			$d[$key] = addslashes($value);
		}
		
		$d['display_style'][1] = ps_vendor::checkCurrencySymbol( $d['display_style'][1] );
		
		$d['display_style'] = implode("|", $d['display_style'] );

		$q = "UPDATE #__{vm}_vendor set vendor_name='" . $d["vendor_name"] . "',";
		$q .= "contact_last_name='" . $d["contact_last_name"] . "',";
		$q .= "contact_first_name='" . $d["contact_first_name"] . "',";
		$q .= "contact_middle_name='" . $d["contact_middle_name"] . "',";
		$q .= "contact_title='" . $d["contact_title"] . "',";
		$q .= "contact_phone_1='" . $d["contact_phone_1"] . "',";
		$q .= "contact_phone_2='" . $d["contact_phone_2"] . "',";
		$q .= "contact_fax='" . $d["contact_fax"] . "',";
		$q .= "contact_email='" . $d["contact_email"] . "',";
		$q .= "vendor_phone='" . $d["vendor_phone"] . "',";
		$q .= "vendor_address_1='" . $d["vendor_address_1"] . "',";
		$q .= "vendor_address_2='" . $d["vendor_address_2"] . "',";
		$q .= "vendor_city='" . $d["vendor_city"] . "',";
		$q .= "vendor_state='" . $d["vendor_state"] . "',";
		$q .= "vendor_country='" . $d["vendor_country"] . "',";
		$q .= "vendor_zip='" . $d["vendor_zip"] . "',";
		$q .= "vendor_store_name='" . $d["vendor_store_name"] . "',";
		$q .= "vendor_store_desc='" . $d["vendor_store_desc"] . "',";
		if (!empty($d["vendor_category_id"]))
		$q .= "vendor_category_id='" . $d["vendor_category_id"] . "',";
		if (!empty($d["vendor_image_path"]))
		$q .= "vendor_image_path='" . $d["vendor_image_path"] . "',";
		$q .= "vendor_thumb_image='" . $d["vendor_thumb_image"] . "',";
		$q .= "vendor_full_image='" . $d["vendor_full_image"] . "',";
		$q .= "vendor_currency='" . $d["vendor_currency"] . "',";
		$q .= "vendor_url='" . $d["vendor_url"] . "',";
		$q .= "mdate='$timestamp', ";
		$q .= "vendor_terms_of_service='" . $d["vendor_terms_of_service"] . "', ";
		$q .= "vendor_min_pov='" . $d["vendor_min_pov"] . "', ";
		$q .= "vendor_currency_display_style='" . $d["display_style"] . "', ";
		$q .= "vendor_freeshipping='" . $d['vendor_freeshipping'] . "' ";
		$q .= "WHERE vendor_id='" . $d["vendor_id"] . "'";

		$db->query($q);

		return True;
	}

	/**************************************************************************
	* name: delete()
	* created by:
	* description:
	* parameters:
	* returns:
	**************************************************************************/
	/**
	* Controller for Deleting Records.
	*/
	function delete(&$d) {

		$record_id = $d["_id"];

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

		if (!$this->validate_delete( $record_id, $d)) {
			return False;
		}

		/* Delete Image files */
		if (!process_images($d)) {
			return false;
		}

		$q = "DELETE FROM #__{vm}_vendor where vendor_id='$record_id'";
		$db->query($q);


		return True;
	}
	
	/**
	 * Checks a currency symbol wether it is a HTML entity.
	 * When not and $convertToEntity is true, it converts the symbol
	 *
	 * @param string $symbol
	 */
	function checkCurrencySymbol( $symbol, $convertToEntity=true ) {
		
		$symbol = str_replace('&amp;', '&', $symbol );
		
		if( substr( $symbol, 0, 1) == '&' && substr( $symbol, strlen($symbol)-1, 1 ) == ';') {
			return $symbol;
		}
		else {
			if( $convertToEntity ) {
				$symbol = htmlentities( $symbol, ENT_QUOTES, 'utf-8' );
				
				if( substr( $symbol, 0, 1) == '&' && substr( $symbol, strlen($symbol)-1, 1 ) == ';') {
					return $symbol;
				}		
				// Sometimes htmlentities() doesn't return a valid HTML Entity
				switch( ord( $symbol ) ) {
					case 128:
					case 63:
						$symbol = '&euro;';
						break;
				}
						
			}
		}
		
		return $symbol;
	}
	/**************************************************************************
	** name: get_user_vendor_id
	** created by: jep
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function get_user_vendor_id() {
		$auth = $_SESSION['auth'];

		$db = new ps_DB;

		$q  = "SELECT vendor_id FROM #__{vm}_auth_user_vendor ";
		$q .= "WHERE user_id='" . $auth["user_id"] . "'";
		$db->query($q);
		$db->next_record();
		return $db->f("vendor_id");
	}

	/**************************************************************************
	* name: find()
	* created by:
	* description:
	* parameters:
	* returns:
	**************************************************************************/
	function find($d, $start=0) {
		$db = new ps_DB;

		if ($d["vendor_thumb_image"] == "none") {
			$d["vendor_thumb_image"] = "";
		}
		if ($d["vendor_full_image"] == "none") {
			$d["vendor_full_image"] = "";
		}

		if ($d["vendor_category_id"] == "0") {
			$d["vendor_category_id"] = "";
		}
		$q = "SELECT * FROM #__{vm}_vendor where vendor_name LIKE '%" . $d["vendor_name"] . "%'";
		$q .= " AND contact_last_name LIKE '%" . $d["contact_last_name"] . "%'";
		$q .= " AND contact_first_name LIKE '%" . $d["contact_first_name"] . "%'";
		$q .= " AND contact_middle_name LIKE '%" . $d["contact_middle_name"] . "%'";
		$q .= " AND contact_title LIKE '%" . $d["contact_title"] . "%'";
		$q .= " AND contact_phone_1 LIKE '%" . $d["contact_phone_1"] . "%'";
		$q .= " AND contact_phone_2 LIKE '%" . $d["contact_phone_2"] . "%'";
		$q .= " AND contact_fax LIKE '%" . $d["contact_fax"] . "%'";
		$q .= " AND contact_email LIKE '%" . $d["contact_email"] . "%'";
		$q .= " AND vendor_phone LIKE '%" . $d["vendor_phone"] . "%'";
		$q .= " AND vendor_address_1 LIKE '%" . $d["vendor_address_1"] . "%'";
		$q .= " AND vendor_address_2 LIKE '%" . $d["vendor_address_2"] . "%'";
		$q .= " AND vendor_city LIKE '%" . $d["vendor_city"] . "%'";
		$q .= " AND vendor_state LIKE '%" . $d["vendor_state"] . "%'";
		$q .= " AND vendor_country LIKE '%" . $d["vendor_country"] . "%'";
		$q .= " AND vendor_zip LIKE '%" . $d["vendor_zip"] . "%'";
		$q .= " AND vendor_store_name LIKE '%" . $d["vendor_store_name"] . "%'";
		$q .= " AND vendor_store_desc LIKE '%" . $d["vendor_store_desc"] . "%'";
		$q .= " AND vendor_category_id LIKE '%" . $d["vendor_category_id"] . "%'";
		$q .= " AND vendor_thumb_image LIKE '%" . $d["vendor_thumb_image"] . "%'";
		$q .= " AND vendor_full_image LIKE '%" . $d["vendor_full_image"] . "%'";
		$q .= " AND vendor_currency LIKE '%" . $d["vendor_currency"] . "%'";

		$db->query($q);
		$db->next_record();
		if ($db->num_rows() == 1) {
			return "?vid=" . $db->f("vendor_id");
		}

		return True;
	}


	/**************************************************************************
	* name: listVendor()
	* created by:
	* description: Creates a list of SELECT recods using vendor name and vendor id.
	* parameters:
	* returns: array of values
	**************************************************************************/
	function get_name($vendor_id,$product_id="") {

		// Returns the vendor name corresponding to a vendor_id;
		$db = new ps_DB;

		if ($vendor_id) {
			$q = "SELECT vendor_name FROM #__{vm}_vendor WHERE vendor_id = '$vendor_id'";
		} elseif ($product_id) {
			$q  = "SELECT vendor_name FROM #__{vm}_product,#__{vm}_vendor ";
			$q .= "WHERE product_id = '$product_id' ";
			$q .= "AND #__{vm}_product.vendor_id = #__{vm}_vendor.vendor_id ";
		} else {
			/* ERROR: No arguments were specified. */
			return 0;
		}

		$db->query($q);
		$db->next_record();
		return $db->f("vendor_name");
	}


	/**************************************************************************
	* name: set_vendor()
	* created by:
	* description: Creates a list of SELECT recods using vendor name and vendor id.
	* parameters:
	* returns: array of values
	**************************************************************************/
	function set_vendor($d) {
		global  $sess;
		$ps_vendor_id = $_SESSION["ps_vendor_id"];

		$ps_vendor_id = $d["vendor_id"];
		return True;

	}

	/**************************************************************************
	** name: listVendor()
	** created by:
	** description: Creates a list of SELECT recods using vendor name and
	**              vendor id.
	** parameters:
	** returns: array of values
	***************************************************************************/
	function list_vendor($vendor_id=0) {
		global $sess;
		$ps_vendor_id = $_SESSION["ps_vendor_id"];

		// Creates a form drop down list and prints it
		$db = new ps_DB;

		$q = "SELECT count(*) as rowcnt FROM #__{vm}_vendor ORDER BY vendor_name";
		$db->query($q);
		$db->next_record();
		$rowcnt = $db->f("rowcnt");

		// If only one vendor do not show list
		if ($rowcnt == 1)
		return True;

		$q = "SELECT * FROM #__{vm}_vendor ORDER BY vendor_name";
		$db->query($q);

		$code = "<form action=\"" . SECUREURL . "\" method=\"post\">\n";
		$code .= "<input type=\"hidden\" name=\"page\" value=\"admin.index\" />\n";
		$code .= "<input type=\"hidden\" name=\"func\" value=\"setvendor\" />\n";
		$code .= "<input type=\"hidden\" name=\"option\" value=\"com_virtuemart\" />\n";
		$code .= "<select name=\"vendor_id\">\n";
		while ($db->next_record()) {
			$code .= "  <option value=\"" . $db->f("vendor_id") . "\"";
			if ($db->f("vendor_id") == $vendor_id) {
				$code .= " selected";
			}
			$code .= ">" . $db->f("vendor_name") . "</option>\n";
		}
		$code .= "</select><BR>\n";
		$code .= "<input type=\"submit\" name=\"go\" value=\"go\">\n";
		$code .= "</font>";
		print $code;
	}


	/**************************************************************************
	** name: get_field
	** created by: pablo
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function get_field($vendor_id, $field_name) {
		$db = new ps_DB;

		$q = "SELECT $field_name FROM #__{vm}_vendor WHERE vendor_id='$vendor_id'";
		$db->query($q);
		if ($db->next_record()) {
			return $db->f($field_name);
		}
		else {
			return False;
		}
	}


	/**************************************************************************
	** name: show_image()
	** created by: pablo
	** description:  Shows the image send in the $image field.
	**               $args are appended to the IMG tag.
	** parameters:
	** returns:
	***************************************************************************/
	function show_image($image, $args="") {

		$ps_vendor_id = $_SESSION["ps_vendor_id"];

		$url = IMAGEURL;
		$path = $this->get_field($ps_vendor_id,"vendor_image_path");
		if (!empty($path))
		$url = str_replace( "shop_image/", $path, $url );

		$url .= "vendor/";
		$url .= $image;
		echo "<img src=\"".$url ."\" ". $args ." />\n";

		return True;
	}
}
?>
