<?php
/**
* @version $Id: toolbar.templates.php 85 2005-09-15 23:12:03Z eddieajau $
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

require_once( $mainframe->getPath( 'toolbar_html' ) );

$client = mosGetParam( $_REQUEST, 'client', '' );

switch ($task) {

	case 'view':
		TOOLBAR_templates::_VIEW();
		break;

	case 'edit_source':
		TOOLBAR_templates::_EDIT_SOURCE();
		break;

	case 'edit_css':
		TOOLBAR_templates::_EDIT_CSS();
		break;

	case 'assign':
		TOOLBAR_templates::_ASSIGN();
		break;

	case 'positions':
		TOOLBAR_templates::_POSITIONS();
		break;

	default:
		TOOLBAR_templates::_DEFAULT($client);
		break;
}
?>