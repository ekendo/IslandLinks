<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

session_save_path("/home/users/web/b453/hy.ekendodreamof/cgi-bin/tmp");
session_start();

/**
* This file prepares the VirtueMart framework
* It should be included whenever a VirtueMart function is needed
*
* @version $Id: virtuemart_parser.php,v 1.21.2.10 2006/07/04 05:56:59 soeren_nb Exp $
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
global $my, $db, $perm, $ps_function, $ps_module, $ps_html, $ps_vendor_id, $vendor_image,$vendor_image_url, $keyword,
	$ps_payment_method,$ps_zone,$sess, $page, $func, $pagename, $modulename, $vars, $VM_LANG, $cmd, $ok, $mosConfig_lang,
	$auth, $ps_checkout,$error, $error_type, $func_perms, $func_list, $func_class, $func_method, $func_list, $dir_list,
	$vendor_currency_display_style, $vendor_freeshipping, $mm_action_url, $limit, $limitstart, $mainframe;

// Raise memory_limit to 16M when it is too low
// Especially the product section needs much memory
$memLimit = @ini_get('memory_limit');
if( stristr( $memLimit, 'k') ) {
	$memLimit = str_replace( 'k', '', str_replace( 'K', '', $memLimit )) * 1024;
}
elseif( stristr( $memLimit, 'm') ) {
	$memLimit = str_replace( 'm', '', str_replace( 'M', '', $memLimit )) * 1024 * 1024;
}
if( $memLimit < 16777216 ) {
	@ini_set('memory_limit', '16M');
}

$option = mosGetParam( $_REQUEST, 'option' );

if( !defined( '_VM_PARSER_LOADED' )) {
	global $my;

	$page = mosgetparam($_REQUEST, 'page', "");
	$func = mosgetparam($_REQUEST, 'func', "");

	if( $my->id > 0 ) {
		// This is necessary to get the real GID
		$my->load( $my->id );
	}

	if( !file_exists( $mosConfig_absolute_path. "/administrator/components/com_virtuemart/virtuemart.cfg.php" )) {
		die( "<h3>The configuration file for VirtueMart is missing!</h3>It should be here: <strong>"
		. $mosConfig_absolute_path. "/administrator/components/com_virtuemart/virtuemart.cfg.php</strong>" );
	}
	// the configuration file for the Shop
	require_once( $mosConfig_absolute_path. "/administrator/components/com_virtuemart/virtuemart.cfg.php" );

	// The abstract language class
	require_once( CLASSPATH."language.class.php" );

	// load the Language File
	if (file_exists( ADMINPATH. 'languages/'.$mosConfig_lang.'.php' ))
		require_once( ADMINPATH. 'languages/'.$mosConfig_lang.'.php' );
	else
		require_once( ADMINPATH. 'languages/english.php' );

	/** @global vmLanguage $VM_LANG */
	$GLOBALS['VM_LANG'] = $GLOBALS['PHPSHOP_LANG'] =& new vmLanguage();

	/** @global Array $product_info: Stores Product Information for re-use */
	$GLOBALS['product_info'] = Array();

	/** @global Array $category_info: Stores Category Information for re-use */
	$GLOBALS['category_info'] = Array();

	/** @global Array $category_info: Stores Vendor Information for re-use */
	$GLOBALS['vendor_info'] = Array();

	// load the MAIN CLASSES
	// CLASSPATH is defined in the config file
	require_once(CLASSPATH."ps_database.php");
	require_once(CLASSPATH."ps_main.php");
	require_once(CLASSPATH."ps_cart.php");
	require_once(CLASSPATH."ps_html.php");
	require_once(CLASSPATH."ps_session.php");
	require_once(CLASSPATH."ps_function.php");
	require_once(CLASSPATH."ps_module.php");
	require_once(CLASSPATH."ps_perm.php");
	require_once(CLASSPATH."ps_shopper_group.php");
	require_once(CLASSPATH."vmAbstractObject.class.php");
	require_once(CLASSPATH."htmlTools.class.php");
	require_once(CLASSPATH."phpInputFilter/class.inputfilter.php");
	require_once(CLASSPATH."Log/Log.php");
	$vmLoggerConf = array(
		'buffering' => true
		);
	/**
	 * This Log Object will help us log messages and errors
	 * See http://pear.php.net/package/Log
	 * @global Log vmLogger
	 */
	$vmLogger = &vmLog::singleton('display', '', '', $vmLoggerConf, PEAR_LOG_TIP);
	$GLOBALS['vmLogger'] =& $vmLogger;
	// Instantiate the DB class
	$db = new ps_DB();

	// Instantiate the permission class
	$perm = new ps_perm();
	// Instantiate the HTML helper class
	$ps_html = new ps_html();

	// Constructor initializes the session!
	$sess = new ps_session();

	// Initialize the cart
	$cart = ps_cart::initCart();

	// Instantiate the module class
	$ps_module = new ps_module();
	// Instantiate the function class
	$ps_function = new ps_function();
	// Instantiate the ps_shopper_group class
	$ps_shopper_group = new ps_shopper_group();

	// Set the mosConfig_live_site to its' SSL equivalent
	if( $_SERVER['SERVER_PORT'] == 443 || @$_SERVER['HTTPS'] == 'on' || @strstr( $page, "checkout." )) {
		// temporary solution until we have
		// $mosConfig_secure_site
		$GLOBALS['real_mosConfig_live_site'] = $GLOBALS['mosConfig_live_site'];
		$GLOBALS['mosConfig_live_site'] = ereg_replace('/$','',SECUREURL);
		$mm_action_url = SECUREURL;
	}
	else {
		$mm_action_url = URL;
	}

	// Enable Mambo Debug Mode when Shop Debug is on
	if( DEBUG == "1" ) {
		$GLOBALS['mosConfig_debug'] = 1;
		$database->_debug = 1;
	}

	// Set Mambo's Cookies for the SSL Domain as well
	// This makes it possible to use Shared SSL
	$sess->prepare_SSL_Session();

	// the global file for PHPShop
	require_once( ADMINPATH . 'global.php' );

	$currency_display = vendor_currency_display_style( $vendor_currency_display_style );

	/** load Currency Display Class **/
	require_once( CLASSPATH.'class_currency_display.php' );
	/** @global CurrencyDisplay $CURRENCY_DISPLAY */
	$GLOBALS['CURRENCY_DISPLAY'] =& new CurrencyDisplay($currency_display["id"], $currency_display["symbol"], $currency_display["nbdecimal"], $currency_display["sdecimal"], $currency_display["thousands"], $currency_display["positive"], $currency_display["negative"]);

	if( $option == "com_virtuemart" ) {

		// Get sure that we have float values with a decimal point!
		@setlocale( LC_NUMERIC, 'en_US', 'en' );

		// some input validation for limitstart
		if (!empty($_REQUEST['limitstart'])) {
			$_REQUEST['limitstart'] = intval( $_REQUEST['limitstart'] );
		}

		$mosConfig_list_limit = isset( $mosConfig_list_limit ) ? $mosConfig_list_limit : SEARCH_ROWS;

		$keyword = substr( urldecode(mosgetparam($_REQUEST, 'keyword', '')), 0, 50 );

		unset( $_REQUEST["error"] );
		$user_id = intval( mosgetparam($_REQUEST, 'user_id', 0) );
		$_SESSION['session_userstate']['product_id'] = $product_id = intval( mosgetparam($_REQUEST, 'product_id', 0) );
		$_SESSION['session_userstate']['category_id'] = $category_id = intval( mosgetparam($_REQUEST, 'category_id', 0) );
		$user_info_id = mosgetparam($_REQUEST, 'user_info_id', '');

		$myInsecureArray = array('keyword' => $keyword,
									'user_info_id' => $user_info_id,
									'page' => $page,
									'func' => $func
									);
		/**
		 * This InputFiler Object will help us filter malicious variable contents
		 * @global vmInputFiler vmInputFiler
		 */
		$GLOBALS['vmInputFilter'] = new vmInputFilter();
		// prevent SQL injection
		$myInsecureArray = $GLOBALS['vmInputFilter']->safeSQL( $myInsecureArray );
		// Re-insert the escaped strings into $_REQUEST
		foreach( $myInsecureArray as $requestvar => $requestval) {
				$_REQUEST[$requestvar] = $requestval;
		}
		// Limit the keyword (=search string) length to 50
		$_SESSION['session_userstate']['keyword'] = $keyword = substr(mosgetparam($_REQUEST, 'keyword', ''), 0, 50);

		$user_info_id = mosgetparam($_REQUEST, 'user_info_id', 0);

		$vars = $_REQUEST;
	}

	// Get default and this users's Shopper Group
	$shopper_group = $ps_shopper_group->get_shoppergroup_by_id( $my->id );

	// User authentication
	$auth = $perm->doAuthentication( $shopper_group );

	if( $option == "com_virtuemart" ) {
		// Check if we have to run a Shop Function
		// and if the user is allowed to execute it
		$funcParams = $ps_function->checkFuncPermissions( $func );

		/**********************************************
		** Get Page/Directory Permissions
		** Displays error if directory is not registered,
		** user has no permission to view it , or file doesn't exist
		************************************************/
		if (empty($page)) {// default page
			if (defined('_PSHOP_ADMIN')) {
				$page = "store.index";
			}
			else {
				$page = HOMEPAGE;
			}
		}
		// Let's check if the user is allowed to view the page
		// if not, $page is set to ERROR_PAGE
		$pagePermissionsOK = $ps_module->checkModulePermissions( $page );

		$ok = true;

		if ( !empty( $funcParams["method"] ) ) {
			// Get the function parameters: function name and class name
			$q = "SELECT #__{vm}_module.module_name,#__{vm}_function.function_class";
			$q .= " FROM #__{vm}_module,#__{vm}_function WHERE ";
			$q .= "#__{vm}_module.module_id=#__{vm}_function.module_id AND ";
			$q .= "#__{vm}_function.function_method='".$funcParams["method"]."' AND ";
			$q .= "#__{vm}_function.function_class='".$funcParams["class"]."'";

			$db->query($q);
			$db->next_record();
			$class = $db->f('function_class');
			if( file_exists( CLASSPATH."$class.php" ) ) {
				// Load class definition file
				require_once( CLASSPATH.$db->f("function_class").".php" );
				$classname = str_replace( '.class', '', $funcParams["class"]);
				// create an object
				$string = "\$$classname = new $classname;";
				eval( $string );

				// RUN THE FUNCTION
				// $ok  = $class->function( $vars );
				$cmd = "\$ok = \$".$classname."->" . $funcParams["method"] . "(\$vars);";
				eval( $cmd );

				if ($ok == false) {
					$no_last = 1;
					if( $_SESSION['last_page'] != HOMEPAGE ) {
						$page = $_SESSION['last_page'];
					}
					$my_page= explode ( '.', $page );
					$modulename = $my_page[0];
					$pagename = $my_page[1];
					$_REQUEST['keyword']= $_SESSION['session_userstate']['keyword'];
					$_REQUEST['category_id']= $_SESSION['session_userstate']['category_id'];
					$_REQUEST['product_id']=$product_id = $_SESSION['session_userstate']['product_id'];
				}
			}
			else {
				$vmLogger->debug( "Could not include the class file $class" );
			}


			if (!empty($vars["error"])) {
				$error = $vars["error"];
			}
			if (!empty($error)) {
				echo vmCommonHTML::getErrorField($error);
			}

		}
		else {
			$no_last = 0;
			//$error="";
		}

		if ($ok == true && empty($error) && !defined('_DONT_VIEW_PAGE')) {
			$_SESSION['last_page'] = $page;
		}
	}
	// I don't get it, why Joomla uses masked gid values!
	if( !defined( '_PSHOP_ADMIN' )) {
		$my = $mainframe->getUser();
		if( isset( $my->_model )) {
			$my = $my->_model;
		}
	}

	// the Log object holds all error messages
	// here we flush the buffer and print out all messages
	$vmLogger->printLog();
	// Now we can switch to implicit flushing
	$vmLogger->_buffering = false;

	define( '_VM_PARSER_LOADED', 1 );
}
?>