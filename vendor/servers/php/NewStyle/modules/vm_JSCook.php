<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
* VirtueMart JSCookTree menu
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @ JSCookTree VirtueMart menu created by Soeren
* @ modified by soeren
* @ Uses JSCookTree Javascript: http://www.cs.ucla.edu/~heng/JSCookTree/
* @ version $Id: vm_JSCook.php,v 1.4.2.1 2006/03/21 19:38:26 soeren_nb Exp $
*
* This file is included by the virtuemart module if the module parameter
* MenuType is set to jscooktree
**/
global $module, $root_label, $jscook_type, $jscookMenu_style, $jscookTree_style, $ps_product_category;
require_once( CLASSPATH . 'ps_product_category.php' );
if( !isset( $ps_product_category )) $ps_product_category = new ps_product_category;
$Itemid = mosGetParam( $_REQUEST, 'Itemid', "");
$TreeId = mosGetParam( $_REQUEST, 'TreeId', "");


if( $jscook_type == "tree" ) {
  
  if($jscookTree_style == "ThemeXP")
    $jscook_tree = "ctThemeXP1";
  if($jscookTree_style == "ThemeNavy")
    $jscook_tree = "ctThemeNavy";

  echo "
    <script language=\"JavaScript\" type=\"text/javascript\" src=\"components/com_virtuemart/js/JSCookTree.js\"></script>
    <link rel=\"stylesheet\" href=\"components/com_virtuemart/js/$jscookTree_style/theme.css\" type=\"text/css\" />
    <script language=\"JavaScript\" type=\"text/javascript\" src=\"components/com_virtuemart/js/$jscookTree_style/theme.js\"></script>
    ";
  $MamboMart = new MamboMartTree();
}
else {

  echo "
    <script language=\"JavaScript\" type=\"text/javascript\" src=\"includes/js/JSCookMenu.js\"></script>
    <link rel=\"stylesheet\" href=\"includes/js/$jscookMenu_style/theme.css\" type=\"text/css\" />
    <script language=\"JavaScript\" type=\"text/javascript\" src=\"includes/js/$jscookMenu_style/theme.js\"></script>
    ";
  $MamboMart = new MamboMartMenu();
}
  
// create a unique tree identifier, in case multiple trees are used 
// (max one per module)
$varname = "JSCook_".uniqid( $jscook_type."_" );

$menu_htmlcode = "<div align=\"left\" class=\"mainlevel\" id=\"div_$varname\"></div>
<script type=\"text/javascript\"><!--
var $varname = 
[
";
$MamboMart->traverse_tree_down($menu_htmlcode);


$menu_htmlcode .= "];
";
if(  $jscook_type == "tree" ) {
  $menu_htmlcode .= "var treeindex = ctDraw ('div_$varname', $varname, $jscook_tree, '$jscookTree_style', 0, 0);";
}
else
  $menu_htmlcode .= "cmDraw ('div_$varname', $varname, '$menu_orientation', cm$jscookMenu_style, '$jscookMenu_style');";

$menu_htmlcode .="
--></script>\n";

if(  $jscook_type == "tree" ) {
  if( $TreeId ) {
    $menu_htmlcode .= "<input type=\"hidden\" id=\"TreeId\" name=\"TreeId\" value=\"$TreeId\" />\n";
    $menu_htmlcode .= "<script language=\"JavaScript\" type=\"text/javascript\">ctExposeTreeIndex( treeindex, parseInt(ctGetObject('TreeId').value));</script>\n";
  }
}
$menu_htmlcode .= "<noscript>";
$menu_htmlcode .= $ps_product_category->get_category_tree( $category_id, $class_mainlevel );
$menu_htmlcode .= "\n</noscript>\n";
echo $menu_htmlcode;


class MamboMartTree {
    /***************************************************
    * function traverse_tree_down
    */
    function traverse_tree_down(&$mymenu_content, $category_id='0', $level='0') {
        static $ibg = -1;
        global $module, $mosConfig_live_site;
        $db = new ps_DB();
        $level++;
        $query = "SELECT category_name, category_id, category_child_id "
        . "FROM #__{vm}_category as a, #__{vm}_category_xref as b "
         . "WHERE a.category_publish='Y' AND "
         . " b.category_parent_id='$category_id' AND a.category_id=b.category_child_id "
         . "ORDER BY category_parent_id, list_order, category_name ASC";
        $db->query( $query );
        
		while( $db->next_record() ) {
            $ibg++;
            $Treeid = $ibg == 0 ? 1 : $ibg;
            $itemid = isset($_REQUEST['itemid']) ? '&itemid='.$_REQUEST['itemid'] : "";
            $mymenu_content.= ",\n[null,'".$db->f("category_name");
            $mymenu_content.= ps_product_category::products_in_category( $db->f("category_id") );
            $mymenu_content.= "','".sefRelToAbs('index.php?option=com_virtuemart&page=shop.browse&category_id='.$db->f("category_id").$itemid."&TreeId=$Treeid")."','_self','".$db->f("category_name")."'\n ";
                
			/* recurse through the subcategories */
			$this->traverse_tree_down($mymenu_content, $db->f("category_child_id"), $level);
		  
			/* let's see if the loop has reached its end */
			$mymenu_content.= "]";
                
		}
	}
}
/************* END OF CATEGORY TREE ******************************
*********************************************************
*/
class MamboMartMenu {
    /***************************************************
    * function traverse_tree_down
    */
    function traverse_tree_down(&$mymenu_content, $category_id='0', $level='0') {
        static $ibg = 0;
        global $module, $mosConfig_live_site;
        $level++;
        $query = "SELECT category_name, category_id, category_child_id "
        . "FROM #__{vm}_category as a, #__{vm}_category_xref as b "
         . "WHERE a.category_publish='Y' AND "
         . " b.category_parent_id='$category_id' AND a.category_id=b.category_child_id "
         . "ORDER BY category_parent_id, list_order, category_name ASC";
        $db = new ps_DB();
        $db->query( $query );
        
		while($db->next_record()) {
            $itemid = isset($_REQUEST['itemid']) ? '&itemid='.$_REQUEST['itemid'] : "";
            if( $ibg != 0 )
              $mymenu_content.= ",";
              
            $mymenu_content.= "\n['<img src=\"$mosConfig_live_site/components/com_virtuemart/js/ThemeXP/darrow.png\">','".$db->f("category_name")."','".sefRelToAbs('index.php?option=com_virtuemart&page=shop.browse&category_id='.$db->f("category_id").$itemid)."',null,'".$db->f("category_name")."'\n ";
            
            $ibg++;
                
            /* recurse through the subcategories */
            $this->traverse_tree_down($mymenu_content, $db->f("category_child_id"), $level);
            
            /* let's see if the loop has reached its end */
            $mymenu_content.= "]";
                
        }
    }
}

?>
