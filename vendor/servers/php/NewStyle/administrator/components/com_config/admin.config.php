<?php
/**
* @version $Id: admin.config.php 123 2005-09-16 21:21:02Z Saka $
* @package Joomla
* @subpackage Config
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

if (!$acl->acl_check( 'administration', 'config', 'users', $my->usertype )) {
	mosRedirect( 'index2.php', _NOT_AUTH );
}

require_once( $mainframe->getPath( 'class' ) );
require_once( $mainframe->getPath( 'admin_html' ) );

switch ( $task ) {
	case 'apply':
	case 'save':
		saveconfig( $task );
		break;

	case 'cancel':
		mosRedirect( 'index2.php' );
		break;

	default:
		showconfig( $option );
		break;
}

/**
 * Show the configuration edit form
 * @param string The URL option
 */
function showconfig( $option) {
	global $database, $mosConfig_absolute_path, $mosConfig_editor;

	$row = new mosConfig();
	$row->bindGlobals();

	// compile list of the languages
	$langs 		= array();
	$menuitems 	= array();
	$lists 		= array();

// PRE-PROCESS SOME LISTS

	// -- Languages --

	if ($handle = opendir( $mosConfig_absolute_path . '/language/' )) {
		$i=0;
		while (false !== ($file = readdir( $handle ))) {
			if (!strcasecmp(substr($file,-4),".php") && $file <> "." && $file <> ".." && strcasecmp(substr($file,-11),".ignore.php")) {
				$langs[] = mosHTML::makeOption( substr($file,0,-4) );
			}
		}
	}

	// sort list of languages
	sort( $langs );
	reset( $langs );

	// -- Editors --

	// compile list of the editors
	$query = "SELECT element AS value, name AS text"
	. "\n FROM #__mambots"
	. "\n WHERE folder = 'editors'"
	. "\n AND published = 1"
	. "\n ORDER BY ordering, name"
	;
	$database->setQuery( $query );
	$edits = $database->loadObjectList();

	// -- Show/Hide --

	$show_hide = array(
		mosHTML::makeOption( 1, 'Hide' ),
		mosHTML::makeOption( 0, 'Show' ),
	);

	$show_hide_r = array(
		mosHTML::makeOption( 0, 'Hide' ),
		mosHTML::makeOption( 1, 'Show' ),
	);

	// -- menu items --

	$query = "SELECT id AS value, name AS text FROM #__menu"
	. "\n WHERE ( type='content_section' OR type='components' OR type='content_typed' )"
	. "\n AND published = 1"
	. "\n AND access = 0"
	. "\n ORDER BY name"
	;
	$database->setQuery( $query );
	$menuitems = array_merge( $menuitems, $database->loadObjectList() );


// SITE SETTINGS

	$lists['offline'] = mosHTML::yesnoRadioList( 'config_offline', 'class="inputbox"', $row->config_offline );

	if ( !$row->config_editor ) {
		$row->config_editor = '';
	}
	// build the html select list
	$lists['editor'] = mosHTML::selectList( $edits, 'config_editor', 'class="inputbox" size="1"', 'value', 'text', $row->config_editor );

	$listLimit = array(
		mosHTML::makeOption( 5, 5 ),
		mosHTML::makeOption( 10, 10 ),
		mosHTML::makeOption( 15, 15 ),
		mosHTML::makeOption( 20, 20 ),
		mosHTML::makeOption( 25, 25 ),
		mosHTML::makeOption( 30, 30 ),
		mosHTML::makeOption( 50, 50 ),
	);

	$lists['list_limit'] = mosHTML::selectList( $listLimit, 'config_list_limit', 'class="inputbox" size="1"', 'value', 'text', ( $row->config_list_limit ? $row->config_list_limit : 50 ) );

// DEBUG

	$lists['debug'] = mosHTML::yesnoRadioList( 'config_debug', 'class="inputbox"', $row->config_debug );

// DATABASE SETTINGS


// SERVER SETTINGS

	$lists['gzip'] = mosHTML::yesnoRadioList( 'config_gzip', 'class="inputbox"', $row->config_gzip );

	$errors = array(
		mosHTML::makeOption( -1, 'System Default' ),
		mosHTML::makeOption( 0, 'None' ),
		mosHTML::makeOption( E_ERROR|E_WARNING|E_PARSE, 'Simple' ),
		mosHTML::makeOption( E_ALL , 'Maximum' )
	);

	$lists['error_reporting'] = mosHTML::selectList( $errors, 'config_error_reporting', 'class="inputbox" size="1"', 'value', 'text', $row->config_error_reporting );


// LOCALE SETTINGS

	$lists['lang'] = mosHTML::selectList( $langs, 'config_lang', 'class="inputbox" size="1"', 'value', 'text', $row->config_lang );

	for ($i=-24;$i<=24;$i++) {
		$timeoffset[] = mosHTML::makeOption( $i, $i );
	}
	$lists['offset'] = mosHTML::selectList( $timeoffset, 'config_offset', 'class="inputbox" size="1"',	'value', 'text', $row->config_offset );


// MAIL SETTINGS

	$mailer = array(
		mosHTML::makeOption( 'mail', 'PHP mail function' ),
		mosHTML::makeOption( 'sendmail', 'Sendmail' ),
		mosHTML::makeOption( 'smtp', 'SMTP Server' )
	);
	$lists['mailer'] 	= mosHTML::selectList( $mailer, 'config_mailer', 'class="inputbox" size="1"', 'value', 'text', $row->config_mailer );

	$lists['smtpauth'] 	= mosHTML::yesnoRadioList( 'config_smtpauth', 'class="inputbox"', $row->config_smtpauth );


// CACHE SETTINGS

	$lists['caching'] 	= mosHTML::yesnoRadioList( 'config_caching', 'class="inputbox"', $row->config_caching );


// USER SETTINGS

	$lists['allowUserRegistration'] = mosHTML::yesnoRadioList( 'config_allowUserRegistration', 'class="inputbox"',	$row->config_allowUserRegistration );

	$lists['useractivation'] 		= mosHTML::yesnoRadioList( 'config_useractivation', 'class="inputbox"',	$row->config_useractivation );

	$lists['uniquemail'] 			= mosHTML::yesnoRadioList( 'config_uniquemail', 'class="inputbox"',	$row->config_uniquemail );

	$lists['shownoauth'] 			= mosHTML::yesnoRadioList( 'config_shownoauth', 'class="inputbox"', $row->config_shownoauth );


// META SETTINGS

	$lists['MetaAuthor']			= mosHTML::yesnoRadioList( 'config_MetaAuthor', 'class="inputbox"', $row->config_MetaAuthor );

	$lists['MetaTitle'] 			= mosHTML::yesnoRadioList( 'config_MetaTitle', 'class="inputbox"', $row->config_MetaTitle );


// STATISTICS SETTINGS

	$lists['log_searches'] 			= mosHTML::yesnoRadioList( 'config_enable_log_searches', 'class="inputbox"', $row->config_enable_log_searches );

	$lists['enable_stats'] 			= mosHTML::yesnoRadioList( 'config_enable_stats', 'class="inputbox"', $row->config_enable_stats );

	$lists['log_items']	 			= mosHTML::yesnoRadioList( 'config_enable_log_items', 'class="inputbox"', $row->config_enable_log_items );


// SEO SETTINGS

	$lists['sef'] 					= mosHTML::yesnoRadioList( 'config_sef', 'class="inputbox" onclick="javascript: if (document.adminForm.config_sef[1].checked) { alert(\'Remember to rename htaccess.txt to .htaccess\') }"', $row->config_sef );

	$lists['pagetitles'] 			= mosHTML::yesnoRadioList( 'config_pagetitles', 'class="inputbox"', $row->config_pagetitles );


// CONTENT SETTINGS

	$lists['link_titles'] 			= mosHTML::yesnoRadioList( 'config_link_titles', 'class="inputbox"', $row->config_link_titles );

	$lists['readmore'] 				= mosHTML::RadioList( $show_hide_r, 'config_readmore', 'class="inputbox"', $row->config_readmore, 'value', 'text' );

	$lists['vote'] 					= mosHTML::RadioList( $show_hide_r, 'config_vote', 'class="inputbox"', $row->config_vote, 'value', 'text' );



	$lists['hideAuthor'] 			= mosHTML::RadioList( $show_hide, 'config_hideAuthor', 'class="inputbox"', $row->config_hideAuthor, 'value', 'text' );

	$lists['hideCreateDate'] 		= mosHTML::RadioList( $show_hide, 'config_hideCreateDate', 'class="inputbox"', $row->config_hideCreateDate, 'value', 'text' );

	$lists['hideModifyDate'] 		= mosHTML::RadioList( $show_hide, 'config_hideModifyDate', 'class="inputbox"', $row->config_hideModifyDate, 'value', 'text' );

	$lists['hits'] 					= mosHTML::RadioList( $show_hide_r, 'config_hits', 'class="inputbox"', $row->config_hits, 'value', 'text' );

	if (is_writable( "$mosConfig_absolute_path/media/" )) {
		$lists['hidePdf'] 			= mosHTML::RadioList( $show_hide, 'config_hidePdf', 'class="inputbox"', $row->config_hidePdf, 'value', 'text' );
	} else {
		$lists['hidePdf'] 			= '<input type="hidden" name="config_hidepdf" value="1" /><strong>Yes</strong>';
	}

	$lists['hidePrint'] 			= mosHTML::RadioList( $show_hide, 'config_hidePrint', 'class="inputbox"', $row->config_hidePrint, 'value', 'text' );

	$lists['hideEmail'] 			= mosHTML::RadioList( $show_hide, 'config_hideEmail', 'class="inputbox"', $row->config_hideEmail, 'value', 'text' );

	$lists['icons'] 				= mosHTML::RadioList( $show_hide_r, 'config_icons', 'class="inputbox"', $row->config_icons, 'value', 'text' );

	$lists['back_button'] 			= mosHTML::RadioList( $show_hide_r, 'config_back_button', 'class="inputbox"', $row->config_back_button, 'value', 'text' );

	$lists['item_navigation'] 		= mosHTML::RadioList( $show_hide_r, 'config_item_navigation', 'class="inputbox"', $row->config_item_navigation, 'value', 'text' );

	$lists['ml_support'] 			= mosHTML::yesnoRadioList( 'config_ml_support', 'class="inputbox" onclick="javascript: if (document.adminForm.config_ml_support[1].checked) { alert(\'Remember to install the MambelFish component.\') }"', $row->config_ml_support );

	$lists['multipage_toc'] 		= mosHTML::RadioList( $show_hide_r, 'config_multipage_toc', 'class="inputbox"', $row->config_multipage_toc, 'value', 'text' );

// SHOW EDIT FORM

	HTML_config::showconfig( $row, $lists, $option );
}

/**
 * Save the configuration
 */
function saveconfig( $task ) {
	global $database, $mosConfig_absolute_path;

	$row = new mosConfig();
	if (!$row->bind( $_POST )) {
		mosRedirect( 'index2.php', $row->getError() );
	}

	$config = "<?php \n";
	$config .= $row->getVarText();
	$config .= "setlocale (LC_TIME, \$mosConfig_locale);\n";
	$config .= '?>';

	$fname = $mosConfig_absolute_path . '/configuration.php';

	$enable_write = mosGetParam($_POST,'enable_write',0);
	$oldperms = fileperms($fname);
		if ( $enable_write ) {
			@chmod( $fname, $oldperms | 0222);
		}

	if ( $fp = fopen($fname, 'w') ) {
		fputs($fp, $config, strlen($config));
		fclose($fp);
		if ($enable_write) {
			@chmod($fname, $oldperms);
		} else {
			if (mosGetParam($_POST,'disable_write',0))
				@chmod($fname, $oldperms & 0777555);
		} // if

		$msg = 'The Configuration Details have been updated';

		// apply file and directory permissions if requested by user
		$applyFilePerms = mosGetParam($_POST,'applyFilePerms',0) && $row->config_fileperms!='';
		$applyDirPerms = mosGetParam($_POST,'applyDirPerms',0) && $row->config_dirperms!='';
		if ($applyFilePerms || $applyDirPerms) {
			$mosrootfiles = array(
				'administrator',
				'cache',
				'components',
				'images',
				'language',
				'mambots',
				'media',
				'modules',
				'templates',
				'configuration.php'
			);
			$filemode = NULL;

			if ( $applyFilePerms ) {
				$filemode = octdec( $row->config_fileperms );
			}

			$dirmode = NULL;

			if ( $applyDirPerms ) {
				$dirmode = octdec( $row->config_dirperms );
			}

			foreach ($mosrootfiles as $file) {
				mosChmodRecursive( $mosConfig_absolute_path.'/'.$file, $filemode, $dirmode );
			}
		} // if

		switch ( $task ) {
			case 'apply':
				mosRedirect( 'index2.php?option=com_config&hidemainmenu=1', $msg );
				break;

			case 'save':
			default:
				mosRedirect( 'index2.php', $msg );
				break;
		}
	} else {
		if ($enable_write) {
			@chmod( $fname, $oldperms );
		}
		mosRedirect( 'index2.php', 'An Error Has Occurred! Unable to open config file to write!' );
	}
}
?>