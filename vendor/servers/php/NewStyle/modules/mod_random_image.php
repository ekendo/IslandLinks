<?php
/**
* @version $Id: mod_random_image.php 85 2005-09-15 23:12:03Z eddieajau $
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

global $mosConfig_absolute_path, $mosConfig_live_site;

$type 			= $params->get( 'type', 'jpg' );
$folder 		= $params->get( 'folder' );
$link 			= $params->get( 'link' );
$width 			= $params->get( 'width' );
$height 		= $params->get( 'height' );
$abspath_folder = $mosConfig_absolute_path .'/'. $folder;
$the_array 		= array();
$the_image 		= array();

if (is_dir($abspath_folder)) {
	if ($handle = opendir($abspath_folder)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != '.' && $file != '..' && $file != 'CVS' && $file != 'index.html' ) {
				$the_array[] = $file;
			}
		}
	}
	closedir($handle);

	foreach ($the_array as $img) {
		if (!is_dir($abspath_folder .'/'. $img)) {
			if (eregi($type, $img)) {
				$the_image[] = $img;
			}
		}
	}

	if (!$the_image) {
		echo 'No images';
	} else {

  	$i = count($the_image);
  	$random = mt_rand(0, $i - 1);
  	$image_name = $the_image[$random];

  	$i = $abspath_folder . '/'. $image_name;
  	$size = getimagesize ($i);

  	if ($width == '') {
  		$width = 100;
  	}
  	if ($height == '') {
  		$coeff = $size[0]/$size[1];
  		$height = (int) ($width/$coeff);
  	}

  	$image = $mosConfig_live_site .'/'. $folder .'/'. $image_name;

	if ($link) {
  		echo '<a href="'. $link .'" target="_self">';
  	}

	}
  	?>
 	<div align="center">
 	<?php
  	if ($link) {
  		?>
  		<a href="<?php echo $link; ?>" target="_self">
  		<?php
  	}
  	?>
 	<img src="<?php echo $image; ?>" border="0" width="<?php echo $width; ?>" height="<?php echo $height; ?>" alt="<?php echo $image_name; ?>" /><br />
 	<?php
  	if ($link) {
  		?>
  		</a>
  		<?php
  	}
  	?>
 	</div>
  	<?php
}
?>