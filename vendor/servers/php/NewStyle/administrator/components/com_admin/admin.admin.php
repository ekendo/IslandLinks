<?php
/**
* @version $Id: admin.admin.php 106 2005-09-16 11:36:10Z eddieajau $
* @package Joomla
* @subpackage Admin
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
require_once( $mainframe->getPath( 'admin_html' ) );

switch ($task) {

	case 'clean_cache':
		mosCache::cleanCache( 'com_content' );
		mosRedirect( 'index2.php', 'Content caches cleaned' );
		break;

	case 'clean_all_cache':
		mosCache::cleanCache();
		mosRedirect( 'index2.php', 'All caches cleaned' );
		break;

	case 'redirect':
		$goto = trim( strtolower( mosGetParam( $_REQUEST, 'link' ) ) );
		if ($goto == 'null') {
			$msg = 'There is no link associated with this item';
			mosRedirect( 'index2.php?option=com_admin&task=listcomponents', $msg );
			exit();
		}
		$goto = str_replace( "'", '', $goto );
		mosRedirect($goto);
		break;

	case 'listcomponents':
		HTML_admin_misc::ListComponents();
		break;

	case 'sysinfo':
		HTML_admin_misc::system_info( $version, $option );
		break;

	case 'changelog':
		HTML_admin_misc::changelog();
		break;

	case 'help':
		HTML_admin_misc::help();
		break;

	case 'preview':
		HTML_admin_misc::preview();
		break;

	case 'preview2':
		HTML_admin_misc::preview( 1 );
		break;

	case 'cpanel':
	default:
		HTML_admin_misc::controlPanel();
		break;

}
?>