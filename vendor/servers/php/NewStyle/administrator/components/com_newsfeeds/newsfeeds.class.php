<?php
/**
* @version $Id: newsfeeds.class.php 85 2005-09-15 23:12:03Z eddieajau $
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

/**
* @package Joomla
* @subpackage Newsfeeds
*/
class mosNewsFeed extends mosDBTable {
/** @var int Primary key */
	var $id					= null;
/** @var int */
	var $catid				= null;
/** @var string */
	var $name				= null;
/** @var string */
	var $link				= null;
/** @var string */
	var $filename			= null;
/** @var int */
	var $published			= null;
/** @var int */
	var $numarticles		= null;
/** @var int */
	var $cache_time			= null;
/** @var int */
	var $checked_out		= null;
/** @var time */
	var $checked_out_time	= null;
/** @var int */
	var $ordering			= null;

/**
* @param database A database connector object
*/
	function mosNewsFeed( &$db ) {
		$this->mosDBTable( '#__newsfeeds', 'id', $db );
	}
}
?>