<?PHP
/**
 * Base class for patTemplate Stat
 *
 * $Id: File.php 47 2005-09-15 02:55:27Z rhuk $
 *
 * A stat component should be implemented for each reader
 * to support caching. Stats return information about the
 * template source.
 *
 * @package		patTemplate
 * @subpackage	Stat
 * @author		Stephan Schmidt <schst@php.net>
 */

/**
 * Base class for patTemplate Stat
 *
 * $Id: File.php 47 2005-09-15 02:55:27Z rhuk $
 *
 * A stat component should be implemented for each reader
 * to support caching. Stats return information about the
 * template source.
 *
 * @package		patTemplate
 * @subpackage	Stat
 * @author		Stephan Schmidt <schst@php.net>
 */
class patTemplate_Stat_File extends patTemplate_Stat
{
   /**
	* get the modification time of a template
	*
	* Needed, if a template cache should be used, that auto-expires
	* the cache.
	*
	* @abstract	must be implemented in the template readers
	* @param	mixed	input to read from.
	*					This can be a string, a filename, a resource or whatever the derived class needs to read from
	* @return	integer	unix timestamp
	*/
	function getModificationTime( $input )
	{
		$fullPath	=	$this->_options['root'] . '/' . $input;
		return @filemtime( $fullPath );
	}
}
?>