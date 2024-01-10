<?php
/**
* @version $Id: mod_mbt_transmenu.php
* @package Mambo
* @copyright (C) 2005 MamboTheme.com
* @license http://www.mambotheme.com
* Mambo is Free Software
*/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
/* Loads main class file
*/	
$params->set( 'module_name', 'ShopMenu' );
$params->set( 'module', 'vm_transmenu' );
$params->set( 'absPath', $mosConfig_absolute_path . '/modules/' . $params->get( 'module' ) );
$params->set( 'LSPath', $mosConfig_live_site . '/modules/' . $params->get( 'module' ) );
include_once( $params->get( 'absPath' ) .'/Shop_Menu.php' );

global $my, $db;

$mbtmenu= new Shop_Menu($db, $params);

$mbtmenu->genMenu();

?>

