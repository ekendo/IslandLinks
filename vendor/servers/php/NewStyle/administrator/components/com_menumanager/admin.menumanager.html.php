<?php
/**
* @version $Id: admin.menumanager.html.php 85 2005-09-15 23:12:03Z eddieajau $
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
* HTML class for all menumanager component output
* @package Joomla
* @subpackage Menus
*/
class HTML_menumanager {
	/**
	* Writes a list of the menumanager items
	*/
	function show ( $option, $menus, $pageNav ) {
		global $mosConfig_live_site;
		?>
		<script language="javascript" type="text/javascript">
		function menu_listItemTask( id, task, option ) {
			var f = document.adminForm;
			cb = eval( 'f.' + id );
			if (cb) {
				cb.checked = true;
				submitbutton(task);
			}
			return false;
		}
		</script>

		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th class="menus">
			Menu Manager
			</th>
		</tr>
		</table>

		<table class="adminlist">
		<tr>
			<th width="20">#</th>
			<th width="20px">
			</th>
			<th class="title" nowrap="nowrap">
			Menu Name
			</th>
			<th width="5%" nowrap="nowrap">
			Menu Items
			</th>
			<th width="10%">
			# Published
			</th>
			<th width="15%">
			# Unpublished
			</th>
			<th width="15%">
			# Trash
			</th>
			<th width="15%">
			# Modules
			</th>
		</tr>
		<?php
		$k = 0;
		$i = 0;
		$start = 0;
		if ($pageNav->limitstart)
			$start = $pageNav->limitstart;
		$count = count($menus)-$start;
		if ($pageNav->limit)
			if ($count > $pageNav->limit)
				$count = $pageNav->limit;
		for ($m = $start; $m < $start+$count; $m++) {
			$menu = $menus[$m];
			$link 	= 'index2.php?option=com_menumanager&task=edit&hidemainmenu=1&menu='. $menu->type;
			$linkA 	= 'index2.php?option=com_menus&menutype='. $menu->type;
			?>
			<tr class="<?php echo "row". $k; ?>">
				<td align="center" width="30px">
				<?php echo $i + 1 + $pageNav->limitstart;?>
				</td>
				<td width="30px" align="center">
				<input type="radio" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $menu->type; ?>" onclick="isChecked(this.checked);" />
				</td>
				<td>
				<a href="<?php echo $link; ?>" title="Edit Menu Name">
				<?php echo $menu->type; ?>
				</a>
				</td>
				<td align="center">
				<a href="<?php echo $linkA; ?>" title="Edit Menu Items">
				<img src="<?php echo $mosConfig_live_site; ?>/includes/js/ThemeOffice/mainmenu.png" border="0"/>
				</a>
				</td>
				<td align="center">
				<?php
				echo $menu->published;
				?>
				</td>
				<td align="center">
				<?php
				echo $menu->unpublished;
				?>
				</td>
				<td align="center">
				<?php
				echo $menu->trash;
				?>
				</td>
				<td align="center">
				<?php
				echo $menu->modules;
				?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
			$i++;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>

		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php
	}


	/**
	* writes a form to take the name of the menu you would like created
	* @param option	display options for the form
	*/
	function edit ( &$row, $option ) {
		global $mosConfig_live_site;

		$new = $row->menutype ? 0 : 1;
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;

			if (pressbutton == 'savemenu') {
				if ( form.menutype.value == '' ) {
					alert( 'Please enter a menu name' );
					form.menutype.focus();
					return;
				}
				<?php
				if ( $new ) {
					?>
					if ( form.title.value == '' ) {
						alert( 'Please enter a module name for your menu' );
						form.title.focus();
						return;
					}
					<?php
				}
				?>
				submitform( 'savemenu' );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th class="menus">
			Menu Details
			</th>
		</tr>
		</table>

		<table class="adminform">
		<tr height="45px;">
			<td width="100px" align="left">
			<strong>Menu Name:</strong>
			</td>
			<td>
			<input class="inputbox" type="text" name="menutype" size="30" value="<?php echo isset( $row->menutype ) ? $row->menutype : ''; ?>" />
			<?php
			$tip = 'This is the identification name used by Joomla! to identify this menu within the code - it must be unique. Recommend that you do not have any spaces in your Menu Name';
			echo mosToolTip( $tip );
			?>
			</td>
		</tr>
		<?php
		if ( $new ) {
			?>
			<tr>
				<td width="100px" align="left" valign="top">
				<strong>Module Title:</strong>
				</td>
				<td>
				<input class="inputbox" type="text" name="title" size="30" value="<?php echo $row->title ? $row->title : '';?>" />
				<?php
				$tip = 'Title of the mod_mainmenu module required to show this Menu';
				echo mosToolTip( $tip );
				?>
				<br/><br/><br/>
				<strong>
				* A new mod_mainmenu module, with the Title you have entered above will automatically be created when you save this menu. *
				<br/><br/>
				Parameters for the module created are to be edited through the 'Modules Manager [site]': Modules -> Site Modules
				</strong>
				</td>
			</tr>
			<?php
		}
		?>
		<tr>
			<td colspan="2">
			</td>
		</tr>
		</table>
		<br /><br />

		<script language="Javascript" src="<?php echo $mosConfig_live_site; ?>/includes/js/overlib_mini.js"></script>
		<?php
		if ( $new ) {
			?>
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="iscore" value="<?php echo $row->iscore; ?>" />
			<input type="hidden" name="published" value="<?php echo $row->published; ?>" />
			<input type="hidden" name="position" value="<?php echo $row->position; ?>" />
			<input type="hidden" name="module" value="mod_mainmenu" />
			<input type="hidden" name="params" value="<?php echo $row->params; ?>" />
			<?php
		}
		?>

		<input type="hidden" name="new" value="<?php echo $new; ?>" />
		<input type="hidden" name="old_menutype" value="<?php echo $row->menutype; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="savemenu" />
		<input type="hidden" name="boxchecked" value="0" />
		</form>
		<?php
		}


	/**
	* A delete confirmation page
	* Writes list of the items that have been selected for deletion
	*/
	function showDelete( $option, $type, $items, $modules ) {
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th>
			Delete Menu: <?php echo $type;?>
			</th>
		</tr>
		</table>

		<br />
		<table class="adminform">
		<tr>
			<td width="3%"></td>
			<td align="left" valign="top" width="20%">
			<?php
			if ( $modules ) {
				?>
				<strong>Module(s) being Deleted:</strong>
				<ol>
				<?php
				foreach ( $modules as $module ) {
					?>
					<li>
					<font color="#000066">
					<strong>
					<?php echo $module->title; ?>
					</strong>
					</font>
					</li>
					<input type="hidden" name="cid[]" value="<?php echo $module->id; ?>" />
					<?php
				}
				?>
				</ol>
				<?php
			}
			?>
			</td>
			<td align="left" valign="top" width="25%">
			<strong>Menu Items being Deleted:</strong>
			<br />
			<ol>
			<?php
			foreach ( $items as $item ) {
				?>
				<li>
				<font color="#000066">
				<?php echo $item->name; ?>
				</font>
				</li>
				<input type="hidden" name="mids[]" value="<?php echo $item->id; ?>" />
				<?php
			}
			?>
			</ol>
			</td>
			<td>
			* This will <strong><font color="#FF0000">Delete</font></strong> this Menu, <br />ALL its Menu Items and the Module(s) associated with it *
			<br /><br /><br />
			<div style="border: 1px dotted gray; width: 70px; padding: 10px; margin-left: 100px;">
			<a class="toolbar" href="javascript:if (confirm('Are you sure you want to Delete this menu? \nThis will Delete the Menu, its Items and the Module(s).')){ submitbutton('deletemenu');}" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('remove','','images/delete_f2.png',1);">
			<img name="remove" src="images/delete.png" alt="Delete" border="0" align="middle" />
			&nbsp;Delete
			</a>
			</div>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		</table>
		<br /><br />

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="type" value="<?php echo $type; ?>" />
		<input type="hidden" name="boxchecked" value="1" />
		</form>
		<?php
	}


	/**
	* A copy confirmation page
	* Writes list of the items that have been selected for copy
	*/
	function showCopy( $option, $type, $items ) {
	?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			if (pressbutton == 'copymenu') {
				if ( document.adminForm.menu_name.value == '' ) {
					alert( 'Please enter a name for the copy of the Menu' );
					return;
				} else if ( document.adminForm.module_name.value == '' ) {
					alert( 'Please enter a name for the new Module' );
					return;
				} else {
					submitform( 'copymenu' );
				}
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th>
			Copy Menu
			</th>
		</tr>
		</table>

		<br />
		<table class="adminform">
		<tr>
			<td width="3%"></td>
			<td align="left" valign="top" width="30%">
			<strong>New Menu Name:</strong>
			<br />
			<input class="inputbox" type="text" name="menu_name" size="30" value="" />
			<br /><br /><br />
			<strong>New Module Name:</strong>
			<br />
			<input class="inputbox" type="text" name="module_name" size="30" value="" />
			<br /><br />
			</td>
			<td align="left" valign="top" width="25%">
			<strong>
			Menu being copied:
			</strong>
			<br />
			<font color="#000066">
			<strong>
			<?php echo $type; ?>
			</strong>
			</font>
			<br /><br />
			<strong>
			Menu Items being copied:
			</strong>
			<br />
			<ol>
			<?php
			foreach ( $items as $item ) {
				?>
				<li>
				<font color="#000066">
				<?php echo $item->name; ?>
				</font>
				</li>
				<input type="hidden" name="mids[]" value="<?php echo $item->id; ?>" />
				<?php
			}
			?>
			</ol>
			</td>
			<td valign="top">
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		</table>
		<br /><br />

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="type" value="<?php echo $type; ?>" />
		</form>
		<?php
	}
}
?>