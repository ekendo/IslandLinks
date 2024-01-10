<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: shop.index.php,v 1.5.2.1 2006/03/14 18:42:23 soeren_nb Exp $
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
require_once( CLASSPATH . 'ps_product.php');
require_once( CLASSPATH . 'ps_product_category.php');
$ps_product = new ps_product;

// Show only top level categories and categories that are
// being published
$query  = "SELECT * FROM #__{vm}_category, #__{vm}_category_xref ";
$query .= "WHERE #__{vm}_category.category_publish='Y' AND ";
$query .= "(#__{vm}_category_xref.category_parent_id='' OR #__{vm}_category_xref.category_parent_id='0') AND ";
$query .= "#__{vm}_category.category_id=#__{vm}_category_xref.category_child_id ";
$query .= "ORDER BY #__{vm}_category.list_order, #__{vm}_category.category_name ASC";

// initialise the query in the $database connector
// this translates the '#__' prefix into the real database prefix
$db->query( $query );

$iCol = 1;
$categories_per_row = 4;
$cellwidth = intval( 100 / $categories_per_row );
?>
<table width="100%" cellspacing="0" cellpadding="0">  
  <tr>
    <td class="componentheading" colspan="<?php echo $categories_per_row ?>"><?php echo $VM_LANG->_PHPSHOP_CATEGORIES ?></td>
  </tr>
  <?php
        // cycle through the returned rows displaying them in a table
	// with links to the product category
	// escaping in and out of php is now permitted
    while( $db->next_record() ) {	  
	  
        if ($iCol == 1) {
          echo "<tr>";
        }
		$catname = shopMakeHtmlSafe($db->f("category_name"))
      ?> 
        <td style="text-align:center;" width="<?php echo $cellwidth ?>%" valign="top">
          <a title="<?php echo $catname ?>" href="<?php echo $sess->url(URL."index.php?option=com_virtuemart&amp;page=shop.browse&amp;category_id=".$db->f("category_id")); ?>"> 
          <?php 
          if ($db->f("category_thumb_image")) {
            echo $ps_product->show_image( $db->f("category_thumb_image"), "alt=\"$catname\"", 0, "category");
            echo "<br />";
          }
		  echo $catname;
          echo ps_product_category::products_in_category( $db->f("category_id") );
?>
          </a>
        </td>
      <?php
        if ($iCol == $categories_per_row) {
          echo "</tr>";
          $iCol = 1;
        }
        else
          $iCol++;

	  }
?>
</table>
<?php echo $vendor_store_desc;  ?>
