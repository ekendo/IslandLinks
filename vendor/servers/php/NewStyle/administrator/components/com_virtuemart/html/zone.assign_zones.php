<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: zone.assign_zones.php,v 1.5 2005/09/29 20:02:18 soeren_nb Exp $
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

require_once( CLASSPATH . "pageNavigation.class.php" );
require_once( CLASSPATH . "htmlTools.class.php" );

if (!empty($keyword)) {
  $list  = "SELECT * FROM #__{vm}_country WHERE ";
  $count = "SELECT count(*) as num_rows FROM #__{vm}_country WHERE ";
  $q  = "(country_name LIKE '%$keyword%')";
  $q .= "ORDER BY country_name ASC ";
  $list .= $q . " LIMIT $limitstart, " . $limit;
  $count .= $q;   
}
else  {
  $q = "";
  $list  = "SELECT * FROM #__{vm}_country ORDER BY country_id ASC ";
  $count = "SELECT count(*) as num_rows FROM #__{vm}_country"; 
  $list .= $q . " LIMIT $limitstart, " . $limit;
  $count .= $q;   
}
$db->query($count);
$db->next_record();
$num_rows = $db->f("num_rows");

// Create the Page Navigation
$pageNav = new vmPageNav( $num_rows, $limitstart, $limit );

// Create the List Object with page navigation
$listObj = new listFactory( $pageNav );

// print out the search field and a list heading
$listObj->writeSearchHeader($VM_LANG->_PHPSHOP_ASSIGN_ZONE_PG_LBL, '', $modulename, "assign_zones");

// start the list table
$listObj->startTable();

// these are the columns in the table
$columns = Array(  "#" => "width=\"20\"", 
					$VM_LANG->_PHPSHOP_ZONE_ASSIGN_COUNTRY_LBL => '',
					$VM_LANG->_PHPSHOP_ZONE_ASSIGN_CURRENT_LBL => '',
					$VM_LANG->_PHPSHOP_ZONE_ASSIGN_ASSIGN_LBL => ''
				);
$listObj->writeTableHeader( $columns );

$db->query($list);
$i = 0;
while ($db->next_record()) {
	$listObj->newRow();
	
	// The row number
	$listObj->addCell( $pageNav->rowNumber( $i ) );
    
	$tmp_cell = '<input type="hidden" name="country_id[]" value="'. $db->f("country_id") .'" />';
	$tmp_cell .= $db->f("country_name");
	$listObj->addCell( $tmp_cell );
        
	// Get the zone rate for each country
	$per_item = $ps_zone->per_item($db->f("zone_id"));
	$zone_limit = $ps_zone->zone_limit($db->f("zone_id"));
	
	$tmp_cell = 'Per Item: <strong>'. $CURRENCY_DISPLAY->getFullValue($per_item).'</strong><br/>'
			. 'Limit: <strong>'. $CURRENCY_DISPLAY->getFullValue($zone_limit).'</strong>';
    $listObj->addCell( $tmp_cell );
    
	$tmp_cell = $ps_zone->list_zones("zone_id[]", $db->f("zone_id"));
	if($db->f("zone_id") > "1") {
		$url = $_SERVER['PHP_SELF']."?page=zone.zone_form&zone_id=" . $db->f("zone_id");
		$tmp_cell .= "<a href=\"" . $sess->url($url) . "\">Edit This Zone</a>"; 
	}
	$listObj->addCell( $tmp_cell );
        
	$i++;

}

$listObj->writeTable();

$listObj->endTable();

$listObj->writeFooter( $keyword );

?>
<script type="text/javascript">document.adminForm.boxchecked.value = 1;</script>
