<?php
/**
* @version $Id: frontpage.class.php 85 2005-09-15 23:12:03Z eddieajau $
* @package Joomla
* @subpackage Content
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
* @subpackage Content
*/
class mosFrontPage extends mosDBTable {
	/** @var int Primary key */
	var $content_id	= null;
	/** @var int */
	var $ordering	= null;

	/**
	* @param database A database connector object
	*/
	function mosFrontPage( &$db ) {
		$this->mosDBTable( '#__content_frontpage', 'content_id', $db );
	}
}
?>