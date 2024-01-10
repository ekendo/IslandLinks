<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

session_save_path("/home/users/web/b453/hy.ekendodreamof/cgi-bin/tmp");
session_start();

/**
* VirtueMart MiniCart Module
*
* @version $Id: mod_virtuemart_cart.php,v 1.1 2005/09/29 20:02:56 soeren_nb Exp $
* @package VirtueMart
* @subpackage modules
*
* @copyright (C) 2004 Soeren Eberhardt
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* VirtueMart is Free Software.
* VirtueMart comes with absolute no warranty.
*
* www.virtuemart.net
*/

/* Load the virtuemart main parse code */
require_once( $mosConfig_absolute_path.'/components/com_virtuemart/virtuemart_parser.php' );

global $VM_LANG, $sess, $mm_action_url;

?><table width="100%">
        <tr>
            <td>
                <a class="mainlevel" href="<?php echo $sess->url($mm_action_url."index.php?page=shop.cart")?>">
                <?php echo $VM_LANG->_PHPSHOP_CART_SHOW ?></a>
            </td>
        </tr>
        <tr>
            <td><?php include (PAGEPATH.'shop.basket_short.php') ?></td>
        </tr>
    </table>
