<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: product.csv_upload.php,v 1.4.2.1 2006/03/10 15:55:15 soeren_nb Exp $
* @package VirtueMart
* @subpackage html
* @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/
mm_showMyFileName( __FILE__ );

require_once( CLASSPATH . "ps_csv.php" );
$ps_csv =& new ps_csv();

if ( (float)substr(phpversion(), 0, 3) >= 4.3) {
  $show_fec = true;
  $cols = 4;
}
else {
  $show_fec = false;
  $cols = 2;
}
?>
<img src="<?php echo IMAGEURL ?>ps_image/csv.gif" alt="CSV Upload" border="0" />
<span class="sectionname"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_CSV_UPLOAD ?></span><br /><br />

<?php 
$tabs = new mShopTabs(0, 1, "_csv");
$tabs->startPane("uploadform-pane");
$tabs->startTab( $VM_LANG->_PHPSHOP_CSV_IMPORT_EXPORT, "uploadform" ); 
?>
<table class="adminform" border="0">
    <tr>
      <td rowspan="2" width="50%">
        <table style="border-right: 1px solid;" class="adminform">
          <tr><th colspan="<?php echo $cols; ?>"><?php echo $VM_LANG->_PHPSHOP_CSV_SETTINGS ?></th></tr>
          <tr>
          <td valign="top" width="15%" align="right">
          <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>" name="adminForm" enctype="multipart/form-data">
              <input type="hidden" name="func" value="product_csv" />
              <input type="hidden" name="task" value="" />
              <input type="hidden" name="page" value="product.mycsv" />
              <input type="hidden" name="option" value="com_virtuemart" />
              <input type="hidden" name="no_html" value="0" />
              <?php echo $VM_LANG->_PHPSHOP_CSV_DELIMITER ?>:
            </td>
            <td valign="top" width="5%">
              <input type="radio" name="csv_delimiter" checked="checked" value="," />
                <span class="sectionname">,</span><br />
              <input type="radio" name="csv_delimiter" value=";" />
                <span class="sectionname">;</span>
            </td>
            <?php
            if( $show_fec ) {
      ?>
              <td valign="top" width="10%" align="right"><?php echo $VM_LANG->_PHPSHOP_CSV_ENCLOSURE ?>:</td>
              <td valign="top" width="15%">
                  <input type="radio" name="csv_enclosurechar" checked="checked" value='"' />
                    <span class="sectionname">"</span><br />
                  <input type="radio" name="csv_enclosurechar" value="'" />
                    <span class="sectionname">'</span><br />
                  <input type="radio" name="csv_enclosurechar" value="" />
                    none
              </td>
              <?php 
            }
              ?>
            </tr>
            <tr>
              <td colspan="<?php echo $cols; ?>"><input type="checkbox" id="skip_first_line" name="skip_first_line" value="Y" /><label for="skip_first_line">Skip first line</label>
              </td>
            </tr>
            <tr><th colspan="<?php echo $cols; ?>"><?php echo $VM_LANG->_PHPSHOP_CSV_UPLOAD_FILE ?></th></tr>
            <tr>
              <td align="center" colspan="<?php echo $cols; ?>">
                    <input type="file" name="file" />
                    <br />
                    <a href="#" onclick="javascript: document.adminForm.func.value='product_csv'; document.adminForm.no_html.value='';document.adminForm.local_csv_file.value='';submitbutton();" >
                    <img alt="Import" border="0" src="<?php echo $mosConfig_live_site ?>/administrator/images/upload_f2.png" align="center" /><?php echo $VM_LANG->_PHPSHOP_CSV_SUBMIT_FILE ?></a>
              </td>
            </tr>
            <tr>
              <td align="center" colspan="<?php echo $cols; ?>">
                    <hr/>
              </td>
            </tr>
            <tr>
              <th colspan="<?php echo $cols; ?>"><?php echo $VM_LANG->_PHPSHOP_CSV_FROM_DIRECTORY ?></th>
            </tr>
            <tr>
              <td align="center" colspan="<?php echo $cols; ?>">
                    <input type="text" size="60" value="<?php echo realpath($mosConfig_absolute_path."/media") ?>" name="local_csv_file" /><br />
                    <a href="#" onclick="javascript: document.adminForm.func.value='product_csv'; document.adminForm.no_html.value='';submitbutton();" >
                    <img alt="Import" border="0" src="<?php echo $mosConfig_live_site ?>/administrator/images/upload_f2.png" align="center" /><?php echo $VM_LANG->_PHPSHOP_CSV_FROM_SERVER ?></a>
  
              </td>
            </tr>
          </table>
        </td>
        <th align="center" width="50%"><?php echo $VM_LANG->_PHPSHOP_CSV_EXPORT_TO_FILE ?></th>
      </tr>
      <tr>
        <td valign="top"><strong><?php echo $VM_LANG->_PHPSHOP_CSV_SELECT_FIELD_ORDERING ?></strong><br/><br/>
          <input type="radio" id="use_standard_order_yes" name="use_standard_order" value="Y" /><label for="use_standard_order_yes"><?php echo $VM_LANG->_PHPSHOP_CSV_DEFAULT_ORDERING ?></label>&nbsp;&nbsp;
          <input type="radio" id="use_standard_order_no" name="use_standard_order" checked="checked" value="N" /><label for="use_standard_order_no"><?php echo $VM_LANG->_PHPSHOP_CSV_CUSTOMIZED_ORDERING ?></label>
          <br/><br/><br/>&nbsp;&nbsp;&nbsp;&nbsp;
          <a href="#" onclick="javascript: document.adminForm.func.value='export_csv'; document.adminForm.no_html.value='1';submitbutton();" >
          <img alt="Export" border="0" src="<?php echo $mosConfig_live_site ?>/administrator/images/backup.png" align="center" /><?php echo $VM_LANG->_PHPSHOP_CSV_SUBMIT_EXPORT ?></a>
        </td>
      </tr>
  </table>
</form>
<?php 
$tabs->endTab();
$tabs->startTab( $VM_LANG->_PHPSHOP_CONFIG, "field_list" ); 
?>
  <h2><?php echo $VM_LANG->_PHPSHOP_CSV_CONFIGURATION_HEADER ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <a class="toolbar" onclick="document.fieldUpdate.submit();" style="cursor:pointer;" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('saveForm','','<?php echo $mosConfig_live_site ?>/administrator/images/save_f2.png',1);"><img src="<?php echo $mosConfig_live_site."/administrator/images/save.png" ?>" name="saveForm" align="center"  border="0" />
    &nbsp;&nbsp;<?php echo $VM_LANG->_PHPSHOP_CSV_SAVE_CHANGES ?></a>
  </h2>
  <br />
  
  <table class="adminlist">
    <tr>
      <th>#</th>
      <th><?php echo $VM_LANG->_PHPSHOP_CSV_FIELD_NAME ?></th>
      <th><?php echo $VM_LANG->_PHPSHOP_CSV_DEFAULT_VALUE ?></th>
      <th><?php echo $VM_LANG->_PHPSHOP_CSV_FIELD_ORDERING ?></th>
      <th><?php echo $VM_LANG->_PHPSHOP_CSV_FIELD_REQUIRED ?></th>
      <th><?php echo $VM_LANG->_PHPSHOP_DELETE ?></th>
    </tr>
    <form name="fieldUpdate" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
      
      <input type="hidden" name="option" value="com_virtuemart" />
      <input type="hidden" name="func" value="csvFieldUpdate" />
      <input type="hidden" name="page" value="product.csv_upload" />
    <?php
    $db->query( "SELECT * FROM #__{vm}_csv ORDER BY field_ordering" );
    $i = 1;
    while( $db->next_record() ) { ?>
    
      <tr>
        <td><?php echo $i++ ?></td>
        <td><?php
        if( in_array( $db->f( "field_name"), $ps_csv->reserved_words ))
          echo $db->f("field_name")."<input type=\"hidden\" name=\"field[$i][_name]\" value=\"".$db->f("field_name")."\" />";
        else
          echo "<input type=\"text\" name=\"field[$i][_name]\" value=\"".$db->f("field_name") ."\" />";
        ?>
        </td>
        <td><input type="text" name="field[<?php echo $i ?>][_default_value]" value="<?php $db->p( "field_default_value") ?>" /></td>
        <td><input type="text" name="field[<?php echo $i ?>][_ordering]" value="<?php $db->p( "field_ordering") ?>" size="4" /></td>
        <td><?php
        if( in_array( $db->f( "field_name"), $ps_csv->reserved_words ))
          echo $VM_LANG->_PHPSHOP_ADMIN_CFG_YES."<input type=\"hidden\" name=\"field[$i][_required]\" value=\"Y\" />\n";
        else { 
        ?>
          <select name="field[<?php echo $i ?>][_required]">
            <option value="Y" <?php if($db->f( "field_required")=="Y") echo "selected=\"selected\"" ?>><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_YES ?></option>
            <option value="N" <?php if($db->f( "field_required")=="N") echo "selected=\"selected\"" ?>><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_NO ?></option>
          </select>
        <?php
        } 
        ?>
        </td>
        <td><?php
        if( !in_array( $db->f( "field_name"), $ps_csv->reserved_words )) { ?>
            <a class="toolbar" href="index2.php?option=com_virtuemart&page=<?php echo $_REQUEST['page'] ?>&func=csvFieldDelete&field_id=<?php echo $db->f("field_id") ?>" onclick="return confirm('<?php echo $VM_LANG->_PHPSHOP_DELETE_MSG ?>');" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('Delete<?php echo $i ?>','','<?php echo IMAGEURL ?>ps_image/delete_f2.gif',1);"><img src="<?php echo IMAGEURL ?>ps_image/delete.gif" alt="Delete this record" name="Delete<?php echo $i ?>" align="middle" border="0"/>
            </a>
        <?php
        } 
        ?>
        </td>
      </tr>
      <input type="hidden" name="field[<?php echo $i ?>][_id]" value="<?php $db->p("field_id") ?>" />
    <?php
    }
    ?>
    </form>
  </table><br/>
  <a style="cursor:pointer;" onclick="addField();" class="toolbar" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('newField','','<?php echo $mosConfig_live_site."/administrator/images/new_f2.png" ?>',1);">
    <img src="<?php echo $mosConfig_live_site."/administrator/images/new.png" ?>" name="newField" border="0" />
    &nbsp;<?php echo $VM_LANG->_PHPSHOP_CSV_NEW_FIELD ?>
  </a>
  <form name="fieldAdd" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
  <input type="hidden" name="option" value="com_virtuemart" />
  <input type="hidden" name="func" value="csvFieldAdd" />
  <input type="hidden" name="page" value="product.csv_upload" />
  <div id="newfieldspace"></div>
  </form>
<?php 
$tabs->endTab();
$tabs->startTab( $VM_LANG->_PHPSHOP_CSV_DOCUMENTATION, "doc-page" ); 
?>
<div align="left">
  <br/><br/><br/>
  First you need a csv file with the required product information.
  <br />
  <strong>Example File:</strong><br />
  <div class="quote" style="max-width:600px;overflow:scroll;">
  <pre><?php echo htmlentities('"G01","<p>Nice hand shovel to dig with in the yard.</p>","<ul>  <li>Hand crafted handle with maximum grip torque  </li><li>Titanium tipped shovel platter  </li><li>Half degree offset for less accidents  </li><li>Includes HowTo Video narrated by Bob Costas  </li></ul>    <b>Specifications</b><br>  5\" Diameter<br>  Tungsten handle tip with 5 point loft<br>","8d886c5855770cc01a3b8a2db57f6600.jpg","cca3cd5db813ee6badf6a3598832f2fc.jpg","10.0000","pounds","0.0000","0.0000","0.0000","inches","10","1072911600","Y","1","Hand Shovel","4.99000","Hand Tools","1","2","0","G01","","","Color::1|Size::2",""
"G02","A really long ladder to reach high places.","<ul>  <li>Hand crafted handle with maximum grip torque  </li><li>Titanium tipped shovel platter  </li><li>Half degree offset for less accidents  </li><li>Includes HowTo Video narrated by Bob Costas  </li></ul>    <b>Specifications</b><br>  5\" Diameter<br>  Tungsten handle tip with 5 point loft<br>","ffd5d5ace2840232c8c32de59553cd8d.jpg","8cb8d644ef299639b7eab25829d13dbc.jpg","10.0000","pounds","0.0000","0.0000","0.0000","inches","76","1072911600","N","0","Ladder","49.99000","Garden Tools","1","2","0","G02","","","Material::1",""
"G03","Nice shovel.  You can dig your way to China with this one.","<ul>  <li>Hand crafted handle with maximum grip torque  </li><li>Titanium tipped shovel platter  </li><li>Half degree offset for less accidents  </li><li>Includes HowTo Video narrated by Bob Costas  </li></ul>    <b>Specifications</b><br>  5\" Diameter<br>  Tungsten handle tip with 5 point loft<br>","8147a3a9666aec0296525dbd81f9705e.jpg","520efefd6d7977f91b16fac1149c7438.jpg","10.0000","pounds","0.0000","0.0000","0.0000","inches","32","1072911600","N","0","Shovel","24.99000","Garden Tools","1","2","0","G03","Size,XL[+1.99],M,S[-2.99];Colour,Red,Green,Yellow,ExpensiveColor[=24.00]","","",""</div>');
?></pre></div>
  <br />
  <strong>Please check the Tab "Configuration" above to see the list of all the fields you can include in the CSV file.
  
  <br/><br/>Minimum required information is</strong><br />
    product_sku<br />
    product_name<br />
    category_path<br />
  <br />
  But you must have 25 (this is the default setting) fields!<br />
  <br />
  <span class="message">It's important to provide all the optional fields even when they are empty!</span>
  (just write <strong>,"",</strong> then)
  <br />
  <br />
  <br />
  <strong>category_path</strong> is a slash delimited string which begins
  with a top-level category and follows with sub-categories, e.g. <br />
  <div class="quote">category/sub-category_1/sub_category_2</div><br />
  When the product has to be assigned to more than one category, you can provide all categories,<br />
  delimited by a <strong>|</strong>
  <div class="quote">Category/Sub-category_1/Sub_category_2|Category2/Subcategory22|Category3/Subcategory33</div>
  <br/>
  <strong>product_thumb_image</strong> and <strong>product_full_image</strong>
  are the names of the respective image files. <br/>You will
  need to FTP the image directly to the <i>/shop_image/product</i> folder.<br /><br />
  
  You can add new fields to the list of CSV fields, but please note that the field name must
  match a name of a field in the table <strong>mos_{vm}_csv</strong>!<br/>
  You can change the ordering of all the fields just as you need it.
</div>
<?php 
$tabs->endTab();
$tabs->endPane();
?>
<script type="text/javascript">
function addField() {
  if( !called ) {
    document.getElementById( 'newfieldspace').innerHTML += '<a onclick="document.fieldAdd.submit();" class="toolbar" style="cursor:pointer;" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage(\'saveForm2\',\'\',\'<?php echo $mosConfig_live_site ?>/administrator/images/save_f2.png\',1);"><img src="<?php echo $mosConfig_live_site."/administrator/images/save.png" ?>" name="saveForm2" align="center"  border="0" />'
    +'&nbsp;&nbsp;<?php echo $VM_LANG->_PHPSHOP_CSV_SAVE_CHANGES ?></a>';
  }
  document.getElementById( 'newfieldspace').innerHTML += '<table class="adminForm"><tr>'
       +' <td><input type="text" name="field['+fieldNum+'>][_name]" /><br/><span class="smallgrey"><?php echo $VM_LANG->_PHPSHOP_CSV_FIELD_NAME ?></span></td>'
       +' <td><input type="text" name="field['+fieldNum+'>][_default_value]" /><br/><span class="smallgrey"><?php echo $VM_LANG->_PHPSHOP_CSV_DEFAULT_VALUE ?></span></td>'
       +' <td><input type="text" name="field['+fieldNum+'>][_ordering]" size="4" /><br/><span class="smallgrey"><?php echo $VM_LANG->_PHPSHOP_CSV_FIELD_ORDERING ?></span></td>'
       +' <td align="right"><select name="field['+fieldNum+'>][_required]">'
       +'       <option value="Y"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_YES ?></option>'
       +'       <option value="N" selected="selected"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_NO ?></option>'
       +'     </select><br/>&nbsp;&nbsp;&nbsp;<span class="smallgrey"><?php echo $VM_LANG->_PHPSHOP_CSV_FIELD_REQUIRED ?></span>'
       +' </td></tr></table>';
  called = true;
  fieldNum++;
}
var called = false;
var fieldNum = 0;
</script>
