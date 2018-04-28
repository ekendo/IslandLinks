<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_checkout.php,v 1.22.2.12 2006/05/07 11:19:03 soeren_nb Exp $
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

define("CHECK_OUT_GET_FINAL_BASKET", 1);
define("CHECK_OUT_GET_SHIPPING_ADDR", 2);
define("CHECK_OUT_GET_SHIPPING_METHOD", 3);
define("CHECK_OUT_GET_PAYMENT_METHOD", 4);
define("CHECK_OUT_GET_FINAL_CONFIRMATION", 99);

/**
 * The class contains the shop checkout code.  It is used to checkout
 * and order and collect payment information.
 *
 */
class ps_checkout {
	var $classname = "ps_checkout";
	var $_SHIPPING = null;
	/** @var int The current subtotal */
	var $_subtotal = 0;
	
	/**
	 * Initiate Shipping Modules
	 *
	 * @return ps_checkout
	 */
	function ps_checkout() {
		global $vendor_freeshipping, $vars, $PSHOP_SHIPPING_MODULES;

		/* Ok, need to decide if we have a free Shipping amount > 0,
		* and IF the cart total is more than that Free Shipping amount,
		* let's set Order Shipping = 0
		*/
		$this->_subtotal = $this->calc_order_subtotal($vars);
		
		if( $vendor_freeshipping > 0 && $vars['order_subtotal_withtax'] > $vendor_freeshipping) {
			$PSHOP_SHIPPING_MODULES = Array( "free_shipping" );
			include_once( CLASSPATH. "shipping/free_shipping.php" );
			$this->_SHIPPING =& new free_shipping();
		}
		elseif( !empty( $_REQUEST['shipping_rate_id'] )) {

			// Create a Shipping Object and assign it to the _SHIPPING attribute
			// We take the first Part of the Shipping Rate Id String
			// which holds the Class Name of the Shipping Module
			$rate_array = explode( "|", urldecode($_REQUEST['shipping_rate_id']) );
			$filename = basename( $rate_array[0] );
			include_once( CLASSPATH. "shipping/".$filename.".php" );
			eval( "\$this->_SHIPPING =& new ".$filename."();");

		}
        if(empty($_REQUEST['ship_to_info_id']) && (CHECKOUT_STYLE=='3' || CHECKOUT_STYLE=='4')) {

            $db = new ps_DB();

            /* Select all the ship to information for this user id and
            * order by modification date; most recently changed to oldest
            */
            $q  = "SELECT user_info_id from `#__{vm}_user_info` WHERE ";
            $q .= "user_id='" . $_SESSION['auth']["user_id"] . "' ";
            $q .= "AND address_type='BT'";
            $db->query($q);
            $db->next_record();

            $_REQUEST['ship_to_info_id'] = $db->f("user_info_id");
        }
	}

	/**
	 * Enter description here...
	 *
	 * @param array $steps_to_do Array holding all steps the customer has to make
	 * @param array $step_msg Array containing the step messages
	 * @param int $step_count Number of steps to make
	 * @param int $highlighted_step The index of the recent step
	 */
	function show_checkout_bar($steps_to_do, $step_msg, $step_count, $highlighted_step) {

		global $sess, $ship_to_info_id, $shipping_rate_id;

		// CSS style for the <td> tag of the cell which is actually highlighted
		$highlighted_style = 'style="font-weight: bold;"';
		echo '
		<table style="background: url('. IMAGEURL .'ps_image/checkout'. $step_count.'_'.$highlighted_step .'.png) right; background-repeat: no-repeat; height:85px;text-align:center;" border="0" cellspacing="0" cellpadding="0">
          <tr>';
          

        for ($i = 1; $i <= $step_count; $i++) { 
        
            echo '<td '.(($highlighted_step==$i) ? $highlighted_style : '') .' width="119" align="center" valign="bottom">';
            if ($highlighted_step > $i) {
            	echo '<a href="'. $sess->url(SECUREURL."index.php?page=checkout.index&option=com_virtuemart&ship_to_info_id=$ship_to_info_id&shipping_rate_id=".@$shipping_rate_id."&checkout_next_step=".$steps_to_do[$i-1]) .'">';
            	echo $step_msg[$i-1] .'</a>';
            }
            else {
            	echo $step_msg[$i-1];
			}
            echo '</td>';

        }
        echo '    
          </tr>
        </table>
        <br />';
        
	}
	
	/**
	 * Called to validate the form values before the order is stored
	 * 
	 * @author gday
	 * @author soeren
	 * 
	 * @param unknown_type $d
	 * @return unknown
	 */
	function validate_form(&$d) {
		global $VM_LANG, $PSHOP_SHIPPING_MODULES, $vmLogger;

		$db = new ps_DB;
		
		$auth = $_SESSION['auth'];
		$cart = $_SESSION['cart'];

		if (!$cart["idx"]) {
			$q  = "SELECT order_id FROM #__{vm}_orders WHERE user_id='" . $auth["user_id"] . "' ";
			$q .= "ORDER BY cdate DESC";
			$db->query($q);
			$db->next_record();
			$d["order_id"] = $db->f("order_id");
			return False;
		}
		if( PSHOP_AGREE_TO_TOS_ONORDER == '1' ) {
			if( empty( $d["agreed"] )) {
				$vmLogger->warning( $VM_LANG->_PHPSHOP_AGREE_TO_TOS );
				return false;
			}
		}

		if ( NO_SHIPPING != "1" && (CHECKOUT_STYLE=='1' || CHECKOUT_STYLE=='3') ) {
			if ( !$this->validate_shipping_method($d) ) {
				return False;
			}
		}
		if ( !$this->validate_payment_method( $d, false )) {
			return false;
		}

		// calculate the unix timestamp for the specified expiration date
		// default the day to the 1st
		$expire_timestamp = @mktime(0,0,0,$_SESSION["ccdata"]["order_payment_expire_month"], 1,$_SESSION["ccdata"]["order_payment_expire_year"]);
		$_SESSION["ccdata"]["order_payment_expire"] = $expire_timestamp;

		return True;
	}


	/**************************************************************************
	** name: validate_add()
	** created by: gday
	** description:  Validates the checkout form values prior to adding
	** parameters: $d
	** returns:  True - validation passed
	**          False - validation failed
	***************************************************************************/
	function validate_add(&$d) {
		global $VM_LANG, $vmLogger;

		require_once(CLASSPATH.'ps_payment_method.php');
		$ps_payment_method = new ps_payment_method;

		$d = $GLOBALS['vmInputFilter']->process( $d );

		if (NO_SHIPTO != '1') {
			if (empty($d["ship_to_info_id"])) {
				$vmLogger->err( $VM_LANG->_PHPSHOP_CHECKOUT_ERR_NO_SHIPTO );
				return False;
			}
		}
		/*
		if (!$d["payment_method_id"]) {
			$vmLogger->err( $VM_LANG->_PHPSHOP_CHECKOUT_MSG_4 );
			return False;
		}*/
		if ($ps_payment_method->is_creditcard(@$d["payment_method_id"])) {

			if (empty($_SESSION["ccdata"]["order_payment_number"])) {
				$vmLogger->err( $VM_LANG->_PHPSHOP_CHECKOUT_ERR_NO_CCNR );
				return False;
			}

			if(!$ps_payment_method->validate_payment($d["payment_method_id"],
					$_SESSION["ccdata"]["order_payment_number"])) {
				$vmLogger->err( $VM_LANG->_PHPSHOP_CHECKOUT_ERR_CCNUM_INV );
				return False;
			}

			if(empty( $_SESSION["ccdata"]["order_payment_expire"])) {
				$vmLogger->err( $VM_LANG->_PHPSHOP_CHECKOUT_ERR_CCDATE_INV );
				return False;
			}
		}

		return True;
	}

	/**************************************************************************
	** name: validate_shipping_method()
	** created by: Ekkehard Domning
	** description: Called to validate the shipping_method
	**              The function caluculate the weight of the product in the cart.
	**              After that the select shipping method is validated against the
	**              Address data of the user, the weight and the shipping_rate_id
	** parameters: $d (shipping_rate_id)
	** returns: True  - validation passed
	**          False - validation failed
	***************************************************************************/
	function validate_shipping_method(&$d) {
		global $VM_LANG, $PSHOP_SHIPPING_MODULES, $vmLogger;
		
		if( empty($d['shipping_rate_id']) ) {
			$vmLogger->err( $VM_LANG->_PHPSHOP_CHECKOUT_ERR_NO_SHIP );
			return false;
		}
		if( is_callable( array($this->_SHIPPING, 'validate') )) {
			
			if(!$this->_SHIPPING->validate( $d )) {
				$vmLogger->err( $VM_LANG->_PHPSHOP_CHECKOUT_ERR_OTHER_SHIP );
				return false;
			}
		}
		// JBarrera 11Jun05 - never returned true
		return true;
	}

	/**************************************************************************
	** name: validate_payment_method()
	** created by: Ekkehard Domning
	** description: Called to validate the payment_method
	**              If payment with CreditCard is used, than the Data must be in stored in the session
	**              This has be done to prevent sending the CreditCard Number back in hidden fields
	**              If the parameter $is_test is true the Number Visa Creditcard number 4111 1111 1111 1111
	**              is reported as Valid
	** parameters: $d, $istest
	** returns: True  - validation passed
	**          False - validation failed
	***************************************************************************/
	function validate_payment_method(&$d, $is_test) {
		global $VM_LANG, $vmLogger, $order_total;

		$auth = $_SESSION['auth'];
		$cart = $_SESSION['cart'];
		
		// We don't need to validate a payment method when
		// the user has no order total he should pay
		if( empty( $_REQUEST['order_total'])) {
			
			if( !empty( $d['order_total'])) {
				if( $d['order_total'] <= 0.00 ) {
					return true;
				}
			}
			if( isset($order_total) && $order_total <= 0.00 ) {
				return true;
			}
		}
		if (empty($d["payment_method_id"]) ) {
			$vmLogger->err( $VM_LANG->_PHPSHOP_CHECKOUT_ERR_NO_PAYM );
			return false;
		}
		require_once(CLASSPATH.'ps_payment_method.php');
		$ps_payment_method = new ps_payment_method;

		$dbp = new ps_DB; //DB Payment_method

		// Now Check if all needed Payment Information are entered
		// Bank Information is found in the User_Info
		$w  = "SELECT enable_processor FROM #__{vm}_payment_method WHERE ";
		$w .= "payment_method_id = '" .  $d["payment_method_id"] . "' ";
		$dbp->query($w);
		$dbp->next_record();
		if (($dbp->f("enable_processor") == "Y") ||
		($dbp->f("enable_processor") == "")) {

			/*** Creditcard ***/
			if (empty( $_SESSION['ccdata']['creditcard_code']) ) {
				$vmLogger->err( "Credit Card Type not found." );
				return false;
			}

			/*** $_SESSION['ccdata'] = $ccdata;
			* The Data should be in the session ***/
			if (!isset($_SESSION['ccdata'])) { //Not? Then Error
				$vmLogger->err( $VM_LANG->_PHPSHOP_CHECKOUT_ERR_NO_CCDATA );
				return False;
			}

			if (!$_SESSION['ccdata']['order_payment_number']) {
				$vmLogger->err( $VM_LANG->_PHPSHOP_CHECKOUT_ERR_NO_CCNR_FOUND );
				return False;
			}

			/** CREDIT CARD NUMBER CHECK
        ** USING THE CREDIT CARD CLASS in ps_payment **/
			if(!$ps_payment_method->validate_payment( $_SESSION['ccdata']['creditcard_code'], $_SESSION['ccdata']['order_payment_number'])) {
				$vmLogger->err( $VM_LANG->_PHPSHOP_CHECKOUT_ERR_NO_CCDATE );
				return False;
			}

			if (!$is_test) {
				$payment_number = ereg_replace(" |-", "", $_SESSION['ccdata']['order_payment_number']);
				if ($payment_number == "4111111111111111") {
					$vmLogger->warning( $VM_LANG->_PHPSHOP_CHECKOUT_ERR_TEST );
					return False;
				}
			}
			if(!empty($_SESSION['ccdata']['need_card_code']) && empty($_SESSION['ccdata']['credit_card_code'])) {
				$vmLogger->err( $VM_LANG->_PHPSHOP_CUSTOMER_CVV2_ERROR );
				return False;
			}
			if(!$_SESSION['ccdata']['order_payment_expire_month']) {
				$vmLogger->err( $VM_LANG->_PHPSHOP_CHECKOUT_ERR_NO_CCMON );
				return False;
			}
			if(!$_SESSION['ccdata']['order_payment_expire_year']) {
				$vmLogger->err( $VM_LANG->_PHPSHOP_CHECKOUT_ERR_NO_CCYEAR );
				return False;
			}
			$date = getdate( time() );
			if ($_SESSION['ccdata']['order_payment_expire_year'] < $date["year"] or
			($_SESSION['ccdata']['order_payment_expire_year'] == $date["year"] and
			$_SESSION['ccdata']['order_payment_expire_month'] < $date["mon"])) {
				$vmLogger->err( $VM_LANG->_PHPSHOP_CHECKOUT_ERR_CCDATE_INV );
				return False;
			}
			return True;
		}
		elseif ($dbp->f("enable_processor") == "B") {
			$_SESSION['ccdata']['creditcard_code'] = "";
			// Bankeinzug
			$dbu = new ps_DB; //DB User
			$q  = "SELECT bank_account_holder,bank_iban,bank_account_nr,bank_sort_code,bank_name FROM #__{vm}_user_info WHERE user_id = '" . $d["user_id"] . "'";
			$dbu->query($q);
			if (!$dbu->next_record()) {
				$vmLogger->err( $VM_LANG->_PHPSHOP_CHECKOUT_ERR_NO_USER_DATA );
				return False;
			}
			if ($dbu->f("bank_account_holder") == ""){
				$vmLogger->err( $VM_LANG->_PHPSHOP_CHECKOUT_ERR_NO_BA_HOLDER_NAME );
				return False;
			}
			if (($dbu->f("bank_iban") == "") and
			($dbu->f("bank_account_nr") =="")) {
				$vmLogger->err( $VM_LANG->_PHPSHOP_CHECKOUT_ERR_NO_IBAN );
				return False;
			}
			if ($dbu->f("bank_iban") == "") {
				if ($dbu->f("bank_account_nr") == ""){
					$vmLogger->err( $VM_LANG->_PHPSHOP_CHECKOUT_ERR_NO_BA_NUM );
					return False;
				}
				if ($dbu->f("bank_sort_code") == ""){
					$vmLogger->err( $VM_LANG->_PHPSHOP_CHECKOUT_ERR_NO_BANK_SORT );
					return False;
				}
				if ($dbu->f("bank_name") == ""){
					$vmLogger->err( $VM_LANG->_PHPSHOP_CHECKOUT_ERR_NO_BANK_NAME );
					return False;
				}
			}
		}
		else {
			$_SESSION['ccdata']['creditcard_code'] = '';
		}
		// Enter additional Payment check procedures here if neccessary

		return True;
	}



	/**************************************************************************
	** name: update()
	** created by: gday
	** description:  Update the order in the database
	** parameters: $d
	** returns:  True - update succeeded
	**          False - update failed
	***************************************************************************/
	function update(&$d) {
		global $vmLogger;
		
		$db = new ps_DB;
		$timestamp = time();


		if ($this->validate_update($d)) {
			return True;
		}
		else {
			$vmLogger->err( $this->error );
			return False;
		}
	}

	/**************************************************************************
	** name: process()
	** created by: Ekkhard Domning
	** description:  Controls the Checkout Process
	** parameters: $d
	** returns:  True - if the Step is validated
	**          False - a failure during the validation
	***************************************************************************/
	function process(&$d) {
		global $checkout_this_step, $sess,$VM_LANG, $vmLogger;
		$ccdata = array();

		if (!$d["checkout_this_step"]) {
			$vmLogger->err( $VM_LANG->_PHPSHOP_CHECKOUT_ERR_NO_VALID_STEP );
			return false;
		}
		switch ($d["checkout_this_step"]) {

			case CHECK_OUT_GET_FINAL_BASKET :
	
				// The User has finished his works on the Basket, now the next steps
				$d["checkout_this_step"] = CHECK_OUT_GET_SHIPPING_ADDR;
				$checkout_this_step = CHECK_OUT_GET_SHIPPING_ADDR;
				break;

			case CHECK_OUT_GET_SHIPPING_ADDR :

				$d["checkout_this_step"] = CHECK_OUT_GET_SHIPPING_METHOD;
				$checkout_this_step = CHECK_OUT_GET_SHIPPING_METHOD;
	
				// The User has choosen a Shipping address
				if (empty($d["ship_to_info_id"])) {
					$d["error"] = $VM_LANG->_PHPSHOP_CHECKOUT_ERR_NO_SHIPTO;
					return False;
				}
				break;

			case CHECK_OUT_GET_SHIPPING_METHOD:

				$d["checkout_this_step"] = CHECK_OUT_GET_PAYMENT_METHOD;
				$checkout_this_step = CHECK_OUT_GET_PAYMENT_METHOD;
				// The User has choosen a Shipping method
				if (!$this->validate_shipping_method($d)) {
					$d["checkout_this_step"] = CHECK_OUT_GET_SHIPPING_METHOD;
					$_REQUEST["checkout_next_step"] = CHECK_OUT_GET_SHIPPING_METHOD;
					return false;
				}
				break;

			case CHECK_OUT_GET_PAYMENT_METHOD:

				$d["checkout_this_step"] = CHECK_OUT_GET_FINAL_CONFIRMATION;
				$checkout_this_step = CHECK_OUT_GET_FINAL_CONFIRMATION;
				
				// The User has choosen a payment method
				$_SESSION['ccdata']['order_payment_name'] = @$d['order_payment_name'];
				// VISA, AMEX, DISCOVER....
				$_SESSION['ccdata']['creditcard_code'] = @$d['creditcard_code'];
				$_SESSION['ccdata']['order_payment_number'] = @$d['order_payment_number'];
				$_SESSION['ccdata']['order_payment_expire_month'] = @$d['order_payment_expire_month'];
				$_SESSION['ccdata']['order_payment_expire_year'] = @$d['order_payment_expire_year'];
				// 3-digit Security Code (CVV)
				$_SESSION['ccdata']['credit_card_code'] = @$d['credit_card_code'];
	
				if (!$this->validate_payment_method($d, false)) { //Change false to true to Let the user play with the VISA Testnumber
					$d["checkout_this_step"] = CHECK_OUT_GET_PAYMENT_METHOD;
					$_REQUEST["checkout_next_step"] = CHECK_OUT_GET_PAYMENT_METHOD;
					return false;
				}
				
				break;

			case CHECK_OUT_GET_FINAL_CONFIRMATION:

				// The User wants to order now, validate everything, if OK than Add immeditialtly
				return( $this->add( $d ) );

			default:
				$vmLogger->crit( "CheckOut step ($checkout_this_step) is undefined!" );
				return false;

		} // end switch
		return true;
	} // end function process

	/**************************************************************************
	** name: ship_to_address_radio()
	** created by: gday
	** description:  Get all the user_info Ship To (ST) records associated
	**               with the $user_id and print an HTML radio check box
	**               form element using the retrieved data.
	** parameters: $user_id - user id of to display ship to addresses
	**             $name - name of the HTML radio element
	**             $value - If matched, then this radio item will be
	**                      checked
	** returns:  Prints html radio element to standard out
	***************************************************************************/
	function ship_to_addresses_radio($user_id, $name, $value) {
		global $sess,$VM_LANG;

		$db = new ps_DB;

		/* Select all the ship to information for this user id and
		* order by modification date; most recently changed to oldest
		*/
		$q  = "SELECT * from #__{vm}_user_info WHERE ";
		$q .= "user_id='" . $user_id . "' ";
		$q .= "AND address_type='BT'";
		$db->query($q);
		$db->next_record();

		$bt_user_info_id = $db->f("user_info_id");

		$q  = "SELECT user_info_id, address_type_name, company, title, ";
		$q .= "last_name, first_name, middle_name, phone_1, phone_2, ";
		$q .= "fax, address_1, address_2, city, ";
		$q .= "state, country, zip ";
		$q .= "FROM #__{vm}_user_info ";
		$q .= "WHERE user_id = '" . $user_id . "' ";
		$q .= "AND address_type = 'ST' ";
		$q .= "ORDER by address_type_name, mdate DESC";

		$db->query($q);

		echo "<table border=\"0\" width=\"100%\" cellpadding=\"2\" cellspacing=\"0\">\n";
		echo "<tr class=\"sectiontableentry1\">\n";
		echo "<td>\n";
		if ($db->num_rows() and $bt_user_info_id != $value) {
			echo "<input type=\"radio\" name=\"$name\" value=\"$bt_user_info_id\" />\n";
		} else {
			echo "<input type=\"radio\" name=\"$name\" value=\"$bt_user_info_id\" checked=\"checked\" />\n";
		}
		echo "</td>\n";
		echo "<td>\n";
		echo $VM_LANG->_PHPSHOP_ACC_BILL_DEF."\n";
		echo "</td>\n";
		echo "</tr>\n";
		$i = 2;
		while($db->next_record()) {
			echo "<tr class=\"sectiontableentry$i\">\n";
			echo "<td>\n";
			if (!strcmp($value, $db->f("user_info_id"))) {
				echo "<input type=\"radio\" name=\"$name\" value=\"" . $db->f("user_info_id") . "\" checked=\"checked\">\n";
			}
			else {
				echo "<input type=\"radio\" name=\"$name\" value=\"" . $db->f("user_info_id") . "\">\n";
			}
			echo "</td>\n";
			echo "<td>\n";
			echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">\n";
			echo "<tr>\n";
			echo "<td>\n";
			echo "<strong>" . $db->f("address_type_name") . "</strong> ";
			$url = SECUREURL . "index.php?page=account.shipto&user_info_id=" . $db->f('user_info_id');
			$url .= "&next_page=checkout.index";
			echo "(<a href=\"".$sess->url($url)."\">".$VM_LANG->_PHPSHOP_UDATE_ADDRESS."</a>)\n";
			echo "<br />\n";
			echo $db->f("title") . " ";
			echo $db->f("first_name") . " ";
			echo $db->f("middle_name") . " ";
			echo $db->f("last_name") . "\n";
			echo "<br />\n";
			if ($db->f("company")) {
				echo $db->f("company") . "<br />\n";
			}
			echo $db->f("address_1") . "\n";
			if ($db->f("address_2")) {
				echo "<br />". $db->f("address_2"). "\n";
			}
			echo "<br />\n";
			echo $db->f("city");
			echo ", ";
			echo $db->f("state") . " ";
			echo $db->f("zip") . "<br />\n";
			echo "Phone:". $db->f("phone_1") . "\n";
			echo "<br />\n";
			echo "Fax:".$db->f("fax") . "\n";
			echo "</td></tr>\n";
			echo "</table></td></tr>\n";
			if($i == 1) $i++;
			elseif($i == 2) $i--;
		}

		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		return(true);
	}

	/**************************************************************************
	** name: display_address()
	** created by: gday
	** description:  Print an HTML table displaying the user_info record
	**               for the specified $user_info_id and $address_type.
	** parameters: $user_info_id - user info id to display
	**             $address_type - address type (BT or ST)
	** returns: Prints HTML table displaying the address information
	***************************************************************************/
	function display_address($user_info_id) {
		$db = new ps_DB;

		$q = "SELECT address_type_name, company, title, last_name, ";
		$q .= "first_name, middle_name, phone_1, phone_2, fax, ";
		$q .= "address_1, address_2, city, state, country, zip ";
		$q .= "FROM #__{vm}_user_info ";
		$q .= "WHERE user_info_id = '$user_info_id'";

		$db->query($q);

		if($db->next_record()) {
			echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">\n";
			echo "<tr><td align=\"center\">";
			if ($db->f("address_type_name") != "-default-") {
				echo "<strong>" . $db->f("address_type_name") . "</strong><br />";
			}
			echo $db->f("title") . " ". $db->f("first_name") . " ". $db->f("middle_name") . " ". $db->f("last_name") . " ";
			echo "<br />";
			if ($db->f("company")) {
				echo $db->f("company");
				echo "<br />";
			}
			echo $db->f("address_1");
			if ($db->f("address_2")) {
				echo "<br />";
				echo $db->f("address_2");
			}
			echo "<br />". $db->f("city"). ", ". $db->f("state") . " ". $db->f("zip"). "<br />";
			echo "Phone:". $db->f("phone_1"). "<br />";
			echo "Fax:". $db->f("fax"). "</td></tr></table>";
		}

		return True;
	}

	/**
	 * This is the main function which stores the order information in the database
	 * 
	 * @author gday, soeren, many others!
	 * @param array $d The REQUEST/$vars array
	 * @return boolean
	 */
	function add( &$d ) {
		global $order_tax_details, $afid, $VM_LANG, $mosConfig_debug, $mosConfig_offset,
		$vmLogger, $vmInputFilter, $discount_factor;

		$ps_vendor_id = $_SESSION["ps_vendor_id"];
		$auth = $_SESSION['auth'];
		$cart = $_SESSION['cart'];
		$d["error"] = "";
		require_once(CLASSPATH. 'ps_payment_method.php' );
		$ps_payment_method = new ps_payment_method;
		require_once(CLASSPATH. 'ps_product.php' );
		$ps_product= new ps_product;
		require_once(CLASSPATH.'ps_cart.php');
		$ps_cart = new ps_cart;

		if (AFFILIATE_ENABLE == '1') {
			require_once(CLASSPATH.'ps_affiliate.php');
			$ps_affiliate = new ps_affiliate;
		}

		$db = new ps_DB;

		/* Set the order number */
		$order_number = $this->get_order_number();

		/* sets _subtotal */
		$order_subtotal = $tmp_subtotal = $this->calc_order_subtotal($d);
		
		$order_taxable = $this->calc_order_taxable($d);

		$payment_discount = $d['payment_discount'] = $this->get_payment_discount($d['payment_method_id'], $order_subtotal);

		/* DISCOUNT HANDLING */
		if( !empty($_SESSION['coupon_discount']) ) {
			$coupon_discount = floatval($_SESSION['coupon_discount']);
		}
		else {
			$coupon_discount = 0.00;
		}

		// make sure Total doesn't become negative
		if( $tmp_subtotal < 0 ) $order_subtotal = $tmp_subtotal = 0;
		if( $order_taxable < 0 ) $order_taxable = 0;

		// from now on we have $order_tax_details
		$d['order_tax'] = $order_tax = round( $this->calc_order_tax($order_taxable, $d), 2 );
		
		if( $this->_SHIPPING ) {			
			/* sets _shipping */
			$d['order_shipping'] = $order_shipping = round( $this->calc_order_shipping( $d ), 2 );

			/* sets _shipping_tax
			* btw: This is WEIRD! To get an exactly rounded value we have to convert
			* the amount to a String and call "round" with the string. */
			$d['order_shipping_tax'] = $order_shipping_tax = round( strval($this->calc_order_shipping_tax($d)), 2 );
			
			//$shipping_taxrate = $this->_SHIPPING->get_tax_rate();
			//@$order_tax_details[$shipping_taxrate] += $order_shipping_tax;
		}
		else {
			$d['order_shipping'] = $order_shipping = $order_shipping_tax = $d['order_shipping_tax'] = 0.00;
		}

		$timestamp = time() + ($mosConfig_offset*60*60);

		$d['order_total'] = $order_total = 	$tmp_subtotal 
											+ $order_tax 
											+ $order_shipping 
											+ $order_shipping_tax
											- $coupon_discount
											- $payment_discount;
		
		$order_tax *= $discount_factor;
		
		if (!$this->validate_form($d)) {
			return false;
		}

		if (!$this->validate_add($d)) {
			return false;
		}

		// make sure Total doesn't become negative
		if( $order_total < 0 ) $order_total = 0;

		$order_total = round( $order_total, 2);


		$vmLogger->debug( '-- Checkout Debug--
		
Subtotal: '.$order_subtotal.'
Taxable: '.$order_taxable.'
Payment Discount: '.$payment_discount.'
Coupon Discount: '.$coupon_discount.'
Shipping: '.$order_shipping.'
Shipping Tax : '.$order_shipping_tax.'
Tax : '.$order_tax.'
------------------------
Order Total: '.$order_total.'
----------------------------' 
		);

		// Check to see if Payment Class File exists
		$payment_class = $ps_payment_method->get_field($d["payment_method_id"], "payment_class");
		$enable_processor = $ps_payment_method->get_field($d["payment_method_id"], "enable_processor");

		if (file_exists(CLASSPATH . "payment/$payment_class.php") ) {
			if( !class_exists( $payment_class ))
			include( CLASSPATH. "payment/$payment_class.php" );

			eval( "\$_PAYMENT = new $payment_class();" );
			if (!$_PAYMENT->process_payment($order_number,$order_total, $d)) {
				$vmLogger->err( $VM_LANG->_PHPSHOP_PAYMENT_ERROR." ($payment_class)" );
				$_SESSION['last_page'] = "checkout.index";
				$_REQUEST["checkout_next_step"] = CHECK_OUT_GET_PAYMENT_METHOD;
				return False;
			}
		}

		else {
			$d["order_payment_log"] = $VM_LANG->_PHPSHOP_CHECKOUT_MSG_LOG;
		}

		// Remove the Coupon, because it is a Gift Coupon and now is used!!
		if( @$_SESSION['coupon_type'] == "gift" ) {
			$d['coupon_id'] = $_SESSION['coupon_id'];
			include_once( CLASSPATH.'ps_coupon.php' );
			ps_coupon::remove_coupon_code( $d );
		}

		/* Insert the main order information */
		$q = "INSERT INTO #__{vm}_orders \n";
		$q .= "(user_id, vendor_id, order_number, user_info_id, ship_method_id, \n";
		$q .= "order_total, order_subtotal, order_tax, order_tax_details, order_shipping, \n";
		$q .= "order_shipping_tax, order_discount, coupon_discount,order_currency, order_status, cdate, \n";
		$q .= "mdate,customer_note,ip_address) \n";
		$q .= "VALUES (";
		$q .= "'" . $auth["user_id"] . "', ";
		$q .= $ps_vendor_id . ", ";
		$q .= "'" . $order_number . "', '";
		$q .= $d["ship_to_info_id"] . "', '";

		if (!empty($d["shipping_rate_id"])) {
			$q .= urldecode($d["shipping_rate_id"]) . "', '";
		}
		else {
			$q .= "', '";
		}
		$q .= $order_total . "', '";
		$q .= $order_subtotal . "', '";
		$q .= $order_tax . "', '";
		$q .= serialize($order_tax_details). "', '";
		$q .= $order_shipping . "', '";
		$q .= $order_shipping_tax . "', '";
		$q .= $payment_discount . "', '";
		$q .= $coupon_discount . "', '";
		$q .= $_SESSION['vendor_currency']."', "; /* Currency is at the product level - line item */
		$q .= "'P', '";
		$q .= $timestamp . "', '";
		$q .= $timestamp. "', '";
		$q .= htmlspecialchars(strip_tags($d['customer_note'])) . "', '";
		if (!empty($_SERVER['REMOTE_ADDR'])) {
			$q .= $_SERVER['REMOTE_ADDR'] . "') ";
		}
		else {
			$q .= "unknown') ";
		}
		$db->query($q);
		$db->next_record();

		/* Get the order id just stored */

		$q = "SELECT order_id FROM #__{vm}_orders WHERE order_number = ";
		$q .= "'" . $order_number . "'";

		$db->query($q);
		$db->next_record();

		$d["order_id"] = $order_id = $db->f("order_id");

		/**
	    * Insert the initial Order History.
	    */
		$mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
		
		$q = "INSERT INTO #__{vm}_order_history ";
		$q .= "(order_id,order_status_code,date_added,customer_notified,comments) VALUES (";
		$q .= "'$order_id', 'P', '" . $mysqlDatetime . "', 1, '')";
		$db->query($q);

		/**
	    * Insert the Order payment info 
	    */
		$payment_number = ereg_replace(" |-", "", @$_SESSION['ccdata']['order_payment_number']);

		$d["order_payment_code"] = @$_SESSION['ccdata']['credit_card_code'];

		// Payment number is encrypted using mySQL ENCODE function.
		$q = "INSERT INTO #__{vm}_order_payment ";
		$q .= "(order_id, order_payment_code, payment_method_id, order_payment_number, ";
		$q .= "order_payment_expire, order_payment_log, order_payment_name, order_payment_trans_id) ";
		$q .= "VALUES ('$order_id', ";
		$q .= "'" . $d["order_payment_code"] . "', ";
		$q .= "'" . $d["payment_method_id"] . "', ";
		$q .= "ENCODE(\"$payment_number\",\"" . ENCODE_KEY . "\"), ";
		$q .= "'" . @$_SESSION["ccdata"]["order_payment_expire"] . "',";
		$q .= "'" . @$d["order_payment_log"] . "',";
		$q .= "'" . @$_SESSION["ccdata"]["order_payment_name"] . "',";
		$q .= "'" . $vmInputFilter->safeSQL( @$d["order_payment_trans_id"] ). "'";
		$q .= ")";
		$db->query($q);
		$db->next_record();

		/**
		* Insert the User Billto & Shipto Info
		*/
		// Bill To Address
		$q = "INSERT INTO `#__{vm}_order_user_info` ";
		$q .= "SELECT '', '$order_id', '".$auth['user_id']."', address_type, address_type_name, company, title, last_name, first_name, middle_name, phone_1, phone_2, fax, address_1, address_2, city, state, country, zip, user_email, extra_field_1, extra_field_2, extra_field_3, extra_field_4, extra_field_5,bank_account_nr,bank_name,bank_sort_code,bank_iban,bank_account_holder,bank_account_type FROM #__{vm}_user_info WHERE user_id='".$auth['user_id']."' AND address_type='BT'";
		$db->query( $q );

		// Ship to Address if applicable
		$q = "INSERT INTO `#__{vm}_order_user_info` ";
		$q .= "SELECT '', '$order_id', '".$auth['user_id']."', address_type, address_type_name, company, title, last_name, first_name, middle_name, phone_1, phone_2, fax, address_1, address_2, city, state, country, zip, user_email, extra_field_1, extra_field_2, extra_field_3, extra_field_4, extra_field_5,bank_account_nr,bank_name,bank_sort_code,bank_iban,bank_account_holder,bank_account_type FROM #__{vm}_user_info WHERE user_id='".$auth['user_id']."' AND user_info_id='".$d['ship_to_info_id']."' AND address_type='ST'";
		$db->query( $q );

		/**
    * Insert all Products from the Cart into order line items; 
    * one row per product in the cart 
    */
		$dboi = new ps_DB;

		for($i = 0; $i < $cart["idx"]; $i++) {

			$r = "SELECT product_id,product_in_stock,product_sales,product_parent_id,product_sku,product_name ";
			$r .= "FROM #__{vm}_product WHERE product_id='".$cart[$i]["product_id"]."'";
			$dboi->query($r);
			$dboi->next_record();

			$product_price_arr = $ps_product->get_adjusted_attribute_price($cart[$i]["product_id"], $cart[$i]["description"]);
			$product_price = $product_price_arr["product_price"];

			if( empty( $_SESSION['product_sess'][$cart[$i]["product_id"]]['tax_rate'] )) {
				$my_taxrate = $ps_product->get_product_taxrate($cart[$i]["product_id"] );
			}
			else {
				$my_taxrate = $_SESSION['product_sess'][$cart[$i]["product_id"]]['tax_rate'];
			}
			// Attribute handling
			$product_parent_id = $dboi->f('product_parent_id');
			$description = '';
			if( $product_parent_id > 0 ) {
				
				$db_atts = $ps_product->attribute_sql( $dboi->f('product_id'), $product_parent_id );
				while( $db_atts->next_record()) {
					$description .=	$db_atts->f('attribute_name').': '.$db_atts->f('attribute_value').'; ';
				}
			}
			
			$description .= $_SESSION['cart'][$i]["description"];
			
			$product_final_price = round( ($product_price *($my_taxrate+1)), 2 );

			$vendor_id = $ps_vendor_id;

			$product_currency = $product_price_arr["product_currency"];

			$q = "INSERT INTO #__{vm}_order_item ";
			$q .= "(order_id, user_info_id, vendor_id, product_id, order_item_sku, order_item_name, ";
			$q .= "product_quantity, product_item_price, product_final_price, ";
			$q .= "order_item_currency, order_status, product_attribute, cdate, mdate) ";
			$q .= "VALUES ('";
			$q .= $order_id . "', '";
			$q .= $d["ship_to_info_id"] . "', '";
			$q .= $vendor_id . "', '";
			$q .= $cart[$i]["product_id"] . "', '";
			$q .= addslashes($dboi->f("product_sku")) . "', '";
			$q .= addslashes($dboi->f("product_name")) . "', '";
			$q .= $cart[$i]["quantity"] . "', '";
			$q .= $product_price . "', '";
			$q .= $product_final_price . "', '";
			$q .= $product_currency . "', ";
			$q .= "'P','";
			// added for advanced attribute storage
			$q .= addslashes( $description ) . "', '";
			// END advanced attribute modifications
			$q .= $timestamp . "','";
			$q .= $timestamp . "'";
			$q .= ")";

			$db->query($q);
			$db->next_record();

			/* Update Stock Level and Product Sales */
			if ($dboi->f("product_in_stock")) {
				$q = "UPDATE #__{vm}_product ";
				$q .= "SET product_in_stock = product_in_stock - ".$cart[$i]["quantity"];
				$q .= " WHERE product_id = '" . $cart[$i]["product_id"]. "'";
				$db->query($q);
				$db->next_record();
			}

			$q = "UPDATE #__{vm}_product ";
			$q .= "SET product_sales= product_sales + ".$cart[$i]["quantity"];
			$q .= " WHERE product_id='".$cart[$i]["product_id"]."'";
			$db->query($q);
			$db->next_record();

		}

		######## BEGIN DOWNLOAD MOD ###############
		if( ENABLE_DOWNLOADS == "1" ) {
			for($i = 0; $i < $cart["idx"]; $i++) {
				$dlnum=0;
				$dl = "SELECT attribute_name,attribute_value ";
				$dl .= "FROM #__{vm}_product_attribute WHERE product_id='".$cart[$i]["product_id"]."'";
				$dl .= " AND attribute_name='download'";
				$db->query($dl);
				$db->next_record();

				if ($db->f("attribute_name") =="download") {

					$str = $order_id;
					$str .=$cart[$i]["product_id"];
					$str .=$dlnum;
					$str .=time();

					$download_id = md5($str);

					$q = "INSERT INTO #__{vm}_product_download ";
					$q .= "(product_id, user_id, order_id, end_date, download_max, download_id, file_name)";
					$q .= " VALUES ('";
					$q .= $cart[$i]["product_id"] . "', '";
					$q .= $auth["user_id"] . "', '";
					$q .= $order_id . "', '";
					$q .= " 0" . "', '";
					$q .= DOWNLOAD_MAX . "', '";
					$q .= $download_id . "', '";
					$q .= $GLOBALS['vmInputFilter']->safeSQL( $db->f("attribute_value")) . "')";
					$db->query($q);
					$db->next_record();
				}
			}
		}
		################## END DOWNLOAD MOD ###########

		if (AFFILIATE_ENABLE == '1') {
			$ps_affiliate->register_sale($order_id);
		}
		// Export the order_id so the checkout complete page can get it
		$d["order_id"] = $order_id;

		// Now as everything else has been done, we can update
		// the Order Status if the Payment Method is
		// "Use Payment Processor", because:
		// Payment Processors return false on any error
		// Only completed payments return true!
		if( $enable_processor == "Y" ) {
			eval( "if( defined(\"".$_PAYMENT->payment_code."_VERIFIED_STATUS\")) {
              \$d['order_status'] = ".$_PAYMENT->payment_code."_VERIFIED_STATUS;
              \$update_order = true;
            }
            else
              \$update_order = false;" );
			if ( $update_order ) {
				require_once(CLASSPATH."ps_order.php");
				$ps_order =& new ps_order();
				$ps_order->order_status_update($d);
			}
		}

		// Send the e-mail confirmation messages
		$this->email_receipt($order_id);

		// Reset the cart (=empty it)
		$ps_cart->reset();

		// Unset the payment_method variables
		$d["payment_method_id"] = "";
		$d["order_payment_number"] = "";
		$d["order_payment_expire"] = "";
		$d["order_payment_name"] = "";
		$d["credit_card_code"] = "";
		// Clear the sensitive Session data
		$_SESSION['ccdata']['order_payment_name']  = "";
		$_SESSION['ccdata']['order_payment_number']  = "";
		$_SESSION['ccdata']['order_payment_expire_month'] = "";
		$_SESSION['ccdata']['order_payment_expire_year'] = "";
		$_SESSION['ccdata']['credit_card_code'] = "";
		$_SESSION['coupon_discount'] = "";
		$_SESSION['coupon_id'] = "";
		$_SESSION['coupon_redeemed'] = false;
		
		$_POST["payment_method_id"] = "";
		$_POST["order_payment_number"] = "";
		$_POST["order_payment_expire"] = "";
		$_POST["order_payment_name"] = "";
		
		return True;
	}

	/**************************************************************************
	** name: get_order_number()
	** created by: gday
	** description:  Create an order number using the session id, session
	**               name, and the current unix timestamp.
	** parameters:
	** returns: unique order_number
	***************************************************************************/
	function get_order_number() {
		global $sess;

		/* Generated a unique order number */

		$str = session_id();
		$str .= session_name();
		$str .= (string)time();

		$order_number = md5($str);

		return($order_number);
	}
	
	/**************************************************************************
	** name: calc_order_subtotal()
	** created by: gday
	** description:  Calculate the order subtotal for the current order.
	**               Does not include tax or shipping charges.
	** parameters: $d
	** returns: sub total for this order
	***************************************************************************/
	function calc_order_subtotal( &$d ) {
		global $order_tax_details;
		
		$order_tax_details = array();
		$d['order_subtotal_withtax'] = 0;
		$d['payment_discount'] = 0;
		$auth = $_SESSION['auth'];
		$cart = $_SESSION['cart'];
		$order_subtotal = 0;

		require_once(CLASSPATH.'ps_product.php');
		$ps_product= new ps_product;

		for($i = 0; $i < $cart["idx"]; $i++) {
			$my_taxrate = $ps_product->get_product_taxrate($cart[$i]["product_id"] );
			$price = $ps_product->get_adjusted_attribute_price($cart[$i]["product_id"], $cart[$i]["description"]);
			$product_price = $price["product_price"];
			if( $auth["show_price_including_tax"] == 1 ) {
				$product_price = round( ($product_price *($my_taxrate+1)), 2 );
				$product_price *= $cart[$i]["quantity"];
				$d['order_subtotal_withtax'] += $product_price;
				$product_price = $product_price /($my_taxrate+1);
				$order_subtotal += $product_price;
			}
			else {
				$order_subtotal += $product_price * $cart[$i]["quantity"];
				
				$product_price = round( ($product_price *($my_taxrate+1)), 2 );
				$product_price *= $cart[$i]["quantity"];
				$d['order_subtotal_withtax'] += $product_price;
				$product_price = $product_price /($my_taxrate+1);
			}
			if( MULTIPLE_TAXRATES_ENABLE ) {
				// Calculate the amounts for each tax rate
				if( !isset( $order_tax_details[$my_taxrate] )) {
					$order_tax_details[$my_taxrate] = 0;
				}
				$order_tax_details[$my_taxrate] += $price["product_price"]*$my_taxrate*$cart[$i]["quantity"];
			}
		}

		return($order_subtotal);
	}


	/**
	 * Calculates the taxable order subtotal for the order.
	 * If an item has no weight, it is non taxable.
	 * @author Chris Coleman
	 * @param array $d
	 * @return float Subtotal
	 */
	function calc_order_taxable($d) {
		$auth = $_SESSION['auth'];
		$cart = $_SESSION['cart'];

		$subtotal = 0.0;
		
		require_once(CLASSPATH.'ps_product.php');
		$ps_product= new ps_product;
		require_once(CLASSPATH.'ps_shipping_method.php');

		$db = new ps_DB;

		for($i = 0; $i < $cart["idx"]; $i++) {
			$price = $ps_product->get_adjusted_attribute_price($cart[$i]["product_id"], $cart[$i]["description"]);
			$product_price = $price["product_price"];
			$item_weight = ps_shipping_method::get_weight($cart[$i]["product_id"]) * $cart[$i]['quantity'];

			if ($item_weight != 0 or TAX_VIRTUAL=='1') {
				$subtotal += $product_price * $cart[$i]["quantity"];
			}
		}
		return($subtotal);
	}
	
	/**
	 * Calculate the tax charges for the current order.
	 * You can switch the way, taxes are calculated:
	 * either based on the VENDOR address,
	 * or based on the ship-to address.
	 * ! Creates the global $order_tax_details
	 *
	 * @param float $order_taxable
	 * @param array $d
	 * @return float
	 */
	function calc_order_tax($order_taxable, $d) {
		global $order_tax_details, $discount_factor;
		$auth = $_SESSION['auth'];
		$ps_vendor_id = $_SESSION["ps_vendor_id"];
		$db = new ps_DB;
		
		require_once(CLASSPATH.'ps_tax.php');
		$ps_tax = new ps_tax;
		
		$discount_factor = 1;
		
		// Shipping address based TAX
		if (TAX_MODE == '0') {
			$q = "SELECT state, country FROM #__{vm}_user_info ";
			$q .= "WHERE user_info_id='".@$_REQUEST["ship_to_info_id"] . "'";
			$db->query($q);
			$db->next_record();
			$state = $db->f("state");
			$country = $db->f("country");
			$q = "SELECT * FROM #__{vm}_tax_rate WHERE tax_country='$country' ";
			if( $state ) {
				$q .= "AND tax_state='$state'";
			}
			$db->query($q);
			if ($db->next_record()) {
				$rate = $order_taxable * floatval( $db->f("tax_rate") );
				if (empty($rate)) {
					$order_tax = 0.0;
				}
				else {
					$order_tax = $rate;
				}
			}
			else {
				$order_tax = 0.0;
			}
			$order_tax_details[$db->f('tax_rate')] = $order_tax;
		}
		// Store Owner Address based TAX
		elseif (TAX_MODE == '1') {

			if (MULTIPLE_TAXRATES_ENABLE != '1' ) {
				$q = "SELECT `tax_rate` FROM #__{vm}_vendor, #__{vm}_tax_rate ";
				$q .= "WHERE tax_country=vendor_country ";
				$q .= "AND #__{vm}_vendor.vendor_id='1' ";
				$q .= "ORDER BY `tax_rate` DESC";
				$db->query($q);
				if ($db->next_record()) {
					$tax_rate = $db->f("tax_rate");
					$rate = $order_taxable * $tax_rate;
					
					if (empty($rate)) {
						$order_tax = 0.0;
					}
					else {
						$order_tax = $rate;
					}
				}
				else {
					$order_tax = 0.0;
				}
				$order_tax_details[$db->f('tax_rate')] = $order_tax;
			}
			else {
				// Calculate the Tax with a tax rate for every product
				$cart = $_SESSION['cart'];
				$order_tax = 0.0;
				$total = 0.0;
				if( (!empty( $_SESSION['coupon_discount'] ) || !empty( $d['payment_discount'] ))
					&& PAYMENT_DISCOUNT_BEFORE == '1' ) {
					// We need to recalculate the tax details when the discounts are applied
					// BEFORE taxes - because they affect the product subtotals then
					$order_tax_details = array();
				}
				require_once(CLASSPATH.'ps_product.php');
				$ps_product= new ps_product;
				require_once(CLASSPATH.'ps_shipping_method.php');

				for($i = 0; $i < $cart["idx"]; $i++) {
					$item_weight = ps_shipping_method::get_weight($cart[$i]["product_id"]) * $cart[$i]['quantity'];

					if ($item_weight !=0 or TAX_VIRTUAL) {
						$price = $ps_product->get_adjusted_attribute_price($cart[$i]["product_id"], $cart[$i]["description"]);
						$tax_rate = $ps_product->get_product_taxrate($cart[$i]["product_id"]);
						
						if( (!empty( $_SESSION['coupon_discount'] ) || !empty( $d['payment_discount'] ))
							&& PAYMENT_DISCOUNT_BEFORE == '1' ) {
							// Reduce the product subtotals by the factor the complete subtotal is reduced/raised by the discounts
							$factor = (100 * (@$_SESSION['coupon_discount'] + @$d['payment_discount'])) / $this->_subtotal;
							$price["product_price"] = $price["product_price"] - ($factor * $price["product_price"] / 100);
							@$order_tax_details[$tax_rate] += $price["product_price"] * $tax_rate * $cart[$i]["quantity"];
						}
						
						$order_tax += $price["product_price"] * $tax_rate * $cart[$i]["quantity"];
						$total += $price["product_price"] * $cart[$i]["quantity"];
					}
				}

				if( (!empty( $_SESSION['coupon_discount'] ) || !empty( $d['payment_discount'] ))
					&& PAYMENT_DISCOUNT_BEFORE != '1' ) {
						
					// Here we need to re-calculate the Discount
					// because we assume the Discount is "including Tax"
					$discounted_total = $d['order_subtotal_withtax'] - $_SESSION['coupon_discount'] - $d['payment_discount'];
					
					if( $discounted_total != $d['order_subtotal_withtax'] ) {
						$discount_factor = $discounted_total / $d['order_subtotal_withtax'];
						
						foreach( $order_tax_details as $rate => $value ) {
							$order_tax_details[$rate] = $value * $discount_factor;
						}
					}
					
				}
				if( $this->_SHIPPING ) {
					$taxrate = $this->_SHIPPING->get_tax_rate();
					if( $taxrate ) {
						$rate = $this->_SHIPPING->get_rate( $d );
						if( $auth["show_price_including_tax"] == 1 ) {
							@$order_tax_details[$taxrate] += $rate - ($rate / ($taxrate+1));
						}
						else {
							@$order_tax_details[$taxrate] += $rate * $taxrate;
						}
					}
				}
			}

		}
		return( round( $order_tax, 2 ) );
	}
  
	/**************************************************************************
	** name: calc_order_shipping()
	** created by: soeren
	** description:  Get the Shipping costs WITHOUT TAX
	** parameters: $d,
	** returns: a decimal number, excluding taxes
	***************************************************************************/
	function calc_order_shipping( &$d ) {

		$auth = $_SESSION['auth'];

		$shipping_total = $this->_SHIPPING->get_rate( $d );
		$shipping_taxrate = $this->_SHIPPING->get_tax_rate();

		// When the Shipping rate is shown including Tax
		// we have to extract the Tax from the Shipping Total
		// before returning the value
		if( $auth["show_price_including_tax"] == 1 ) {
			$d['shipping_tax'] = $shipping_total - ($shipping_total / ($shipping_taxrate+1));
			$d['shipping_total'] = $shipping_total - $d['shipping_tax'];
		}
		else {
			$d['shipping_tax'] = $shipping_total * $shipping_taxrate;
			$d['shipping_total'] = $shipping_total;
		}

		return $d['shipping_total'];
	}




	/**************************************************************************
	** name: calc_order_shipping_tax()
	** created by: Soeren
	** description:  Calculate the tax for the shipping of the current order
	** Assumes that the function calc_order_shipping has been called before
	** parameters: $d
	** returns: Tax for the shipping of this order
	***************************************************************************/
	function calc_order_shipping_tax($d) {

		return $d['shipping_tax'];

	}

	/**************************************************************************
	** name: get_vendor_currency()
	** created by: gday
	** description:  Get the currency type used by the $vendor_id
	** parameters: $vendor_id - vendor id to return currency type
	** returns: Currency type for this vendor
	***************************************************************************/
	function get_vendor_currency($vendor_id) {
		$db = new ps_DB;

		$q = "SELECT vendor_currency FROM #__{vm}_vendor WHERE vendor_id='$vendor_id'";

		$db->query($q);
		$db->next_record();

		$currency = $db->f("vendor_currency");

		return($currency);
	}


	/**************************************************************************
	** name: get_payment_discount()
	** created by: soeren
	** description:  Get the discount for the selected payment
	** parameters: $payment_method_id
	** returns: Discount as a decimal if found
	**          0 if nothing is found
	***************************************************************************/
	function get_payment_discount($payment_method_id, $subtotal) {

		//MOD ei
		// There is a special payment method, which fee is depend on subtotal
		// it is a type of cash on delivery

		require_once(CLASSPATH.'ps_payment_method.php');
		$ps_payment_method = new ps_payment_method;

		$payment_class = $ps_payment_method->get_field($payment_method_id, "payment_class");

		// Check to see if Payment Class File exists
		if (file_exists(CLASSPATH . "payment/$payment_class.php") ) {

			require_once( CLASSPATH. "payment/$payment_class.php" );
			eval( "\$_PAYMENT = new $payment_class();" );

			if(is_callable(array($payment_class, 'get_payment_rate'))) {
				return $_PAYMENT->get_payment_rate($subtotal);
			}
		}
		//End of MOD ei

		$discount = $ps_payment_method->get_field($payment_method_id, "payment_method_discount");

		if (!empty($discount)) {
			return(floatval($discount));
		}
		else {
			return(0);
		}
	}

	/**************************************************************************
	** name: email_receipt()
	** created by: gday
	** description:  Create a receipt for the current order and email it to
	**               the customer and the vendor.
	** parameters: $order_id - Order ID for which to create the email receipts
	** returns:  True - receipt created and emailed
	**          False - error occured
	***************************************************************************/
	function email_receipt($order_id) {
		global $sess, $ps_product, $VM_LANG, $CURRENCY_DISPLAY, $vmLogger, $database, 
		$mosConfig_absolute_path, $mosConfig_live_site, $mosConfig_mailfrom,
		$mosConfig_fromname, $mosConfig_smtpauth, $mosConfig_mailer, $mosConfig_lang,
		$mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtphost;

		$ps_vendor_id = $_SESSION["ps_vendor_id"];
		$auth = $_SESSION["auth"];

		require_once(CLASSPATH.'ps_product.php');
		$ps_product = new ps_product;

		// Connect to database and gather appropriate order information
		$db = new ps_DB;
		$q  = "SELECT * FROM #__{vm}_orders WHERE order_id='$order_id'";
		$db->query($q);
		$db->next_record();
		$user_id = $db->f("user_id");
		$customer_note = $db->f("customer_note");

		$dbbt = new ps_DB;
		$dbst = new ps_DB;

		$qt = "SELECT * FROM #__{vm}_user_info WHERE user_id='".$user_id."' AND address_type='BT'";
		$dbbt->query($qt);
		$dbbt->next_record();

		$qt = "SELECT * FROM #__{vm}_user_info WHERE user_info_id='". $db->f("user_info_id") . "'";
		$dbst->query($qt);
		$dbst->next_record();

		$dbv = new ps_DB;
		$qt = "SELECT * from #__{vm}_vendor ";
		/* Need to decide on vendor_id <=> order relationship */
		$qt .= "WHERE vendor_id = '".$_SESSION['ps_vendor_id']."'";
		$dbv->query($qt);
		$dbv->next_record();

		$dboi = new ps_DB;
		$q_oi = "SELECT * FROM #__{vm}_product, #__{vm}_order_item, #__{vm}_orders ";
		$q_oi .= "WHERE #__{vm}_product.product_id=#__{vm}_order_item.product_id ";
		$q_oi .= "AND #__{vm}_order_item.order_id='$order_id' ";
		$q_oi .= "AND #__{vm}_orders.order_id=#__{vm}_order_item.order_id";
		$dboi->query($q_oi);

		$db_payment = new ps_DB;
		$q  = "SELECT op.payment_method_id, payment_method_name FROM #__{vm}_order_payment as op, #__{vm}_payment_method as pm
              WHERE order_id='$order_id' AND op.payment_method_id=pm.payment_method_id";
		$db_payment->query($q);
		$db_payment->next_record();

		if ($auth["show_price_including_tax"] == 1) {

			$order_shipping = $db->f("order_shipping");
			$order_shipping += $db->f("order_shipping_tax");
			$order_shipping_tax = 0;
			$order_tax = $db->f("order_tax") + $db->f("order_shipping_tax");
		}
		else {

			$order_shipping = $db->f("order_shipping");
			$order_shipping_tax = $db->f("order_shipping_tax");
			$order_tax = $db->f("order_tax");
		}
		$order_total = $db->f("order_total");
		$order_discount = $db->f("order_discount");
		$coupon_discount = $db->f("coupon_discount");

		// Email Addresses for shopper and vendor
		// **************************************
		$shopper_email = $dbbt->f("user_email");
		$shopper_name = $dbbt->f("first_name")." ".$dbbt->f("last_name");

		$from_email = $dbv->f("contact_email");

		$shopper_subject = $dbv->f("vendor_name") . " ".$VM_LANG->_PHPSHOP_ORDER_PRINT_PO_LBL." - " . $db->f("order_id");
		$vendor_subject = $dbv->f("vendor_name") . " ".$VM_LANG->_PHPSHOP_ORDER_PRINT_PO_LBL." - " . $db->f("order_id");

		$shopper_order_link = $sess->url( SECUREURL ."index.php?page=account.order_details&order_id=$order_id" );
		$vendor_order_link = $sess->url( SECUREURL ."index.php?page=order.order_print&order_id=$order_id&pshop_mode=admin" );

		/**
		 * Prepare the payment information, including Credit Card information when not empty
		 */
		$payment_info_details = $db_payment->f("payment_method_name");
		if( !empty( $_SESSION['ccdata']['order_payment_name'] )
			&& !empty($_SESSION['ccdata']['order_payment_number'])) {
	  		$payment_info_details .= '<br />'.$VM_LANG->_PHPSHOP_CHECKOUT_CONF_PAYINFO_NAMECARD.': '.$_SESSION['ccdata']['order_payment_name'].'<br />';
	  		$payment_info_details .= $VM_LANG->_PHPSHOP_CHECKOUT_CONF_PAYINFO_CCNUM.': '.$this->asterisk_pad($_SESSION['ccdata']['order_payment_number'], 4 ).'<br />';
	  		$payment_info_details .= $VM_LANG->_PHPSHOP_CHECKOUT_CONF_PAYINFO_EXDATE.': '.$_SESSION['ccdata']['order_payment_expire_month'].' / '.$_SESSION['ccdata']['order_payment_expire_year'].'<br />';
	  		if( !empty($_SESSION['ccdata']['credit_card_code'])) {
	  			$payment_info_details .= 'CVV code: '.$_SESSION['ccdata']['credit_card_code'].'<br />';
	  		}
		}
		// Convert HTML into Text
		$payment_info_details_text = str_replace( '<br />', "\n", $payment_info_details );
		
		// Get the Shipping Details
		$shipping_arr = explode("|", @urldecode($_REQUEST['shipping_rate_id']) );
		
		// Headers and Footers
		// ******************************
		// Shopper Header
		$shopper_header = $VM_LANG->_PHPSHOP_CHECKOUT_EMAIL_SHOPPER_HEADER1."\n";
		
		// Get the legal information about the returns/order cancellation policy
		if( @VM_ONCHECKOUT_SHOW_LEGALINFO == '1' ) {
			$article = intval(@VM_ONCHECKOUT_LEGALINFO_LINK);
			if( $article > 0 ) {
				$content = new mosContent( $database );
				$content->load( $article );
				if( $content->introtext != '' ) {
					$legal_info_title = $content->title;
					$legal_info_text = strip_tags( str_replace( '<br />', "\n", $content->introtext ));
					$legal_info_html = $content->introtext;
				}
			}
		}
		
		//Shopper Footer
		$shopper_footer = "\n\n".$VM_LANG->_PHPSHOP_CHECKOUT_EMAIL_SHOPPER_HEADER2."\n";
		$shopper_footer .= "\n\n".$VM_LANG->_PHPSHOP_CHECKOUT_EMAIL_SHOPPER_HEADER5."\n";
		$shopper_footer .= $shopper_order_link;
		$shopper_footer .= "\n\n".$VM_LANG->_PHPSHOP_CHECKOUT_EMAIL_SHOPPER_HEADER3."\n";
		$shopper_footer .= "Email: " . $from_email;
		// New in version 1.0.5
		if( @VM_ONCHECKOUT_SHOW_LEGALINFO == '1' && !empty( $legal_info_title )) {
			$shopper_footer .= "\n\n____________________________________________\n";
			$shopper_footer .= $legal_info_title."\n";
			$shopper_footer .= $legal_info_text."\n";
		}
		
		$shopper_footer_html = "<br /><br />".$VM_LANG->_PHPSHOP_CHECKOUT_EMAIL_SHOPPER_HEADER2."<br />";
		$shopper_footer_html .= "<br /><a title=\"".$VM_LANG->_PHPSHOP_CHECKOUT_EMAIL_SHOPPER_HEADER5."\" href=\"$shopper_order_link\">"
		. $VM_LANG->_PHPSHOP_CHECKOUT_EMAIL_SHOPPER_HEADER5."</a>";
		$shopper_footer_html .= "<br /><br />".$VM_LANG->_PHPSHOP_CHECKOUT_EMAIL_SHOPPER_HEADER3."<br />";
		$shopper_footer_html .= _CMN_EMAIL.": <a href=\"mailto:" . $from_email."\">".$from_email."</a>";
		// New in version 1.0.5
		if( @VM_ONCHECKOUT_SHOW_LEGALINFO == '1' && !empty( $legal_info_title )) {
			$shopper_footer_html .= "<br /><br />____________________________________________<br />";
			$shopper_footer_html .= '<h5>'.$legal_info_title.'</h5>';
			$shopper_footer_html .= $legal_info_html.'<br />';
		}
		
		// Vendor Header
		$vendor_header = $VM_LANG->_PHPSHOP_CHECKOUT_EMAIL_SHOPPER_HEADER4."\n";

		// Vendor Footer
		$vendor_footer = "\n\n".$VM_LANG->_PHPSHOP_CHECKOUT_EMAIL_SHOPPER_HEADER5."\n";
		$vendor_footer .= $vendor_order_link;

		$vendor_footer_html = "<br /><br /><a title=\"".$VM_LANG->_PHPSHOP_CHECKOUT_EMAIL_SHOPPER_HEADER5."\" href=\"$vendor_order_link\">"
		. $VM_LANG->_PHPSHOP_CHECKOUT_EMAIL_SHOPPER_HEADER5."</a>";

		$vendor_email = $from_email;

		/////////////////////////////////////
		// set up text mail
		//

		// Main Email Message Purchase Order
		// *********************************
		$shopper_message  = "\n".$VM_LANG->_PHPSHOP_ORDER_PRINT_PO_LBL."\n";
		$shopper_message .= "------------------------------------------------------------------------\n";
		$shopper_message .= $VM_LANG->_PHPSHOP_ORDER_PRINT_PO_NUMBER.": " . $db->f("order_id") . "\n";
		$shopper_message .= $VM_LANG->_PHPSHOP_ORDER_PRINT_PO_DATE.":   ";
		$shopper_message .= date("d-M-Y:H:i", $db->f("cdate")) . "\n";
		$shopper_message .= $VM_LANG->_PHPSHOP_ORDER_PRINT_PO_STATUS.": ";
		
		$dbos = new ps_DB;

		$q = "SELECT order_status_id, order_status_name FROM `#__{vm}_order_status` WHERE order_status_code='".$db->f("order_status")."'";
		$dbos->query($q);
		$dbos->next_record();
		
		$shopper_message .= $dbos->f("order_status_name")."\n\n";
		$order_status = $dbos->f("order_status_name");
		
		$shopper_message .= $VM_LANG->_PHPSHOP_ORDER_PRINT_CUST_INFO_LBL."\n";
		$shopper_message .= "--------------------\n\n";
		$shopper_message .= $VM_LANG->_PHPSHOP_ORDER_PRINT_BILL_TO_LBL."\n";
		$shopper_message .= "-------\n\n";
		$shopper_message .= "     ".$VM_LANG->_PHPSHOP_ORDER_PRINT_COMPANY.":    ";
		$shopper_message .= $dbbt->f("company") . "\n";
		$shopper_message .= "     ".$VM_LANG->_PHPSHOP_ORDER_PRINT_NAME.":       ";
		if ($dbbt->f("title")) {
			$shopper_message .= $dbbt->f("title") . " ";
		}
		$shopper_message .= $dbbt->f("first_name") . " ";
		if ($dbbt->f("middle_name")) {
			$shopper_message .= $dbbt->f("middle_name") . " ";
		}
		$shopper_message .= $dbbt->f("last_name") . "\n";
		$shopper_message .= "     ".$VM_LANG->_PHPSHOP_ORDER_PRINT_ADDRESS_1.":   ";
		$shopper_message .= $dbbt->f("address_1") . "\n";
		$shopper_message .= "     ".$VM_LANG->_PHPSHOP_ORDER_PRINT_ADDRESS_2.":   ";
		$shopper_message .= $dbbt->f("address_2") . "\n";
		$shopper_message .= "     ".$VM_LANG->_PHPSHOP_ORDER_PRINT_CITY.":       ";
		$shopper_message .= $dbbt->f("city") . "\n";
		$shopper_message .= "     ".$VM_LANG->_PHPSHOP_ORDER_PRINT_STATE.":      ";
		$shopper_message .= $dbbt->f("state") . "\n";
		$shopper_message .= "     ".$VM_LANG->_PHPSHOP_ORDER_PRINT_ZIP.":        ";
		$shopper_message .= $dbbt->f("zip") . "\n";
		$shopper_message .= "     ".$VM_LANG->_PHPSHOP_ORDER_PRINT_COUNTRY.":    ";
		$shopper_message .= $dbbt->f("country") . "\n";
		$shopper_message .= "     ".$VM_LANG->_PHPSHOP_ORDER_PRINT_PHONE.":      ";
		$shopper_message .= $dbbt->f("phone_1") . "\n";
		$shopper_message .= "     ".$VM_LANG->_PHPSHOP_ORDER_PRINT_FAX.":        ";
		$shopper_message .= $dbbt->f("fax") . "\n\n";

		$shopper_message .= $VM_LANG->_PHPSHOP_ORDER_PRINT_SHIP_TO_LBL."\n";
		$shopper_message .= "-------\n\n";
		$shopper_message .= "     ".$VM_LANG->_PHPSHOP_ORDER_PRINT_COMPANY.":    ";
		$shopper_message .= $dbst->f("company") . "\n";
		$shopper_message .= "     ".$VM_LANG->_PHPSHOP_ORDER_PRINT_NAME.":       ";
		$shopper_message .= $dbbt->f("title") . " ";
		$shopper_message .= $dbst->f("first_name") . " ";
		$shopper_message .= $dbst->f("middle_name") . " ";
		$shopper_message .= $dbst->f("last_name") . "\n";
		$shopper_message .= "     ".$VM_LANG->_PHPSHOP_ORDER_PRINT_ADDRESS_1.":   ";
		$shopper_message .= $dbst->f("address_1") . "\n";
		$shopper_message .= "     ".$VM_LANG->_PHPSHOP_ORDER_PRINT_ADDRESS_2.":   ";
		$shopper_message .= $dbst->f("address_2") . "\n";
		$shopper_message .= "     ".$VM_LANG->_PHPSHOP_ORDER_PRINT_CITY.":       ";
		$shopper_message .= $dbst->f("city") . "\n";
		$shopper_message .= "     ".$VM_LANG->_PHPSHOP_ORDER_PRINT_STATE.":      ";
		$shopper_message .= $dbst->f("state") . "\n";
		$shopper_message .= "     ".$VM_LANG->_PHPSHOP_ORDER_PRINT_ZIP.":        ";
		$shopper_message .= $dbst->f("zip") . "\n";
		$shopper_message .= "     ".$VM_LANG->_PHPSHOP_ORDER_PRINT_COUNTRY.":    ";
		$shopper_message .= $dbst->f("country") . "\n";
		$shopper_message .= "     ".$VM_LANG->_PHPSHOP_ORDER_PRINT_PHONE.":      ";
		$shopper_message .= $dbst->f("phone_1") . "\n";
		$shopper_message .= "     ".$VM_LANG->_PHPSHOP_ORDER_PRINT_FAX.":        ";
		$shopper_message .= $dbst->f("fax") . "\n\n";

		$shopper_message .= $VM_LANG->_PHPSHOP_ORDER_PRINT_ITEMS_LBL."\n";
		$shopper_message .= "-----------";
		$sub_total = 0.00;
		while($dboi->next_record()) {
			$shopper_message .= "\n\n";
			$shopper_message .= $VM_LANG->_PHPSHOP_PRODUCT."  = ";
			if ($dboi->f("product_parent_id")) {
				$shopper_message .= $dboi->f("order_item_name") . "\n";
				$shopper_message .= "SERVICE  = ";
			}
			$shopper_message .= $dboi->f("product_name") . "; "
								.$ps_product->getDescriptionWithTax( $dboi->f("product_attribute"),$dboi->f("product_id")) ."\n";
			$shopper_message .= $VM_LANG->_PHPSHOP_ORDER_PRINT_QUANTITY." = ";
			$shopper_message .= $dboi->f("product_quantity") . "\n";
			$shopper_message .= $VM_LANG->_PHPSHOP_ORDER_PRINT_SKU."      = ";
			$shopper_message .= $dboi->f("order_item_sku") . "\n";

			$shopper_message .= $VM_LANG->_PHPSHOP_ORDER_PRINT_PRICE."    = ";
			if ($auth["show_price_including_tax"] == 1) {
				$sub_total += ($dboi->f("product_quantity") * $dboi->f("product_final_price"));
				$shopper_message .= $CURRENCY_DISPLAY->getFullValue($dboi->f("product_final_price"));
			} else {
				$sub_total += ($dboi->f("product_quantity") * $dboi->f("product_final_price"));
				$shopper_message .= $CURRENCY_DISPLAY->getFullValue($dboi->f("product_item_price"));
			}
		}

		$shopper_message .= "\n\n";

		$shopper_message .= $VM_LANG->_PHPSHOP_ORDER_PRINT_SUBTOTAL." = ";
		$shopper_message .= $CURRENCY_DISPLAY->getFullValue($sub_total)."\n";

		if ( PAYMENT_DISCOUNT_BEFORE == '1') {
			if( !empty($order_discount)) {
				if ($order_discount > 0) {
					$shopper_message .= $VM_LANG->_PHPSHOP_PAYMENT_METHOD_LIST_DISCOUNT." = ";
					$shopper_message .= "- ".$CURRENCY_DISPLAY->getFullValue(abs($order_discount)) . "\n";
				} else {
					$shopper_message .= $VM_LANG->_PHPSHOP_FEE." = ";
					$shopper_message .= "+ ".$CURRENCY_DISPLAY->getFullValue(abs($order_discount)) . "\n";
				}
			}
			if( !empty($coupon_discount)) {
				/* following 2 lines added by Erich for coupon hack */
				$shopper_message .= $VM_LANG->_PHPSHOP_COUPON_DISCOUNT . ": ";
				$shopper_message .= $CURRENCY_DISPLAY->getFullValue($coupon_discount) . "\n";
			}
		}

		if ($auth["show_price_including_tax"] != 1) {
			$shopper_message .= $VM_LANG->_PHPSHOP_ORDER_PRINT_TOTAL_TAX."      = ";
			$shopper_message .= $CURRENCY_DISPLAY->getFullValue($order_tax) . "\n";
		}
		$shopper_message .= $VM_LANG->_PHPSHOP_ORDER_PRINT_SHIPPING." = ";
		$shopper_message .= $CURRENCY_DISPLAY->getFullValue($order_shipping) . "\n";
		if( !empty($order_shipping_tax)) {
			$shopper_message .= $VM_LANG->_PHPSHOP_ORDER_PRINT_SHIPPING_TAX."   = ";
			$shopper_message .= $CURRENCY_DISPLAY->getFullValue($order_shipping_tax);
		}
		$shopper_message .= "\n\n";
		if ( PAYMENT_DISCOUNT_BEFORE != '1') {
			if( !empty($order_discount)) {
				if ($order_discount > 0) {
					$shopper_message .= $VM_LANG->_PHPSHOP_PAYMENT_METHOD_LIST_DISCOUNT." = ";
					$shopper_message .= "- ".$CURRENCY_DISPLAY->getFullValue(abs($order_discount)) . "\n";
				} else {
					$shopper_message .= $VM_LANG->_PHPSHOP_FEE." = ";
					$shopper_message .= "+ ".$CURRENCY_DISPLAY->getFullValue(abs($order_discount)) . "\n";
				}
			}
			if( !empty($coupon_discount)) {
				/* following 2 lines added by Erich for coupon hack */
				$shopper_message .= $VM_LANG->_PHPSHOP_COUPON_DISCOUNT . ": ";
				$shopper_message .= $CURRENCY_DISPLAY->getFullValue($coupon_discount) . "\n";
			}
		}
		$shopper_message .= $VM_LANG->_PHPSHOP_ORDER_PRINT_TOTAL."    = ";
		$shopper_message .= $CURRENCY_DISPLAY->getFullValue($order_total);

		if ($auth["show_price_including_tax"] == 1) {
			$shopper_message .= "\n---------------";
			$shopper_message .= "\n";
			$shopper_message .= $VM_LANG->_PHPSHOP_ORDER_PRINT_TOTAL_TAX."      = ";
			$shopper_message .= $CURRENCY_DISPLAY->getFullValue($order_tax) . "\n";
		}
		if( $db->f('order_tax_details') ) {
			$shopper_message .= str_replace( '<br />', "\n", ps_checkout::show_tax_details( $db->f('order_tax_details') ) );
		}
		// Payment Details
		$shopper_message .= "\n\n------------------------------------------------------------------------\n";
		$shopper_message .= $payment_info_details_text;
		
		// Shipping Details
		if( $this->_SHIPPING) {
			$shopper_message .= "\n\n------------------------------------------------------------------------\n";
			$shopper_message .= $VM_LANG->_PHPSHOP_ORDER_PRINT_SHIPPING_LBL.":\n";
			$shopper_message .= $shipping_arr[1]." (".$shipping_arr[2].")";
		}
		// Customer Note
		$shopper_message .= "\n\n------------------------------------------------------------------------\n";
		$shopper_message .= "\n".$VM_LANG->_PHPSHOP_ORDER_PRINT_CUSTOMER_NOTE."\n";
		$shopper_message .= "---------------";
		$shopper_message .= "\n";
		if( !empty( $customer_note )) {
			$shopper_message .= $customer_note."\n";
		}
		else {
			$shopper_message .= " ./. \n";
		}
		$shopper_message .= "------------------------------------------------------------------------\n";
		
		// Decode things like &euro; => €
		$shopper_message = vmHtmlEntityDecode( $shopper_message );
		
		// End of Purchase Order
		// *********************

		//
		//END: set up text mail
		/////////////////////////////////////
		// Send text email
		//
		if (ORDER_MAIL_HTML == '0') {

			$msg = $shopper_header . $shopper_message . $shopper_footer;

			// Mail receipt to the shopper
			vmMail( $from_email, $mosConfig_fromname, $shopper_email, $shopper_subject, $msg, "" );

			$msg = $vendor_header . $shopper_message . $vendor_footer;

			// Mail receipt to the vendor
			vmMail($shopper_email, $mosConfig_fromname, $vendor_email, $vendor_subject,	$msg, "" );

		}

		////////////////////////////
		// set up the HTML email
		//
		elseif (ORDER_MAIL_HTML == '1') {

			$dboi->query($q_oi);

			// CREATE THE LIST WITH ALL ORDER ITEMS
			// TO BE PLACED INTO {phpShopOrderItems}
			$order_items = "";
			$sub_total = 0.00;
			while($dboi->next_record()) {
				$my_qty = $dboi->f("product_quantity");
				$order_items .= "<tr class=\"Stil1\"><td>". $my_qty . "</td>";
				$order_items .= "<td>".$dboi->f("product_name");
				if($dboi->f("product_attribute")) {
					$order_items .= " (".$ps_product->getDescriptionWithTax( $dboi->f("product_attribute"), $dboi->f("product_id")) .")";
				}
				$order_items .= "</td>";
				$order_items .= "<td>".$ps_product->get_field($dboi->f("product_id"), "product_sku") . "</td>";
				if ($auth["show_price_including_tax"] == 1) {
					$price = $dboi->f("product_final_price");
					$my_price = $CURRENCY_DISPLAY->getFullValue($dboi->f("product_final_price"));
				} else {
					$price = $dboi->f("product_item_price");
					$my_price = $CURRENCY_DISPLAY->getFullValue($dboi->f("product_item_price"));
				}
				$order_items .= "<td>".$my_price. "</td>";
				$my_subtotal = $my_qty * $price;
				$sub_total += $my_subtotal;
				$order_items .= "<td>".$CURRENCY_DISPLAY->getFullValue($my_subtotal)."</td></tr>";
			}
			// DISCOUNT HANDLING
			$order_disc1 = $order_disc2 = $order_disc3 ="";

			if ( PAYMENT_DISCOUNT_BEFORE == '1') {
				if ($order_discount > 0 || $order_discount < 0) {
					if ($order_discount > 0) {
						$order_disc1 = "<tr class=\"Stil1\"><td align=\"right\" colspan=\"4\">".$VM_LANG->_PHPSHOP_PAYMENT_METHOD_LIST_DISCOUNT.": </td>";
						$order_disc1 .= "<td>- ".$CURRENCY_DISPLAY->getFullValue(abs($order_discount))."</td>";
					}
					elseif ($order_discount < 0) {
						$order_disc1 = "<tr class=\"Stil1\"><td align=\"right\" colspan=\"4\">".$VM_LANG->_PHPSHOP_FEE.": </td>";
						$order_disc1 .= "<td>+ ".$CURRENCY_DISPLAY->getFullValue(abs($order_discount))."</td></tr>";
					}
				}
				if ($coupon_discount > 0 || $coupon_discount < 0) {
					$order_disc1 .= "<tr class=\"Stil1\"><td align=\"right\" colspan=\"4\">".$VM_LANG->_PHPSHOP_COUPON_DISCOUNT.": </td>";
					if ($coupon_discount > 0)
					$order_disc1 .= "<td>- ".$CURRENCY_DISPLAY->getFullValue(abs($coupon_discount))."</td>";
					elseif ($coupon_discount < 0)
					$order_disc1 .= "<td>+ ".$CURRENCY_DISPLAY->getFullValue(abs($coupon_discount))."</td></tr>";
				}
			}
			elseif ( PAYMENT_DISCOUNT_BEFORE != '1') {
				if ($order_discount > 0) {
					$order_disc2 = "<tr class=\"Stil1\"><td align=\"right\" colspan=\"4\">".$VM_LANG->_PHPSHOP_PAYMENT_METHOD_LIST_DISCOUNT.": </td>";
					$order_disc2 .= "<td>- ".$CURRENCY_DISPLAY->getFullValue(abs($order_discount))."</td>";
				}
				elseif ($order_discount < 0) {
					$order_disc2 = "<tr class=\"Stil1\"><td align=\"right\" colspan=\"4\">".$VM_LANG->_PHPSHOP_FEE.": </td>";
					$order_disc2 .= "<td>+ ".$CURRENCY_DISPLAY->getFullValue(abs($order_discount))."</td></tr>";
				}
				if ($coupon_discount > 0 || $coupon_discount < 0) {
					$order_disc2 .= "<tr class=\"Stil1\"><td align=\"right\" colspan=\"4\">".$VM_LANG->_PHPSHOP_COUPON_DISCOUNT.": </td>";
					if ($coupon_discount > 0)
					$order_disc2 .= "<td>- ".$CURRENCY_DISPLAY->getFullValue(abs($coupon_discount))."</td>";
					elseif ($coupon_discount < 0)
					$order_disc2 .= "<td>+ ".$CURRENCY_DISPLAY->getFullValue(abs($coupon_discount))."</td></tr>";
				}
			}

			// open the HTML file and read it into $html
			if (file_exists(PAGEPATH."templates/order_emails/email_".$mosConfig_lang.".html")) {
				$html_file = fopen(PAGEPATH."templates/order_emails/email_".$mosConfig_lang.".html","r");
			}
			elseif(file_exists(ADMINPATH."email_".$mosConfig_lang.".html")) {
				$html_file = fopen(ADMINPATH."email_".$mosConfig_lang.".html","r");
			}
			else {
				$html_file = fopen(PAGEPATH."templates/order_emails/email_english.html","r");
			}

			$html = "";

			while (!feof($html_file)) {
				$buffer = fgets($html_file, 1024);
				$html .= $buffer;
			}
			fclose ($html_file);

			$v_sn = $dbv->f("vendor_store_name"); $v_ad1 = $dbv->f("vendor_address_1"); $v_ad2 = $dbv->f("vendor_address_2");
			$v_zip = $dbv->f("vendor_zip"); $v_ci = $dbv->f("vendor_city"); $v_st = $dbv->f("vendor_state");
			$v_vfi = "<img src=\"cid:vendor_image\" alt=\"vendor_image\" border=\"0\" />";
			$v_oi = $db->f("order_id");

			// now parse the email template and replace
			// the placeholders with the real data.
			$html = str_replace('{phpShopVendorName}',$v_sn,$html);
			$html = str_replace('{phpShopVendorStreet1}',$v_ad1,$html);
			$html = str_replace('{phpShopVendorStreet2}',$v_ad2,$html);
			$html = str_replace('{phpShopVendorZip}',$v_zip,$html);
			$html = str_replace('{phpShopVendorCity}',$v_ci,$html);
			$html = str_replace('{phpShopVendorState}',$v_st,$html);
			$html = str_replace('{phpShopVendorImage}',$v_vfi,$html);
			$html = str_replace('{phpShopOrderHeader}',$VM_LANG->_PHPSHOP_ORDER_PRINT_PO_LBL,$html);
			$html = str_replace('{phpShopOrderNumber}',$v_oi,$html);
			$html = str_replace('{phpShopOrderDate}',strftime( _DATE_FORMAT_LC, $db->f("cdate")),$html);
			$html = str_replace('{phpShopOrderStatus}', $order_status, $html);

			$html = str_replace('{phpShopBTCompany}', $dbbt->f("company"), $html);
			$html = str_replace('{phpShopBTName}', $dbbt->f("first_name")." ".$dbbt->f("middle_name")." ".$dbbt->f("last_name"), $html);
			$html = str_replace('{phpShopBTStreet1}', $dbbt->f("address_1"), $html);
			$html = str_replace('{phpShopBTStreet2}', $dbbt->f("address_2"), $html);
			$html = str_replace('{phpShopBTCity}', $dbbt->f("city"), $html);
			$html = str_replace('{phpShopBTState}', $dbbt->f("state"), $html);
			$html = str_replace('{phpShopBTZip}', $dbbt->f("zip"), $html);
			$html = str_replace('{phpShopBTCountry}', $dbbt->f("country"), $html);
			$html = str_replace('{phpShopBTPhone}', $dbbt->f("phone_1"), $html);
			$html = str_replace('{phpShopBTFax}', $dbbt->f("fax"), $html);
			$html = str_replace('{phpShopBTEmail}', $dbbt->f("user_email"), $html);

			$html = str_replace('{phpShopSTCompany}', $dbst->f("company"), $html);
			$html = str_replace('{phpShopSTName}', $dbst->f("first_name")." ".$dbst->f("middle_name")." ".$dbst->f("last_name"),$html);
			$html = str_replace('{phpShopSTStreet1}', $dbst->f("address_1"), $html);
			$html = str_replace('{phpShopSTStreet2}', $dbst->f("address_2"), $html);
			$html = str_replace('{phpShopSTCity}', $dbst->f("city"), $html);
			$html = str_replace('{phpShopSTState}', $dbst->f("state"), $html);
			$html = str_replace('{phpShopSTZip}', $dbst->f("zip"), $html);
			$html = str_replace('{phpShopSTCountry}', $dbst->f("country"), $html);
			$html = str_replace('{phpShopSTPhone}', $dbst->f("phone_1"), $html);
			$html = str_replace('{phpShopSTFax}', $dbst->f("fax"), $html);

			$html = str_replace('{phpShopOrderItems}',$order_items,$html);

			$html = str_replace('{phpShopOrderSubtotal}',$CURRENCY_DISPLAY->getFullValue($sub_total),$html);
			$html = str_replace('{phpShopOrderShipping}',$CURRENCY_DISPLAY->getFullValue($order_shipping),$html);
			$html = str_replace('{phpShopOrderTax}',$CURRENCY_DISPLAY->getFullValue($order_tax). ps_checkout::show_tax_details( $db->f('order_tax_details') ), $html );

			$html = str_replace('{phpShopOrderTotal}',$CURRENCY_DISPLAY->getFullValue($order_total),$html);

			$html = str_replace('{phpShopOrderDisc1}',$order_disc1, $html);
			$html = str_replace('{phpShopOrderDisc2}',$order_disc2, $html);
			$html = str_replace('{phpShopOrderDisc3}',$order_disc3, $html);
			$html = str_replace('{phpShopCustomerNote}',nl2br($customer_note), $html);

			$html = str_replace('{PAYMENT_INFO_LBL}', $VM_LANG->_PHPSHOP_ORDER_PRINT_PAYINFO_LBL, $html);

			$html = str_replace('{PAYMENT_INFO_DETAILS}', $payment_info_details, $html);

			$html = str_replace('{SHIPPING_INFO_LBL}', $VM_LANG->_PHPSHOP_ORDER_PRINT_SHIPPING_LBL, $html);
			if( $this->_SHIPPING ) {
				$html = str_replace('{SHIPPING_INFO_DETAILS}', $shipping_arr[1]." (".$shipping_arr[2].")", $html);
			}
			else {
				$html = str_replace('{SHIPPING_INFO_DETAILS}', " ./. ", $html);
			}

			$shopper_html = str_replace('{phpShopOrderHeaderMsg}',$shopper_header, $html);
			$shopper_html = str_replace('{phpShopOrderClosingMsg}',$shopper_footer_html, $shopper_html);
			
			$vendor_html = str_replace('{phpShopOrderHeaderMsg}',$vendor_header, $html);
			$vendor_html = str_replace('{phpShopOrderClosingMsg}',$vendor_footer_html,$vendor_html);
			$html = $shopper_html;

			/*
			* Add the text, html and embedded images.
			* The name of the image should match exactly
			* (case-sensitive) to the name in the html.
			*/
			$shopper_mail_Body = $html;
			$shopper_mail_AltBody = $shopper_header . $shopper_message . $shopper_footer;

			$vendor_mail_Body = $vendor_html;
			$vendor_mail_AltBody = $vendor_header . $shopper_message . $vendor_footer;

			$imagefile = pathinfo($dbv->f("vendor_full_image"));
			$extension = $imagefile['extension'] == "jpg" ? "jpeg" : "jpeg";

			$EmbeddedImages[] = array(	'path' => IMAGEPATH."vendor/".$dbv->f("vendor_full_image"),
								'name' => "vendor_image", 
								'filename' => $dbv->f("vendor_full_image"),
								'encoding' => "base64",
								'mimetype' => "image/".$extension );

			
			$shopper_mail = vmMail( $from_email, $mosConfig_fromname, $shopper_email, $shopper_subject, $shopper_mail_Body, $shopper_mail_AltBody, true, null, null, $EmbeddedImages);

			$vendor_mail = vmMail( $shopper_email, $shopper_name, $vendor_email, $vendor_subject, $vendor_mail_Body, $vendor_mail_AltBody, true, null, null, $EmbeddedImages);

			if ( !$shopper_mail || !$vendor_mail ) {
				
				$vmLogger->debug( 'Something went wrong while sending the order confirmation email to '.$from_email.' and '.$shopper_email );
				return false;
			}
			//
			// END: set up and send the HTML email
			////////////////////////////////////////
		}

		return true;

	} // end of function email_receipt()



	/**
	 * Return $str with all but $display_length at the end as asterisks.
	 * @author gday
	 *
	 * @param string $str The string to mask
	 * @param int $display_length The length at the end of the string that is NOT masked
	 * @param boolean $reversed When true, masks the end. Masks from the beginning at default
	 * @return string The string masked by asteriks
	 */
	function asterisk_pad($str, $display_length, $reversed = false) {

		$total_length = strlen($str);

		if($total_length > $display_length) {
			if( !$reversed) {
				for($i = 0; $i < $total_length - $display_length; $i++) {
					$str[$i] = "*";
				}
			}
			else {
				for($i = $total_length-1; $i >= $total_length - $display_length; $i--) {
					$str[$i] = "*";
				}
			}
		}

		return($str);
	}

	/**************************************************************************
	* function final_info
	* @author soeren
	* @desc Shows all collected Checkout information on the confirmation Screen
	**************************************************************************/
	function final_info() {
		global $VM_LANG, $CURRENCY_DISPLAY, $order_total;
		$db = new ps_DB;
		// Begin with Shipping Address
		if(CHECKOUT_STYLE=='1' || CHECKOUT_STYLE=='2') {

			$db->query("SELECT * FROM #__{vm}_user_info WHERE user_info_id='".strip_tags($_REQUEST['ship_to_info_id'])."'");
			$db->next_record();

			echo "<strong>".$VM_LANG->_PHPSHOP_ADD_SHIPTO_2 . ":</strong>&nbsp;";
			echo $db->f("first_name")." ".$db->f("last_name").", ".$db->f("address_1").", ".$db->f("zip")." ".$db->F("city");
			echo "<br /><br />";
		}

		// Print out the Selected Shipping Method
		if((CHECKOUT_STYLE=='1' || CHECKOUT_STYLE=='3')) {

			echo "<strong>".$VM_LANG->_PHPSHOP_INFO_MSG_SHIPPING_METHOD . ":</strong>&nbsp;";
			$rate_details = explode( "|", urldecode(urldecode($_REQUEST['shipping_rate_id'])) );
			foreach( $rate_details as $k => $v ) {
				if( $k == 3 ) {
					echo $CURRENCY_DISPLAY->getFullValue( $v )."; ";
				} elseif( $k > 0 && $k < 4) {
					echo "$v; ";
				}
			}
			echo "<br /><br />";
		}

		unset( $row );
		if( !isset($order_total) || $order_total > 0.00 ) {
			$payment_method_id = mosGetParam( $_REQUEST, 'payment_method_id' );
			
			$db->query("SELECT payment_method_id, payment_method_name FROM #__{vm}_payment_method WHERE payment_method_id='$payment_method_id'");
			$db->next_record();
			echo "<strong>".$VM_LANG->_PHPSHOP_ORDER_PRINT_PAYMENT_LBL . ":</strong>&nbsp;";
			echo $db->f("payment_method_name");
			echo "<br />";
		}
	}
	/**
	 * Displays the order_tax_details array when it contains
	 * more than one 
	 * @param mixed $details
	 * @return string
	 */
	function show_tax_details( $details ) {
		global $discount_factor, $CURRENCY_DISPLAY, $VM_LANG;
		
		if( !isset( $discount_factor) || !empty($_REQUEST['discount_factor'])) {
			$discount_factor = 1;
		}
		$auth = $_SESSION['auth'];
		if( !is_array( $details )) {
			$details = @unserialize( $details );
			if( !is_array($details)) {
				return false;
			}
		}
		$html = '';
		if( sizeof( $details) > 1 ) {
			$html .= '<br />'.$VM_LANG->_VM_TAXDETAILS_LABEL.':<br />';
			
			foreach ($details as $rate => $value ) {
				if( !$auth['show_price_including_tax']) {
					$value /= $discount_factor;
				}
				$rate = str_replace( '-', $CURRENCY_DISPLAY->decimal, $rate )*100;
				$html .= $CURRENCY_DISPLAY->getFullValue( $value, 5 ).' ('.$rate.'% '.$VM_LANG->_PHPSHOP_CART_TAX.')<br />';
			}
		}
		return $html;
	}
	
	/*
	* @abstract This function is very useful to round totals with definite decimals.
	*
	* @param float   $value
	* @param integer $dec
	* @return float
	*/
	function approx( $value, $dec = 2 ) {
		$value += 0.0;
		$unit  = floor( $value * pow( 10, $dec + 1 ) ) / 10;
		$round = round( $unit );
		return $round / pow( 10, $dec );
	}


}
?>