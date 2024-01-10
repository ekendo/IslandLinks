<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: help.about.php,v 1.4 2005/10/18 04:35:41 soeren_nb Exp $
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
include_once( ADMINPATH . "version.php" );
?>
<br /><br />
<table align="center" style="width: 80%;" class="admin">
 <tbody>
  <tr>
   <td>
    <pre><strong>Your <?php echo $VMVERSION->PRODUCT ?> version:</strong> <?php echo $myVersion ?></pre>
    <img hspace="5" align="left" src="<?php echo $mosConfig_live_site ?>/administrator/components/com_virtuemart/cart.gif" alt="cart.gif" />
    <span style="font-weight: bold;">
     VirtueMart</span> is the complete Open Source E-Commerce solution for Mambo and Joomla. It is an Application, which comes with a Component, 6 Modules and 2 Mambots.
     It has its roots in a Shopping Cart Script called &quot;phpShop&quot; (Authors: Edikon Corp. &amp; the <a href="http://www.virtuemart.org/" target="_blank">phpShop</a> community).
     <br /><br />VirtueMart is licensed under the <a href="http://www.gnu.org/copyleft/gpl.html" target="_blank">GNU / GPL License</a>.
     <br /><br /><br />There's a small bunch of developers who help in pushing this
          Shopping Script towards professionality and usability.
      <br /><br />
      <span style="font-weight: bold;">Project Leader:</span> Soeren Eberhardt<br />
      <span style="font-weight: bold;">Homepage:</span> <a href="http://virtuemart.net" target="_blank" title="virtuemart.net">http://virtuemart.net</a><br />
      <span style="font-weight: bold;">Forum:</span> <a href="http://virtuemart.net/index.php?option=com_smf&Itemid=71" target="_blank" title="VirtueMart Forum">VirtueMart Forum</a><br />
      
      <br />
	<?php
	include( ADMINPATH."COPYRIGHT.php" );
	?>
	<br /><br />
      <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank"><span style="font-weight: bold;">
      Please consider a small donation to help me keep up the  work on this component.</span><br style="font-weight: bold;" /><br />
      <input type="hidden" name="cmd" value="_xclick" />
      <input type="hidden" name="business" value="soeren_nb@yahoo.de" />
      <input type="hidden" name="item_name" value="VirtueMart Donation" />
      <input type="hidden" name="item_number" />
      <input type="hidden" name="currency_code" value="EUR" />
      <input type="hidden" name="tax" value="0" />
      <input type="hidden" name="no_note" value="0" />
      <input type="hidden" name="amount" />
      <input type="image" border="0" src="<?php echo $mosConfig_live_site ?>/administrator/components/com_virtuemart/x-click-but21.gif" name="submit" alt="Make payments with PayPal - it's fast, free and secure!" />
    </form>
   </td>
  </tr>
 </tbody>
</table>
