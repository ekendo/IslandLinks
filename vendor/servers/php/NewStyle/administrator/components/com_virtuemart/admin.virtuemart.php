<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: admin.virtuemart.php,v 1.14.2.4 2006/03/14 18:42:04 soeren_nb Exp $
* @package VirtueMart
* @subpackage core
* @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/

defined( '_PSHOP_ADMIN' ) or define( '_PSHOP_ADMIN', '1' );

$no_menu = mosGetParam( $_REQUEST, 'no_menu', 0 );
global $VM_LANG;
/*** INSTALLER SECTION ***/
include( $mosConfig_absolute_path.'/administrator/components/com_virtuemart/install.virtuemart.php' );

if (isset($_REQUEST['install_type']) && file_exists( $mosConfig_absolute_path.'/administrator/components/'.$option.'/install.php' )) {
	virtuemart_is_installed();
	include( $mosConfig_absolute_path.'/administrator/components/'.$option.'/install.php' );

	/** can be update and newinstall **/
	$install_type = mosgetparam( $_REQUEST, 'install_type', 'newinstall' );

	/** true or false **/
	$install_sample_data = mosgetparam( $_GET, 'install_sample_data', false );

	installvirtuemart( $install_type, $install_sample_data );
	$error = "";
	$page = "store.index";

	$installfile = dirname( __FILE__ ) . "/install.php";
	if( !@unlink( $installfile ) ) {
		echo "<br /><span class=\"message\">Something went wrong when trying to delete the file <strong>install.php</strong>!<br />";
		echo "You'll have to delete the file manually before being able to use VirtueMart!</span>";
	}

}
elseif( file_exists( $mosConfig_absolute_path.'/administrator/components/'.$option.'/install.php' )) {
	virtuemart_is_installed();
	com_install();
	exit();
	
}
/*** END INSTALLER ***/


/* Load the virtuemart main parse code */
require_once( $mosConfig_absolute_path.'/components/'.$option.'/virtuemart_parser.php' );

if( empty( $page )) {
	$page = 'store.index';
}

$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit );
$limitstart = $mainframe->getUserStateFromRequest( "view{$page}{$product_id}{$category_id}limitstart", 'limitstart', 0 );

if (defined('_DONT_VIEW_PAGE') && !isset($install_type) ) {
        echo "<script type=\"text/javascript\">alert('$error. Your permissions: ".$_SESSION['auth']['perms']."')</script>\n";
}

// renew Page-Information
$my_page= explode ( '.', $page );
$modulename = $my_page[0];
$pagename = $my_page[1];

$_SESSION['last_page'] = $page;

if( $no_menu != 1 ) {
	include(ADMINPATH.'header.php');
}
// Include the Stylesheet
echo '<link rel="stylesheet" href="components/'.$option.'/admin.styles.css" type="text/css" />';
echo '<link href="../components/'.$option.'/css/shop.css" type="text/css" rel="stylesheet" media="screen, projection" />';
echo '<script type="text/javascript" src="../components/'.$option.'/js/functions.js"></script>';

// Load PAGE
if(file_exists(PAGEPATH.$modulename.".".$pagename.".php")) {
	include( PAGEPATH.$modulename.".".$pagename.".php" );
}
else {
	include( PAGEPATH.'store.index.php' );
}
include_once( ADMINPATH. 'version.php' );

echo '<br style="clear:both;"/><div class="smallgrey" align="center">'
                .$VMVERSION->PRODUCT.' '.$VMVERSION->RELEASE
                .' (<a href="http://virtuemart.net/index2.php?option=com_versions&amp;catid=1&amp;myVersion='.@$VMVERSION->RELEASE.'" onclick="javascript:void window.open(\'http://virtuemart.net/index2.php?option=com_versions&catid=1&myVersion='.$VMVERSION->RELEASE .'\', \'win2\', \'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=580,directories=no,location=no\'); return false;" title="VirtueMart Version Check" target="_blank">Check for latest version</a>)</div>';

if( DEBUG == '1' ) {
        // Load PAGE
	include( PAGEPATH."shop.debug.php" );
}
if( defined( 'vmToolTipCalled')) {
	echo '<script language="Javascript" type="text/javascript" src="'. $mosConfig_live_site.'/components/'.$option.'/js/wz_tooltip.js"></script>';
}
?>
