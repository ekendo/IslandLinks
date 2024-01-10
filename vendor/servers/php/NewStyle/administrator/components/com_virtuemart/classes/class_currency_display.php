<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: class_currency_display.php,v 1.4.2.1 2005/12/01 20:00:32 soeren_nb Exp $
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
if(!defined("_CLASS_CURRENCY_DISPLAY_LOADED")) {
	define("_CLASS_CURRENCY_DISPLAY_LOADED", true);

	// ============================================================
	// ================ CURRENCY DISPLAY ==========================
	// ============================================================
	// == version : 1.1	    (class_currency_display.php)
	// ============================================================
	// ==== Description
	// == Currency display class : format money values for display
	// ==== Relationships :
	// == None, but may be ideally used with CurrencyConvert class
	// ============================================================
	// ==== History :
	// == 16/11/2000 : S. Mouton	First Version
	// == 29/11/2000 : S. Mouton	Added euro conversion euro
	// == 27/02/2001 : S. Mouton	Full re organisation : separate between DB and non DB version
	// == 14/03/2001 : S. Mouton    Minor bug in negative displays corrected
	// ============================================================

	class CurrencyDisplay {
		var $id      		= "euro";		// string ID related with the currency (ex : language)
		var $symbol    		= "&euro;";	// Printable symbol
		var $nbDecimal 		= 2;	// Number of decimals past colon (or other)
		var $decimal   		= ",";	// Decimal symbol ('.', ',', ...)
		var $thousands 		= " "; 	// Thousands separator ('', ' ', ',')
		var $positivePos	= 1;	// Currency symbol position with Positive values :
									// 0 = '00Symb'
									// 1 = '00 Symb'
									// 2 = 'Symb00'
									// 3 = 'Symb 00'
		var $negativePos	= 8;	// Currency symbol position with Negative values :
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
	// ================		 
	function CurrencyDisplay(	$id			="euro",
								$symbol		="&euro;",
								$nbDecimal	= 2,
								$decimal   	= ",",
								$thousands 	= " ",
								$positivePos= 1,
								$negativePos= 8){
		$this->id		 = $id;
		$this->symbol    = $symbol;
		$this->nbDecimal = $nbDecimal;
		$this->decimal   = $decimal;
		$this->thousands = $thousands;
		$this->positivePos = $positivePos;
		$this->negativePos = $negativePos;
	}

	// ================
	function getSymbol(){
		return($this->symbol);
	}

	// ================
	function getId(){
		return($this->id);
	}
	// ================
	function getValue($nb, $decimals=''){
		$res = "";		
		// Warning ! number_format function performs implicit rounding
		// Rounding is not handled in this DISPLAY class
		// that's why you have to use the right decimal value.
		// Workaround :number_format accepts either 1, 2 or 4 parameters.
		// this cause problem when no thousands separator is given : in this
		// case, an unwanted ',' is displayed.
		// That's why we have to do the work ourserlve.
		// Note : when no decimal il given (i.e. 3 parameters), everything works fine
		if( $decimals === '') {
			$decimals = $this->nbDecimal;
		}
		if ($this->thousands != ''){
			$res=number_format($nb,$decimals,$this->decimal,$this->thousands);
		} else {
			// If decimal is equal to defaut thousand separator, apply a trick
			if ($this->decimal==','){
				$res=number_format($nb,$decimals,$this->decimal,'|');
				$res=str_replace('|','',$res);			
			} else {
				// Else a simple substitution is enough
				$res=number_format($nb,$decimals,$this->decimal,$this->thousands);
				$res=str_replace(',','',$res);
			}
		}
		return($res);
	}

	// ================
	function getFullValue($nb, $decimals=''){
		$res = "";
		// Currency symbol position
		if ($nb == abs($nb)){
			$res=$this->getValue($nb, $decimals);
			// Positive number
			switch ($this->positivePos){
			case 0:
				// 0 = '00Symb'
				$res=$res.$this->symbol;
			break;
			case 2:
				// 2 = 'Symb00'
				$res=$this->symbol.$res;
			break;
			case 3:
				// 3 = 'Symb 00'
				$res=$this->symbol.' '.$res;
			break;
			case 1:
			default :
				// 1 = '00 Symb'
				$res=$res.' '.$this->symbol;
			break;
			}
		} else {
			// Negative number
			$res=$this->getValue(abs($nb), $decimals);
			switch ($this->negativePos){
			case 0:
				// 0 = '(Symb00)'
				$res='('.$this->symbol.$res.')';
			break;
			case 1:
				// 1 = '-Symb00'
				$res='-'.$this->symbol.$res;
			break;
			case 2:
				// 2 = 'Symb-00'
				$res=$this->symbol.'-'.$res;
			break;
			case 3:
				// 3 = 'Symb00-'
				$res=$this->symbol.$res.'-';
			break;
			case 4:
				// 4 = '(00Symb)'
				$res='('.$res.$this->symbol.')';
			break;
			case 5:
				// 5 = '-00Symb'
				$res='-'.$res.$this->symbol;
			break;
			case 6:
				// 6 = '00-Symb'
				$res=$res.'-'.$this->symbol;
			break;
			case 7:
				// 7 = '00Symb-'
				$res=$res.$this->symbol.'-';
			break;
			case 9:
				// 9 = '-Symb 00'
				$res='-'.$this->symbol.' '.$res;
			break;
			case 10:
				// 10 = '00 Symb-'
				$res=$res.' '.$this->symbol.'-';
			break;
			case 11:
				// 11 = 'Symb 00-'
				$res=$this->symbol.' '.$res.'-';
			break;
			case 12:
				// 12 = 'Symb -00'
				$res=$this->symbol.' -'.$res;
			break;
			case 13:
				// 13 = '00- Symb'
				$res=$res.'- '.$this->symbol;
			break;
			case 14:
				// 14 = '(Symb 00)'
				$res='('.$this->symbol.' '.$res.')';
			break;
			case 15:
				// 15 = '(00 Symb)'
				$res='('.$res.' '.$this->symbol.')';
			break;
			case 8:
			default :
				// 8 = '-00 Symb'
				$res='-'.$res.' '.$this->symbol;
			break;
			}
		}
		return($res);
	}
	// ================ /CURRENCY DISPLAY =========================
	// ============================================================
	} // end class
}
?>
