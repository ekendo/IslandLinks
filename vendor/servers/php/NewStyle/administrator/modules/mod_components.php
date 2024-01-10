<?php
/**
* @version $Id: mod_components.php 85 2005-09-15 23:12:03Z eddieajau $
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

// cache some acl checks
$canConfig = $acl->acl_check( 'administration', 'config', 'users', $my->usertype );

$manageTemplates 	= $acl->acl_check( 'administration', 'manage', 'users', $my->usertype, 'components', 'com_templates' );
$manageLanguages 	= $acl->acl_check( 'administration', 'manage', 'users', $my->usertype, 'components', 'com_languages' );
$installModules 	= $acl->acl_check( 'administration', 'install', 'users', $my->usertype, 'modules', 'all' );
$editAllModules 	= $acl->acl_check( 'administration', 'edit', 'users', $my->usertype, 'modules', 'all' );
$installComponents 	= $acl->acl_check( 'administration', 'install', 'users', $my->usertype, 'components', 'all' );
$editAllComponents 	= $acl->acl_check( 'administration', 'edit', 'users', $my->usertype, 'components', 'all' );
$canMassMail 		= $acl->acl_check( 'administration', 'manage', 'users', $my->usertype, 'components', 'com_massmail' );
$canManageUsers 	= $acl->acl_check( 'administration', 'manage', 'users', $my->usertype, 'components', 'com_users' );

$query = "SELECT *"
. "\n FROM #__components"
. "\n ORDER BY ordering, name"
;
$database->setQuery( $query );
$comps = $database->loadObjectList();	// component list

$subs = array();	// sub menus

// first pass to collect sub-menu items
foreach ($comps as $row) {
	if ($row->parent) {
		if (!array_key_exists( $row->parent, $subs )) {
			$subs[$row->parent] = array();
		} // if
		$subs[$row->parent][] = $row;
	} // if
} // foreach
?>
<table class="adminlist">
<tr>
	<th class="title">
	   Components
	</th>
</tr>
<tr>
	<td>
		<table width="100%" class="adminlist">
		<?php
		$topLevelLimit = 100;
		$i = 0;
		foreach ($comps as $row) {

			if ( $editAllComponents | $acl->acl_check( 'administration', 'edit', 'users', $my->usertype, 'components', $row->option ) ) {

				if ($row->parent == 0 && (trim( $row->admin_menu_link ) || array_key_exists( $row->id, $subs ))) {

					if ($i >= $topLevelLimit) {
						if ($i == $topLevelLimit) {

							echo "Error!<br />";
							$i = 1000;
						} // if
					} else {
						if ($i < $topLevelLimit ) {
							$i++;
							$name = htmlspecialchars( $row->name, ENT_QUOTES );
							// $alt = htmlspecialchars( $row->admin_menu_alt, ENT_QUOTES );
							if ($row->admin_menu_link) {
								?>
								<tr>
									<td>
										<?php
										echo '<a href="index2.php?'.htmlspecialchars($row->admin_menu_link,ENT_QUOTES).'"><strong>'.$name.'</strong></a><br/>';
										?>
									</td>
								</tr>
								<?php
							} else {
								?>
								<tr>
									<td>
										<strong>
										<?php echo $name; ?>
										</strong>
										<br/>
									</td>
								</tr>
								<?php
							} // if else
							if (array_key_exists( $row->id, $subs )) {
								foreach ($subs[$row->id] as $sub) {//print_r($row);
									?>
									<tr>
										<td>
											<ul style="padding: 0px 0px 0px 20px; margin: 0px;">
												<?php
					   							$name = htmlspecialchars( $sub->name );
												// $alt = htmlspecialchars( $sub->admin_menu_alt );
												// $link = $sub->admin_menu_link ? "" : "null";
												// $img = $sub->admin_menu_img ? "<img src=\"../includes/$sub->admin_menu_img\" />" : '';
												if ($sub->admin_menu_link) {
													?>
													<li>
														<?php echo '<a href="index2.php?'.htmlspecialchars($sub->admin_menu_link, ENT_QUOTES).'">'.$name.'</a><br/>'; ?>
													</li>
													<?php
												} else {
													?>
													<li>
														<?php echo $name; ?>
														<br/>
													</li>
													<?php
												} // if else
												?>
					   						</ul>
										</td>
									</tr>
									<?php
								} // foreach
							} // if
						} // if
					} // if else
				} // if
			} // if
		} // foreach
		?>
		</table>
	</td>
</tr>
<tr>
	<th>
	</th>
</tr>
</table>