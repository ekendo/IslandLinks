<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
* This file lists all shipping modules. It's in a file that's not called shipping_module_list
* because we currently can't add or remove shipping modules automatically!
*
* @version $Id: store.shipping_modules.php,v 1.5 2005/11/22 18:30:08 soeren_nb Exp $
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

require_once( CLASSPATH. "ps_shipping_method.php" );
$ps_shipping_method = new ps_shipping_method;

 ?>
 <table width="100%" cellspacing="0" cellpadding="4" border="0">
  <tr>
    <td>
      <br />&nbsp;&nbsp;&nbsp;<img src="<?php echo IMAGEURL ?>ps_image/ups.gif" border="0" />
      <br /><br />
    </td>
    <td><span class="sectionname">Shipping Module List</span></td>
  </tr>
</table>

<?php
$rows = $ps_shipping_method->method_list();
if ( !$rows ) {
     echo $VM_LANG->_PHPSHOP_NO_SEARCH_RESULT;
}
else {
?>
  <table width="100%" class="adminlist">
    <tr> 
      <th width="20">#</th>
      <th width="20">Active?</th>
      <th class="title">Name</th>
      <th class="title">Version</th>
      <th class="title">Author</th>
      <th class="title">Author URL</th>
      <th class="title">Author Email</th>
      <th>Description</th>
    </tr>
<?php
    $i = 0;
    foreach( $rows as $row ) {
      if ($i++ % 2) 
         $bgcolor=SEARCH_COLOR_1;
      else
         $bgcolor=SEARCH_COLOR_2;
          ?> 
      <tr bgcolor="<?php echo $bgcolor ?>"> 
        <td><?php echo( $i ); ?></td>
        <td><?php 
          if( in_array(str_replace('.php', '', $row['filename']), $PSHOP_SHIPPING_MODULES ) )
            echo "<img src=\"$mosConfig_live_site/administrator/images/tick.png\" border=\"0\" alt=\"Active\"  align=\"center\"/>";
        ?></td>
        <td width="19%"><?php
        echo $row["name"];
        echo "<br/>"; 
        
        if( $row['filename'] == "zone_shipping.php" ) {
        	echo "<a href=\"".$sess->url( $_SERVER['PHP_SELF']."?page=zone.zone_list" )."\">";
        }
        elseif( $row['filename'] == "standard_shipping.php" ) {
        	echo "<a href=\"".$sess->url( $_SERVER['PHP_SELF']."?page=shipping.rate_list.php" )."\">";
        }
        elseif( $row['filename'] == "no_shipping.php" ) {
        	//
        }	
        else {
              echo "<a href=\"".$sess->url( $_SERVER['PHP_SELF']."?page=store.shipping_module_form&shipping_module=".$row['filename'] )."\">";
        }
        
        if( $row['filename'] != 'no_shipping.php' ) {
        	echo $VM_LANG->_PHPSHOP_ISSHIP_FORM_UPDATE_LBL."</a>";
        }
        
          ?>
        </td>
        <td width="7%"><?php echo $row["version"]; ?></td>
        <td width="24%"><?php echo $row["author"]; ?></td>
        <td width="10%"><?php echo "<a target=\"_blank\" href=\"http://".$row["authorUrl"]."\">".$row["authorUrl"]."</a>"; ?>&nbsp;</td>
        <td width="10%"><?php echo $row["authorEmail"]; ?>&nbsp;</td>
        <td width="50%"><?php echo $row["description"]; ?>&nbsp;</td>
      </tr>
  <?php 
  } 
?> 
</table>
<?php 

}
?>
