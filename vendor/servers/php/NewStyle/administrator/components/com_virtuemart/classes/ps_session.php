<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_session.php,v 1.15.2.12 2006/05/07 12:39:03 soeren_nb Exp $
* @package VirtueMart
* @subpackage classes
* @copyright Copyright (C) 2004-2006 Soeren Eberhardt. All rights reserved.
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
 * This class handles the session initialization, restart
 * and the re-init of a session after redirection to a Shared SSL domain
 *
 */
class ps_session {

	var $component_name = "option=com_virtuemart";
	var $_session_name = 'virtuemart';
	/**
     * Initialize the Session environment for VirtueMart
     *
     */
	function ps_session() {
		$this->initSession();
	}
	/**
     * Initiate the Session
     *
     */
	function initSession() {
		global $vmLogger, $mainframe;
		
		// Some servers start the session before we can, so close those and start again		
		if( !defined('_PSHOP_ADMIN') && !empty($_SESSION)) {
			session_write_close();
			unset( $_SESSION );
		}
		if( empty( $_SESSION )) {
			// Session not yet started!			
			session_name( $this->_session_name );
			
			if( @$_REQUEST['option'] == 'com_virtuemart' ) {
				ob_start();
			}
			@session_start();
			
			if( !empty($_SESSION) && !empty($_COOKIE[$this->_session_name])) {
				$vmLogger->debug( 'A Session called '.$this->_session_name.' (ID: '.session_id().') was successfully started!' );
			}
			else {
				$vmLogger->debug( 'A Cookie had to be set to keep the session (there was none - does your Browser keep the Cookie?) although a Session already has been started! If you see this message on each page load, your browser doesn\'t accept Cookies from this site.' );
			}
		}
	}
		
	/**
	 * Returns the Joomla/Mambo Session ID
	 *
	 */
	function getSessionId() {
		global $mainframe;
		// Joomla >= 1.0.8
		if( is_callable( array( 'mosMainframe', 'sessionCookieName'))) {			
			// Session Cookie `name`
			$sessionCookieName 	= mosMainFrame::sessionCookieName();
			// Get Session Cookie `value`
			$sessionCookie 		= mosGetParam( $_COOKIE, $sessionCookieName, null );
			// Session ID / `value`
			return mosMainFrame::sessionCookieValue( $sessionCookie );
		}
		// Mambo 4.6
		elseif( is_callable( array('mosSession', 'getCurrent' ))) {
			$session =& mosSession::getCurrent();
			return $session->session_id;
		}
		// Mambo <= 4.5.2.3 and Joomla <= 1.0.7
		elseif( !empty( $mainframe->_session->session_id )) {
			// Set the sessioncookie if its missing
			// this is needed for joomla sites only
			return $mainframe->_session->session_id;
		}
		
	}
	function restartSession( $sid = '') {
		
		// Save the session data and close the session
		session_write_close();
		
		// Prepare the new session
		if( $sid != '' ) {
			session_id( $sid );
		}
		session_name( $this->_session_name );
		// Start the new Session.
		session_start();
		
	}
	function emptySession() {
		global $mainframe;
		$_SESSION = array();
		$_COOKIE[$this->_session_name] = md5( $this->getSessionId() );
	}
	/**
     * This is a solution for  the Shared SSL problem
     * We have to copy some cookies from the Main Mambo site domain into
     * the shared SSL domain (only when necessary!)
	 *
	 * The function is called on each page load.
	 */
	function prepare_SSL_Session() {
		global $mainframe, $my, $mosConfig_secret, $_VERSION;

		$ssl_redirect = mosGetParam( $_GET, "ssl_redirect", 0 );
		$martID = mosGetParam( $_GET, 'martID', null );
		$ssl_domain = "";
		
		/**
        * This is the first part of the Function:
        * We check if the function must be called at all
        * Usually this is only called once: Before we go to the checkout.
        * The variable ssl_redirect=1 is appended to the URL, just for this function knows
        * is must be active! This has nothing to do with SSL / Shared SSL or whatever
        */
		if( $ssl_redirect == 1 ) {
			// check_Shared_SSL compares the usual domain name
			// and the https Domain Name. If both do not match, we move on
			// else we leave this function.
			if( $this->check_Shared_SSL( $ssl_domain ) && $_SERVER['SERVER_PORT'] != 443) {
				
				$sessionId = $this->getSessionId();
				
				if( !empty($my->id)) {
					// User is already logged in
					// We need to transfer the usercookie if present
					$martID = @base64_encode( $_COOKIE[$this->_session_name]."|".$sessionId."|".$_COOKIE['usercookie']['password']."|".$_COOKIE['usercookie']['username'] );

				}
				else {
					// User is not logged in, but has Cart Contents
					$martID = base64_encode( $_COOKIE[$this->_session_name]."|".$sessionId );
				}
				$sessionFile = IMAGEPATH. md5( $martID ).'.sess';
				
				$session_contents = session_encode();
				if( file_exists( ADMINPATH.'install.copy.php')) {
					require_once( ADMINPATH.'install.copy.php');
				}
				file_put_contents( $sessionFile, $session_contents );

				// Redirect and send the Cookie Values within the variable martID
				mosRedirect( SECUREURL . "index.php?option=com_virtuemart&page=checkout.index&martID=$martID" );
			}
			// do nothing but redirect
			else {
				mosRedirect( $this->url(SECUREURL . "index.php?page=checkout.index") );
			}
		}
		/**
        * This is part two of the function
        * If the redirect (see 4/5 lines above) was successful
        * and the Store uses Shared SSL, we have the variable martID
        * So let's copy the Cookie Values on the new domain and start the old session
        * othwerwise: do nothing.
        */
		if( !empty( $martID ) ) {
			if( $this->check_Shared_SSL( $ssl_domain ) ) {
	
				// We now need to copy the Mambo Cookies (which are only
				// valid for the "normal" Domain) to the SSL Domain
				if( $martID ) {
					$cookievals = base64_decode( $martID );
	
					$id_array = explode( "|", $cookievals );
	
					$virtuemartcookie = $id_array[0];
					$sessioncookie = $id_array[1];
					$usercookie["password"] = @$id_array[2];
					$usercookie["username"] = @$id_array[3];
	
					// Log the user in with his username
					if( !empty( $usercookie["username"]) && !empty( $usercookie["password"] )) {
						$mainframe->login( $usercookie["username"], $usercookie["password"] );
					}
					
					require_once( ADMINPATH.'install.copy.php');
					
					$sessionFile = IMAGEPATH. md5( $martID ).'.sess';
					
					// Read the contents of the session file
					$session_data = file_get_contents( $sessionFile );
					// Delete it for security and disk space reasons
					unlink( $sessionFile );
					
					// Read the session data into $_SESSION
					session_decode( $session_data );
					
					session_write_close();
					
					// Prevent the martID from being displayed in the URL
					if( !empty( $_GET['martID'] )) {
						mosRedirect( $this->url(SECUREURL . "index.php?page=checkout.index&cartReset=N") );
					}
	
				}
	
			}
		}
	}
	/**
	 * This function compares the store URL with the SECUREURL
	 * and returns the result
	 *
	 * @param string $ssl_domain The SSL domain (empty string to be filled here)
	 * @return boolean True when we have to do a SSL redirect (for Shared SSL)
	 */
	function check_Shared_SSL( &$ssl_domain ) {

		if( URL == SECUREURL ) {
			$ssl_domain = str_replace("http://", "", URL );
			$ssl_redirect = false;
			return $ssl_redirect;
		}
		// Extract the Domain Names without the Protocol
		$domain = str_replace("http://", "", URL );
		$ssl_domain = str_replace("https://", "", SECUREURL );
		// If SSL and normal Domain do not match,
		// we assume that you use Shared SSL

		if( $ssl_domain != $domain ) {
			$ssl_redirect = true;
		}
		else {
			$ssl_redirect = false;
		}

		return $ssl_redirect;
	}
	
	/**
     * Gets the Itemid for the com_virtuemart Component
     * and stores it in a global Variable
     *
     * @return int Itemid
     */
	function getShopItemid() {

		if( empty( $_REQUEST['shopItemid'] )) {
			$db = new ps_DB;
			$db->query( "SELECT id FROM #__menu WHERE link='index.php?option=com_virtuemart' AND published=1 AND access=0");
			if( $db->next_record() ) {
				$_REQUEST['shopItemid'] = $db->f("id");
			}
			else {
				if( !empty( $_REQUEST['Itemid'] )) {
					$_REQUEST['shopItemid'] = $_REQUEST['Itemid'];
				}
				else {
					$_REQUEST['shopItemid'] = 1;
				}
			}
		}

		return $_REQUEST['shopItemid'];

	}
	
	/**
	 * Prints a reformatted URL
	 *
	 * @param string $text
	 */
	function purl($text) {		
		echo $this->url( $text );		
	}
	
	/**
	 * This reformats an URL, appends "option=com_virtuemart" and "Itemid=XX"
	 * where XX is the Id of an entry in the table mos_menu with "link: option=com_virtuemart"
	 * It also calls sefRelToAbs to apply SEF formatting
	 * 
	 * @param string $text THE URL
	 * @return string The reformatted URL
	 */
	function url($text) {
		global $mm_action_url, $page;
		
		if( !defined( '_PSHOP_ADMIN' )) {
			$Itemid = "&Itemid=".$this->getShopItemid();
		}
		else {
			$Itemid = '';
		}

		switch ($text) {
			case SECUREURL:
				$text =  SECUREURL.$_SERVER['PHP_SELF']."?".$this->component_name.$Itemid;
				break;
			case URL:
				$text =  URL.$_SERVER['PHP_SELF']."?".$this->component_name.$Itemid;
				break;
				
			default:
				$limiter = strpos($text, '?');

				$appendix = "";
				// now append "&option=com_virtuemart&Itemid=XX"
				if (!strstr($text, "option=")) {
					$appendix .= "&" . $this->component_name;
				}
				$appendix .= $Itemid;
	
				if (!defined( '_PSHOP_ADMIN' )) {
	
					// be sure that we have the correct PHP_SELF in front of the url
					if( stristr( $_SERVER['PHP_SELF'], "index2.php" )) {
						$prep = "index2.php";
					}
					else {
						$prep = "index.php";
					}
					if( stristr( $text, "index2.php" )) {
						$prep = "index2.php";
					}
	
					$appendix = $prep.substr($text, $limiter, strlen($text)-1).$appendix;
					$appendix = sefRelToAbs( str_replace( $prep.'&', $prep.'?', $appendix ) );
					if( !stristr( $appendix, URL ) && !stristr( $appendix, SECUREURL ) ) {
						$appendix = $mm_action_url . $appendix;
					}
				}
				elseif( $_SERVER['SERVER_PORT'] == 443 ) {
					$appendix = SECUREURL."administrator/index2.php".substr($text, $limiter, strlen($text)-1).$appendix;
				}
				else {
					$appendix = URL."administrator/index2.php".substr($text, $limiter, strlen($text)-1).$appendix;
				}
	
				if ( stristr($text, SECUREURL)) {
					$appendix = str_replace(URL, SECUREURL, $appendix);
				}
				elseif( !@strstr( $page, "checkout." ) && !@strstr( $page, "account." ) ) {
					$appendix = str_replace(SECUREURL, URL, $appendix);
				}
	
				$text = $appendix;
	
				break;
		}
		/**
	    ** This has to be redone, because it doesn't work with mosRedirect
	
	    if (!defined( '_PSHOP_ADMIN' ) && $pshop_mode != "admin") {
	        $text = str_replace( "&", "&amp;", $text );
	        $text = str_replace( "&amp;amp;", "&amp;", $text );
	    } 
	    */
		return $text;
	}

	/**
	 * Formerly printed the session id into a hidden field
	 * @deprecated 
	 * @return boolean
	 */
	function hidden_session() {
		return true;
	}


} // end of class session
?>
