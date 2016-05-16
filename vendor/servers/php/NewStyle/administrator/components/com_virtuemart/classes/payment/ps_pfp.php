<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/*
 * @package mambo-phpShop
 * @subpackage Payment
 *
 * The ps_pfp class, containing the payment processing code
 * for transactions with VeriSign's Pay Flow Pro.
 */

class ps_pfp {

	var $payment_code = "PFP";
	var $classname = "ps_pfp";
  
	/*
	 * Show all configuration parameters for this payment method
	 * @returns boolean False when the Payment method has no configration
	 */
	function show_configuration() { 
    
		global $VM_LANG, $sess;
		$db =& new ps_DB;
		$payment_method_id = mosGetParam( $_REQUEST,
		    'payment_method_id', null );
		/** Read current Configuration ***/
		require_once(CLASSPATH ."payment/".$this->classname.".cfg.php");
	?>
	  <table>
	    <tr>
              <td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ENABLE_PFP_TESTMODE ?></strong></td>
	      <td>
                <select name="PFP_TEST_REQUEST" class="inputbox" >
                <option <?php if (PFP_TEST_REQUEST == 'TRUE') echo "selected=\"selected\""; ?> value="TRUE"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_YES ?></option>
                <option <?php if (PFP_TEST_REQUEST == 'FALSE') echo "selected=\"selected\""; ?> value="FALSE"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_NO ?></option>
                </select>
	      </td>
	      <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ENABLE_PFP_TESTMODE_EXPLAIN ?></td>
	    </tr>
	    <tr>
	      <td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PFP_PARTNER ?></strong></td>
	      <td>
                <input type="text" name="PFP_PARTNER" class="inputbox" value="<? echo PFP_PARTNER ?>" />
	      </td>
	      <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PFP_PARTNER_EXPLAIN ?></td>
	    </tr>
	    <tr>
	      <td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PFP_USERNAME ?></strong></td>
	      <td>
                <input type="text" name="PFP_LOGIN" class="inputbox" value="<? echo PFP_LOGIN ?>" />
	      </td>
	      <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PFP_USERNAME_EXPLAIN ?></td>
	    </tr>
	    <tr>
	      <td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PFP_PWD ?></strong></td>
	      <td>
                <a id="changekey" href="<?php $sess->purl($_SERVER['PHP_SELF']."?page=store.payment_method_keychange&pshop_mode=admin&payment_method_id=$payment_method_id") ?>" >
                <input onclick="document.location=document.getElementById('changekey').href" type="button" name="" value="<?php echo $VM_LANG->_PHPSHOP_CHANGE_TRANSACTION_KEY ?>" /><a/>
	      </td>
	      <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PFP_PWD_EXPLAIN ?></td>
	    </tr>
	    <tr>
	      <td><strong><?php echo $VM_LANG->_PHPSHOP_PAYMENT_CVV2 ?></strong></td>
	      <td>
                <select name="PFP_CHECK_CARD_CODE" class="inputbox">
                <option <?php if (PFP_CHECK_CARD_CODE == 'YES') echo "selected=\"selected\""; ?> value="YES">
                <?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_YES ?></option>
                <option <?php if (PFP_CHECK_CARD_CODE == 'NO') echo "selected=\"selected\""; ?> value="NO">
                <?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_NO ?></option>
                </select>
	      </td>
	      <td><?php echo $VM_LANG->_PHPSHOP_PAYMENT_CVV2_TOOLTIP ?></td>
	    </tr>
	    <tr>
	      <td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PFP_AUTHENTICATIONTYPE ?></strong></td>
	      <td>
		<select name="PFP_TYPE" class="inputbox">
                <option <?php if (PFP_TYPE == 'A') echo "selected=\"selected\""; ?> value="A">Authorize</option>
                <option <?php if (PFP_TYPE == 'S') echo "selected=\"selected\""; ?> value="S">Sale</option>
                </select>
	      </td>
	      <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PFP_AUTHENTICATIONTYPE_EXPLAIN ?></td>
	    </tr>
	    <tr>
	      <td><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PFP_CERT_PATH ?></strong></td>
	      <td>
                <input type="text" name="PFP_CERT_PATH" class="inputbox" value="<? echo PFP_CERT_PATH ?>" />
	      </td>
	      <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PFP_CERT_PATH_EXPLAIN ?></td>
	    </tr>
	    <tr><td colspan="3"><hr/></td></tr>
	    <tr>
	      <td><strong>Order Status for successful transactions</strong></td>
	      <td>
                <select name="PFP_VERIFIED_STATUS" class="inputbox" >
                <?php
                    $q = "SELECT order_status_name,order_status_code FROM #__{vm}_order_status ORDER BY list_order";
                    $db->query($q);
                    $order_status_code = Array();
                    $order_status_name = Array();
                    
                    while ($db->next_record()) {
                      $order_status_code[] = $db->f("order_status_code");
                      $order_status_name[] =  $db->f("order_status_name");
                    }
                    for ($i = 0; $i < sizeof($order_status_code); $i++) {
                      echo "<option value=\"" . $order_status_code[$i];
                      if (PFP_VERIFIED_STATUS == $order_status_code[$i]) 
                         echo "\" selected=\"selected\">";
                      else
                         echo "\">";
                      echo $order_status_name[$i] . "</option>\n";
                    }?>
                    </select>
	      </td>
	      <td>
		<?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PFP_VERIFIED_STATUS_EXPLAIN ?>
	      </td>
	    </tr>
            <tr>
	      <td><strong>Order Status for failed transactions</strong></td>
	      <td>
                <select name="PFP_INVALID_STATUS" class="inputbox" >
                <?php
                    for ($i = 0; $i < sizeof($order_status_code); $i++) {
                      echo "<option value=\"" . $order_status_code[$i];
                      if (PFP_INVALID_STATUS == $order_status_code[$i]) 
                         echo "\" selected=\"selected\">";
                      else
                         echo "\">";
                      echo $order_status_name[$i] . "</option>\n";
                    } ?>
		</select>
	      </td>
	      <td>
		<?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PFP_INVALID_STATUS_EXPLAIN ?>
	      </td>
	    </tr>
	  </table>
	<?php
		// return false if there's no configuration
		return true;
	}
   
	function has_configuration() {
		// return false if there's no configuration
		return true;
	}
   
	/*
	 * Returns the "is_writeable" status of the configuration file
	 * @param void
	 * @returns boolean True when the configuration file is writeable,
	 *     false when not
	 */
	function configfile_writeable() {
		return is_writeable( CLASSPATH . "payment/" .
		    $this->classname . ".cfg.php" );
	}
   
	/*
	 * Returns the "is_readable" status of the configuration file
	 * @param void
	 * @returns boolean True when the configuration file is writeable,
	 *    false when not
	 */
	function configfile_readable() {
		return is_readable( CLASSPATH . "payment/" .
		    $this->classname . ".cfg.php" );
	}   

	/*
	 * Writes the configuration file for this payment method
	 * @param array An array of objects
	 * @returns boolean True when writing was successful
	 */
	function write_configuration( &$d ) {
      
		$my_config_array = array(
		    "PFP_TEST_REQUEST" => $d['PFP_TEST_REQUEST'],
		    "PFP_PARTNER" => $d['PFP_PARTNER'],
		    "PFP_LOGIN" => $d['PFP_LOGIN'],
		    "PFP_TYPE" => $d['PFP_TYPE'],
		    "PFP_CHECK_CARD_CODE" => $d['PFP_CHECK_CARD_CODE'],
		    "PFP_VERIFIED_STATUS" => $d['PFP_VERIFIED_STATUS'],
		    "PFP_INVALID_STATUS" => $d['PFP_INVALID_STATUS'],
		    "PFP_CERT_PATH" => $d['PFP_CERT_PATH']);
		$config = "<?php\n";
		$config .= "defined('_VALID_MOS') or ";
		$config .= "die('Direct Access to this location is ";
		$config .= "not allowed.'); \n\n";
		foreach( $my_config_array as $key => $value ) {
			$config .= "define ('$key', '$value');\n";
		}
      
		$config .= "?>";
  
		if ($fp = fopen(CLASSPATH . "payment/" .
		    $this->classname . ".cfg.php", "w")) {
			fputs($fp, $config, strlen($config));
			fclose ($fp);
			return true;
		} else {
			return false;
		}
	}
   
	/*
	 * name: process_payment()
	 * created by: durian
	 * description: process transaction with Pay Flow Pro
	 * parameters:
	 * 	$order_number, the number of the order we're processing here
	 * 	$order_total, the total $ of the order
	 * returns: 
	 */
	function process_payment($order_number, $order_total, &$d) {
        
		global $vendor_mail, $vendor_currency, $VM_LANG, $vmLogger;
		$database = new ps_DB;
      
		$ps_vendor_id = $_SESSION["ps_vendor_id"];
		$auth = $_SESSION['auth'];
		$ps_checkout = new ps_checkout;
      
		/*** Get the Configuration File for Pay Flow Pro ***/
		require_once(CLASSPATH . "payment/" .
		    $this->classname . ".cfg.php");
        
		// Get the Transaction Key securely from the database
		$database->query( "SELECT DECODE(payment_passkey,'" .
		    ENCODE_KEY . "') as passkey " .
		    "FROM #__{vm}_payment_method WHERE payment_class='" .
		    $this->classname . "' " .
		    "AND shopper_group_id='" . $auth['shopper_group_id'] .
		    "'");
		$transaction = $database->record[0];
		if( empty($transaction->passkey)) {
			$vmLogger->err($VM_LANG->_PHPSHOP_PAYMENT_ERROR .
			    "PFP account password is empty. You must " .
			    "adjust your settings.");
			return false;
		}
        
		// Get user billing information
		$dbbt = new ps_DB;
		$qt = "SELECT * FROM #__{vm}_user_info WHERE user_id='" .
		    $auth["user_id"] . "' AND address_type='BT'";
		$dbbt->query($qt);
		$dbbt->next_record();
		$user_info_id = $dbbt->f("user_info_id");
		if( $user_info_id != $d["ship_to_info_id"]) {
			// Get user billing information
			$dbst =& new ps_DB;
			$qt = "SELECT * FROM #__{vm}_user_info " .
			    "WHERE user_info_id='" . $d["ship_to_info_id"] .
			    "' AND address_type='ST'";
			$dbst->query($qt);
			$dbst->next_record();
		} else {
			$dbst = $dbbt;
		}

		/* XXX If a test request, use "test-payflow.verisign.com" */
		if (PFP_TEST_REQUEST == 'TRUE') {
			$host = 'test-payflow.verisign.com';
			$vmLogger->debug("Using test site: " . $host);
		} else {
			$host = 'payflow.verisign.com';
			$vmLogger->debug("Using real site: " . $host);
		}

		$name = $dbbt->f("first_name") . ' ' . $dbbt->f("last_name");
		$expmon = $_SESSION['ccdata']['order_payment_expire_month'];
		$expyear = $_SESSION['ccdata']['order_payment_expire_year'];
		$expmon = sprintf("%02d", $expmon % 100);
		$expyear = sprintf("%02d", $expyear % 100);
		/* Pay Flow Pro vars to send */
		$request = array (
			'USER' => PFP_LOGIN,
			'VENDOR' => PFP_LOGIN,
			'PWD' => $transaction->passkey,
			'PARTNER' => PFP_PARTNER,
			'TENDER' => 'C',
			/* This needs to be either [S]ale or [A]uthorize */
			'TRXTYPE' => PFP_TYPE,
			'AMT' => $order_total,
			'NAME' => substr($name, 0, 30),
			'ACCT' => $_SESSION['ccdata']['order_payment_number'],
			'CVV2' => $_SESSION['ccdata']['credit_card_code'],
			'EXPDATE' => $expmon . $expyear,
			'STREET' => substr($dbbt->f("address_1"), 0, 30),
			'ZIP' => substr($dbbt->f("zip"), 0, 9),
			'COMMENT1' => substr('Email: ' . $dbbt->f("email") .
			    ', Remote IP: ' . $_SERVER["REMOTE_ADDR"], 0, 128),
			'CUSTREF' => substr($order_number, 0, 12),
			);

		/*
		 * Let pfpro know where to find its special certificate
		 * This won't work if Safe Mode is on.  If it is on,
		 * add SetEnv PFPRO_CERT_PATH path to apache config file.
		 */
		$certpath = 'PFPRO_CERT_PATH=';
		$certpath .= PFP_CERT_PATH;
		putenv($certpath);

		/*
		 * We are using the pfpro PHP extension.  If you don't
		 * have this it won't work.
		 */
		$response = pfpro_process($request, $host);

		if ($response['RESULT'] == '0') {
			/* We're approved (or captured)! */
			$d["order_payment_log"] =
			    $VM_LANG->_PHPSHOP_PAYMENT_TRANSACTION_SUCCESS
			    . ': ';
			$d["order_payment_log"] .= $response['RESPMSG'];

			/* record transaction ID */
			$d["order_payment_trans_id"] = $response['PNREF'];

			return True;
		} elseif ($response['RESULT'] == '12') {
			/* credit card declined - get out the scissors */
			$vmLogger->err($response['RESPMSG']);
			$d["order_payment_log"] =
			    $VM_LANG->_PHPSHOP_PAYMENT_ERROR . ': ';
			$d["order_payment_log"] .= $response['RESPMSG'];

			/* record transaction ID */
			$d["order_payment_trans_id"] = $response['PNREF'];

			return False;
		} else {
			// Transaction Error
			$vmLogger->err($response['RESPMSG']);
			$d["order_payment_log"] =
			    $VM_LANG->_PHPSHOP_INTERNAL_ERROR . ': ';
			$d["order_payment_log"] .= $response['RESPMSG'];

			/* record transaction ID */
			$d["order_payment_trans_id"] = $response['PNREF'];

			return False;
		}
	}

	/*
	 * name: capture_payment()
	 * description: Capture a previous transaction with PayFlow Pro
	 * parameters: $order_number, the number of the order, we're processing.
	 */
	function capture_payment( &$d ) {

		global $vendor_mail, $vendor_currency, $VM_LANG, $vmLogger;
		$database = new ps_DB();
		$auth = $_SESSION['auth'];

		$vmLogger->debug("Capturing credit card.");

		/* If a test request, use "test-payflow.verisign.com" */
		if (PFP_TEST_REQUEST == 'TRUE') {
			$host = 'test-payflow.verisign.com';
			$vmLogger->debug("Using test site: " . $host);
		} else {
			$host = 'payflow.verisign.com';
			$vmLogger->debug("Using real site: " . $host);
		}

		if (empty($d['order_number'])) {
			$d['error'] = "Error: No Order Number provided.";
			return false;
		}

		/* Get the Configuration File for PayFlow Pro */
		require_once(CLASSPATH . "payment/" . $this->classname .
		    ".cfg.php");
        
		/* Get the Transaction Key securely from the database */
		$database->query( "SELECT DECODE(payment_passkey,'" .
		    ENCODE_KEY . "') as passkey " .
		    "FROM #__{vm}_payment_method WHERE payment_class='" .
		    $this->classname . "' " .
		    "AND shopper_group_id='" . $auth['shopper_group_id'] .
		    "'");
		$transaction = $database->record[0];
		if (empty($transaction->passkey)) {
			$vmLogger->err($VM_LANG->_PHPSHOP_PAYMENT_ERROR .
			    "PFP account password is empty. You must " .
			    "adjust your settings.");
			return false;
		}
		$db = new ps_DB;
		$q = "SELECT * FROM #__{vm}_orders, #__{vm}_order_payment ";
		$q .= "WHERE order_number='" . $d['order_number'] . "' ";
		$q .= "AND #__{vm}_orders.order_id=#__{vm}_order_payment.order_id";
		$db->query( $q );
		if( !$db->next_record() ) {
			$d['error'] = "Error: Order not found.";
			return false;
		}

		/* Pay Flow Pro vars to send */
		$request = array (
			'USER' => PFP_LOGIN,
			'VENDOR' => PFP_LOGIN,
			'PWD' => $transaction->passkey,
			'PARTNER' => PFP_PARTNER,
			'TENDER' => 'C',
			'TRXTYPE' => 'D',
			'ORIGID' => $db->f("order_payment_trans_id"),
			/*
			 * I don't know if the order amount can be
			 * changed through the admin interface, but
			 * if it can, we should use the new value
			 * in the the charge amount is now lower than
			 * it was originally.
			 */
			'AMT' => $db->f("order_total"),
			);

		/*
		 * Let pfpro know where to find its special certificate
		 * This won't work if Safe Mode is on.  If it is on,
		 * add SetEnv PFPRO_CERT_PATH path to apache config file.
		 */
		$certpath = 'PFPRO_CERT_PATH=';
		$certpath .= PFP_CERT_PATH;
		putenv($certpath);

		/*
		 * We are using the pfpro PHP extension.  If you don't
		 * have this it won't work.
		 */
		$response = pfpro_process($request, $host);
        
		if ($response['RESULT'] == '0') {
			/* approved */
			$d["order_payment_log"] =
			    $VM_LANG->_PHPSHOP_PAYMENT_TRANSACTION_SUCCESS
			    . ' (delayed caputre): ';
			   $d["order_payment_log"] .= $response['RESPMSG'];

			/* Record transaction ID */
			$d["order_payment_trans_id"] = $response['PNREF'];
           
			$q = "UPDATE #__{vm}_order_payment SET ";
			$q .= "order_payment_log='" .
			    $d["order_payment_log"] . "',";
			$q .= "order_payment_trans_id='" .
			    $d["order_payment_trans_id"] . "' ";
			$q .= "WHERE order_id='" . $db->f("order_id") . "' ";
			$db->query( $q );
           
			return True;
		} elseif ($response['RESULT'] == '12') {
			/* declined */
			$vmLogger->err($response['RESPMSG']);

			$d["order_payment_log"] =
			    $VM_LANG->_PHPSHOP_PAYMENT_ERROR . ': ';
			$d["order_payment_log"] .= $response['RESPMSG'];

			/* record transaction ID */
			$d["order_payment_trans_id"] = $response['PNREF'];

			return False;
		} else {
			/* Transaction Error */
			$vmLogger->err($response['RESPMSG']);

			$d["order_payment_log"] =
			    $VM_LANG->_PHPSHOP_INTERNAL_ERROR . ': ';
			$d["order_payment_log"] .= $response['RESPMSG'];

			/* record transaction ID */
			$d["order_payment_trans_id"] = $response['PNREF'];

			return False;
		}
	}

	/*
	 * name: void_authorization()
	 * description: Voids an authorization (in the case of a
	 *     cancelled order).
	 *     XXX I'm not sure, but this might work for captured cards too.
	 * parameters: $order_number, the number of the order, we're processing.
	 */
	function void_authorization(&$d) {

		global $vendor_mail, $vendor_currency, $VM_LANG, $vmLogger;
		$database = new ps_DB;
		$auth = $_SESSION['auth'];

		$vmLogger->debug("voiding credit card transaction");

		/* If a test request, use "test-payflow.verisign.com" */
		if (PFP_TEST_REQUEST == 'TRUE') {
			$host = 'test-payflow.verisign.com';
			$vmLogger->debug("Using test site: " . $host);
		} else {
			$host = 'payflow.verisign.com';
			$vmLogger->debug("Using real site: " . $host);
		}

		if (empty($d['order_number'])) {
			$d['error'] = "Error: No Order Number provided.";
			return false;
		}

		/* Get the Configuration File for PayFlow Pro */
		require_once(CLASSPATH . "payment/" . $this->classname .
		    ".cfg.php");
        
		/* Get the Transaction Key securely from the database */
		$database->query( "SELECT DECODE(payment_passkey,'" .
		    ENCODE_KEY .  "') as passkey " .
		    "FROM #__{vm}_payment_method " .
		    "WHERE payment_class='" .  $this->classname . "'" .
		    " AND shopper_group_id='" . $auth['shopper_group_id'] .
		    "'");
		$transaction = $database->record[0];
		if (empty($transaction->passkey)) {
			$vmLogger->err($VM_LANG->_PHPSHOP_PAYMENT_ERROR .
			    "PFP account password is empty. You must " .
			    "adjust your settings.");
			return false;
		}
		$db = new ps_DB;
		$q = "SELECT * FROM #__{vm}_orders, #__{vm}_order_payment ";
		$q .= "WHERE order_number='" . $d['order_number'] . "' ";
		$q .= "AND #__{vm}_orders.order_id=#__{vm}_order_payment.order_id";
		$db->query( $q );
		if( !$db->next_record() ) {
			$d['error'] = "Error: Order not found.";
			return false;
		}

		/* Pay Flow Pro vars to send */
		$request = array (
			'USER' => PFP_LOGIN,
			'VENDOR' => PFP_LOGIN,
			'PWD' => $transaction->passkey,
			'PARTNER' => PFP_PARTNER,
			'TENDER' => 'C',
			'TRXTYPE' => 'V',
			'ORIGID' => $db->f("order_payment_trans_id"),
			/*
			 * I don't know if the order amount can be
			 * changed through the admin interface, but
			 * if it can, we should use the new value
			 * in the the charge amount is now lower than
			 * it was originally.
			 */
			'AMT' => $db->f("order_total"),
			);

		/*
		 * Let pfpro know where to find its special certificate
		 * This won't work if Safe Mode is on.  If it is on,
		 * add SetEnv PFPRO_CERT_PATH path to apache config file.
		 */
		$certpath = 'PFPRO_CERT_PATH=';
		$certpath .= PFP_CERT_PATH;
		putenv($certpath);

		/*
		 * We are using the pfpro PHP extension.  If you don't
		 * have this it won't work.
		 */
		$response = pfpro_process($request, $host);
        
		if ($response['RESULT'] == '0') {
			/* approved */
			$d["order_payment_log"] =
			    $VM_LANG->_PHPSHOP_PAYMENT_TRANSACTION_SUCCESS
			    . ' (Void action): ';
			   $d["order_payment_log"] .= $response['RESPMSG'];

			/* Record transaction ID */
			$d["order_payment_trans_id"] = $response['PNREF'];
           
			$q = "UPDATE #__{vm}_order_payment SET ";
			$q .= "order_payment_log='" .
			    $d["order_payment_log"] . "',";
			$q .= "order_payment_trans_id='" .
			    $d["order_payment_trans_id"] . "' ";
			$q .= "WHERE order_id='" . $db->f("order_id") . "' ";
			$db->query( $q );
           
			return True;
		} elseif ($response['RESULT'] == '12') {
			/* declined */
			$vmLogger->err($response['RESPMSG']);

			$d["order_payment_log"] =
			    $VM_LANG->_PHPSHOP_PAYMENT_ERROR . ': ';
			$d["order_payment_log"] .= $response['RESPMSG'];

			/* record transaction ID */
			$d["order_payment_trans_id"] = $response['PNREF'];

			return False;
		} else {
			/* Transaction Error */
			$vmLogger->err($response['RESPMSG']);

			$d["order_payment_log"] =
			    $VM_LANG->_PHPSHOP_INTERNAL_ERROR . ': ';
			$d["order_payment_log"] .= $response['RESPMSG'];

			/* record transaction ID */
			$d["order_payment_trans_id"] = $response['PNREF'];

			return False;
		}
	}
}
