<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: product.product_form.php,v 1.14.2.4 2006/03/10 15:55:15 soeren_nb Exp $
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

require_once( CLASSPATH.'ps_product_discount.php' );

$product_id = mosGetParam( $_REQUEST, 'product_id');
if( is_array( $product_id ))
	$product_id = (int)$product_id[0];
	
$product_parent_id = mosGetParam( $_REQUEST, 'product_parent_id');
$next_page = mosGetParam( $_REQUEST, 'next_page', "product.product_display" );
$option = empty($option)?mosgetparam( $_REQUEST, 'option', 'com_virtuemart'):$option;
$clone_product = mosGetParam( $_REQUEST, 'clone_product', "0" );

$dl_checked = "";
$curr_filename = "";
$list = Array();
$my_categories = array();
$related_products = Array();

if ($product_parent_id) {
    if ($product_id) {
        $action = $VM_LANG->_PHPSHOP_PRODUCT_FORM_UPDATE_ITEM_LBL;
    } 
    else {
        $action = $VM_LANG->_PHPSHOP_PRODUCT_FORM_NEW_ITEM_LBL;
    }
    $info_label = $VM_LANG->_PHPSHOP_PRODUCT_FORM_ITEM_INFO_LBL;
    $status_label = $VM_LANG->_PHPSHOP_PRODUCT_FORM_ITEM_STATUS_LBL;
    $dim_weight_label = $VM_LANG->_PHPSHOP_PRODUCT_FORM_ITEM_DIM_WEIGHT_LBL;
    $images_label = $VM_LANG->_PHPSHOP_PRODUCT_FORM_ITEM_IMAGES_LBL;
    $delete_message = $VM_LANG->_PHPSHOP_PRODUCT_FORM_DELETE_ITEM_MSG;
}
else {
    $product_parent_id = '';
    if ($product_id = @$vars["product_id"]) {
    	if( $clone_product == '1') {
    		$action = $VM_LANG->_PHPSHOP_PRODUCT_CLONE;
    	}
    	else {
    		$action = $VM_LANG->_PHPSHOP_PRODUCT_FORM_UPDATE_ITEM_LBL;
    	}
    } 
    else {
        $action = $VM_LANG->_PHPSHOP_PRODUCT_FORM_NEW_PRODUCT_LBL;
    }
    $info_label = $VM_LANG->_PHPSHOP_PRODUCT_FORM_PRODUCT_INFO_LBL;
    $status_label = $VM_LANG->_PHPSHOP_PRODUCT_FORM_PRODUCT_STATUS_LBL;
    $dim_weight_label = $VM_LANG->_PHPSHOP_PRODUCT_FORM_PRODUCT_DIM_WEIGHT_LBL;
    $images_label = $VM_LANG->_PHPSHOP_PRODUCT_FORM_PRODUCT_IMAGES_LBL;
    $delete_message = $VM_LANG->_PHPSHOP_PRODUCT_FORM_DELETE_PRODUCT_MSG;
}

if (!empty($product_id)) {
	$price = $ps_product->get_retail_price($product_id);
}

if (!empty($product_id)) {
  // get the Database object we're filling the product form with
  $db = $ps_product->sql($product_id); 
  $db->next_record();
  
  // Get category IDs
  $db2 = new ps_DB;
  $q = "SELECT category_id FROM #__{vm}_product_category_xref WHERE product_id='$product_id'";
  $db2->query($q);
  while ($db2->next_record()) {
      $my_categories[$db2->f("category_id")] = "1";
  }
  
  // Get the Manufacturer ID
  $db2->query("SELECT manufacturer_id FROM #__{vm}_product_mf_xref WHERE product_id='$product_id'");
  $db2->next_record();
  $manufacturer_id = $db2->f("manufacturer_id");
    
  // Get the Related Products
  $db2->query("SELECT related_products FROM #__{vm}_product_relations WHERE product_id='$product_id'");
  if($db2->next_record()) {
  	$related_products = explode("|", $db2->f("related_products"));
  }
    
  // Look if the Product is downloadable
  $q_dl = "SELECT attribute_name,attribute_value AS filename FROM #__{vm}_product_attribute WHERE ";
  $q_dl .= "product_id='$product_id' AND attribute_name='download'";
  $db2->query($q_dl);
  if ($db2->next_record()) {
    $dl_checked = "checked=\"checked\"";
  }
  $curr_filename = $db2->f("filename");
  
}
elseif (empty($vars["error"])) {
  $default["product_publish"] = "Y";
  $default["product_weight_uom"] = $VM_LANG->_PHPSHOP_PRODUCT_FORM_WEIGHT_UOM_DEFAULT;
  $default["product_lwh_uom"] = $VM_LANG->_PHPSHOP_PRODUCT_FORM_DIMENSION_UOM_DEFAULT;
  $default["product_unit"] = $VM_LANG->_PHPSHOP_PRODUCT_FORM_UNIT_DEFAULT;
  $default["product_available_date"] = time();
}
// get the default shopper group
$shopper_db = new ps_DB;
$q =  "SELECT shopper_group_id,shopper_group_name FROM #__{vm}_shopper_group WHERE `default`= '1' AND vendor_id='".$db->f("vendor_id")."'";
$shopper_db->query($q);
if ($shopper_db->num_rows()<1) {  // when there is no "default", take the first in the table 
    $q =  "SELECT shopper_group_id,shopper_group_name from #__{vm}_shopper_group WHERE vendor_id='$ps_vendor_id'";
    $shopper_db->query($q);
}
$shopper_db->next_record();
$my_shopper_group_id = $shopper_db->f("shopper_group_id");

// For cloning a product, we just need to empty the variable product_id
if( $clone_product == "1" ) {
  $product_id = "";
}
?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo $mosConfig_live_site ?>/includes/js/calendar/calendar-mos.css" title="green" />
<!-- import the calendar script -->
<script type="text/javascript" src="<?php echo $mosConfig_live_site ?>/includes/js/calendar/calendar.js"></script>
<!-- import the language module -->
<script type="text/javascript" src="<?php echo $mosConfig_live_site ?>/includes/js/calendar/lang/calendar-en.js"></script>
<br />
<?php
$title = '<img src="'. IMAGEURL .'ps_image/product_code.png" border="0" align="center" alt="Product Form" />&nbsp;&nbsp;';
$title .= $action;

if( !empty( $product_id )) {
	$title .= " :: " . $db->f("product_name");
	$flypage = $ps_product->get_flypage($product_id); 
	?>
	<a href="<?php echo $mosConfig_live_site."/index2.php?option=com_virtuemart&page=shop.product_details&flypage=$flypage&product_id=$product_id" ?>" target="_blank">
		  <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_SHOW_FLYPAGE ?>
	</a>
	<?php
}
//First create the object and let it print a form heading
$formObj = &new formFactory( $title );
//Then Start the form
$formObj->startForm( 'adminForm', 'enctype="multipart/form-data"');

$tabs = new mShopTabs(0, 1, "_main");
$tabs->startPane("content-pane");
$tabs->startTab( "<img src=\"". IMAGEURL ."ps_image/edit.png\" align=\"center\" width=\"16\" height=\"16\" border=\"0\" />&nbsp;$info_label", "info-page");
?>

<table class="adminform">
  <tr> 
   <td valign="top">
     <table width="100%" border="0">
      <tr> 
       <td align="left" colspan="2"><?php echo "<h2 >$info_label</h2>"; ?></td>
    </tr>
    <tr> 
      <td  width="21%" ><div style="text-align:right;font-weight:bold;">
      <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_PUBLISH ?>:</div>
      </td>
      <td width="79%" > <?php if ($db->sf("product_publish")=="Y") { 
    echo "<input type=\"checkbox\" name=\"product_publish\" value=\"Y\" checked=\"checked\" />";
} 
else {
    echo "<input type=\"checkbox\" name=\"product_publish\" value=\"Y\" />";
}
?> </td>
    </tr>
    <tr> 
      <td width="21%" ><div style="text-align:right;font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_SKU ?>:</div>
      </td>
      <td width="79%" height="2"> 
        <input type="text" class="inputbox"  name="product_sku" value="<?php $db->sp("product_sku"); ?>" size="32" maxlength="64" />
      </td>
    </tr>
    <tr> 
      <td width="21%" height="18"><div style="text-align:right;font-weight:bold;">
      <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_NAME ?>:</div>
      </td>
      <td width="79%" height="18" > 
        <input type="text" class="inputbox"  name="product_name" value="<?php echo shopMakeHtmlSafe( $db->sf("product_name")); ?>" size="32" maxlength="255" />
      </td>
    </tr>
    <tr> 
      <td width="21%"><div style="text-align:right;font-weight:bold;">
        <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_URL ?>:</div>
      </td>
      <td width="79%"> 
        <input type="text" class="inputbox"  name="product_url" value="<?php $db->sp("product_url"); ?>" size="32" maxlength="255" />
      </td>
    </tr>
    <tr> 
      <td width="21%"><div style="text-align:right;font-weight:bold;">
        <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_VENDOR ?>:</div>
      </td>
      <td width="79%" ><?php $ps_product->list_vendor($db->sf("vendor_id"));  ?></td>
    </tr>
    <tr> 
      <td width="21%" ><div style="text-align:right;font-weight:bold;">
        <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_MANUFACTURER ?>:</div>
      </td>
      <td width="79%" ><?php $ps_product->list_manufacturer(@$manufacturer_id);  ?></td>
    </tr>
    <?php
    if (!$product_parent_id) { ?>
    <tr> 
      <td width="29%" valign="top"><div style="text-align:right;font-weight:bold;">
       <?php echo $VM_LANG->_PHPSHOP_CATEGORIES ?>:<br/><br/>
       <?php echo mm_ToolTip( $VM_LANG->_PHPSHOP_MULTISELECT ) ?></div>
       </td>
      <td width="71%" ><?php 
        $ps_product_category->list_all("product_categories[]", "", $my_categories, 10, false, true); ?></td>
    </tr>
    <?php
    }
    ?>
  </table>
 </td>
 <td>
  <table>
    <tr> 
      <td width="29%" ><div style="text-align:right;font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_PRICE_NET ?>:</div>
      </td>
      <td width="71%" >
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
            <td>
                <input type="text" value="<?php echo @$price["product_price"]; ?>" class="inputbox" name="product_price" onkeyup="updateGross();" size="10" maxlength="10" />
                <input type="hidden" name="product_price_id" value="<?php echo @$price["product_price_id"] ?>" />
            </td>
            <td><?php
              if(empty($price["product_currency"]))
                $price["product_currency"] = $vendor_currency;
              $ps_html->list_currency("product_currency",$price["product_currency"]) ?>
            </td>
            <td>&nbsp;<?php
                echo mm_ToolTip( $VM_LANG->_PHPSHOP_PRICE_FORM_GROUP . ": ".$shopper_db->f("shopper_group_name")); ?>               
                <input type="hidden" name="shopper_group_id" value="<?php echo $my_shopper_group_id ?>" />
             </td>
            </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td width="29%" ><div style="text-align:right;font-weight:bold;">
        <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_PRICE_GROSS ?>:</div>
      </td>
      <td width="71%" ><input type="text" class="inputbox" onkeyup="updateNet();" name="product_price_incl_tax" size="10" /></td>
    </tr>
    <tr>
      <td width="29%" ><div style="text-align:right;font-weight:bold;">
        <?php echo $VM_LANG->_PHPSHOP_RATE_FORM_VAT_ID ?>:</div></td>
      <td width="71%" >
        <?php
        require_once(CLASSPATH.'ps_tax.php');
        $tax_rates = ps_tax::list_tax_value("product_tax_id",$db->sf("product_tax_id"),"updateGross();") ?>
      </td>
    </tr>
    <tr> 
      <td width="21%" ><div style="text-align:right;font-weight:bold;">
        <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_DISCOUNT_TYPE ?>:</div>
      </td>
      <td width="79%" ><?php
        echo ps_product_discount::discount_list( $db->sf("product_discount_id") ); ?>
      </td>
    </tr>
    <tr> 
      <td width="21%" ><div style="text-align:right;font-weight:bold;">
        <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_DISCOUNTED_PRICE ?>:</div>
      </td>
      <td width="79%" >
		<input type="text" size="10" name="discounted_price_override" onkeyup="try { document.adminForm.product_discount_id[document.adminForm.product_discount_id.length-1].selected=true; } catch( e ) {}" />&nbsp;&nbsp;
		<?php echo mm_ToolTip( $VM_LANG->_PHPSHOP_PRODUCT_FORM_DISCOUNTED_PRICE_TIP ) ?>
	</td>
    </tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr> 
      <td width="29%" valign="top"><div style="text-align:right;font-weight:bold;">
          <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_S_DESC ?>:</div>
      </td>
      <td width="71%"  valign="top">
          <textarea class="inputbox" name="product_s_desc" id="short_desc" cols="35" rows="6" ><?php echo $db->sf("product_s_desc"); ?></textarea> 
      </td>
    </tr>
  </table>
  </td>
  </tr>
  </table>
  <table class="adminform">
    <tr>
      <td valign="top" width="15%"><div style="font-weight:bold;">
        <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_DESCRIPTION ?>:</div>
      </td>
      <td width="85%">
        <?php
		editorArea( 'editor1', htmlspecialchars( $db->sf("product_desc"), ENT_QUOTES ), 'product_desc', '550', '300', '55', '25' )
	?>
      </td>
    </tr>
  </table>
  
  
<?php
$tabs->endTab();
$tabs->startTab( "<img src=\"". IMAGEURL ."ps_image/options.png\" width=\"16\" height=\"16\" align=\"center\" border=\"0\" />&nbsp;$status_label", "status-page");
?>

  <table width="100%" border="0" cellspacing="0" cellpadding="2" class="adminform">
    <tr> 
      <td align="left" colspan="2"><?php echo "<h2>$status_label</h2>"; ?></td>
    </tr>
    <tr> 
      <td width="21%" height="2" ><div style="text-align:right;font-weight:bold;">
      <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_IN_STOCK ?>:</div>
      </td>
      <td width="79%" height="2" > 
        <input type="text" class="inputbox"  name="product_in_stock" value="<?php $db->sp("product_in_stock"); ?>" size="10" />
      </td>
    </tr>
    <tr> 
      <td width="21%" ><div style="text-align:right;font-weight:bold;">
        <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_AVAILABLE_DATE ?>:</div>
      </td>
      <td width="79%" >
          <input class="inputbox" type="text" name="product_available_date" id="product_available_date" size="20" maxlength="19" value="<?php echo date('Y-m-d', $db->sf("product_available_date") ); ?>" />
          <input name="reset" type="reset" class="button" onClick="return showCalendar('product_available_date', 'y-mm-dd');" value="..." />
     </td>
    </tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
      <td valign="top" width="21%" ><div style="text-align:right;font-weight:bold;">
          <?php echo $VM_LANG->_PHPSHOP_AVAILABILITY ?>:</div>
      </td>
      <td width="79%" >
        <input type="text" class="inputbox" name="product_availability" value="<?php $db->sp("product_availability"); ?>" />
      <?php
      echo mm_ToolTip($VM_LANG->_PHPSHOP_PRODUCT_FORM_AVAILABILITY_TOOLTIP1); ?>
      <br /><br />
        <select class="inputbox" name="image" onchange="javascript:if (document.adminForm.image.options[selectedIndex].value!='') {document.imagelib.src='<?php echo IMAGEURL ?>availability/' + document.adminForm.image.options[selectedIndex].value; document.adminForm.product_availability.value=document.adminForm.image.options[selectedIndex].value;} else {document.imagelib.src='<?php echo $mosConfig_live_site ?>/images/stories/noimage.png'}">
          <option value="">Select Image</option><?php
            $path = IMAGEPATH."availability";
            $files = mosReadDirectory( "$path", ".", true, true);
            foreach ($files as $file) { 
                $file_info = pathinfo($file);
                $filename = $file_info['basename']; 
                if ($filename != "index.html") {?>
                <option <?php echo ($db->f("product_availability")==$filename) ? "selected=\"selected\"" : "" ?> value="<?php echo $filename ?>">
                <?php echo $filename ?>
                </option><?php 
                }
            }  ?>
        </select>&nbsp;
        <?php echo mm_ToolTip($VM_LANG->_PHPSHOP_PRODUCT_FORM_AVAILABILITY_TOOLTIP2);?>
        &nbsp;&nbsp;&nbsp;
        <img src="<?php echo $db->f("product_availability") ? IMAGEURL."availability/".$db->sf("product_availability") : $mosConfig_live_site ."/images/stories/noimage.png"; ?>" name="imagelib" border="0" alt="Preview" />
      </td>
    <tr> 
      <td width="21%" ><div style="text-align:right;font-weight:bold;">
      <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_SPECIAL ?>:</div>
      </td>
      <td width="79%" ><?php if ($db->sf("product_special")=="Y") { ?>
        <input type="checkbox" name="product_special" value="Y" checked="checked" />
    <?php    } 
       else { ?>
        <input type="checkbox" name="product_special" value="Y" />
    <?php }
    ?> </td>
    </tr>
      <!-- Added for the avanced attribute modification -->
    <tr>
    <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td align="right" width="21%" valign="top"><div style="text-align:right;font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_ATTRIBUTE_LIST ?>:</div></td> 
        <td width="79%" >
        <input class="inputbox" type="text" name="product_advanced_attribute" value="<?php $db->sp("attribute"); ?>" size="64" />
    </tr>
    <tr>
    <td>&nbsp;</td>
    <td><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_ATTRIBUTE_LIST_EXAMPLES ?></td></tr>
  <!-- END added for the advanced attribute modification --> 
  
  <!-- Added for the custom attribute modification -->
    <tr>
    <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td align="right" width="21%" valign="top"><div style="text-align:right;font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_CUSTOM_ATTRIBUTE_LIST ?>:</div></td> 
        <td width="79%" >
        <input class="inputbox" type="text" name="product_custom_attribute" value="<?php $db->sp("custom_attribute"); ?>" size="64" />
    </tr>
    <tr>
     <td>&nbsp;</td>
     <td><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_CUSTOM_ATTRIBUTE_LIST_EXAMPLES ?></td>
	</tr>
  <!-- END added for the custom attribute modification -->
  </table>

<?php
$db_items = $ps_product->items_sql($product_id);
if (!$product_parent_id and $product_id and $db_items->num_rows() > 0) { 
?> 
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr> 
      <td colspan="4">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="4"><div style="text-align:right;font-weight:bold;">
          <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_PRODUCT_ITEMS_LBL ?></div>
      </td>
    </tr>
    <tr nowrap> 
      <td><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_NAME ?></td>
      <td><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_SKU ?></td>
      <td><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_PRICE ?></td>
      <?php
  $db_heading = $ps_product->attribute_sql("",$product_id);
  while ($db_heading->next_record()) {
?> 
      <td><?php echo $db_heading->sf("attribute_name"); ?></td>
      <?php
  }
?> </tr>
    <tr> 
      <td colspan="<?php echo $db_heading->num_rows() + 3 ?>"> 
        <hr size="1" />
      </td>
    </tr>
    <?php
  while ($db_items->next_record()) {
?> 
    <tr nowrap> 
      <td> <?php
    $url = $_SERVER['PHP_SELF'] . "?page=$modulename.product_form&product_id=" . $db_items->f("product_id") . "&product_parent_id=$product_id";
    echo "<a href=\"" . $sess->url($url) . "\">";
    echo $db_items->f("product_name"); 
    echo "</a>"; 
?> </td>
      <td><?php $db_items->sp("product_sku"); ?> </td>
      <td> <?php
    $price = $ps_product->get_price($db_items->f("product_id"));
    $url  = $_SERVER['PHP_SELF'] . "?page=$modulename.product_price_list&product_id=" . $db_items->f("product_id") . "&product_parent_id=$product_parent_id";
    $url .= "&return_args=" . urlencode("page=$page&product_id=$product_id");
    echo "<a href=\"" . $sess->url($url) . "\">";
    if ($price) {
      if (!empty($price["item"])) {
        echo $price["product_price"];
      } else {
        echo "none";
      } 
    } else {
      echo "none";
    }
    echo "</a>";
?> </td>
      <?php
    $db_detail = $ps_product->attribute_sql($db_items->f("product_id"),$product_id);
    while ($db_detail->next_record()) {
?> 
      <td><?php $db_detail->p("attribute_value"); ?></td>
      <?php
    }
?> </tr>
    <?php
  } 
?> 
  </table>
  <?php
} elseif ($product_parent_id) {
?> 
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><strong><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_ITEM_ATTRIBUTES_LBL ?></strong></td>
    </tr>
    <?php
  if (!empty($_REQUEST['product_id'])) {
    $db_attribute = $ps_product->attribute_sql($product_id,$product_parent_id);
  } else {
    $db_attribute = $ps_product->attribute_sql("",$product_parent_id);
  }
  $num = 0;
  while ($db_attribute->next_record()) {
    $num++; ?> 
    <tr nowrap> 
      <td width="21%" height="22" > 
        <div style="text-align:right;font-weight:bold;"><?php
          echo $db_attribute->sf("attribute_name") . ":";
          $field_name = "attribute_$num"; ?></div>
      </td>
      <td width="79%" > 
        <input type="text" class="inputbox"  name="<?php echo $field_name; ?>" size="32" maxlength="255" value="<?php $db_attribute->sp("attribute_value"); ?>" />
      </td>
    </tr>
    <?php
  } ?> 
  </table>
  <?php
} 

$tabs->endTab();
$tabs->startTab( "<img src=\"". IMAGEURL ."ps_image/info.png\" width=\"16\" height=\"16\" align=\"center\" border=\"0\" alt=\"info\" />&nbsp;$dim_weight_label", "about-page");

?>

<table class="adminform">
 <tr>
  <td width="50%"><?php
    echo "<h2>$dim_weight_label</h2>";
?><table>
    <tr> 
      <td width="21%" valign="top" > 
        <div style="text-align:right;font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_LENGTH ?>:</div>
      </td>
      <td width="79%" > 
        <input type="text" class="inputbox"  name="product_length" value="<?php $db->sp("product_length"); ?>" size="15" maxlength="15" />
      </td>
    </tr>
    <tr> 
      <td width="21%" valign="top" > 
        <div style="text-align:right;font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_WIDTH ?>:</div>
      </td>
      <td width="79%" > 
        <input type="text" class="inputbox"  name="product_width" value="<?php $db->sp("product_width"); ?>" size="15" maxlength="15" />
      </td>
    </tr>
    <tr> 
      <td width="21%" valign="top" > 
        <div style="text-align:right;font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_HEIGHT ?>:</div>
      </td>
      <td width="79%" > 
        <input type="text" class="inputbox"  name="product_height" value="<?php $db->sp("product_height"); ?>" size="15" maxlength="15" />
      </td>
    </tr>
    <tr> 
      <td width="21%" valign="top" > 
        <div style="text-align:right;font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_DIMENSION_UOM ?>:</div>
      </td>
      <td width="79%" > 
        <input type="text" class="inputbox"  name="product_lwh_uom" value="<?php $db->sp("product_lwh_uom"); ?>" size="8" maxlength="32" />
      </td>
    </tr>
    <tr> 
      <td width="21%" valign="top" >&nbsp;</td>
      <td width="79%" >&nbsp;</td>
    </tr>
    <tr> 
      <td width="21%" valign="top" > 
        <div style="text-align:right;font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_WEIGHT ?>:</div>
      </td>
      <td width="79%" > 
        <input type="text" class="inputbox"  name="product_weight" size="15" maxlength="15" value="<?php $db->sp("product_weight"); ?>" />
      </td>
    </tr>
    <tr> 
      <td width="21%" valign="top" > 
        <div style="text-align:right;font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_WEIGHT_UOM ?>:</div>
      </td>
      <td width="79%" > 
        <input type="text" class="inputbox"  name="product_weight_uom" value="<?php $db->sp("product_weight_uom"); ?>" size="8" maxlength="32" />
      </td>
    </tr>
    <!-- Changed Packaging - Begin -->
    <tr> 
      <td width="21%" valign="top" >&nbsp;</td>
      <td width="21%" >&nbsp;</td>
    </tr>
    <tr> 
      <td width="21%" valign="top" > 
        <div align="right"><strong><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_UNIT ?>:</strong></div>
      </td>
      <td width="21%" > 
        <input type="text" class="inputbox"  name="product_unit" size="15" maxlength="15" value="<?php $db->sp("product_unit"); ?>" />
      </td>
    </tr>
    <tr>
      <td width="21%" valign="top" > 
        <div align="right"><strong><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_PACKAGING ?>:</strong></div>
      </td>
      <td width="21%" > 
        <input type="text" class="inputbox"  name="product_packaging" value="<?php echo $db->f("product_packaging") & 0xFFFF; ?>" size="8" maxlength="32" />&nbsp;<?php
        echo mm_ToolTip($VM_LANG->_PHPSHOP_PRODUCT_FORM_PACKAGING_DESCRIPTION); ?>
      </td>
    </tr>
    <tr>
      <td width="21%" valign="top" > 
        <div align="right"><strong><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_BOX ?>:</strong></div>
      </td>
      <td width="21%" > 
        <input type="text" class="inputbox"  name="product_box" value="<?php echo ($db->f("product_packaging")>>16)&0xFFFF; ?>" size="8" maxlength="32" />&nbsp;<?php
        echo mm_ToolTip($VM_LANG->_PHPSHOP_PRODUCT_FORM_BOX_DESCRIPTION); ?>
      </td>
    </tr>
    <!-- Changed Packaging - End -->
  </table>
  </td>
  <td width="50%" valign="top">
    <h2><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_DOWNLOADABLEGOODS ?></h2>
    <table width="100%" border="0" cellspacing="0" cellpadding="2">
      <tr> 
        <td width="31%"><div style="text-align:right;font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_DOWNLOADABLE ?></div></td>
        <td align="left" width="69%">
        <input class="inputbox" <?php echo $dl_checked ?> type="checkbox" name="downloadable" onchange="javascript: if(document.adminForm.downloadable.checked==true) document.adminForm.filename.disabled=false; else {document.adminForm.filename.disabled=true;}" value="Y" /></td>
      </tr>
      <tr> 
        <td width="31%"><div align="right"><?php if($curr_filename) echo $VM_LANG->_PHPSHOP_FILES_FORM_CURRENT_FILE.":"; ?></div></td>
        <td valign="top" align="left" width="69%"><?php echo $curr_filename; ?>
        </td>
      </tr>
      <tr> 
        <td width="31%"><div style="text-align:right;font-weight:bold;"><?php
        echo mm_ToolTip($VM_LANG->_PHPSHOP_PRODUCT_FORM_FILENAME_TOOLTIP); ?>
        &nbsp;&nbsp;<?php 
        echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_FILENAME; ?>:</div></td>
        <td valign="top" align="left" width="69%">
          <input type="text" name="filename" class="inputbox" value="<?php echo $curr_filename; ?>" size="32" />
        </td>
      </tr>
      <tr> 
        <td width="31%"><div style="text-align:right;font-weight:bold;"><?php
        echo mm_ToolTip($VM_LANG->_PHPSHOP_PRODUCT_FORM_UPLOAD_TOOLTIP); ?>
        &nbsp;&nbsp;<?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_UPLOAD ?>:</div></td>
        <td valign="top" align="left" width="69%">
          <input type="file" name="file_upload" class="inputbox" size="32" />
        </td>
      </tr>
    </table>
  </td>
 </tr>
</table>
<?php
$tabs->endTab();
$tabs->startTab( "<img src=\"". IMAGEURL ."ps_image/image.png\" width=\"16\" height=\"16\" align=\"center\" border=\"0\" />&nbsp;$images_label", "images-page");

if( !stristr( $db->f("product_thumb_image"), "http") && $clone_product != "1" )
  echo "<input type=\"hidden\" name=\"product_thumb_image_curr\" value=\"". $db->f("product_thumb_image") ."\" />";

if( !stristr( $db->f("product_full_image"), "http") && $clone_product != "1" )
  echo "<input type=\"hidden\" name=\"product_full_image_curr\" value=\"". $db->f("product_full_image") ."\" />";
  
 $ps_html->writableIndicator( array( IMAGEPATH."product", IMAGEPATH."product/resized") );
 
 ?>
  <table class="adminform" >
    <tr> 
      <td valign="top" width="50%" style="border-right: 1px solid black;">
        <h2><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_FULL_IMAGE ?></h2>
        <table>
          <tr> 
            <td colspan="2" ><?php 
              if ($product_id) {
                echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_IMAGE_UPDATE_LBL . "<br />"; } ?> 
              <input type="file" class="inputbox" name="product_full_image" onchange="document.adminForm.product_full_image_url.value='';document.adminForm.product_full_image_action[1].checked=true;" size="50" maxlength="255" />
            </td>
          </tr>
          <tr> 
            <td colspan="2" ><div style="font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_IMAGE_ACTION ?>:</div><br/>
              <input type="radio" class="inputbox" id="product_full_image_action0" name="product_full_image_action" checked="checked" value="none" onchange="toggleDisable( document.adminForm.product_full_image_action[1], document.adminForm.product_thumb_image, true );toggleDisable( document.adminForm.product_full_image_action[1], document.adminForm.product_thumb_image_url, true );"/>
              <label for="product_full_image_action0"><?php echo $VM_LANG->_PHPSHOP_NONE; ?></label><br/>
              <?php
              // Check if GD library is available
              if( function_exists('imagecreatefromjpeg')) { ?>
	              <input type="radio" class="inputbox" id="product_full_image_action1" name="product_full_image_action" value="auto_resize" onchange="toggleDisable( document.adminForm.product_full_image_action[1], document.adminForm.product_thumb_image, true );toggleDisable( document.adminForm.product_full_image_action[1], document.adminForm.product_thumb_image_url, true );"/>
	              <label for="product_full_image_action1"><?php echo $VM_LANG->_PHPSHOP_FILES_FORM_AUTO_THUMBNAIL . "</label><br />"; 
              }
              if ($product_id and $db->f("product_full_image")) { ?>
                <input type="radio" class="inputbox" id="product_full_image_action2" name="product_full_image_action" value="delete" onchange="toggleDisable( document.adminForm.product_full_image_action[1], document.adminForm.product_thumb_image, true );toggleDisable( document.adminForm.product_full_image_action[1], document.adminForm.product_thumb_image_url, true );"/>
                <label for="product_full_image_action2"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_IMAGE_DELETE_LBL . "</label><br />"; 
              } ?> 
            </td>
          </tr>
          <tr><td colspan="2">&nbsp;</td></tr>
          <tr> 
            <td width="21%" ><?php echo _URL." ("._CMN_OPTIONAL."!)&nbsp;"; ?></td>
            <td width="79%" >
              <?php 
              if( stristr($db->f("product_full_image"), "http") )
                $product_full_image_url = $db->f("product_full_image");
              else if(!empty($_REQUEST['product_full_image_url']))
                $product_full_image_url = $_REQUEST['product_full_image_url'];
              else
                $product_full_image_url = "";
              ?>
              <input type="text" class="inputbox" size="50" name="product_full_image_url" value="<?php echo $product_full_image_url ?>" onchange="if( this.value.length>0) document.adminForm.product_full_image_action[1].checked=false; else document.adminForm.product_full_image_action[1].checked=true; toggleDisable( document.adminForm.product_full_image_action[1], document.adminForm.product_thumb_image_url, true );toggleDisable( document.adminForm.product_full_image_action[1], document.adminForm.product_thumb_image, true );" />
            </td>
          </tr>
          <tr><td colspan="2">&nbsp;</td></tr>
          <tr> 
            <td colspan="2" >
              <div style="overflow:auto;">
                <?php 
                if( $clone_product != "1" ) {
                	echo $ps_product->image_tag($db->f("product_full_image"), "", 0); 
                }
                ?>
              </div>
            </td>
          </tr>
        </table>
      </td>

      <td valign="top" width="50%">
        <h2><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_THUMB_IMAGE ?></h2>
        <table>
          <tr> 
            <td colspan="2" ><?php if ($product_id) {
                echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_IMAGE_UPDATE_LBL . "<br>"; } ?> 
              <input type="file" class="inputbox" name="product_thumb_image" size="50" maxlength="255" onchange="if(document.adminForm.product_thumb_image.value!='') document.adminForm.product_thumb_image_url.value='';" />
            </td>
          </tr>
          <tr> 
            <td colspan="2" ><div style="font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_IMAGE_ACTION ?>:</div><br/>
              <input type="radio" class="inputbox" id="product_thumb_image_action0" name="product_thumb_image_action" checked="checked" value="none" onchange="toggleDisable( document.adminForm.product_thumb_image_action[1], document.adminForm.product_thumb_image, true );toggleDisable( document.adminForm.product_thumb_image_action[1], document.adminForm.product_thumb_image_url, true );"/>
              <label for="product_thumb_image_action0"><?php echo $VM_LANG->_PHPSHOP_NONE ?></label><br/>
              <?php 
              if ($product_id and $db->f("product_thumb_image")) { ?>
                <input type="radio" class="inputbox" id="product_thumb_image_action1" name="product_thumb_image_action" value="delete" onchange="toggleDisable( document.adminForm.product_thumb_image_action[1], document.adminForm.product_thumb_image, true );toggleDisable( document.adminForm.product_thumb_image_action[1], document.adminForm.product_thumb_image_url, true );"/>
                <label for="product_thumb_image_action1"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_IMAGE_DELETE_LBL . "</label><br />"; 
              } ?> 
            </td>
          </tr>
          <tr><td colspan="2">&nbsp;</td></tr>
          <tr> 
            <td width="21%" ><?php echo _URL." ("._CMN_OPTIONAL.")&nbsp;"; ?></td>
            <td width="79%" >
              <?php 
              if( stristr($db->f("product_thumb_image"), "http") )
                $product_thumb_image_url = $db->f("product_thumb_image");
              else if(!empty($_REQUEST['product_thumb_image_url']))
                $product_thumb_image_url = $_REQUEST['product_thumb_image_url'];
              else
                $product_thumb_image_url = "";
              ?>
              <input type="text" class="inputbox" size="50" name="product_thumb_image_url" value="<?php echo $product_thumb_image_url ?>" />
            </td>
          </tr>
          <tr><td colspan="2">&nbsp;</td></tr>
          <tr>
            <td colspan="2" >
              <div style="overflow:auto;">
                <?php 
                if( $clone_product != "1" )
                  echo $ps_product->image_tag($db->f("product_thumb_image"), "", 0) 
                ?>
              </div>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

<?php
$tabs->endTab();
$tabs->startTab( "<img src=\"". IMAGEURL ."ps_image/related.png\" width=\"16\" height=\"16\" align=\"center\" border=\"0\" />&nbsp;".$VM_LANG->_PHPSHOP_RELATED_PRODUCTS, "related-page");
?>
	<table class="adminform">
		<tr>
			<td colspan="2"><h2><?php echo $VM_LANG->_PHPSHOP_RELATED_PRODUCTS ?></h2></td>
		</tr>
		<tr>
			<td width="21%" valign="top">
				<div style="text-align:right;font-weight:bold;"><?php echo $VM_LANG->_PHPSHOP_INFO_MSG_PLEASE_SELECT ?>:</div>
				<br/><br/>
				<?php echo mm_ToolTip( $VM_LANG->_PHPSHOP_RELATED_PRODUCTS_TIP );  ?>
			</td>
			<td width="79%"><?php 
				echo $ps_html->list_products("related_products[]", $related_products, $product_id, false ); 
			?></td>
		</tr>
	</table>
<!-- Changed Product Type - Begin -->
<?php
$tabs->endTab();

  // Get Product Types
  $dba = new ps_DB;
  $q  = "SELECT * FROM #__{vm}_product_product_type_xref,#__{vm}_product_type WHERE ";
  $q .= "#__{vm}_product_product_type_xref.product_type_id=#__{vm}_product_type.product_type_id ";
  $q .= "AND product_id='$product_id' ";
/*  if (!$product_parent_id) {
    $q .= "AND product_id='$product_id' ";
  }
  else {
    $q .= "AND product_id='$product_parent_id' ";
  }*/
  $q .= "ORDER BY product_type_list_order";
  $dba->query($q);
  
  $dbpt = new ps_DB;
  $dbp = new ps_DB;

  while ($dba->next_record()) {
  
  $product_type_id = $dba->f("product_type_id");
  
$tabs->startTab( "<img src=\"". IMAGEURL ."ps_image/info.png\" width=\"16\" height=\"16\" align=\"center\" border=\"0\" />&nbsp;".$dba->f("product_type_name"), "parameter-page-$product_type_id");

  $q  = "SELECT * FROM #__{vm}_product_type_parameter WHERE ";
  $q .= "product_type_id='$product_type_id' ";
  $q .= "ORDER BY parameter_list_order";
  $dbpt->query($q);
  
  $q  = "SELECT * FROM #__{vm}_product_type_$product_type_id WHERE ";
  $q .= "product_id='$product_id'";
  $dbp->query($q);
?>

  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr> 
      <td colspan="2" height="2" >&nbsp;</td>
    </tr>

    <?php 
	while ($dbpt->next_record()) {
		if ($dbpt->f("parameter_type")!="B") {
			echo "<tr>\n  <td width=\"21%\" height=\"2\" valign=\"top\"><div style=\"text-align:right;font-weight:bold;\">";
			echo $dbpt->f("parameter_label");
			
			if ($dbpt->f("parameter_description")) {
				echo "&nbsp;";
				echo mm_ToolTip($dbpt->f("parameter_description"));
			}
			echo "&nbsp;:</div>\n  </td>\n  <td width=\"79%\" height=\"2\" >";
			
			$parameter_values=$dbpt->f("parameter_values");
			if (!empty($parameter_values)) { // List of values
				$fields=explode(";",$parameter_values);
				echo "<select class=\"inputbox\" name=\"product_type_".$product_type_id."_".$dbpt->f("parameter_name");
				
				if ($dbpt->f("parameter_type")=="V") { //  Type: Multiple Values
					$size = min(count($fields),6);
					echo "[]\" multiple size=\"$size\">\n";
					$selected_value = array();
					$get_item_value = $dbp->f($dbpt->f("parameter_name"));
					$get_item_value = explode(",",$get_item_value);
					foreach($get_item_value as $value) {
						$selected_value[$value] = 1;
					}
					foreach($fields as $field) {
						echo "<option value=\"$field\"".(($selected_value[$field]==1) ? " selected>" : ">"). $field."</option>\n";
					}
				}
				else {  // Other Parameter type
					echo "\">\n";
					foreach($fields as $field) {
						echo "<option value=\"$field\" ";
						if ($dbp->f($dbpt->f("parameter_name")) == $field) echo "selected=\"selected\"";
						echo " >".$field."</option>\n";
					}
				}
				echo "</select>\n";
			}
			else { // Input field
				switch( $dbpt->f("parameter_type") ) {
					case "I": // Integer
					case "F": // Float
					case "D": // Date & Time
					case "A": // Date
					case "M": // Time
						echo "    <input type=\"text\" class=\"inputbox\"  name=\"product_type_".$product_type_id."_".$dbpt->f("parameter_name")."\" value=\"".$dbp->f($dbpt->f("parameter_name"))."\" size=\"20\" />";
					break;
					case "T": // Text
					case "S": // Short Text
						echo "<textarea class=\"inputbox\" name=\"product_type_".$product_type_id."_".$dbpt->f("parameter_name")."\" cols=\"35\" rows=\"6\" >";
						echo $dbp->sf($dbpt->f("parameter_name"))."</textarea>";
					break;
					case "C": // Char
						echo "    <input type=\"text\" class=\"inputbox\"  name=\"product_type_".$product_type_id."_".$dbpt->f("parameter_name")."\" value=\"".$dbp->f($dbpt->f("parameter_name"))."\" size=\"5\" />";
					break;
					case "V": // Multiple Values
						echo "    <input type=\"text\" class=\"inputbox\"  name=\"product_type_".$product_type_id."_".$dbpt->f("parameter_name")."\" value=\"".$dbp->f($dbpt->f("parameter_name"))."\" size=\"20\" />";
						
// 						$fields=explode(";",$parameter_values);
// 						echo "<select class=\"inputbox\" name=\"product_type_".$product_type_id."_".$dbpt->f("parameter_name");
// 						if ($db->f("parameter_multiselect")=="Y") {
// 							$size = min(count($fields),6);
// 							echo "[]\" multiple size=\"$size\">\n";
// 							$selected_value = array();
// 							$get_item_value = explode(",",$dbp->sf($dbpt->f("parameter_name")));
// 							foreach($get_item_value as $value) {
// 								$selected_value[$value] = 1;
// 							}
// 							foreach($fields as $field) {
// 								echo "<option value=\"$field\"".(($selected_value[$field]==1) ? " selected>" : ">"). $field."</option>\n";
// 							}
// 						}
// 						else {
// 							echo "\">\n";
// 							$get_item_value = $dbp->sf($dbpt->f("parameter_name"));
// 							foreach($fields as $field) {
// 								echo "<option value=\"$field\"".(($get_item_value==$field) ? " selected>" : ">"). $field."</option>\n";
// 							}
// 						}
// 						echo "</select>";
						break;
					default: // Default type Short Text
						echo "    <input type=\"text\" class=\"inputbox\" name=\"product_type_".$product_type_id."_".$dbpt->f("parameter_name")."\" value=\"".$dbp->f($dbpt->f("parameter_name"))."\" size=\"20\" />";
				}
			}
			echo " ".$dbpt->f("parameter_unit");
			if ($dbpt->f("parameter_default")) {
				echo " (".$VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_DEFAULT.": ";
				echo $dbpt->f("parameter_default").")";
			}
			echo " [ ".$VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE.": ";
			switch( $dbpt->f("parameter_type") ) {
				case "I": echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_INTEGER; break;	// Integer
				case "T": echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_TEXT; break; 	// Text
				case "S": echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_SHORTTEXT; break; // Short Text
				case "F": echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_FLOAT; break; 	// Float
				case "C": echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_CHAR; break; 	// Char
				case "D": echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_DATETIME." ";	// Date & Time
					echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_DATE_FORMAT." ";
					echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_TIME_FORMAT;
					break;
				case "A": echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_DATE." ";		// Date
					echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_DATE_FORMAT;
					break;
				case "M": echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_TIME." ";		// Time
					echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_TIME_FORMAT;
					break;
				case "V": echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_TYPE_MULTIVALUE; break; 	// Multiple Value
			}
			echo " ]";
		}
		else {
			echo "<tr>\n  <td colspan=\"2\" height=\"2\" ><hr/>";
		}
		echo "  </td>\n</tr>";
	}
?>

  </table>
  
  <?php 

  $tabs->endTab();
  //<!-- Changed Product Type - End -->
}
if( $clone_product == "1" ) {  
  $tabs->startTab( "<img src=\"". IMAGEURL ."ps_image/copy_f2.gif\" width=\"16\" height=\"16\" align=\"center\" border=\"0\" />Clone Product Otions", "clone-page" );
  echo '<input type="hidden" name="clone_product" value="Y" />';
  echo '<input type="hidden" name="old_product_id" value="'.$_REQUEST['product_id'].'" />';
  $db_att = new ps_DB;
  $db->query( "SELECT product_id, product_name 
                FROM #__{vm}_product
                WHERE product_parent_id='".$_REQUEST['product_id']."' " );
  if( $db->num_rows() > 0 ) {
  	echo "<h3>Also clone these Child Items:</h3>";
  }
  while( $db->next_record() ) {
    $db_att->query( "SELECT attribute_name, attribute_value FROM #__{vm}_product_attribute 
                      WHERE product_id ='".$db->f("product_id")."'" );
    echo '<input type="checkbox" checked="checked" name="child_items[]" value="'.$db->f("product_id").'" id="child_'.$db->f("product_id").'" />
    <label for="child_'.$db->f("product_id").'">'.$db->f("product_name").' (';
    while( $db_att->next_record() ) {
    	echo $db_att->f("attribute_name").": ".$db_att->f("attribute_value")."; ";
    }
    echo ')</label><br/>';
  }
  
  $tabs->endTab();
}

$tabs->endPane();

// Add necessary hidden fields
$formObj->hiddenField( 'product_id', $product_id );
$formObj->hiddenField( 'product_parent_id', $product_parent_id );

$funcname = !empty($product_id) ? "productUpdate" : "productAdd";

// finally close the form:
$formObj->finishForm( $funcname, $next_page, $option );

?>

<script type="text/javascript">
<!--
function toggleDisable( elementOnChecked, elementDisable, disableOnChecked ) {
	try {
	  if( !disableOnChecked ) {
	    if(elementOnChecked.checked==true) {
	      elementDisable.disabled=false; 
	    }
	    else {
	      elementDisable.disabled=true;
	    }
	  }
	  else {
	    if(elementOnChecked.checked==true) {
	      elementDisable.disabled=true; 
	    }
	    else {
	      elementDisable.disabled=false;
	    }
	  }
	}
	catch( e ) {}
}
// borrowed from OSCommerce with small modifications. 
// All rights reserved.
var tax_rates = new Array();
<?php
foreach( $tax_rates as $tax_rate_id => $tax_rate ) {
  echo "tax_rates[\"$tax_rate_id\"] = $tax_rate;\n";
}
?>
function doRound(x, places) {
  return Math.round(x * Math.pow(10, places)) / Math.pow(10, places);
}

function getTaxRate() {
  var selected_value = document.adminForm.product_tax_id.selectedIndex;
  var parameterVal = document.adminForm.product_tax_id[selected_value].value;

  if ( (parameterVal > 0) && (tax_rates[parameterVal] > 0) ) {
    return tax_rates[parameterVal];
  } else {
    return 0;
  }
}

function updateGross() {
	if( document.adminForm.product_price.value != '' ) {
		var taxRate = getTaxRate();
		
		var r = new RegExp("\,", "i");
		document.adminForm.product_price.value = document.adminForm.product_price.value.replace( r, "." );
		  
		var grossValue = document.adminForm.product_price.value;
		  
		if (taxRate > 0) {
			grossValue = grossValue * (taxRate + 1);
		}
		
		document.adminForm.product_price_incl_tax.value = doRound(grossValue, 5);
	}
}

function updateNet() {
	if( document.adminForm.product_price_incl_tax.value != '' ) {
		var taxRate = getTaxRate();
		  
		var r = new RegExp("\,", "i");
		document.adminForm.product_price_incl_tax.value = document.adminForm.product_price_incl_tax.value.replace( r, "." );
		  
		var netValue = document.adminForm.product_price_incl_tax.value;
		
		if (taxRate > 0) {
			netValue = netValue / (taxRate + 1);
		}
		
		document.adminForm.product_price.value = doRound(netValue, 5);
	}
}

function updateDiscountedPrice() {
	if( document.adminForm.product_price.value != '' ) {
		try {
			var selected_discount = document.adminForm.product_discount_id.selectedIndex;
			var discountCalc = document.adminForm.product_discount_id[selected_discount].id;
			var origPrice = document.adminForm.product_price_incl_tax.value;
			
			if( discountCalc ) {
				eval( 'var discPrice = ' + origPrice + discountCalc );
				if( discPrice != origPrice ) {
					document.adminForm.discounted_price_override.value = discPrice.toFixed( 2 );
				}
			}
		}
		catch( e ) { }
	}
}

updateGross();
updateDiscountedPrice();
toggleDisable( document.adminForm.downloadable, document.adminForm.filename, false );
toggleDisable( document.adminForm.product_full_image_action[1], document.adminForm.product_thumb_image, true );


//-->
</script>
