<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.'); 
/**
*
* @version $Id: canadapost.cfg.php,v 1.3 2005/09/29 20:02:18 soeren_nb Exp $
* @package VirtueMart
* @subpackage shipping
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
define ('MERCHANT_CPCID', 'CPC_DEMO_XML');
define ('CP_SERVER', '206.191.4.228');
define ('CP_PORT', '30000');
define ('CP_FEDERAL_TAX', '0');
define ('CP_PROVINCIAL_TAX', '0');
define ('CP_ARRIVAL_DATE_EXPLAIN', 'La date de livraison est calcul�e en ajoutant les normes de livraison de Postes Canada au d�lai d�ex�cution des commandes.');
define ('CP_HANDLING_CHARGE_EXPLAIN', 'Les frais d�exp�dition sont calcul�s en ajoutant les services de Postes Canada aux co�ts de manutention. Taxes incluses.');
?>