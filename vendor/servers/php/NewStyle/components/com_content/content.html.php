<?php
/**
* @version $Id: content.html.php 89 2005-09-16 02:49:20Z stingrey $
* @package Joomla
* @subpackage Content
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

require_once( $GLOBALS['mosConfig_absolute_path'] . '/includes/HTML_toolbar.php' );

/**
* Utility class for writing the HTML for content
* @package Joomla
* @subpackage Content
*/
class HTML_content {
	/**
	* Draws a Content List
	* Used by Content Category & Content Section
	*/
	function showContentList( $title, &$items, &$access, $id=0, $sectionid=NULL, $gid, &$params, &$pageNav, $other_categories, &$lists ) {
		global $Itemid, $mosConfig_live_site;

		if ( $sectionid ) {
			$id = $sectionid;
		}

		if ( strtolower(get_class( $title )) == 'mossection' ) {
			$catid = 0;
		} else {
			$catid = $title->id;
		}

		if ( $params->get( 'page_title' ) ) {
			?>
			<div class="componentheading<?php echo $params->get( 'pageclass_sfx' ); ?>">
			<?php echo $title->name; ?>
			</div>
			<?php
		}
		?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" class="contentpane<?php echo $params->get( 'pageclass_sfx' ); ?>">
		<tr>
			<td width="60%" valign="top" class="contentdescription<?php echo $params->get( 'pageclass_sfx' ); ?>" colspan="2">
			<?php
			if ( $title->image ) {
				$link = $mosConfig_live_site .'/images/stories/'. $title->image;
				?>
				<img src="<?php echo $link;?>" align="<?php echo $title->image_position;?>" hspace="6" alt="<?php echo $title->image;?>" />
				<?php
			}
			echo $title->description;
			?>
			</td>
		</tr>
		<tr>
			<td>
			<?php
			// Displays the Table of Items in Category View
			if ( $items ) {
				HTML_content::showTable( $params, $items, $gid, $catid, $id, $pageNav, $access, $sectionid, $lists );
			} else if ( $catid ) {
				?>
				<br />
				<?php echo _EMPTY_CATEGORY; ?>
				<br /><br />
				<?php
			}
			?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			<?php
			// Displays listing of Categories
			if ( ( ( count( $other_categories ) > 1 ) || ( count( $other_categories ) < 2 && count( $items ) < 1 ) ) ) {
				if ( ( $params->get( 'type' ) == 'category' ) && $params->get( 'other_cat' ) ) {
					HTML_content::showCategories( $params, $items, $gid, $other_categories, $catid, $id, $Itemid );
				}
				if ( ( $params->get( 'type' ) == 'section' ) && $params->get( 'other_cat_section' ) ) {
					HTML_content::showCategories( $params, $items, $gid, $other_categories, $catid, $id, $Itemid );
				}
			}
			?>
			</td>
		</tr>
		</table>
		<?php
		// displays back button
		mosHTML::BackButton ( $params );
	}


	/**
	* Display links to categories
	*/
	function showCategories( &$params, &$items, $gid, &$other_categories, $catid, $id, $Itemid ) {
		?>
		<ul>
		<?php
		foreach ( $other_categories as $row ) {
			if ( $catid != $row->id ) {
				if ( $row->access <= $gid ) {
					$link = sefRelToAbs( 'index.php?option=com_content&amp;task=category&amp;sectionid='. $id .'&amp;id='. $row->id .'&amp;Itemid='. $Itemid );
					?>
					<li>
					<a href="<?php echo $link; ?>" class="category">
					<?php echo $row->name;?>
					</a>
					<?php
					if ( $params->get( 'cat_items' ) ) {
						?>
						&nbsp;<i>( <?php echo $row->numitems; echo _CHECKED_IN_ITEMS;?> )</i>
						<?php
					}
					// Writes Category Description
					if ( $params->get( 'cat_description' ) && $row->description ) {
						echo "<br />";
						echo $row->description;
					}
					?>
					</li>
				<?php
				} else {
					?>
					<li>
					<?php echo $row->name; ?>
					<a href="<?php echo sefRelToAbs( 'index.php?option=com_registration&amp;task=register' ); ?>">
					( <?php echo _E_REGISTERED; ?> )
					</a>
					<?php
				}
			}
		}
		?>
		</ul>
		<?php
	}


	/**
	* Display Table of items
	*/
	function showTable( &$params, &$items, &$gid, $catid, $id, &$pageNav, &$access, &$sectionid, &$lists ) {
		global $mosConfig_live_site, $Itemid;
		$link = 'index.php?option=com_content&amp;task=category&amp;sectionid='. $sectionid .'&amp;id='. $catid .'&amp;Itemid='. $Itemid;
		?>
		<form action="<?php echo sefRelToAbs($link); ?>" method="post" name="adminForm">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td colspan="4">
				<table>
				<tr>
					<?php
					if ( $params->get( 'filter' ) ) {
						?>
						<td align="right" width="100%" nowrap="nowrap">
						<?php
						echo _FILTER .'&nbsp;';
						?>
						<input type="text" name="filter" value="<?php echo $lists['filter'];?>" class="inputbox" onchange="document.adminForm.submit();" />
						</td>
						<?php
					}

					if ( $params->get( 'order_select' ) ) {
						?>
						<td align="right" width="100%" nowrap="nowrap">
						<?php
						echo '&nbsp;&nbsp;&nbsp;'. _ORDER_DROPDOWN .'&nbsp;';
						echo $lists['order'];
						?>
						</td>
						<?php
					}

					if ( $params->get( 'display' ) ) {
						?>
						<td align="right" width="100%" nowrap="nowrap">
						<?php
						echo '&nbsp;&nbsp;&nbsp;'. _PN_DISPLAY_NR .'&nbsp;';
						$link = 'index.php?option=com_content&amp;task=category&amp;sectionid='. $sectionid .'&amp;id='. $catid .'&amp;Itemid='. $Itemid;
						echo $pageNav->getLimitBox( $link );
						?>
						</td>
						<?php
					}
					?>
				</tr>
				</table>
			</td>
		</tr>
		<?php
		if ( $params->get( 'headings' ) ) {
			?>
			<tr>
				<?php
				if ( $params->get( 'date' ) ) {
					?>
					<td class="sectiontableheader<?php echo $params->get( 'pageclass_sfx' ); ?>" width="35%">
					&nbsp;<?php echo _DATE; ?>
					</td>
					<?php
				}
				if ( $params->get( 'title' ) ) {
					?>
					<td class="sectiontableheader<?php echo $params->get( 'pageclass_sfx' ); ?>" width="45%">
					<?php echo _HEADER_TITLE; ?>
					</td>
					<?php
				}
				if ( $params->get( 'author' ) ) {
					?>
					<td class="sectiontableheader<?php echo $params->get( 'pageclass_sfx' ); ?>" align="left" width="25%">
					<?php echo _HEADER_AUTHOR; ?>
					</td>
					<?php
				}
				if ( $params->get( 'hits' ) ) {
					?>
					<td align="center" class="sectiontableheader<?php echo $params->get( 'pageclass_sfx' ); ?>" width="5%">
					<?php echo _HEADER_HITS; ?>
					</td>
					<?php
				}
				?>
			</tr>
			<?php
		}

		$k = 0;
		foreach ( $items as $row ) {
			$row->created = mosFormatDate ($row->created, $params->get( 'date_format' ));
			?>
			<tr class="sectiontableentry<?php echo ($k+1) . $params->get( 'pageclass_sfx' ); ?>" >
				<?php
				if ( $params->get( 'date' ) ) {
					?>
					<td>
					<?php echo $row->created; ?>
					</td>
					<?php
				}
				if ( $params->get( 'title' ) ) {
					if( $row->access <= $gid ){
						$link = sefRelToAbs( 'index.php?option=com_content&amp;task=view&amp;id='. $row->id .'&amp;Itemid='. $Itemid );
						?>
						<td>
						<a href="<?php echo $link; ?>">
						<?php echo $row->title; ?>
						</a>
						<?php
						HTML_content::EditIcon( $row, $params, $access );
						?>
						</td>
						<?php
					} else {
						?>
						<td>
						<?php
						echo $row->title .' : ';
						$link = sefRelToAbs( 'index.php?option=com_registration&amp;task=register' );
						?>
						<a href="<?php echo $link; ?>">
						<?php echo _READ_MORE_REGISTER; ?>
						</a>
						</td>
						<?php
					}
				}
				if ( $params->get( 'author' ) ) {
					?>
					<td align="left">
					<?php echo $row->created_by_alias ? $row->created_by_alias : $row->author; ?>
					</td>
					<?php
				}
				if ( $params->get( 'hits' ) ) {
				?>
					<td align="center">
					<?php echo $row->hits ? $row->hits : '-'; ?>
					</td>
				<?php
			} ?>
		</tr>
		<?php
			$k = 1 - $k;
		}
		if ( $params->get( 'navigation' ) ) {
			?>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td align="center" colspan="4" class="sectiontablefooter<?php echo $params->get( 'pageclass_sfx' ); ?>">
				<?php
				$link = 'index.php?option=com_content&amp;task=category&amp;sectionid='. $sectionid .'&amp;id='. $catid .'&amp;Itemid='. $Itemid;
				echo $pageNav->writePagesLinks( $link );
				?>
				</td>
			</tr>
			<tr>
				<td colspan="4" align="right">
				<?php echo $pageNav->writePagesCounter(); ?>
				</td>
			</tr>
			<?php
		}
		?>
		<?php
		if ( $access->canEdit || $access->canEditOwn ) {
			$link = sefRelToAbs( 'index.php?option=com_content&amp;task=new&amp;sectionid='. $id .'&amp;cid='. $row->id .'&amp;Itemid='. $Itemid );
			?>
			<tr>
				<td colspan="4">
				<a href="<?php echo $link; ?>">
				<img src="<?php echo $mosConfig_live_site;?>/images/M_images/new.png" width="13" height="14" align="middle" border="0" alt="<?php echo _CMN_NEW;?>" />
				&nbsp;<?php echo _CMN_NEW;?>...
				</a>
				</td>
			</tr>
			<?php
		}
		?>
		</table>
		<input type="hidden" name="id" value="<?php echo $catid; ?>" />
		<input type="hidden" name="sectionid" value="<?php echo $sectionid; ?>" />
		<input type="hidden" name="task" value="<?php echo $lists['task']; ?>" />
		<input type="hidden" name="option" value="com_content" />
		</form>
		<?php
	}


	/**
	* Display links to content items
	*/
	function showLinks( &$rows, $links, $total, $i=0, $show=1, $ItemidCount ) {
		global $mainframe;

		if ( $show ) {
			?>
			<div>
			<strong>
			<?php echo _MORE; ?>
			</strong>
			</div>
			<ul>
			<?php
		}
		for ( $z = 0; $z < $links; $z++ ) {
			if ( $i >= $total ) {
				// stops loop if total number of items is less than the number set to display as intro + leading
				break;
			}
			// needed to reduce queries used by getItemid
			$_Itemid = $mainframe->getItemid( $rows[$i]->id, 0, 0, $ItemidCount['bs'], $ItemidCount['bc'], $ItemidCount['gbs']  );
			$link = sefRelToAbs( 'index.php?option=com_content&amp;task=view&amp;id='. $rows[$i]->id .'&amp;Itemid='. $_Itemid )
			?>
			<li>
			<a class="blogsection" href="<?php echo $link; ?>">
			<?php echo $rows[$i]->title; ?>
			</a>
			</li>
			<?php
			$i++;
		}
		?>
		</ul>
		<?php
	}


	/**
	* Show a content item
	* @param object An object with the record data
	* @param boolean If <code>false</code>, the print button links to a popup window.  If <code>true</code> then the print button invokes the browser print method.
	*/
	function show( &$row, &$params, &$access, $page=0, $option, $ItemidCount=NULL ) {
		global $mainframe, $my, $hide_js;
		global $mosConfig_sitename, $Itemid, $mosConfig_live_site, $task;
		global $_MAMBOTS;

		$mainframe->appendMetaTag( 'description', $row->metadesc );
		$mainframe->appendMetaTag( 'keywords', $row->metakey );

		$gid 		= $my->gid;
		$_Itemid 	= $Itemid;
		$link_on 	= '';
		$link_text 	= '';
	
		// process the new bots
		$_MAMBOTS->loadBotGroup( 'content' );
		$results = $_MAMBOTS->trigger( 'onPrepareContent', array( &$row, &$params, $page ), true );

		// adds mospagebreak heading or title to <site> Title
		if ( @$row->page_title ) {
			$mainframe->SetPageTitle( $row->title .': '. $row->page_title );
		}

		// determines the link and link text of the readmore button
		if ( $params->get( 'intro_only' ) ) {
			// checks if the item is a public or registered/special item
			if ( $row->access <= $gid ) {
				if ($task != "view") {
					$_Itemid = $mainframe->getItemid( $row->id, 0, 0, $ItemidCount['bs'], $ItemidCount['bc'], $ItemidCount['gbs'] );
				}
				$link_on = sefRelToAbs("index.php?option=com_content&amp;task=view&amp;id=".$row->id."&amp;Itemid=".$_Itemid);
				if ( strlen( trim( $row->fulltext ) )) {
					$link_text = _READ_MORE;
				}
			} else {
				$link_on = sefRelToAbs("index.php?option=com_registration&amp;task=register");
				if (strlen( trim( $row->fulltext ) )) {
					$link_text = _READ_MORE_REGISTER;
				}
			}
		}

		$no_html = mosGetParam( $_REQUEST, 'no_html', null);

		// for pop-up page
		if ( $params->get( 'popup' ) && $no_html == 0) {
			?>
			<title>
			<?php echo $mosConfig_sitename .' :: '. $row->title; ?>
			</title>
			<?php
		}

		// determines links to next and prev content items within category
		if ( $params->get( 'item_navigation' ) ) {
			if ( $row->prev ) {
				$row->prev = sefRelToAbs( 'index.php?option=com_content&amp;task=view&amp;id='. $row->prev .'&amp;Itemid='. $_Itemid );
			} else {
				$row->prev = 0;
			}
			if ( $row->next ) {
				$row->next = sefRelToAbs( 'index.php?option=com_content&amp;task=view&amp;id='. $row->next .'&amp;Itemid='. $_Itemid );
			} else {
				$row->next = 0;
			}
		}

		if ( $params->get( 'item_title' ) || $params->get( 'pdf' )  || $params->get( 'print' ) || $params->get( 'email' ) ) {
			// link used by print button
			$print_link = $mosConfig_live_site. '/index2.php?option=com_content&amp;task=view&amp;id='. $row->id .'&amp;Itemid='. $Itemid .'&amp;pop=1&amp;page='. @$page;
			?>
			<table class="contentpaneopen<?php echo $params->get( 'pageclass_sfx' ); ?>">
			<tr>
				<?php
				// displays Item Title
				HTML_content::Title( $row, $params, $link_on, $access );

				// displays PDF Icon
				HTML_content::PdfIcon( $row, $params, $link_on, $hide_js );

				// displays Print Icon
				mosHTML::PrintIcon( $row, $params, $hide_js, $print_link );

				// displays Email Icon
				HTML_content::EmailIcon( $row, $params, $hide_js );
				?>
			</tr>
			</table>
			<?php
 		} else if ( $access->canEdit ) {
 			// edit icon when item title set to hide
 			?>
			<table class="contentpaneopen<?php echo $params->get( 'pageclass_sfx' ); ?>">
 			<tr>
 				<td>
 				<?php
 				HTML_content::EditIcon( $row, $params, $access );
 				?>
 				</td>
 			</tr>
 			</table>
 			<?php
  		}

		if ( !$params->get( 'intro_only' ) ) {
			$results = $_MAMBOTS->trigger( 'onAfterDisplayTitle', array( &$row, &$params, $page ) );
			echo trim( implode( "\n", $results ) );
		}

		$results = $_MAMBOTS->trigger( 'onBeforeDisplayContent', array( &$row, &$params, $page ) );
		echo trim( implode( "\n", $results ) );
		?>

		<table class="contentpaneopen<?php echo $params->get( 'pageclass_sfx' ); ?>">
		<?php
		// displays Section & Category
		HTML_content::Section_Category( $row, $params );

		// displays Author Name
		HTML_content::Author( $row, $params );

		// displays Created Date
		HTML_content::CreateDate( $row, $params );

		// displays Urls
		HTML_content::URL( $row, $params );
		?>
		<tr>
			<td valign="top" colspan="2">
			<?php
			// displays Table of Contents
			HTML_content::TOC( $row );

			// displays Item Text
			echo $row->text;
			?>
			</td>
		</tr>
		<?php

		// displays Modified Date
		HTML_content::ModifiedDate( $row, $params );

		// displays Readmore button
		HTML_content::ReadMore( $params, $link_on, $link_text );
		?>
		</table>
		<span class="article_seperator"></span>
		<?php
		$results = $_MAMBOTS->trigger( 'onAfterDisplayContent', array( &$row, &$params, $page ) );
		echo trim( implode( "\n", $results ) );

		// displays the next & previous buttons
		HTML_content::Navigation ( $row, $params );

		// displays close button in pop-up window
		mosHTML::CloseButton ( $params, $hide_js );

		// displays back button in pop-up window
		mosHTML::BackButton ( $params, $hide_js );
	}


	/**
	* Writes Title
	*/
	function Title( $row, $params, $link_on, $access ) {
		if ( $params->get( 'item_title' ) ) {
			if ( $params->get( 'link_titles' ) && $link_on != '' ) {
				?>
				<td class="contentheading<?php echo $params->get( 'pageclass_sfx' ); ?>" width="100%">
				<a href="<?php echo $link_on;?>" class="contentpagetitle<?php echo $params->get( 'pageclass_sfx' ); ?>">
				<?php echo $row->title;?>
				</a>
				<?php HTML_content::EditIcon( $row, $params, $access ); ?>
				</td>
				<?php
			} else {
				?>
				<td class="contentheading<?php echo $params->get( 'pageclass_sfx' ); ?>" width="100%">
				<?php echo $row->title;?>
				<?php HTML_content::EditIcon( $row, $params, $access ); ?>
				</td>
				<?php
			}
		} else {
			?>
			<td class="contentheading<?php echo $params->get( 'pageclass_sfx' ); ?>" width="100%">
			<?php HTML_content::EditIcon( $row, $params, $access ); ?>
			</td>
			<?php			
		}
	}

	/**
	* Writes Edit icon that links to edit page
	*/
	function EditIcon( $row, $params, $access ) {
		global $Itemid, $my;
		if ( $params->get( 'popup' ) ) {
			return;
		}
		if ( $row->state < 0 ) {
			return;
		}
		if ( !$access->canEdit && !( $access->canEditOwn && $row->created_by == $my->id ) ) {
			return;
		}
		mosCommonHTML::loadOverlib();			
		
		$link = 'index.php?option=com_content&amp;task=edit&amp;id='. $row->id .'&amp;Itemid='. $Itemid .'&amp;Returnid='. $Itemid;
		$image = mosAdminMenus::ImageCheck( 'edit.png', '/images/M_images/', NULL, NULL, _E_EDIT );
		
		if ( $row->state == 0 ) {
			$overlib = _CMN_UNPUBLISHED;
		} else {
			$overlib = _CMN_PUBLISHED;
		}
		$date 		= mosFormatDate( $row->created );
		$author		= $row->created_by_alias ? $row->created_by_alias : $row->author;
		
		$overlib 	.= '<br/>';
		$overlib 	.= $row->groups;
		$overlib 	.= '<br/>';
		$overlib 	.= $date;
		$overlib 	.= '<br/>';
		$overlib 	.= $author;
		?>
		<a href="<?php echo sefRelToAbs( $link ); ?>" title="<?php echo _E_EDIT;?>"  onMouseOver="return overlib('<?php echo $overlib; ?>', CAPTION, '<?php echo 'Edit Item'; ?>', BELOW, RIGHT);" onMouseOut="return nd();">
		<?php echo $image; ?>
		</a>
		<?php
	}


	/**
	* Writes PDF icon
	*/
	function PdfIcon( $row, $params, $link_on, $hide_js ) {
		global $mosConfig_live_site;
		if ( $params->get( 'pdf' ) && !$params->get( 'popup' ) && !$hide_js ) {
			$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
			$link = $mosConfig_live_site. '/index2.php?option=com_content&amp;do_pdf=1&amp;id='. $row->id;
			if ( $params->get( 'icons' ) ) {
				$image = mosAdminMenus::ImageCheck( 'pdf_button.png', '/images/M_images/', NULL, NULL, _CMN_PDF );
			} else {
				$image = _CMN_PDF .'&nbsp;';
			}
			?>
			<td align="right" width="100%" class="buttonheading">
			<a href="javascript:void window.open('<?php echo $link; ?>', 'win2', '<?php echo $status; ?>');" title="<?php echo _CMN_PDF;?>">
			<?php echo $image; ?>
			</a>
			</td>
			<?php
		}
	}


	/**
	* Writes Email icon
	*/
	function EmailIcon( $row, $params, $hide_js ) {
		global $mosConfig_live_site;
		if ( $params->get( 'email' ) && !$params->get( 'popup' ) && !$hide_js ) {
			$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=400,height=250,directories=no,location=no';
			$link = $mosConfig_live_site .'/index2.php?option=com_content&amp;task=emailform&amp;id='. $row->id;
			if ( $params->get( 'icons' ) ) {
				$image = mosAdminMenus::ImageCheck( 'emailButton.png', '/images/M_images/', NULL, NULL, _CMN_EMAIL );
			} else {
				$image = '&nbsp;'. _CMN_EMAIL;
			}
			?>
			<td align="right" width="100%" class="buttonheading">
			<a href="javascript:void window.open('<?php echo $link; ?>', 'win2', '<?php echo $status; ?>');" title="<?php echo _CMN_EMAIL;?>">
			<?php echo $image; ?>
			</a>
			</td>
			<?php
		}
	}

	/**
	* Writes Container for Section & Category
	*/
	function Section_Category( $row, $params ) {
		if ( $params->get( 'section' ) || $params->get( 'category' ) ) {
			?>
			<tr>
				<td>
			<?php
		}

		// displays Section Name
		HTML_content::Section( $row, $params );

		// displays Section Name
		HTML_content::Category( $row, $params );

		if ( $params->get( 'section' ) || $params->get( 'category' ) ) {
			?>
				</td>
			</tr>
		<?php
		}
	}

	/**
	* Writes Section
	*/
	function Section( $row, $params ) {
		if ( $params->get( 'section' ) ) {
				?>
				<span>
				<?php
				echo $row->section;
				// writes dash between section & Category Name when both are active
				if ( $params->get( 'category' ) ) {
					echo ' - ';
				}
				?>
				</span>
			<?php
		}
	}

	/**
	* Writes Category
	*/
	function Category( $row, $params ) {
		if ( $params->get( 'category' ) ) {
			?>
			<span>
			<?php
			echo $row->category;
			?>
			</span>
			<?php
		}
	}

	/**
	* Writes Author name
	*/
	function Author( $row, $params ) {
		global $acl;
		if ( ( $params->get( 'author' ) ) && ( $row->author != "" ) ) {
			$grp = $acl->getAroGroup( $row->created_by );
			$is_frontend_user = $acl->is_group_child_of( intval( $grp->group_id ), 'Public Frontend', 'ARO' );
			$by = $is_frontend_user ? _AUTHOR_BY : _WRITTEN_BY;
		?>
		<tr>
			<td width="70%" align="left" valign="top" colspan="2">
			<span class="small">
			<?php echo $by. ' '.( $row->created_by_alias ? $row->created_by_alias : $row->author ); ?>
			</span>
			&nbsp;&nbsp;
			</td>
		</tr>
		<?php
		}
	}


	/**
	* Writes Create Date
	*/
	function CreateDate( $row, $params ) {
		$create_date = null;
		if ( intval( $row->created ) != 0 ) {
			$create_date = mosFormatDate( $row->created );
		}
		if ( $params->get( 'createdate' ) ) {
			?>
			<tr>
				<td valign="top" colspan="2" class="createdate">
				<?php echo $create_date; ?>
				</td>
			</tr>
			<?php
		}
	}

	/**
	* Writes URL's
	*/
	function URL( $row, $params ) {
		if ( $params->get( 'url' ) && $row->urls ) {
			?>
			<tr>
				<td valign="top" colspan="2">
				<a href="http://<?php echo $row->urls ; ?>" target="_blank">
				<?php echo $row->urls; ?>
				</a>
				</td>
			</tr>
			<?php
		}
	}

	/**
	* Writes TOC
	*/
	function TOC( $row ) {
		if ( @$row->toc ) {
			echo $row->toc;
		}
	}

	/**
	* Writes Modified Date
	*/
	function ModifiedDate( $row, $params ) {
		$mod_date = null;
		if ( intval( $row->modified ) != 0) {
			$mod_date = mosFormatDate( $row->modified );
		}
		if ( ( $mod_date != '' ) && $params->get( 'modifydate' ) ) {
			?>
			<tr>
				<td colspan="2" align="left" class="modifydate">
				<?php echo _LAST_UPDATED; ?> ( <?php echo $mod_date; ?> )
				</td>
			</tr>
			<?php
		}
	}

	/**
	* Writes Readmore Button
	*/
	function ReadMore ( $params, $link_on, $link_text ) {
		if ( $params->get( 'readmore' ) ) {
			if ( $params->get( 'intro_only' ) && $link_text ) {
				?>
				<tr>
					<td align="left" colspan="2">
					<a href="<?php echo $link_on;?>" class="readon<?php echo $params->get( 'pageclass_sfx' ); ?>">
					<?php echo $link_text;?>
					</a>
					</td>
				</tr>
				<?php
			}
		}
	}

	/**
	* Writes Next & Prev navigation button
	*/
	function Navigation( $row, $params ) {
		$task = mosGetParam( $_REQUEST, 'task', '' );
		if ( $params->get( 'item_navigation' ) && ( $task == "view" ) && !$params->get( 'popup' ) ) {
		?>
		<table align="center" style="margin-top: 25px;">
		<tr>
			<?php
			if ( $row->prev ) {
				?>
				<th class="pagenav_prev">
				<a href="<?php echo $row->prev; ?>">
				<?php echo _ITEM_PREVIOUS; ?>
				</a>
				</th>
				<?php
			}
			if ( $row->prev && $row->next ) {
				?>
				<td width="50px">&nbsp;

				</td>
				<?php
			}
			if ( $row->next ) {
				?>
				<th class="pagenav_next">
				<a href="<?php echo $row->next; ?>">
				<?php echo _ITEM_NEXT; ?>
				</a>
				</th>
				<?php
			}
			?>
		</tr>
		</table>
		<?php
		}
	}

	/**
	* Writes the edit form for new and existing content item
	*
	* A new record is defined when <var>$row</var> is passed with the <var>id</var>
	* property set to 0.
	* @param mosContent The category object
	* @param string The html for the groups select list
	*/
	function editContent( &$row, $section, &$lists, &$images, &$access, $myid, $sectionid, $task, $Itemid ) {
		global $mosConfig_live_site;
		mosMakeHtmlSafe( $row );

		$Returnid 	= intval( mosGetParam( $_REQUEST, 'Returnid', $Itemid ) );
		$tabs 		= new mosTabs(0);
		?>
  		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
  		<link rel="stylesheet" type="text/css" media="all" href="includes/js/calendar/calendar-mos.css" title="green" />
			<!-- import the calendar script -->
			<script type="text/javascript" src="<?php echo $mosConfig_live_site;?>/includes/js/calendar/calendar_mini.js"></script>
			<!-- import the language module -->
			<script type="text/javascript" src="<?php echo $mosConfig_live_site;?>/includes/js/calendar/lang/calendar-en.js"></script>
	  	<script language="Javascript" src="<?php echo $mosConfig_live_site;?>/includes/js/overlib_mini.js"></script>
	  	<script language="javascript" type="text/javascript">
		onunload = WarnUser;
		var folderimages = new Array;
		<?php
		$i = 0;
		foreach ($images as $k=>$items) {
			foreach ($items as $v) {
				echo "\n	folderimages[".$i++."] = new Array( '$k','".addslashes( $v->value )."','".addslashes( $v->text )."' );";
			}
		}
		?>
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// var goodexit=false;
			// assemble the images back into one field
			form.goodexit.value=1
			var temp = new Array;
			for (var i=0, n=form.imagelist.options.length; i < n; i++) {
				temp[i] = form.imagelist.options[i].value;
			}
			form.images.value = temp.join( '\n' );
			try {
				form.onsubmit();
			}
			catch(e){}
			// do field validation
			if (form.title.value == "") {
				alert ( "<?php echo _E_WARNTITLE; ?>" );
			} else if (parseInt('<?php echo $row->sectionid;?>')) {
				// for content items
				if (getSelectedValue('adminForm','catid') < 1) {
					alert ( "<?php echo _E_WARNCAT; ?>" );
				//} else if (form.introtext.value == "") {
				//	alert ( "<?php echo _E_WARNTEXT; ?>" );
				} else {
					<?php
					getEditorContents( 'editor1', 'introtext' );
					getEditorContents( 'editor2', 'fulltext' );
					?>
					submitform(pressbutton);
				}
			//} else if (form.introtext.value == "") {
			//	alert ( "<?php echo _E_WARNTEXT; ?>" );
			} else {
				// for static content
				<?php
				getEditorContents( 'editor1', 'introtext' ) ;
				?>
				submitform(pressbutton);
			}
		}

		function setgood(){
			document.adminForm.goodexit.value=1;
		}

		function WarnUser(){
			if (document.adminForm.goodexit.value==0) {
				alert('<?php echo _E_WARNUSER;?>');
				window.location="<?php echo sefRelToAbs("index.php?option=com_content&task=".$task."&sectionid=".$sectionid."&id=".$row->id."&Itemid=".$Itemid); ?>"
			}
		}
		</script>

		<?php
		//$docinfo = "<strong>"._E_SUBJECT."</strong> ";
		//$docinfo .= $row->title."<br />";
		$docinfo = "<strong>"._E_EXPIRES."</strong> ";
		$docinfo .= $row->publish_down."<br />";
		$docinfo .= "<strong>"._E_VERSION."</strong> ";
		$docinfo .= $row->version."<br />";
		$docinfo .= "<strong>"._E_CREATED."</strong> ";
		$docinfo .= $row->created."<br />";
		$docinfo .= "<strong>"._E_LAST_MOD."</strong> ";
		$docinfo .= $row->modified."<br />";
		$docinfo .= "<strong>"._E_HITS."</strong> ";
		$docinfo .= $row->hits."<br />";
		?>
		<form action="index.php" method="post" name="adminForm" onSubmit="javascript:setgood();">

		<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td class="contentheading" >
			<?php echo $section;?> / <?php echo $row->id ? _E_EDIT : _E_ADD;?>&nbsp;
			<?php echo _E_CONTENT;?> &nbsp;&nbsp;&nbsp;
			<a href="javascript: void(0);" onMouseOver="return overlib('<table><?php echo $docinfo; ?></table>', CAPTION, '<?php echo _E_ITEM_INFO;?>', BELOW, RIGHT);" onMouseOut="return nd();">
			<strong>[Info]</strong>
			</a>
			</td>
		</tr>
		</table>

		<table class="adminform">
		<tr>
			<td>
				<div style="float: left;">
					<?php echo _E_TITLE; ?>
					<br/>
					<input class="inputbox" type="text" name="title" size="50" maxlength="100" value="<?php echo $row->title; ?>" />
				</div>
				<div style="float: right;">
					<?php
					// Toolbar Top
					mosToolBar::startTable();
					mosToolBar::save();
					mosToolBar::apply();
					mosToolBar::cancel();
					mosToolBar::endtable();
					?>
				</div>
			</td>
		</tr>
		<?php
		if ($row->sectionid) {
			?>
			<tr>
				<td>
				<?php echo _E_CATEGORY; ?>
				<br/>
				<?php echo $lists['catid']; ?>
				</td>
			</tr>
			<?php
		}
		?>
		<tr>
			<?php
			if (intval( $row->sectionid ) > 0) {
				?>
				<td>
				<?php echo _E_INTRO.' ('._CMN_REQUIRED.')'; ?>:
				</td>
				<?php
			} else {
				?>
				<td>
				<?php echo _E_MAIN.' ('._CMN_REQUIRED.')'; ?>:
				</td>
			<?php
			} ?>
		</tr>
		<tr>
			<td>
			<?php
			// parameters : areaname, content, hidden field, width, height, rows, cols
			editorArea( 'editor1',  $row->introtext , 'introtext', '600', '400', '70', '15' ) ;
			?>
			</td>
		</tr>
		<?php
		if (intval( $row->sectionid ) > 0) {
			?>
			<tr>
				<td>
				<?php echo _E_MAIN.' ('._CMN_OPTIONAL.')'; ?>:
				</td>
			</tr>
			<tr>
				<td>
				<?php
				// parameters : areaname, content, hidden field, width, height, rows, cols
				editorArea( 'editor2',  $row->fulltext , 'fulltext', '600', '400', '70', '15' ) ;
				?>
				</td>
			</tr>
			<?php
		}
		?>
		</table>

		<?php
		// Toolbar Bottom
		mosToolBar::startTable();
		mosToolBar::save();
		mosToolBar::apply();
		mosToolBar::cancel();
		mosToolBar::endtable();
		?>

	 	<?php
		$tabs->startPane( 'content-pane' );
		$tabs->startTab( _E_IMAGES, 'images-page' );
		?>
			<table class="adminform">
			<tr>
				<td colspan="6">
				<?php echo _CMN_SUBFOLDER; ?> :: <?php echo $lists['folders'];?>
				</td>
			</tr>
			<tr>
				<td align="top">
				<?php echo _E_GALLERY_IMAGES; ?>
				</td>
				<td align="top">
				<?php echo _E_CONTENT_IMAGES; ?>
				</td>
				<td align="top">
				<?php echo _E_EDIT_IMAGE; ?>
				</td>
			<tr>
				<td valign="top">
				<?php echo $lists['imagefiles'];?>
				<br />
				<input class="button" type="button" value="<?php echo _E_INSERT; ?>" onclick="addSelectedToList('adminForm','imagefiles','imagelist')" />
				</td>
				<td valign="top">
				<?php echo $lists['imagelist'];?>
				<br />
				<input class="button" type="button" value="<?php echo _E_UP; ?>" onclick="moveInList('adminForm','imagelist',adminForm.imagelist.selectedIndex,-1)" />
				<input class="button" type="button" value="<?php echo _E_DOWN; ?>" onclick="moveInList('adminForm','imagelist',adminForm.imagelist.selectedIndex,+1)" />
				<input class="button" type="button" value="<?php echo _E_REMOVE; ?>" onclick="delSelectedFromList('adminForm','imagelist')" />
				</td>
				<td valign="top">
					<table>
					<tr>
						<td align="right">
						<?php echo _E_SOURCE; ?>
						</td>
						<td>
						<input class="inputbox" type="text" name= "_source" value="" size="15" />
						</td>
					</tr>
					<tr>
						<td align="right" valign="top">
						<?php echo _E_ALIGN; ?>
						</td>
						<td>
						<?php echo $lists['_align']; ?>
						</td>
					</tr>
					<tr>
						<td align="right">
						<?php echo _E_ALT; ?>
						</td>
						<td>
						<input class="inputbox" type="text" name="_alt" value="" size="15" />
						</td>
					</tr>
					<tr>
						<td align="right">
						<?php echo _E_BORDER; ?>
						</td>
						<td>
						<input class="inputbox" type="text" name="_border" value="" size="3" maxlength="1" />
						</td>
					</tr>
					<tr>
						<td align="right">
						Caption:
						</td>
						<td>
						<input class="text_area" type="text" name="_caption" value="" size="30" />
						</td>
					</tr>
					<tr>
						<td align="right">
						Caption Position:
						</td>
						<td>
						<?php echo $lists['_caption_position']; ?>
						</td>
					</tr>
					<tr>
						<td align="right">
						Caption Align:
						</td>
						<td>
						<?php echo $lists['_caption_align']; ?>
						</td>
					</tr>
					<tr>
						<td align="right">
						Caption Width:
						</td>
						<td>
						<input class="text_area" type="text" name="_width" value="" size="5" maxlength="5" />
						</td>
					</tr>
					<tr>
						<td align="right"></td>
						<td>
						<input class="button" type="button" value="<?php echo _E_APPLY; ?>" onclick="applyImageProps()" />
						</td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
				<img name="view_imagefiles" src="<?php echo $mosConfig_live_site;?>/images/M_images/blank.png" width="50" alt="No Image" />
				</td>
				<td>
				<img name="view_imagelist" src="<?php echo $mosConfig_live_site;?>/images/M_images/blank.png" width="50" alt="No Image" />
				</td>
			</tr>
			</table>
		<?php
		$tabs->endTab();
		$tabs->startTab( _E_PUBLISHING, 'publish-page' );
		?>
			<table class="adminform">
			<?php
			if ($access->canPublish) {
				?>
				<tr>
					<td align="left">
					<?php echo _E_STATE; ?>
					</td>
					<td>
					<?php echo $lists['state']; ?>
					</td>
				</tr>
				<?php
			} ?>
			<tr>
				<td align="left">
				<?php echo _E_ACCESS_LEVEL; ?>
				</td>
				<td>
				<?php echo $lists['access']; ?>
				</td>
			</tr>
			<tr>
				<td align="left">
				<?php echo _E_AUTHOR_ALIAS; ?>
				</td>
				<td>
				<input type="text" name="created_by_alias" size="50" maxlength="100" value="<?php echo $row->created_by_alias; ?>" class="inputbox" />
				</td>
			</tr>
			<tr>
				<td align="left">
				<?php echo _E_ORDERING; ?>
				</td>
				<td>
				<?php echo $lists['ordering']; ?>
				</td>
			</tr>
			<tr>
				<td align="left">
				<?php echo _E_START_PUB; ?>
				</td>
				<td>
				<input class="inputbox" type="text" name="publish_up" id="publish_up" size="25" maxlength="19" value="<?php echo $row->publish_up; ?>" />
				<input type="reset" class="button" value="..." onclick="return showCalendar('publish_up', 'y-mm-dd');" />
				</td>
			</tr>
			<tr>
				<td align="left">
				<?php echo _E_FINISH_PUB; ?>
				</td>
				<td>
				<input class="inputbox" type="text" name="publish_down" id="publish_down" size="25" maxlength="19" value="<?php echo $row->publish_down; ?>" />
				<input type="reset" class="button" value="..." onclick="return showCalendar('publish_down', 'y-mm-dd');" />
				</td>
			</tr>
			<tr>
				<td align="left">
				<?php echo _E_SHOW_FP; ?>
				</td>
				<td>
				<input type="checkbox" name="frontpage" value="1" <?php echo $row->frontpage ? 'checked="checked"' : ''; ?> />
				</td>
			</tr>
			</table>
		<?php
		$tabs->endTab();
		$tabs->startTab( _E_METADATA, 'meta-page' );
		?>
			<table class="adminform">
			<tr>
				<td align="left" valign="top">
				<?php echo _E_M_DESC; ?>
				</td>
				<td>
				<textarea class="inputbox" cols="45" rows="3" name="metadesc"><?php echo str_replace('&','&amp;',$row->metadesc); ?></textarea>
				</td>
			</tr>
			<tr>
				<td align="left" valign="top">
				<?php echo _E_M_KEY; ?>
				</td>
				<td>
				<textarea class="inputbox" cols="45" rows="3" name="metakey"><?php echo str_replace('&','&amp;',$row->metakey); ?></textarea>
				</td>
			</tr>
			</table>
		<?php
		$tabs->endTab();
		$tabs->endPane();
		?>

		<div style="clear:both;"></div>

		<input type="hidden" name="images" value="" />
		<input type="hidden" name="goodexit" value="0" />
		<input type="hidden" name="option" value="com_content" />
		<input type="hidden" name="Returnid" value="<?php echo $Returnid; ?>" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="version" value="<?php echo $row->version; ?>" />
		<input type="hidden" name="sectionid" value="<?php echo $row->sectionid; ?>" />
		<input type="hidden" name="created_by" value="<?php echo $row->created_by; ?>" />
		<input type="hidden" name="referer" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}

	/**
	* Writes Email form for filling in the send destination
	*/
	function emailForm( $uid, $title, $template='' ) {
		global $mosConfig_sitename;
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton() {
			var form = document.frontendForm;
			// do field validation
			if (form.email.value == "" || form.youremail.value == "") {
				alert( '<?php echo addslashes( _EMAIL_ERR_NOINFO ); ?>' );
				return false;
			}
			return true;
		}
		</script>

		<title><?php echo $mosConfig_sitename; ?> :: <?php echo $title; ?></title>
		<link rel="stylesheet" href="templates/<?php echo $template; ?>/css/template_css.css" type="text/css" />
		<form action="index2.php?option=com_content&task=emailsend" name="frontendForm" method="post" onSubmit="return submitbutton();">
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td colspan="2">
			<?php echo _EMAIL_FRIEND; ?>
			</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td width="130">
			<?php echo _EMAIL_FRIEND_ADDR; ?>
			</td>
			<td>
			<input type="text" name="email" class="inputbox" size="25">
			</td>
		</tr>
		<tr>
			<td height="27">
			<?php echo _EMAIL_YOUR_NAME; ?>
			</td>
			<td>
			<input type="text" name="yourname" class="inputbox" size="25">
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _EMAIL_YOUR_MAIL; ?>
			</td>
			<td>
			<input type="text" name="youremail" class="inputbox" size="25">
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _SUBJECT_PROMPT; ?>
			</td>
			<td>
			<input type="text" name="subject" class="inputbox" maxlength="100" size="40">
			</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">
			<input type="submit" name="submit" class="button" value="<?php echo _BUTTON_SUBMIT_MAIL; ?>">
			&nbsp;&nbsp; <input type="button" name="cancel" value="<?php echo _BUTTON_CANCEL; ?>" class="button" onclick="window.close();">
			</td>
		</tr>
		</table>

		<input type="hidden" name="id" value="<?php echo $uid; ?>">
		<input type="hidden" name="<?php echo mosHash( 'validate' );?>" value="1">
		</form>
		<?php
	}

	/**
	* Writes Email sent popup
	* @param string Who it was sent to
	* @param string The current template
	*/
	function emailSent( $to, $template='' ) {
		global $mosConfig_sitename;
		?>
		<title><?php echo $mosConfig_sitename; ?></title>
		<link rel="stylesheet" href="templates/<?php echo $template; ?>/css/template_css.css" type="text/css" />
		<span class="contentheading"><?php echo _EMAIL_SENT." $to";?></span> <br />
		<br />
		<br />
		<a href='javascript:window.close();'>
		<span class="small"><?php echo _PROMPT_CLOSE;?></span>
		</a>
		<?php
	}
}
?>