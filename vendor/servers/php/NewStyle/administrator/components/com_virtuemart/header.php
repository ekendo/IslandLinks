<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
* Header file for the shop administration.
* shows all modules that are available to the user in a dropdown menu
*
* @version $Id: header.php,v 1.8.2.4 2006/04/05 18:16:48 soeren_nb Exp $
* @package VirtueMart
* @subpackage core
* @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*
* http://virtuemart.net
*/
mm_showMyFileName( __FILE__ );

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
global $error, $page, $ps_product, $ps_product_category;
$product_id = mosGetParam( $_REQUEST, 'product_id' );

if( is_array( $product_id )) {
    $recent_product_id = "";
}
else {
    $recent_product_id = $product_id;
}
        
$mod = array();
$q = "SELECT module_name,module_perms from #__{vm}_module WHERE module_publish='Y' ";
$q .= "AND module_name <> 'checkout' ORDER BY list_order ASC";
$db->query($q);
while ($db->next_record()) {
        if ($perm->check($db->f("module_perms"))) {
                $mod[] = $db->f("module_name");
}
}
if (!defined('_PSHOP_ADMIN')) {
  $my_path = "includes/js/ThemeOffice/";
  if( stristr( $_SERVER['PHP_SELF'], "index2.php" )) {
	echo '<script type="text/javascript" src="includes/js/mambojavascript.js"></script>
	<a href="index.php" title="Back"><h3>&nbsp;&nbsp;&gt;&gt; '.$VM_LANG->_PHPSHOP_BACK_TO_MAIN_SITE.' &lt;&lt;</h3></a>';
  }
  // We need the admin template css now, but which one? - so check here
  $adminTemplate = $_VERSION->PRODUCT == 'Joomla!' ? 'joomla_admin' : 'mambo_admin_blue';
?>
<link rel="stylesheet" href="<?php echo $my_path ?>theme.css" type="text/css" />
<link rel="stylesheet" href="administrator/templates/<?php echo $adminTemplate; ?>/css/template_css.css" type="text/css" />
<script language="JavaScript" src="includes/js/JSCookMenu.js" type="text/javascript"></script>
<script language="JavaScript" src="<?php echo $my_path ?>theme.js" type="text/javascript"></script>
        <?php 
}
    else {
      $my_path = "../includes/js/ThemeOffice/";
    }
    ?>
<script language="JavaScript" type="text/javascript">
var vmMenu =
[  <?php 

// To be able to display special characters,
// we must 
ob_start();

for ($i=0;$i < sizeof($mod);$i++) {  // recurse through all modules 

    $label = "\$lbl = \$VM_LANG->_PHPSHOP_".strtoupper($mod[$i])."_MOD;";
            eval($label);
             switch($mod[$i]) {
            
                case "admin": 
                    if ($i != 0) {
                    ?> ], _cmSplit, <?php 
                    } ?>
                    [null,'<?php echo $lbl ?>',null,null,'<?php echo $lbl ?>',
                        ['<img src="<?php echo $my_path ?>config.png" />','<?php echo $VM_LANG->_PHPSHOP_CONFIG ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=admin.show_cfg&option=com_virtuemart") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_CONFIG ?>'],
                        _cmSplit,
                        <?php if (defined('_PSHOP_ADMIN')) { ?>
                        ['<img src="<?php echo $my_path ?>users.png" />','<?php echo $VM_LANG->_PHPSHOP_USERS ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=admin.user_list") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_USERS ?>'],
                        _cmSplit,
                        <?php } ?>
                        ['<img src="<?php echo $my_path ?>sections.png" />','<?php echo $VM_LANG->_PHPSHOP_USER_FORM_COUNTRY ?>',null,null,'<?php echo $VM_LANG->_PHPSHOP_USER_FORM_COUNTRY ?>',
                            ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_COUNTRY_LIST_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=admin.country_list") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_COUNTRY_LIST_MNU ?>'],
                            ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_COUNTRY_LIST_ADD ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=admin.country_form") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_COUNTRY_LIST_ADD ?>']
                        ],
                        ['<img src="<?php echo $my_path ?>sections.png" />','<?php echo $VM_LANG->_PHPSHOP_PRODUCT_LIST_CURRENCY ?>',null,null,'<?php echo $VM_LANG->_PHPSHOP_PRODUCT_LIST_CURRENCY ?>',
                            ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_CURRENCY_LIST_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=admin.curr_list") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_CURRENCY_LIST_MNU ?>'],
                            ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_CURRENCY_LIST_ADD ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=admin.curr_form") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_CURRENCY_LIST_ADD ?>']
                        ],
                        _cmSplit,
                        ['<img src="<?php echo $my_path ?>sections.png" />','<?php echo $VM_LANG->_PHPSHOP_MODULES ?>',null,null,'<?php echo $VM_LANG->_PHPSHOP_MODULES ?>',
                            ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_MODULE_LIST_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=admin.module_list") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_MODULE_LIST_MNU ?>'],
                            ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_MODULE_FORM_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=admin.module_form") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_MODULE_FORM_MNU ?>']
                        ],
                        <?php if (!empty($_REQUEST['module_id'])) { ?>
                            ['<img src="<?php echo $my_path ?>sections.png" />','<?php echo $VM_LANG->_PHPSHOP_FUNCTIONS ?>',null,null,'<?php echo $VM_LANG->_PHPSHOP_FUNCTIONS ?>',
                                ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_FUNCTION_LIST_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=admin.function_list&module_id=".$_REQUEST['module_id']) ?>',null,'<?php echo $VM_LANG->_PHPSHOP_FUNCTION_LIST_MNU ?>'],
                                ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_FUNCTION_FORM_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=admin.function_form&module_id=".$_REQUEST['module_id']) ?>',null,'<?php echo $VM_LANG->_PHPSHOP_FUNCTION_FORM_MNU ?>']
                            ]
                
                        <?php } ?>
                        <?php break;
                        
                case "product":
                    include_class("product");
                    if ($i != 0) {
                    ?> ], _cmSplit, <?php 
                    } ?>
                    [null,'<?php echo $lbl ?>',null,null,'<?php echo $lbl ?>',
                        ['<img src="<?php echo $my_path ?>query.png" />','<?php echo $VM_LANG->_PHPSHOP_PRODUCT_CSV_UPLOAD ?>','<?php $sess->purl($_SERVER['PHP_SELF'] . "?pshop_mode=admin&page=product.csv_upload"); ?>',null,'<?php echo $VM_LANG->_PHPSHOP_PRODUCT_CSV_UPLOAD ?>'],
                        _cmSplit,
                        [_cmNoAction,'<td class="ThemeOfficeMenuFolderLeft">&nbsp;</td><td colspan="2" align="center" class="ThemeOfficeMenu"><strong><?php echo $lbl ?></strong></td>'],
                        <?php    
                        if (!empty($recent_product_id) && empty($_REQUEST['product_parent_id'])) { 
                              if (!isset($return_args)) $return_args = ""; ?> 
                        
                        ['<img src="<?php echo $my_path ?>sections.png" />','<?php echo $VM_LANG->_PHPSHOP_CURRENT_PRODUCT ?>',null,null,'<?php echo $VM_LANG->_PHPSHOP_CURRENT_PRODUCT ?>',
                            ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_ATTRIBUTE_LIST_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF'] . "?pshop_mode=admin&page=product.product_attribute_list&product_id=$recent_product_id&return_args=" . urlencode($return_args)); ?>',null,'<?php echo $VM_LANG->_PHPSHOP_ATTRIBUTE_LIST_MNU ?>'],
                            ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_ATTRIBUTE_FORM_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF'] . "?pshop_mode=admin&page=product.product_attribute_form&product_id=$recent_product_id&return_args=" . urlencode($return_args)); ?>',null,'<?php echo $VM_LANG->_PHPSHOP_ATTRIBUTE_FORM_MNU ?>'],
                            ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_PRICE_FORM_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF'] . "?pshop_mode=admin&page=product.product_price_list&product_id=$recent_product_id&return_args=" . urlencode($return_args)); ?>',null,'<?php echo $VM_LANG->_PHPSHOP_PRICE_FORM_MNU ?>'],
                            ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_PRODUCT_PRODUCT_TYPE_LIST_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF'] . "?pshop_mode=admin&page=product.product_product_type_list&product_id=$recent_product_id&return_args=" . urlencode($return_args)); ?>',null,'<?php echo $VM_LANG->_PHPSHOP_PRODUCT_PRODUCT_TYPE_LIST_MNU ?>'],
                            ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_PRODUCT_PRODUCT_TYPE_FORM_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF'] . "?pshop_mode=admin&page=product.product_product_type_form&product_id=$recent_product_id&return_args=" . urlencode($return_args)); ?>',null,'<?php echo $VM_LANG->_PHPSHOP_PRODUCT_PRODUCT_TYPE_FORM_MNU ?>']
                            <?php if ($ps_product->product_has_attributes($recent_product_id)) { ?>
                            ,['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_ADD_ITEM_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF'] . "?pshop_mode=admin&page=product.product_form&product_parent_id=$recent_product_id"); ?>',null,'<?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_ADD_ITEM_MNU ?>']
                            <?php } ?>
                        ],
                        _cmSplit,
                    <?php    }
                        elseif (!empty($_REQUEST['product_parent_id'])) { ?> 
                    
                            ['<img src="<?php echo $my_path ?>sections.png" />','<?php echo $VM_LANG->_PHPSHOP_CURRENT_ITEM ?>',null,null,'<?php echo $VM_LANG->_PHPSHOP_CURRENT_ITEM ?>',
                                ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_PRICE_FORM_MNU ?>','<?php @$sess->purl($_SERVER['PHP_SELF'] . "?pshop_mode=admin&page=product.product_price_list&product_id=$recent_product_id&product_parent_id=$product_parent_id&return_args=" . urlencode($return_args)); ?>',null,'<?php echo $VM_LANG->_PHPSHOP_PRICE_FORM_MNU ?>'],
                                ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_ADD_ANOTHER_ITEM_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF'] . "?pshop_mode=admin&page=product.product_form&product_parent_id=" . $product_parent_id); ?>',null,'<?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_ADD_ANOTHER_ITEM_MNU ?>'],
								['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_PRODUCT_PRODUCT_TYPE_LIST_MNU ?>','<?php @$sess->purl($_SERVER['PHP_SELF'] . "?pshop_mode=admin&page=product.product_product_type_list&product_id=$recent_product_id&product_parent_id=$product_parent_id&return_args=" . urlencode($return_args)); ?>',null,'<?php echo $VM_LANG->_PHPSHOP_PRODUCT_PRODUCT_TYPE_LIST_MNU ?>'],
                                ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_RETURN_LBL ?>','<?php $sess->purl($_SERVER['PHP_SELF'] . "?pshop_mode=admin&page=product.product_form&product_id=" . $product_parent_id); ?>',null,'<?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_RETURN_LBL ?>']
                            ],
                        _cmSplit,
                        <?php 
                        } ?>
                        ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_PRODUCT_LIST_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=product.product_list") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_PRODUCT_LIST_MNU ?>'],
                        ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=product.product_form") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_MNU ?>'],
                        _cmSplit,
                        ['<img src="<?php echo $my_path ?>media.png" />','<?php echo $VM_LANG->_PHPSHOP_FILEMANAGER ?>','<?php $sess->purl($_SERVER['PHP_SELF'] . "?pshop_mode=admin&page=product.filemanager"); ?>',null,'<?php echo $VM_LANG->_PHPSHOP_FILEMANAGER ?>'
                        <?php 
                        if( !empty($recent_product_id) ) { ?>
                            ,['<img src="<?php echo $my_path ?>media.png" />','<?php echo $VM_LANG->_PHPSHOP_FILEMANAGER_ADD ?>','<?php $sess->purl($_SERVER['PHP_SELF'] . "?pshop_mode=admin&page=product.file_form&product_id=$recent_product_id"); ?>',null,'<?php echo $VM_LANG->_PHPSHOP_FILEMANAGER_ADD ?>']],
                        <?php 
                        }
                        else echo "],";
                        ?>
                        _cmSplit,
                        ['<img src="<?php echo $my_path ?>sections.png" />','<?php echo $VM_LANG->_PHPSHOP_OTHER_LISTS ?>',null,null,'Inventory, Featured Products, Product Folders',
                            ['<img src="<?php echo $my_path ?>install.png" />','<?php echo $VM_LANG->_PHPSHOP_PRODUCT_INVENTORY_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF'] . "?pshop_mode=admin&page=product.product_inventory"); ?>',null,'<?php echo $VM_LANG->_PHPSHOP_PRODUCT_INVENTORY_MNU ?>'],
                            ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_SPECIAL_PRODUCTS ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=product.specialprod") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_SPECIAL_PRODUCTS ?>'],
                            ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_PRODUCT_FOLDERS ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=product.folders") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_PRODUCT_FOLDERS ?>']
                        ],
                        _cmSplit,
                        ['<img src="<?php echo $my_path ?>sections.png" />','<?php echo $VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_LBL ?>',null,null,'<?php echo $VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_LBL ?>',
                            ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_LIST_LBL ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=product.product_discount_list") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_LIST_LBL ?>'],
                            ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_ADDEDIT ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=product.product_discount_form") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_ADDEDIT ?>']
                        ],
                        _cmSplit,
                        
						['<img src="<?php echo $my_path ?>sections.png" />','<?php echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_LBL ?>',null,null,'<?php echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_LBL ?>',
                            ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_LIST_LBL ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=product.product_type_list") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_LIST_LBL ?>'],
                            ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_ADDEDIT ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=product.product_type_form") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_ADDEDIT ?>']
                        ],
                        _cmSplit,
                        [_cmNoAction,'<td class="ThemeOfficeMenuFolderLeft">&nbsp;</td><td colspan="2" align="center" class="ThemeOfficeMenu"><strong><?php echo $VM_LANG->_PHPSHOP_CATEGORIES ?></strong></td>'],
                        ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_CATEGORY_LIST_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=product.product_category_list") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_CATEGORY_LIST_MNU ?>'],
                        ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_CATEGORY_FORM_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=product.product_category_form") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_CATEGORY_FORM_MNU ?>'],
                        <?php 
                    break;


                case "vendor":
                    if ($i != 0) {
                    ?> ], _cmSplit, <?php 
                    } ?>
                    [null,'<?php echo $lbl ?>',null,null,'<?php echo $lbl ?>',
                        ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_VENDOR_LIST_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=vendor.vendor_list") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_VENDOR_LIST_MNU ?>'],
                        ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_VENDOR_FORM_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=vendor.vendor_form") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_VENDOR_FORM_MNU ?>'],
                        _cmSplit,
                        ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_VENDOR_CAT_LIST_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=vendor.vendor_category_list") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_VENDOR_CAT_LIST_MNU ?>'],
                        ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_VENDOR_CAT_FORM_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=vendor.vendor_category_form") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_VENDOR_CAT_FORM_MNU ?>']
                        <?php break;
                        
                case "shopper":
                    if ($i != 0) {
                    ?> ], _cmSplit, <?php 
                    } ?>
                    [null,'<?php echo $lbl ?>',null,null,'<?php echo $lbl ?>',
                        ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_SHOPPER_GROUP_LIST_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=shopper.shopper_group_list") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_SHOPPER_GROUP_LIST_MNU ?>'],
                        ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_SHOPPER_GROUP_FORM_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=shopper.shopper_group_form") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_SHOPPER_GROUP_FORM_MNU ?>']
                        <?php break;
                        
                case "tax":
                    if ($i != 0) {
                    ?> ], _cmSplit, <?php 
                    } ?>
                    [null,'<?php echo $lbl ?>',null,null,'<?php echo $lbl ?>',
                        ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_TAX_LIST_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=tax.tax_list") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_TAX_LIST_MNU ?>'],
                        ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_TAX_FORM_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=tax.tax_form") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_TAX_FORM_MNU ?>']
                    
                        <?php break;
                        
                case "store": 
                    if ($i != 0) {
                    ?> ], _cmSplit, <?php 
                    } ?>
                    [null,'<?php echo $lbl ?>',null,null,'<?php echo $lbl ?>',
                        ['<img src="<?php echo $my_path ?>sysinfo.png" />','<?php echo $VM_LANG->_PHPSHOP_STATISTIC_SUMMARY ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=store.index") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_STATISTIC_SUMMARY ?>'],
                        _cmSplit,
                        ['<img src="<?php echo $my_path ?>config.png" />','<?php echo $VM_LANG->_PHPSHOP_STORE_FORM_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=store.store_form") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_STORE_FORM_MNU ?>'],
                        <?php 
                        if ($_SESSION['auth']['perms'] != "admin" && defined('_PSHOP_ADMIN')) { ?>
                        ['<img src="<?php echo $my_path ?>users.png" />','<?php echo $VM_LANG->_PHPSHOP_USERS ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=store.user_list") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_USER_LIST_MNU ?>'],
                        <?php } ?>
                        _cmSplit,
                        ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_PAYMENT_METHOD_LIST_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=store.payment_method_list") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_PAYMENT_METHOD_LIST_MNU ?>'],
                        ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_PAYMENT_METHOD_FORM_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=store.payment_method_form") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_PAYMENT_METHOD_FORM_MNU ?>'],
                        _cmSplit,
                        ['<img src="<?php echo $my_path ?>content.png" />','Shipping Module List','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=store.shipping_modules") ?>',null,'Shipping Method List'],                        
                        _cmSplit,
                        ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_CREDITCARD_LIST_LBL ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=store.creditcard_list") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_CREDITCARD_LIST_LBL ?>'],
                        ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_CREDITCARD_FORM_LBL ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=store.creditcard_form") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_CREDITCARD_FORM_LBL ?>']
                        
                        <?php break;
                        
                case "zone":
                    if ($i != 0) {
                    ?> ], _cmSplit, <?php 
                    } ?>
                    [null,'<?php echo $lbl ?>',null,null,'<?php echo $lbl ?>',
                        ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_ZONE_ASSIGN_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=zone.assign_zones") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_ZONE_ASSIGN_MNU ?>'],
                        ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_ZONE_LIST_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=zone.zone_list") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_ZONE_LIST_MNU ?>'],
                        ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_ZONE_FORM_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=zone.zone_form") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_ZONE_FORM_MNU ?>']
                    
                        <?php break; 
                        
                case "reportbasic":
                    if ($i != 0) {
                    ?> ], _cmSplit, <?php 
                    } ?>
                    [null,'<?php echo $lbl ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=reportbasic.index") ?>',null,'<?php echo $lbl ?>'
                    
                        <?php break; 


                case "order":
                    if ($i != 0) {
                    ?> ], _cmSplit, <?php 
                    } ?>
                    [null,'<?php echo $lbl ?>',null,null,'<?php echo $lbl ?>',
                        ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_ORDER_LIST_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=order.order_list") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_ORDER_LIST_MNU ?>'],
                        ['<img src="<?php echo $my_path ?>sections.png" />','<?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PO_STATUS ?>',null,null,'<?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PO_STATUS ?>',
                            ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_ORDER_STATUS_LIST_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=order.order_status_list") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_ORDER_STATUS_LIST_MNU ?>'],
                            ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_ORDER_STATUS_FORM_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=order.order_status_form") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_ORDER_STATUS_FORM_MNU ?>']
                        ],
                        <?php break;
                        
                case "shipping":
                    if ($i != 0) {
                    ?> ], _cmSplit, <?php 
                    } ?>
                    [null,'<?php echo $lbl ?>',null,null,'<?php echo $lbl ?>',
                        ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_CARRIER_LIST_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF'] . "?pshop_mode=admin&page=shipping.carrier_list") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_CARRIER_LIST_MNU ?>'],
                        ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_CARRIER_FORM_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF'] . "?pshop_mode=admin&page=shipping.carrier_form") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_CARRIER_FORM_MNU ?>'],
                        _cmSplit,
                        ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_RATE_LIST_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF'] . "?pshop_mode=admin&page=shipping.rate_list") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_RATE_LIST_MNU ?>'],
                        ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_RATE_FORM_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF'] . "?pshop_mode=admin&page=shipping.rate_form") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_RATE_FORM_MNU ?>']
                    
                        <?php break; 
                        
                case "help":
                    if ($i != 0) {
                    ?> ], _cmSplit, <?php 
                    } ?>
                    [null,'<?php echo $lbl ?>',null,null,'<?php echo $lbl ?>',
                        ['<img src="<?php echo $my_path ?>document.png" />','About','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=help.about") ?>',null,'About'],
                        ['<img src="<?php echo $my_path ?>help.png" />','Help Topics','http://virtuemart.net/documentation/User_Manual/index.html','_blank','Help Topics'],
                        ['<img src="<?php echo $my_path ?>language.png" />','Forum','http://virtuemart.net/index.php?option=com_smf&Itemid=71','_blank','Forum']
                    
                        <?php break; 

                case "affiliate":
                    if ($i != 0) {
                    ?> ], _cmSplit, <?php 
                    } ?>
                    [null,'<?php echo $lbl ?>',null,null,'<?php echo $lbl ?>',
                        ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_AFFILIATE_LIST_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=affiliate.affiliate_list") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_AFFILIATE_LIST_MNU ?>'],
                        ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_AFFILIATE_EMAIL_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=affiliate.affiliate_email") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_AFFILIATE_EMAIL_MNU ?>'],
                        ['<img src="<?php echo $my_path ?>edit.png" />','Add affiliate','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=affiliate.shopper_list") ?>',null,'Add affiliate']
                    
                        <?php break; 
                        

                case "manufacturer":
                    if ($i != 0) {
                    ?> ], _cmSplit, <?php 
                    } ?>
                    [null,'<?php echo $lbl ?>',null,null,'<?php echo $lbl ?>',
                        ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_MANUFACTURER_LIST_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=manufacturer.manufacturer_list") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_MANUFACTURER_LIST_MNU ?>'],
                        ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_MANUFACTURER_FORM_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=manufacturer.manufacturer_form") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_MANUFACTURER_FORM_MNU ?>'],
                        _cmSplit,
                        ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_MANUFACTURER_CAT_LIST_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=manufacturer.manufacturer_category_list") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_MANUFACTURER_CAT_LIST_MNU ?>'],
                        ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_MANUFACTURER_CAT_FORM_MNU ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=manufacturer.manufacturer_category_form") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_MANUFACTURER_CAT_FORM_MNU ?>']
                        <?php break;
                        
                case "coupon":
                    if ($i != 0) {
                    ?> ], _cmSplit, <?php 
                    } ?>
                    [null,'<?php echo $lbl ?>',null,null,'<?php echo $lbl ?>',
                        ['<img src="<?php echo $my_path ?>content.png" />','<?php echo $VM_LANG->_PHPSHOP_COUPON_LIST ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=coupon.coupon_list") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_COUPON_LIST ?>'],
                        ['<img src="<?php echo $my_path ?>edit.png" />','<?php echo $VM_LANG->_PHPSHOP_COUPON_NEW_HEADER ?>','<?php $sess->purl($_SERVER['PHP_SELF']."?pshop_mode=admin&page=coupon.coupon_form") ?>',null,'<?php echo $VM_LANG->_PHPSHOP_COUPON_NEW_HEADER ?>']
                        <?php break;
          }
}

// Get the buffered menu code
$menu_code = ob_get_contents();
// clean this output buffer and end it
ob_end_clean();
// convert all special chars into HTML entities

$menu_code = htmlentities( $menu_code, ENT_NOQUOTES, vmGetCharset() );
// reconvert "htmlspecialchars"
$menu_code = str_replace( '&gt;', '>', 
                         str_replace( '&lt;', '<', 
                         str_replace( '&amp;', '&', $menu_code )));

echo $menu_code;

?>
          ]          
];
</script>

<img align="left" hspace="15" src="<?php echo IMAGEURL ?>ps_image/menu_logo.gif" alt="VirtueMart Cart Logo" />
<br/><div id="vmMenuID" style="border: 1px solid black;text-align:left;background:#E6D48E;" height="52" ></div>
<br />

<?php 
if (!empty($error) && ($page != ERRORPAGE)) {
     echo '<br /><div class="message">'. $error.'</div><br />';
}
?>
<script language="JavaScript" type="text/javascript">
cmDraw ('vmMenuID', vmMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
</script>


