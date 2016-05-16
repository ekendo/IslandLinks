<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: product.product_list.php,v 1.9.2.3 2006/04/05 18:16:54 soeren_nb Exp $
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
global $ps_product, $ps_product_category;
  
$keyword = mosgetparam($_REQUEST, 'keyword' );
$vendor = mosgetparam($_REQUEST, 'vendor', '');
$product_parent_id = mosgetparam($_REQUEST, 'product_parent_id', null);
$category_id = mosgetparam($_REQUEST, 'category_id', null);
$product_type_id = mosgetparam($_REQUEST, 'product_type_id', null); // Changed Product Type
$search_date = mosgetparam($_REQUEST, 'search_date', null); // Changed search by date

$now = getdate();
$nowstring = $now["hours"].":".$now["minutes"]." ".$now["mday"].".".$now["mon"].".".$now["year"];
$search_order = @$_REQUEST["search_order"] ? $_REQUEST["search_order"] : "<";
$search_type = @$_REQUEST["search_type"] ? $_REQUEST["search_type"] : "product";
	
require_once( CLASSPATH . "pageNavigation.class.php" );
require_once( CLASSPATH . "htmlTools.class.php" );

?>
<div align="right">

	<form style="float:right;" action="<?php $_SERVER['PHP_SELF'] ?>" method="get"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_LIST_SEARCH_BY_DATE ?>&nbsp;
          <select class="inputbox" name="search_type">
              <option value="product"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_LIST_SEARCH_BY_DATE_TYPE_PRODUCT ?></option>
              <option value="price" <?php echo $search_type == "price" ? 'selected="selected"' : ''; ?>><?php echo $VM_LANG->_PHPSHOP_PRODUCT_LIST_SEARCH_BY_DATE_TYPE_PRICE ?></option>
              <option value="withoutprice" <?php echo $search_type == "withoutprice" ? 'selected="selected"' : ''; ?>><?php echo $VM_LANG->_PHPSHOP_PRODUCT_LIST_SEARCH_BY_DATE_TYPE_WITHOUTPRICE ?></option>
          </select>
          <select class="inputbox" name="search_order">
              <option value="<"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_LIST_SEARCH_BY_DATE_BEFORE ?></option>
              <option value=">" <?php echo $search_order == ">" ? 'selected="selected"' : ''; ?>><?php echo $VM_LANG->_PHPSHOP_PRODUCT_LIST_SEARCH_BY_DATE_AFTER ?></option>
          </select>
          <input type="hidden" name="option" value="com_virtuemart" />
          <input class="inputbox" type="text" size="15" name="search_date" value="<?php echo mosgetparam($_REQUEST, 'search_date', $nowstring) ?>" />
          <input type="hidden" name="page" value="product.product_list" />
          <input class="button" type="submit" name="search" value="<?php echo $VM_LANG->_PHPSHOP_SEARCH_TITLE?>" />
		  <br/>
         <select class="inputbox" id="category_id" name="category_id" onchange="window.location='<?php echo $_SERVER['PHP_SELF'] ?>?option=com_virtuemart&page=product.product_list&category_id='+document.getElementById('category_id').options[selectedIndex].value;">
		<option value=""><?php echo _SEL_CATEGORY ?></option>
		<?php
         $ps_product_category->list_tree( $category_id );
        ?>
         </select>
	</form>
	<br/>
</div>
<?php

if (!$perm->check("admin")) {
    $q = "SELECT vendor_id FROM #__{vm}_auth_user_vendor WHERE user_id='".$auth['user_id']."'";
    $db->query( $q );
    $db->next_record();
    $vendor = $db->f("vendor_id");
}

$search_sql = " (#__{vm}_product.product_name LIKE '%$keyword%' OR \n";
$search_sql .= "#__{vm}_product.product_sku LIKE '%$keyword%' OR \n";
$search_sql .= "#__{vm}_product.product_s_desc LIKE '%$keyword%' OR \n";
$search_sql .= "#__{vm}_product.product_desc LIKE '%$keyword%'";
$search_sql .= ") \n";

// Check to see if this is a search or a browse by category
// Default is to show all products 
if (!empty($category_id)) {
	$list  = "SELECT #__{vm}_category.category_name,#__{vm}_product.product_id,#__{vm}_product.product_name,#__{vm}_product.product_sku,#__{vm}_product.vendor_id,product_publish";
	$list .= " FROM #__{vm}_product, #__{vm}_product_category_xref, #__{vm}_category WHERE ";
	$count  = "SELECT count(*) as num_rows FROM #__{vm}_product, #__{vm}_product_category_xref, #__{vm}_category WHERE ";

	$q = "#__{vm}_product_category_xref.category_id='$category_id' "; 
	$q .= "AND #__{vm}_category.category_id=#__{vm}_product_category_xref.category_id ";
	$q .= "AND #__{vm}_product.product_id=#__{vm}_product_category_xref.product_id ";
	$q .= "AND #__{vm}_product.product_parent_id='' ";
	if (!$perm->check("admin")) {
		$q  .= "AND #__{vm}_product.vendor_id = '$ps_vendor_id' ";
	}
	elseif( !empty($vendor) ) {
		$q .=  "AND #__{vm}_product.vendor_id='$vendor' ";
	}
	if( !empty( $keyword)) {
		$q .= " AND $search_sql";
	}
	$count .= $q;
	$q .= "ORDER BY product_publish DESC,product_name ";
}  
elseif (!empty($keyword)) {
	$list  = "SELECT DISTINCT *";
	$list .= " FROM #__{vm}_product WHERE ";
	$count = "SELECT DISTINCT count(*) as num_rows FROM #__{vm}_product WHERE ";
	$q = $search_sql;
	$q .= "AND #__{vm}_product.product_parent_id='' ";
	if (!$perm->check("admin")) {
		$q  .= "AND #__{vm}_product.vendor_id = '$ps_vendor_id' ";
	}
	elseif( !empty($vendor) ) {
		$q .=  "AND #__{vm}_product.vendor_id='$vendor' ";
	}
	$count .= $q;   
	$q .= " ORDER BY product_publish DESC,product_name ";
}
elseif (!empty($product_parent_id)) {
	$list  = "SELECT DISTINCT * FROM #__{vm}_product WHERE ";
	$count = "SELECT DISTINCT count(*) as num_rows FROM #__{vm}_product WHERE ";
	$q = "product_parent_id='$product_parent_id' ";
	$q .= !empty($vendor) ? "AND #__{vm}_product.vendor_id='$vendor'" : "";
	if( !empty( $keyword)) {
		$q .= " AND $search_sql";
	}
	//$q .= "AND #__{vm}_product.product_id=#__{vm}_product_reviews.product_id ";
	//$q .= "AND #__{vm}_category.category_id=#__{vm}_product_category_xref.category_id ";
	$count .= $q;
	$q .= " ORDER BY product_publish DESC,product_name ";
} 
/** Changed Product Type - Begin */
elseif (!empty($product_type_id)) {
	$list  = "SELECT DISTINCT * FROM #__{vm}_product,#__{vm}_product_product_type_xref WHERE ";
	$count = "SELECT DISTINCT count(*) as num_rows FROM #__{vm}_product,#__{vm}_product_product_type_xref WHERE ";
	$q = "#__{vm}_product.product_id=#__{vm}_product_product_type_xref.product_id ";
	$q .= "AND product_type_id='$product_type_id' ";
	if (!$perm->check("admin")) {
		$q  .= "AND #__{vm}_product.vendor_id = '$ps_vendor_id' ";
	}
	elseif( !empty($vendor) ) {
		$q .=  "AND #__{vm}_product.vendor_id='$vendor' ";
	}
	if( !empty( $keyword)) {
		$q .= " AND $search_sql";
	}
	$q .= " ORDER BY product_publish DESC,product_name ";
	$count .= $q;
}  /** Changed Product Type - End */
/** Changed search by date - Begin */
elseif (!empty($search_date)) {
    list($time,$date) = explode(" ",$search_date);
    list($d["search_date_hour"],$d["search_date_minute"]) = explode(":",$time);
    list($d["search_date_day"],$d["search_date_month"],$d["search_date_year"]) = explode(".",$date);
    $d["search_date_use"] = true;
    if (process_date_time($d,"search_date",$VM_LANG->_PHPSHOP_SEARCH_LBL)) {
        $date = $d["search_date"];
        switch( $search_type ) {
            case "product" : 
                $list  = "SELECT DISTINCT * FROM #__{vm}_product WHERE ";
                $count = "SELECT DISTINCT count(*) as num_rows FROM #__{vm}_product WHERE ";
                break;
            case "withoutprice" :
            case "price" :
                $list  = "SELECT DISTINCT #__{vm}_product.product_id,product_name,product_sku,vendor_id,";
                $list .= "product_publish,product_parent_id FROM #__{vm}_product ";
                $list .= "LEFT JOIN #__{vm}_product_price ON #__{vm}_product.product_id = #__{vm}_product_price.product_id WHERE ";
                $count = "SELECT DISTINCT count(*) as num_rows FROM #__{vm}_product ";
                $count.= "LEFT JOIN #__{vm}_product_price ON #__{vm}_product.product_id = #__{vm}_product_price.product_id WHERE ";
                break;
        }
        $where = array();
//         $where[] = "#__{vm}_product.product_parent_id='0' ";
        if (!$perm->check("admin")) {
            $where[] = " #__{vm}_product.vendor_id = '$ps_vendor_id' ";
        }
        elseif( !empty($vendor) ) {
            $where[] =  " #__{vm}_product.vendor_id='$vendor' ";
        }
        $q = "";
        switch( $search_type ) {
            case "product" :
                $where[] = "#__{vm}_product.mdate ". $search_order . " $date ";
                break;
            case "price" :
                $where[] = "#__{vm}_product_price.mdate ". $search_order . " $date ";
                $q = "GROUP BY #__{vm}_product.product_sku ";
                break;
            case "withoutprice" :
                $where[] = "#__{vm}_product_price.mdate IS NULL ";
                $q = "GROUP BY #__{vm}_product.product_sku ";
                break;
        }
        
        $q = implode(" AND ",$where) . $q . " ORDER BY #__{vm}_product.product_publish DESC,#__{vm}_product.product_name ";
        $count .= $q;
    }
    else {
    	echo "<script type=\"text/javascript\">alert('".$d["error"]."')</script>\n";  
    }
}
/** Changed search by date - End */
else {
	$list  = "SELECT DISTINCT * FROM #__{vm}_product WHERE ";
	$count = "SELECT DISTINCT count(*) as num_rows FROM #__{vm}_product WHERE ";
	$q = "product_parent_id='0' ";
	if (!$perm->check("admin")) {
		$q  .= "AND #__{vm}_product.vendor_id = '$ps_vendor_id' ";
	}
	elseif( !empty($vendor) ) {
		$q .=  "AND #__{vm}_product.vendor_id='$vendor' ";
	}
	//$q .= "AND #__{vm}_product.product_id=#__{vm}_product_reviews.product_id ";
	//$q .= "AND #__{vm}_category.category_id=#__{vm}_product_category_xref.category_id ";
	$count .= $q;
	$q .= " ORDER BY product_publish DESC,product_name ";
}
$db->query($count);
$db->next_record();
$num_rows = $db->f("num_rows");
       
// Create the Page Navigation
$pageNav = new vmPageNav( $num_rows, $limitstart, $limit );

$limitstart = $pageNav->limitstart;
$list .= $q . " LIMIT $limitstart, " . $limit;

// Create the List Object with page navigation
$listObj = new listFactory( $pageNav );

// print out the search field and a list heading
$listObj->writeSearchHeader($VM_LANG->_PHPSHOP_PRODUCT_LIST_LBL, IMAGEURL."ps_image/product_code.png", "product", "product_list");

// start the list table
$listObj->startTable();

// these are the columns in the table
$columns = Array(  "#" => "", 
					"<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".$num_rows.")\" />" => "",
					$VM_LANG->_PHPSHOP_PRODUCT_LIST_NAME => "width=\"30%\"",
					$VM_LANG->_PHPSHOP_PRODUCT_LIST_SKU => "width=\"15%\"",
					$VM_LANG->_PHPSHOP_CATEGORY => "width=\"15%\"",
					$VM_LANG->_PHPSHOP_VENDOR_MOD => "width=\"15%\"",
					$VM_LANG->_PHPSHOP_REVIEWS => "width=\"10%\"",
					$VM_LANG->_PHPSHOP_PRODUCT_LIST_PUBLISH => "width=\"5%\"",
					$VM_LANG->_PHPSHOP_PRODUCT_CLONE => "",
					_E_REMOVE => "width=\"5%\""
				);
$listObj->writeTableHeader( $columns );

if ($num_rows > 0) {

	$db->query($list);
	$i = 0;
	$db_cat = new ps_DB;
	$tmpcell = "";
	
	while ($db->next_record()) {
		
		$listObj->newRow();
		
		// The row number
		$listObj->addCell( $pageNav->rowNumber( $i ) );
		
		// The Checkbox
		$listObj->addCell( mosHTML::idBox( $i, $db->f("product_id"), false, "product_id" ) );
		
		// The link to the product form / to the child products
		$tmpcell = "<a href=\"".$sess->url( $_SERVER['PHP_SELF'] . "?page=$modulename.product_form&limitstart=$limitstart&keyword=$keyword&product_id=" . $db->f("product_id")."&product_parent_id=".$product_parent_id )."\">".$db->f("product_name"). "</a>";
		if( $ps_product->parent_has_children( $db->f("product_id") ) ) {
			$tmpcell .= "&nbsp;&nbsp;&nbsp;<a href=\"";
			$tmpcell .= $sess->url($_SERVER['PHP_SELF'] . "?page=$modulename.product_list&product_parent_id=" . $db->f("product_id"));
			$tmpcell .=  "\">[ ".$VM_LANG->_PHPSHOP_PRODUCT_FORM_ITEM_INFO_LBL. " ]</a>";
		}
		$listObj->addCell( $tmpcell );
		
		// The product sku
		$listObj->addCell( $db->f("product_sku") );
		
		// The Categories or the parent product's name
		$tmpcell = "";
		if( empty($product_parent_id) ) {
		  $db_cat->query("SELECT #__{vm}_category.category_id, category_name FROM #__{vm}_category,#__{vm}_product_category_xref 
							WHERE #__{vm}_category.category_id=#__{vm}_product_category_xref.category_id
							AND #__{vm}_product_category_xref.product_id='".$db->f("product_id") ."'");
		  while($db_cat->next_record()) {
			  $tmpcell .= $db_cat->f("category_name") . "<br/>";
		  }
		}
		else {
		  $tmpcell .= $VM_LANG->_PHPSHOP_CATEGORY_FORM_PARENT .": <a href=\"";
		  $url = $_SERVER['PHP_SELF'] . "?page=$modulename.product_form&limitstart=$limitstart&keyword=$keyword&product_id=$product_parent_id";
		  $tmpcell .= $sess->url( $url );
		  $tmpcell .= "\">".$ps_product->get_field($product_parent_id,"product_name"). "</a>";
		}
		$listObj->addCell( $tmpcell );
		
		$listObj->addCell( $ps_product->getVendorName($db->f("vendor_id")) );
		
		$db_cat->query("SELECT count(*) as num_rows FROM #__{vm}_product_reviews WHERE product_id='".$db->f("product_id")."'");
		$db_cat->next_record();
		if ($db_cat->f("num_rows")) {
			$tmpcell = $db_cat->f("num_rows")."&nbsp;";
			$tmpcell .= "<a href=\"".$_SERVER["PHP_SELF"]."?option=com_virtuemart&page=product.review_list&product_id=".$db->f("product_id")."\">";
			$tmpcell .= "[".$VM_LANG->_PHPSHOP_SHOW."]</a>";
		}
		else {
			$tmpcell = " - ";
		}
		$listObj->addCell( $tmpcell );
		
		$tmpcell = "<a href=\"". $sess->url( $_SERVER['PHP_SELF']."?page=product.product_list&category_id=$category_id&product_id=".$db->f("product_id")."&func=changePublishState" );
		if ($db->f("product_publish")=='N') {
			$tmpcell .= "&task=publish\">";
		} 
		else { 
			$tmpcell .= "&task=unpublish\">";
		}
		$tmpcell .= vmCommonHTML::getYesNoIcon( $db->f("product_publish"), "Publish", "Unpublish" );
		$tmpcell .= "</a>";
		$listObj->addCell( $tmpcell );
		
		$tmpcell = "<a title=\"".$VM_LANG->_PHPSHOP_PRODUCT_CLONE."\" onmouseout=\"MM_swapImgRestore();\"  onmouseover=\"MM_swapImage('copy_$i','','". IMAGEURL ."ps_image/copy_f2.gif',1);\" href=\"";
		$url = $_SERVER['PHP_SELF'] . "?page=$modulename.product_form&clone_product=1&limitstart=$limitstart&keyword=$keyword&product_id=" . $db->f("product_id");
		if( !empty($product_parent_id) )
			$url .= "&product_parent_id=$product_parent_id";
		$tmpcell .= $sess->url( $url );
		$tmpcell .= "\"><img src=\"".IMAGEURL."/ps_image/copy.gif\" name=\"copy_$i\" border=\"0\" alt=\"".$VM_LANG->_PHPSHOP_PRODUCT_CLONE."\" /></a>";
		$listObj->addCell( $tmpcell );
	  
		$listObj->addCell( $ps_html->deleteButton( "product_id", $db->f("product_id"), "productDelete", $keyword, $limitstart ) );
	
		$i++;
	}
}

$listObj->writeTable();

$listObj->endTable();

$listObj->writeFooter( $keyword,  "&product_parent_id=$product_parent_id&category_id=$category_id&product_type_id=$product_type_id&search_date$search_date");
	
?>