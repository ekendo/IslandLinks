<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
* This is no class! This file only provides core virtuemart functions.
* 
* @version $Id: ps_main.php,v 1.11.2.4 2006/05/06 10:05:27 soeren_nb Exp $
* @package VirtueMart
* @subpackage classes
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


/**
 * Validates an uploaded image. Creates UNIX commands to be used
 * by the process_images function, which has to be called after
 * the validation.
 * @author jep
 * @author soeren
 *
 * @param array $d
 * @param string $field_name The name of the field in the table $table, the image is assigned to [e.g. product_thumb_image]
 * @param string $table_name The name of a table in the database [e.g. #__{vm}_product]
 * @return boolean When the upload validation was sucessfull, true is returned, otherwise false
 */
function validate_image(&$d,$field_name,$table_name) {
	global $vmLogger;

	
	/* Generate the path to images */
	$path  = IMAGEPATH;
	$path .= $table_name . "/";

	/* Check permissions to write to destination directory */
	// Workaround for Window$
	if(strstr($path , ":" )) {
		$path_begin = substr( $path, strpos( $path , ":" )+1, strlen($path));
		$path = str_replace( "//", "/", $path_begin );
	}
	if (!is_dir( $path )) {
		mkdir( $path, 0777 );
		$vmLogger->debug( 'Had to create the directory '.$path);
	}
	
	if( !is_writable($path) && !empty( $_FILES[$field_name]["tmp_name"]) ) {
		$vmLogger->err( 'Cannot write to '.$table_name.' image directory: '.$path );
		return false;
	}
	// Check for upload errors
	require_once( CLASSPATH. 'ps_product_files.php');
	ps_product_files::checkUploadedFile( $field_name );
	
	$tmp_field_name = str_replace( "thumb", "full", $field_name );
	//	Class for resizing Thumbnails
	require_once( CLASSPATH . "class.img2thumb.php");
			
	if( @$d[$tmp_field_name.'_action'] == 'auto_resize'
		&& empty($d['resizing_prepared'])
	) {
		// Resize the Full Image
		if( !empty ( $_FILES[$tmp_field_name]["tmp_name"] )) {
			$full_file = $_FILES[$tmp_field_name]["tmp_name"];
			$image_info = getimagesize($full_file);
		}
		elseif( !empty($d[$tmp_field_name."_url"] )) {
			$tmp_file_from_url = $full_file = ps_product_files::getRemoteFile($d[$tmp_field_name."_url"]);
			if( $full_file ) {
				$vmLogger->debug( 'Successfully fetched the image file from '.$d[$tmp_field_name."_url"].' for later resizing' );
				$image_info = getimagesize($full_file);
			}
		}
		if( !empty( $image_info )) {
			
			if( $image_info[2] == 1) {
				if( function_exists("imagegif") ) {
					$ext = ".gif";
					$noimgif="";
				}
				else {
					$ext = ".jpg";
					$noimgif = ".gif";
				}
			}
			elseif( $image_info[2] == 2) {
				$ext = ".jpg";
				$noimgif="";
			}
			elseif( $image_info[2] == 3) {
				$ext = ".png";
				$noimgif="";
			}
			$vmLogger->debug( 'The resized Thumbnail will have extension '.$noimgif.$ext );
			/* Generate Image Destination File Name */
			$to_file_thumb = md5(uniqid("VirtueMart"));
			$fileout = IMAGEPATH."/product/resized/".$to_file_thumb."_".PSHOP_IMG_WIDTH."x".PSHOP_IMG_HEIGHT.$noimgif.$ext;
			$neu = new Img2Thumb( $full_file, PSHOP_IMG_WIDTH, PSHOP_IMG_HEIGHT, $fileout, 0, 255, 255, 255 );
			$vmLogger->debug( 'Finished creating the thumbnail' );
						
			if( isset($tmp_file_from_url) ) unlink( realpath($tmp_file_from_url) );
			$tmp_field_name = str_replace( "full", "thumb", $tmp_field_name );
			$tmp_field_name = str_replace( "_url", "", $tmp_field_name );
			$_FILES[$tmp_field_name]['tmp_name'] = $fileout;
			$_FILES[$tmp_field_name]['name'] = basename($fileout);
			$d[$tmp_field_name] = basename($fileout);

			$d['resizing_prepared'] = "1";
		}
	}

	$temp_file = isset($_FILES[$field_name]['tmp_name']) ? $_FILES[$field_name]['tmp_name'] : "";
	$file_type = isset($_FILES[$field_name]['type']) ? $_FILES[$field_name]['type'] : "";

	$orig_file = isset($_FILES[$field_name]["name"]) ? $_FILES[$field_name]['name'] : "";
	$curr_file = isset($_REQUEST[$field_name."_curr"]) ? $_REQUEST[$field_name."_curr"] : "";
	
	/**
	 * The commands to be executed by the process_images
         * function are returned as a string here.  The
         * commands are EVAL commands separated by ";"
         */
        if (empty($d['image_commands'])) {
                $d['image_commands'] = array();
        }

        /* Generate text to display in error messages */
	if (eregi("thumb",$field_name)) {
		$image_type = "thumbnail image";
	} elseif (eregi("full",$field_name))  {
		$image_type = "full image";
	} else {
		$image_type = ereg_replace("_"," ",$field_name);
	}
	
	/* If User types "none" in Image Upload Field */
	if ($d[$field_name."_action"] == "delete") {
		/* If there is a current image file */
                if (!empty($curr_file)) {
                        
                        $delete = str_replace("\\", "/", realpath($path."/".$curr_file));
                        $d["image_commands"][] = "if( file_exists( \"$delete\" ) ) {
                                                                                \$ret = unlink(\"$delete\");
                                                                                }
                                                                                else {
                                                                                        \$ret = true;
                                                                                }";
                        
                        $vmLogger->debug( 'Preparing: delete old '.$image_type.' '.$delete );
                        /* Remove the resized image if exists */
                        if( PSHOP_IMG_RESIZE_ENABLE=="1" && $image_type == "thumbnail image") {
				$pathinfo = pathinfo( $delete );
				isset($pathinfo["dirname"]) or $pathinfo["dirname"] = "";
                                isset($pathinfo["extension"]) or $pathinfo["extension"] = "";
                                $filehash = basename( $delete, ".".$pathinfo["extension"] );
                                $resizedfilename = $pathinfo["dirname"]."/resized/".$filehash."_".PSHOP_IMG_WIDTH."x".PSHOP_IMG_HEIGHT.".".$pathinfo["extension"];
                                
                                $d["image_commands"][] = "if( file_exists(\"$resizedfilename\")) {
                                                                                        \$ret = unlink(\"$resizedfilename\");
                                                                                }
                                                                                else {
                                                                                        \$ret = true;
                                                                                }";
                                $vmLogger->debug( 'Preparing: delete resized thumbnail '.$resizedfilename );
                                
                        }

                }
		$d[$field_name] = "";
		return true;
	}
	/* If upload fails */
	elseif($orig_file and $temp_file == "none") {
		$vmLogger->err( $image_type.' upload failed.');
		return false;
	}

	else {
		// If nothing was entered in the Upload box, there is no image to process
		if (!$orig_file) {
			$d[$field_name] = $curr_file;
			return true;
		}
	}
	if( empty( $temp_file )) {
		$vmLogger->err( 'The File Upload was not successful: there\'s no uploaded temporary file!' );
		return false;
	}
        // Check permissions to read temp file
        if (!is_readable($temp_file)) {
                $vmLogger->err( 'Cannot read uploaded '.$image_type.' temp file: '.$temp_file.'.
                                        One common reason for this that the upload path cannot be accessed 
                                        because of the open_basedir settings in the php.ini. Or maybe the 
                                        directory for temporary upload files on this server is not readable.' );
                return false;
        }

	// Generate Image Destination File Name
	$to_file = md5(uniqid("VirtueMart"));

	/* Check image file format */
	if( $orig_file != "none" ) {
		$to_file .= $ext = '.'.Img2Thumb::GetImgType( $temp_file );
		if( !$to_file ) {
			$vmLogger->err( $image_type.' file is invalid: '.$file_type.'.' );
			return false;
		}
	}
	/*
	** If it gets to this point then there is an uploaded file in the system
	** and it is a valid image file.
	*/


	/* If Updating */
        if (!empty($curr_file)) {
                /* Command to remove old image file */
                $delete = str_replace( "\\", "/", realpath($path)."/".$curr_file);
                
                $d["image_commands"][] = "if( file_exists( \"$delete\" ) ) {
                                                                        \$ret = unlink(\"$delete\");
                                                                  } else { \$ret = true; }";
                $vmLogger->debug( 'Preparing: delete old '.$image_type.' '.$delete );
                
                /* Remove the resized image if exists */
                if( PSHOP_IMG_RESIZE_ENABLE=="1" && $image_type == "thumbnail image") {
                        $pathinfo = pathinfo( $delete );
                        $filehash = basename( $delete, ".".$pathinfo["extension"] );
                        $resizedfilename = $pathinfo["dirname"]."/resized/".$filehash."_".PSHOP_IMG_WIDTH."x".PSHOP_IMG_HEIGHT.".".$pathinfo["extension"];
                        
                        $d["image_commands"][] = "if( file_exists(\"$resizedfilename\")) {
                                                                                \$ret = unlink(\"$resizedfilename\");
                                                                          } else { \$ret = true; }";
                        $vmLogger->debug( 'Preparing: delete resized thumbnail '.$resizedfilename );
                        
                }
        }

        /* Command to move uploaded file into destination directory */
        $d["image_commands"][] = "\$ret = copy(\"".addslashes(realpath($temp_file))."\", \"".$path.$to_file."\");";
        
        $d["image_commands"][] = "if( file_exists( realpath(\"$temp_file\") )) {
                                                                \$ret = @unlink(\"".addslashes(realpath($temp_file))."\" );
                                                          }
                                                          else {
                                                                \$ret = true;
                                                          }";
        

        /* Return new image file name */
        $d[$field_name] = $to_file;
	return true;
}

/**
 * The function that safely executes $d['image_commands'] and catches errors
 *
 * @param array $d
 * @return boolean True when all image commands were executed successfully, false when not
 */
function process_images(&$d) {
	global $vmLogger;
	require_once(CLASSPATH.'ps_product_files.php');
        
        if (!empty($d["image_commands"])) {

                foreach ( $d['image_commands'] as $command ) {
                        $command = str_replace('\\"', '"', $command);
                        
                        $res = eval($command . ";");
                        if( $res === false ) {
                                $vmLogger->err( 'Parse Error in this command: '.$command);
                        }
                        if ($ret == false) {
                                $vmLogger->err ( 'The following image update command failed: '. $command );
                                return false;
                        }
                        else {
                                $vmLogger->debug( 'Successfully processed image command: '.$command );
                        }

                }
                $d["image_commands"] = array();
        }
        return true;
}

/**************************************************************************
** name: process_date_time
** created by: jep
** description:
** parameters:
** returns:
***************************************************************************/
/**
 * This function validates a given date and creates a timestamp
 * @deprecated 
 *
 * @param array $d
 * @param string $field The name of the field
 * @param string $type
 * @return boolean
 */
function process_date_time(&$d,$field,$type="") {
	$month = $d["$field" . "_month"];
	$day = $d["$field" . "_day"];
	$year = $d["$field" . "_year"];
	$hour = $d["$field" . "_hour"];
	$minute = $d["$field" . "_minute"];
	$use = $d["$field" . "_use"];
	$valid = true;

	/* If user unchecked "Use date and time" then time = 0 */
	if (!$use) {
		$d[$field] = 0;
		return true;
	}
	if (!checkdate($month,$day,$year)) {
		$d["error"] .= "ERROR: $type date is invalid.";
		$valid = false;
	}
	if (!$hour and !$minute) {
		$hour = 0;
		$minute = 0;
	} elseif ($hour < 0 or $hour > 23 or $minute < 0 or $minute > 59) {
		$d["error"] .= "ERROR: $type time is invalid.";
		$valid = false;
	}

	if ($valid) {
		$d[$field] = mktime($hour,$minute,0,$month,$day,$year);
	}

	return $valid;
}

/**
 * Validates an email address by using regular expressions
 * Does not resolve the domain name!
 *
 * @param string $email
 * @return boolean The result of the validation
 */
function mShop_validateEmail( $email ) {

	if(ereg('^[_a-z0-9A-Z+-]+(\.[_a-z0-9A-Z+-]+)*@[a-z0-9A-Z-]+(\.[a-z0-9A-Z-]+)*$', $email)) {      
		return(true);
	}
	else {
		return(false);
	}
}


/****************************************************************************
*    function: post
*  created by: Steve Endredy
* description: http post (used by mShop_validateEUVat)
*     returns: html result (string)
****************************************************************************/
/**
 * Process a http post command
 *
 * @param string $host The name of the host that is posted to
 * @param string $query The URL that is posted
 * @param string $others Additional http headers when needed
 * @return string The answer from the host
 */
function mShop_post( $host,$query,$others='' ){
	$path=explode('/',$host);
	$host=$path[0];
	unset($path[0]);
	$path='/'.(implode('/',$path));
	$post="POST $path HTTP/1.1\r\nHost: $host\r\nContent-type: application/x-www-form-urlencoded\r\n${others}User-Agent: Mozilla 4.0\r\nContent-length: ".strlen($query)."\r\nConnection: close\r\n\r\n$query";
	$h=fsockopen($host,80);
	fwrite($h,$post);
	for($a=0,$r='';!$a;){
		$b=fread($h,8192);
		$r.=$b;
		$a=(($b=='')?1:0);
	}
	fclose($h);
	return $r;
}


/**
 * Validates an EU-vat number
 * @author Steve Endredy
 * @param string $euvat EU-vat number to validate
 * @return boolean The result of the validation
 */
function mShop_validateEUVat( $euvat ){
	$eurl = "www.europa.eu.int/comm/taxation_customs/vies/cgi-bin/viesquer";
	if(!ereg("([a-zA-Z][a-zA-Z])[- ]*([0-9]*)", $euvat, $r)){
		return false;
	}
	$CountryCode = $r[1];
	$VAT = $r[2];
	$query = "Lang=EN&MS=$CountryCode&ISO=$CountryCode&VAT=$VAT";
	$ret = mShop_post($eurl, $query);
	if (ereg("Yes, valid VAT number", $ret))
	return true;
	return false;

}

/**
 * Returns the current time in microseconds
 *
 * @return float current time in microseconds
 */
function utime()
{
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}

/****************************************************************************
*    function: in_list
*  created by: pablo
* description:
*  parameters:
*     returns:
****************************************************************************/
/**
 * Checks if $item is in $list
 *
 * @param array $list
 * @param string $item
 * @return mixed An integer representing the postion of $item in $list, false when not in list
 */
function in_list($list, $item) {
	for ($i=0;$i<$list["cnt"];$i++) {
		if (!strcmp($list[$i]["name"],$item)) {
			return $i;
		}
	}
	return False;
}

/**
 * reads a file and returns its content as a string
 *
 * @param string $file The path to the file that shall be read
 * @param string $defaultfile The path to the file to is read when $file doesn't exist
 * @return string The file contents
 */
function read_file( $file, $defaultfile='' ) {

	// open the HTML file and read it into $html
	if (file_exists( $file )) {
		$html_file = fopen( $file, "r" );
	}
	elseif( !empty( $defaultfile ) && file_exists( $defaultfile ) ) {
		$html_file = fopen( $defaultfile, "r" );
	}
	else {
		return;
	}
	$html = "";

	while (!feof($html_file)) {
		$buffer = fgets($html_file, 1024);
		$html .= $buffer;
	}
	fclose ($html_file);

	return( $html );
}

/**
 * Includes all needed classes for a core module and create + populate the objects
 *
 * @param string $module The name of the virtuemart core module
 */
function include_class($module) {

	// globalize the vars so that they can be used outside of this function
	global $ps_vendor, $ps_affiliate, $ps_manufacturer, $ps_manufacturer_category,
	$ps_user,
	$ps_vendor_category,
	$ps_checkout,
	$ps_intershipper,
	$psShip,
	$ps_shipping,
	$ps_order,
	$ps_order_status,
	$ps_product,
	$ps_product_category ,
	$ps_product_attribute,
	$ps_product_type, // Changed Product Type
	$ps_product_type_parameter, // Changed Product Type
	$ps_product_product_type, // Changed Product Type
	$ps_product_price,
	$nh_report,
	$ps_payment_method,
	$ps_shopper,
	$ps_shopper_group,
	$ps_cart,
	$ps_zone,
	$ps_tax,
	$zw_waiting_list;

	switch ( $module ) {

		case "account":
			break;

		case "admin" :
			// Load class files
			require_once(CLASSPATH. 'ps_html.php' );
			require_once(CLASSPATH. 'ps_function.php' );
			require_once(CLASSPATH. 'ps_module.php' );
			require_once(CLASSPATH. 'ps_perm.php' );
			require_once(CLASSPATH. 'ps_user.php' );
			require_once(CLASSPATH. 'ps_user_address.php' );
			require_once(CLASSPATH. 'ps_session.php' );
	
			//Instantiate Classes
			$ps_html = new ps_html;
			$ps_function = new ps_function;
			$ps_module= new ps_module;
			$ps_perm= new ps_perm;
			$ps_user= new ps_user;
			$ps_user_address = new ps_user_address;
			$ps_session = new ps_session;
	
			break;

		case "affiliate" :
			// Load class file
			require_once(CLASSPATH. 'ps_affiliate.php' );
			//Instantiate Class
			$ps_affiliate = new ps_affiliate;
			break;

		case "checkout" :
			// Load class file
			require_once(CLASSPATH. 'ps_checkout.php' );
	
			//Instantiate Class
			$ps_checkout = new ps_checkout;
	
			break;

		case "order" :
			// Load classes
			require_once(CLASSPATH.'ps_order.php' );
			require_once(CLASSPATH.'ps_order_status.php' );
	
			// Instantiate Classes
			$ps_order = new ps_order;
			$ps_order_status = new ps_order_status;
			break;

		case "product" :
			// Load Classes
			require_once(CLASSPATH.'ps_product.php' );
			require_once(CLASSPATH.'ps_product_category.php' );
			require_once(CLASSPATH.'ps_product_attribute.php' );
			require_once(CLASSPATH.'ps_product_type.php' ); // Changed Product Type
			require_once(CLASSPATH.'ps_product_type_parameter.php' ); // Changed Product Type
			require_once(CLASSPATH.'ps_product_product_type.php' ); // Changed Product Type
			require_once(CLASSPATH.'ps_product_price.php' );
	
			// Instantiate Classes
			$ps_product = new ps_product;
			$ps_product_category = new ps_product_category;
			$ps_product_attribute = new ps_product_attribute;
			$ps_product_type = new ps_product_type; // Changed Product Type
			$ps_product_type_parameter = new ps_product_type_parameter; // Changed Product Type
			$ps_product_product_type = new ps_product_product_type; // Changed Product Type
			$ps_product_price = new ps_product_price;
			break;

		case "reportbasic" :
			// Load Classes
			require_once( CLASSPATH . 'ps_reportbasic.php');
			$nh_report = new nh_report;
			break;

		case "shipping" :
			// Load Class
			require_once( CLASSPATH . 'ps_shipping.php');
			// Instantiate Class
			$ps_shipping = new ps_shipping;
			break;

		case "shop" :
			// Load Classes
			require_once( CLASSPATH. 'ps_cart.php' );
			require_once( CLASSPATH. 'zw_waiting_list.php');

			// Instantiate Classes
			$ps_cart = new ps_cart;
			$zw_waiting_list = new zw_waiting_list;
			break;

		case "shopper" :
			// Load Classes
			require_once( CLASSPATH . 'ps_shopper.php' );
			require_once( CLASSPATH . 'ps_shopper_group.php' );
			// Instantiate Classes
			$ps_shopper = new ps_shopper;
			$ps_shopper_group = new ps_shopper_group;
			break;

		case "store" :
			// Load Classes
			require_once( CLASSPATH . 'ps_payment_method.php' );
			// Instantiate Classes
			$ps_payment_method = new ps_payment_method;
			break;

		case "tax" :
			// Load Classes
			require_once ( CLASSPATH . 'ps_tax.php' );
			// Instantiate Classes
			$ps_tax = new ps_tax;
			break;

		case "vendor" :
			// Load Classes
			require_once (CLASSPATH . 'ps_vendor.php' );
			require_once (CLASSPATH . 'ps_vendor_category.php' );
			// Instantiate Classes
			$ps_vendor = new ps_vendor;
			$ps_vendor_category = new ps_vendor_category;
			break;

		case "zone" :
			// Load Class
			require_once (CLASSPATH . 'ps_zone.php');
			// Instantiate Class
			$ps_zone = new ps_zone;
			break;

		case "manufacturer" :

			require_once (CLASSPATH . 'ps_manufacturer.php');
			require_once (CLASSPATH . 'ps_manufacturer_category.php');
			$ps_manufacturer = new ps_manufacturer;
			$ps_manufacturer_category = new ps_manufacturer_category;
			break;
	}
}

/**
* @param string The vendor_currency_display_code
*   FORMAT: 
    1: id, 
    2: CurrencySymbol, 
    3: NumberOfDecimalsAfterDecimalSymbol,
    4: DecimalSymbol,
    5: Thousands separator
    6: Currency symbol position with Positive values :
									// 0 = '00Symb'
									// 1 = '00 Symb'
									// 2 = 'Symb00'
									// 3 = 'Symb 00'
    7: Currency symbol position with Negative values :
									// 0 = '(Symb00)'
									// 1 = '-Symb00'
									// 2 = 'Symb-00'
									// 3 = 'Symb00-'
									// 4 = '(00Symb)'
									// 5 = '-00Symb'
									// 6 = '00-Symb'
									// 7 = '00Symb-'
									// 8 = '-00 Symb'
									// 9 = '-Symb 00'
									// 10 = '00 Symb-'
									// 11 = 'Symb 00-'
									// 12 = 'Symb -00'
									// 13 = '00- Symb'
									// 14 = '(Symb 00)'
									// 15 = '(00 Symb)'
    EXAMPLE: ||&euro;|2|,||1|8
* @return string
*/
function vendor_currency_display_style( $style ) {

	$array = explode( "|", $style );
	$display = Array();
	$display["id"] = @$array[0];
	$display["symbol"] = @$array[1];
	$display["nbdecimal"] = @$array[2];
	$display["sdecimal"] = @$array[3];
	$display["thousands"] = @$array[4];
	$display["positive"] = @$array[5];
	$display["negative"] = @$array[6];
	return $display;
}


/**
* Login validation function
*
* Username and encoded password is compared to db entries in the mos_users
* table. A successful validation returns true, otherwise false
*/
function mShop_checkpass() {
	global $database, $perm, $my;

	// only allow access to admins or storeadmins
	if( $perm->check("admin,storeadmin")) {

		$username = $my->username;
		$passwd = trim( mosGetParam( $_POST, 'passwd', '' ) );
		$passwd = md5( $passwd );
		$bypost = 1;
		if (!$username || !$passwd || $_REQUEST['option'] != "com_virtuemart") {
			return false;
		}
		else {
			$database->setQuery( "SELECT id, gid, block, usertype"
			. "\nFROM #__users"
			. "\nWHERE username='$username' AND password='$passwd'"
			);
			$row = null;
			if ($database->loadObject( $row )) {
				return true;
			}
			else {
				return false;
			}
		}
	}
	else
	return false;
}
/**
 * Formerly used to print a search header for lists
 * use class listFactory instead
 * @deprecated 
 *
 */
function search_header() {
	echo "### THIS FUNCTION IS DEPRECATED. Use the class listFactory instead. ###";
}
/**
 * Formerly used to print a search header for lists
 * use class listFactory instead
 * @deprecated 
 *
 */
function search_footer() {
	echo "### THIS FUNCTION IS DEPRECATED. Use the class listFactory instead. ###";
}
/**
 * Used by the frontend adminsitration to save editor field contents
 *
 * @param string $editor1 the name of the editor field no. 1
 * @param string $editor2 the name of the editor field no. 2
 */
function editorScript($editor1='', $editor2='') {
	?>
	 <script type="text/javascript">
	 function submitbutton(pressbutton) {
	 	var form = document.adminForm;
	 	if (pressbutton == 'cancel') {
	 		submitform( pressbutton );
	 		return;
	 	}
	 	<?php
	 	if ($editor1 != '')
	 	getEditorContents( 'editor1', $editor1 ) ; ?>
	 	<?php
	 	if ($editor2 != '')
	 	getEditorContents( 'editor2', $editor2 ) ; ?>
	 	submitform( pressbutton );

	 }
	 </script><?php
}

/**
* Function to create an email object for further use (uses phpMailer)
* @param string From e-mail address
* @param string From name
* @param string E-mail subject
* @param string Message body
* @return vmPHPMailer Mail object
*/
function vmCreateMail( $from='', $fromname='', $subject='', $body='' ) {
	global $mosConfig_absolute_path, $mosConfig_sendmail;
	global $mosConfig_smtpauth, $mosConfig_smtpuser;
	global $mosConfig_smtppass, $mosConfig_smtphost;
	global $mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_mailer;
	
	require_once( CLASSPATH . 'phpmailer/class.phpmailer.php');
	
	$mail = new vmPHPMailer();

	$mail->PluginDir = CLASSPATH .'phpmailer/';
	$mail->SetLanguage( 'en', CLASSPATH . 'phpmailer/language/' );
	$mail->CharSet 	= substr_replace(_ISO, '', 0, 8);
	$mail->IsMail();
	$mail->From 	= $from ? $from : $mosConfig_mailfrom;
	$mail->FromName = $fromname ? $fromname : $mosConfig_fromname;
	$mail->Sender 	= $from ? $from : $mosConfig_mailfrom;
	$mail->Mailer 	= $mosConfig_mailer;

	// Add smtp values if needed
	if ( $mosConfig_mailer == 'smtp' ) {
		$mail->SMTPAuth = $mosConfig_smtpauth;
		$mail->Username = $mosConfig_smtpuser;
		$mail->Password = $mosConfig_smtppass;
		$mail->Host 	= $mosConfig_smtphost;
	} else

	// Set sendmail path
	if ( $mosConfig_mailer == 'sendmail' ) {
		if (isset($mosConfig_sendmail))
			$mail->Sendmail = $mosConfig_sendmail;
	} // if
	if( $subject ) {
		$mail->Subject 	= vmAbstractLanguage::safe_utf8_encode( $subject, $mail->CharSet );
	}
	if( $body) {
		$mail->Body 	= $body;
	}
	// Patch to get correct Line Endings
	switch( substr( strtoupper( PHP_OS ), 0, 3 ) ) {
		case "WIN":
			$mail->LE = "\r\n";
			break;
		case "MAC": // fallthrough
		case "DAR": // Does PHP_OS return 'Macintosh' or 'Darwin' ?
			$mail->LE = "\r";
		default: // change nothing
			break;
	}
	return $mail;
}

/**
* Mail function (uses phpMailer)
* @param string From e-mail address
* @param string From name
* @param string/array Recipient e-mail address(es)
* @param string E-mail subject
* @param string Message body
* @param boolean false = plain text, true = HTML
* @param string/array CC e-mail address(es)
* @param string/array BCC e-mail address(es)
* @param array Images path,cid,name,filename,encoding,mimetype
* @param string/array Attachment file name(s)
* @return boolean Mail send success
*/
function vmMail($from, $fromname, $recipient, $subject, $body, $Altbody, $mode=false, $cc=NULL, $bcc=NULL, $images=null, $attachment=null ) {
	global $mosConfig_debug;
	$mail = vmCreateMail( $from, $fromname, $subject, $body );
	
	if( $Altbody != "" ) {
		// In this section we take care for utf-8 encoded mails
		$mail->AltBody = vmAbstractLanguage::safe_utf8_encode( $Altbody, $mail->CharSet );
	}
	
	// activate HTML formatted emails
	if ( $mode ) {
		$mail->IsHTML(true);
	}
	if( $mail->ContentType == "text/plain" ) {
		$mail->Body = vmAbstractLanguage::safe_utf8_encode( $mail->Body, $mail->CharSet );
	}
	if( is_array($recipient) ) {
		foreach ($recipient as $to) {
			$mail->AddAddress($to);
		}
	} else {
		$mail->AddAddress($recipient);
	}
	if (isset($cc)) {
		if( is_array($cc) )
			foreach ($cc as $to) $mail->AddCC($to);
		else
			$mail->AddCC($cc);
	}
	if (isset($bcc)) {
		if( is_array($bcc) )
			foreach ($bcc as $to) $mail->AddBCC($to);
		else
			$mail->AddBCC($bcc);
	}
	if( $images ) {
		foreach( $images as $image) {
			$mail->AddEmbeddedImage( $image['path'], $image['name'], $image['filename'], $image['encoding'], $image['mimetype']);
		}
	}
	if ($attachment) {
		if ( is_array($attachment) )
			foreach ($attachment as $fname) $mail->AddAttachment($fname);
		else
			$mail->AddAttachment($attachment);
	}
	$mailssend = $mail->Send();

	if( $mosConfig_debug ) {
		//$mosDebug->message( "Mails send: $mailssend");
	}
	if( $mail->error_count > 0 ) {
		//$mosDebug->message( "The mail message $fromname <$from> about $subject to $recipient <b>failed</b><br /><pre>$body</pre>", false );
		//$mosDebug->message( "Mailer Error: " . $mail->ErrorInfo . "" );
	}
	return $mailssend;
} 

// $ Id: html_entity_decode.php,v 1.7 2005/01/26 04:55:13 aidan Exp $
if (!defined('ENT_NOQUOTES')) {
    define('ENT_NOQUOTES', 0);
}
if (!defined('ENT_COMPAT')) {
    define('ENT_COMPAT', 2);
}
if (!defined('ENT_QUOTES')) {
    define('ENT_QUOTES', 3);
}

/**
 * Replace html_entity_decode()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.html_entity_decode
 * @author      David Irvine <dave@codexweb.co.za>
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.11.2.4 $
 * @since       PHP 4.3.0
 * @internal    Setting the charset will not do anything
 * @require     PHP 4.0.0 (user_error)
 */
function vmHtmlEntityDecode($string, $quote_style = ENT_COMPAT, $charset = null) {
	
	if( is_null( $charset )) {
		$charset = vmGetCharset();
	}
	if( function_exists( 'html_entity_decode' )) {
		return @html_entity_decode( $string, $quote_style, $charset );
	}
	
    if (!is_int($quote_style) && !is_null($quote_style)) {
        user_error(__FUNCTION__.'() expects parameter 2 to be long, ' .
            gettype($quote_style) . ' given', E_USER_WARNING);
        return;
    }
    $trans_tbl = get_html_translation_table(HTML_ENTITIES);
    $trans_tbl = array_flip($trans_tbl);

    // Add single quote to translation table;
    $trans_tbl['&#039;'] = '\'';

    // Not translating double quotes
    if ($quote_style & ENT_NOQUOTES) {
        // Remove double quote from translation table
        unset($trans_tbl['&quot;']);
    }

    return strtr($string, $trans_tbl);
}

/**
 * Reads a file and sends them in chunks to the browser
 * This should overcome memory problems
 * http://www.php.net/manual/en/function.readfile.php#54295
 *
 * @param string $filename
 * @param boolean $retbytes
 * @return mixed
 */
function vmReadFileChunked($filename,$retbytes=true) {
	$chunksize = 1*(1024*1024); // how many bytes per chunk
	$buffer = '';
	$cnt =0;
	// $handle = fopen($filename, 'rb');
	$handle = fopen($filename, 'rb');
	if ($handle === false) {
		return false;
	}
	while (!feof($handle)) {
		$buffer = fread($handle, $chunksize);
		echo $buffer;
		flush();
		if ($retbytes) {
			$cnt += strlen($buffer);
		}
	}
	$status = fclose($handle);
	if ($retbytes && $status) {
		return $cnt; // return num. bytes delivered like readfile() does.
	}
	return $status;
}

/**
 * Returns the charset string from the global _ISO constant
 *
 * @return string UTF-8 by default
 */
function vmGetCharset() {
	$iso = explode( '=', _ISO );
	if( !empty( $iso[1] )) {
		return $iso[1];
	}
	else {
		return 'UTF-8';
	}
}

/**
 * Equivalent to Joomla's josSpoofCheck function
 * @author Joomla core team
 *
 * @param boolean $header
 * @param unknown_type $alt
 */
function vmSpoofCheck( $header=NULL, $alt=NULL ) {	
	$validate 	= mosGetParam( $_POST, vmSpoofValue($alt), 0 );
	
	// probably a spoofing attack
	if (!$validate) {
		header( 'HTTP/1.0 403 Forbidden' );
		mosErrorAlert( _NOT_AUTH );
		return;
	}
	
	// First, make sure the form was posted from a browser.
	// For basic web-forms, we don't care about anything
	// other than requests from a browser:   
	if (!isset( $_SERVER['HTTP_USER_AGENT'] )) {
		header( 'HTTP/1.0 403 Forbidden' );
		mosErrorAlert( _NOT_AUTH );
		return;
	}
	
	// Make sure the form was indeed POST'ed:
	//  (requires your html form to use: action="post")
	if (!$_SERVER['REQUEST_METHOD'] == 'POST' ) {
		header( 'HTTP/1.0 403 Forbidden' );
		mosErrorAlert( _NOT_AUTH );
		return;
	}
	
	if ($header) {
	// Attempt to defend against header injections:
		$badStrings = array(
			'Content-Type:',
			'MIME-Version:',
			'Content-Transfer-Encoding:',
			'bcc:',
			'cc:'
		);
		
		// Loop through each POST'ed value and test if it contains
		// one of the $badStrings:
		foreach ($_POST as $k => $v){
			foreach ($badStrings as $v2) {
				if (strpos( $v, $v2 ) !== false) {
					header( "HTTP/1.0 403 Forbidden" );
					mosErrorAlert( _NOT_AUTH );
					return;
				}
			}
		}   
		
		// Made it past spammer test, free up some memory
		// and continue rest of script:   
		unset($k, $v, $v2, $badStrings);
	}
}
/**
 * Equivalent to Joomla's josSpoofValue function
 *
 * @param boolean $alt
 * @return string Validation Hash
 */
function vmSpoofValue($alt=NULL) {
	global $mainframe;
	
	if ($alt) {
		$random		= date( 'Ymd' );
	} else {		
		$random		= date( 'dmY' );
	}
	$validate 	= mosHash( $mainframe->getCfg( 'db' ) . $random );
	
	return $validate;
}
?>