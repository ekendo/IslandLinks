<?php
/**
* @version $Id: contact.html.php 85 2005-09-15 23:12:03Z eddieajau $
* @package Joomla
* @subpackage Contact
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
* @subpackage Contact
*/
class HTML_contact {


	function displaylist( &$categories, &$rows, $catid, $currentcat=NULL, &$params, $tabclass ) {
		global $Itemid, $mosConfig_live_site, $hide_js;

		if ( $params->get( 'page_title' ) ) {
			?>
	<div class="componentheading<?php echo $params->get( 'pageclass_sfx' ); ?>">
		<?php echo $currentcat->header; ?>
	</div>
			<?php
		}
		?>
	<form action="index.php" method="post" name="adminForm">

		<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $params->get( 'pageclass_sfx' ); ?>">
		<tr>
			<td width="60%" valign="top" class="contentdescription<?php echo $params->get( 'pageclass_sfx' ); ?>" colspan="2">
			<?php
			// show image
			if ( $currentcat->img ) {
				?>
				<img src="<?php echo $currentcat->img; ?>" align="<?php echo $currentcat->align; ?>" hspace="6" alt="<?php echo _WEBLINKS_TITLE; ?>" />
				<?php
			}
			echo $currentcat->descrip;
			?>
			</td>
		</tr>
		<tr>
			<td>
			<?php
			if ( count( $rows ) ) {
				HTML_contact::showTable( $params, $rows, $catid, $tabclass );
			}
			?>
			</td>
		</tr>
		<tr>
			<td>&nbsp;

			</td>
		</tr>
		<tr>
			<td>
			<?php
			// Displays listing of Categories
			if ( ( $params->get( 'type' ) == 'category' ) && $params->get( 'other_cat' ) ) {
				HTML_contact::showCategories( $params, $categories, $catid );
			} else if ( ( $params->get( 'type' ) == 'section' ) && $params->get( 'other_cat_section' ) ) {
				HTML_contact::showCategories( $params, $categories, $catid );
			}
			?>
			</td>
		</tr>
		</table>
		</form>
		<?php
		// displays back button
		mosHTML::BackButton ( $params, $hide_js );
	}

	/**
	* Display Table of items
	*/
	function showTable( &$params, &$rows, $catid, $tabclass ) {
		global $mosConfig_live_site, $Itemid;
		?>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
		<?php
		if ( $params->get( 'headings' ) ) {
			?>
			<tr>
				<td height="20" class="sectiontableheader<?php echo $params->get( 'pageclass_sfx' ); ?>">
					<?php echo _CONTACT_HEADER_NAME; ?>
				</td>
				<?php
				if ( $params->get( 'position' ) ) {
					?>
					<td height="20" class="sectiontableheader<?php echo $params->get( 'pageclass_sfx' ); ?>">
						<?php echo _CONTACT_HEADER_POS; ?>
					</td>
					<?php
				}
				?>
				<?php
				if ( $params->get( 'email' ) ) {
					?>
					<td height="20" class="sectiontableheader<?php echo $params->get( 'pageclass_sfx' ); ?>">
						<?php echo _CONTACT_HEADER_EMAIL; ?>
					</td>
					<?php
				}
				?>
				<?php
				if ( $params->get( 'telephone' ) ) {
					?>
					<td height="20" class="sectiontableheader<?php echo $params->get( 'pageclass_sfx' ); ?>">
						<?php echo _CONTACT_HEADER_PHONE; ?>
					</td>
					<?php
				}
				?>
				<?php
				if ( $params->get( 'fax' ) ) {
					?>
					<td height="20" class="sectiontableheader<?php echo $params->get( 'pageclass_sfx' ); ?>">
						<?php echo _CONTACT_HEADER_FAX; ?>
					</td>
					<?php
				}
				?>
				<td width="100%"></td>
			</tr>
			<?php
		}

		$k = 0;
		foreach ($rows as $row) {
			$link = 'index.php?option=com_contact&amp;task=view&amp;contact_id='. $row->id .'&amp;Itemid='. $Itemid;
			?>
			<tr>
				<td width="25%" height="20" class="<?php echo $tabclass[$k]; ?>">
					<a href="<?php echo sefRelToAbs( $link ); ?>" class="category<?php echo $params->get( 'pageclass_sfx' ); ?>">
						<?php echo $row->name; ?></a>
				</td>
				<?php
				if ( $params->get( 'position' ) ) {
					?>
					<td width="25%" class="<?php echo $tabclass[$k]; ?>">
						<?php echo $row->con_position; ?>
					</td>
					<?php
				}
				?>
				<?php
				if ( $params->get( 'email' ) ) {
					if ( $row->email_to ) {
						$row->email_to = mosHTML::emailCloaking( $row->email_to, 1 );
					}
					?>
					<td width="20%" class="<?php echo $tabclass[$k]; ?>">
						<?php echo $row->email_to; ?>
					</td>
					<?php
				}
				?>
				<?php
				if ( $params->get( 'telephone' ) ) {
					?>
					<td width="15%" class="<?php echo $tabclass[$k]; ?>">
						<?php echo $row->telephone; ?>
					</td>
					<?php
				}
				?>
				<?php
				if ( $params->get( 'fax' ) ) {
					?>
					<td width="15%" class="<?php echo $tabclass[$k]; ?>">
						<?php echo $row->fax; ?>
					</td>
					<?php
				}
				?>
				<td width="100%"></td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php
	}

	/**
	* Display links to categories
	*/
	function showCategories( &$params, &$categories, $catid ) {
		global $mosConfig_live_site, $Itemid;
		?>
		<ul>
		<?php
		foreach ( $categories as $cat ) {
			if ( $catid == $cat->catid ) {
				?>
				<li>
					<b>
					<?php echo $cat->title;?>
					</b>
					&nbsp;
					<span class="small<?php echo $params->get( 'pageclass_sfx' ); ?>">
					(<?php echo $cat->numlinks;?>)
					</span>
				</li>
				<?php
			} else {
				$link = 'index.php?option=com_contact&amp;catid='. $cat->catid .'&amp;Itemid='. $Itemid;
				?>
				<li>
					<a href="<?php echo sefRelToAbs( $link ); ?>" class="category<?php echo $params->get( 'pageclass_sfx' ); ?>">
						<?php echo $cat->title;?></a>
					<?php
					if ( $params->get( 'cat_items' ) ) {
						?>
						&nbsp;
						<span class="small<?php echo $params->get( 'pageclass_sfx' ); ?>">
							(<?php echo $cat->numlinks;?>)
						</span>
						<?php
					}
					?>
					<?php
					// Writes Category Description
					if ( $params->get( 'cat_description' ) ) {
						echo '<br />';
						echo $cat->description;
					}
					?>
				</li>
				<?php
			}
		}
		?>
		</ul>
		<?php
	}


	function viewcontact( &$contact, &$params, $count, &$list, &$menu_params ) {

		global $mosConfig_live_site;
		global $mainframe, $Itemid;
		$template = $mainframe->getTemplate();
		$sitename = $mainframe->getCfg( 'sitename' );
		$hide_js = mosGetParam($_REQUEST,'hide_js', 0 );
		?>
		<script language="JavaScript" type="text/javascript">
		<!--
		function validate(){
			if ( ( document.emailForm.text.value == "" ) || ( document.emailForm.email.value.search("@") == -1 ) || ( document.emailForm.email.value.search("[.*]" ) == -1 ) ) {
				alert( "<?php echo _CONTACT_FORM_NC; ?>" );
			} else {
			document.emailForm.action = "<?php echo sefRelToAbs("index.php?option=com_contact&Itemid=$Itemid"); ?>"
			document.emailForm.submit();
			}
		}
		//-->
		</script>
		<script type="text/javascript">
		<!--
		function ViewCrossReference( selSelectObject ){
			var links = new Array();
			<?php
			$n = count( $list );
			for ($i = 0; $i < $n; $i++) {
				echo "\nlinks[".$list[$i]->value."]='"
					. sefRelToAbs( 'index.php?option=com_contact&task=view&contact_id='. $list[$i]->value .'&Itemid='. $Itemid )
					. "';";
			}
			?>

			var sel = selSelectObject.options[selSelectObject.selectedIndex].value
			if (sel != "") {
				location.href = links[sel];
			}
		}
		//-->
		</script>
		<?php
		// For the pop window opened for print preview
		if ( $params->get( 'popup' ) ) {
			?>
			<title><?php echo $sitename ." :: ". $contact->name; ?></title>
			<link rel="stylesheet" href="<?php echo $mosConfig_live_site ."/templates/". $template ."/css/template_css.css";?>" type="text/css" />
			<?php
		}
		if ( $menu_params->get( 'page_title' ) ) {
			?>
			<div class="componentheading<?php echo $menu_params->get( 'pageclass_sfx' ); ?>">
				<?php echo $menu_params->get( 'header' ); ?>
			</div>
			<?php
		}
		?>

		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="contentpane<?php echo $params->get( 'pageclass_sfx' ); ?>">
		<?php
		// displays Page Title
		HTML_contact::_writePageTitle( $params );

		// displays Contact Select box
		HTML_contact::_writeSelectContact( $contact, $params, $count );

		// displays Name & Positione
		HTML_contact::_writeContactName( $contact, $params, $hide_js );
		?>
		<tr>
			<td>
				<table border="0" width="100%">
				<tr>
					<td></td>
					<td rowspan="2" align="right" valign="top">
					<?php
					// displays Image
					HTML_contact::_writeImage( $contact, $params );
					?>
					</td>
				</tr>
				<tr>
					<td>
					<?php
					// displays Address
					HTML_contact::_writeContactAddress( $contact, $params );

					// displays Email & Telephone
					HTML_contact::_writeContactContact( $contact, $params );

					// displays Misc Info
					HTML_contact::_writeContactMisc( $contact, $params );
					?>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		<?php
		// displays Email Form
		HTML_contact::_writeVcard( $contact, $params );
		// displays Email Form
		HTML_contact::_writeEmailForm( $contact, $params, $sitename );
		?>
		</table>
		<?php
		// display Close button in pop-up window
		mosHTML::CloseButton ( $params, $hide_js );

		// displays back button
		mosHTML::BackButton ( $params, $hide_js );
	}


	/**
	* Writes Page Title
	*/
	function _writePageTitle( &$params ) {
		if ( $params->get( 'page_title' )  && !$params->get( 'popup' ) ) {
			?>
			<tr>
				<td class="componentheading<?php echo $params->get( 'pageclass_sfx' ); ?>" colspan="2">
					<?php echo $params->get( 'header' ); ?>
				</td>
			</tr>
			<?php
		}
	}

	/**
	* Writes Dropdown box to select contact
	*/
	function _writeSelectContact( &$contact, &$params, $count ) {
		if ( ( $count > 1 )  && !$params->get( 'popup' ) && $params->get( 'drop_down' ) ) {
			global $Itemid;
			?>
			<tr>
				<td colspan="2" align="center">
				<br />
				<form action="<?php echo sefRelToAbs( 'index.php?option=com_contact&amp;Itemid='. $Itemid ); ?>" method="post" name="selectForm" target="_top" id="selectForm">
					<?php echo (_CONTACT_SEL); ?>
					<br />
					<?php echo $contact->select; ?>
				</form>
				</td>
			</tr>
			<?php
		}
	}

	/**
	* Writes Name & Position
	*/
	function _writeContactName( &$contact, &$params ) {
		global $mosConfig_live_site, $Itemid, $hide_js;
		global $mosConfig_absolute_path, $cur_template;
		if ( $contact->name ||  $contact->con_position ) {
			if ( $contact->name && $params->get( 'name' ) ) {
				?>
				<tr>
					<td width="100%" class="contentheading<?php echo $params->get( 'pageclass_sfx' ); ?>">
					<?php
					echo $contact->name;
					?>
					</td>
					<?php
					// displays Print Icon
					$print_link = $mosConfig_live_site. '/index2.php?option=com_contact&amp;task=view&contact_id='. $contact->id .'&amp;Itemid='. $Itemid .'&amp;pop=1';
					mosHTML::PrintIcon( $contact, $params, $hide_js, $print_link );
					?>
				</tr>
				<?php
			}
			if ( $contact->con_position && $params->get( 'position' ) ) {
				?>
				<tr>
					<td colspan="2">
					<?php
					echo $contact->con_position;
					?>
					<br /><br />
					</td>
				</tr>
				<?php
			}
		}
	}

	/*
	* Writes Image
	*/
	function _writeImage( &$contact, &$params ) {
		global $mosConfig_live_site;
		if ( $contact->image && $params->get( 'image' ) ) {
			?>
			<div style="float: right;">
			<img src="<?php echo $mosConfig_live_site;?>/images/stories/<?php echo $contact->image; ?>" align="middle" alt="Contact" />
			</div>
			<?php
		}
	}

	/**
	* Writes Address
	*/
	function _writeContactAddress( &$contact, &$params ) {
		if ( ( $params->get( 'address_check' ) > 0 ) &&  ( $contact->address || $contact->suburb  || $contact->state || $contact->country || $contact->postcode ) ) {
			global $mosConfig_live_site;
			?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<?php
			if ( $params->get( 'address_check' ) > 0 ) {
				?>
				<tr>
					<td rowspan="6" valign="top" width="<?php echo $params->get( 'column_width' ); ?>" align="left">
					<?php
					echo $params->get( 'marker_address' );
					?>
					</td>
				</tr>
				<?php
			}
			?>
			<?php
			if ( $contact->address && $params->get( 'street_address' ) ) {
				?>
				<tr>
					<td valign="top">
					<?php
					echo $contact->address;
					?>
					</td>
				</tr>
				<?php
			}
			if ( $contact->suburb && $params->get( 'suburb' ) ) {
				?>
				<tr>
					<td valign="top">
					<?php
					echo $contact->suburb;
					?>
					</td>
				</tr>
				<?php
			}
			if ( $contact->state && $params->get( 'state' ) ) {
				?>
				<tr>
					<td valign="top">
					<?php
					echo $contact->state;
					?>
					</td>
				</tr>
				<?php
			}
			if ( $contact->country && $params->get( 'country' ) ) {
				?>
				<tr>
					<td valign="top">
					<?php
					echo $contact->country;
					?>
					</td>
				</tr>
				<?php
			}
			if ( $contact->postcode && $params->get( 'postcode' ) ) {
				?>
				<tr>
					<td valign="top">
					<?php
					echo $contact->postcode;
					?>
					</td>
				</tr>
				<?php
			}
			?>
			</table>
			<br />
			<?php
		}
	}

	/**
	* Writes Contact Info
	*/
	function _writeContactContact( &$contact, &$params ) {
		if ( $contact->email_to || $contact->telephone  || $contact->fax ) {
			global $mosConfig_live_site;
			?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<?php
			if ( $contact->email_to && $params->get( 'email' ) ) {
				?>
				<tr>
					<td width="<?php echo $params->get( 'column_width' ); ?>" align="left">
					<?php
					echo $params->get( 'marker_email' );
					?>
					</td>
					<td>
					<?php
					echo $contact->email;
					?>
					</td>
				</tr>
				<?php
			}
			if ( $contact->telephone && $params->get( 'telephone' ) ) {
				?>
				<tr>
					<td width="<?php echo $params->get( 'column_width' ); ?>" align="left">
					<?php
					echo $params->get( 'marker_telephone' );
					?>
					</td>
					<td>
					<?php
					echo $contact->telephone;
					?>
					</td>
				</tr>
				<?php
			}
			if ( $contact->fax && $params->get( 'fax' ) ) {
				?>
				<tr>
					<td width="<?php echo $params->get( 'column_width' ); ?>" align="left">
					<?php
					echo $params->get( 'marker_fax' );
					?>
					</td>
					<td>
					<?php
					echo $contact->fax;
					?>
					</td>
				</tr>
				<?php
			}
			?>
			</table>
			<br />
			<?php
		}
	}

	/**
	* Writes Misc Info
	*/
	function _writeContactMisc( &$contact, &$params ) {
		if ( $contact->misc && $params->get( 'misc' ) ) {
			global $mosConfig_live_site;
			?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="<?php echo $params->get( 'column_width' ); ?>" valign="top" align="left">
				<?php
				echo $params->get( 'marker_misc' );
				?>
				</td>
				<td>
				<?php
				echo $contact->misc;
				?>
				</td>
			</tr>
			</table>
			<br />
			<?php
		}
	}

	/**
	* Writes Email form
	*/
	function _writeVcard( &$contact, &$params ) {
		if ( $params->get( 'vcard' ) ) {
			?>
			<tr>
				<td colspan="2">
				<?php echo(_CONTACT_DOWNLOAD_AS);?>
				<a href="index2.php?option=com_contact&task=vcard&contact_id=<?php echo $contact->id; ?>&no_html=1">
				<?php echo(_VCARD);?>
				</a>
				</td>
			</tr>
			<?php
		}
	}

	/**
	* Writes Email form
	*/
	function _writeEmailForm( &$contact, &$params, $sitename ) {
		if ( $contact->email_to && !$params->get( 'popup' ) && $params->get( 'email_form' ) ) {
			?>
			<tr>
				<td colspan="2">
				<br />
				<?php echo $params->get( 'email_description' ) ?>
				<br /><br />
				<form action="<?php echo sefRelToAbs( 'index.php?option=com_contact&amp;Itemid='. $contact->id ); ?>" method="post" name="emailForm" target="_top" id="emailForm">
				<div class="contact_email<?php echo $params->get( 'pageclass_sfx' ); ?>">
					<label for="contact_name">
						<?php echo(_NAME_PROMPT);?>
					</label>
					<br />
					<input type="text" name="name" id="contact_name" size="30" class="inputbox" value="" />
					<br />
					<label for="contact_email">
						<?php echo(_EMAIL_PROMPT);?>
					</label>
					<br />
					<input type="text" name="email" id="contact_email" size="30" class="inputbox" value="" />
					<br />
					<label for="contact_subject">
						<?php echo(_SUBJECT_PROMPT);?>
					</label>
					<br />
					<input type="text" name="subject" id="contact_subject" size="30" class="inputbox" value="" />
					<br /><br />
					<label for="contact_text">
						<?php echo(_MESSAGE_PROMPT);?>
					</label>
					<br />
					<textarea cols="50" rows="10" name="text" id="contact_text" class="inputbox"></textarea>
					<?php
					if ( $params->get( 'email_copy' ) ) {
						?>
						<br />
							<input type="checkbox" name="email_copy" id="contact_email_copy" value="1"  />
							<label for="contact_email_copy">
								<?php echo(_EMAIL_A_COPY); ?>
							</label>
						<?php
					}
					?>
					<br />
					<br />
					<input type="button" name="send" value="<?php echo(_SEND_BUTTON); ?>" class="button" onclick="validate()" />
				</div>
				<input type="hidden" name="option" value="com_contact" />
				<input type="hidden" name="con_id" value="<?php echo $contact->id; ?>" />
				<input type="hidden" name="sitename" value="<?php echo $sitename; ?>" />
				<input type="hidden" name="op" value="sendmail" />
				</form>
				<br />
				</td>
			</tr>
			<?php
		}
	}


	function nocontact( &$params ) {
		?>
		<br />
		<br />
			<?php echo _CONTACT_NONE;?>
		<br />
		<br />
		<?php
		// displays back button
		mosHTML::BackButton ( $params );
	}
}
?>