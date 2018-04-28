<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: store.shipping_module_form.php,v 1.3 2005/09/29 20:02:18 soeren_nb Exp $
* @package VirtueMart
* @subpackage html
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
mm_showMyFileName( __FILE__ );

$shipping_module = mosgetparam($_REQUEST, 'shipping_module', null);

if( $shipping_module ) {
  if( !include( CLASSPATH."shipping/$shipping_module" ))
    mosredirect( $_SERVER['PHP_SELF']."?option=com_virtuemart&page=store.shipping_modules", "Could not instantiate Class $shipping_module" );
  else
    eval( "\$_SHIPPING = new ".basename($shipping_module,".php")."();");
  
  ?>
  <div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
  <script language="Javascript" src="<?php echo $mosConfig_live_site;?>/includes/js/overlib_mini.js"></script>
  <br />
  &nbsp;&nbsp;<span class="sectionname">Shipping Module Configuration: <?php echo $shipping_module ?></span>
  <br /><br />
  <form action="<?php echo $_SERVER['PHP_SELF']?>" name="adminForm" method="post">
  <?php
    $_SHIPPING->show_configuration();
  ?>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="option" value="com_virtuemart" />
    <input type="hidden" name="func" value="shippingmethodSave" />
    <input type="hidden" name="page" value="store.shipping_modules" />
    <input type="hidden" name="shipping_class" value="<?php echo basename($shipping_module,".php"); ?>" />
  </form>
  <?php
}
else {

  // Form for new shipping modules
  
}

?>
