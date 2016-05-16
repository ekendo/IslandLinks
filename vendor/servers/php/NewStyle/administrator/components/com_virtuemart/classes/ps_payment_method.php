<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: ps_payment_method.php,v 1.10.2.2 2006/03/24 18:34:42 soeren_nb Exp $
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
define('UNKNOWN', 0);
define('MASTERCARD', 1);
define('VISA', 2);
define('AMEX', 3);
define('DINNERS', 4);
define('DISCOVER', 5);
define('ENROUTE', 6);
define('JCB', 7);
define('BANKCARD', 8);
define('SOLO_MAESTRO', 9);
define('SWITCH_MAESTRO', 10);
define('SWITCH_', 11);
define('MAESTRO ', 12);
define('UK_ELECTRON', 13);
define('SWITCHCARD', 14);

define('CC_OK', 0);
define('CC_ECALL', 1);
define('CC_EARG', 2);
define('CC_ETYPE', 3);
define('CC_ENUMBER', 4);
define('CC_EFORMAT', 5);
define('CC_ECANTYPE', 6);

class ps_payment_method extends vmAbstractObject {

  var $classname = "ps_payment_method";
  
  /* CreditCard Validation vars */
  var $number = 0;
  var $type = UNKNOWN;
  var $errno = CC_OK;
  
  /**************************************************************************
  ** name: validate_add()
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/
  function validate_add(&$d) {

    if (!$d["payment_method_name"]) {
       $d["error"] = "Please enter a payment method name.";
       return False;
    }
    if (!$d["payment_method_code"]) {
       $d["error"] = "Please enter a payment method code.";
       return False;
    }
    
    $d['is_creditcard'] = !empty( $d['creditcard']) ? '1' : '0';
        
    if (empty($d['payment_class']))
        $d['payment_class'] = "";
        
    if (empty($d["payment_enabled"])) {
       $d["payment_enabled"] = "N";
    }
    if (empty($d["creditcard"])) {
       $d["accepted_creditcards"] = "";
    }
    else {
        $d["accepted_creditcards"] = "";
        foreach($d['creditcard'] as $num => $creditcard_id)
          $d["accepted_creditcards"] .= $creditcard_id . ",";
    }
    
    $d['payment_extrainfo'] = mosGetParam( $_POST, 'payment_extrainfo', '', _MOS_ALLOWHTML );
	if( !get_magic_quotes_runtime() || !get_magic_quotes_gpc() ) {
		$d['payment_extrainfo'] = addslashes( $d['payment_extrainfo'] );
	}
    
    return true;
  }

  /**************************************************************************
  ** name: validate_update()
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/
  function validate_update(&$d) {

    if (!$d["payment_method_code"]) {
       $d["error"] = "Please enter a payment method code.";
       return False;
    }
	$d['is_creditcard'] = !empty( $d['creditcard']) ? '1' : '0';
        
    if (empty($d['payment_class']))
        $d['payment_class'] = "";
        
    if (empty($d["payment_enabled"])) {
       $d["payment_enabled"] = "N";
    }
    if (empty($d["creditcard"])) {
       $d["accepted_creditcards"] = "";
    }
    else {
        $d["accepted_creditcards"] = "";
        foreach($d['creditcard'] as $num => $creditcard_id)
          $d["accepted_creditcards"] .= $creditcard_id . ",";
    }
    
    if (!$d["payment_method_name"]) {
       $d["error"] = "Please enter a payment method name.";
       return False;
    }

    if (!$d["payment_method_id"]) {
       $d["error"] = "Please select a payment method to update.";
       return False;
    }
    
    $d['payment_extrainfo'] = mosGetParam( $_POST, 'payment_extrainfo', '', _MOS_ALLOWHTML );
	if( !get_magic_quotes_runtime() || !get_magic_quotes_gpc() ) {
		$d['payment_extrainfo'] = addslashes( $d['payment_extrainfo'] );
	}

    return True;
  }
  
  /**************************************************************************
  ** name: validate_delete()
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/
  function validate_delete(&$d) {

    if (!$d["payment_method_id"]) {
       $d["error"] = "Please select a payment method to delete.";
       return False;
    }

      return True;
  }
  
  /**************************************************************************
   * name: add()
   * created by: pablo
   * description: 
   * parameters:
   * returns:
   **************************************************************************/
  function add(&$d) {
    $ps_vendor_id = $_SESSION["ps_vendor_id"];
    $db = new ps_DB;

    if (!$this->validate_add($d)) {
      return False;
    }
    if ( !empty($d["payment_class"]) ) {
        if (include( CLASSPATH."payment/".$d["payment_class"].".php" ))
            eval( "\$_PAYMENT = new ".$d["payment_class"]."();");
    }
    else {
        include( CLASSPATH."payment/ps_payment.php" );
        $_PAYMENT = new ps_payment();
    }
    if( is_callable( array( $_PAYMENT, 'write_configuration'))) {
    	$_PAYMENT->write_configuration( $d );
    }
    
    if (!$d["shopper_group_id"]) {
       $q =  "SELECT * from #__{vm}_shopper_group WHERE ";
       $q .= "`default`='1' ";
       $q .= "AND vendor_id='$ps_vendor_id'";
       $db->query($q);
       $db->next_record();
       $d["shopper_group_id"] = $db->f("shopper_group_id");
    }

        
    $q = "INSERT INTO #__{vm}_payment_method (vendor_id, payment_method_name, payment_class, shopper_group_id, ";
    $q .= "payment_method_discount, payment_method_code, enable_processor, list_order, is_creditcard,payment_enabled, ";
    $q .= "accepted_creditcards, payment_extrainfo) VALUES (";
    $q .= "'$ps_vendor_id',";
    $q .= "'" . $d["payment_method_name"] . "', ";
    $q .= "'" . $d["payment_class"] . "', ";
    $q .= "'" . $d["shopper_group_id"] . "', ";
    $q .= "'" . $d["payment_method_discount"] . "',";
    $q .= "'" . $d["payment_method_code"] . "',";
    $q .= "'" . $d["enable_processor"] . "',";
    $q .= "'" . $d["list_order"] . "',";
    $q .= "'" . $d["is_creditcard"] . "',";
    $q .= "'" . $d["payment_enabled"] . "',";
    $q .= "'" . $d["accepted_creditcards"] . "',";
    $q .= "'" . $d['payment_extrainfo'] . "')";
 
    $db->query($q);

    return True;
    
  }
  
  /**************************************************************************
  ** name: update()
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/
  function update(&$d) {
    $ps_vendor_id = $_SESSION["ps_vendor_id"];

    $db = new ps_DB;
 
    if (!$this->validate_update($d)) {
      return False;
    }
    
    if ( !empty($d["payment_class"]) ) {
        if (include( CLASSPATH."payment/".$d["payment_class"].".php" ))
            eval( "\$_PAYMENT = new ".$d["payment_class"]."();");
    }
    else {
        include( CLASSPATH."payment/ps_payment.php" );
        $_PAYMENT = new ps_payment();
    }
    
    $_PAYMENT->write_configuration( $d );
        
    $q = "UPDATE #__{vm}_payment_method SET ";
    $q .= "payment_method_name='" . $d["payment_method_name"] ."',";
    $q .= "payment_class='" . $d["payment_class"] ."',";
    $q .= "shopper_group_id='" . $d["shopper_group_id"] . "',";
    $q .= "payment_method_discount='" . $d["payment_method_discount"] . "', ";
    $q .= "payment_method_code='" . $d["payment_method_code"] . "', ";
    $q .= "enable_processor='" . $d["enable_processor"] . "', ";
    $q .= "list_order='" . $d["list_order"] . "', ";
    $q .= "is_creditcard='" . $d["is_creditcard"] . "', ";
    $q .= "payment_enabled='" . $d["payment_enabled"] . "', ";
    $q .= "accepted_creditcards='" . $d["accepted_creditcards"] . "', ";
    $q .= "payment_extrainfo='" . $d['payment_extrainfo'] . "' ";
    $q .= "WHERE payment_method_id='".$d["payment_method_id"]."'";
    $q .= " AND vendor_id='" . $ps_vendor_id . "'";
	
    $db->query($q);

    return True;
  }
  
  /**************************************************************************
  ** name: delete()
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/
	/**
	* Controller for Deleting Records.
	*/
	function delete(&$d) {
	
		if (!$this->validate_delete($d)) {
		  return False;
		}
		$record_id = $d["payment_method_id"];
		
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
		$ps_vendor_id = $_SESSION["ps_vendor_id"];
		
		$q = "DELETE from #__{vm}_payment_method WHERE payment_method_id='$record_id' AND ";
		$q .= "vendor_id='$ps_vendor_id'";
		$db->query($q);
		
		return True;
  }


  /**************************************************************************
  ** name: list_method()
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/
  function list_method($payment_method_id) {
    $ps_vendor_id = $_SESSION["ps_vendor_id"];
    $db = new ps_DB;
 
    require_once(CLASSPATH.'ps_shopper_group.php');
    $ps_shopper_group = new ps_shopper_group;


    $q =  "SELECT * from #__{vm}_shopper_group WHERE ";
    $q .= "`default`='1' ";
    $q .= "AND vendor_id='$ps_vendor_id'";
    $db->query($q);
    if (!$db->num_rows()) {
        $q =  "SELECT * from #__{vm}_shopper_group WHERE ";
        $q .= "vendor_id='$ps_vendor_id'";
        $db->query($q);
    }
    $db->next_record();
    $default_shopper_group_id = $db->f("shopper_group_id");
 

      $q = "SELECT * from #__{vm}_payment_method WHERE ";
      $q .= "vendor_id='$ps_vendor_id' AND ";
      $q .= "shopper_group_id='$default_shopper_group_id' ";
      if ($ps_shopper_group->get_id() != $default_shopper_group_id) 
        $q .= "OR shopper_group_id='".$ps_shopper_group->get_id()."' ";
      $q .= "ORDER BY list_order";
      $db->query($q);

    // Start drop down list
    echo "<select class=\"inputbox\" name=\"payment_method_id\">\n";
    echo "<option value=\"0\">"._PHPSHOP_SELECT."</option>\n";
    while ($db->next_record()) {
       echo "<option value=" . $db->f("payment_method_id") . " ";
       if ($db->f("payment_method_id") == $payment_method_id) 
	  echo "selected=\"selected\">\n";
       else
	  echo ">\n";
       echo $db->f("payment_method_name") . "</option>\n";
    }

    // End drop down list
    echo "</select>\n";
  }


  /**************************************************************************
  ** name: list_payment_radio($selector, $payment_method_id, $horiz)
  ** created by: Ekkehard Domning
  ** description: Returns all pyment_method with given selector in a Radiolist
  ** parameters:
  **              $selector : A String like "B"
  **              $payment_method_id : An ID to preselect
  **              $horiz : Separates Items with Space if true, else with <BR>
  ** returns:
  ***************************************************************************/
  function list_payment_radio($selector, $payment_method_id, $horiz) {
    global $CURRENCY_DISPLAY;
    $ps_vendor_id = $_SESSION["ps_vendor_id"];
    $auth = $_SESSION["auth"];
    $db = new ps_DB;

    require_once(CLASSPATH.'ps_shopper_group.php');
    $ps_shopper_group = new ps_shopper_group;

    $q =  "SELECT shopper_group_id from #__{vm}_shopper_group WHERE ";
    $q .= "`default`='1' ";
    $db->query($q);
     if (!$db->num_rows()) {
        $q =  "SELECT shopper_group_id from #__{vm}_shopper_group";
        $db->query($q);
    }
    $db->next_record();
    $default_shopper_group_id = $db->f("shopper_group_id");

    $q = "SELECT payment_method_id,payment_method_discount, payment_method_name from #__{vm}_payment_method WHERE ";
    $q .= "(enable_processor='$selector') AND ";
    $q .= "payment_enabled='Y' AND ";
    $q .= "vendor_id='$ps_vendor_id' AND ";

    if ($auth["shopper_group_id"] == $default_shopper_group_id) {
      $q .= "shopper_group_id='$default_shopper_group_id' ";
    } else {
      $q .= "(shopper_group_id='$default_shopper_group_id' ";
      $q .= "OR shopper_group_id='".$auth["shopper_group_id"]."') ";
    }

    $q .= "ORDER BY list_order";
    $db->query($q);

    // Start radio list
    while ($db->next_record()) {
       echo "<input type=\"radio\" name=\"payment_method_id\" id=\"".$db->f("payment_method_name")."\" value=\"".$db->f("payment_method_id")."\" ";
       if( $selector == "' OR enable_processor='Y" ) {
          echo "onchange=\"javascript: changeCreditCardList();\" ";
       }
       if (( $db->f("payment_method_id") == $payment_method_id || empty( $payment_method_id ) ) && !@$GLOBALS['payment_selected']) {
          echo "checked=\"checked\" />\n";
          $GLOBALS['payment_selected'] = true;
       }
       else {
       	echo ">\n";
       }
       
       $discount  = $db->f("payment_method_discount");
       echo "<label for=\"".$db->f("payment_method_name")."\">".$db->f("payment_method_name");
       if ($discount > 0.00) {
           echo " (- ".$CURRENCY_DISPLAY->getFullValue(abs($discount)).") \n";
       } 
       elseif ($discount < 0.00) {
           echo " (+ ".$CURRENCY_DISPLAY->getFullValue(abs($discount)).") \n";
       } 
       echo "</label>";
       if ($horiz) {
         echo(" ");
       } else {
         echo("<br />");
       }
    }
  }

  /**************************************************************************
  ** name: payment_sql($payment_method_id)
  ** created by: Ekkehard Domning
  ** description: Query the payment_method Table for the given ID
  ** parameters:  $payment_method_id : An ID
  ** returns:     the Database
  ***************************************************************************/
  function payment_sql($payment_method_id) {
    $db = new ps_DB;
    $q = "SELECT * FROM #__{vm}_payment_method WHERE payment_method_id=$payment_method_id";
    $db->query($q);
    return $db;
  }


  /**************************************************************************
  ** name: list_cc($payment_method_id, $horiz)
  ** created by: Ekkehard Domning
  ** description: Returns all CreditCards in a Radiolist
  ** parameters:
  ** returns:
  ***************************************************************************/
  function list_cc($payment_method_id, $horiz) {
    $this->list_payment_radio("' OR enable_processor='Y",$payment_method_id, $horiz); //A bit strange :-)
  }

  /**************************************************************************
  ** name: list_bank($payment_method_id, $horiz)
  ** created by: Ekkehard Domning
  ** description: Returns all Bank payment in a Radiolist
  ** parameters:
  ** returns:
  ***************************************************************************/
  function list_bank($payment_method_id, $horiz) {
    $this->list_payment_radio("B",$payment_method_id, $horiz); //A bit easier :-)
  }

  /**************************************************************************
  ** name: list_nocheck ($payment_method_id, $horiz)
  ** created by: Ekkehard Domning
  ** description: Returns all Paymentmethods which need no check
  ** parameters:
  ** returns:
  ***************************************************************************/
  function list_nocheck($payment_method_id, $horiz) {
    $this->list_payment_radio("N",$payment_method_id, $horiz); //A bit easier :-)
  }
  
  /**************************************************************************
  ** name: list_paypalrelated ($payment_method_id, $horiz)
  ** created by: soeren
  ** description: Returns all Payment methods which a paypal - like
  ** parameters:
  ** returns:
  ***************************************************************************/
  function list_paypalrelated($payment_method_id, $horiz) {
    $this->list_payment_radio("P",$payment_method_id, $horiz); //A bit easier :-)
  }
  
  /*
   * get_field public method
   *   return string
   */
  function get_field($payment_method_id, $field_name) {

    $db = new ps_DB;

    $q = "SELECT $field_name FROM #__{vm}_payment_method WHERE payment_method_id='$payment_method_id'";
    $db->query($q);
    $db->next_record();
    return $db->f($field_name);
  }
  
  /**************************************************************************
  ** name: is_creditcard()
  ** created by: soeren
  ** description: returns true if the payment is credit card payment
  ** parameters: $payment_id
  ** returns: 
  ***************************************************************************/
    function is_creditcard($payment_id) {
    
        $db = new ps_DB;
        $q = "SELECT is_creditcard,accepted_creditcards FROM #__{vm}_payment_method ";
        $q .= "WHERE payment_method_id='".$payment_id."'";
        $db->query($q);
        $db->next_record();
        $details = $db->f('accepted_creditcards');
        
        return $details != "";
    
    }
  /**************************************************************************
  ** name: validate_payment()
  ** Adapted From CreditCard Class
  ** Copyright (C) 2002 Daniel Fr�z Costa
  **
  ** created by: soeren
  * Documentation:
  *
  * Card Type                   Prefix           Length     Check digit
  * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
  * MasterCard                  51-55            16         mod 10
  * Visa                        4                13, 16     mod 10
  * AMEX                        34, 37           15         mod 10
  * Dinners Club/Carte Blanche  300-305, 36, 38  14         mod 10
  * Discover                    6011             16         mod 10
  * enRoute                     2014, 2149       15         any
  * JCB                         3                16         mod 10
  * JCB                         2131, 1800       15         mod 10
  *
  * More references:
  * http://www.beachnet.com/~hstiles/cardtype.hthml
  *
  ** returns:  True - credit card number has a valid format
  **          False - credit card number has no valid format
  ***************************************************************************/
  function validate_payment($creditcard_code, $cardnum) {
    
    $this->number = $this->_strtonum($cardnum);
/*
    if(!$this->detectType($this->number))
    {
      $this->errno = CC_ETYPE;
      $d['error'] = $this->errno;
      return false;
    }*/

    if(!$this->mod10($this->number))
    {
      $this->errno = CC_ENUMBER;
      $d['error'] = $this->errno;
      return false;
    }

    return true;
  }
  
  /*
   * detectType method
   *   returns card type in number format
   */
  function detectType($cardnum = 0)
  {
    if($cardnum)
      $this->number = $this->_strtonum($cardnum);
    if(!$this->number)
    {
      $this->errno = CC_ECALL;
      return UNKNOWN;
    }

    if(preg_match("/^5[1-5]\d{14}$/", $this->number))
      $this->type = MASTERCARD;

    else if(preg_match("/^4(\d{12}|\d{15})$/", $this->number))
      $this->type = VISA;

    else if(preg_match("/^3[47]\d{13}$/", $this->number))
      $this->type = AMEX;

    else if(preg_match("/^[300-305]\d{11}$/", $this->number) ||
      preg_match("/^3[68]\d{12}$/", $this->number))
      $this->type = DINNERS;
      
     elseif (ereg ('^6334[5-9].{11}$', $this->number) || ereg ('^6767[0-9].{11}$', $this->number))
      $this->type = SOLO_MAESTRO;

     elseif (ereg ('^564182[0-9].{9}$', $this->number) 
      || ereg ('^6333[0-4].{11}$', $this->number)
      || ereg ('^6759[0-9].{11}$', $this->number))
        $this->type= SWITCH_MAESTRO;

     elseif (ereg ('^49030[2-9].{10}$', $this->number) 
     || ereg ('^49033[5-9].{10}$', $this->number)
     || ereg ('^49110[1-2].{10}$', $this->number)
     || ereg ('^49117[4-9].{10}$', $this->number)
     || ereg ('^49118[0-2].{10}$', $this->number)
     || ereg ('^4936[0-9].{11}$', $this->number))
      $this->type = SWITCH_;
    
     //failing earlier 6xxx xxxx xxxx xxxx checks then its a Maestro card
     elseif (ereg ('^6[0-9].{14}$', $this->number) || ereg ('^5[0,6-8].{14}$', $this->number))
      $this->type = MAESTRO;
      
     elseif (ereg ('^450875[0-9].{9}$', $this->number)
     || ereg ('^48440[6-8].{10}$', $this->number)
     || ereg ('^48441[1-9].{10}$', $this->number)
     || ereg ('^4844[2-4].{11}$', $this->number)
     || ereg ('^48445[0-5].{10}$', $this->number)
     || ereg ('^4917[3-5].{11}$', $this->number)
     || ereg ('^491880[0-9].{9}$', $this->number))  
      $this->type= UK_ELECTRON;
      
	//DB 18-07-05
    else if(preg_match("/^6\d{15,21}$/", $this->number))
       $this->type = SWITCHCARD;
       
    else if(preg_match("/^6011\d{12}$/", $this->number))
      $this->type = DISCOVER;

    else if(preg_match("/^5610\d{12}$/", $this->number))
      $this->type = BANKCARD;

    else if(preg_match("/^2(014|149)\d{11}$/", $this->number))
      $this->type = ENROUTE;

    else if(preg_match("/^3\d{15}$/", $this->number) ||
      preg_match("/^(2131|1800)\d{11}$/", $this->number))
      $this->type = JCB;

    if(!$this->type)
    {
      $this->errno = CC_ECANTYPE;
      return UNKNOWN;
    }

    return $this->type;
  }

  /*
   * detectTypeString
   *   return string of card type
   */
  function detectTypeString($cardnum = 0)
  {
    if(!$cardnum)
    {
      if(!$this->type)
        $this->errno = CC_EARG;
    }
    else
      $this->type = $this->detectType($cardnum);

    if(!$this->type)
    {
      $this->errno = CC_ETYPE;
      return NULL;
    }

    switch($this->type)
    {
      case MASTERCARD:
        return "MASTERCARD";
      case VISA:
        return "VISA";
      case AMEX:
        return "AMEX";
      case DINNERS:
        return "DINNERS";
      case DISCOVER:
        return "DISCOVER";
      case ENROUTE:
        return "ENROUTE";
      case JCB:
        return "JCB";
      default:
        $this->errno = CC_ECANTYPE;
        return NULL;
    }
  }

  /*
   * getCardNumber
   *   returns card number, only digits
   */
  function getCardNumber()
  {
    if(!$this->number)
    {
      $this->errno = CC_ECALL;
      return 0;
    }

    return $this->number;
  }

  /*
   * errno method
   *   return error number
   */
  function errno()
  {
    return $this->errno;
  }

  /*
   * mod10 method - Luhn check digit algorithm
   *   return 0 if true and !0 if false
   */
  function mod10( $card_number )
  {

    $digit_array = array ();
    $cnt = 0;

    //Reverse the card number
    $card_temp = strrev ( $card_number );

    //Multiple every other number by 2 then ( even placement )
    //Add the digits and place in an array
    for ( $i = 1; $i <= strlen ( $card_temp ) - 1; $i = $i + 2 )
    {
      //multiply every other digit by 2
      $t = substr ( $card_temp, $i, 1 );
      $t = $t * 2;
      //if there are more than one digit in the
      //result of multipling by two ex: 7 * 2 = 14
      //then add the two digits together ex: 1 + 4 = 5
      if ( strlen ( $t ) > 1 )
      {
        //add the digits together
        $tmp = 0;
        //loop through the digits that resulted of
        //the multiplication by two above and add them
        //together
        for ( $s = 0; $s < strlen ( $t ); $s++ )
        {
          $tmp = substr ( $t, $s, 1 ) + $tmp;
        }
      }
      else{  // result of (* 2) is only one digit long
        $tmp = $t;
      }
      //place the result in an array for later
      //adding to the odd digits in the credit card number
      $digit_array [ $cnt++ ] = $tmp;
    }
    $tmp = 0;

    //Add the numbers not doubled earlier ( odd placement )
    for ( $i = 0; $i <= strlen ( $card_temp ); $i = $i + 2 )
    {
      $tmp = substr ( $card_temp, $i, 1 ) + $tmp;
    }

    //Add the earlier doubled and digit-added numbers to the result
    $result = $tmp + array_sum ( $digit_array );

    //Check to make sure that the remainder
    //of dividing by 10 is 0 by using the modulas
    //operator
    return ( $result % 10 == 0 );

  }

  /*
   * resetCard method
   *   clear only cards information
   */
  function resetCard()
  {
    $this->number = 0;
    $this->type = 0;
  }

  /*
   * strError method
   *   return string error
   */
  function strError()
  {
    switch($this->errno)
    {
      case CC_ECALL:
        return "Invalid call for this method";
      case CC_ETYPE:
        return "Invalid card type";
      case CC_ENUMBER:
        return "Invalid card number";
      case CC_EFORMAT:
        return "Invalid format";
      case CC_ECANTYPE:
        return "Cannot detect the type of your card";
      case CC_OK:
        return "Success";
    }
  }
  
  /*
   * _strtonum private method
   *   return formated string - only digits
   */
  function _strtonum($string)
  {
    $nstr = "";
    for($i=0; $i< strlen($string); $i++)
    {
      if(!is_numeric($string{$i}))
        continue;
      $nstr = "$nstr".$string{$i};
    }
    return $nstr;
  }
  
}
?>
