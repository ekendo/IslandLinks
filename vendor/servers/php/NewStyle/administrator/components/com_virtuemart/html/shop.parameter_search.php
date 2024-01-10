<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
* Advanced Attributes search for phpShop
* @author Zdenek Dvorak (zdenek.dvorak@seznam.cz)
* @version $Id: shop.parameter_search.php,v 1.3 2005/09/29 20:02:18 soeren_nb Exp $
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

?>
<h2><?php echo $VM_LANG->_PHPSHOP_PARAMETER_SEARCH ?></h2>

<table width="100%" border="0" cellpadding="2" cellspacing="0">
<tr>
	<td><?php echo $VM_LANG->_PHPSHOP_PARAMETER_SEARCH_TEXT1 ?></td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>
		<table width="100%" border="0" cellpadding="2" cellspacing="0">
<?php
	$q  = "SELECT * FROM #__{vm}_product_type ";
	$q .= "WHERE product_type_publish='Y' ";
	$q .= "ORDER BY product_type_list_order";
	$db->query($q);
	while ($db->next_record()) {
		echo "<tr><td>";
		echo "<a href=\"".URL."index.php?option=com_virtuemart&page=shop.parameter_search_form&product_type_id=".$db->f("product_type_id")."&Itemid=".$_REQUEST['Itemid']."\">";
		echo $db->f("product_type_name");
		echo "</a></td>\n<td>";
		echo $db->f("product_type_description");
		echo "</td></tr>";
	}
	echo "</table>\n";
	
	if ($db->num_rows() == 0) {
		echo $VM_LANG->_PHPSHOP_PARAMETER_SEARCH_NO_PRODUCT_TYPE;
	}
?>
	</td>
</tr>
</table>
<!-- /** Changed Product Type - End */ -->

