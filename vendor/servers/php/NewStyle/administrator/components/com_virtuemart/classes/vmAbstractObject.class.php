<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: vmAbstractObject.class.php,v 1.1 2005/10/27 16:09:13 soeren_nb Exp $
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
 * The abstract class for all virtuemart entities
 * @abstract 
 * @author soeren
 */
class vmAbstractObject {
	/**
	 * Abstract function for validating input values before adding an item
	 * @abstract 
	 * @param array $d
	 * @return boolean True on success, false on failure
	 */
	function validate_add( &$d ) {
		return true;
	}
	/**
	 * Abstract function for validating input values before updating an item
	 * @abstract 
	 * @param array $d
	 * @return boolean True on success, false on failure
	 */
	function validate_update( &$d ) {
		return true;
	}
	/**
	 * Abstract function for validating input values before deleting an item
	 * @abstract 
	 * @param array $d
	 * @return boolean True on success, false on failure
	 */
	function validate_delete( &$d ) {
		return true;
	}
	/**
	 * Prepare the change of the pulish state of an item
	 *
	 * @param array $d The REQUEST array
	 * @return boolean True on success, false on failure
	 */
	function handlePublishState( $d ) {
		global $vmLogger;
		
		if( !empty($d['product_id'])) {
				$table_name = "#__{vm}_product";
				$publish_field_name = 'product_publish';
				$field_name = 'product_id';
		}
		elseif( !empty($d['category_id'])) {
				$table_name = "#__{vm}_category";
				$publish_field_name = 'category_publish';
				$field_name = 'category_id';
		}
		elseif( !empty( $d['payment_method_id'])) {
				$table_name = "#__{vm}_payment_method";
				$publish_field_name = 'payment_enabled';
				$field_name = 'payment_method_id';
		}
		else {
			$vmLogger->err( 'Could not determine the item type that is to be (un)published.');
			return false;
		}
		
		return $this->changePublishState( $d[$field_name], $d['task'], $table_name, $publish_field_name, $field_name );
		
	}
	/**
	 * Updates the $publish_field_name of the item(s) $itemId to Y or N ($task)
	 * in the table $table_name for field $field_name
	 *
	 * @param int/array $itemId (A single integer is later converted into an array)
	 * @param string $task Either 'publish' or 'unpublish'
	 * @param string $table_name
	 * @param string $publish_field_name
	 * @param string $field_name
	 * @return boolean
	 */
	function changePublishState( $itemId, $task, $table_name, $publish_field_name, $field_name ) {
		global $vmLogger;
		
		$db = new ps_DB();
		$value = ($task == 'unpublish') ? 'N' : 'Y';
		
		if( !is_array( $itemId )) {
			$set[] = $itemId;
		}
		else {
			$set =& $itemId;
		}
		$set = implode( ',', $set );
		
		$q = "UPDATE `$table_name` SET `$publish_field_name` = '$value' ";
		$q .= "WHERE FIND_IN_SET( `$field_name`, '$set' )";
		$q .= " AND `vendor_id`=".$_SESSION['ps_vendor_id'];
		$db->query( $q );
		
		$vmLogger->info($field_name.'(s) '.$set.' was/were '.$task.'ed.' );
		
		return true;
	}
}