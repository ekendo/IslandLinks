<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

session_save_path("/home/users/web/b453/hy.ekendodreamof/cgi-bin/tmp");
session_start();

/**
* mambo-phphop Main Module
* NOTE: THIS MODULE REQUIRES AN INSTALLED MAMBO-PHPSHOP COMPONENT!
*
* @version $Id: mod_virtuemart.php,v 1.4.2.3 2006/06/29 18:27:12 soeren_nb Exp $
* @package VirtueMart
* @subpackage modules
*
* @copyright (C) 2004-2005 Soeren Eberhardt
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*
* VirtueMart is Free Software.
* VirtueMart comes with absolute no warranty.
*
* www.virtuemart.net
*/

/* Load the virtuemart main parse code */
require_once( $mosConfig_absolute_path.'/components/com_virtuemart/virtuemart_parser.php' );

require_once(CLASSPATH.'ps_product_category.php');
$ps_product_category =& new ps_product_category();

global $module, $root_label, $mosConfig_allowUserRegistration, $jscook_type, $jscookMenu_style, $jscookTree_style, $VM_LANG, $sess, $mm_action_url;

$category_id = mosGetParam( $_REQUEST, 'category_id' );

$mod_dir = dirname( __FILE__ );

/* Get module parameters */
$show_login_form = $params->get( 'show_login_form', 'no' );
$show_categories = $params->get( 'show_categories', 'yes' );
$show_listall = $params->get( 'show_listall', 'yes' );
$show_minicart = $params->get( 'show_minicart', 'yes' );
$show_productsearch = $params->get( 'show_productsearch', 'yes' );
$show_product_parameter_search = $params->get( 'show_product_parameter_search', 'no' );
$menutype = $params->get( 'menutype', "links" );
$class_sfx = $params->get( 'class_sfx', '' );
$pretext = $params->get( 'pretext', '' );
$jscookMenu_style = $params->get( 'jscookMenu_style', 'ThemeOffice' );
$jscookTree_style = $params->get( 'jscookTree_style', 'ThemeXP' );
$jscook_type = $params->get( 'jscook_type', 'menu' );
$menu_orientation = $params->get( 'menu_orientation', 'hbr' );
$_REQUEST['root_label'] = $params->get( 'root_label', 'Shop' );

$class_mainlevel = "mainlevel".$class_sfx;

// This is "Categories:" by default. Change it in the Module Parameters Form
echo $pretext;

// update the cart because something could have
// changed while running a function
$cart = $_SESSION["cart"];
$auth = $_SESSION["auth"];

if( $show_categories == "yes" ) {


  if ( $menutype == 'links' ) {
	/* MENUTPYE LINK LIST */
    echo $ps_product_category->get_category_tree( $category_id, $class_mainlevel );

  }
  elseif( $menutype == "transmenu" ) {
      /* TransMenu script to display a DHTML Drop-Down Menu */
      include( $mod_dir.'/vm_transmenu.php' );

  }
  elseif( $menutype == "dtree" ) {
      /* dTree script to display structured categories */
      include( $mod_dir.'/vm_dtree.php' );

  }
  elseif( $menutype == "jscook" ) {
      /* JSCook Script to display structured categories */
      include( $mod_dir.'/vm_JSCook.php' );

  }
}
?>
<table cellpadding="1" cellspacing="1" border="0" width="100%">
<?php
// "List all Products" Link
if ( $show_listall == 'yes' ) { ?>
    <tr>
      <td colspan="2"><br />
          <a href="<?php $sess->purl($mm_action_url."index.php?page=shop.browse") ?>">
          <?php echo $VM_LANG->_PHPSHOP_LIST_ALL_PRODUCTS ?>
          </a>
      </td>
    </tr>
  <?php
}

// Product Search Box
if ( $show_productsearch == 'yes' ) { ?>

  <!--BEGIN Search Box -->
  <tr>
    <td colspan="2">
	  <hr />
      <label for="shop_search_field"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_SEARCH_LBL ?></label>
      <form action="<?php echo $mm_action_url."index.php" ?>" method="get">
        <input id="shop_search_field" title="<?php echo $VM_LANG->_PHPSHOP_SEARCH_TITLE ?>" class="inputbox" type="text" size="12" name="keyword" />
        <input class="button" type="submit" name="Search" value="<?php echo $VM_LANG->_PHPSHOP_SEARCH_TITLE ?>" />
		<input type="hidden" name="Itemid" value="<?php echo intval(@$_REQUEST['Itemid']) ?>" />
		<input type="hidden" name="option" value="com_virtuemart" />
		<input type="hidden" name="page" value="shop.browse" />
	  </form>
        <br />
        <a href="<?php echo $sess->url($mm_action_url."index.php?option=com_virtuemart&page=shop.search") ?>">
            <?php echo $VM_LANG->_PHPSHOP_ADVANCED_SEARCH ?>
        </a><?php /** Changed Product Type - Begin */
	if ( $show_product_parameter_search == 'yes' ) { ?>
        <br />
        <a href="<?php echo $sess->url($mm_action_url."index.php?option=com_virtuemart&page=shop.parameter_search") ?>" title="<?php echo $VM_LANG->_PHPSHOP_PARAMETER_SEARCH ?>">
            <?php echo $VM_LANG->_PHPSHOP_PARAMETER_SEARCH ?>
        </a>
<?php } /** Changed Product Type - End */ ?>
        <hr />
    </td>
  </tr>
  <!-- End Search Box -->
<?php
}

$perm = new ps_perm;
if ($perm->check("admin,storeadmin")
      && ((!stristr($my->usertype, "admin") ^ PSHOP_ALLOW_FRONTENDADMIN_FOR_NOBACKENDERS == '' )
          || stristr($my->usertype, "admin")
      )
    ) { ?>
    <tr>
      <td colspan="2"><a class="<?php echo $class_mainlevel ?>" href="<?php $sess->purl(SECUREURL . "index2.php?page=store.index&pshop_mode=admin");
      echo "\">".$VM_LANG->_PHPSHOP_ADMIN_MOD; ?></a></td>
    </tr>
  <?php }
   if ($perm->is_registered_customer($auth["user_id"])) {
  ?>
    <tr>
      <td colspan="2"><a class="<?php echo $class_mainlevel ?>" href="<?php $sess->purl(SECUREURL . "index.php?page=account.index");?>">
      <?php echo $VM_LANG->_PHPSHOP_ACCOUNT_TITLE ?></a></td>
    </tr><?php
}

if ( $show_login_form == "yes" ) {
	/*
	START - HACK BY TOM TO SHOW MAMBO PHPSHOP LOGOUT FORM IF USER'S LOGGED IN
	*/
    if ($my->id) {
?>
	<tr>
	  <td colspan="2" valign="top">
		<div align="left" style="margin: 0px; padding: 0px;">
		  <form action="<?php echo $mm_action_url ?>index.php?option=logout" method="post" name="login" id="login">
			<input type="submit" name="Submit" class="button" value="<?php echo _BUTTON_LOGOUT ?>" /><br /><hr />
			<input type="hidden" name="op2" value="logout" />
			<input type="hidden" name="return" value="<?php echo $mm_action_url ?>index.php" />
			<input type="hidden" name="lang" value="english" />
			<input type="hidden" name="message" value="0" />
		  </form>
		</div>
	  </td>
	</tr>
<?php
	}
	else
	{
		?>
		<tr>
		  <td colspan="2" align="left" valign="top" style="margin: 0px; padding: 0px;">
			<form action="<?php echo $mm_action_url ?>index.php?option=login" method="post" name="login" id="login">
			<label for="username_field"><?php echo _USERNAME ?></label><br/>
			<input class="inputbox" type="text" id="username_field" size="12" name="username" />
		  <br/>
			<label for="password_field"><?php echo _PASSWORD ?></label><br/>
			<input type="password" class="inputbox" id="password_field" size="12" name="passwd" />
			<input type="hidden" value="login" name="op2" />
			<input type="hidden" value="yes" name="remember" />
			<input type="hidden" value="<?php $sess->purl($mm_action_url . "index.php?". $_SERVER['QUERY_STRING']); ?>" name="return" />
		  <br/>
			<input type="submit" value="<?php echo _BUTTON_LOGIN ?>" class="button" name="Login" />

			<?php
		  	// used for spoof hardening
			$validate = vmSpoofValue(1);
			?>
			<input type="hidden" name="<?php echo $validate; ?>" value="1" />
			</form>
		  </td>
		</tr>
		<tr>
		  <td colspan="2">
			<a href="<?php echo sefRelToAbs( 'index.php?option=com_registration&amp;task=lostPassword&amp;Itemid='.$_REQUEST['Itemid'] ); ?>">
			<?php echo _LOST_PASSWORD; ?>
			</a>
		  </td>
		</tr>
		<?php
		if( $mosConfig_allowUserRegistration == '1' ) {
		?>
			<tr>
			  <td colspan="2">
				<?php echo _NO_ACCOUNT; ?>
				<a href="<?php $sess->purl( SECUREURL.'index.php?option=com_virtuemart&amp;page=shop.registration' ); ?>">
				<?php echo _CREATE_ACCOUNT; ?>
				</a>
				<hr />
			  </td>
			</tr>
			<?php
		}
	}
	/*
	END - HACK BY TOM TO SHOW MAMBO PHPSHOP LOGIN FORM IF USER'S NOT LOGGED IN
	*/
  }

/*********************
** DOWNLOAD MOD
**/
if (ENABLE_DOWNLOADS == '1') { ?>
  <tr>
    <td colspan="2">
        <a class="<?php echo $class_mainlevel ?>" href="<?php $sess->purl(SECUREURL . "index.php?page=shop.downloads");?>">
        <?php echo $VM_LANG->_PHPSHOP_DOWNLOADS_TITLE ?>
        </a>
    </td>
  </tr><?php
}
/**
** END DOWNLOAD MOD
/*********************/

if (USE_AS_CATALOGUE != '1' && $show_minicart == 'yes') {
?>
    <tr>
        <td colspan="2">
		<a class="<?php echo $class_mainlevel ?>" href="<?php $sess->purl($mm_action_url."index.php?page=shop.cart")?>"><?php echo $VM_LANG->_PHPSHOP_CART_SHOW ?></a>
	</td>
    </tr>
    <tr>
        <td colspan="2"><?php include (PAGEPATH.'shop.basket_short.php') ?></td>
    </tr>
        <?php
} ?>

</table>
<?php
// Just for SIMPLEBOARD compatibility !
if (@$_REQUEST['option'] != "com_virtuemart") $db = array();   ?>
