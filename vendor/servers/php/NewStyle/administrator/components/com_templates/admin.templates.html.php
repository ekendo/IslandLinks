<?php
/**
* @version $Id: admin.templates.html.php 85 2005-09-15 23:12:03Z eddieajau $
* @package Joomla
* @subpackage Templates
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
* @subpackage Templates
*/
class HTML_templates {
	/**
	* @param array An array of data objects
	* @param object A page navigation object
	* @param string The option
	*/
	function showTemplates( &$rows, &$pageNav, $option, $client ) {
		global $my, $mosConfig_live_site;

		if ( isset( $row->authorUrl) && $row->authorUrl != '' ) {
			$row->authorUrl = str_replace( 'http://', '', $row->authorUrl );
		}

		mosCommonHTML::loadOverlib();
		?>
		<script language="Javascript">
		<!--
		function showInfo(name) {
			var pattern = /\b \b/ig;
			name = name.replace(pattern,'_');
			name = name.toLowerCase();
			if (document.adminForm.doPreview.checked) {
				var src = '<?php echo $mosConfig_live_site . ($client == 'admin' ? '/administrator' : '');?>/templates/'+name+'/template_thumbnail.png';
				var html=name;
				html = '<br /><img border="1" src="'+src+'" name="imagelib" alt="No preview available" width="206" height="145" />';
				return overlib(html, CAPTION, name)
			} else {
				return false;
			}
		}
		-->
		</script>

		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th class="templates">
			Template Manager <small><small>[ <?php echo $client == 'admin' ? 'Administrator' : 'Site';?> ]</small></small>
			</th>
			<td align="right" nowrap="true">
			Preview Template
			</td>
			<td align="right">
			<input type="checkbox" name="doPreview" checked="checked"/>
			</td>
		</tr>
		</table>
		<table class="adminlist">
		<tr>
			<th width="5%">#</th>
			<th width="5%">&nbsp;</th>
			<th width="25%" class="title">
			Name
			</th>
			<?php
			if ( $client == 'admin' ) {
				?>
				<th width="10%">
				Default
				</th>
				<?php
			} else {
				?>
				<th width="5%">
				Default
				</th>
				<th width="5%">
				Assigned
				</th>
				<?php
			}
			?>
			<th width="20%" align="left">
			Author
			</th>
			<th width="5%" align="center">
			Version
			</th>
			<th width="10%" align="center">
			Date
			</th>
			<th width="20%" align="left">
			Author URL
			</th>
		</tr>
		<?php
		$k = 0;
		for ( $i=0, $n = count( $rows ); $i < $n; $i++ ) {
			$row = &$rows[$i];
			?>
			<tr class="<?php echo 'row'. $k; ?>">
				<td>
				<?php echo $pageNav->rowNumber( $i ); ?>
				</td>
				<td>
				<?php
				if ( $row->checked_out && $row->checked_out != $my->id ) {
					?>
					&nbsp;
					<?php
				} else {
					?>
					<input type="radio" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->directory; ?>" onClick="isChecked(this.checked);" />
					<?php
				}
				?>
				</td>
				<td>
				<a href="#info" onmouseover="showInfo('<?php echo $row->name;?>')" onmouseout="return nd();">
				<?php echo $row->name;?>
				</a>
				</td>
				<?php
				if ( $client == 'admin' ) {
					?>
					<td align="center">
					<?php
					if ( $row->published == 1 ) {
						?>
					<img src="images/tick.png" alt="Published">
						<?php
					} else {
						?>
						&nbsp;
						<?php
					}
					?>
					</td>
					<?php
				} else {
					?>
					<td align="center">
					<?php
					if ( $row->published == 1 ) {
						?>
						<img src="images/tick.png" alt="Default">
						<?php
					} else {
						?>
						&nbsp;
						<?php
					}
					?>
					</td>
					<td align="center">
					<?php
					if ( $row->assigned == 1 ) {
						?>
						<img src="images/tick.png" alt="Assigned" />
						<?php
					} else {
						?>
						&nbsp;
						<?php
					}
					?>
					</td>
					<?php
				}
				?>
				<td>
				<?php echo $row->authorEmail ? '<a href="mailto:'. $row->authorEmail .'">'. $row->author .'</a>' : $row->author; ?>
				</td>
				<td align="center">
				<?php echo $row->version; ?>
				</td>
				<td align="center">
				<?php echo $row->creationdate; ?>
				</td>
				<td>
				<a href="<?php echo substr( $row->authorUrl, 0, 7) == 'http://' ? $row->authorUrl : 'http://'.$row->authorUrl; ?>" target="_blank">
				<?php echo $row->authorUrl; ?>
				</a>
				</td>
			</tr>
			<?php
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		<input type="hidden" name="client" value="<?php echo $client;?>" />
		</form>
		<?php
	}


	/**
	* @param string Template name
	* @param string Source code
	* @param string The option
	*/
	function editTemplateSource( $template, &$content, $option, $client ) {
		global $mosConfig_absolute_path;
		$template_path =
			$mosConfig_absolute_path . ($client == 'admin' ? '/administrator':'') .
			'/templates/' . $template . '/index.php';
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table cellpadding="1" cellspacing="1" border="0" width="100%">
		<tr>
			<td width="290"><table class="adminheading"><tr><th class="templates">Template HTML Editor</th></tr></table></td>
			<td width="220">
				<span class="componentheading">index.php is :
				<b><?php echo is_writable($template_path) ? '<font color="green"> Writeable</font>' : '<font color="red"> Unwriteable</font>' ?></b>
				</span>
			</td>
<?php
			if (mosIsChmodable($template_path)) {
				if (is_writable($template_path)) {
?>
			<td>
				<input type="checkbox" id="disable_write" name="disable_write" value="1"/>
				<label for="disable_write">Make unwriteable after saving</label>
			</td>
<?php
				} else {
?>
			<td>
				<input type="checkbox" id="enable_write" name="enable_write" value="1"/>
				<label for="enable_write">Override write protection while saving</label>
			</td>
<?php
				} // if
			} // if
?>
		</tr>
		</table>
		<table class="adminform">
			<tr><th><?php echo $template_path; ?></th></tr>
			<tr><td><textarea style="width:100%;height:500px" cols="110" rows="25" name="filecontent" class="inputbox"><?php echo $content; ?></textarea></td></tr>
		</table>
		<input type="hidden" name="template" value="<?php echo $template; ?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="client" value="<?php echo $client;?>" />
		</form>
		<?php
	}


	/**
	* @param string Template name
	* @param string Source code
	* @param string The option
	*/
	function editCSSSource( $template, &$content, $option, $client ) {
		global $mosConfig_absolute_path;
		$css_path =
			$mosConfig_absolute_path . ($client == 'admin' ? '/administrator' : '')
			. '/templates/' . $template . '/css/template_css.css';
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table cellpadding="1" cellspacing="1" border="0" width="100%">
		<tr>
			<td width="280"><table class="adminheading"><tr><th class="templates">Template CSS Editor</th></tr></table></td>
			<td width="260">
				<span class="componentheading">template_css.css is :
				<b><?php echo is_writable($css_path) ? '<font color="green"> Writeable</font>' : '<font color="red"> Unwriteable</font>' ?></b>
				</span>
			</td>
<?php
			if (mosIsChmodable($css_path)) {
				if (is_writable($css_path)) {
?>
			<td>
				<input type="checkbox" id="disable_write" name="disable_write" value="1"/>
				<label for="disable_write">Make unwriteable after saving</label>
			</td>
<?php
				} else {
?>
			<td>
				<input type="checkbox" id="enable_write" name="enable_write" value="1"/>
				<label for="enable_write">Override write protection while saving</label>
			</td>
<?php
				} // if
			} // if
?>
		</tr>
		</table>
		<table class="adminform">
			<tr><th><?php echo $css_path; ?></th></tr>
			<tr><td><textarea style="width:100%;height:500px" cols="110" rows="25" name="filecontent" class="inputbox"><?php echo $content; ?></textarea></td></tr>
		</table>
		<input type="hidden" name="template" value="<?php echo $template; ?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="client" value="<?php echo $client;?>" />
		</form>
		<?php
	}


	/**
	* @param string Template name
	* @param string Menu list
	* @param string The option
	*/
	function assignTemplate( $template, &$menulist, $option ) {
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminform">
		<tr>
			<th class="left" colspan="2">
			Assign template <?php echo $template; ?> to menu items
			</th>
		</tr>
		<tr>
			<td valign="top" align="left">
			Page(s):
			</td>
			<td width="90%">
			<?php echo $menulist; ?>
			</td>
		</tr>
		</table>
		<input type="hidden" name="template" value="<?php echo $template; ?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}


	/**
	* @param array
	* @param string The option
	*/
	function editPositions( &$positions, $option ) {
		$rows = 25;
		$cols = 2;
		$n = $rows * $cols;
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th class="templates">
			Module Positions
			</th>
		</tr>
		</table>

		<table class="adminlist">
		<tr>
		<?php
		for ( $c = 0; $c < $cols; $c++ ) {
			?>
			<th width="25">
			#
			</th>
			<th align="left">
			Position
			</th>
			<th align="left">
			Description
			</th>
			<?php
		}
		?>
		</tr>
		<?php
		$i = 1;
		for ( $r = 0; $r < $rows; $r++ ) {
			?>
			<tr>
			<?php
			for ( $c = 0; $c < $cols; $c++ ) {
				?>
				<td>(<?php echo $i; ?>)</td>
				<td>
				<input type="text" name="position[<?php echo $i; ?>]" value="<?php echo @$positions[$i-1]->position; ?>" size="10" maxlength="10" />
				</td>
				<td>
				<input type="text" name="description[<?php echo $i; ?>]" value="<?php echo htmlspecialchars( @$positions[$i-1]->description ); ?>" size="50" maxlength="255" />
				</td>
				<?php
				$i++;
			}
			?>
			</tr>
			<?php
		}
		?>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}
}
?>