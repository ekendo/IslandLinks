<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
* Products & Categories in a dTree menu
* @author Soeren Eberhardt
* @ Uses dTree Javascript: http://www.destroydrop.com/javascripts/tree/
*
* @version $Id: product.folders.php,v 1.4 2005/10/18 05:16:51 soeren_nb Exp $
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

$jscook_theme = "ThemeXP";
$jscook_tree = "ctThemeXP1";
    
    
/*********************************************************
************* CATEGORY TREE ******************************
*/

$phpShopmenu = new phpShopmenu();
  
// create a unique tree identifier, in case multiple trees are used 
// (max one per module)
$treename = "JSCook".uniqid( "Tree_" );

$menu_htmlcode = "<br/><br/>
<a onclick=\"javascript: ctExpandTree('div_$treename',99);\" style=\"cursor:pointer\">".$VM_LANG->_PHPSHOP_EXPAND_TREE."</a>
&nbsp;&nbsp;|&nbsp;&nbsp;
<a onclick=\"javascript: ctCollapseTree('div_$treename');\" style=\"cursor:pointer\">".$VM_LANG->_PHPSHOP_COLLAPSE_TREE."</a>
<br/>
<div style=\"margin-left:50px;\" id=\"div_$treename\"></div>
<br/><br/>
<script type=\"text/javascript\"><!--
var $treename = 
[
";
$phpShopmenu->traverse_tree_down($menu_htmlcode);
  
$menu_htmlcode .= "];
var treeindex = ctDraw ('div_$treename', $treename, $jscook_tree, '$jscook_theme', 0, 0);
--></script>";


echo "
<script language=\"JavaScript\" type=\"text/javascript\" src=\"$mosConfig_live_site/components/com_virtuemart/js/JSCookTree.js\"></script>
<link rel=\"stylesheet\" href=\"$mosConfig_live_site/components/com_virtuemart/js/$jscook_theme/theme.css\" type=\"text/css\" />
<script type=\"text/javascript\">var ctThemeXPBase = '$mosConfig_live_site/components/com_virtuemart/js/ThemeXP/';</script>
<script language=\"JavaScript\" type=\"text/javascript\" src=\"$mosConfig_live_site/components/com_virtuemart/js/$jscook_theme/theme.js\"></script>
";

echo $menu_htmlcode;


class phpShopmenu {
    /***************************************************
    * function traverse_tree_down
    */
    function traverse_tree_down(&$mymenu_content, $category_id='0', $level='0') {
        static $ibg = -1;
        global $db, $module, $mosConfig_live_site;
        $level++;
        $query = "SELECT category_name as cname, category_id as cid, category_child_id as ccid "
        . "FROM #__{vm}_category as a, #__{vm}_category_xref as b "
         . "WHERE a.category_publish='Y' AND "
         . " b.category_parent_id='$category_id' AND a.category_id=b.category_child_id "
         . "ORDER BY category_parent_id, list_order, category_name ASC";
        $db->query( $query );
        
        $categories = $db->record;
        
        if( !( $categories==null ) ) {
          $i = 1;
          foreach ($categories as $category) {
            $ibg++;
            $Treeid = $ibg == 0 ? 1 : $ibg;
            $itemid = isset($_REQUEST['itemid']) ? '&itemid='.$_REQUEST['itemid'] : "";
            $mymenu_content.= ",\n[null,'".$category->cname;
            $mymenu_content.= ps_product_category::products_in_category( $category->cid );
            $mymenu_content.= "','".$_SERVER['PHP_SELF'].'?option=com_virtuemart&page=product.product_category_form&category_id='.$category->cid."','_self','".$category->cname."'\n ";
            
            $q = "SELECT #__{vm}_product.product_name,#__{vm}_product.product_id FROM #__{vm}_product, #__{vm}_product_category_xref ";
            $q .= "WHERE #__{vm}_product.product_id=#__{vm}_product_category_xref.product_id ";
            $q .= "AND #__{vm}_product_category_xref.category_id='".$category->cid."' ";
            $q .= "ORDER BY #__{vm}_product.product_name";
            $db->query( $q );
            $products = $db->record;
            $xx = 1;
            foreach( $products as $product ) {
              // get name and link (just to save space in the code later on)
              $mymenu_content.= ",\n[null,'".$product->product_name;
              $url = $_SERVER['PHP_SELF'].'?option=com_virtuemart&page=product.product_form&product_id='.$product->product_id;
              $mymenu_content .= "','".$url."','_self','".$product->product_name."']";
              if( $xx++ < sizeof( $products ))
                $mymenu_content .= ",\n";
              else
                $mymenu_content .= "\n";
            }
                
              /* recurse through the subcategories */
              $this->traverse_tree_down($mymenu_content, $category->ccid, $level);
              
              /* let's see if the loop has reached its end */
              if ( $i == sizeof( $categories ) && $level == 1)
                $mymenu_content.= "]";
              else
                $mymenu_content.= "]";
              $i++;
              
                
          }
        }
        else {
            
        }
      }
}
/************* END OF CATEGORY TREE ******************************
*********************************************************
*/
?>
