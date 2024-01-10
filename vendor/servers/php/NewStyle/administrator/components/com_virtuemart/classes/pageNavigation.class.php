<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: pageNavigation.class.php,v 1.8 2005/11/04 15:16:48 soeren_nb Exp $
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
* Page navigation support class
* @package VirtueMart
*/
class vmPageNav {
	/** @var int The record number to start dislpaying from */
	var $limitstart = null;
	/** @var int Number of rows to display per page */
	var $limit = null;
	/** @var int Total number of rows */
	var $total = null;
	
	function vmPageNav( $total, $limitstart, $limit ) {
		$this->total = intval( $total );
		$this->limitstart = max( $limitstart, 0 );
		$this->limit = max( $limit, 1 );
		if ($this->limit > $this->total) {
			$this->limitstart = 0;
		}
		if (($this->limit-1)*$this->limitstart > $this->total) {
			$this->limitstart -= $this->limitstart % $this->limit;
		}
	}
	/**
	* @return string The html for the limit # input box
	*/
	function getLimitBox () {
		$limits = array();
		for ($i=5; $i <= 30; $i+=5) {
			$limits[] = mosHTML::makeOption( "$i" );
		}
		$limits[] = mosHTML::makeOption( "50" );

		// build the html select list
		$html = mosHTML::selectList( $limits, 'limit', 'class="inputbox" size="1" onchange="document.adminForm.submit();"',
		'value', 'text', $this->limit );
		$html .= "\n<input type=\"hidden\" name=\"limitstart\" value=\"$this->limitstart\" />";
		return $html;
	}
	/**
	* Writes the html limit # input box
	*/
	function writeLimitBox () {
		echo vmPageNav::getLimitBox();
	}
	function writePagesCounter() {
		echo $this->getPagesCounter();
	}
	/**
	* @return string The html for the pages counter, eg, Results 1-10 of x
	*/
	function getPagesCounter() {
	    $html = '';
		$from_result = $this->limitstart+1;
		if ($this->limitstart + $this->limit < $this->total) {
			$to_result = $this->limitstart + $this->limit;
		} else {
			$to_result = $this->total;
		}
		if ($this->total > 0) {
			$html .= _PN_RESULTS." $from_result - $to_result "._PN_OF." $this->total";
		} else {
			//$html .= "\nNo records found.";
		}
		return $html;
	}
	/**
	* Writes the html for the pages counter, eg, Results 1-10 of x
	*/
	function writePagesLinks() {
	    echo $this->getPagesLinks();
	}
	/**
	* @return string The html links for pages, eg, previous, next, 1 2 3 ... x
	*/
	function getPagesLinks() {
	    $html = '';
		$displayed_pages = 10;
		$total_pages = ceil( $this->total / $this->limit );
		$this_page = ceil( ($this->limitstart+1) / $this->limit );
		$start_loop = (floor(($this_page-1)/$displayed_pages))*$displayed_pages+1;
		if ($start_loop + $displayed_pages - 1 < $total_pages) {
			$stop_loop = $start_loop + $displayed_pages - 1;
		} else {
			$stop_loop = $total_pages;
		}

		if ($this_page > 1) {
			$page = ($this_page - 2) * $this->limit;
			$html .= "\n<a href=\"#beg\" class=\"pagenav\" title=\"first page\" onclick=\"javascript: document.adminForm.limitstart.value=0; document.adminForm.submit();return false;\">&lt;&lt; "._PN_START."</a>";
			$html .= "\n<a href=\"#prev\" class=\"pagenav\" title=\"previous page\" onclick=\"javascript: document.adminForm.limitstart.value=$page; document.adminForm.submit();return false;\">&lt; "._PN_PREVIOUS."</a>";
		} else {
			$html .= "\n<span class=\"pagenav\">&lt;&lt; "._PN_START."</span>";
			$html .= "\n<span class=\"pagenav\">&lt; "._PN_PREVIOUS."</span>";
		}

		for ($i=$start_loop; $i <= $stop_loop; $i++) {
			$page = ($i - 1) * $this->limit;
			if ($i == $this_page) {
				$html .= "\n<span class=\"pagenav\"> $i </span>";
			} else {
				$html .= "\n<a href=\"#$i\" class=\"pagenav\" onclick=\"javascript: document.adminForm.limitstart.value=$page; document.adminForm.submit();return false;\"><strong>$i</strong></a>";
			}
		}

		if ($this_page < $total_pages) {
			$page = $this_page * $this->limit;
			$end_page = ($total_pages-1) * $this->limit;
			$html .= "\n<a href=\"#next\" class=\"pagenav\" title=\"next page\" onclick=\"javascript: document.adminForm.limitstart.value=$page; document.adminForm.submit();return false;\"> "._PN_NEXT." &gt;</a>";
			$html .= "\n<a href=\"#end\" class=\"pagenav\" title=\"end page\" onclick=\"javascript: document.adminForm.limitstart.value=$end_page; document.adminForm.submit();return false;\"> "._PN_END." &gt;&gt;</a>";
		} else {
			$html .= "\n<span class=\"pagenav\">"._PN_NEXT." &gt;</span>";
			$html .= "\n<span class=\"pagenav\">"._PN_END." &gt;&gt;</span>";
		}
		return $html;
	}
	
	function getListFooter() {
	    $html = '<table class="adminlist">';
	    if( $this->total > $this->limit || $this->limitstart > 0) {
	    	
		    $html .= '<tr><th colspan="3">';
		    
			$html .= $this->getPagesLinks();
			$html .= '</th></tr>';
	    }
		$html .= '<tr><td nowrap="true" width="48%" align="right">'._PN_DISPLAY_NR.'</td>';
		$html .= '<td>' .$this->getLimitBox() . '</td>';
		$html .= '<td nowrap="true" width="48%" align="left">' . $this->getPagesCounter() . '</td>';
		$html .= '</tr></table>';
  		return $html;
	}
/**
* @param int The row index
* @return int
*/
	function rowNumber( $i ) {
		return $i + 1 + $this->limitstart;
	}
/**
* @param int The row index
* @param string The task to fire
* @param string The alt text for the icon
* @return string
*/
	function orderUpIcon( $i, $condition=true, $task='orderup', $alt='Move Up', $page, $func ) {
		global $mosConfig_live_site;
		if (($i > 0 || ($i+$this->limitstart > 0)) && $condition) {
		    return '<a href="#reorder" onclick="return vm_listItemTask(\'cb'.$i.'\',\''.$task.'\', \'adminForm\', \''.$page.'\', \''.$func.'\')" title="'.$alt.'">
				<img src="'.$mosConfig_live_site.'/administrator/images/uparrow.png" width="12" height="12" border="0" alt="'.$alt.'" />
			</a>';
  		} else {
  		    return '&nbsp;';
		}
	}
/**
* @param int The row index
* @param int The number of items in the list
* @param string The task to fire
* @param string The alt text for the icon
* @return string
*/
	function orderDownIcon( $i, $n, $condition=true, $task='orderdown', $alt='Move Down', $page, $func ) {
		global $mosConfig_live_site;
		if (($i < $n-1 || $i+$this->limitstart < $this->total-1) && $condition) {
			return '<a href="#reorder" onclick="return vm_listItemTask(\'cb'.$i.'\',\''.$task.'\', \'adminForm\', \''.$page.'\', \''.$func.'\')" title="'.$alt.'">
				<img src="'.$mosConfig_live_site.'/administrator/images/downarrow.png" width="12" height="12" border="0" alt="'.$alt.'" />
			</a>';
  		} else {
  		    return '&nbsp;';
		}
	}
}
?>