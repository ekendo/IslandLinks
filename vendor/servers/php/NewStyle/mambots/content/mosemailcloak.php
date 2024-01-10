<?php
/**
* @version $Id: mosemailcloak.php 85 2005-09-15 23:12:03Z eddieajau $
* @package Joomla
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

$_MAMBOTS->registerFunction( 'onPrepareContent', 'botMosEmailCloak' );

/**
* Mambot that Cloaks all emails in content from spambots via javascript
*/
function botMosEmailCloak( $published, &$row, &$params, $page=0 ) {
	global $database;

	// load mambot params info
	$query = "SELECT id"
	. "\n FROM #__mambots"
	. "\n WHERE element = 'mosemailcloak'"
	. "\n AND folder = 'content'"
	;
	$database->setQuery( $query );
 	$id 		= $database->loadResult();
 	$mambot 	= new mosMambot( $database );
  	$mambot->load( $id );
 	$botParams 	= new mosParameters( $mambot->params );
 	$mode		= $botParams->def( 'mode', 1 );

 	$search 	= "([[:alnum:]_\.\-]+)(\@[[:alnum:]\.\-]+\.+)([[:alnum:]\.\-]+)";
 	$search_text 	= "([[:alnum:][:space:][:punct:]][^<>]+)";

	// search for derivativs of link code <a href="mailto:email@amail.com">email@amail.com</a>
	// extra handling for inclusion of title and target attributes either side of href attribute
	$searchlink	= "(<a [[:alnum:] _\"\'=\@\.\-]*href=[\"\']mailto:". $search ."[\"\'][[:alnum:] _\"\'=\@\.\-]*>)". $search ."</a>";
	while( eregi( $searchlink, $row->text, $regs ) ) {
		$mail 		= $regs[2] . $regs[3] . $regs[4];
		$mail_text 	= $regs[5] . $regs[6] . $regs[7];

		// check to see if mail text is different from mail addy
		if ( $mail_text ) {
			$replacement 	= mosHTML::emailCloaking( $mail, $mode, $mail_text );
		} else {
			$replacement 	= mosHTML::emailCloaking( $mail, $mode );
		}

		// replace the found address with the js cloacked email
		$row->text 	= str_replace( $regs[0], $replacement, $row->text );
	}

	// search for derivativs of link code <a href="mailto:email@amail.com">anytext</a>
	// extra handling for inclusion of title and target attributes either side of href attribute
	$searchlink	= "(<a [[:alnum:] _\"\'=\@\.\-]*href=[\"\']mailto:". $search ."[\"\'][[:alnum:] _\"\'=\@\.\-]*)>". $search_text ."</a>";
	while( eregi( $searchlink, $row->text, $regs ) ) {
		$mail 		= $regs[2] . $regs[3] . $regs[4];
		$mail_text 	= $regs[5];

		$replacement 	= mosHTML::emailCloaking( $mail, $mode, $mail_text, 0 );

		// replace the found address with the js cloacked email
		$row->text 	= str_replace( $regs[0], $replacement, $row->text );
	}

	// search for plain text email@amail.com
	while( eregi( $search, $row->text, $regs ) ) {
		$mail = $regs[0];

		$replacement = mosHTML::emailCloaking( $mail, $mode );

		// replace the found address with the js cloacked email
		$row->text = str_replace( $regs[0], $replacement, $row->text );
	}
}
?>