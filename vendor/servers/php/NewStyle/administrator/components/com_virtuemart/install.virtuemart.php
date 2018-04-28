<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
* @version $Id: install.virtuemart.php,v 1.7.2.6 2006/05/07 09:20:29 soeren_nb Exp $
* @package VirtueMart
* @subpackage core
* @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*
* http://virtuemart.net
*/

function virtuemart_is_installed() {
	global $database, $mosConfig_absolute_path, $mosConfig_dbprefix, 
		$VMVERSION, $shortversion, $myVersion, $version_info;
	$option = 'com_virtuemart';
	$installfile = dirname( __FILE__ ) . "/install.php";
	
	$database->setQuery( "SHOW TABLES LIKE '".$mosConfig_dbprefix."vm_%'" );
	$vm_tables = $database->loadObjectList();
	
	if( file_exists( $mosConfig_absolute_path.'/administrator/components/'.$option.'/classes/htmlTools.class.php' ) 
		&& count( $vm_tables)> 30 ) {
		// VirtueMart is installed! But is it an older version that needs to be updated?
		$database->setQuery( 'SELECT id, params FROM `#__components` WHERE name = \'virtuemart_version\'' );
		$database->loadObject( $old_version );
		if( $old_version && file_exists( $mosConfig_absolute_path.'/administrator/components/com_virtuemart/classes/htmlTools.class.php')) {
			$version_info = new mosParameters( $old_version->params );
			include_once( $mosConfig_absolute_path.'/administrator/components/'.$option.'/version.php' );
			$VMVERSION = new vmVersion();
			$result = version_compare( $version_info->get( 'RELEASE' ), '1.0.5' );
			// IF we need to update, version_compare has returned -1, that means that the current version is lower than 1.0.5
			if( $result == -1 ) {
				return false;
			}			
		}
		
		@unlink( $installfile );
		if( ( file_exists($installfile)) || !file_exists(dirname( __FILE__ ) . "/virtuemart.cfg.php")) {
			die('<h2>Virtuemart Installation Notice</h2>
			<p>You already have installed VirtueMart.</p>
			<p>You MUST 
			<ol>
				<li>DELETE the file <strong>'.$installfile.'</strong>,</li>
				<li>RENAME the file <strong>virtuemart.cfg-dist.php</strong> to <strong>virtuemart.cfg.php</strong></li>
			</ol>before you can use VirtueMart.
			</p>');
		}
		else {
			mosRedirect('index2.php?option=com_virtuemart');
		}
	}
	return false;
	
}
function com_install() {
	global $mosConfig_absolute_path, $mosConfig_dbprefix, $database, 
		$VMVERSION, $myVersion, $shortversion, $version_info;
	include( $mosConfig_absolute_path. "/administrator/components/com_virtuemart/version.php" );
	if( !isset( $shortversion )) {
		$shortversion = $VMVERSION->PRODUCT . " " . $VMVERSION->RELEASE . " " . $VMVERSION->DEV_STATUS. " ";
		$myVersion = $shortversion . " [".$VMVERSION->CODENAME ."] <br />" . $VMVERSION->RELDATE . " "
					. $VMVERSION->RELTIME . " " . $VMVERSION->RELTZ;
	}
	if( defined( '_RELEASE' )) {
		// Refuse to install on Mambo 4.5.0
		if( _RELEASE == '4.5' || (float)_RELEASE <= 4.50 )
			die( '<h2>VirtueMart Installation can\'t continue: Wrong Mambo version!</h2>
					<p>VirtueMart requires at least Mambo <strong>4.5.1</strong></p>
					<p>Your Version: Mambo <strong>'._RELEASE.'.0 '._DEV_STATUS.' '._DEV_LEVEL.'</strong>, Codename: '._CODENAME.'</p>' );
	}
	// Check for old mambo-phpShop Tables. When they exist,
	// offer an Upgrade
	$database->setQuery( "SHOW TABLES LIKE '".$mosConfig_dbprefix."pshop_%'" );
	$pshop_tables = $database->loadObjectList();
	
	if( !empty( $pshop_tables )) {
	  $installation = "phpshopupdate";
	}
	else {
		$database->setQuery( 'SELECT id,params FROM `#__components` WHERE name = \'virtuemart_version\'' );
		$database->loadObject( $old_version );
		
		if( $old_version && file_exists( $mosConfig_absolute_path.'/administrator/components/com_virtuemart/classes/htmlTools.class.php')) {
			$version_info = new mosParameters( $old_version->params );
			$isBefore_103 = version_compare( $version_info->get( 'RELEASE' ), '1.0.3' );
			// Version_compare returns -1, which is true for the meaning of the variable meaning
			if( $isBefore_103 == -1 ) {
				// the update from
				$installation = 'vm_update_from102_orOlder';
			}
			else {
				$installation = 'vm_update_from103_orYounger';
			}
		}
		else {
			$installation = "new";
		}
	}
	?>
	<link rel="stylesheet" href="components/com_virtuemart/install.css" type="text/css" />
	<div align="center">
		<table width="100%" border="0">
			<tr>
				<td valign="middle" align="center">
					<div id="ctr" align="center">
						<div class="install">
							<div id="stepbar">
								<div>
									<a href="http://virtuemart.net" target="_blank"><img border="0" align="right" src="components/com_virtuemart/cart.gif" alt="Cart" /></a>
									<br/>
								</div>
								<div class="clr"></div>
								<br/><br/><br/>
								<div class="step-on" >
									<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
										Please consider a small donation to help me keep up the work on this component.<br /><br />
										<input type="hidden" name="cmd" value="_xclick" />
										<input type="hidden" name="business" value="soeren_nb@yahoo.de" />
										<input type="hidden" name="item_name" value="VirtueMart Donation" />
										<input type="hidden" name="item_number" value="" />
										<input type="hidden" name="currency_code" value="EUR" />
										<input type="hidden" name="tax" value="0" />
										<input type="hidden" name="no_note" value="0" />
										<input type="hidden" name="amount" value="" />
										<input type="image" src="components/com_virtuemart/x-click-but21.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!" />
									</form>
								</div>
							</div>
							<div id="right">
								<div id="step">Welcome to <?php echo $shortversion ?>!</div>
			
								<div class="clr"></div>
								<pre><?php echo $myVersion ?></pre>
								<h1>The first step of the Installation was <font color="green">SUCCESSFUL</font></h1>
								<table>
								<?php
								if( $installation == "new" ) { ?>
									<tr>
										<td colspan="3" class="error">Let's prepare the database now (the Installation Script hasn't found existing mambo-phpShop/VirtueMart Tables, so let's do a fresh installation).</td>
									</tr>
									<tr>
										<td width="40%">Basic Installation has been finished. You can use VirtueMart in a moment after having clicked on a link below.<br/></td>
										<td width="20%">&nbsp;</td>
										<td width="40%">To fill your Shop with dummy products, and to see how things can be set up, 
												you can install some Sample Data now.
										</td>
									</tr>
									<tr>
										<td width="40%">
											<a title="Go directly to the Shop &gt;&gt;" onclick="alert('Please don\'t interrupt the next Step! \n It is essential for running VirtueMart.');" name="Button1" class="button" href="index2.php?option=com_virtuemart&install_type=newinstall">Go directly to the Shop &gt;&gt;</a>
										</td>
										<td width="20%">&nbsp;</td>
										<td width="40%">
											<a name="Button2" onclick="alert('Please don\'t interrupt the next Step! \n It is essential for running VirtueMart.');" class="button" title="Install SAMPLE DATA &gt;&gt;" href="index2.php?option=com_virtuemart&install_type=newinstall&install_sample_data=true">Install SAMPLE DATA &gt;&gt;</a>
										</td>
									</tr>
									<tr>
										<td align="center" colspan="3"><br /><br /><hr /><br /></td>
									</tr>
									<?php 
								}
								elseif( $installation == 'vm_update_from102_orOlder' || $installation == 'vm_update_from103_orYounger' ) { 
									$old_version = get_class($version_info) =='mosparameters' ? $version_info->get( 'RELEASE') : '1.0.x';
									?>
										<td colspan="3" class="error">[UPDATE MODE]<br/>The Installation script has found out that you've already installed VirtueMart <?php echo $old_version ?>, so let's update your Database.</td>
									<tr>
									</tr>
									<tr>
										<td align="left" colspan="3">
											<div align="center">
												<a title="UPDATE FROM VERSION <?php echo $old_version ?> &gt;&gt;" onclick="alert('Please don\'t interrupt the next Step! \n It is essential for updating VirtueMart.');" name="Button2" class="button" href="index2.php?option=com_virtuemart&install_type=<?php echo $installation ?>">UPDATE FROM VERSION <?php echo $old_version ?> &gt;&gt;</a>
											</div><br /><br/>
											Your version is NOT <strong><?php echo $old_version ?></strong>? Then please just delete the file <pre><?php echo dirname(__FILE__).'/install.php' ?></pre><br />
											
										</td>
									</tr>
									<?php
								}
								elseif( $installation == 'phpshopupdate' ) {  ?>
									<tr>
										<td colspan="3" class="error">[UPDATE MODE]<br/>The Installation Script has found existing mambo-phpShop Tables, so let's update your Database.</td>
									</tr>
									<tr>
										<td align="left" colspan="3">If you're upgrading from mambo-phpShop, version <strong>1.2 stable-pl3</strong> or <strong>Mambo eCommerce Edition</strong> you'll have to click on this link!<br />
											<br /><br/>
											<div align="center">
												<a title="UPDATE FROM VERSION 1.2 stable-pl3 &gt;&gt;" onclick="alert('Please don\'t interrupt the next Step! \n It is essential for updating mambo-phpShop to VirtueMart.');" name="Button2" class="button" href="index2.php?option=com_virtuemart&install_type=update12pl3">UPDATE FROM VERSION 1.2 stable-pl3 &gt;&gt;</a>
											</div>
											<div class="error">Note:</div>
											If your Version Number is between 1.1 and 1.2 stable.pl3 (e.g. <i>1.2 beta3</i>), you have to update your database before using the Step-by-Step SQL Update Scripts from the folder 
											<pre>/administrator/components/com_virtuemart/sql/other</pre> of your VirtueMart Installation. You can run these Scripts with <a href="http://mamboforge.net/projects/mosphpmyadmin/" target="_blank">phpMyAdmin for Mambo/Joomla</a>.                
										</td>
									</tr>
									<tr>
										<td align="center" colspan="3"><br /><br /><hr /><br /></td>
									</tr>
									<tr>
										<td align="center" colspan="3">
											<br /><br/>
											<div align="center">
												<a title="UPDATE FROM VERSION 1.2 RC2 &gt;&gt;" onclick="alert('Please don\'t interrupt the next Step! \n It is essential for updating mambo-phpShop to VirtueMart.');" name="Button2" class="button" href="index2.php?option=com_virtuemart&install_type=update12">UPDATE FROM VERSION 1.2 RC2 &gt;&gt;</a>
											</div>
										</td>
									</tr>
										<td align="center" colspan="3">If you're updating from version 1.1(a) you'll have to click on this link!<br /><br />
											<a name="Button2" class="button" title="UPDATE FROM VERSION 1.1(a) &gt;&gt;" onclick="alert('Please don\'t interrupt the next Step! \n It is essential for updating mambo-phpShop to VirtuMart.');" href="index2.php?option=com_virtuemart&install_type=update11">UPDATE FROM VERSION 1.1(a) &gt;&gt;<a />
										</td>
									</tr>
									<tr>
										<td align="center" colspan="3"><br /><br /><hr /><br /></td>
									</tr>
									<?php 
								}
									?>
									<tr>
										<td align="center" colspan="3">Go to <a href="http://www.virtuemart.net" target="_blank">virtuemart.net</a> for further Help</td>
									</tr>
								</table>
							</div>
							<div class="clr"></div>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<?php
}
?>