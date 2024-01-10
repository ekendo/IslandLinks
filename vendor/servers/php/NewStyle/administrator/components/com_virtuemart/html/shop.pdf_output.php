<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: shop.pdf_output.php,v 1.3.2.2 2006/05/06 10:05:27 soeren_nb Exp $
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

$showpage = mosGetParam( $_REQUEST, 'showpage');
$flypage = mosGetParam( $_REQUEST, 'flypage');
$product_id = mosGetParam( $_REQUEST, 'product_id');
$category_id = mosGetParam( $_REQUEST, 'category_id');

/* Who cares for Safe Mode ? Not me! */
if (@file_exists( "/usr/bin/htmldoc" )) {
	
	$load_page = $mosConfig_live_site . "/index2.php?option=com_virtuemart&page=$showpage&flypage=$flypage&product_id=$product_id&category_id=$category_id&pop=1&hide_js=1&output=pdf";
	header( "Content-Type: application/pdf" );
	header( "Content-Disposition: inline; filename=\"pdf-mambo.pdf\"" );
	flush();
	//following line for Linux only - windows may need the path as well...
	passthru( "/usr/bin/htmldoc --no-localfiles --quiet -t pdf14 --jpeg --webpage --header t.D --footer ./. --size letter --left 0.5in '$load_page'" );
	exit;
} 
else {
	freePDF( $showpage, $flypage, $product_id, $category_id );
}
function repairImageLinks( $html ) {
	
	if( PSHOP_IMG_RESIZE_ENABLE == '1' ) {
		$images = array();
		if (preg_match_all("/<img[^>]*>/", $html, $images) > 0) {
		  $i = 0;
		  foreach ($images as $image) {
			if ( is_array( $image ) ) {
			  foreach( $image as $src) {
				  preg_match("'src=\"[^\"]*\"'si", $src, $matches);
				  $source = str_replace ("src=\"", "", $matches[0]);
				  $source = str_replace ("\"", "", $source);
				  $fileNamePos = strpos($source, "filename=");
				  if ( $fileNamePos > 0 ) {
					$firstAmpersand = strpos( $source, "&" );
					$fileName = substr( $source, $fileNamePos+9, $firstAmpersand - $fileNamePos-9 );
					$extension = strrchr( $fileName, "." );
					$fileNameNoExt = str_replace( $extension, "", $fileName );
					$newSource = IMAGEURL . "product/resized/".$fileNameNoExt."_".PSHOP_IMG_WIDTH."x".PSHOP_IMG_HEIGHT.$extension;
				  }
				  else
					$newSource= $source;
					
				  $html = str_replace( $source, $newSource, $html );
			  }
			}
		  }
		}
	}
	return $html;

}
function freePDF( $showpage, $flypage, $product_id, $category_id ) {
	global $db, $sess, $auth, $my, $perm, $VM_LANG, $mosConfig_live_site, $mosConfig_sitename, $mosConfig_offset, $mosConfig_hideCreateDate, $mosConfig_hideAuthor, 
	$mosConfig_hideModifyDate,$mm_action_url, $database, $mainframe, $mosConfig_absolute_path, $vendor_full_image, $vendor_name, $limitstart, $limit;
	
	while( @ob_end_clean() );
	error_reporting( 0 );
	ini_set( "allow_url_fopen", "1" );
	define('FPDF_FONTPATH', CLASSPATH.'pdf/font/');
	define( 'RELATIVE_PATH', CLASSPATH.'pdf/' );
	require( CLASSPATH.'pdf/html2fpdf.php');
	require( CLASSPATH.'pdf/html2fpdf_site.php');
	
	$pdf = new PDF();
	
	$pdf->AddPage();
	$pdf->SetFont('Arial','',11);
	$logo = IMAGEPATH . "vendor/$vendor_full_image";
	$pdf->InitLogo($logo);
	$pdf->PutTitle($mosConfig_sitename);
	$pdf->PutAuthor( $vendor_name );
	
	switch( $showpage ) {  
	  
	case "shop.product_details":
	  
	  $_REQUEST['flypage'] = "shop.flypage_lite_pdf";
	  $_REQUEST['product_id'] = $product_id;
	  ob_start();
	  include( PAGEPATH . $showpage . '.php' );
	  $html = ob_get_contents();
	  ob_end_clean();
	  $html = repairImageLinks( $html );
	  $pdf->WriteHTML($html);
	  break;
	
	case "shop.browse":
	  $_REQUEST['category_id'] = $category_id;
	  ob_start();
	  include( PAGEPATH . 'shop.browse.php' );
	  $html = ob_get_contents();
	  ob_end_clean();
	  $html = repairImageLinks( $html );
	  //echo "HTML: ".$html; exit;
	  $pdf->WriteHTML($html);
	  break;
	}
	
	//Output the file
	$pdf->Output();		
}
