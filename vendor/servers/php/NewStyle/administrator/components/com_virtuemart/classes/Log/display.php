<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: display.php,v 1.9.2.1 2005/11/28 17:57:36 soeren_nb Exp $
* @package VirtueMart
* @subpackage Log
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
 * $Header: /cvsroot/virtuemart/virtuemart/classes/Log/display.php,v 1.9.2.1 2005/11/28 17:57:36 soeren_nb Exp $
 *
 * @version $ Revision: 1.8 $
 * @package Log
 */

/**
 * The Log_display class is a concrete implementation of the Log::
 * abstract class which writes message into browser in usual PHP maner.
 * This may be useful because when you use PEAR::setErrorHandling in
 * PEAR_ERROR_CALLBACK mode error messages are not displayed by
 * PHP error handler.
 *
 * @author  Paul Yanchenko <pusher@inaco.ru>
 * @since   Log 1.8.0
 * @package Log
 *
 * @example display.php     Using the display handler.
 */
class Log_display extends vmLog
{

    /**
     * String used to represent a line break.
     * @var string
     * @access private
     */
    var $_linebreak = "<br />\n";
    
	/**
     * Flag to enable or disable buffering.
     * @var boolean
     * @access private
     */
    var $_buffering = true;
    
    /**
     * Array to store messages when buffering is enabled
     *
     * @var array
     * @access private
     */
    var $_messages = array();
    
    /**
     * Counts messages in the message array
     * @var int
     */
    var $_count = 0;
    
    /**
     * Constructs a new Log_display object.
     *
     * @param string $name     Ignored.
     * @param string $ident    The identity string.
     * @param array  $conf     The configuration array.
     * @param int    $level    Log messages up to and including this level.
     * @access public
     */
    function Log_display($name = '', $ident = '', $conf = array(),
                         $level = PEAR_LOG_TIP)
    {
        $this->_id = md5(microtime());
        $this->_ident = $ident;
        $this->_mask = vmLog::UPTO($level);

        if (isset($conf['linebreak'])) {
            $this->_linebreak = $conf['linebreak'];
        }
        
        if (isset($conf['buffering'])) {
            $this->_buffering = $conf['buffering'];
        }
    }

    /**
     * Writes $message to the text browser. Also, passes the message
     * along to any Log_observer instances that are observing this Log.
     *
     * @param mixed  $message    String or object containing the message to log.
     * @param string $priority The priority of the message.  Valid
     *                  values are: PEAR_LOG_EMERG, PEAR_LOG_ALERT,
     *                  PEAR_LOG_CRIT, PEAR_LOG_ERR, PEAR_LOG_WARNING,
     *                  PEAR_LOG_NOTICE, PEAR_LOG_INFO, and PEAR_LOG_DEBUG.
     * @return boolean  True on success or false on failure.
     * @access public
     */
    function log($message, $priority = null)
    {
        /* If a priority hasn't been specified, use the default value. */
        if ($priority === null) {
            $priority = $this->_priority;
        }

        /* Abort early if the priority is above the maximum logging level. */
        if (!$this->_isMasked($priority)) {
            return false;
        }
        $this->_ticker++;
        
		if( $priority >= PEAR_LOG_ERR ) {
			defined( '_VM_LOG_ERRORS' ) or define( '_VM_LOG_ERRORS', 1);
		}
        /* Extract the string representation of the message. */
        $message = $this->_extractMessage($message);

        // Store the log message and its priority
    	$this->_messages[$this->_count]['priority'] = $priority;
    	$this->_messages[$this->_count]['message'] = $message;
    	$this->_count++;
        	
        if( !$this->_buffering ) {
        	
        	$this->printLog();
        }
        /* Notify observers about this log message. */
        $this->_announce(array('priority' => $priority, 'message' => $message));

        return true;
    }
    /**
     * Formats a message depending on its priority
     *
     * @param string $message
     * @param int $priority
     * @return formatted HTML code
     */
    function formatOutput( $message, $priority) {
    	if( $priority >= PEAR_LOG_TIP) {
    		return '<div class="shop_tip">'. $message . '</div>';
    	}
    	elseif( $priority >= PEAR_LOG_DEBUG) {
    		return '<div class="shop_debug">'. $message . '</div>';
    	}
    	elseif( $priority >= PEAR_LOG_INFO) {
    		return '<div class="shop_info">'. $message . '</div>';
    	}
    	elseif( $priority >= PEAR_LOG_WARNING ) {
    		return '<div class="shop_warning">'. $message . '</div>';
    	}
    	elseif( $priority >= PEAR_LOG_ERR ) {
    		return '<div class="shop_error">'. $message . '</div>';
    	}
    	elseif( $priority >= PEAR_LOG_CRIT ) {
    		return '<div class="shop_critical">'. $message . '</div>';
    	}
    }
    /**
     * Flush the _messages array and print all messages
     * @author Soeren Eberhardt
     */
	function printLog( $priority = null ) {
		$output = "";
		if( $this->_count > 10 && DEBUG) {
			// Wrap the messages into a scrollable div field
			$output .= '<div style="width:90%; overflow:auto; height:150px;">';
		}
		foreach( $this->_messages as $message ) {
			if( ( $priority === null || $priority <= $message['priority'] )
				&& $message['priority'] !== PEAR_LOG_DEBUG
				|| ( $message['priority'] === PEAR_LOG_DEBUG && DEBUG == '1')) {
				$output .= $this->formatOutput(
			             '<b>' . ucfirst($this->priorityToString($message['priority'])) . '</b>: '.
			             nl2br(htmlspecialchars($message['message'])) 
			             . $this->_linebreak,
			             $message['priority']
			           );
			}
		}
		if( $this->_count > 10 ) {
			$output .= '</div>';
		}
		$this->_count = 0;
		$this->_messages = array();
		if( $output ) {
			echo $output .  $this->_linebreak;
		}
	}
}
