<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: shop.error.php,v 1.3.2.1 2006/03/10 15:55:15 soeren_nb Exp $
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
?>
<table width="100%" border="0" cellspacing="0" cellpadding="1" bgcolor="#000000">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="0" cellpadding="2" bgcolor="khaki">
        <tr align="center"> 
          <td> 
            <h4><?php echo $VM_LANG->_PHPSHOP_ERROR ?></h4>
            <span class="message"><?php echo $error_type;?></span>
            <center>
              <span class="message"><?php echo $error?></span>
              </center>
</td>
        </tr>
      </table>
    </td>
  </tr>
</table>
