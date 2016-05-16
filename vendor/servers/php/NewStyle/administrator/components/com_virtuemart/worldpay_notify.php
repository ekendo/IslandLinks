<?php
/*
* @version $Id: worldpay_notify.php,v 1.1 2005/09/06 20:04:19 soeren_nb Exp $
* @package VirtueMart
* @subpackage Payment
*
* @copyright (C) 2004 Soeren Eberhardt
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* VirtueMart is Free Software.
* VirtueMart comes with absolute no warranty.
*
* www.virtuemart.net
*/

if ($_POST) {

    

    define('_VALID_MOS', '1');

    global $mosConfig_absolute_path, $mosConfig_live_site, $mosConfig_lang;

    /*** access Mambo's configuration file ***/
    $my_path = dirname($_SERVER['SCRIPT_FILENAME']);
    $mambo_path = str_replace("administrator/components/com_virtuemart", "", $my_path);
    require_once($mambo_path.'configuration.php');
    require_once($mambo_path.'includes/database.php');

    global $database;

    $database = new database( $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix );

    // load Mambo Language File
    if (file_exists( $mosConfig_absolute_path. '/language/'.$mosConfig_lang.'.php' )){
      require_once( $mosConfig_absolute_path. '/language/'.$mosConfig_lang.'.php' );
    }
    else{
      require_once( $mosConfig_absolute_path. '/language/english.php' );
    }

    /*** END of Mambo config ***/

    /*** VirtueMart part ***/

    define('PHPSHOPPATH', $mosConfig_absolute_path.'/administrator/components/com_virtuemart/');

    require_once(PHPSHOPPATH."virtuemart.cfg.php");

	
	require_once( CLASSPATH. "language.class.php" );
	
    //Set up the mailer to infor Warehouse of validated order
    //require_once( $mosConfig_absolute_path . '/includes/phpmailer/class.phpmailer.php');
    //$mail = new mosPHPMailer();
    //$mail->PluginDir = $mosConfig_absolute_path . '/includes/phpmailer/';
    //$mail->SetLanguage("en", $mosConfig_absolute_path . '/includes/phpmailer/language/');
  

    /* load the VirtueMart Language File */
    if (file_exists( ADMINPATH. 'languages/'.$mosConfig_lang.'.php' ))
      require_once( ADMINPATH. 'languages/'.$mosConfig_lang.'.php' );
    else
      require_once( ADMINPATH. 'languages/english.php' );


    /* 
    Moded
    Gotta replace this...perhaps
    Load the PayPal Configuration File 
    */ 
    require_once( CLASSPATH. 'payment/ps_worldpay.cfg.php' );



    /* Load the VirtueMart database class */
    require_once( CLASSPATH. 'ps_database.php' );

    /*** END VirtueMart part ***/

    /**
    Read in the post from worldpay.
    Email was used in PayPal version

    **/
    $workstring = 'cmd=_notify-validate'; // Notify validate
    $i = 1;
    foreach ($_POST as $ipnkey => $ipnval) {
        if (get_magic_quotes_gpc())
            // Fix issue with magic quotes
            $ipnval = stripslashes ($ipnval);
            
       if (!eregi("^[_0-9a-z-]{1,30}$",$ipnkey)  || !strcasecmp ($ipnkey, 'cmd'))  { 
            // ^ Antidote to potential variable injection and poisoning
            unset ($ipnkey); 
            unset ($ipnval); 
        } 
       // Eliminate the above
        // Remove empty keys (not values)
        if (@$ipnkey != '') { 
          //unset ($_POST); // Destroy the original ipn post array, sniff...
          $workstring.='&'.@$ipnkey.'='.urlencode(@$ipnval); 
        }
       echo "key ".$i++.": $ipnkey, value: $ipnval<br />";
    } // Notify string

    $payment_status  = trim(stripslashes($_POST['transStatus'])); //if $payment_status == 'Y'?
    $order_id =  trim(stripslashes($_POST['cartId']));
    
    $d['order_id'] = $order_id;    //this identifies the order record

    if( $payment_status == 'Y' ){
        $d['order_status'] = 'C';  //this is the new value for the database field I think X for cancelled, C for confirmed
    }
    else if( $payment_status == 'C' ){
        $d['order_status'] = 'X';  //this is the new value for the database field I think X for cancelled, C for confirmed
    }

    require_once ( CLASSPATH . 'ps_order.php' );

    $ps_order= new ps_order;

    $ps_order->order_status_update($d);
}

?>
