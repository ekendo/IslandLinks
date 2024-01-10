<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: shop.product_details.php,v 1.12.2.6 2006/04/05 18:16:54 soeren_nb Exp $
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

require_once(CLASSPATH . 'ps_product_files.php' );
require_once(CLASSPATH . 'ps_product.php' );
$ps_product = new ps_product;

require_once(CLASSPATH . 'ps_product_category.php' );
$ps_product_category = new ps_product_category;

require_once(CLASSPATH . 'ps_product_attribute.php' );
$ps_product_attribute = new ps_product_attribute;

require_once(CLASSPATH . 'ps_product_type.php' );
$ps_product_type = new ps_product_type;
require_once(CLASSPATH . 'ps_reviews.php' );  

/* Flypage Parameter has old page syntax: shop.flypage
* so let's get the second part - flypage */
$flypage = mosGetParam($_REQUEST, "flypage", FLYPAGE);

$flypage = str_replace( 'shop.', '', $flypage);

$product_id = intval( mosgetparam($_REQUEST, "product_id", null) );
$product_sku = $db->getEscaped( mosgetparam($_REQUEST, "sku", '' ) );
$category_id = mosgetparam($_REQUEST, "category_id", null);
$manufacturer_id = mosgetparam($_REQUEST, "manufacturer_id", null);
$Itemid = mosgetparam($_REQUEST, "Itemid", null);
$db_product = new ps_DB;

// Get the product info from the database
$q = "SELECT * FROM `#__{vm}_product` WHERE ";
if( !empty($product_id)) {
	$q .= "`product_id`=$product_id";
}
elseif( !empty($product_sku )) {
	$q .= "`product_sku`='$product_sku'";
}
else {
	mosRedirect( $_SERVER['PHP_SELF']."?option=com_virtuemart&keyword={$_SESSION['keyword']}&category_id={$_SESSION['category_id']}&limitstart={$_SESSION['limitstart']}", $VM_LANG->_PHPSHOP_PRODUCT_NOT_FOUND );
}

if( !$perm->check("admin,storeadmin") ) {
  	$q .= " AND `product_publish`='Y'";
  	if( CHECK_STOCK && PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS != "1") {
    	$q .= " AND `product_in_stock` > 0 ";
  	}
}
$db_product->query( $q );

// Redirect back to Product Browse Page on Error
if( !$db_product->next_record() ) {
  mosRedirect( $_SERVER['PHP_SELF']."?option=com_virtuemart&keyword={$_SESSION['keyword']}&category_id={$_SESSION['category_id']}&limitstart={$_SESSION['limitstart']}", $VM_LANG->_PHPSHOP_PRODUCT_NOT_FOUND );
}
if( empty($product_id)) {
	$product_id = $db_product->f('product_id');
}
$product_parent_id = $db_product->f("product_parent_id");
if ($product_parent_id != 0) {
	$dbp= new ps_DB;
	$dbp->query("SELECT * FROM #__{vm}_product WHERE product_id='$product_parent_id'" );
	$dbp->next_record();
}

// Let's have a look wether the product has images.
if( $product_parent_id != 0 ) {
	  $db->query( "SELECT COUNT(file_id) AS images FROM #__{vm}_product_files WHERE file_product_id=$product_parent_id AND file_is_image=1" );
}
else {
  	$db->query( "SELECT COUNT(file_id) AS images FROM #__{vm}_product_files WHERE file_product_id=$product_id AND file_is_image=1" );
}
$db->next_record();
$images = new stdClass();
$images->images = $db->f("images");

// Let's have a look wether the product has related products.
  $q = "SELECT product_sku, related_products FROM #__{vm}_product,#__{vm}_product_relations ";
  $q .= "WHERE #__{vm}_product_relations.product_id='$product_id' AND product_publish='Y' ";
  $q .= "AND FIND_IN_SET(#__{vm}_product.product_id, REPLACE(related_products, '|', ',' )) LIMIT 0, 4";
  $db->query( $q );
/*// This shows randomly selected products from the products table
  // if you don't like to set up related products for each product
  $q = "SELECT product_sku FROM #__{vm}_product ";
  $q .= "WHERE product_publish='Y' AND product_id != $product_id ";
  $q .= "ORDER BY RAND() LIMIT 0, 4";
  $db->query( $q );*/
  
  $related_product_html = "";
  if( $db->num_rows() > 0 ) {
    $related_product_html .= "<hr/>\n";
    $related_product_html .= "<h3>".$VM_LANG->_PHPSHOP_RELATED_PRODUCTS_HEADING.":</h3>\n";
    $related_product_html .= "<table width=\"100%\" align=\"center\"><tr>\n";
    while( $db->next_record() ) {
      $related_product_html .= "<td valign=\"top\">".$ps_product->product_snapshot( $db->f('product_sku') )."</td>\n";
	}
    $related_product_html .= "</tr></table>\n";
  }
  
/** GET THE PRODUCT NAME **/
  $product_name = shopMakeHtmlSafe( $db_product->f("product_name") );
  if( $db_product->f("product_publish") == "N" )
    $product_name .= " ("._CMN_UNPUBLISHED.")";
  $product_description = $db_product->f("product_desc");  
  if( (str_replace("<br />", "" , $product_description)=='') && ($product_parent_id!=0) ) {
    $product_description = $dbp->f("product_desc"); // Use product_desc from Parent Product
  }
  
/** Get the CATEGORY NAVIGATION **/
  $navigation_pathway = "";
  $navigation_childlist = "";
  $pathway_appended = false;
  if (empty($category_id))  {
    $q = "SELECT category_id FROM #__{vm}_product_category_xref WHERE product_id = '$product_id' LIMIT 0,1";
    $db->query( $q );
    $db->next_record();
    if( !$db->f("category_id") ) {
      // The Product Has no category entry and must be a Child Product
      // So let's get the Parent Product
      $q = "SELECT product_id FROM #__{vm}_product WHERE product_id = '".$db_product->f("product_parent_id")."' LIMIT 0,1";
      $db->query( $q );
      $db->next_record();
      
      $q = "SELECT category_id FROM #__{vm}_product_category_xref WHERE product_id = '".$db->f("product_id")."' LIMIT 0,1";
      $db->query( $q );
      $db->next_record();
    }
    $_GET['category_id'] = $category_id = $db->f("category_id");
  }
  $navigation_pathway .= $ps_product_category->get_navigation_list($category_id);
  $navigation_pathway .= " ".$ps_product_category->pathway_separator()." ". $product_name;

  if ($ps_product_category->has_childs($category_id) ) { 
    $navigation_childlist .= $ps_product_category->get_child_list($category_id);
  }
  
  /* Set Dynamic Pathway */
    $mainframe->appendPathWay( $navigation_pathway );
 
  /* Set Dynamic Page Title */
    $mainframe->setPageTitle( html_entity_decode( substr($product_name, 0, 60 ) ));

  /* Prepend Product Short Description Meta Tag "description" */
    $mainframe->prependMetaTag( "description", strip_tags( $db_product->f("product_s_desc")) );


/** Show an "Edit PRODUCT"-Link ***/
  if ($perm->check("admin,storeadmin")) {
    $edit_link = "<a href=\"". sefRelToAbs($mosConfig_live_site."/index.php?page=product.product_form&next_page=shop.product_details&product_id=$product_id&option=com_virtuemart&Itemid=$Itemid")."\">
      <img src=\"images/M_images/edit.png\" width=\"16\" height=\"16\" alt=\"". $VM_LANG->_PHPSHOP_PRODUCT_FORM_EDIT_PRODUCT ."\" border=\"0\" /></a>";
  }
  else {
    $edit_link = "";
  }
  
/** LINK TO MANUFACTURER POP-UP **/
  $manufacturer_id = $ps_product->get_manufacturer_id($product_id);
  $manufacturer_name = $ps_product->get_mf_name($product_id);
  $manufacturer_link = "";
  if( $manufacturer_id && !empty($manufacturer_name) ) {
    $link = "$mosConfig_live_site/index2.php?page=shop.manufacturer_page&amp;manufacturer_id=$manufacturer_id&amp;output=lite&amp;option=com_virtuemart&amp;Itemid=".$Itemid;
	$text = '( '.$manufacturer_name.' )';
    $manufacturer_link .= vmPopupLink( $link, $text );
	
    // Avoid JavaScript on PDF Output                           
    if( @$_REQUEST['output'] == "pdf" ) 
      $manufacturer_link = "<a href=\"$link\" target=\"_blank\" title=\"$text\">$text</a>";
  }
/** PRODUCT PRICE **/
  if (_SHOW_PRICES == '1') { /** Change - Begin */
    if( $db_product->f("product_unit") && VM_PRICE_SHOW_PACKAGING_PRICELABEL)
      $product_price = "<strong>". $VM_LANG->_PHPSHOP_CART_PRICE_PER_UNIT.' ('.$db_product->f("product_unit")."):</strong>";
    else /** Change - End */
      $product_price = "<strong>". $VM_LANG->_PHPSHOP_CART_PRICE. ": </strong>";
    $product_price .= $ps_product->show_price( $product_id ); 
  }
  else
    $product_price = "";
  
/** Change Packaging - Begin */
/** PRODUCT PACKAGING **/
	if (  $db_product->f("product_packaging") ) {
        $packaging = $db_product->f("product_packaging") & 0xFFFF;
        $box = ($db_product->f("product_packaging") >> 16) & 0xFFFF;
        $product_packaging = "";
        if ( $packaging ) {
            $product_packaging .= $VM_LANG->_PHPSHOP_PRODUCT_PACKAGING1.$packaging;
            if( $box ) $product_packaging .= "<br/>";
        }
        if ( $box ) 
            $product_packaging .= $VM_LANG->_PHPSHOP_PRODUCT_PACKAGING2.$box;
            
        $product_packaging = str_replace("{unit}",$db_product->f("product_unit")?$db_product->f("product_unit") : $VM_LANG->_PHPSHOP_PRODUCT_FORM_UNIT_DEFAULT,$product_packaging);
    }
    else
        $product_packaging = "";
/** Change Packaging - End */

/** PRODUCT IMAGE **/
  $product_image = "";
  $full_image = $db_product->f("product_full_image");
  $full_image = $product_parent_id!=0 && !$db_product->f("product_full_image") ? 
  		$dbp->f("product_full_image") : $db_product->f("product_full_image"); // Change
  $product_thumb_image = $product_parent_id!=0 && !$db_product->f("product_thumb_image") ? 
  		$dbp->f("product_thumb_image") : $db_product->f("product_thumb_image"); // Change

  /* Wrap the Image into an URL when applicable */
  if ( $db_product->f("product_url") ) { 
    $product_image = "<a href=\"". $db_product->f("product_url")."\" title=\"".$product_name."\" target=\"_blank\">";
    $product_image .= $ps_product->image_tag($full_image, "alt=\"".$product_name."\"", 0);
    $product_image .= "</a>";
  }
  /* Show the Thumbnail with a Link to the full IMAGE */ 
  elseif( !$db_product->f("product_url") ) {
    if( empty($full_image ) ) {
      $product_image = "<img src=\"".IMAGEURL.NO_IMAGE."\" alt=\"".$product_name."\" border=\"0\" />";
    }
    else {
      // file_exists doesn't work on remote files,
      // so returns false on remote files
      // This should fix the "Long Page generation bug"
      if( file_exists( IMAGEPATH."product/$full_image" )) {
      	
        /* Get image width and height */
        if( $image_info = @getimagesize(IMAGEPATH."product/$full_image") ) {
          $width = $image_info[0] + 20;
          $height = $image_info[1] + 20;
        }
      }
      else {
        $width = 640;
        $height= 480;
      }
      if( stristr( $full_image, "http" ) ) {
        $imageurl = $full_image;
      }
      else {
        $imageurl = IMAGEURL."product/$full_image";
      }
      /* Build the "See Bigger Image" Link */
      if( @$_REQUEST['output'] != "pdf" ) {
		$link = $imageurl;
		$text = $ps_product->image_tag($product_thumb_image, "alt=\"".$product_name."\"", 1)."<br/>".$VM_LANG->_PHPSHOP_FLYPAGE_ENLARGE_IMAGE;
		// vmPopupLink can be found in: htmlTools.class.php
		$product_image = vmPopupLink( $link, $text, $width, $height );
      }
      else {
        $product_image = "<a href=\"$imageurl\" target=\"_blank\">".$ps_product->image_tag($product_thumb_image, "alt=\"".$product_name."\"", 1)."</a>";
      }
    }
  }

  /* MORE IMAGES ??? */
    $more_images = "";
  if( !empty($images->images) ) {
    /* Build the JavaScript Link */
    $more_images = "<a href=\"$mosConfig_live_site/index.php?option=com_virtuemart&page=shop.view_images&flypage=".@$_REQUEST['flypage']."&product_id=$product_id&category_id=$category_id&Itemid=$Itemid\">";
    $more_images .= "<img src=\"".IMAGEURL."ps_image/more_images.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"".$VM_LANG->_PHPSHOP_MORE_IMAGES ." (".$images->images.")\" />";
    $more_images .= "<br />".$VM_LANG->_PHPSHOP_MORE_IMAGES." (".$images->images.")</a>";
  }
  /* Files? */
  $file_list = ps_product_files::get_file_list( $product_id );
  
/** AVAILABILITY **/
  // This is the place where it shows: 
  // Availability: 24h, In Stock: 5 etc.
  // You can make changes to this functionality in the file: classes/ps_product.php
  if( @$_REQUEST['output'] != "pdf" )
    $product_availability = $ps_product->get_availability($product_id); 
    
/* SHOW RATING */
  $product_rating = "";
  if (PSHOP_ALLOW_REVIEWS == '1') {
    $product_rating = "<span class=\"contentheading\">".$VM_LANG->_PHPSHOP_CUSTOMER_RATING.":</span><br />";
    $product_rating .= ps_reviews::allvotes( $product_id );
  }

/* ADD-TO-CART */
  $addtocart = "<div>
    <form action=\"". $mm_action_url."index.php\" method=\"post\" name=\"addtocart\" id=\"addtocart\">"
          .$ps_product_attribute->list_attribute($product_id)
          // added for the advanced attribute modification
          .$ps_product_attribute->list_advanced_attribute($product_id)
          // end added for advanced attribute modification
          .$ps_product_attribute->list_custom_attribute($product_id);
          // end added for custom attribute modification
	if (USE_AS_CATALOGUE != '1' && $product_price != "" && !stristr( $product_price, $VM_LANG->_PHPSHOP_PRODUCT_CALL )) { 
                $addtocart .= "
        <p><label for=\"quantity\" style=\"vertical-align: middle;\">".$VM_LANG->_PHPSHOP_CART_QUANTITY.":</label>
            <input type=\"text\" class=\"inputbox\" size=\"4\" id=\"quantity\" name=\"quantity\" value=\"1\" style=\"vertical-align: middle;\" />&nbsp;
            <input type=\"submit\" ";
                $addtocart .= "style=\"text-align:center;background-position:bottom left;width:160px;height:35px;cursor:pointer;border:none;font-weight:bold;font-family:inherit;background: url('". IMAGEURL ."ps_image/".PSHOP_ADD_TO_CART_STYLE ."') no-repeat left center transparent;vertical-align: middle;\" ";
                $addtocart .= "value=\"".$VM_LANG->_PHPSHOP_CART_ADD_TO ."\" title=\"".$VM_LANG->_PHPSHOP_CART_ADD_TO."\" />
          </p>
      <input type=\"hidden\" name=\"flypage\" value=\"shop.$flypage\" />
      <input type=\"hidden\" name=\"page\" value=\"shop.cart\" />
      <input type=\"hidden\" name=\"manufacturer_id\" value=\"$manufacturer_id\" />
      <input type=\"hidden\" name=\"category_id\" value=\"$category_id\" />
      <input type=\"hidden\" name=\"func\" value=\"cartAdd\" />
      <input type=\"hidden\" name=\"option\" value=\"$option\" />
      <input type=\"hidden\" name=\"Itemid\" value=\"$Itemid\" />";
	}
    $addtocart .= "</form>
    </div>";

/* LIST ALL REVIEWS **/
  if (PSHOP_ALLOW_REVIEWS == '1') {
      /*** Show all reviews available ***/
      $product_reviews = ps_reviews::product_reviews( $product_id );
      /*** Show a form for writing a review ***/
      $product_reviewform = ps_reviews::reviewform( $product_id ); 
  }
  else {
    $product_reviews = $product_reviewform = "";
  }
/* LINK TO VENDOR-INFO POP-UP **/
  $vend_id = $ps_product->get_vendor_id($product_id);  
  $vend_name = $ps_product->get_vendorname($product_id);
  
  $link = "$mosConfig_live_site/index2.php?page=shop.infopage&amp;vendor_id=$vend_id&amp;output=lite&amp;option=com_virtuemart&amp;Itemid=".$Itemid;
  $text = $VM_LANG->_PHPSHOP_VENDOR_FORM_INFO_LBL;
  $vendor_link = vmPopupLink( $link, $text );
  
  // Avoid JavaScript on PDF Output 
  if( @$_REQUEST['output'] == "pdf" ) 
    $vendor_link = "<a href=\"$link\" target=\"_blank\" title=\"$text\">$text</a>";
  
  if ($product_parent_id!=0 && !$ps_product_type->product_in_product_type($product_id)) {
  	$product_type = $ps_product_type->list_product_type($product_parent_id);
  }
  else {
	$product_type = $ps_product_type->list_product_type($product_id);
  }

/** 
*   Read the template file into a String variable.
*   Then replace the placeholders with HTML formatted product details
*
* function read_file( $file, $defaultfile='') ***/
$template = read_file( PAGEPATH."templates/product_details/$flypage.php", PAGEPATH."templates/product_details/flypage.php");

/** NOW LET'S BEGIN AND FILL THE TEMPLATE **/
$template = str_replace( "{navigation_pathway}", $navigation_pathway, $template );
$template = str_replace( "{navigation_childlist}", $navigation_childlist, $template );
$template = str_replace( "{product_name}", $product_name, $template );
$template = str_replace( "{product_image}", $product_image, $template );
$template = str_replace( "{full_image}", $full_image, $template ); // to display the full image on flypage
$template = str_replace( "{more_images}", $more_images, $template );
$template = str_replace( "{file_list}", $file_list, $template );
$template = str_replace( "{edit_link}", $edit_link, $template );
$template = str_replace( "{manufacturer_link}", $manufacturer_link, $template );
$template = str_replace( "{product_price}", $product_price, $template );
$template = str_replace( "{product_s_desc}", $db_product->f("product_s_desc"), $template );
$template = str_replace( "{product_description}", $product_description, $template );
$template = str_replace( "{product_weight}", $db_product->f("product_weight"), $template );
$template = str_replace( "{product_sku}", $db_product->f("product_sku"), $template );
$template = str_replace( "{addtocart}", $addtocart, $template );
$template = str_replace( "{product_reviews}", $product_reviews, $template );
$template = str_replace( "{product_reviewform}", $product_reviewform, $template );
$template = str_replace( "{product_availability}", $product_availability, $template );
$template = str_replace( "{vendor_link}", $vendor_link, $template );
$template = str_replace( "{mosConfig_live_site}", $mosConfig_live_site, $template );
$template = str_replace( "{related_products}", $related_product_html, $template );
$template = str_replace( "{product_type}", $product_type, $template ); // Changed Product Type
$template = str_replace( "{product_packaging}", $product_packaging, $template ); // Changed Packaging

/* 
  Really cool regular expression that let's you easily translate the flypage 
  Usage:
   {vm_lang:_PHPSHOP__PHPSHOP_ACCOUNT_TITLE} 
   will print the content of $VM_LANG->_PHPSHOP_ACCOUNT_TITLE
   from the current language
*/
$template = preg_replace("/{vm_lang:([^}]*)}/ie", "\$VM_LANG->\\1", $template);

/* Finish and Print out the Page */
echo $template;

?>
