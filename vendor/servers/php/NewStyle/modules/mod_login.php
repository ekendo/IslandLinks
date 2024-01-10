<?php
/**
* @version $Id: mod_login.php 85 2005-09-15 23:12:03Z eddieajau $
* @package Joomla
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

$return = mosGetParam( $_SERVER, 'REQUEST_URI', null );
// converts & to &amp; for xtml compliance
$return = str_replace( '&', '&amp;', $return );

$registration_enabled 	= $mainframe->getCfg( 'allowUserRegistration' );
$message_login 			= $params->def( 'login_message', 0 );
$message_logout 		= $params->def( 'logout_message', 0 );
$pretext 				= $params->get( 'pretext' );
$posttext 				= $params->get( 'posttext' );
$login 					= $params->def( 'login', $return );
$logout 				= $params->def( 'logout', $return );
$name 					= $params->def( 'name', 1 );
$greeting 				= $params->def( 'greeting', 1 );

if ( $name ) {
	$query = "SELECT name"
	. "\n FROM #__users"
	. "\n WHERE id = $my->id"
	;
	$database->setQuery( $query );
	$name = $database->loadResult();
} else {
	$name = $my->username;
}

if ( $my->id ) {
	?>
	<form action="<?php echo sefRelToAbs( 'index.php?option=logout' ); ?>" method="post" name="login">
	<?php
	if ( $greeting ) {
		echo _HI;
		echo $name;
	}
	?>
	<br />
	<div align="center">
		<input type="submit" name="Submit" class="button" value="<?php echo _BUTTON_LOGOUT; ?>" />
	</div>

	<input type="hidden" name="op2" value="logout" />
	<input type="hidden" name="lang" value="<?php echo $mosConfig_lang; ?>" />
	<input type="hidden" name="return" value="<?php echo sefRelToAbs( $logout ); ?>" />
	<input type="hidden" name="message" value="<?php echo $message_logout; ?>" />
	</form>
	<?php
} else {
	?>
	<form action="<?php echo sefRelToAbs( 'index.php' ); ?>" method="post" name="login" >
	<?php
	echo $pretext;
	?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td>
			<label for="mod_login_username">
				<?php echo _USERNAME; ?>
			</label>
			<br />
			<input name="username" id="mod_login_username" type="text" class="inputbox" alt="username" size="10" />
			<br />
			<label for="mod_login_password">
				<?php echo _PASSWORD; ?>
			</label>
			<br />
			<input type="password" id="mod_login_password" name="passwd" class="inputbox" size="10" alt="password" />
			<br />
			<input type="checkbox" name="remember" id="mod_login_remember" class="inputbox" value="yes" alt="Remember Me" />
			<label for="mod_login_remember">
				<?php echo _REMEMBER_ME; ?>
			</label>
			<br />
			<input type="hidden" name="option" value="login" />
			<input type="submit" name="Submit" class="button" value="<?php echo _BUTTON_LOGIN; ?>" />
		</td>
	</tr>
	<tr>
		<td>
		<a href="<?php echo sefRelToAbs( 'index.php?option=com_registration&amp;task=lostPassword' ); ?>">
			<?php echo _LOST_PASSWORD; ?></a>
		</td>
	</tr>
	<?php
	if ( $registration_enabled ) {
		?>
		<tr>
			<td>
				<?php echo _NO_ACCOUNT; ?>
				<a href="<?php echo sefRelToAbs( 'index.php?option=com_registration&amp;task=register' ); ?>">
					<?php echo _CREATE_ACCOUNT; ?></a>
			</td>
		</tr>
		<?php
	}
	?>
	</table>
	<?php
	echo $posttext;
	?>

	<input type="hidden" name="op2" value="login" />
	<input type="hidden" name="lang" value="<?php echo $mosConfig_lang; ?>" />
	<input type="hidden" name="return" value="<?php echo sefRelToAbs( $login ); ?>" />
	<input type="hidden" name="message" value="<?php echo $message_login; ?>" />
	</form>
	<?php
}
?>