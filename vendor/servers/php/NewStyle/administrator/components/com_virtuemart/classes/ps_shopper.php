<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: ps_shopper.php,v 1.13.2.4 2006/04/05 18:16:53 soeren_nb Exp $
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
*
* The class is meant to manage shopper entries
*/
class ps_shopper {
  var $classname = "ps_shopper";
  
	/**************************************************************************
	** name: validate_add()
	** created by:
	** description:
	** parameters:
	** returns:
	  ***************************************************************************/
	function validate_add(&$d) {
		global $my, $perm;
		
		$db = new ps_DB;
		
		$provided_required = true;
		$missing = "";
		
		if( empty( $my->id ) && VM_SILENT_REGISTRATION != '1') {
			if (empty($d['username'])) { $provided_required = false; $missing .= "username,"; }
			if (empty($d['password'])) { $provided_required = false; $missing .= "password,"; }
			if (empty($d['password2'])) { $provided_required = false; $missing .= "password2,"; }
			if (empty($d['email'])) { $provided_required = false; $missing .= "email,"; }
		}
		
		if (empty($d['first_name'])) { $provided_required = false; $missing .= "first_name,"; }
		if (empty($d['last_name']))  { $provided_required = false; $missing .= "last_name,"; }
		//if (empty($d['company']))  { $provided_required = false; $missing .= "company,"; }
		if (empty($d['address_1']))  { $provided_required = false; $missing .= "address_1,"; }
		if (empty($d['city']))  { $provided_required = false; $missing .= "city,"; }
		if (empty($d['zip'])) { $provided_required = false; $missing .= "zip,"; }
		if (CAN_SELECT_STATES == '1') {
			if (empty($d['state'])) { $provided_required = false; $missing .= "state,"; }
		}
		if (empty($d['country'])) { $provided_required = false; $missing .= "country,"; }
		//if (empty($d['phone_1'])) { $provided_required = false; $missing .= "phone_1"; }
		
		if (MUST_AGREE_TO_TOS == '1'&& !$perm->is_registered_customer( $my->id )) { 
			if( empty( $d['agreed'] )) {
				$provided_required = false;
				$missing .= "agreed,";
			}
		}
		
		if (!$provided_required) {
			$_REQUEST['missing'] = $missing;
			return false;
			/*
			$url="username=".$d['username'];
			$url.="&email=".$d['email'];
			$url.="&first_name=".$d['first_name'];
			$url.="&last_name=".$d['last_name'];
			$url.="&middle_name=".$d['middle_name'];
			$url.="&title=".$d['title'];
			$url.="&company=".$d['company'];
			$url.="&address_1=".$d['address_1'];
			$url.="&address_2=".$d['address_2'];
			$url.="&city=".$d['city'];
			$url.="&zip=".$d['zip'];
			$url.="&state=".$d['state'];
			$url.="&country=".$d['country'];
			$url.="&phone_1=".$d['phone_1'];
			$url.="&fax=".$d['fax'];
			$url.="&bank_account_nr=".@$d['bank_account_nr'];
			$url.="&bank_sort_code=".@$d['bank_sort_code'];
			$url.="&bank_name=".@$d['bank_name'];
			$url.="&bank_iban=".@$d['bank_iban'];
			$url.="&bank_account_holder=".@$d['bank_account_holder'];
			$url.="&bank_account_type=".@$d['bank_account_type'];
			
			mosRedirect( "index.php?option=com_virtuemart&page=".$_SESSION['last_page']."&missing=$missing&$url", _CONTACT_FORM_NC );
			*/
		}
		
		$d['user_email'] = @$d['email'];
		$d['perms'] = 'shopper';
	
		return $provided_required;
	  }

	/**************************************************************************
	** name: validate_update()
	** created by:
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function validate_update(&$d) {
		global $my, $perm, $vmLogger;
		
		if ($my->id == 0){
		  $vmLogger->err( "Please Login first." );
		  
		  return false;
		}
		return $this->validate_add( $d );
		
	}
	
	/**************************************************************************
	** name: validate_delete()
	** created by:
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function validate_delete(&$d) {
		global $my;		
		 
		if ($my->id == 0){
		 	$vmLogger->err( "Please Login first." );
			return false;
		}
		if (!$d["user_id"]) {
			$vmLogger->err( "Please select a user to delete." );
			return False;
		}
		else {
			return True;
		}
	}

  
	/**
	* Function to add a new Shopper into the Shop and Joomla
	*/
	function add( &$d ) {
		global $my, $ps_user, $mainframe, $mosConfig_absolute_path, 
		$VM_LANG, $database, $option, $mosConfig_useractivation;
		
		$ps_vendor_id = $_SESSION["ps_vendor_id"];
		$hash_secret = "VirtueMartIsCool";
		$db = new ps_DB;
		$timestamp = time();
		
		if (!$this->validate_add($d)) {
		  return False;
		}
		// Use InputFilter class to prevent SQL injection or HTML tags
		$d = $GLOBALS['vmInputFilter']->safeSQL( $d );
	
		if ($VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_4 and $d["extra_field_4"] == "") {
		  $d["extra_field_4"] = "N";
		}
		if ($VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_5 and $d["extra_field_5"] == "") {
		  $d["extra_field_5"] = "N";
		} 
		if( empty( $my->id ) ) {

			$_POST['name'] = $d['first_name']." ".$d['last_name'];
			if( VM_SILENT_REGISTRATION == '1' ) {
				$silent_username = substr( str_replace( '-', '_', $d['email'] ), 0, 25 );
				$db->query( 'SELECT username FROM `#__users` WHERE username=\''.$silent_username.'\'');
				$i = 0;
				while( $db->next_record()) {
					$silent_username = substr_replace( $silent_username, $i, strlen($silent_username)-1 );
					$db->query( 'SELECT username FROM `#__users` WHERE username=\''.$silent_username.'\'');
					$i++;
				}
				$_POST['username'] = $d['username'] = $silent_username;
				$_POST['password'] = $d['password'] = mosMakePassword();
				$_POST['password2'] = $_POST['password'];
			}
			// Process Mambo/Joomla registration stuff
			if( !$this->saveRegistration() ) {
				return false;
			}
			
			$database->setQuery( "SELECT id FROM #__users WHERE username='".$d['username']."'" );
			$database->loadObject( $userid );
			$uid = $userid->id;
		}
		else {
			$uid = $my->id;
			$d['email'] = $_POST['email'] = $my->email;
			
		}
		$db->query( 'SELECT user_id FROM #__{vm}_user_info WHERE user_id='.$my->id );
		$db->next_record();
		
		if( $db->f('user_id')) {
			return $this->update( $d );
		}
		
		// Insert billto
		$q = "INSERT INTO #__{vm}_user_info VALUES (";
		$q .= "'" . md5(uniqid( $hash_secret)) . "',";
		$q .= "'" . $uid . "',";
		$q .= "'BT',";
		$q .= "'-default-',";
		$q .= "'" .$d["company"] . "',";
		$q .= "'" .$d["title"] . "',";
		$q .= "'" .$d["last_name"] . "',";
		$q .= "'" .$d["first_name"] . "',";
		$q .= "'" .$d["middle_name"] . "',";
		$q .= "'" .$d["phone_1"] . "',";
		$q .= "'" .@$d["phone_2"] . "',";
		$q .= "'" .$d["fax"] . "',";
		$q .= "'" .$d["address_1"] . "',";
		$q .= "'" .$d["address_2"] . "',";
		$q .= "'" .$d["city"] . "',";
		$q .= "'" .@$d["state"] . "',";
		$q .= "'" .$d["country"] . "',";
		$q .= "'" .$d["zip"] . "',";
		$q .= "'" .$d["email"] . "',";
		$q .= "'" .@$d["extra_field_1"] . "',";
		$q .= "'" .@$d["extra_field_2"] . "',";
		$q .= "'" .@$d["extra_field_3"] . "',";
		$q .= "'" .@$d["extra_field_4"] . "',";
		$q .= "'" .@$d["extra_field_5"] . "',";
		$q .= "'" .$timestamp . "',";
		$q .= "'" .$timestamp . "',";
		$q .= "'shopper', ";
		$q .= "'" . @$d["bank_account_nr"] . "', ";
		$q .= "'" . @$d["bank_name"] . "', ";
		$q .= "'" . @$d["bank_sort_code"] . "', ";
		$q .= "'" . @$d["bank_iban"] . "', ";
		$q .= "'" . @$d["bank_account_holder"] . "', ";
		$q .= "'" . @$d["bank_account_type"] . "') ";
		
		$db->query($q);
		
		
		// Insert vendor relationship
		$q = "INSERT INTO #__{vm}_auth_user_vendor (user_id,vendor_id)";
		$q .= " VALUES ";
		$q .= "('" . $uid . "','";
		$q .= $ps_vendor_id . "') ";
		$db->query($q);
		
		// Insert Shopper -ShopperGroup - Relationship    
		$q =  "SELECT shopper_group_id from #__{vm}_shopper_group WHERE ";
		$q .= "`default`='1' ";
   
		$db->query($q);
		if (!$db->num_rows()) {  // take the first in the table
   
			$q =  "SELECT shopper_group_id from #__{vm}_shopper_group";
			$db->query($q);
		}
		$db->next_record();
		$d['shopper_group_id'] = $db->f("shopper_group_id");

		$customer_nr = uniqid( rand() );
		
		$q  = "INSERT INTO #__{vm}_shopper_vendor_xref ";
		$q .= "(user_id,vendor_id,shopper_group_id,customer_number) ";
		$q .= "VALUES ('$uid', '$ps_vendor_id','".$d['shopper_group_id']."', '$customer_nr')";
		$db->query($q);
		
		if( !$my->id && $mosConfig_useractivation == '0') {
			$mainframe->login($d['username'], md5( $d['password'] ));
			mosRedirect( "index.php?option=$option&page=checkout.index" );
		}
		elseif( $my->id ) {
			mosRedirect( "index.php?option=$option&page=checkout.index" );
		}
		else {
			mosRedirect( "index.php?option=$option&page=shop.index", _REG_COMPLETE_ACTIVATE );
		}
		
		return True;
    
	}

	/**
	 * The function from com_registration!
	 * Registers a user into Mambo/Joomla
	 *
	 * @return boolean True when the registration process was successful, False when not
	 */
	function saveRegistration() {
		global $database, $acl, $VM_LANG, $vmLogger;
		global $mosConfig_sitename, $mosConfig_live_site, $mosConfig_useractivation, $mosConfig_allowUserRegistration;
		global $mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_mailfrom, $mosConfig_fromname;
	
		if ($mosConfig_allowUserRegistration=='0') {
			mosNotAuth(); 
			return false;
		}
	
		$row = new mosUser( $database );
	
		if (!$row->bind( $_POST, 'usertype' )) {
			$error = vmHtmlEntityDecode( $row->getError() );
			$vmLogger->err( $error );
			echo "<script type=\"text/javascript\"> alert('". $error. "');</script>\n";
			return false;
		}
	
		mosMakeHtmlSafe($row);
		
		$usergroup = 'Registered';
		$row->id = 0;
		$row->usertype = $usergroup;
		$row->gid = $acl->get_group_id( $usergroup, 'ARO' );
	
		if ($mosConfig_useractivation == '1') {
			$row->activation = md5( mosMakePassword() );
			$row->block = '1';
		}
	
		if (!$row->check()) {
			$error = vmHtmlEntityDecode( $row->getError() );
			$vmLogger->err( $error );
			echo "<script type=\"text/javascript\"> alert('". $error. "');</script>\n";
			return false;
		}
	
		$pwd 				= $row->password;
		$row->password 		= md5( $row->password );
		$row->registerDate 	= date('Y-m-d H:i:s');
	
		if (!$row->store()) {
			$error = vmHtmlEntityDecode( $row->getError() );
			$vmLogger->err( $error );
			echo "<script type=\"text/javascript\"> alert('". $error. "');</script>\n";
			return false;
		}
		$row->checkin();
	
		$name 		= $row->name;
		$email 		= $row->email;
		$username 	= $row->username;
	
		$subject 	= sprintf (_SEND_SUB, $name, $mosConfig_sitename);
		$subject 	= vmHtmlEntityDecode($subject, ENT_QUOTES);
		if ($mosConfig_useractivation=="1"){
			$message = sprintf (_USEND_MSG_ACTIVATE, $name, $mosConfig_sitename, $mosConfig_live_site."/index.php?option=com_registration&task=activate&activation=".$row->activation, $mosConfig_live_site, $username, $pwd);
		} else {
			$message = sprintf ($VM_LANG->_PHPSHOP_USER_SEND_REGISTRATION_DETAILS, $name, $mosConfig_sitename, $mosConfig_live_site, $username, $pwd);
		}
	
		$message = vmHtmlEntityDecode($message, ENT_QUOTES);
		// Send email to user
		if ($mosConfig_mailfrom != "" && $mosConfig_fromname != "") {
			$adminName2 = $mosConfig_fromname;
			$adminEmail2 = $mosConfig_mailfrom;
		} else {
			$query = "SELECT name, email"
			. "\n FROM #__users"
			. "\n WHERE LOWER( usertype ) = 'superadministrator'"
			. "\n OR LOWER( usertype ) = 'super administrator'"
			;
			$database->setQuery( $query );
			$rows = $database->loadObjectList();
			$row2 			= $rows[0];
			$adminName2 	= $row2->name;
			$adminEmail2 	= $row2->email;
		}
	
		mosMail($adminEmail2, $adminName2, $email, $subject, $message);
	
		// Send notification to all administrators
		$subject2 = sprintf (_SEND_SUB, $name, $mosConfig_sitename);
		$message2 = sprintf (_ASEND_MSG, $adminName2, $mosConfig_sitename, $row->name, $email, $username);
		$subject2 = vmHtmlEntityDecode($subject2, ENT_QUOTES);
		$message2 = vmHtmlEntityDecode($message2, ENT_QUOTES);
	
		// get superadministrators id
		$admins = $acl->get_group_objects( 25, 'ARO' );
	
		foreach ( $admins['users'] AS $id ) {
			$query = "SELECT email, sendEmail"
			. "\n FROM #__users"
			."\n WHERE id = $id"
			;
			$database->setQuery( $query );
			$rows = $database->loadObjectList();
	
			$row = $rows[0];
	
			if ($row->sendEmail) {
				mosMail($adminEmail2, $adminName2, $row->email, $subject2, $message2);
			}
		}
	
		if ( $mosConfig_useractivation == 1 ){
			echo _REG_COMPLETE_ACTIVATE;
		} else {
			echo _REG_COMPLETE;
		}
		return true;
	}

  
	/**
	* Function to update a Shopper Entry
	* (uses who have perms='shopper')
	*/
	function update(&$d) {
		global $my, $perm, $sess, $vmLogger;
		
		$auth = $_SESSION['auth'];
			
		$db = new ps_DB;
		
		$d = $GLOBALS['vmInputFilter']->safeSQL( $d );
	
		if (@$d["user_id"] != $my->id && $auth["perms"] != "admin") {
		  $vmLogger->crit( "Tricky tricky, but we know about this one." );
		  return False;
		}
		
		require_once(CLASSPATH. 'ps_user.php' );
		if( !empty($d['username'])) {
			$_POST['username'] = $d['username'];
		}
		else {
			$_POST['username'] = $my->username;
		}
		$_POST['name'] = $d['first_name']." ". $d['last_name'];
		$_POST['id'] = $auth["user_id"];
		$_POST['gid'] = $my->gid;
		$d['error'] = "";
		
		ps_user::saveUser( $d );
		if( !empty( $d['error']) ) {
			
			return false;
		}
		
		if (!$this->validate_update($d)) {
			$_SESSION['last_page'] = "checkout.index";
			return false;
		}
		$user_id = $auth["user_id"];
		
		/* Update Bill To */
		$q  = "UPDATE #__{vm}_user_info SET ";
		if (!empty($d['company'])) {
			$q .= "company='" . $d["company"] . "', ";
		} else { 
			$q .= "company='', "; 
		}
		$q .= "title='" . $d["title"] . "', ";
		$q .= "last_name='" . $d["last_name"] . "', ";
		$q .= "first_name='" . $d["first_name"] . "', ";
		if (!empty($d['middle_name'])) {
			$q .= "middle_name='" . $d["middle_name"] . "', ";
		}
		else {
			$q .= "middle_name='', ";
		}
		$q .= "phone_1='" . $d["phone_1"] . "', ";
		if (!empty($d['phone_2'])) {
			$q .= "phone_2='" . $d["phone_2"] . "',";
		} else {
			$q .= "phone_2='',";
		}
		if (!empty($d['fax'])) {
			$q .= "fax='" . $d["fax"] . "', ";
		} else {
			$q .= "fax='', ";
		}
		$q .= 	"address_1='" . $d["address_1"] . "', ";
		$q .= "address_2='" . @$d["address_2"] . "', ";
		$q .= "city='" . $d["city"] . "', ";
		if (!empty($d['state'])){
			$q .= "state='" . $d["state"] . "', ";
		} else {
			$q .= "state='', ";
		}
		$q .= "country='" . $d["country"] . "', ";
		$q .= "zip='" . $d["zip"] . "', ";
		$q .= "extra_field_1='" . @$d["extra_field_1"] . "', ";
		$q .= "extra_field_2='" . @$d["extra_field_2"] . "', ";
		$q .= "extra_field_3='" . @$d["extra_field_3"] . "', ";
		$q .= "extra_field_4='" . @$d["extra_field_4"] . "', ";
		$q .= "extra_field_5='" . @$d["extra_field_5"] . "' ";
		if (!empty($d['bank_iban'])) {
			$q .= ",bank_iban='" . $d["bank_iban"] . "' ";
		} else {
			$q .= ",bank_iban='' ";
		}
		if (!empty($d['bank_account_nr'])){
			$q .= ",bank_account_nr='" . $d["bank_account_nr"] . "' ";
		} else { 
			$q .= ",bank_account_nr='' ";
		}
		if (!empty($d['bank_sort_code'])) {
			$q .= ",bank_sort_code='" . $d["bank_sort_code"] . "' ";
		} else { 
			$q .= ",bank_sort_code='' ";
		}
		if (!empty($d['bank_name'])) {
			$q .= ",bank_name='" . $d["bank_name"] . "'";
		} else {
			$q .= ",bank_name=''";
		}
		if (!empty($d['bank_account_holder'])) {
			$q .= ", bank_account_holder='" . $d["bank_account_holder"] . "' ";
		} else {
			$q .= ", bank_account_holder='' ";
		}
		if (mShop_validateEmail(@$d['email'])) {
			$q .= ",user_email = '".@$d['email']."' ";
		}
		$q .= "WHERE user_id=" . intval( $user_id ). " AND address_type='BT'";
	  
		$db->query($q);
	  
		// UPDATE #__{vm}_shopper group relationship
		$q = "SELECT shopper_group_id FROM #__{vm}_shopper_vendor_xref ";
		$q .= "WHERE user_id = '".$user_id."'";
		$db->query($q);
		
		if (!$db->num_rows()) {
			//add
			
			$shopper_db = new ps_DB;
			// get the default shopper group
			$q =  "SELECT shopper_group_id from #__{vm}_shopper_group WHERE ";
			$q .= "`default`='1'";
			$shopper_db->query($q);
			if (!$shopper_db->num_rows()) {  // when there is no "default", take the first in the table 
				$q =  "SELECT shopper_group_id from #__{vm}_shopper_group";
				$shopper_db->query($q);
			}
			
			$shopper_db->next_record();
			$my_shopper_group_id = $shopper_db->f("shopper_group_id");
			if (empty($d['customer_number'])) 
				$d['customer_number'] = "";
			
			$q  = "INSERT INTO #__{vm}_shopper_vendor_xref ";
			$q .= "(user_id,vendor_id,shopper_group_id) ";
			$q .= "VALUES ('";
			$q .= $_SESSION['auth']['user_id'] . "','";
			$q .= $_SESSION['ps_vendor_id'] . "','";
			$q .= $my_shopper_group_id. "')";
			$db->query($q);
		}
		$q = "SELECT user_id FROM #__{vm}_auth_user_vendor ";
		$q .= "WHERE user_id = '".$_SESSION['auth']['user_id']."'";
		$db->query($q);
		if (!$db->num_rows()) {
			// Insert vendor relationship
			$q = "INSERT INTO #__{vm}_auth_user_vendor (user_id,vendor_id)";
			$q .= " VALUES ";
			$q .= "('" . $_SESSION['auth']['user_id'] . "','";
			$q .= $_SESSION['ps_vendor_id'] . "') ";
			$db->query($q);
		}
		
		return True;
	}
  
	/**
	* Function to delete a Shopper
	*/
	function delete(&$d) {
		global $my;
	  
		$db = new ps_DB;
		
		if (!$this->validate_delete($d)) {
			return False;
		}
	
		// Delete user_info entries
		// and Shipping addresses
		$q = "DELETE FROM #__{vm}_user_info where user_id='" . $d["user_id"] . "'"; 
		$db->query($q);
		
		// Delete shopper_vendor_xref entries
		$q = "DELETE FROM #__{vm}_shopper_vendor_xref where user_id='" . $d["user_id"] . "'"; 
		$db->query($q);
		
		$q = "DELETE FROM #__{vm}_auth_user_vendor where user_id='" . $d["user_id"] . "'"; 
		$db->query($q);
		return True;
	}
}
$ps_shopper = new ps_shopper;
?>
