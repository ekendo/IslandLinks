<?php
/**
* @version $Id: admin.newsfeeds.php 85 2005-09-15 23:12:03Z eddieajau $
* @package Joomla
* @subpackage Newsfeeds
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
		| $acl->acl_check( 'administration', 'edit', 'users', $my->usertype, 'components', 'com_newsfeeds' ))) {
	mosRedirect( 'index2.php', _NOT_AUTH );
}

require_once( $mainframe->getPath( 'admin_html' ) );
require_once( $mainframe->getPath( 'class' ) );

$task 	= mosGetParam( $_REQUEST, 'task', array(0) );
$cid 	= mosGetParam( $_POST, 'cid', array(0) );
$id 	= mosGetParam( $_GET, 'id', 0 );
if (!is_array( $cid )) {
	$cid = array(0);
}

switch ($task) {

	case 'new':
		editNewsFeed( 0, $option );
		break;

	case 'edit':
		editNewsFeed( $cid[0], $option );
		break;

	case 'editA':
		editNewsFeed( $id, $option );
		break;

	case 'save':
		saveNewsFeed( $option );
		break;

	case 'publish':
		publishNewsFeeds( $cid, 1, $option );
		break;

	case 'unpublish':
		publishNewsFeeds( $cid, 0, $option );
		break;

	case 'remove':
		removeNewsFeeds( $cid, $option );
		break;

	case 'cancel':
		cancelNewsFeed( $option );
		break;

	case 'orderup':
		orderNewsFeed( $cid[0], -1, $option );
		break;

	case 'orderdown':
		orderNewsFeed( $cid[0], 1, $option );
		break;

	default:
		showNewsFeeds( $option );
		break;
}

/**
* List the records
* @param string The current GET/POST option
*/
function showNewsFeeds( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$catid = $mainframe->getUserStateFromRequest( "catid{$option}", 'catid', 0 );
	$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit );
	$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );

	// get the total number of records
	$query = "SELECT COUNT(*)"
	. "\n FROM #__newsfeeds"
	. ( $catid ? "\n WHERE catid = $catid" : '' )
	;
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit );

	// get the subset (based on limits) of required records
	$query = "SELECT a.*, c.name AS catname, u.name AS editor"
	. "\n FROM #__newsfeeds AS a"
	. "\n LEFT JOIN #__categories AS c ON c.id = a.catid"
	. "\n LEFT JOIN #__users AS u ON u.id = a.checked_out"
	. ( $catid ? "\n WHERE a.catid = $catid" : '' )
	. "\n ORDER BY a.ordering"
	. "\n LIMIT $pageNav->limitstart, $pageNav->limit"
	;
	$database->setQuery( $query );

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	// build list of categories
	$javascript = 'onchange="document.adminForm.submit();"';
	$lists['category'] = mosAdminMenus::ComponentCategory( 'catid', $option, $catid, $javascript );

	HTML_newsfeeds::showNewsFeeds( $rows, $lists, $pageNav, $option );
}

/**
* Creates a new or edits and existing user record
* @param int The id of the user, 0 if a new entry
* @param string The current GET/POST option
*/
function editNewsFeed( $id, $option ) {
	global $database, $my;

	$catid = intval( mosGetParam( $_REQUEST, 'catid', 0 ) );

	$row = new mosNewsFeed( $database );
	// load the row from the db table
	$row->load( $id );

	if ($id) {
		// do stuff for existing records
		$row->checkout( $my->id );
	} else {
		// do stuff for new records
		$row->ordering 		= 0;
		$row->numarticles 	= 5;
		$row->cache_time 	= 3600;
		$row->published 	= 1;
	}

	// build the html select list for ordering
	$query = "SELECT a.ordering AS value, a.name AS text"
	. "\n FROM #__newsfeeds AS a"
	. "\n ORDER BY a.ordering"
	;
	$lists['ordering'] 			= mosAdminMenus::SpecificOrdering( $row, $id, $query, 1 );

	// build list of categories
	$lists['category'] 			= mosAdminMenus::ComponentCategory( 'catid', $option, intval( $row->catid ) );
	// build the html select list
	$lists['published'] 		= mosHTML::yesnoRadioList( 'published', 'class="inputbox"', $row->published );

	HTML_newsfeeds::editNewsFeed( $row, $lists, $option );
}

/**
* Saves the record from an edit form submit
* @param string The current GET/POST option
*/
function saveNewsFeed( $option ) {
	global $database, $my;

	$row = new mosNewsFeed( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
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

	mosRedirect( 'index2.php?option='. $option );
}

/**
* Publishes or Unpublishes one or more modules
* @param array An array of unique category id numbers
* @param integer 0 if unpublishing, 1 if publishing
* @param string The current GET/POST option
*/
function publishNewsFeeds( $cid, $publish, $option ) {
	global $database;

	if (count( $cid ) < 1) {
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script> alert('Select a module to $action'); window.history.go(-1);</script>\n";
		exit;
	}

	$cids = implode( ',', $cid );

	$query = "UPDATE #__newsfeeds"
	. "\n SET published = " . intval( $publish )
	. "\n WHERE id IN ( $cids )"
	. "\n AND ( checked_out = 0 OR ( checked_out = $my->id ) )"
	;
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if (count( $cid ) == 1) {
		$row = new mosNewsFeed( $database );
		$row->checkin( $cid[0] );
	}

	mosRedirect( 'index2.php?option='. $option );
}

/**
* Removes records
* @param array An array of id keys to remove
* @param string The current GET/POST option
*/
function removeNewsFeeds( &$cid, $option ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
		exit;
	}
	if (count( $cid )) {
		$cids = implode( ',', $cid );
		$query = "DELETE FROM #__newsfeeds"
		. "\n WHERE id IN ( $cids )"
		. "\n AND checked_out = 0"
		;
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
	}

	mosRedirect( 'index2.php?option='. $option );
}

/**
* Cancels an edit operation
* @param string The current GET/POST option
*/
function cancelNewsFeed( $option ) {
	global $database;

	$row = new mosNewsFeed( $database );
	$row->bind( $_POST );
	$row->checkin();
	mosRedirect( 'index2.php?option='. $option );
}

/**
* Moves the order of a record
* @param integer The id of the record to move
* @param integer The direction to reorder, +1 down, -1 up
* @param string The current GET/POST option
*/
function orderNewsFeed( $id, $inc, $option ) {
	global $database;

	$limit 		= mosGetParam( $_REQUEST, 'limit', 0 );
	$limitstart = mosGetParam( $_REQUEST, 'limitstart', 0 );
	$catid 		= intval( mosGetParam( $_REQUEST, 'catid', 0 ) );

	$row = new mosNewsFeed( $database );
	$row->load( $id );
	$row->move( $inc );

	mosRedirect( 'index2.php?option='. $option );
}
?>