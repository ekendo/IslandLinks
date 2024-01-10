<?php
/**
* @version $Id: toolbar.banners.html.php 108 2005-09-16 17:39:25Z stingrey $
* @package Joomla
* @subpackage Banners
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

/**
* @package Joomla
* @subpackage Banners
*/
class TOOLBAR_banners {
	/**
	* Draws the menu for to Edit a banner
	*/
	function _EDIT() {
		global $id;

		mosMenuBar::startTable();
		mosMenuBar::media_manager( 'banners' );
		mosMenuBar::spacer();
		mosMenuBar::save();
		mosMenuBar::spacer();
		if ( $id ) {
			// for existing content items the button is renamed `close`
			mosMenuBar::cancel( 'cancel', 'Close' );
		} else {
			mosMenuBar::cancel();
		}
		mosMenuBar::spacer();
		mosMenuBar::help( 'screen.banners.edit' );
		mosMenuBar::endTable();
	}
	function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::media_manager( 'banners' );
		mosMenuBar::spacer();
		mosMenuBar::publishList();
		mosMenuBar::spacer();
		mosMenuBar::unpublishList();
		mosMenuBar::spacer();
		mosMenuBar::deleteList();
		mosMenuBar::spacer();
		mosMenuBar::editListX();
		mosMenuBar::spacer();
		mosMenuBar::addNewX();
		mosMenuBar::spacer();
		mosMenuBar::help( 'screen.banners' );
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
}

/**
* @package Joomla
*/
class TOOLBAR_bannerClient {
	/**
	* Draws the menu for to Edit a client
	*/
	function _EDIT() {
		global $id;

		mosMenuBar::startTable();
		mosMenuBar::save( 'saveclient' );
		mosMenuBar::spacer();
		if ( $id ) {
			// for existing content items the button is renamed `close`
			mosMenuBar::cancel( 'cancelclient', 'Close' );
		} else {
			mosMenuBar::cancel( 'cancelclient' );
		}
		mosMenuBar::spacer();
		mosMenuBar::help( 'screen.banners.client.edit' );
		mosMenuBar::endTable();
	}
	/**
	* Draws the default menu
	*/
	function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::deleteList( '', 'removeclients' );
		mosMenuBar::spacer();
		mosMenuBar::editListX( 'editclient' );
		mosMenuBar::spacer();
		mosMenuBar::addNewX( 'newclient' );
		mosMenuBar::spacer();
		mosMenuBar::help( 'screen.banners.client' );
		mosMenuBar::endTable();
	}
}
?>