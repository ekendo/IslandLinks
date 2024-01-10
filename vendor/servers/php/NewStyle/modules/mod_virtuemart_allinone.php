<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/* 
* All-in-one module for VirtueMart
* includes:
    Latest Products Manager
    Top Ten Products Manager
    Special Products ManagerM
    (All Modules originally designed by Mr PHP)
*
* @version $Id: mod_virtuemart_allinone.php,v 1.1.2.1 2006/02/18 09:20:11 soeren_nb Exp $
* @package VirtueMart
* @subpackage modules
*
* Conversion to Mambo and the rest:
* 	@copyright (C) 2004-2005 Soeren Eberhardt
*
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* VirtueMart is Free Software.
* VirtueMart comes with absolute no warranty.
*
* www.virtuemart.net
*/

global $mosConfig_absolute_path;

/* get parameters */
$show_new = $params->get( 'show_new', 0 );
$show_topten = $params->get( 'show_topten', 0 );
$show_special = $params->get( 'show_special', 0 );
$show_random = $params->get( 'show_random', 0 );

if( empty($show_price))
  $show_price = (bool)$params->get( 'show_price', 1 ); // Display the Product Price?
if( empty($show_addtocart))
  $show_addtocart = (bool)$params->get( 'show_addtocart', 1 ); // Display the "Add-to-Cart" Link?
  
$count_mods = $show_new + $show_topten + $show_special + $show_random;
$max_mods = $count_mods;

// check if parameters are given
// if no, give default values
if ($count_mods == 0) { 
    $max_mods = $count_mods = 3;
    $show_new = '1';
    $show_topten = '1';
    $show_special = '1';
}

/* Load the virtuemart main parse code */
require_once( $mosConfig_absolute_path.'/components/com_virtuemart/virtuemart_parser.php' );


?>

<style>
.ontab {
	font-family : Verdana, Arial, Helvetica, sans-serif;
	font-size: 8pt;
	background-color: #ffae00;
	border-left: outset 2px #ff9900;
	border-right: outset 2px #808080;
	border-top: outset 2px #ff9900;
	border-bottom: solid 1px #d5d5d5;
	width: 14%;
	text-align: center;
	cursor: hand;
	font-weight: bold;
	color: #FFFFFF;
}
.offtab {
	font-family : Verdana, Arial, Helvetica, sans-serif;
	font-size: 8pt;
        background-color : #e5e5e5;
	border-left: outset 2px #E0E0E0;
	border-right: outset 2px #E0E0E0;
	border-top: outset 2px #E0E0E0;
	border-bottom: solid 1px #d5d5d5;
	width: 14%;
	text-align: center;
	cursor: hand;
	font-weight: normal;
}
.tabheading {
	background-color: #ffae00;
	color: #FFFFFF;
	font-family : Verdana, Arial, Helvetica, sans-serif;
	font-size: 8pt;
	text-align: left;
}
.pagetext {
	visibility: hidden;
	display: none;
	position: relative;
	top: 0;
}
.number{
	font: 8pt Verdana, Geneva, Arial, Helvetica, sans-serif;
	text-align: center;
	text-decoration: underline;
	border-right-color: Black;
	border-right: 1px;
	border-style: none solid none none;
	}
.modtableborder {
	border-width: 1px;
        border-color: black;
        border-style: solid;
	}
</style>
<script language="javascript" src="includes/js/mambojavascript.js"></script>
<table cellspacing="0" cellpadding="1" border="0" width="100%">
  <tr>
    <td width="" class="tabpadding">&nbsp;</td>
    <?php if ($show_new == '1') {
        $new_number = $count_mods;
        $count_mods -= 1; ?>
    <td id="tab<?php echo $new_number ?>" style="cursor:pointer;" class="offtab" onClick="dhtml.cycleTab(this.id)"><?php echo _CMN_NEW ?>&nbsp;<br />
        <img src='<?php echo IMAGEURL ?>ps_image/new.png' border=0 alt='New' align='absmiddle'></td>
    <?php }
    
    if ($show_topten == '1') { 
        $topten_number = $count_mods;
        $count_mods -= 1; ?>
    <td id="tab<?php echo $topten_number ?>" style="cursor:pointer;" class="offtab" onClick="dhtml.cycleTab(this.id)">Top&nbsp;<br /><img src='administrator/images/upload_f2.png' border=0 alt='Top Ten' align='absmiddle'></td>
    <?php }
    
    if ($show_special == '1') { 
        $special_number = $count_mods;
        $count_mods -= 1;
    ?>
    <td id="tab<?php echo $special_number ?>" style="cursor:pointer;" class="offtab" onClick="dhtml.cycleTab(this.id)">Special&nbsp;<br /><img src='<?php echo IMAGEURL ?>ps_image/special.png' border=0 alt='Special' align='absmiddle'></td>
    <?php }
    
    if ($show_random == '1') { 
        $random_number = $count_mods;
        $count_mods -= 1;
    ?>
    <td id="tab<?php echo $random_number ?>" style="cursor:pointer;" class="offtab" onClick="dhtml.cycleTab(this.id)">Random&nbsp;<br /><img src='<?php echo IMAGEURL ?>ps_image/random.png' border=0 alt='Random' align='absmiddle'></td>
    <?php }
?>

    <td width="90%" class="tabpadding">&nbsp;</td>
  </tr>
</table>

    <?php if ($show_new == '1') { ?>
    
<div id="page<?php echo $new_number ?>" class="pagetext">
    <table cellspacing="0" cellpadding="1" width="100%" class="modtableborder">
    <?php
    //////////////////////////////
    // Latest Products
    //
    
    $max_items=2; //maximum number of items to display
    
    require_once ( CLASSPATH. 'ps_product.php');
    $ps_product = new ps_product;
        
    $db=new ps_DB;

    $q  = "SELECT * FROM #__{vm}_product, #__{vm}_product_category_xref, #__{vm}_category WHERE ";
    $q .= "product_parent_id=''";
    $q .= "AND #__{vm}_product.product_id=#__{vm}_product_category_xref.product_id ";
    $q .= "AND #__{vm}_category.category_id=#__{vm}_product_category_xref.category_id ";
    $q .= "AND #__{vm}_product.product_publish='Y' ";
    $q .= "ORDER BY #__{vm}_product.product_id DESC ";
    $q .= "LIMIT $max_items ";
    $db->query($q);
    
    $i = 0;
    if ($db->num_rows()!=0) { 
            
        while($db->next_record()){ 
        
            if ($i == 0) {
                $sectioncolor = "sectiontableentry2";
                $i += 1;
            }
            else {
                $sectioncolor = "sectiontableentry1";
                $i -= 1;
            } ?>
            <tr align="center" class="<?php echo $sectioncolor ?>">
              <td><?php
              $ps_product->show_snapshot($db->f("product_sku"), $show_price, $show_addtocart);
              ?><br /></td></tr><?php 
            }
            ?>
          </td>
        </tr>
    <?php } ?>
    </table>
</div>

<?php }

if ($show_topten == '1') { ?>

<div id="page<?php echo $topten_number ?>" class="pagetext">
  <table cellspacing="0" cellpadding="1" width="100%" class="modtableborder">
      <?php
      //////////////////////////////
      // Top Ten
      //
      
     require_once(CLASSPATH.'ps_product.php');
  $ps_product = new ps_product;
  
  require_once(CLASSPATH.'ps_product_attribute.php');
  $ps_product_attribute = new ps_product_attribute;
  
  require_once(CLASSPATH.'ps_product_category.php');
  $ps_product_category = new ps_product_category;
  
  // change the number of items you wanna haved listed via module parameters
  $num_topsellers = @$params->num_topsellers ? $params->num_topsellers : 10;
  
  ?>
  
  <!--Top 10-->
  <table border="0" cellpadding="2" cellspacing="0" width="100%">
    <tr>
        <td>
        <?php
        $list  = "SELECT #__{vm}_product.product_id, product_parent_id,product_name, #__{vm}_category.category_id, category_flypage ";
		$list .= "FROM #__{vm}_product, #__{vm}_product_category_xref, #__{vm}_category WHERE ";
		$q = "#__{vm}_product.product_publish='Y' AND ";
		$q .= "#__{vm}_product_category_xref.product_id = #__{vm}_product.product_id AND ";
		$q .= "#__{vm}_product_category_xref.category_id = #__{vm}_category.category_id AND ";
        $q .= "#__{vm}_product.product_sales>0 ";
        $q .= "ORDER BY #__{vm}_product.product_sales DESC";
        $list .= $q . " LIMIT 0, $num_topsellers "; 
        ?>
        
          <table border="0" cellpadding="0" cellspacing="0" width="100%" class="menu">
          <?php
          global $sess;
          $db = new ps_DB;
          $db->query($list);
          $tt_item=0;
          $i = 0;
          while ($db->next_record()) {
              if ($i == 0) {
                  $sectioncolor = "sectiontableentry2";
                  $i += 1;
              }
              else {
                  $sectioncolor = "sectiontableentry1";
                  $i -= 1;
              } 
              $flypage = $ps_product->get_flypage($db->f("product_id"));
              $tt_item++;
              $pid = $db->f("product_parent_id") ? $db->f("product_parent_id") : $db->f("product_id");
              ?>
            <tr class="<?php echo $sectioncolor ?>">
              <td valign="top"><?php printf("%02d", $tt_item); ?>&nbsp;<br /></td>
              <td valign="top">
                <a href="<?php  $sess->purl(URL . "?page=shop.product_details&flypage=$flypage&product_id=" . $pid . "&category_id=" . $db->f("category_id")) ?>">
                              <?php $db->p("product_name"); ?>
                                  </a><br />
              </td>
            </tr>
            <?php 
          } ?>
          </table>
        </td>
    </tr>
  </table>
  <!--Top 10 End-->
</div>
<?php }

if ($show_special == '1') { ?>

<div id="page<?php echo $special_number ?>" class="pagetext">


<table cellspacing="0" cellpadding="1" width="100%" class="modtableborder">

    <?php  //////////////////////////////
        // Featured / Special products
        //
        
        require_once ( CLASSPATH. 'ps_product.php');
        $ps_product = new ps_product;
        
        $db=new ps_DB;
        
        //$max_items = 2;
        
          $q  = "SELECT DISTINCT product_sku FROM #__{vm}_product, #__{vm}_product_category_xref, #__{vm}_category WHERE ";
          $q .= "(#__{vm}_product.product_parent_id='' OR #__{vm}_product.product_parent_id='0') ";
          $q .= "AND #__{vm}_product.product_id=#__{vm}_product_category_xref.product_id ";
          $q .= "AND #__{vm}_category.category_id=#__{vm}_product_category_xref.category_id ";
          $q .= "AND #__{vm}_product.product_publish='Y' ";
          $q .= "AND #__{vm}_product.product_special='Y' ";
          $q .= "ORDER BY product_name DESC ";
        //  $q .= "LIMIT $max_items ";
          $db->query($q);
          if($db->num_rows()!=0) { ?>
          <?php
          }
          $i = 0;
          
          while($db->next_record()){
          
            if ($i == 0) {
                $sectioncolor = "sectiontableentry2";
                $i += 1;
            }
            else {
                $sectioncolor = "sectiontableentry1";
                $i -= 1;
            } 
                
          ?>
                <tr align="center" valign="top" class="<?php echo $sectioncolor ?>">
                  <td><br />
                    <?php $ps_product->show_snapshot($db->f("product_sku"), $show_price, $show_addtocart); ?>
                  </td>
                </tr>
          <?php
          }
          ?>
    </table>
    
</div>

<?php }

if ($show_random == '1') { ?>

<div id="page<?php echo $random_number ?>" class="pagetext">
<table cellspacing="0" cellpadding="1" width="100%" class="modtableborder">

<?php  //////////////////////////////
    // Random products
    //
    require_once ( CLASSPATH. 'ps_product.php');
    $ps_product = new ps_product;
        
    $db=new ps_DB;
    
    $max_items=2; //maximum number of items to display


    $q  = "SELECT DISTINCT product_sku FROM #__{vm}_product, #__{vm}_product_category_xref, #__{vm}_category WHERE ";
    $q .= "product_parent_id=''";
    $q .= "AND #__{vm}_product.product_id=#__{vm}_product_category_xref.product_id ";
    $q .= "AND #__{vm}_category.category_id=#__{vm}_product_category_xref.category_id ";
    $q .= "AND #__{vm}_product.product_publish='Y' ";
    $q .= "ORDER BY product_name DESC";
    $db->query($q);
    
    $i=0;
    while($db->next_record()){
        $prodlist[$i]=$db->f("product_sku");
        $i++;
    }
    
    if($db->num_rows()!=0){ ?>
          
            <tr align="center">
              <td>
                <br><?php
    
        srand ((double) microtime() * 10000000);
        if (sizeof($prodlist)>1)
            $rand_prods = array_rand ($prodlist, $max_items);
        else
            $rand_prods = rand (4545.3545, $max_items);
            
        if($max_items==1){
          $ps_product->show_snapshot($prodlist[$rand_prods], $show_price, $show_addtocart);
          print "<br /><br />";
        }
        else{
          for($i=0; $i<$max_items; $i++){
            $ps_product->show_snapshot($prodlist[$rand_prods[$i]], $show_price, $show_addtocart);
            print "<br /><br />";
          }
        }
    
              ?>
              </td>
            </tr>
          
          <?php
      } ?>
      
     
    </table>
</div>
 <?php
    }
  ?>
 
<script language="javascript" type="text/javascript">
		dhtml.cycleTab('tab<?php echo $max_mods ?>');
</script>
