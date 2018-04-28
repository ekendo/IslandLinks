<?php

//
// Open Web Analytics - An Open Source Web Analytics Framework
//
// Copyright 2006 Peter Adams. All rights reserved.
//
// Licensed under GPL v2.0 http://www.gnu.org/copyleft/gpl.html
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.
//
// $Id$
//

/**
 * OWA Configuration
 * 
 * @author      Peter Adams <peter@openwebanalytics.com>
 * @copyright   Copyright &copy; 2006 Peter Adams <peter@openwebanalytics.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GPL v2.0
 * @category    owa
 * @package     owa
 * @version		$Revision$	      
 * @since		owa 1.0.0
 */
 
/**
 * DATABASE CONFIGURATION
 *
 * Connection info for databases that will be used by OWA. 
 *
 */

define('OWA_DB_TYPE', 'mysql'); // options: mysql
define('OWA_DB_NAME', 'ekendo_data'); // name of the database
define('OWA_DB_HOST', 'ekendo.hypermartmysql.com'); // host name of the server housing the database
define('OWA_DB_USER', 'island_track1'); // database user
define('OWA_DB_PASSWORD', 'IslandVann+'); // database user's password

/**
 * AUTHENTICATION KEYS AND SALTS
 *
 * Change these to different unique phrases.
 */
define('OWA_NONCE_KEY', '[ _Z%<v5y[H/R0i RA@Xg4A+Y%0TKd&FW?2G~AHbcr!JL&B@n*=y*oLi,,M0g;ag');  
define('OWA_NONCE_SALT', '!R>@>Xd.-v}]h_fo&Vv;j,_u4~A@qNR>A|JC4N4xTi*I3TkaQ&6vJG$5O:ER&}>8');
define('OWA_AUTH_KEY', 'dcu6o@DE3rD5ie+#1uv^Oy`i6C)3IO%1vE1DTtK)qJ;WtTo(_sXr WMk68r#I%>R');
define('OWA_AUTH_SALT', '7-!(Vhoa;9#Sn-LgpD!Ns&#|h7c1EO>Ea,YFXv[ V1>klF]Ki/)TX`;((B [{>DH');

/** 
 * PUBLIC URL
 *
 * Define the URL of OWA's base directory e.g. http://www.domain.com/path/to/owa/ 
 * Don't forget the slash at the end.
 */
 
define('OWA_PUBLIC_URL', 'http://ekendotech.com/Data/php/owa/');  

/** 
 * OWA ERROR HANDLER
 *
 * Overide OWA error handler. This should be done through the admin GUI, but 
 * can be handy during install or development. 
 * 
 * Choices are: 
 *
 * 'production' - will log only critical errors to a log file.
 * 'development' - logs al sorts of useful debug to log file.
 */

//define('OWA_ERROR_HANDLER', 'development');

/** 
 * LOG PHP ERRORS
 *
 * Log all php errors to OWA's error log file. Only do this to debug.
 */

//define('OWA_LOG_PHP_ERRORS', true);
 
/** 
 * OBJECT CACHING
 *
 * Override setting to cache objects. Caching will increase performance. 
 */

//define('OWA_CACHE_OBJECTS', true);

/**
 * CONFIGURATION ID
 *
 * Override to load an alternative user configuration
 */
 
//define('OWA_CONFIGURATION_ID', '1');


?>