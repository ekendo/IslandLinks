<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: ps_user.php,v 1.9.2.3 2006/02/27 19:41:42 soeren_nb Exp $
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

class ps_user {
    var $classname = "ps_user";
    var $permissions = array(
			   "shopper" 	=>  "1",
			   "demo" 	=>  "2",
			   "storeadmin" =>  "4",
			   "admin" 	=>  "8" 
			);
  /**************************************************************************
  ** name: validate_add()
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/
  function validate_add(&$d) {
    global $my, $perm, $vmLogger;
    $valid = true;
    
    if( !$perm->check( 'admin,storeadmin')) {
	    	
	    if (empty($d["country"])) {
	      $vmLogger->warning( 'Please select a country.' );
	      $valid = false;
	    }
		if (empty($d["address_1"])) {
	      $vmLogger->warning( '"Address 1" is a required field.');
	      $valid = false;
	    }
	    if ( empty($d["city"]) ) {
	      $vmLogger->warning( '"City" is a required field.' );
	      $valid = false;
	    }
	    if (CAN_SELECT_STATES == '1') {
	        if ( empty($d["state"]) ) {
	          $vmLogger->warning( '"State/Region" is a required field.' );
	          $valid = false;
	        }
	    }
	    if ( empty($d["zip"]) ) {
	      $vmLogger->warning( '"Zip" is a required field.' );
	      $valid = false;
	    }
	}
	
    if (!$d['perms']) {
      $vmLogger->warning( 'You must assign the user to a group.' );
      $valid = false;
    }
	else {
		if( !$perm->hasHigherPerms( $d['perms'] )) {
			$vmLogger->err( 'You have no permission to add a user of that usertype: '.$d['perms'] );
			$valid = false;
		}
		
	}
    return $valid;
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
		
		$valid = true;
		
		if (empty($d['user_id'])){
		  $vmLogger->warning( 'Please select a user to update.' );
		  $valid = false;
		}
		if( !$perm->check( 'admin,storeadmin')) {
			
			if (empty($d["country"])) {
			  $vmLogger->warning( 'Please select a country.');
			  $valid = false;
			}
				if (!$d["address_1"]) {
			  $vmLogger->warning( '"Address 1" is a required field.' );
			  $valid = false;
			}
			if (!$d["city"]) {
			  $vmLogger->warning( '"City" is a required field.' );
			  $valid = false;
			}
			if (CAN_SELECT_STATES == '1') {
				if (!$d["state"]) {
				  $vmLogger->warning( '"State/Region" is a required field.' );
				  $valid = false;
				}
			}
			if (!$d["zip"]) {
			  $vmLogger->warning( '"Zip" is a required field.' );
			  $valid = false;
			}
		}
		
	    if (!$d['perms']) {
	      $vmLogger->warning( 'You must assign the user to a group.' );
	      $valid = false;
	    }
		else {
			if( !$perm->hasHigherPerms( $d['perms'] )) {
				$vmLogger->err( 'You have no permission to add a user of that usertype: '.$d['perms'] );
				$valid = false;
			}
		}
		return $valid;
	}
  
	/**************************************************************************
	** name: validate_delete()
	** created by:
	** description:
	** parameters:
	** returns:
	  ***************************************************************************/
	function validate_delete( $id ) {
		global $my, $vmLogger;
		$auth = $_SESSION['auth'];
		
		$valid = true;
		
		if( empty($id)) {
			$vmLogger->err( 'Please select a user to delete.' );
			return false;
		}
		$db = new ps_DB();
		$q = "SELECT user_id, perms FROM #__{vm}_user_info WHERE user_id=$id";
		$db->query( $q );
		$perms = $db->f('perms');
		if( $this->permissions[$perms] >= $this->permissions[$auth['perms']]) {
			$vmLogger->err( 'You have no permission to delete a user of that usertype: '.$perms );
			$valid = false;
		}
		if( $id == $my->id) {
			$vmLogger->err( 'Very funny, but you cannot delete yourself.' );
			$valid = false;			
		}
		
		return $valid;
	}
	  
	/**************************************************************************
	* name: add()
	* created by:
	* description:
	* parameters:
	* returns:
	**************************************************************************/
	function add(&$d) {
		global $my, $VM_LANG, $perm, $vmLogger;
		$ps_vendor_id = $_SESSION["ps_vendor_id"];
		$hash_secret = "VirtueMartIsCool";
		$db = new ps_DB;
		$timestamp = time();
		
		if (!$this->validate_add($d)) {
		  return False;
		}
		
		if ($VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_4 and $d["extra_field_4"] == "") {
		  $d["extra_field_4"] = "N";
		}
		if ($VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_4 and $d["extra_field_5"] == "") {
		  $d["extra_field_5"] = "N";
		} 
		
		// Joomla User Information stuff
		$uid = $this->saveUser( $d );
		if( empty( $uid ) && empty( $d['id'] ) ) {
			$vmLogger->err( 'New User couldn\'t be added' );
			return false;
		}
		elseif( !empty( $d['id'])) {
			$uid = $d['id'];
		}
			
		// Insert billto
		$q = "INSERT INTO #__{vm}_user_info VALUES (";
		$q .= "'" . md5(uniqid( $hash_secret)) . "',";
		$q .= "'" . $uid . "',";
		$q .= "'".$d['address_type']."',";
		$q .= "'".$d['address_type_name']."',";
		$q .= "'" .$d["company"] . "',";
		$q .= "'" .$d["title"] . "',";
		$q .= "'" .$d["last_name"] . "',";
		$q .= "'" .$d["first_name"] . "',";
		$q .= "'" .$d["middle_name"] . "',";
		$q .= "'" .$d["phone_1"] . "',";
		$q .= "'" .$d["phone_2"] . "',";
		$q .= "'" .$d["fax"] . "',";
		$q .= "'" .$d["address_1"] . "',";
		$q .= "'" .$d["address_2"] . "',";
		$q .= "'" .$d["city"] . "',";
		$q .= "'" .$d["state"] . "',";
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
		$q .= "'".$d['perms']."', ";
		$q .= "'" . $d["bank_account_nr"] . "', ";
		$q .= "'" . $d["bank_name"] . "', ";
		$q .= "'" . $d["bank_sort_code"] . "', ";
		$q .= "'" . $d["bank_iban"] . "', ";
		$q .= "'" . $d["bank_account_holder"] . "', ";
		$q .= "'" . $d["bank_account_type"] . "') ";
		
		$db->query($q);
		if( $perm->check("admin"))
			$vendor_id = $d['vendor_id'];
		else
			$vendor_id = $ps_vendor_id;
			
		// Insert vendor relationship
		$q = "INSERT INTO #__{vm}_auth_user_vendor (user_id,vendor_id)";
		$q .= " VALUES ";
		$q .= "('" . $uid . "','$vendor_id') ";
		$db->query($q);
		
		// Insert Shopper -ShopperGroup - Relationship
		$q  = "INSERT INTO #__{vm}_shopper_vendor_xref ";
		$q .= "(user_id,vendor_id,shopper_group_id,customer_number) ";
		$q .= "VALUES ('$uid', '$vendor_id','".$d['shopper_group_id']."', '".$d['customer_number']."')";
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
		global $my, $VM_LANG, $perm;
		$ps_vendor_id = $_SESSION["ps_vendor_id"];
		$db = new ps_DB;
		$timestamp = time();
		
		if (!$this->validate_update($d)) {
		  return False;
		}
		
		if ($VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_4 and @$d["extra_field_4"] == "") {
		  $d["extra_field_4"] = "N";
		}
		if ($VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_5 and @$d["extra_field_5"] == "") {
		  $d["extra_field_5"] = "N";
		}
		
		// Joomla User Information stuff
		$this->saveUser( $d );
	
		/* Update Bill To */
		$q  = "UPDATE #__{vm}_user_info SET ";
		$q .= "company='" . $d["company"] . "', ";
		$q .= "address_type='" . $d["address_type"] . "', ";
		$q .= "address_type_name='" . $d["address_type_name"] . "', ";
		$q .= "title='" . $d["title"] . "', ";
		$q .= "last_name='" . $d["last_name"] . "', ";
		$q .= "first_name='" . $d["first_name"] . "', ";
		$q .= "middle_name='" . $d["middle_name"] . "', ";
		$q .= "phone_1='" . $d["phone_1"] . "', ";
		$q .= "phone_2='" . $d["phone_2"] . "', ";
		$q .= "fax='" . $d["fax"] . "', ";
		$q .= "address_1='" . $d["address_1"] . "', ";
		$q .= "address_2='" . $d["address_2"] . "', ";
		$q .= "city='" . $d["city"] . "', ";
		$q .= "state='" . @$d["state"] . "', ";
		$q .= "country='" . $d["country"] . "', ";
		$q .= "zip='" . $d["zip"] . "', ";
		$q .= "user_email='" . $d["email"] . "', ";
		$q .= "extra_field_1='" . @$d["extra_field_1"] . "', ";
		$q .= "extra_field_2='" . @$d["extra_field_2"] . "', ";
		$q .= "extra_field_3='" . @$d["extra_field_3"] . "', ";
		$q .= "extra_field_4='" . @$d["extra_field_4"] . "', ";
		$q .= "extra_field_5='" . @$d["extra_field_5"] . "', ";
		$q .= "mdate='" . $timestamp . "', ";
		$q .= "bank_iban='" . $d["bank_iban"] . "', ";
		$q .= "bank_account_nr='" . $d["bank_account_nr"] . "', ";
		$q .= "bank_sort_code='" . $d["bank_sort_code"] . "', ";
		$q .= "bank_name='" . $d["bank_name"] . "', ";
		$q .= "bank_account_holder='" . $d["bank_account_holder"] . "', ";
		$q .= "perms ='".$d['perms']."' ";    
		$q .= "WHERE user_id='" . $d["user_id"] . "' AND ";
		$q .= "address_type='BT'";
		
		$db->query($q);
		if( $perm->check("admin")) {
			$vendor_id = $d['vendor_id'];
		}
		else {
			$vendor_id = $ps_vendor_id;
		}
		
		$db->query( "SELECT COUNT(user_id) FROM #__{vm}_auth_user_vendor WHERE vendor_id='".$vendor_id."' AND user_id='" . $d["user_id"] . "'" );
		if( $db->num_rows() < 1 ) {		
			// Insert vendor relationship
			$q = "INSERT INTO #__{vm}_auth_user_vendor (user_id,vendor_id)";
			$q .= " VALUES ";
			$q .= "('" . $d['user_id'] . "','$vendor_id') ";
			$db->query($q);
		}
		else {
			// Update the User- Vendor  relationship
			$q = "UPDATE #__{vm}_auth_user_vendor set ";
			$q .= "vendor_id='".$d['vendor_id']."' ";
			$q .= "WHERE user_id='" . $d["user_id"] . "'";
			$db->query($q);
		}
		$db->query( "SELECT COUNT(user_id) FROM #__{vm}_shopper_vendor_xref WHERE vendor_id='".$vendor_id."' AND user_id='" . $d["user_id"] . "'" );
		if( $db->num_rows() < 1 ) {
			// Insert Shopper -ShopperGroup - Relationship
			$q  = "INSERT INTO #__{vm}_shopper_vendor_xref ";
			$q .= "(user_id,vendor_id,shopper_group_id,customer_number) ";
			$q .= "VALUES ('".$d['user_id']."', '$vendor_id','".$d['shopper_group_id']."', '".$d['customer_number']."')";
		}
		else {
			// Update the Shopper Group Entry for this user
			$q = "UPDATE #__{vm}_shopper_vendor_xref SET ";
			$q .= "shopper_group_id='".$d['shopper_group_id']."' ";
			$q.= ",vendor_id ='".$vendor_id."' ";
			$q .= "WHERE user_id='" . $d["user_id"] . "' ";
		}
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
	function delete(&$d) {
		$db = new ps_DB;
		$ps_vendor_id = $_SESSION['ps_vendor_id'];
		
		$this->removeUsers( $d['user_id' ], $d );
		
		if( !is_array( $d['user_id'] )) {
			$d['user_id'] = array( $d['user_id'] );
		}
			
		foreach( $d['user_id'] as $user ) {
			if( !$this->validate_delete( $user ) ) {
				return false;
			}
			// Delete user_info entries
			$q  = "DELETE FROM #__{vm}_user_info ";
			$q .= "WHERE user_id='" . $user . "' ";
			$q .= "AND address_type='BT'";
			$db->query($q);
			$db->next_record();
		
			$q = "DELETE FROM #__{vm}_auth_user_vendor where user_id='$user' AND vendor_id='$ps_vendor_id'"; 
			$db->query($q);
			
			$q = "DELETE FROM #__{vm}_shopper_vendor_xref where user_id='$user' AND vendor_id='$ps_vendor_id'"; 
			$db->query($q);
		}
		
		return True;
	}
  
  
	/**************************************************************************
	* name: list_perms()
	* created by: pablo
	* description: lists the permission in a select box
	* parameters:
	* returns:
	**************************************************************************/
	function list_perms($name,$group_name) {
		global $perm,$VM_LANG;
		$auth = $_SESSION['auth'];
			
		$db = new ps_DB;
	  
		// Get users current permission value 
		$dvalue = $this->permissions[$auth["perms"]];
		echo "<select class=\"inputbox\" name=\"$name\">\n";
		echo "<option value=\"0\">".$VM_LANG->_PHPSHOP_SELECT ."</option>\n";
		while (list($key,$value) = each($this->permissions)) {
			// Display only those permission that this user can set
			if ($value <= $dvalue)
				if ($key == $group_name) {
					echo "<option value=\"".$key."\" selected>$key</option>\n";
				}
				else {
					echo "<option value=\"$key\">$key</option>\n";
				}
		}
		echo "</select>\n";
	}
	
	/**
	* Function to save User Information
	* into Joomla
	*/
	function saveUser( &$d ) {
		global $database, $my, $_VERSION;
		global $mosConfig_live_site, $mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_sitename;
		
		$aro_id = 'aro_id';
		$group_id = 'group_id';
		// Column names have changed (but why???)
		if( $_VERSION->PRODUCT == 'Joomla!' && $_VERSION->RELEASE >= 1.1 ) {
			$aro_id = 'id';
			$group_id = 'id';
		}
		
		$row = new mosUser( $database );
		if (!$row->bind( $_POST )) {
			echo "<script type=\"text/javascript\"> alert('".vmHtmlEntityDecode($row->getError())."');</script>\n";
		}
	
		$isNew 	= !$row->id;
		$pwd 	= '';
		
		// MD5 hash convert passwords
		if ($isNew) {
			// new user stuff
			if ($row->password == '') {
				$pwd = mosMakePassword();
				$row->password = md5( $pwd );
			} else {
				$pwd = $row->password;
				$row->password = md5( $row->password );
			}
			$row->registerDate = date( 'Y-m-d H:i:s' );
		} else {
			// existing user stuff
			if ($row->password == '') {
				// password set to null if empty
				$row->password = null;
			} else {
				if( !empty( $_POST['password'] )) {
					if( $row->password != @$_POST['password2'] ) {
						$d['error'] = vmHtmlEntityDecode(_REGWARN_VPASS2);
						return false;
					}
				}
				$row->password = md5( $row->password );
			}
		}
	
		// save usertype to usetype column
		$query = "SELECT name"
		. "\n FROM #__core_acl_aro_groups"
		. "\n WHERE `$group_id` = $row->gid"
		;
		$database->setQuery( $query );
		$usertype = $database->loadResult();
		$row->usertype = $usertype;
	
		// save params
		$params = mosGetParam( $_POST, 'params', '' );
		if (is_array( $params )) {
			$txt = array();
			foreach ( $params as $k=>$v) {
				$txt[] = "$k=$v";
			}
			$row->params = implode( "\n", $txt );
		}
	
		if (!$row->check()) {
			echo "<script type=\"text/javascript\"> alert('".vmHtmlEntityDecode($row->getError())."');</script>\n";
			return false;
		}
		if (!$row->store()) {
			echo "<script type=\"text/javascript\"> alert('".vmHtmlEntityDecode($row->getError())."');</script>\n";
			return false;
		}
		if ( $isNew ) {
			$newUserId = $row->id;
		}
		else
			$newUserId = false;
		
		$row->checkin();
		
		$_SESSION['session_user_params']= $row->params;
		
		// update the ACL
		if ( !$isNew ) {
			$query = "SELECT `$aro_id`"
			. "\n FROM #__core_acl_aro"
			. "\n WHERE value = '$row->id'"
			;
			$database->setQuery( $query );
			$aro_id = $database->loadResult();
	
			$query = "UPDATE #__core_acl_groups_aro_map"
			. "\n SET group_id = $row->gid"
			. "\n WHERE aro_id = $aro_id"
			;
			$database->setQuery( $query );
			$database->query() or die( $database->stderr() );
		}
	
		// for new users, email username and password
		if ($isNew) {
			$query = "SELECT email"
			. "\n FROM #__users"
			. "\n WHERE id = $my->id"
			;
			$database->setQuery( $query );
			$adminEmail = $database->loadResult();
	
			$subject = _NEW_USER_MESSAGE_SUBJECT;
			$message = sprintf ( _NEW_USER_MESSAGE, $row->name, $mosConfig_sitename, $mosConfig_live_site, $row->username, $pwd );
	
			if ($mosConfig_mailfrom != "" && $mosConfig_fromname != "") {
				$adminName 	= $mosConfig_fromname;
				$adminEmail = $mosConfig_mailfrom;
			} else {
				$query = "SELECT name, email"
				. "\n FROM #__users"
				// administrator
				. "\n WHERE gid = 25"
				;
				$database->setQuery( $query );
				$admins = $database->loadObjectList();
				$admin 		= $admins[0];
				$adminName 	= $admin->name;
				$adminEmail = $admin->email;
			}
			mosMail( $adminEmail, $adminName, $row->email, $subject, $message );
		}
		return $newUserId;
	}
	
	/**
	* Function to remove a user from Joomla
	*/
	function removeUsers( $cid, &$d ) {
		global $database, $acl, $my;
	
		if (!is_array( $cid ) ) {
			$cid = array( $cid );
		}
	
		if ( count( $cid ) ) {
			$obj = new mosUser( $database );
			foreach ($cid as $id) {
				// check for a super admin ... can't delete them
				$groups 	= $acl->get_object_groups( 'users', $id, 'ARO' );
				$this_group = strtolower( $acl->get_group_name( $groups[0], 'ARO' ) );
				if ( $this_group == 'super administrator' ) {
					$d["error"] = "You cannot delete a Super Administrator";
				} else if ( $id == $my->id ){
					$d["error"] = "You cannot delete Yourself!";
				} else if ( ( $this_group == 'administrator' ) && ( $my->gid == 24 ) ){
					$d["error"] = "You cannot delete another `Administrator` only `Super Administrators` have this power";
				} else {
					$obj->delete( $id );
					$d["error"] = $obj->getError();
				}
			}
		}
	}
}

?>
