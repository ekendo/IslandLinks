<?php
/**
* @version $Id: index.php 87 2005-09-16 01:26:29Z rhuk $
* @package Joomla
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Restricted access' );

$tstart = mosProfiler::getmicrotime();
// needed to seperate the ISO number from the language file constant _ISO
$iso = explode( '=', _ISO );
// xml prolog
echo '<?xml version="1.0" encoding="'. $iso[1] .'"?' .'>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $mosConfig_sitename; ?> - Administration [Joomla]</title>
<link rel="stylesheet" href="templates/joomla_admin/css/template_css.css" type="text/css" />
<link rel="stylesheet" href="templates/joomla_admin/css/theme.css" type="text/css" />
<script language="JavaScript" src="<?php echo $mosConfig_live_site; ?>/includes/js/JSCookMenu_mini.js" type="text/javascript"></script>
<script language="JavaScript" src="<?php echo $mosConfig_live_site; ?>/administrator/includes/js/ThemeOffice/theme.js" type="text/javascript"></script>
<script language="JavaScript" src="<?php echo $mosConfig_live_site; ?>/includes/js/joomla.javascript.js" type="text/javascript"></script>
<?php
include_once( $mosConfig_absolute_path . '/editor/editor.php' );
initEditor();
?>
<meta http-equiv="Content-Type" content="text/html; <?php echo _ISO; ?>" />
<meta name="Generator" content="Joomla! Content Management System" />
<link rel="shortcut icon" href="<?php echo $mosConfig_live_site .'/images/favicon.ico';?>" />
</head>
<body onload="MM_preloadImages('images/help_f2.png','images/archive_f2.png','images/back_f2.png','images/cancel_f2.png','images/delete_f2.png','images/edit_f2.png','images/new_f2.png','images/preview_f2.png','images/publish_f2.png','images/save_f2.png','images/unarchive_f2.png','images/unpublish_f2.png','images/upload_f2.png')">

<div id="wrapper">
	<div id="header">
			<div id="joomla"><img src="templates/joomla_admin/images/header_text.png" alt="Joomla! Logo" /></div>
	</div>
</div>
<table width="100%" class="menubar" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td class="menubackgr" style="padding-left:5px;">
		<?php mosLoadAdminModule( 'fullmenu' );?>
	</td>
	<td class="menubackgr" align="right">
		<div id="wrapper1">
			<?php mosLoadAdminModules( 'header', 2 );?>
		</div>
	</td>
	<td class="menubackgr" align="right" style="padding-right:5px;">
		<a href="index2.php?option=logout" style="color: #333333; font-weight: bold">
			Logout</a>
		<strong><?php echo $my->username;?></strong>
	</td>
</tr>
</table>

<table width="100%" class="menubar" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td class="menudottedline" width="40%">
		<?php mosLoadAdminModule( 'pathway' );?>
	</td>
	<td class="menudottedline" align="right">
		<?php mosLoadAdminModule( 'toolbar' );?>
	</td>
</tr>
</table>

<br />
<?php mosLoadAdminModule( 'mosmsg' );?>

<div align="center" class="centermain">
	<div class="main">
		<?php mosMainBody_Admin(); ?>
	</div>
</div>

<table width="99%" border="0">
<tr>
	<td align="center">
		<div align="center">
			<?php echo $_VERSION->URL; ?>
		</div>
		<?php
		echo '<div class="smallgrey">';
		$tend = mosProfiler::getmicrotime();
		$totaltime = ($tend - $tstart);
		printf ("Page was generated in %f seconds", $totaltime);
		echo '</div>';
		?>
	</td>
</tr>
</table>
<?php mosLoadAdminModules( 'debug' );?>
</body>
</html>