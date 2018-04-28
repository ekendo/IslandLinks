<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_creditcard.php,v 1.7.2.2 2006/03/14 18:42:11 soeren_nb Exp $
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
* ps_creditcard
*
* The class is is used to manage the CreditCards in your store.
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
class ps_creditcard {
	var $classname = "ps_creditcard";
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

		if (!$d["creditcard_name"]) {
			$this->error = "ERROR:  You must enter a name for the Credit Card.";
			return False;
		}
		if (!$d["creditcard_code"]) {
			$this->error = "ERROR:  You must enter a code for the Credit Card.";
			return False;
		}

		$q = "SELECT count(*) as rowcnt from #__{vm}_creditcard where";
		$q .= " creditcard_name='" .  $d["creditcard_name"] . "' OR ";
		$q .= " creditcard_code='" .  $d["creditcard_code"] . "'";
		$db->setQuery($q);
		$db->query();
		$db->next_record();
		if ($db->f("rowcnt") > 0) {
			$this->error = "The given Credit Card Name/Code already exists.";
			return False;
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

		if (!$d["creditcard_id"]) {
			$this->error = "ERROR:  Please select a Credit Card Type to delete.";
			return False;
		}
		else {
			return True;
		}
	}

	/**************************************************************************
	** name: validate_update
	** created by:
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function validate_update($d) {

		if (!$d["creditcard_name"]) {
			$this->error = "ERROR:  You must enter a name for the Credit Card Type.";
			return False;
		}
		if (!$d["creditcard_code"]) {
			$this->error = "ERROR:  You must enter a code for the Credit Card Type.";
			return False;
		}

		return true;
	}


	/**************************************************************************
	* name: add()
	* created by: soeren
	* description: creates a new Credit Card Entry
	* parameters:
	* returns:
	**************************************************************************/
	function add(&$d) {
		$hash_secret="PHPShopIsCool";
		$db = new ps_DB;
		$timestamp = time();

		if (!$this->validate_add($d)) {
			$d["error"] = $this->error;
			return False;
		}
		$q = "INSERT INTO #__{vm}_creditcard (vendor_id, creditcard_name, creditcard_code)";
		$q .= " VALUES ('";
		$q .= $_SESSION["ps_vendor_id"] . "','";
		$q .= $d["creditcard_name"] . "','";
		$q .= $d["creditcard_code"] . "')";

		$db->query( $q );

		return true;

	}

	/**************************************************************************
	* name: update()
	* created by: soeren
	* description: updates creditcard information
	* parameters:
	* returns:
	**************************************************************************/
	function update(&$d) {
		$db = new ps_DB;
		$timestamp = time();

		if (!$this->validate_update($d)) {
			$d["error"] = $this->error;
			return False;
		}
		$q = "UPDATE #__{vm}_creditcard set ";
		$q .= "creditcard_name='" . $d["creditcard_name"];
		$q .= "',creditcard_code='" . $d["creditcard_code"]."' ";
		$q .= "WHERE creditcard_id='".$d["creditcard_id"]."'";
		$db->setQuery($q);
		$db->query();
		$db->next_record();
		return True;
	}

	/**
	* Controller for Deleting Credit Card Records.
	*/
	function delete(&$d) {

		$creditcard_id = $d["creditcard_id"];

		if( is_array( $creditcard_id)) {
			foreach( $creditcard_id as $creditcard) {
				if( !$this->delete_creditcard( $creditcard, $d ))
				return false;
			}
			return true;
		}
		else {
			return $this->delete_creditcard( $creditcard_id, $d );
		}
	}
	/**
	* Deletes a Credit Card Record.
	*/
	function delete_creditcard( $creditcard_id, &$d ) {
		global $db;

		if (!$this->validate_delete($d)) {
			$d["error"]=$this->error;
			return False;
		}
		$q = "DELETE FROM #__{vm}_creditcard WHERE creditcard_id='" . $creditcard_id . "'";
		$db->query($q);
		return True;
	}


	/**************************************************************************
	* name: creditcard_checkboxes()
	* created by: soeren
	* description: Creates a Checkbox - List of all Credit Card Records.
	* parameters: String "selected": a comma-delimited list of creditcard_IDs, assigned to
	*                  this payment method
	* returns:
	**************************************************************************/
	function creditcard_checkboxes( $selected ) {

		if (!empty( $selected ))
		$selected_arr = explode( ",", $selected);
		else
		$selected_arr = Array();
		$db = new ps_DB;
                $q = "SELECT creditcard_name, creditcard_id FROM #__{vm}_creditcard WHERE vendor_id='".$_SESSION['ps_vendor_id']."'";
                $db->query( $q );
                $html = "";
                $i = 0;
                while( $db->next_record() ) {
                        $html .= "<input type=\"checkbox\" name=\"creditcard[]\"  id=\"creditcard$i\" value=\"".$db->f("creditcard_id")."\" class=\"inputbox\" ";
                        if (in_array($db->f("creditcard_id"), $selected_arr)) {
                                $html .= "checked=\checked\"";
                        }
                        $html .= "/>";
                        $html .= "<label for=\"creditcard$i\">".$db->f("creditcard_name")."</label><br/>";
                        $i++;
                }

                echo $html;
	}

	/**************************************************************************
	* name: creditcard_selector()
	* created by: soeren
	* description: Creates a Drop Down - List of Credit Card Records.
	* parameters:
	* returns:
	**************************************************************************/
	function creditcard_selector( $payment_method_id="" ) {

		$db = new ps_DB;

		/*** Select all credit card records ***/
		if(empty($payment_method_id)) {
			$q = "SELECT creditcard_name, creditcard_id,creditcard_code FROM #__{vm}_creditcard WHERE vendor_id='".$_SESSION['ps_vendor_id']."'";
		}
		/*** Get only accepted credit cards records ***/
		else {
			$q = "SELECT accepted_creditcards FROM #__{vm}_payment_method WHERE payment_method_id='$payment_method_id'";
			$db->query( $q );
			$db->next_record();
			$cc_array = explode( ",", $db->f("accepted_creditcards"));
			$q = "SELECT creditcard_name,creditcard_id,creditcard_code FROM #__{vm}_creditcard WHERE vendor_id='".$_SESSION['ps_vendor_id']."' AND (";
			foreach ( $cc_array as $idx => $creditcard_id ) {
				$q .= "creditcard_id='$creditcard_id' ";
				if( $idx+1 < sizeof( $cc_array )) $q.= "OR ";
				else $q .= ")";
			}
		}
		$db->query( $q );
		$html = "<select name=\"creditcard_code\" class=\"inputbox\">";

		while( $db->next_record() ) {
			$html .= "<option value=\"".$db->f("creditcard_code")."\">";
			$html .= $db->f("creditcard_name")."</option>";
		}
		$html .= "</select>";

		echo $html;
	}


	/**************************************************************************
	* name: creditcard_lists()
	* created by: soeren
	* description: Build a Credit Card list for each CreditCard Payment Method
	*              Uses JavsScript from mambojavascript: changeDynaList()
	* parameters: $db_cc, a ps_database Object with a query pending
	* returns: the script code
	**************************************************************************/
	function creditcard_lists( &$db_cc ) {
		$db = new ps_DB;

		$db_cc->next_record();
		// Build the Credit Card lists for each CreditCard Payment Method
		$script = "<script language=\"javascript\" type=\"text/javascript\">\n";
		$script .= "<!--\n";
		$script .= "var originalOrder = '1';\n";
		$script .= "var originalPos = '".$db_cc->f("payment_method_name")."';\n";
		$script .= "var orders = new Array();	// array in the format [key,value,text]\n";
		$i = 0;
		$db_cc->reset();

		while( $db_cc->next_record() ) {
			$accepted_creditcards = explode( ",", $db_cc->f("accepted_creditcards") );
			$cards = Array();
			foreach( $accepted_creditcards as $value ) {
				if( !empty( $value)) {
					$q = "SELECT creditcard_code,creditcard_name FROM #__{vm}_creditcard WHERE creditcard_id='$value'";
					$db->query( $q );
					$db->next_record();

					$cards[$db->f('creditcard_code')] = htmlspecialchars( $db->f('creditcard_name'), ENT_QUOTES );
				}
			}
			foreach( $cards as $code => $name ) {
				$script .= "orders[".$i++."] = new Array( '".addslashes($db_cc->f("payment_method_name"))."','$code','$name' );\n";
			}

			}
			$script .= "function changeCreditCardList() { \n";
			$script .= "var selected_payment = null;
      for (var i=0; i<document.adminForm.payment_method_id.length; i++)
         if (document.adminForm.payment_method_id[i].checked)
            selected_payment = document.adminForm.payment_method_id[i].id;\n";
			$script .="changeDynaList('creditcard_code',orders,selected_payment, originalPos, originalOrder);\n";
			$script .="}\n";
			$script .="//-->\n";
			$script .="</script>\n";

			return $script;
		}
	}

?>