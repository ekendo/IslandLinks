<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_affiliate.php,v 1.5.2.1 2006/04/05 18:16:52 soeren_nb Exp $
* @package VirtueMart
* @subpackage classes
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

class ps_affiliate {
  var $classname = "ps_affiliate";
  var $error;

  /**************************************************************************
   * name: add()
   * created by: pablo
   * description: adds a shopper to a vendor
   * parameters:
   * returns:
   **************************************************************************/
  function add(&$d) {
#    global $ps_user;
    $db = new ps_DB;
    
    if (empty($d['user_info_id'])) {
      $d['error'] = "Please provide an user info id!";
      return false;
    }
    $q = "SELECT user_id FROM #__{vm}_affiliate WHERE user_id='".$d['user_info_id']."'";
    $db->query( $q );
    
    if ($db->next_record()) {
        $d['error'] = "The given user id already is an affiliate!";
        return false;
    }
    else {
        $q = "INSERT INTO #__{vm}_affiliate (user_id, active, rate) ";
        $q .= "VALUES ('". $d["user_info_id"] ."','Y','5')";
        $db->query($q);
      
        return True;
    }
    
  }
 
   /**************************************************************************
   * name: delete()
   * created by: soeren
   * description: deletes an affiliate
   * parameters:
   * returns:
   **************************************************************************/
  function delete(&$d) {
#    global $ps_user;
    $db = new ps_DB;
    
    if (empty($d['user_info_id'])) {
      $d['error'] = "Please provide an user info id!";
      return false;
    }
	if( is_array( $d["user_info_id"] )) {
		foreach( $d["user_info_id"] as $affiliate ) {
			$q = "DELETE FROM #__{vm}_affiliate WHERE user_id ='". $affiliate ."' ";
			$db->query($q);		
		}
	}
	else {
        $q = "DELETE FROM #__{vm}_affiliate WHERE user_id ='". $d["user_info_id"] ."' ";
        $db->query($q);
	}
        return True;

    
  }
  
   /**************************************************************************
   ** name: visit_register
   ** created by: SP Bridgewater
   ** description: registers the visit from an affiliates site
   ** parameters: none
   ** returns: True;
   **************************************************************************/ 

   
   function visit_register()
   {
    global $sess,$afid,$page;
    $timestamp = time();
    $db = new ps_DB;
    $q = "INSERT INTO #__{vm}_visit (visit_id, affiliate_id,pages,entry_page,exit_page,sdate,edate)";
    $q .=" VALUES ('".session_id()."','".$afid."','1','".$page."',";
    $q .="'".$page."','".$timestamp."','".$timestamp."'";
    $q .=")";
    $db->query($q);
    $db->next_record();	          
	return True;
   }


   /**************************************************************************
   ** name: visit_update
   ** created by: SP Bridgewater
   ** description: updates the visitor record for the affiliate
   ** parameters: none
   ** returns: True;
   **************************************************************************/ 

   function visit_update(){
    global $sess,$afid,$page;
	$timestamp = time();
    $db = new ps_DB;
    $q = "UPDATE #__{vm}_visit SET pages = pages +1, edate= '".$timestamp."'";
    $q .=",exit_page = '".$page."'"; 
    $q .=" WHERE visit_id = '".session_id()."'";
    $db->query($q);
    return True;
	 
  } 

 
   /**************************************************************************
   ** name: update
   ** created by: SP Bridgewater
   ** description: updates the affiliate details
   ** parameters: none
   ** returns: True;
   **************************************************************************/ 

  function update(&$d)
  {
    
    $db = new ps_DB;
	$q = "UPDATE #__{vm}_affiliate SET";
	$q .=" rate = '".$d["rate"]."', active = ";
	$d["active"] == 'on' ? $q .="'Y'" :$q .="'N'";
	$q .=" WHERE affiliate_id ='".$d["affiliate_id"]."'";
	$db->query($q);
    return True;
  } 
  
   /**************************************************************************
   ** name: register sale
   ** created by: SP Bridgewater
   ** description: registers the sale with the affiliate visit
   ** parameters: order_id
   ** returns: True;
   **************************************************************************/ 

  function register_sale($order_id){

    if (isset($_SESSION['afid'])) {
    $afid = $_SESSION['afid'];
     
    $db = new ps_DB;
	$q = "SELECT rate FROM #__{vm}_affiliate ";
	$q .=" WHERE affiliate_id = '".$afid."'";
	$db->query($q);
	$db->next_record();
	$rate = $db->f("rate");
    $q = "INSERT into #__{vm}_affiliate_sale(affiliate_id, order_id,visit_id,rate)";
	$q .=" VALUES('".$afid."','".$order_id."','".session_id()."','".$rate."')";
    $db->query($q);
    }
   return True;
  } 

  /**************************************************************************
   * name: list_order
   * created by: spb
   * description: shows a listbox of orders which can be used in a form
   * parameters: set to order_id
   * returns:
   **************************************************************************/
  function list_order($order_status=A, $secure=0, $date=0) {
    global $ps_vendor_id;
    $auth = $_SESSION['auth'];
    
    $db = new ps_DB;
    $i = 0;
	$order_total = 0;
    	
	
	//if month as not been passed then view current month
	 if($date == 0){
		 $month = date("n");
		 $year = date("Y");
		
	 }
	 else{
	   $month = date("n",$date);
	   $year = date("Y",$date);
	  }
   $start_date = mktime(0,0,0,$month,1,$year);
	 $end_date = mktime(24,0,0,$month+1,0,$year);
	
	//get the affiliate id from affiliate table for this user
	$q =  "SELECT affiliate_id,rate FROM #__{vm}_affiliate, #__users";
	$q .= " WHERE #__{vm}_affiliate.user_id = #__users.user_info_id";
	$q .= " AND #__users.id = '".$auth["user_id"]."'";
	
	$db->query($q);
	if($db->next_record()){
			$affiliate = $db->f("affiliate_id");
		   	$q = "SELECT * FROM #__{vm}_orders,#__{vm}_affiliate_sale ";
	    	$q .= "WHERE vendor_id='$ps_vendor_id' ";
			$q .= "AND #__{vm}_affiliate_sale.order_id = #__{vm}_orders.order_id ";
			$q .= "AND affiliate_id = '$affiliate' ";
			$q .= "AND cdate BETWEEN $start_date AND $end_date ";
	    	$q .= " GROUP BY #__{vm}_orders.order_id ";
			$q .= " ORDER BY #__{vm}_orders.cdate DESC ";
		   	$db->query($q);
	    	echo "<select name=order_id size=" . MAX_ROWS . ">";;
	    	while ($db->next_record()) {
	      	$i++;
		  	echo "<option value=" . $db->f("order_id") . ">";
	      	printf("%08d", $db->f("order_id"));
	      	echo "&nbsp;&nbsp;&nbsp;&nbsp;";
	      	echo date("dMY.H:i", $db->f("cdate"));
	      	echo "&nbsp;&nbsp;&nbsp;";
	      	echo $db->f("order_status") . "&nbsp; &nbsp;";
	      	echo "&nbsp;&nbsp;";
	      	echo $db->f("order_subtotal"). "&nbsp; &nbsp;";
		  	$order_total += $db->f("order_subtotal");
		  	printf("%1.2f",$db->f("order_subtotal")*($db->f("rate")/100));
	        echo "</OPTION>";
	        }
	       if (!$i) {
	         echo "<option>---------------------- No Orders to Display ------------------</option>\n";
	       }
	    	echo "</SELECT>\n";    

		  }
  }
  
  /**************************************************************************
  ** name: get_affiliate_details
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/    
  function get_affiliate_details($user_id,$affiliate_id='0')
  {
   global $auth;
   
    $db = new ps_DB;
   
   //get the affiliate id from affiliate table for this user
	$q =  "SELECT affiliate_id,rate,company,user_email FROM `#__{vm}_affiliate`, `#__{vm}_user_info`";
	$q .= " WHERE #__{vm}_affiliate.user_id = #__{vm}_user_info.user_info_id";
	if(!$affiliate_id){
		$q .= " AND #__{vm}_user_info.user_id = '".$auth["user_id"]."'";
	}
	else {
		$q .=" AND #__{vm}_affiliate.affiliate_id = '".$affiliate_id."'";
	}
	$db->query($q);
    if($db->next_record()){
	 $affiliate["id"] = $db->f("affiliate_id");
	 $affiliate["rate"] = $db->f("rate");
	 $affiliate["company"] = $db->f("company");
	 $affiliate["email"] = $db->f("user_email");
	 return $affiliate; 
	}
	else{
	 return NULL;
	}
	
    return False;
  }
  
  
  /**************************************************************************
  ** name: get_stats
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/    
  function get_stats($date=0,$affiliate_id){
    $company_details = $this->get_affiliate_details(0,$affiliate_id);
    $affiliate_details=$this->get_details(time(),$affiliate_id);
    $stats_string = "Affiliate statistics for ".$company_details["company"]." : ".date("F Y")."\n";
	$stats_string .="---------------------------------------------------------------------\n";
	$stats_string .="Your affiliate id            =".$company_details["id"]."\n";
	$stats_string .="Your current commission rate = ".$company_details["rate"]."%\n";
	$stats_string .="-----------------------------------------\n";
	$stats_string .="Number of referrals    = " .$affiliate_details["visitors"]."\n"; 
	$stats_string .="Number of pages viewed = " .$affiliate_details["pages"]."\n";
	$stats_string .="Number of orders made  = " .$affiliate_details["orders_made"]."\n"; 
	$stats_string .="Total order revenue    = " .$affiliate_details["orders_total"]."\n"; 
	$stats_string .="-----------------------------------------\n";
	$stats_string .="Commission earned      = " .$affiliate_details["commission_total"]."\n"; 
	$stats_string .="-----------------------------------------\n";
	$stats_string .="Please note that commission will only be\n";
	$stats_string .="paid on orders that have been completed.\n";
	$stats_string .="These details are to provide a summary of\n";
	$stats_string .="the commission earned when all orders have\n";
	$stats_string .="been completed and shipped.\n";	
	return $stats_string;
    
  }
  
  /**************************************************************************
  ** name: email
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/    
  function email(&$d)
  {
   global $email_status,$ps_vendor_id;
   $db = new ps_DB;
   $dbv = new ps_DB;
   
   $qt = "SELECT * from #__{vm}_vendor ";
   $qt .= "WHERE vendor_id = $ps_vendor_id";
   $dbv->query($qt);
   $dbv->next_record();
   
   $q ="SELECT * from #__{vm}_affiliate ";
   $q .=" WHERE active ='Y' ";
   if($d["affiliate_id"] != "*"){
     $q .="AND affiliate_id = '".$d["affiliate_id"]."'";
   }
  
  $db->query($q);
  while($db->next_record()){
   $i++;
   if($d["send_stats"]=="stats_on"){
   	$d["email"].="\n\n\n".$this->get_stats(time(),$db->f("affiliate_id"));
    }
     
  
   $affiliate = $this->get_affiliate_details(0,$db->f("affiliate_id"));
 
    if(!mail($affiliate["email"],$d["subject"],$d["email"],$dbv->f("contact_email")))
	{
	 $email_status ="Failed";
	}
	else
	 {
	  $j++;
	 }
	
   	    
  }
  
  if($i==$j){
   $email_status = "Emailed $i affiliates successfully - Email more ....";
  }
  
 
   

  }
  
  /**************************************************************************
  ** name: get_affiliate_list
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/    
  function get_affiliate_list($affiliate_active='Y')
  {
    global $ps_vendor_id;
   $db = new ps_DB;
    $i = 0;

    	
	
	//get the affiliate id from affiliate table for this user
	$q =  "SELECT affiliate_id,first_name,last_name,name,username FROM #__{vm}_affiliate, #__users";
	$q .= " WHERE #__{vm}_affiliate.user_id = #__users.user_info_id";
    if($affiliate_active  == 'Y'){
	$q .=" AND active = 'Y' ";
	}
	
	
	$db->query($q);
	    	echo "<select name=\"affiliate_id\" size=\"1\">";
			echo "<option value =\"*\">*</option>";
	    	while ($db->next_record()) {
	      	$i++;
		  	echo "<option value=" . $db->f("affiliate_id") . ">";
        echo $db->f("first_name") ." ". $db->f("first_name") ." (".$db->f("username").")";
	        echo "</option>";
			
	        }
	       if (!$i) {
	         echo "<option>---------------------- No Affiliates to Display ------------------</option>\n";
	       }
	    	echo "</select>\n";    

		  
   
  }
  
  
  

  /**************************************************************************
  ** name: get_details
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/    
  function get_details($date,$affiliate_id = 0)
  {
    
    global $ps_vendor_id;
    $auth = $_SESSION['auth'];
    $db = new ps_DB;
    $i = 0;
	
	//if month as not been passed then view current month
	 if($date == 0){
		 $month = date("n");
		 $year = date("Y");
		
	 }
	 else{
	   $month = date("n",$date);
	   $year = date("Y",$date);
	  }
	 $start_date = mktime(0,0,0,$month,1,$year);
	 $end_date = mktime(24,0,0,$month+1,0,$year);

	//get the affiliate id from affiliate table for this user
	
	if($affiliate_id ==0){
		$q =  "SELECT affiliate_id,rate FROM #__{vm}_affiliate, #__users";
		$q .= " WHERE #__{vm}_affiliate.user_id = #__users.user_info_id";
		$q .= " AND #__users.id = '".$auth["user_id"]."'";	
		
	}
	else{
	   $q =  "SELECT affiliate_id,rate FROM #__{vm}_affiliate";
	   $q .= " WHERE affiliate_id = '".$affiliate_id."'";
	}
	
	$db->query($q);
	
    if($db->next_record()){
      $affiliate["id"] = $db->f("affiliate_id");
	  $affiliate["rate"] = $db->f("rate");
		
	//get the orders for this month
	$q = "SELECT affiliate_id, COUNT(#__{vm}_affiliate_sale.order_id) AS orders_made,";
	$q .="SUM(order_subtotal) AS order_total, ";
	$q .="SUM(order_subtotal*(rate*0.01)) AS commission FROM #__{vm}_orders,#__{vm}_affiliate_sale";
	$q .=" WHERE #__{vm}_orders.order_id = #__{vm}_affiliate_sale.order_id";
	$q .=" AND #__{vm}_affiliate_sale.affiliate_id = '".$affiliate["id"]."'";
	$q .= "AND #__{vm}_orders.cdate BETWEEN $start_date AND $end_date ";
	$q .=" GROUP BY affiliate_id";
	$db->query($q);
		
	if($db->next_record()){
	   if($db->f("orders_made"))
 	       $affiliate["orders_made"] = $db->f("orders_made");
	   else
	   	   $affiliate["orders_made"] = "none";
	  
	   if($db->f("order_total"))
	       $affiliate["orders_total"]=$db->f("order_total");
	    else
	      $affiliate["orders_total"] = "none";
		  
	   if($db->f("commission"))
	      $affiliate["commission_total"] = $db->f("commission");
	   else	
	      $affiliate["commission_total"] = "none";  
	}
	
	
	
	//query the visit table
	$q = "SELECT  count(affiliate_id) AS visitors,sum(pages) AS pages_viewed FROM #__{vm}_visit ";
	$q .=" WHERE affiliate_id = '".$affiliate["id"]."'";
	$q .= "AND sdate BETWEEN $start_date AND $end_date ";
	$q .=" GROUP BY affiliate_id";
	$db->query($q);

	if($db->next_record()){
	   if($db->f("visitors"))
	     $affiliate["visitors"] = $db->f("visitors");
	   else
	     $affiliate["visitors"] = "none";
	   if($db->f("pages_viewed"))
	   	$affiliate["pages"] = $db->f("pages_viewed");
	   else
	     $affiliate["pages"] = "none";
	   
	  
	}
	 return $affiliate;
   }
		
	  
  return False;
  }
  
}
?>
