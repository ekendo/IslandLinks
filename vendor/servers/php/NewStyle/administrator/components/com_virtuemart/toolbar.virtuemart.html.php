<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: toolbar.virtuemart.html.php,v 1.1.2.1 2006/04/10 19:10:12 soeren_nb Exp $
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
$_REQUEST['keyword'] = urldecode(mosgetparam($_REQUEST, 'keyword', 0));
$keyword = $_REQUEST['keyword'];

global $vmIcons;
$vmIcons['back_icon'] = $mosConfig_live_site."/administrator/images/back.png";
$vmIcons['back_icon2'] = $mosConfig_live_site."/administrator/images/back_f2.png";
$vmIcons['cancel_icon'] = $mosConfig_live_site."/administrator/images/cancel.png";
$vmIcons['cancel_icon2'] = $mosConfig_live_site."/administrator/images/cancel_f2.png";	
$vmIcons['new_icon'] = $mosConfig_live_site."/administrator/images/new.png";
$vmIcons['new_icon2'] = $mosConfig_live_site."/administrator/images/new_f2.png";
$vmIcons['save_icon'] = $mosConfig_live_site."/administrator/images/save.png";
$vmIcons['save_icon2'] = $mosConfig_live_site."/administrator/images/save_f2.png";
$vmIcons['delete_icon'] = $mosConfig_live_site."/administrator/images/delete.png";
$vmIcons['delete_icon2'] = $mosConfig_live_site."/administrator/images/delete_f2.png";
$vmIcons['publish_icon'] = $mosConfig_live_site."/administrator/images/publish.png";
$vmIcons['publish_icon2'] = $mosConfig_live_site."/administrator/images/publish_f2.png";	
$vmIcons['unpublish_icon'] = $mosConfig_live_site."/administrator/images/unpublish.png";
$vmIcons['unpublish_icon2'] = $mosConfig_live_site."/administrator/images/unpublish_f2.png";	
$vmIcons['apply_icon'] = $mosConfig_live_site."/administrator/images/apply.png";
$vmIcons['apply_icon2'] = $mosConfig_live_site."/administrator/images/apply_f2.png";

class MENU_virtuemart {
	/**
	* The function to handle all default page situations
	* not responsible for lists!
	*/
    function FORMS_MENU_SAVE_CANCEL() {     
        global $mosConfig_absolute_path,$mosConfig_live_site, $mosConfig_lang, $VM_LANG, 
        		$page, $limitstart,	$mosConfig_editor, $vmIcons;
		$bar = & JToolBar::getInstance('JComponent');
        $product_parent_id = mosGetParam( $_REQUEST, 'product_parent_id', 0 );
        $product_id = mosGetParam( $_REQUEST, 'product_id' );
		if( is_array( $product_id ))
			$product_id = "";
		// These editor arrays tell the toolbar to load correct "getEditorContents" script parts
		// This is necessary for WYSIWYG Editors like TinyMCE / mosCE / FCKEditor
        $editor1_array = Array('product.product_form' => 'product_desc', 'shopper.shopper_group_form' => 'shopper_group_desc',
								'product.product_category_form' => 'category_description', 'manufacturer.manufacturer_form' => 'mf_desc',
								'store.store_form' => 'vendor_store_desc',
								'product.product_type_parameter_form' => 'parameter_description',
								'product.product_type_form' => 'product_type_description',
								'vendor.vendor_form' => 'vendor_store_desc');
        $editor2_array = Array('store.store_form' => 'vendor_terms_of_service',
								'vendor.vendor_form' => 'vendor_terms_of_service');
								
		$editor1 = isset($editor1_array[$page]) ? $editor1_array[$page] : '';
		$editor2 = isset($editor2_array[$page]) ? $editor2_array[$page] : '';
		
		$script = '<script type="text/javascript">
        	function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == \'cancel\') {
				submitform( pressbutton );
				return;
			}
			';
              
		if ($editor1 != '') {
			ob_start();
			getEditorContents( 'editor1', $editor1 );
			$script .= ob_get_contents(); ob_end_clean();
		}
		if ($editor2 != '') {
			ob_start();
			getEditorContents( 'editor1', $editor1 );
			$script .= ob_get_contents(); ob_end_clean();
		}
		$script .= '
			submitform( pressbutton );
		}
		</script>';
		
        $bar->appendButton( 'Custom', $script );		
		
		vmMenuBar::startTable();
		
		if ($page == "product.product_form" && !empty($product_id)) {
			if( empty($product_parent_id) ) { 
				// add new attribute
				$href=$_SERVER['PHP_SELF']."?option=com_virtuemart&page=product.product_attribute_form&product_id=". $product_id ."&limitstart=". $limitstart;
				$alt = "&nbsp;". $VM_LANG->_PHPSHOP_ATTRIBUTE_FORM_MNU;
				vmMenuBar::customHref( $href, $vmIcons['new_icon'], $vmIcons['new_icon2'], $alt );
				vmMenuBar::spacer();
			}
			else {
                // back to parent product
				$href=$_SERVER['PHP_SELF']."?option=com_virtuemart&page=product.product_form&product_id=$product_parent_id&limitstart=".$limitstart;
				$alt = "&nbsp;". $VM_LANG->_PHPSHOP_PRODUCT_FORM_RETURN_LBL;
				vmMenuBar::customHref( $href, $vmIcons['back_icon'], $vmIcons['back_icon2'], $alt );
				vmMenuBar::spacer();
				// new child product
				$href=$_SERVER['PHP_SELF']."?option=com_virtuemart&page=product.product_form&product_parent_id=$product_parent_id&limitstart=". $limitstart;
				$alt = "&nbsp;". $VM_LANG->_PHPSHOP_PRODUCT_FORM_ADD_ANOTHER_ITEM_MNU;
				vmMenuBar::customHref( $href, $vmIcons['new_icon'], $vmIcons['new_icon2'], $alt );
				vmMenuBar::spacer();
			} 
			// Go to Price list
			$href = $_SERVER['PHP_SELF']."?page=product.product_price_list&product_id=$product_id&product_parent_id=$product_parent_id&limitstart=$limitstart&return_args=&option=com_virtuemart";
			$alt = "&nbsp;". $VM_LANG->_PHPSHOP_PRICE_LIST_MNU;
			vmMenuBar::customHref( $href, $vmIcons['new_icon'], $vmIcons['new_icon2'], $alt );
			vmMenuBar::spacer();
	
			// add product type
			$href= $_SERVER['PHP_SELF']."?option=com_virtuemart&page=product.product_product_type_form&product_id=$product_id&product_parent_id=$product_parent_id&limitstart=$limitstart";
			$alt = "&nbsp;". $VM_LANG->_PHPSHOP_PRODUCT_PRODUCT_TYPE_FORM_MNU;
			vmMenuBar::customHref( $href, $vmIcons['new_icon'], $vmIcons['new_icon2'], $alt );
			vmMenuBar::spacer();
			
			/*** Adding an item is only pssible, if the product has attributes ***/
			if (ps_product::product_has_attributes( $product_id ) ) { 
				// Add Item
				$href=$_SERVER['PHP_SELF']."?option=com_virtuemart&page=product.product_form&product_parent_id=$product_id&limitstart=<?php echo $limitstart";
				$alt = "&nbsp;". $VM_LANG->_PHPSHOP_PRODUCT_FORM_NEW_ITEM_LBL;
				vmMenuBar::customHref( $href, $vmIcons['new_icon'], $vmIcons['new_icon2'], $alt );
				vmMenuBar::spacer();
			}
			vmMenuBar::divider();
		}
		elseif( $page == "admin.country_form" ) {
            if( !empty( $_REQUEST['country_id'] )) {
				$href= $_SERVER['PHP_SELF'] ."?option=com_virtuemart&page=admin.country_state_form&country_id=". $_REQUEST['country_id'] ."&limitstart=". $limitstart;
				$alt = "&nbsp;".$VM_LANG->_PHPSHOP_ADD_STATE;
				vmMenuBar::customHref( $href, $vmIcons['new_icon'], $vmIcons['new_icon2'], $alt );
				vmMenuBar::spacer();
				
				$href = $_SERVER['PHP_SELF'] ."?option=com_virtuemart&page=admin.country_state_list&country_id=". $_REQUEST['country_id'] ."&limitstart=". $limitstart;
				$alt = "&nbsp;".$VM_LANG->_PHPSHOP_LIST_STATES;
				vmMenuBar::customHref( $href, $vmIcons['new_icon'], $vmIcons['new_icon2'], $alt );
				vmMenuBar::spacer();
				
				vmMenuBar::divider();
			}
		}
		vmMenuBar::spacer();
		
		vmMenuBar::save( 'save', _E_SAVE );
		
        vmMenuBar::spacer();
		
		if(empty($my_page)) {
			if ($page == "store.store_form")
				$my_page = "store.index";
			elseif ($page == "admin.user_address_form")
				$my_page = "admin.user_list";
			else
				$my_page = str_replace('form','list',$page);
		}
		if ($page == "admin.show_cfg")
				$my_page = "store.index";
		
		vmMenuBar::cancel();

		vmMenuBar::spacer();
		vmMenuBar::endTable();
    }
    /**
	* The function for all page which allow adding new items
	* usually when page= *.*_list
	*/
    function LISTS_MENU_NEW() {
        global $page, $mosConfig_live_site, $VM_LANG, $limitstart, $vmIcons;

        $my_page = str_replace('list','form',$page);
		
        vmMenuBar::addNew( "new", $my_page, _CMN_NEW );
		
        if ($page == 'admin.country_state_list') {
			// Back to the country
			vmMenuBar::divider();
			$href = $_SERVER['PHP_SELF']. '?option=com_virtuemart&page=admin.country_list';
			vmMenuBar::customHref( $href, $vmIcons['back_icon'], $vmIcons['back_icon2'], '&nbsp;'.$VM_LANG->_PHPSHOP_BACK_TO_COUNTRY );
        }
        elseif ($page == 'product.file_list') {
			// Back to the file manager
			vmMenuBar::divider();
			$href = $_SERVER['PHP_SELF']. "?option=com_virtuemart&page=product.filemanager";
			vmMenuBar::customHref( $href, $vmIcons['back_icon'], $vmIcons['back_icon2'], '&nbsp;'.$VM_LANG->_PHPSHOP_BACK_TO_FILEMANAGER );
        }
   
        vmMenuBar::spacer();
		
    }
	/**
	* Draws a list publish button
	*/
    function LISTS_MENU_PUBLISH( $funcName ) {
		
		vmMenuBar::publishList( $funcName );
		vmMenuBar::spacer();
		vmMenuBar::unpublishList( $funcName );
		vmMenuBar::spacer();
	}
	/**
	* Draws a list delete button
	*/
    function LISTS_MENU_DELETE( $funcName ) {
		
		vmMenuBar::deleteList( $funcName );
		
	}
	
	/** 
	* Handles special task selectors for pages
	* like the product list
	*/
	function LISTS_SPECIAL_TASKS( $page ) {
		global $mosConfig_live_site, $VM_LANG, $product_id, $vmIcons;
		switch( $page ) {
		
			case "product.product_list":
			
				if( empty($_REQUEST['product_parent_id']) ) { 
					// add new attribute
					$alt = "&nbsp;". $VM_LANG->_PHPSHOP_ATTRIBUTE_FORM_MNU;
					vmMenuBar::custom( "", "product.product_attribute_form", $vmIcons['new_icon'], $vmIcons['new_icon2'], $alt );
					vmMenuBar::spacer();
				}
				// Go to Price list
				$alt = "&nbsp;". $VM_LANG->_PHPSHOP_PRICE_LIST_MNU;
				vmMenuBar::custom( "", "product.product_price_list", $vmIcons['new_icon'], $vmIcons['new_icon2'], $alt );
				
				vmMenuBar::spacer();
		
				// add product type
				$alt = "&nbsp;". $VM_LANG->_PHPSHOP_PRODUCT_PRODUCT_TYPE_FORM_MNU;
				vmMenuBar::custom( "", "product.product_product_type_form", $vmIcons['new_icon'], $vmIcons['new_icon2'], $alt );
				
				vmMenuBar::spacer();
		
				/*** Adding an item is only pssible, if the product has attributes ***/
				if (ps_product::product_has_attributes( $product_id ) ) { 
					// Add Item
					$alt = "&nbsp;". $VM_LANG->_PHPSHOP_PRODUCT_FORM_NEW_ITEM_LBL;
					vmMenuBar::custom( "", "product.product_child_form", $vmIcons['new_icon'], $vmIcons['new_icon2'], $alt );
				}
				vmMenuBar::divider();
				vmMenuBar::spacer();
				break;
			
			case "admin.country_list":

					$alt = "&nbsp;".$VM_LANG->_PHPSHOP_ADD_STATE;
					vmMenuBar::custom( "", "admin.country_state_form", $vmIcons['new_icon'], $vmIcons['new_icon2'], $alt );
					vmMenuBar::spacer();
					
					$alt = "&nbsp;".$VM_LANG->_PHPSHOP_LIST_STATES;
					vmMenuBar::custom( "", "admin.country_state_list", $vmIcons['new_icon'], $vmIcons['new_icon2'], $alt );
					vmMenuBar::spacer();
					vmMenuBar::divider();
					vmMenuBar::spacer();
				break;
			
			default:
			
		}
		
	}
	
	
	/**
	* Draws the menu for a New users
	*/
	function _NEW_USERS() {
		vmMenuBar::startTable();
		vmMenuBar::save();
		vmMenuBar::cancel();
		vmMenuBar::spacer();
		vmMenuBar::endTable();
	}
	
	function _EDIT_USERS() {
		vmMenuBar::startTable();
		vmMenuBar::save();
		vmMenuBar::cancel();
		vmMenuBar::spacer();
		vmMenuBar::endTable();
	}
	
	function _DEFAULT_USERS() {
		vmMenuBar::startTable();
		vmMenuBar::addNew();
		vmMenuBar::editList();
		vmMenuBar::deleteList();
		vmMenuBar::spacer();
		vmMenuBar::custom( 'remove_as_customer', 'admin.user_list', IMAGEURL .'ps_image/remove_as_customer.png', IMAGEURL .'ps_image/remove_as_customer_f2.png' );
		vmMenuBar::spacer();
		vmMenuBar::endTable();
	}
  
}
?>
