<?php
/**
* @version $Id: contact_item_link.menu.html.php 85 2005-09-15 23:12:03Z eddieajau $
* @package Joomla
* @subpackage Menus
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

/**
* Disaply contact item link
* @package Joomla
* @subpackage Menus
*/
class contact_item_link_menu_html {

	function edit( &$menu, &$lists, &$params, $option, $contact ) {
		global $mosConfig_live_site;
		?>
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (trim(form.name.value) == ""){
				alert( "Link must have a name" );
			} else if (trim(form.contact_item_link.value) == ""){
				alert( "You must select a Contact to link to" );
			} else {
				form.link.value = "index.php?option=com_contact&task=view&contact_id=" + form.contact_item_link.value;
				form.componentid.value = form.contact_item_link.value;
				submitform( pressbutton );
			}
		}
		</script>

		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th>
			<?php echo $menu->id ? 'Edit' : 'Add';?> Menu Item :: Link - Contact Item
			</th>
		</tr>
		</table>

		<table width="100%">
		<tr valign="top">
			<td width="60%">
				<table class="adminform">
				<tr>
					<th colspan="2">
					Details
					</th>
				</tr>
				<tr>
					<td width="10%" align="right">
					Name:
					</td>
					<td width="80%">
					<input class="inputbox" type="text" name="name" size="50" maxlength="100" value="<?php echo $menu->name; ?>" />
					</td>
				</tr>
				<tr>
					<td width="10%" align="right" valign="top">
					Contact to Link:
					</td>
					<td width="80%">
					<?php echo $lists['contact']; ?>
					</td>
				</tr>
				<tr>
					<td width="10%" align="right">Url:</td>
					<td width="80%">
					<?php echo $lists['link']; ?>
					</td>
				</tr>
				<tr>
					<td width="10%" align="right" valign="top">
					On Click, Open in:
					</td>
					<td width="80%">
					<?php echo $lists['target']; ?>
					</td>
				</tr>
				<tr>
					<td align="right">
					Parent Item:
					</td>
					<td>
					<?php echo $lists['parent']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">
					Ordering:
					</td>
					<td>
					<?php echo $lists['ordering']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">
					Access Level:
					</td>
					<td>
					<?php echo $lists['access']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">Published:</td>
					<td>
					<?php echo $lists['published']; ?>
					</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				</table>
			</td>
			<td width="40%">
				<table class="adminform">
				<tr>
					<th>
					Parameters
					</th>
				</tr>
				<tr>
					<td>
					<?php echo $params->render();?>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="id" value="<?php echo $menu->id; ?>" />
		<input type="hidden" name="componentid" value="" />
		<input type="hidden" name="link" value="" />
		<input type="hidden" name="menutype" value="<?php echo $menu->menutype; ?>" />
		<input type="hidden" name="type" value="<?php echo $menu->type; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<script language="Javascript" src="<?php echo $mosConfig_live_site;?>/includes/js/overlib_mini.js"></script>
		<?php
	}

}
?>