<?php
/**
* @version $Id: wrapper.php 85 2005-09-15 23:12:03Z eddieajau $
* @package Joomla
* @subpackage Wrapper
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

/** load the html drawing class */
require_once( $mainframe->getPath( 'front_html' ) );

showWrap( $option );

function showWrap( $option ) {
	global $database, $Itemid, $mainframe;

	$menu = new mosMenu( $database );
	$menu->load( $Itemid );
	$params = new mosParameters( $menu->params );
	$params->def( 'back_button', $mainframe->getCfg( 'back_button' ) );
	$params->def( 'scrolling', 'auto' );
	$params->def( 'page_title', '1' );
	$params->def( 'pageclass_sfx', '' );
	$params->def( 'header', $menu->name );
	$params->def( 'height', '500' );
	$params->def( 'height_auto', '1' );
	$params->def( 'width', '100%' );
	$params->def( 'add', '1' );
	$url = $params->def( 'url', '' );

	$row = new stdClass();
	if ( $params->get( 'add' ) ) {
		// adds 'http://' if none is set
		if ( substr( $url, 0, 1 ) == '/' ) {
			// relative url in component. use server http_host.
			$row->url = 'http://'. $_SERVER['HTTP_HOST'] . $url;
		} elseif ( !strstr( $url, 'http' ) && !strstr( $url, 'https' ) ) {
			$row->url = 'http://'. $url;
		} else {
			$row->url = $url;
		}
	} else {
		$row->url = $url;
	}

	// auto height control
	if ( $params->def( 'height_auto' ) ) {
		$row->load = 'onload="iFrameHeight()"';
	} else {
		$row->load = '';
	}

	$mainframe->SetPageTitle($menu->name);

	HTML_wrapper::displayWrap( $row, $params, $menu );
}
?>