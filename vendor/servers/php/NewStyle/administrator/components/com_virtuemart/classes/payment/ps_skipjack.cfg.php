<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_skipjack.cfg.php,v 1.3 2005/09/29 20:02:18 soeren_nb Exp $
* @package VirtueMart
* @subpackage payment
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
*
* The ps_skipjack class, containing the payment processing code
*  for transactions with Skipjack.com
 * @copyright (C) 2005 Matthew Schick
*/

define ('SKJ_TEST_REQUEST', 'FALSE');
define ('SKJ_SERIAL', '0000000000');
define ('SKJ_CHECK_CARD_CODE', 'NO');
define ('SKJ_VERIFIED_STATUS', 'P');
define ('SKJ_INVALID_STATUS', 'P');
define ('SKJ_RECURRING', 'NO');
?>
