<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
* @version $Id: phpshop.debug.php,v 1.2 2005/09/29 20:02:18 soeren_nb Exp $
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
<table width="100%" border="1" cellspacing="0" cellpadding="6">
  <tr>
    <td>
      <table width="100%" border="1" cellspacing="0" cellpadding="2">
        <tr nowrap> 
          <td nowrap colspan="2" class="sectiontableheader" align="center">--DEBUG--</td>
        </tr>
        <tr nowrap> 
          <td nowrap align="right" width="14%"><b>RunTime:</b></td>
          <td align="left" nowrap width="32%"><?php echo $runtime; ?>&nbsp;</td>
        </tr>
        <tr nowrap> 
          <td width="14%" align="right" valign="top" nowrap><b>SessionID:</b></td>
          <td width="32%" valign="top" nowrap><b><?php echo session_id(); ?></b></td>
        </tr>
         <tr> 
          <td align="right" valign="top" width="14%"><b>$auth:</b></td>
          <td width="32%"><b><?php   while (list($val,$key) = each($auth)) {
                                  echo "$val=>$key<br />";
                               } ?></b></td>
        </tr>
        <tr> 
          <td align="right"  valign="top" width="14%"><b>$cart:</b></td>
          <td width="32%"><b><?php   while (list($val,$key) = each($cart)) {
                                  echo "$val=>$key<br />";
                               } ?></b></td>
        </tr>
        <tr> 
          <td align="right"  valign="top" width="14%"><b>$my:</b></td>
          <td width="32%"><b><?php   while (list($val,$key) = each($my)) {
                                  echo "$val=>$key<br />";
                               }; ?></b></td>
        </tr>
        <tr> 
          <td align="right" valign="top" width="14%"><b>$_SESSION:</b> 
          </td>
          <td width="32%"><b><?php   while (list($val,$key) = each($_SESSION)) {
                                  /*if ($key == "cart") echo "\$cart: "; print_r($_SESSION['cart']);
                                  if ($key == "auth") echo "\$auth: "; print_r($_SESSION['auth']);*/
                                  echo "$val=>$key<br />";
                               } ?></b></td>
        </tr>
        <tr>
          <td width="18%" align="right" valign="top"><b>Current Page:</b></td>
          <td width="36%" valign="top"><b><?php echo $page; ?></b></td>
          </tr>
          <tr>
          <td width="18%" align="right" valign="top"><b>Last Page:</b></td>
          <td width="36%" valign="top"><b><?php echo $_SESSION['last_page']; ?>&nbsp;</b></td>
          </tr>
        <tr nowrap> 
          <td width="14%" align="right" valign="top"><b>Perms:</b></td>
          <td width="32%" valign="top"><b><?php echo $auth["perms"]; ?>&nbsp;</b></td>
          </tr>
          <tr>
          <td width="18%" align="right" valign="top"><b>&nbsp;&nbsp;Command:</b></td>
          <td width="36%" valign="top"><b>
            <?php echo $cmd."<br />Result:"; 
                      echo $ok ? "True" : "False"; 
                      ?>  &nbsp;</b></td>
        </tr>
        <tr nowrap> 
          <td width="14%" align="right" valign="top"><b>$func_perms:</b></td>
          <td width="32%" valign="top"><font size="-2"><b><?php echo $func_list["perms"]; ?>&nbsp;</b></td>
        </tr>
        <tr>
          <td width="18%" align="right" valign="top"><b>&nbsp;&nbsp;</b><b>$ps_vendor_id:</b></td>
          <td width="36%" valign="top"><b><?php
            echo $ps_vendor_id; 
            ?> &nbsp;&nbsp;</b></td>
        </tr>
        <tr> 
          <td width="14%" align="right" valign="top"><b>$dir_perms:</b></td>
          <td width="32%" valign="top"><b><?php echo $dir_list["perms"]; ?>&nbsp;</b></td>
        </tr>
        </tr>
          <td width="18%" align="right" valign="top"><b>$error:</b></td>
          <td width="36%" valign="top"><b><?php
            echo $error; 
            ?> &nbsp;&nbsp;</b></td>
        </tr>
        <?php if ($_POST) { ?>
        <tr> 
          <td align="right"><b>POST VARS:</b></td>
          <td><b>&nbsp;<?php   while (list($val,$key) = each($_POST)) {
                                echo "$val=>$key<br />";
                             }
                          ?></b></td>
        </tr><?php }
                            if ($_REQUEST) { ?>
        <tr>
          <td align="right">&nbsp;<b>REQUEST-VARS:</b></td>
          <td><b><?php   while (list($val,$key) = each($_REQUEST)) {
                                    echo "$val=>$key<BR>";
                                 }
                              ?></b></td>
        </tr><?php }
                            if ($vars) { ?>
        <tr>
          <td align="right"><b>$vars:</b></td>
          <td><b><?php   while (list($val,$key) = each($vars)) {
                                  echo "$val=>$key<br />";
                               }
                            ?></b></td>
        </tr>
        <?php } ?>
      </table>
    </td>
  </tr>
</table>
