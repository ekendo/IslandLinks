<?php
/**
* @version $Id: admin.templates.php 85 2005-09-15 23:12:03Z eddieajau $
* @package Joomla
* @subpackage Templates
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
if (!$acl->acl_check( 'administration', 'manage', 'users', $GLOBALS['my']->usertype, 'components', 'com_templates' )) {
	mosRedirect( 'index2.php', _NOT_AUTH );
}

require_once( $mainframe->getPath( 'admin_html' ) );
require_once( $mosConfig_absolute_path .'/administrator/components/com_templates/admin.templates.class.php' );
// XML library
require_once( $mosConfig_absolute_path .'/includes/domit/xml_domit_lite_include.php' );

$cid 	= mosGetParam( $_REQUEST, "cid", array(0) );
$client = mosGetParam( $_REQUEST, 'client', '' );

if (!is_array( $cid )) {
	$cid = array(0);
}

switch ($task) {
	case 'new':
		mosRedirect ( 'index2.php?option=com_installer&element=template&client='. $client );
		break;

	case 'edit_source':
		editTemplateSource( $cid[0], $option, $client );
		break;

	case 'save_source':
		saveTemplateSource( $option, $client );
		break;

	case 'edit_css':
		editTemplateCSS( $cid[0], $option, $client );
		break;

	case 'save_css':
		saveTemplateCSS( $option, $client );
		break;

	case 'remove':
		removeTemplate( $cid[0], $option, $client );
		break;

	case 'publish':
		defaultTemplate( $cid[0], $option, $client );
		break;

	case 'default':
		defaultTemplate( $cid[0], $option, $client );
		break;

	case 'assign':
		assignTemplate( $cid[0], $option, $client );
		break;

	case 'save_assign':
		saveTemplateAssign( $option, $client );
		break;

	case 'cancel':
		mosRedirect( 'index2.php?option='. $option .'&client='. $client );
		break;

	case 'positions':
		editPositions( $option );
		break;

	case 'save_positions':
		savePositions( $option );
		break;

	default:
		viewTemplates( $option, $client );
		break;
}


/**
* Compiles a list of installed, version 4.5+ templates
*
* Based on xml files found.  If no xml file found the template
* is ignored
*/
function viewTemplates( $option, $client ) {
	global $database, $mainframe;
	global $mosConfig_absolute_path, $mosConfig_list_limit;

	$limit = $mainframe->getUserStateFromRequest( 'viewlistlimit', 'limit', $mosConfig_list_limit );
	$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );

	if ($client == 'admin') {
		$templateBaseDir = mosPathName( $mosConfig_absolute_path . '/administrator/templates' );
	} else {
		$templateBaseDir = mosPathName( $mosConfig_absolute_path . '/templates' );
	}

	$rows = array();
	// Read the template dir to find templates
	$templateDirs		= mosReadDirectory($templateBaseDir);

	$id = intval( $client == 'admin' );

	if ($client=='admin') {
		$query = "SELECT template"
		. "\n FROM #__templates_menu"
		. "\n WHERE client_id = 1"
		. "\n AND menuid = 0"
		;
		$database->setQuery( $query );
	} else {
		$query = "SELECT template"
		. "\n FROM #__templates_menu"
		. "\n WHERE client_id = 0"
		. "\n AND menuid = 0"
		;
		$database->setQuery( $query );
	}
	$cur_template = $database->loadResult();

	$rowid = 0;
	// Check that the directory contains an xml file
	foreach($templateDirs as $templateDir) {
		$dirName = mosPathName($templateBaseDir . $templateDir);
		$xmlFilesInDir = mosReadDirectory($dirName,'.xml$');

		foreach($xmlFilesInDir as $xmlfile) {
			// Read the file to see if it's a valid template XML file
			$xmlDoc = new DOMIT_Lite_Document();
			$xmlDoc->resolveErrors( true );
			if (!$xmlDoc->loadXML( $dirName . $xmlfile, false, true )) {
				continue;
			}

			$root = &$xmlDoc->documentElement;

			if ($root->getTagName() != 'mosinstall') {
				continue;
			}
			if ($root->getAttribute( 'type' ) != 'template') {
				continue;
			}

			$row = new StdClass();
			$row->id 		= $rowid;
			$row->directory = $templateDir;
			$element 		= &$root->getElementsByPath('name', 1 );
			$row->name 		= $element->getText();

			$element 		= &$root->getElementsByPath('creationDate', 1);
			$row->creationdate = $element ? $element->getText() : 'Unknown';

			$element 		= &$root->getElementsByPath('author', 1);
			$row->author 	= $element ? $element->getText() : 'Unknown';

			$element 		= &$root->getElementsByPath('copyright', 1);
			$row->copyright = $element ? $element->getText() : '';

			$element 		= &$root->getElementsByPath('authorEmail', 1);
			$row->authorEmail = $element ? $element->getText() : '';

			$element 		= &$root->getElementsByPath('authorUrl', 1);
			$row->authorUrl = $element ? $element->getText() : '';

			$element 		= &$root->getElementsByPath('version', 1);
			$row->version 	= $element ? $element->getText() : '';

			// Get info from db
			if ($cur_template == $templateDir) {
				$row->published	= 1;
			} else {
				$row->published = 0;
			}

			$row->checked_out = 0;
			$row->mosname = strtolower( str_replace( ' ', '_', $row->name ) );

			// check if template is assigned
			$query = "SELECT COUNT(*)"
			. "\n FROM #__templates_menu"
			. "\n WHERE client_id = 0"
			. "\n AND template = '$row->directory'"
			. "\n AND menuid <> 0"
			;
			$database->setQuery( $query );
			$row->assigned = $database->loadResult() ? 1 : 0;

			$rows[] = $row;
			$rowid++;
		}
	}

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( count( $rows ), $limitstart, $limit );

	$rows = array_slice( $rows, $pageNav->limitstart, $pageNav->limit );

	HTML_templates::showTemplates( $rows, $pageNav, $option, $client );
}


/**
* Publish, or make current, the selected template
*/
function defaultTemplate( $p_tname, $option, $client ) {
	global $database;

	if ($client=='admin') {
		$query = "DELETE FROM #__templates_menu"
		. "\n WHERE client_id = 1"
		. "\n AND menuid = 0"
		;
		$database->setQuery( $query );
		$database->query();

		$query = "INSERT INTO #__templates_menu"
		. "\n SET client_id = 1, template = '$p_tname', menuid = 0"
		;
		$database->setQuery( $query );
		$database->query();
	} else {
		$query = "DELETE FROM #__templates_menu"
		. "\n WHERE client_id = 0"
		. "\n AND menuid = 0"
		;
		$database->setQuery( $query );
		$database->query();

		$query = "INSERT INTO #__templates_menu"
		. "\n SET client_id = 0, template = '$p_tname', menuid = 0"
		;
		$database->setQuery( $query );
		$database->query();

		$_SESSION['cur_template'] = $p_tname;
	}

	mosRedirect('index2.php?option='. $option .'&client='. $client);
}

/**
* Remove the selected template
*/
function removeTemplate( $cid, $option, $client ) {
	global $database;

	$client_id = $client=='admin' ? 1 : 0;

	$query = "SELECT template"
	. "\n FROM #__templates_menu"
	. "\n WHERE client_id = $client_id"
	. "\n AND menuid = 0"
	;
	$database->setQuery( $query );
	$cur_template = $database->loadResult();

	if ($cur_template == $cid) {
		echo "<script>alert(\"You can not delete template in use.\"); window.history.go(-1); </script>\n";
		exit();
	}

	// Un-assign
	$query = "DELETE FROM #__templates_menu"
	. "\n WHERE template = '$cid'"
	. "\n AND client_id = $client_id"
	. "\n AND menuid <> 0"
	;
	$database->setQuery( $query );
	$database->query();

	mosRedirect( 'index2.php?option=com_installer&element=template&client='. $client .'&task=remove&cid[]='. $cid );
}

function editTemplateSource( $p_tname, $option, $client ) {
	global $mosConfig_absolute_path;

	if ( $client == 'admin' ) {
		$file = $mosConfig_absolute_path .'/administrator/templates/'. $p_tname .'/index.php';
	} else {
		$file = $mosConfig_absolute_path .'/templates/'. $p_tname .'/index.php';
	}

	if ( $fp = fopen( $file, 'r' ) ) {
		$content = fread( $fp, filesize( $file ) );
		$content = htmlspecialchars( $content );

		HTML_templates::editTemplateSource( $p_tname, $content, $option, $client );
	} else {
		mosRedirect( 'index2.php?option='. $option .'&client='. $client, 'Operation Failed: Could not open'. $file );
	}
}


function saveTemplateSource( $option, $client ) {
	global $mosConfig_absolute_path;

	$template 		= mosGetParam( $_POST, 'template', '' );
	$filecontent 	= mosGetParam( $_POST, 'filecontent', '', _MOS_ALLOWHTML );

	if ( !$template ) {
		mosRedirect( 'index2.php?option='. $option .'&client='. $client, 'Operation failed: No template specified.' );
	}
	if ( !$filecontent ) {
		mosRedirect( 'index2.php?option='. $option .'&client='. $client, 'Operation failed: Content empty.' );
	}

	if ( $client == 'admin' ) {
		$file = $mosConfig_absolute_path .'/administrator/templates/'. $template .'/index.php';
	} else {
		$file = $mosConfig_absolute_path .'/templates/'. $template .'/index.php';
	}

	$enable_write = mosGetParam($_POST,'enable_write',0);
	$oldperms = fileperms($file);
	if ($enable_write) @chmod($file, $oldperms | 0222);

	clearstatcache();
	if ( is_writable( $file ) == false ) {
		mosRedirect( 'index2.php?option='. $option , 'Operation failed: '. $file .' is not writable.' );
	}

	if ( $fp = fopen ($file, 'w' ) ) {
		fputs( $fp, stripslashes( $filecontent ), strlen( $filecontent ) );
		fclose( $fp );
		if ($enable_write) {
			@chmod($file, $oldperms);
		} else {
			if (mosGetParam($_POST,'disable_write',0))
				@chmod($file, $oldperms & 0777555);
		} // if
		mosRedirect( 'index2.php?option='. $option .'&client='. $client );
	} else {
		if ($enable_write) @chmod($file, $oldperms);
		mosRedirect( 'index2.php?option='. $option .'&client='. $client, 'Operation failed: Failed to open file for writing.' );
	}

}

function editTemplateCSS( $p_tname, $option, $client ) {
	global $mosConfig_absolute_path;

	if ( $client == 'admin' ) {
		$file = $mosConfig_absolute_path .'/administrator/templates/'. $p_tname .'/css/template_css.css';
	} else {
		$file = $mosConfig_absolute_path .'/templates/'. $p_tname .'/css/template_css.css';
	}

	if ($fp = fopen( $file, 'r' )) {
		$content = fread( $fp, filesize( $file ) );
		$content = htmlspecialchars( $content );

		HTML_templates::editCSSSource( $p_tname, $content, $option, $client );
	} else {
		mosRedirect( 'index2.php?option='. $option .'&client='. $client, 'Operation Failed: Could not open'. $file );
	}
}


function saveTemplateCSS( $option, $client ) {
	global $mosConfig_absolute_path;
	$template = mosGetParam( $_POST, 'template', '' );
	$filecontent = mosGetParam( $_POST, 'filecontent', '', _MOS_ALLOWHTML );

	if ( !$template ) {
		mosRedirect( 'index2.php?option='. $option .'&client='. $client, 'Operation failed: No template specified.' );
	}

	if ( !$filecontent ) {
		mosRedirect( 'index2.php?option='. $option .'&client='. $client, 'Operation failed: Content empty.' );
	}

	if ( $client == 'admin' ) {
		$file = $mosConfig_absolute_path .'/administrator/templates/'. $template .'/css/template_css.css';
	} else {
		$file = $mosConfig_absolute_path .'/templates/'. $template .'/css/template_css.css';
	}

	$enable_write = mosGetParam($_POST,'enable_write',0);
	$oldperms = fileperms($file);
	if ($enable_write) @chmod($file, $oldperms | 0222);

	clearstatcache();
	if ( is_writable( $file ) == false ) {
		mosRedirect( 'index2.php?option='. $option .'&client='. $client, 'Operation failed: The file is not writable.' );
	}

	if ($fp = fopen ($file, 'w')) {
		fputs( $fp, stripslashes( $filecontent ) );
		fclose( $fp );
		if ($enable_write) {
			@chmod($file, $oldperms);
		} else {
			if (mosGetParam($_POST,'disable_write',0))
				@chmod($file, $oldperms & 0777555);
		} // if
		mosRedirect( 'index2.php?option='. $option );
	} else {
		if ($enable_write) @chmod($file, $oldperms);
		mosRedirect( 'index2.php?option='. $option .'&client='. $client, 'Operation failed: Failed to open file for writing.' );
	}

}


function assignTemplate( $p_tname, $option, $client ) {
	global $database;

	// get selected pages for $menulist
	if ( $p_tname ) {

		$query = "SELECT menuid AS value"
		. "\n FROM #__templates_menu"
		. "\n WHERE client_id = 0"
		. "\n AND template = '$p_tname'"
		;
		$database->setQuery( $query );
		$lookup = $database->loadObjectList();
	}

	// build the html select list
	$menulist = mosAdminMenus::MenuLinks( $lookup, 0, 1 );

	HTML_templates::assignTemplate( $p_tname, $menulist, $option, $client );
}


function saveTemplateAssign( $option, $client ) {
	global $database;

	$menus 		= mosGetParam( $_POST, 'selections', array() );
	$template 	= mosGetParam( $_POST, 'template', '' );

	$query = "DELETE FROM #__templates_menu"
	. "\n WHERE client_id = 0"
	. "\n AND template = $template"
	. "\n AND menuid <> 0"
	;
	$database->setQuery( $query );
	$database->query();

	if ( !in_array( '', $menus ) ) {
		foreach ( $menus as $menuid ){
			// If 'None' is not in array
			if ( $menuid <> -999 ) {
				// check if there is already a template assigned to this menu item
				$query = "DELETE FROM #__templates_menu"
				. "\n WHERE client_id = 0"
				. "\n AND menuid = $menuid"
				;
				$database->setQuery( $query );
				$database->query();

				$query = "INSERT INTO #__templates_menu"
				. "\n SET client_id = 0, template = '$template', menuid = $menuid"
				;
				$database->setQuery( $query );
				$database->query();
			}
		}
	}

	mosRedirect( 'index2.php?option='. $option .'&client='. $client );
}


/**
*/
function editPositions( $option ) {
	global $database;

	$query = "SELECT *"
	. "\n FROM #__template_positions"
	;
	$database->setQuery( $query );
	$positions = $database->loadObjectList();

	HTML_templates::editPositions( $positions, $option );
}

/**
*/
function savePositions( $option ) {
	global $database;

	$positions 		= mosGetParam( $_POST, 'position', array() );
	$descriptions 	= mosGetParam( $_POST, 'description', array() );

	$query = "DELETE FROM #__template_positions";
	$database->setQuery( $query );
	$database->query();

	foreach ($positions as $id=>$position) {
		$position = trim( $database->getEscaped( $position ) );
		$description = mosGetParam( $descriptions, $id, '' );
		if ($position != '') {
			$id = intval( $id );
			$query = "INSERT INTO #__template_positions"
			. "\n VALUES ( $id, '$position', '$description' )"
			;
			$database->setQuery( $query );
			$database->query();
		}
	}
	mosRedirect( 'index2.php?option='. $option .'&task=positions', 'Positions saved' );
}
?>