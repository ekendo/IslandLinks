<?php
/**
* @version $Id: admin.contact.php 85 2005-09-15 23:12:03Z eddieajau $
* @package Joomla
* @subpackage Contact
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

// ensure user has access to this function
if (!($acl->acl_check( 'administration', 'edit', 'users', $my->usertype, 'components', 'all' )
		| $acl->acl_check( 'administration', 'edit', 'users', $my->usertype, 'components', 'com_contact' ))) {
	mosRedirect( 'index2.php', _NOT_AUTH );
}

require_once( $mainframe->getPath( 'admin_html' ) );
require_once( $mainframe->getPath( 'class' ) );

$id 	= mosGetParam( $_GET, 'id', 0 );
$cid 	= mosGetParam( $_POST, 'cid', array(0) );
if (!is_array( $cid )) {
	$cid = array(0);
}

switch ($task) {

	case 'new':
		editContact( '0', $option);
		break;

	case 'edit':
		editContact( $cid[0], $option );
		break;

	case 'editA':
		editContact( $id, $option );
		break;

	case 'save':
		saveContact( $option );
		break;

	case 'remove':
		removeContacts( $cid, $option );
		break;

	case 'publish':
		changeContact( $cid, 1, $option );
		break;

	case 'unpublish':
		changeContact( $cid, 0, $option );
		break;

	case 'orderup':
		orderContacts( $cid[0], -1, $option );
		break;

	case 'orderdown':
		orderContacts( $cid[0], 1, $option );
		break;

	case 'cancel':
		cancelContact();
		break;

	default:
		showContacts( $option );
		break;
}

/**
* List the records
* @param string The current GET/POST option
*/
function showContacts( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$catid 		= $mainframe->getUserStateFromRequest( "catid{$option}", 'catid', 0 );
	$limit 		= $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit );
	$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
	$search 	= $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
	$search 	= $database->getEscaped( trim( strtolower( $search ) ) );

	if ( $search ) {
		$where[] = "cd.name LIKE '%$search%'";
	}
	if ( $catid ) {
		$where[] = "cd.catid = '$catid'";
	}
	if ( isset( $where ) ) {
		$where = "\n WHERE ". implode( ' AND ', $where );
	} else {
		$where = '';
	}

	// get the total number of records
	$query = "SELECT COUNT(*)"
	. "\n FROM #__contact_details AS cd"
	. $where
	;
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	// get the subset (based on limits) of required records
	$query = "SELECT cd.*, cc.title AS category, u.name AS user, v.name as editor"
	. "\n FROM #__contact_details AS cd"
	. "\n LEFT JOIN #__categories AS cc ON cc.id = cd.catid"
	. "\n LEFT JOIN #__users AS u ON u.id = cd.user_id"
	. "\n LEFT JOIN #__users AS v ON v.id = cd.checked_out"
	. $where
	. "\n ORDER BY cd.catid, cd.ordering, cd.name ASC"
	. "\n LIMIT $pageNav->limitstart, $pageNav->limit"
	;
	$database->setQuery( $query );
	$rows = $database->loadObjectList();

	// build list of categories
	$javascript = 'onchange="document.adminForm.submit();"';
	$lists['catid'] = mosAdminMenus::ComponentCategory( 'catid', 'com_contact_details', intval( $catid ), $javascript );

	HTML_contact::showcontacts( $rows, $pageNav, $search, $option, $lists );
}

/**
* Creates a new or edits and existing user record
* @param int The id of the record, 0 if a new entry
* @param string The current GET/POST option
*/
function editContact( $id, $option ) {
	global $database, $my;
	global $mosConfig_absolute_path;

	$row = new mosContact( $database );
	// load the row from the db table
	$row->load( $id );

	if ($id) {
		// do stuff for existing records
		$row->checkout($my->id);
	} else {
		// do stuff for new records
		$row->imagepos = 'top';
		$row->ordering = 0;
		$row->published = 1;
	}
	$lists = array();

	// build the html select list for ordering
	$query = "SELECT ordering AS value, name AS text"
	. "\n FROM #__contact_details"
	. "\n WHERE published >= 0"
	. "\n AND catid = '$row->catid'"
	. "\n ORDER BY ordering"
	;
	$lists['ordering'] 			= mosAdminMenus::SpecificOrdering( $row, $id, $query, 1 );

	// build list of users
	$lists['user_id'] 			= mosAdminMenus::UserSelect( 'user_id', $row->user_id, 1, NULL, 'name', 0 );
	// build list of categories
	$lists['catid'] 			= mosAdminMenus::ComponentCategory( 'catid', 'com_contact_details', intval( $row->catid ) );
	// build the html select list for images
	$lists['image'] 			= mosAdminMenus::Images( 'image', $row->image );
	// build the html select list for the group access
	$lists['access'] 			= mosAdminMenus::Access( $row );
	// build the html radio buttons for published
	$lists['published'] 		= mosHTML::yesnoradioList( 'published', '', $row->published );
	// build the html radio buttons for default
	$lists['default_con'] 		= mosHTML::yesnoradioList( 'default_con', '', $row->default_con );

	// get params definitions
	$file = $mosConfig_absolute_path .'/administrator/components/com_contact/contact_items.xml';
	$params = new mosParameters( $row->params, $file, 'component' );

	HTML_contact::editcontact( $row, $lists, $option, $params );
}

/**
* Saves the record from an edit form submit
* @param string The current GET/POST option
*/
function saveContact( $option ) {
	global $database;

	$row = new mosContact( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	// save params
	$params = mosGetParam( $_POST, 'params', '' );
	if (is_array( $params )) {
		$txt = array();
		foreach ( $params as $k=>$v) {
			$txt[] = "$k=$v";
		}
		$row->params = implode( "\n", $txt );
	}

	// pre-save checks
	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	// save the changes
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->checkin();
	$row->updateOrder();
	if ($row->default_con) {
		$query = "UPDATE #__contact_details"
		. "\n SET default_con = 0"
		. "\n WHERE id <> $row->id"
		. "\n AND default_con = 1"
		;
		$database->setQuery( $query );
		$database->query();
	}

	mosRedirect( "index2.php?option=$option" );
}

/**
* Removes records
* @param array An array of id keys to remove
* @param string The current GET/POST option
*/
function removeContacts( &$cid, $option ) {
	global $database;

	if (count( $cid )) {
		$cids = implode( ',', $cid );
		$query = "DELETE FROM #__contact_details"
		. "\n WHERE id IN ( $cids )"
		;
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
	}

	mosRedirect( "index2.php?option=$option" );
}

/**
* Changes the state of one or more content pages
* @param array An array of unique category id numbers
* @param integer 0 if unpublishing, 1 if publishing
* @param string The current option
*/
function changeContact( $cid=null, $state=0, $option ) {
	global $database, $my;

	if (count( $cid ) < 1) {
		$action = $state == 1 ? 'publish' : 'unpublish';
		echo "<script> alert('Select a record to $action'); window.history.go(-1);</script>\n";
		exit;
	}

	$cids = implode( ',', $cid );

	$query = "UPDATE #__contact_details"
	. "\n SET published = " . intval( $state )
	. "\n WHERE id IN ( $cids )"
	. "\n AND ( checked_out = 0 OR ( checked_out = $my->id ) )"
	;
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if (count( $cid ) == 1) {
		$row = new mosContact( $database );
		$row->checkin( intval( $cid[0] ) );
	}

	mosRedirect( "index2.php?option=$option" );
}

/** JJC
* Moves the order of a record
* @param integer The increment to reorder by
*/
function orderContacts( $uid, $inc, $option ) {
	global $database;

	$row = new mosContact( $database );
	$row->load( $uid );
	$row->move( $inc, "published >= 0" );

	mosRedirect( "index2.php?option=$option" );
}

/** PT
* Cancels editing and checks in the record
*/
function cancelContact() {
	global $database;

	$row = new mosContact( $database );
	$row->bind( $_POST );
	$row->checkin();
	mosRedirect('index2.php?option=com_contact');
}
?>