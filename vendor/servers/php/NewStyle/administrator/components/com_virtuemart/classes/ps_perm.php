<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_perm.php,v 1.11.2.5 2006/04/21 17:05:17 soeren_nb Exp $
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
 * The permission handler class for VirtueMart.
 *
 */
class ps_perm {

	// Can be easily extended
	// Another permissions array must then
	// be changed in ps_user.php!!
	var $permissions = array(
		"shopper" 	=>  "1",
		"demo" 	=>  "2",
		"storeadmin" =>  "4",
		"admin" 	=>  "8"
		);
	
	/**
	* This function does the basic authentication
	* for a user in the shop.
	* It assigns permissions, the name, country, zip  and
	* the shopper group id with the user and the session.
	* @return array Authentication information
	*/
	function doAuthentication( $shopper_group ) {

		global $my, $acl, $user, $_VERSION;
		$db = new ps_DB;
		$auth = array();
		
		if( VM_PRICE_ACCESS_LEVEL != '' ) {
			// Get the usertype property when not present
			if( empty( $my->usertype ) ) {
				if( empty( $my->id )) { 
					$gid = 29; 
				}
				else {
					$gid = $my->gid;
				}
				$fieldname = ($_VERSION->RELEASE >= 1.1 && $_VERSION->PRODUCT == 'Joomla!' ) ? 'id' : 'group_id';
				$db->query( 'SELECT `name` FROM `#__core_acl_aro_groups` WHERE `'.$fieldname.'` ='.$gid );
				$db->next_record();
				$my->usertype = $db->f( 'name' );
			}
			
			$this->prepareACL();
			
			// Is the user allowed to see the prices?
			// this code will change when Joomla has a good ACL implementation
			if( is_callable( array( $user, 'authorize'))) {			
				$auth['show_prices']  = $user->authorize( 'virtuemart', 'prices' );	
			}
			else {
				$auth['show_prices']  = $acl->acl_check( 'virtuemart', 'prices', 'users', strtolower($my->usertype), null, null );
			}
		}
		else {
			$auth['show_prices'] = 1;
		}
		
		if (!empty($my->id)) { // user has already logged in

			$auth["user_id"]   = $my->id;
			$auth["username"] = $my->username;
	
			if ($this->is_registered_customer($my->id)) {
	
				$q = "SELECT perms,first_name,last_name,country,zip FROM #__{vm}_user_info WHERE user_id='".$my->id."'";
				$db->query($q);
				$db->next_record();
	
				$auth["perms"]  = $db->f("perms");
	
				$auth["first_name"] = $db->f("first_name");
				$auth["last_name"] = $db->f("last_name");
				$auth["country"] = $db->f("country");
				$auth["zip"] = $db->f("zip");
	
				// Shopper is the default value
				// We must prevent that Administrators or Managers are 'just' shoppers
				if( $auth["perms"] == "shopper" ) {
					if (stristr($my->usertype,"Administrator")) {
						$auth["perms"]  = "admin";
					}
					elseif (stristr($my->usertype,"Manager")) {
						$auth["perms"]  = "storeadmin";
					}
				}
				$auth["shopper_group_id"] = $shopper_group["shopper_group_id"];
				$auth["shopper_group_discount"] = $shopper_group["shopper_group_discount"];
				$auth["show_price_including_tax"] = $shopper_group["show_price_including_tax"];
				$auth["default_shopper_group"] = $shopper_group["default_shopper_group"];
				$auth["is_registered_customer"] = true;
			}
	
			// user is no registered customer
			else {
				if (stristr($my->usertype,"Administrator")) {
					$auth["perms"]  = "admin";
				}
				elseif (stristr($my->usertype,"Manager")) {
					$auth["perms"]  = "storeadmin";
				}
				else {
					$auth["perms"]  = "shopper"; // DEFAULT
				}
				$auth["shopper_group_id"] = $shopper_group["default_shopper_group"];
				$auth["shopper_group_discount"] = $shopper_group["shopper_group_discount"];
				$auth["show_price_including_tax"] = $shopper_group["show_price_including_tax"];
				$auth["default_shopper_group"] = 1;
				$auth["is_registered_customer"] = false;
			}

		} // user is not logged in
		else {

			$auth["user_id"] = 0;
			$auth["username"] = "demo";
			$auth["perms"]  = "";
			$auth["first_name"] = "guest";
			$auth["last_name"] = "";
			$auth["shopper_group_id"] = $shopper_group["shopper_group_id"];
			$auth["shopper_group_discount"] = $shopper_group["shopper_group_discount"];
			$auth["show_price_including_tax"] = $shopper_group["show_price_including_tax"];
			$auth["default_shopper_group"] = 1;
			$auth["is_registered_customer"] = false;
		}

		// register $auth into SESSION
		$_SESSION["auth"] = $auth;

		return $auth;

	}

	/**
	 * Validates the permission to do something.
	 *
	 * @param string $perms
	 * @return boolean Check successful or not
	 * @example $perm->check( 'admin', 'storeadmin' );
	 * 			returns true when the user is admin or storeadmin
	 */
	function check($perms) {

		$auth = $_SESSION["auth"];
		
		// Parse all permissions in argument, comma separated
		// It is assumed auth_user only has one group per user.
		if ($perms == "none") {
			return True;
		}
		else {
			$p1 = explode(",", $auth['perms']);
			$p2 = explode(",", $perms);
			while (list($key1, $value1) = each($p1)) {
				while (list($key2, $value2) = each($p2)) {
					if ($value1 == $value2) {
						return True;
					}
				}
			}
		}
		return False;

	}
	/**
	 * Checks if the user has higher permissions than $perm
	 *
	 * @param string $perm
	 * @return boolean
	 * @example $perm->hasHigherPerms( 'storeadmin' );
	 * 			returns true when user is admin
	 */
	function hasHigherPerms( $perm ) {
		$auth = $_SESSION["auth"];

		if( $this->permissions[$perm] <= $this->permissions[$auth['perms']] ) {
			return true;	
		}
		else {
			return false;
		}
	
	}
	
	/**
	 * lists the permission levels in a select box
	 * @author pablo
	 * @param string $name The name of the select element
	 * @param string $group_name The preselected key
	 */
	function list_perms( $name, $group_name ) {
		global $VM_LANG;
		$auth = $_SESSION['auth'];
			
		$db = new ps_DB;
	  
		// Get users current permission value 
		$dvalue = $this->permissions[$auth["perms"]];
		echo "<select class=\"inputbox\" name=\"$name\">\n";
		echo "<option value=\"0\">".$VM_LANG->_PHPSHOP_SELECT ."</option>\n";
		while( list($key,$value) = each($this->permissions) ) {
			// Display only those permission that this user can set
			if ($value <= $dvalue)
				if ($key == $group_name) {
					echo "<option value=\"".$key."\" selected=\"selected\">$key</option>\n";
				}
				else {
					echo "<option value=\"$key\">$key</option>\n";
				}
		}
		echo "</select>\n";
	}
	
	
	/**************************************************************************
	** name: is_registered_customer()
	** created by: soeren
	** description: Validates if someone is registered customer.
	**            by checking if one has a billing address
	** parameters: user_id
	** returns: true if the user has a BT address
	**          false if the user has none
	***************************************************************************/
	/**
	 * Check if a user is registered in the shop (=customer)
	 *
	 * @param int $user_id
	 * @return boolean
	 */
	function is_registered_customer($user_id) {
		global $page, $func, $auth;
		/**
		if( @is_bool( $auth["is_registered_customer"]) && ($page!="checkout.index" && strtolower($func)!="shopperupdate")) {
			return $auth["is_registered_customer"];
		}
		else {
		*/
			$db_check = new ps_DB;
			$q  = "SELECT #__users.id, #__{vm}_user_info.user_id from #__users, #__{vm}_user_info 
					WHERE #__users.id='" . $user_id . "' AND #__users.id=#__{vm}_user_info.user_id 
					AND #__{vm}_user_info.address_type='BT' AND #__{vm}_user_info.first_name != '' 
					AND #__{vm}_user_info.last_name != '' AND #__{vm}_user_info.city != ''";
			$db_check->query($q);
			
			// Query failed or not?
			if ($db_check->num_rows() > 0) {
				return true;
			}
			else {
				return false;
			}
		//}
	}
	
	/**
	 * HERE WE INSERT GROUPS THAT ARE ALLOWED TO VIEW PRICES
	 *
	 */
	function prepareACL() {
		global $acl;
		
		// The basic ACL integration in Mambo/Joomla is not awesome
		$child_groups = ps_perm::getChildGroups( '#__core_acl_aro_groups', 'g1.group_id, g1.name, COUNT(g2.name) AS level',	'g1.name', null, VM_PRICE_ACCESS_LEVEL );
		foreach( $child_groups as $child_group ) {
			ps_perm::_addToGlobalACL( 'virtuemart', 'prices', 'users', $child_group->name, null, null );
		}
		$admin_groups = ps_perm::getChildGroups( '#__core_acl_aro_groups', 'g1.group_id, g1.name, COUNT(g2.name) AS level',	'g1.name', null, 'Public Backend' );
		foreach( $admin_groups as $child_group ) {
			ps_perm::_addToGlobalACL( 'virtuemart', 'prices', 'users', $child_group->name, null, null );
		}
		
	}
	
	/**
	 * Function from an old Mambo phpgacl integration function
	 * @deprecated (but necessary, sigh!)
	 * @static 
	 * @param string $table
	 * @param string $fields
	 * @param string $groupby
	 * @param int $root_id
	 * @param string $root_name
	 * @param boolean $inclusive
	 * @return array
	 */
	function getChildGroups( $table, $fields, $groupby=null, $root_id=null, $root_name=null, $inclusive=true ) {
		global $database, $_VERSION;

		$root = new stdClass();
		$root->lft = 0;
		$root->rgt = 0;
		if( $_VERSION->PRODUCT == 'Joomla!' && $_VERSION->RELEASE >= 1.1 ) {
			$fields = str_replace( 'group_id', 'id', $fields );
		}
		
		if ($root_id) {
		} else if ($root_name) {
			$database->setQuery( "SELECT `lft`, `rgt` FROM `$table` WHERE `name`='$root_name'" );
			$database->loadObject( $root );
		}

		$where = '';
		if ($root->lft+$root->rgt != 0) {
			if ($inclusive) {
				$where = "WHERE g1.lft BETWEEN $root->lft AND $root->rgt";
			} else {
				$where = "WHERE g1.lft BETWEEN $root->lft+1 AND $root->rgt-1";
			}
		}

		$database->setQuery( "SELECT $fields"
			. "\nFROM $table AS g1"
			. "\nINNER JOIN $table AS g2 ON g1.lft BETWEEN g2.lft AND g2.rgt"
			. "\n$where"
			. ($groupby ? "\nGROUP BY $groupby" : "")
			. "\nORDER BY g1.lft"
		);

		//echo $database->getQuery();
		return $database->loadObjectList();
	}
	
	/**
	* This is a temporary function to allow 3PD's to add basic ACL checks for their
	* modules and components.  NOTE: this information will be compiled in the db
	* in future versions
	 * @static 
	 * @param unknown_type $aco_section_value
	 * @param unknown_type $aco_value
	 * @param unknown_type $aro_section_value
	 * @param unknown_type $aro_value
	 * @param unknown_type $axo_section_value
	 * @param unknown_type $axo_value
	 */
	function _addToGlobalACL( $aco_section_value, $aco_value,
		$aro_section_value, $aro_value, $axo_section_value=NULL, $axo_value=NULL ) {
		global $acl;
		$acl->acl[] = array( $aco_section_value, $aco_value, $aro_section_value, $aro_value, $axo_section_value, $axo_value );
		$acl->acl_count = count( $acl->acl );
	}
	
	/**
	 * Returns a tree with the children of the root group id
	 * @static 
	 * @param int $root_id
	 * @param string $root_name
	 * @param boolean $inclusive
	 * @return unknown
	 */
	function getGroupChildrenTree( $root_id=null, $root_name=null, $inclusive=true ) {
		global $database, $_VERSION;

		$tree = ps_perm::getChildGroups( '#__core_acl_aro_groups',
			'g1.group_id, g1.name, COUNT(g2.name) AS level',
			'g1.name',
			$root_id, $root_name, $inclusive );

		// first pass get level limits
		$n = count( $tree );
		$min = $tree[0]->level;
		$max = $tree[0]->level;
		for ($i=0; $i < $n; $i++) {
			$min = min( $min, $tree[$i]->level );
			$max = max( $max, $tree[$i]->level );
		}

		$indents = array();
		foreach (range( $min, $max ) as $i) {
			$indents[$i] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		}
		// correction for first indent
		$indents[$min] = '';

		$list = array();
		for ($i=$n-1; $i >= 0; $i--) {
			$shim = '';
			foreach (range( $min, $tree[$i]->level ) as $j) {
				$shim .= $indents[$j];
			}

			if (@$indents[$tree[$i]->level+1] == '.&nbsp;') {
				$twist = '&nbsp;';
			} else {
				$twist = "-&nbsp;";
			}

			if( $_VERSION->PRODUCT == 'Joomla!' && $_VERSION->RELEASE >= 1.1 ) {
				$tree[$i]->group_id = $tree[$i]->id;
			}
			$list[$i] = mosHTML::makeOption( $tree[$i]->group_id, $shim.$twist.$tree[$i]->name );
			if ($tree[$i]->level < @$tree[$i-1]->level) {
				$indents[$tree[$i]->level+1] = '.&nbsp;';
			}
		}

		ksort($list);
		return $list;
	}
}

?>