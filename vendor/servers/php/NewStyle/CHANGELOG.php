<?php
/**
* @version $Id: CHANGELOG.php 130 2005-09-16 22:53:18Z Saka $
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
?>

1. Changelog
------------
This is a non-exhaustive (but still near complete) changelog for
Joomla! 1.0, including beta and release candidate versions.
Our thanks to all those people who've contributed bug reports and
code fixes.

Legend:

# -> Bug Fix
+ -> Addition
^ -> Change
- -> Removed
! -> Note

-------------------- 1.0.0 Released ----------------------

16-Sep-2005 Andrew Eddie
 # Fixed: 1014 : & amp ; in pathway
 # Fixed: Missing space in mosimage IMG tags
 # Fixed: Incomplete function call - mysql_insert_id()
 + Added nullDate handling to database class
 + Added database::NameQuote function for quoting field names
 # Fixed: com_checkin to properly use database class
 # Fixed: Missed stripslashes in`global configuration - site`
 + Added admin menu item to clear all caches (for 3rd party addons)

16-Sep-2005 Emir Sakic
 # Fixed sorting by author on frontend category listing
 + Added time offset to copyright year in footer
 # Fixed spelling in sample data
 # Reflected some file name changes in installer CHMOD
 # Fixed bugs in paged search component

16-Sep-2005 Alex Kempkens
 + template contest winner 'MadeYourWeb' added

16-Sep-2005 Rey Gigataras
 + Pagination Support for Search Component
 ^ Ordering of Toolbar Icons/buttons now more consistent
 ^ Frontend Edit, status info moved to an overlib
 ^ Search Component converted to GET method
 # Fixed artf1018 : Warning Backend Statistic
 # Fixed artf1016 : Notice: RSS undefined constant
 # Fixed artf1020 : Hide mosimages in blogview doesn't work
 # Various Search Component Fixes
 # Fixed Search Component not honouring Show/Hide Date Global Config setting
 # Fixed [#6668] No static content edit icon for frontend logged in author
 # Fixed [#6710] `Link to menu` function from components Category not working
 # Fixed [#7011] Subtle bug in saveUser() - admin.users.php
 # Fixed [#7120] Articles with `publish_up` today after noon are shown with status `pending`
 # Fixed [#6669] mosmail BCC not working, send as CC
 # Fixed [#7422] Weblink submission emails
 # Fixed [#7196] mosRedirect and Input Filter CGI Error
 # Fixed [#6814] com_wrapper Iframe Name tag / relative url modifications
 # Fixed [#6844] rss version is wrong in the Live Bookmark feeds
 # Fixed [#7120] Articles with `publish_up` today after noon are shown with status `pending`
 # Fixed [#7161] Apparently unncessary code in sendNewPass - registration.php

15-Sep-2005 Andy Miller
 ^ Fixed some width issues with Admin template in IE
 ^ Fixed some UI issues with Banners Component
 ^ Added a default header image for components that don't specify one

15-Sep-2005 Andrew Eddie
 - Removed unused globals from joomla.php
 + Added mosAbstractLog class

15-Sep-2005 Rey Gigataras
 + added `Apply` button to frontend Content editing
 ^ Added publish date to syndicated feeds output [credit: gharding]
 ^ Added RSS Enclosure support to feedcreator [credit: Joseph L. LeBlanc]
 ^ Added Google Sitemap support to feedcreator
 ^ Modified layout of Media Manager
 ^ Added Media Manager support for XCF, ODG, ODT, ODS, ODP file formats
 # Fixed use of 302 redirect instead of 301
 # Content frontend `Save` Content redirects to full content view
 # Fixed Wrapper auto-height problem
 # Queries cleaned of incorrect encapsulation of integer values
 # Fixed Login Component redirection [credit: David Gal]

15-Sep-2005 Arno Zijlstra
 ^ changed tab images to fit new color
 ^ changed overlib colors

14-Sep-2005 Rey Gigataras
 ^ Ugraded TinyMCE [2.0 RC2]
 ^ Param tip style change to dashed underline
 # Queries cleaned of incorrect encapsulation of integer values

14-Sep-2005 Andrew Eddie
 # Added PHP 5 compatibility functions file_put_contents and file_get_contents
 + Added new version of js calendar
 + mosAbstractTasker::setAccessControl method
 + mosUser::getUserListFromGroup
 + mosParameters::toObject and mosParameters::toArray

13-Sep-2005 Andrew Eddie
 ^ Rationalised global configuration handling
 # Fixed polls access bug
 # Fixed module positions preview to show positions regardless of module count
 ^ Modified database:setQuery method to take offset and record limit
 + Added alternative version of globals.php that emulates register_globals=off
 # Added missing parent_id field from mosCategory class

12-Sep-2005 Rey Gigataras
 + Per User Editor selection
 # Module styling applied to custom/new modules
 # Fixed Agent Browser bug

12-Sep-2005 Andrew Eddie
 + New onAfterMainframe event added to site index.php
 + Added dtree javascript library
 + Added some extra useful toolbar icons
 + Added css for fieldsets and legends and some 1.1 admin style formating
 + Added mosDBTable::isCheckedOut() method, applied to components
 # fixed bug in typedcontent edit - checked out is done before object load and always passes
 ^ Updated Help toolbar button to accept component based help files
 ^ Updated version class with new methods
 + Added support for params file to have <mosparams> root tag

12-Sep-2005 Andy Stewart
 # Fixed issue with new content where Categories weren't displayed for sections

12-Sep-2005 Andrew Eddie
 ^ Upgrade DOMIT! and DOMIT!RSS (fixes issues in PHP 4.4.x)
 + Added database.mysqli.php, a MySQL 4.1.x compatible version
 + Added [Check Again] button to installation check screen
 ^ Changed web installer to always use the database connector
 # Fixed PHP 4.4 issues with new objects returning by reference

11-Sep-2005 Rey Gigataras
 + Output Buffering for Admin [pulled from Johan's work in 1.1]
 + Loading of WYSIWYG Editor only when `editorArea` is present [pulled from Johan's work in 1.1]
 ^ Upgraded JSCookMenu [1.4.3]
 ^ Upgraded wz_tooltip [3.34]
  ^ Upgraded Overlib [4.21]
 ^ editor-xtd mosimage & mospagebreak button hidden on category, section & module pages
 # Poll class $this-> bug
 # Fixed change creator dropdown to exclude registered users (who do not have author rights)

11-sep-2005 Arno Zijlstra
 + Added offlinebar.php
 ^ Changed site offline check
 ^ Cosmetic change to offline.php

11-Sep-2005 Andrew Eddie
 + Added sort up and down icons
 + Added mosPageNav::setTemplateVars method

10-Sep-2005 Rey Gigataras
 + `Submit - Content` menu type [credit: Jason Murpy]

09-Sep-2005 Andy Miller
 ^ made changes to new joomla admin template
 ^ changed login lnf to match new admin template
 ^ removed border and width, set padding on div.main in admin
 ^ changed Force Logout text

09-Sep-2005 Alex Kempkens
 ^ changed mosHTML::makeOption to handle different coulmn names
 ^ corrected several calls from makeOption in order to become multi lingual compatible
 ^ corrected little fixes in query handling in order to get multi lingual compatible
 + Added system bot's for better integration of ml support, ssl & multi sites

08-Sep-2005 Rey Gigataras
 + Added back Sys Info link in menubar
 + Added Changelog link to Help area
 ^ Cosmetic change to Toolbar Icon appearance
 ^ Cosmetic change to QuickIcon appearance
 ^ Toolbar icons now 'coloured' no longer 'greyed out'
 ^ Dropdown menu now shows on edit pages but is inactive
 # Fixed Newsfeed component generates image tag instead of img tag
 # Fixed Joomlaxml: tooltips need to use label instead of name
 # Fixed One parameter too many in orderModule call in admin.modules.php
 # Fixed inabiility to show/hide VCard
 # Fixed Mambot Manager filtering

08-Sep-2005 Alex Kempkens
 + mosParameter::_mos_filelist for xml parameters
 ^ mos_ table prefix to jos_ in installation and in some other files.
 + added category handling for contact component
 + added color adapted joomla_admin template

07-Sep-2005 Andrew Eddie
 # Added label tags to mod_login (WCAG compliance)
 # Added label tags to com_contact (WCAG compliance)
 # Added label tags to com_search (WCAG compliance)
 # Added label tag support to mosHTML::selectList (WCAG compliance)
 # Added label tag support to mosHTML::radioList (WCAG compliance)

01-Sep-2005 Andrew Eddie
 + Added article_separator span after a content item
 ^ Hardened mosGetParam by using phpInputFilter for NO_HTML mode
 + Added new mosHash function to produce secure keys
 + Hardened Email to Friend form

31-Aug-2005 Andrew Eddie
 + Added setTemplateVars method to admin pageNavigation class
 ^ Added auto mapping function to mosAbstractTasker constructor
 + Added patHTML class for patTemplate utility methods
 ^ Upgraded patTemplate library
 ! patTemplate::createTemplate has changed parameters
 - Removed requirement to accept GPL on installation
 # Fixed bug in Send New Password function - mail from not defined
 # Fixed undefined $row variable in wrapper component
 # Fixed undefined $params in contacts component
 - Removed unused getids.php
 - Removed redundant whitespace
 ^ Convert 4xSpace to tab

08-Aug-2005 Andrew Eddie
 ^ Encased text files in PHP wrapper to help obsfucate version info
 # Changed admin session name to hash of live_site to allow you to log into more than one Joomla! on the same host
 # Fixed hardcoded (c) character in web installer files
 # Fixed slow query in admin User Manager list screen
 # Fixed bug in poll stats calculation
 # Fixed SQL injection bugs in user activation (thanks Enno Klasing)
 # Updated bug fixes in phpMailer class
 # Fixed login bug for nested Joomla! sites on the same domain

02-Aug-2005 Alex Kempkens
 # [#6775] Display of static content without Itemid
 # [#6330] Corrected default value of field

----- Derived from Mambo 4.5.2.3 circa. 17 Aug 12005 -----

2. Copyright and disclaimer
---------------------------
This application is opensource software released under the GPL.  Please
see source code and the LICENSE file
