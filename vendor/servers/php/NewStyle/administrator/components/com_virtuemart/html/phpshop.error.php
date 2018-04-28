<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
* @version $Id: phpshop.error.php,v 1.2.2.1 2006/03/10 15:55:15 soeren_nb Exp $
* @package VirtueMart
* @subpackage HTML
* Contains code from PHPShop(tm):
* 	@copyright (C) 2000 - 2004 Edikon Corporation (www.edikon.com)
*	Community: www.virtuemart.org, forums.virtuemart.org
* Conversion to Mambo and the rest:
* 	@copyright (C) 2004-2005 Soeren Eberhardt
*
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* VirtueMart is Free Software.
* VirtueMart comes with absolute no warranty.
*
* www.virtuemart.net
*/
mm_showMyFileName( __FILE__ );
?>
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#FFFFFF">
<table width="100%" border="0" cellspacing="0" cellpadding="10" bgcolor="#000000">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="0" cellpadding="2" bgcolor="#FFCC33">
        <tr align="center"> 
          <td> 
            <h4><font face="Verdana, Arial, Helvetica, sans-serif"><?php echo $VM_LANG->_PHPSHOP_ERROR ?></font></h4>
            <h5><font face="Verdana, Arial, Helvetica, sans-serif"><?php echo $error_type;?></font></h5>
            <center>
              <h5><font face="Verdana, Arial, Helvetica, sans-serif"><?php echo $error?></font></h5>
              </center>
</td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<h1>&nbsp;</h1>
<CENTER>
</CENTER>
</body>
</html>
