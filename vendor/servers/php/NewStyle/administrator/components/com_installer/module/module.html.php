<?php
/**
* @version $Id: module.html.php 85 2005-09-15 23:12:03Z eddieajau $
* @package Joomla
* @subpackage Installer
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
* @package Joomla
* @subpackage Installer
*/
class HTML_module {

	function showInstalledModules( &$rows, $option, &$xmlfile, &$lists ) {
		if (count($rows)) {
			?>
			<form action="index2.php" method="post" name="adminForm">
			<table class="adminheading">
			<tr>
				<th class="install">
				Installed Modules
				</th>
				<td>
				Filter:
				</td>
				<td width="right">
				<?php echo $lists['filter'];?>
				</td>
			</tr>
			<tr>
				<td colspan="3">
				Only those Modules that can be uninstalled are displayed - some Core Modules cannot be removed.
				<br /><br />
				</td>
			</tr>
			</table>

			<table class="adminlist">
			<tr>
				<th width="20%" class="title">
				Module File
				</th>
				<th width="10%" align="left">
				Client
				</th>
				<th width="10%" align="left">
				Author
				</th>
				<th width="5%" align="center">
				Version
				</th>
				<th width="10%" align="center">
				Date
				</th>
				<th width="15%" align="left">
				Author Email
				</th>
				<th width="15%" align="left">
				Author URL
				</th>
			</tr>
			<?php
			$rc = 0;
			for ($i = 0, $n = count( $rows ); $i < $n; $i++) {
				$row =& $rows[$i];
				?>
				<tr class="<?php echo "row$rc"; ?>">
					<td>
					<input type="radio" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);"><span class="bold"><?php echo $row->module; ?></span></td>
					<td>
					<?php echo $row->client_id == "0" ? 'Site' : 'Administrator'; ?>
					</td>
					<td>
					<?php echo @$row->author != "" ? $row->author : "&nbsp;"; ?>
					</td>
					<td align="center">
					<?php echo @$row->version != "" ? $row->version : "&nbsp;"; ?>
					</td>
					<td align="center">
					<?php echo @$row->creationdate != "" ? $row->creationdate : "&nbsp;"; ?>
					</td>
					<td>
					<?php echo @$row->authorEmail != "" ? $row->authorEmail : "&nbsp;"; ?>
					</td>
					<td>
					<?php echo @$row->authorUrl != "" ? "<a href=\"" .(substr( $row->authorUrl, 0, 7) == 'http://' ? $row->authorUrl : 'http://'.$row->authorUrl) ."\" target=\"_blank\">$row->authorUrl</a>" : "&nbsp;"; ?>
					</td>
				</tr>
				<?php
				$rc = $rc == 0 ? 1 : 0;
			}
		} else {
			?>
			<td class="small">
			No custom modules installed
			</td>
			<?php
		}
		?>
		</table>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="option" value="com_installer" />
		<input type="hidden" name="element" value="module" />
		</form>
		<?php
	}
}
?>