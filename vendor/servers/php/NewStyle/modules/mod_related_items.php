<?php
/**
* @version $Id: mod_related_items.php 85 2005-09-15 23:12:03Z eddieajau $
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

$option = mosGetParam( $_REQUEST, 'option' );
$task 	= mosGetParam( $_REQUEST, 'task' );
$id 	= intval( mosGetParam( $_REQUEST, 'id', null ) );

if ($option == 'com_content' && $task == 'view' && $id) {
	// select the meta keywords from the item
	$query = "SELECT metakey"
	. "\n FROM #__content"
	. "\n WHERE id = $id"
	;
	$database->setQuery( $query );
	if ($metakey = trim( $database->loadResult() )) {
		// explode the meta keys on a comma
		$keys = explode( ',', $metakey );
		$likes = array();

		// assemble any non-blank word(s)
		foreach ($keys as $key) {
			$key = trim( $key );
			if ($key) {
				$likes[] = $database->getEscaped( $key );
			}
		}

		if (count( $likes )) {
			// select other items based on the metakey field 'like' the keys found
			$query = "SELECT id, title"
			. "\n FROM #__content"
			. "\n WHERE id <> $id"
			. "\n AND state = 1"
			. "\n AND access <= $my->gid"
			. "\n AND ( metakey LIKE '%" . implode( "%' OR metakey LIKE '%", $likes ) ."%' )"
			;
			$database->setQuery( $query );
			if ( $related = $database->loadObjectList() ) {
				?>
				<ul>
				<?php
				foreach ($related as $item) {
					if ($option="com_content" && $task="view") {
						$Itemid = $mainframe->getItemid($item->id);
					}
					$href = sefRelToAbs( "index.php?option=com_content&task=view&id=$item->id&Itemid=$Itemid" );
					echo '<li><a href="'. $href .'">'. $item->title .'</a></li>';
					?>
					<li>
						<a href="<?php echo $href; ?>">
							<?php echo $item->title; ?></a>
					</li>
					<?php
				}
				?>
				</ul>
				<?php
			}
		}
	}
}
?>