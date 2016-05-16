<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
* This Class provides some utility functions
* to easily create drop-down lists
*
* @version $Id: ps_html.php,v 1.14.2.4 2006/04/27 19:35:52 soeren_nb Exp $
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

class ps_html {
	var $classname = "ps_html";


	/**
	 * Prints an HTML dropdown box named $name using $arr to
	 * load the drop down.  If $value is in $arr, then $value
	 * will be the selected option in the dropdown.
	 * @author gday
	 * @author soeren
	 * 
	 * @param string $name The name of the select element
	 * @param string $value The pre-selected value
	 * @param array $arr The array containting $key and $val
	 * @param int $size The size of the select element
	 * @param string $multiple use "multiple=\"multiple\" to have a multiple choice select list
	 * @param string $extra More attributes when needed
	 * @return string HTML drop-down list
	 */
	function dropdown_display($name, $value, $arr, $size=1, $multiple="", $extra="") {

		if( !empty( $arr ) ) {
			echo "<select class=\"inputbox\" name=\"$name\" size=\"$size\" $multiple $extra>\n";

			while (list($key, $val) = each($arr)) {
				$selected = "";
				if( is_array( $value )) {
					if( in_array( $key, $value )) {
						$selected = "selected=\"selected\"";
					}
				}
				else {
					if(strcmp($value, $key) == 0) {
						$selected = "selected=\"selected\"";
					}
				}
				echo "<option value=\"$key\" $selected>$val";
				echo "</option>\n";
			}

			echo "</select>\n";
		}
		return True;
	}


	/**
	 * Lists titles for people
	 *
	 * @param string $t The selected title value
	 * @param string $extra More attributes when needed
	 */
	function list_user_title($t, $extra="") {
		global $VM_LANG;

		$title = array($VM_LANG->_PHPSHOP_REGISTRATION_FORM_MR,
		$VM_LANG->_PHPSHOP_REGISTRATION_FORM_MRS,
		$VM_LANG->_PHPSHOP_REGISTRATION_FORM_DR,
		$VM_LANG->_PHPSHOP_REGISTRATION_FORM_PROF);
		echo "<select class=\"inputbox\" name=\"title\" $extra>\n";
		echo "<option value=\"\">".$VM_LANG->_PHPSHOP_REGISTRATION_FORM_NONE."</option>\n";
		for ($i=0;$i<count($title);$i++) {
			echo "<option value=\"" . $title[$i]."\"";
			if ($title[$i] == $t)
			echo " selected=\"selected\" ";
			echo ">" . $title[$i] . "</option>\n";
		}
		echo "</select>\n";

	}

	/**************************************************************************
	** name: list_month($list_name)
	** created by: pfmartin
	** description:  Print an HTML dropdown box for the credit cards
	** parameters: $name - name of the HTML dropdown element
	**             $value - Drop down item to make selected
	**             $arr - array used to build the HTML drop down element
	** returns: prints HTML drop down element to standard output
	***************************************************************************/
	/**
	 * Creates a Drop-Down List for the 12 months in a year
	 *
	 * @param string $list_name The name for the select element
	 * @param string $selected_item The pre-selected value
	 * @return HTML code with the drop-down list
	 */
	function list_month($list_name, $selected_item="") {
		global $VM_LANG;
		$list = array("Month",
		"01" => _JAN,
		"02" => _FEB,
		"03" => _MAR,
		"04" => _APR,
		"05" => _MAY,
		"06" => _JUN,
		"07" => _JUL,
		"08" => _AUG,
		"09" => _SEP,
		"10" => _OCT,
		"11" => _NOV,
		"12" => _DEC);
		$this->dropdown_display($list_name, $selected_item, $list);
		return 1;
	}

	/**
	 * Creates an drop-down list with the next 7 years
	 *
	 * @param string $list_name The name of the select element
	 * @param string $selected_item The pre-selected value
	 * @return HTML code with the drop-down list
	 */
	function list_year($list_name,$selected_item="") {
		$current = date("Y");
		for ($i=$current; $i<$current+7; $i++)
		$list[$i] = $i;
		$this->dropdown_display($list_name, $selected_item, $list);
		return 1;
	}


	/**
	 * Creates a drop-down list for all countries
	 *
	 * @param string $list_name The name of the select element
	 * @param string $value The value of the pre-selected option
	 * @param string $extra More attributes for the select element when needed
	 * @return string The HTML code for the select list
	 */
	function list_country($list_name, $value="", $extra="") {
		global $VM_LANG;

		$db = new ps_DB;

		$q = "SELECT * from #__{vm}_country ORDER BY country_name ASC";
		$db->query($q);
		echo "<select class=\"inputbox\" name=\"$list_name\" $extra>\n";
		echo "<option value=\"\">".$VM_LANG->_PHPSHOP_SELECT."</option>\n";
		while ($db->next_record()) {
			echo "<option value=\"" . $db->f("country_3_code")."\"";
			if ($value == $db->f("country_3_code")) {
				echo " selected=\"selected\"";
			}
			echo ">" . $db->f("country_name") . "</option>\n";
		}
		echo "</select>\n";
		return True;
	}
	
	/**
	 * Creates a drop-down list for states [filtered by country_id]
	 *
	 * @param string $list_name The name of the select element
	 * @param string $selected_item The value of the pre-selected option
	 * @param int $country_id The ID of a country to filter states from
	 * @param string $extra More attributes for the select element when needed
	 * @return HTML code with the drop-down list
	 */
	function list_states($list_name,$selected_item="", $country_id="", $extra="") {
		global $VM_LANG;

		$db =& new ps_DB;
		$q = "SELECT country_name, state_name, state_3_code , state_2_code FROM #__{vm}_state, #__{vm}_country ";
		$q .= "WHERE #__{vm}_state.country_id = #__{vm}_country.country_id ";
		if( !empty( $country_id ))
		$q .= " AND country_id='$country_id' ";
		$q .= "ORDER BY country_name, state_name";
		$db->query( $q );
		$list = Array();
		$list["0"] = $VM_LANG->_PHPSHOP_SELECT;
		$list["NONE"] = "not listed";
		$country = "";

		while( $db->next_record() ) {
			if( $country != $db->f("country_name")) {
				$list[] = "------- ".$db->f("country_name")." -------";
				$country = $db->f("country_name");
			}
			$list[$db->f("state_2_code")] = $db->f("state_name");
		}

		$this->dropdown_display($list_name, $selected_item, $list,"","",$extra);
		return 1;
	}
	/**
	 * Creates a Javascript based dynamic state list, depending of the selected
	 * country of a country drop-down list (specified by $country_list_name)
	 *
	 * @param string $country_list_name The name of the country select list element
	 * @param string $state_list_name The name for this states drop-down list
	 * @param string $selected_country_code The 3-digit country code that is pre-selected
	 * @param string $selected_state_code The state code of a pre-selected state
	 * @return string HTML code containing the dynamic state list
	 */
	function dynamic_state_lists( $country_list_name, $state_list_name, $selected_country_code="", $selected_state_code="" ) {
		global $vendor_country_3_code, $VM_LANG;
		$db = new ps_DB;
		if( empty( $selected_country_code ))
		$selected_country_code = $vendor_country_3_code;

		if( empty( $selected_state_code ))
		$selected_state_code = "originalPos";
		else
		$selected_state_code = "'".$selected_state_code."'";

		$db->query( "SELECT #__{vm}_country.country_id,country_3_code
								  FROM #__{vm}_country" );

		if( $db->num_rows() > 0 ) {
			$dbs = new ps_DB;
			// Build the State lists for each Country
			$script = "<script language=\"javascript\" type=\"text/javascript\">//<![CDATA[\n";
			$script .= "<!--\n";
			$script .= "var originalOrder = '1';\n";
			$script .= "var originalPos = '$selected_country_code';\n";
			$script .= "var states = new Array();	// array in the format [key,value,text]\n";
			$i = 0;

			while( $db->next_record() ) {

				$dbs->query( "SELECT state_name, state_2_code FROM #__{vm}_state WHERE country_id='".$db->f("country_id")."'" );

				if( $dbs->num_rows() > 0 ) {
					while( $dbs->next_record() ) {
						// array in the format [key,value,text]
						$script .= "states[".$i++."] = new Array( '".$db->f("country_3_code")."','".$dbs->f("state_2_code")."','".htmlentities($dbs->f("state_name"), ENT_QUOTES)."' );\n";
					}
				}
				else {
					$script .= "states[".$i++."] = new Array( '".$db->f("country_3_code")."',' - ','".$VM_LANG->_PHPSHOP_NONE."' );\n";
				}


			}
			$script .= "
			function changeStateList() { 
			  var selected_country = null;
			  for (var i=0; i<document.adminForm.".$country_list_name.".length; i++)
				 if (document.adminForm.".$country_list_name."[i].selected)
					selected_country = document.adminForm.".$country_list_name."[i].value;
			  changeDynaList('".$state_list_name."',states,selected_country, originalPos, originalOrder);
			  
			}
			writeDynaList( 'class=\"inputbox\" name=\"".$state_list_name."\" size=\"1\" id=\"state\"', states, originalPos, originalPos, $selected_state_code );
			//-->
			//]]></script>";

			return $script;
		}
	}


	/**
	 * Creates a drop-down list for weight units-of-measure
	 *
	 * @param string $list_name The name for the select element
	 * @return string The HTML code for the select list
	 */
	function list_weight_uom($list_name) {
		global $VM_LANG;

		$list = array($VM_LANG->_PHPSHOP_SELECT,
		"LBS" => "Pounds",
		"KGS" => "Kilograms",
		"G" => "Grams");
		$this->dropdown_display($list_name, "", $list);
		return 1;
	}


	/**
	 * Creates a drop-down list for currencies. The currency code is used as option value
	 *
	 * @param string $list_name The name of the select element
	 * @param string $value The value of the pre-selected option
	 * @return HTML code with the drop-down list
	 */
	function list_currency($list_name, $value="") {
		global $VM_LANG;
		$db = new ps_DB;

		$q = "SELECT * from #__{vm}_currency ORDER BY currency_name ASC";
		$db->query($q);
		echo "<select class=\"inputbox\" name=\"$list_name\">\n";
		echo "<option value=\"\">".$VM_LANG->_PHPSHOP_SELECT."</option>\n";
		while ($db->next_record()) {
			echo "<option value=" . $db->f("currency_code");
			if ($value == $db->f("currency_code")) {
				echo " selected=\"selected\"";
			}
			echo ">" . $db->f("currency_name") . "</option>\n";
		}
		echo "</select>\n";
		return True;
	}

	/**
	 * Creates a drop-down list for currencies. The currency ID is used as option value
	 *
	 * @param string $list_name The name of the select element
	 * @param string $value The value of the pre-selected option
	 * @return HTML code with the drop-down list
	 */
	function list_currency_id($list_name, $value="") {
		global $VM_LANG;
		$db = new ps_DB;

		$q = "SELECT * from #__{vm}_currency ORDER BY currency_name ASC";
		$db->query($q);
		echo "<select class=\"inputbox\" name=\"$list_name\">\n";
		echo "<option value=\"\">".$VM_LANG->_PHPSHOP_SELECT."</option>\n";
		while ($db->next_record()) {
			echo "<option value=" . $db->f("currency_id");
			if ($value == $db->f("currency_id")) {
				echo " selected=\"selected\"";
			}
			echo ">" . $db->f("currency_name") . "</option>\n";
		}
		echo "</select>\n";
		return True;
	}

	/**
	 * This is the equivalent to mosCommonHTML::idBox
	 * 
	 * @param int The row index
	 * @param int The record id
	 * @param string The name of the form element
	 * @param string The name of the checkbox element
	 * @return string
	 */
	function idBox( $rowNum, $recId, $frmName="adminForm", $name='cid' ) {

		return '<input type="checkbox" id="cb'.$rowNum.'" name="'.$name.'[]" value="'.$recId.'" onclick="ms_isChecked(this.checked, \''.$frmName.'\');" />';

	}
	/**
	 * Creates a multi-select list with all products except the given $product_id
	 *
	 * @param string $list_name The name of the select element
	 * @param array $values Contains the IDs of all products which are pre-selected
	 * @param int $product_id The product id that is excluded from the list
	 * @param boolean $show_items Wether to show child products as well
	 */
	function list_products($list_name, $values=array(), $product_id, $show_items=false ) {

		$db =& new ps_DB;

		$q = "SELECT product_id, product_name FROM #__{vm}_product ";
		if( !$show_items ) {
			$q .= "WHERE product_parent_id='0' AND product_id <> '$product_id'";
		}
		else {
			$q .= "WHERE product_id <> '$product_id'";
		}
		$q .= ' AND product_publish=\'Y\'';
		// This is necessary, because so much products are difficult to handle!
		$q .= ' LIMIT 0, 2000';
		
		$db->query( $q );
		$products = Array();
		while( $db->next_record() ) {
			$products[$db->f("product_id")] = $db->f("product_name");
		}
		$this->dropdown_display($list_name, $values, $products, $size=20, "multiple=\"multiple\"");
	}

	/**
	 * Creates a drop-down list for Extra fields
	 *
	 * @param string $t The pre-selected value
	 * @param string $extra Additional attributes for the select element
	 */
	function list_extra_field_4($t, $extra="") {
		global $VM_LANG;

		$title = array(array('Y',$VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_4_1),
		array('N',$VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_4_2));

		echo "<select class=\"inputbox\" name=\"extra_field_4\" $extra>\n";
		for ($i=0;$i<count($title);$i++) {
			echo "<option value=\"" . $title[$i][0]."\"";
			if ($title[$i][0] == $t)
			echo " selected=\"selected\" ";
			echo ">" . $title[$i][1] . "</option>\n";
		}
		echo "</select>\n";
	}
	/**
	 * Creates a drop-down list for Extra fields
	 *
	 * @param string $t The pre-selected value
	 * @param string $extra Additional attributes for the select element
	 */
	function list_extra_field_5($t, $extra="") {
		global $VM_LANG;

		$title = array(array('A',$VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_5_1),
		array('B',$VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_5_2),
		array('C',$VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_5_3));

		echo "<select class=\"inputbox\" name=\"extra_field_5\" $extra>\n";
		for ($i=0;$i<count($title);$i++) {
			echo "<option value=\"" . $title[$i][0]."\"";
			if ($title[$i][0] == $t)
			echo " selected=\"selected\" ";
			echo ">" . $title[$i][1] . "</option>\n";
		}
		echo "</select>\n";
	}


	/**
	* Writes a box containing an information about the write access to a file
	* A green colored "Writable" box when the file is writeable
	* A red colored "Unwritable" box when the file is NOT writeable
	* 
	* @param string A path to a file or directory
	* @return string Prints a div element
	*/
	function writableIndicator( $folder ) {
		if( !is_array( $folder)) {
			$folder = array($folder);
		}
		echo '<div class="quote" style="text-align:left;margin-left:20px;" >';
		foreach( $folder as $dir ) {
			echo $dir . ' :: ';
			echo is_writable( $dir )
			? '<span style="font-weight:bold;color:green;">Writeable</span>'
			: '<span style="font-weight:bold;color:red;">Unwriteable</span>';
			echo '<br/>';
		}
		echo '</div>';
	}
	/**
	 * This is used by lists to show a "Delete this item" button in each row
	 *
	 * @param string $id_fieldname The name of the identifying field [example: product_id]
	 * @param mixed $id The unique ID identifying the item that is to be deleted
	 * @param string $func The name of the function that is used to delete the item [e.g. productDelete]
	 * @param string $keyword The recent keyword [deprecated]
	 * @param int $limitstart The recent limitstart value [deprecated]
	 * @param string $extra Additional URL parameters to be appended to the link
	 * @return A link with the delete button in it
	 */
	function deleteButton( $id_fieldname, $id, $func, $keyword="", $limitstart=0, $extra="" ) {
		global $page, $VM_LANG;

		$code = "<a class=\"toolbar\" href=\"index2.php?option=com_virtuemart&page=$page&func=$func&$id_fieldname=$id&keyword=". urlencode($keyword)."&limitstart=$limitstart".$extra."\" onclick=\"return confirm('".$VM_LANG->_PHPSHOP_DELETE_MSG ."');\" onmouseout=\"MM_swapImgRestore();\"  onmouseover=\"MM_swapImage('delete$id','','". IMAGEURL ."ps_image/delete_f2.gif',1);\">";
		$code .= "<img src=\"". IMAGEURL ."ps_image/delete.gif\" alt=\"Delete this record\" name=\"delete$id\" align=\"middle\" border=\"0\" />";
		$code .= "</a>";

		return $code;
	}
	/**
	 * Used to create the Control Panel links with icons in it
	 *
	 * @param string $image The complete icon URL
	 * @param string $link The URL that is linked to
	 * @param string $text The text / label for the link
	 */
	function writePanelIcon( $image, $link, $text ) {
		echo '<div style="float:left;"><div class="icon">
			<a title="'.$text.'" href="'.$link.'">
					<img src="'.$image.'" alt="'.$text.'" align="middle" name="image" border="0" /><br />
			'.$text.'</a></div></div>
			';

	}
}

?>