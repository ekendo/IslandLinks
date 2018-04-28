<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_reviews.php,v 1.7.2.2 2006/04/05 18:16:53 soeren_nb Exp $
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

class ps_reviews {
  

  
  function show_votes( $product_id ) {  
      echo ps_reviews::allvotes( $product_id );
  }
  
  function show_voteform( $product_id ) {
      echo ps_reviews::voteform( $product_id );
  }
    
  function show_reviews( $product_id ) {
    echo ps_reviews::product_reviews( $product_id );
  }
  
  function show_reviewform( $product_id ) {
      echo ps_reviews::reviewform( $product_id );
  }
  
  function allvotes( $product_id ) {
      global $db, $my, $VM_LANG;
      
      if (PSHOP_ALLOW_REVIEWS == "1") {
          
          $q = "SELECT votes, allvotes, rating FROM #__{vm}_product_votes "
                  . "WHERE product_id='$product_id' ";

          $db->query( $q );
          $allvotes = 0;
          $rating=0;
          if ( $db->next_record() ) {
            $allvotes = $db->f("allvotes");
            $rating = $db->f("rating");
          }
          $html = "<img src=\"".IMAGEURL."stars/$rating.gif\" align=\"middle\" border=\"0\" alt=\"$rating stars\" />&nbsp;";
          $html .= $VM_LANG->_PHPSHOP_TOTAL_VOTES.": ". $allvotes;
          return $html;
      }
  } 
  
  function voteform( $product_id ) {
      global $VM_LANG, $page, $my, $option;
      $html = "";
      if (PSHOP_ALLOW_REVIEWS == "1" && !empty($my->id)) { 
        $html = "<strong>". $VM_LANG->_PHPSHOP_CAST_VOTE .":</strong>&nbsp;&nbsp;
        <form method=\"post\" action=\"". URL ."index.php\">
            <select name=\"user_rating\" class=\"inputbox\">
                <option value=\"5\">5</option>
                <option value=\"4\">4</option>
                <option selected=\"selected\" value=\"3\">3</option>
                <option value=\"2\">2</option>
                <option value=\"1\">1</option>
                <option value=\"0\">0</option>
            </select>
            <input class=\"button\" type=\"submit\" name=\"submit_vote\" value=\"". $VM_LANG->_PHPSHOP_RATE_BUTTON."\" />
            <input type=\"hidden\" name=\"product_id\" value=\"$product_id\" />
            <input type=\"hidden\" name=\"option\" value=\"$option\" />
            <input type=\"hidden\" name=\"page\" value=\"$page\" />
            <input type=\"hidden\" name=\"category_id\" value=\"". @$_REQUEST['category_id'] ."\" />
            <input type=\"hidden\" name=\"Itemid\" value=\"". @$_REQUEST['Itemid'] ."\" />
            <input type=\"hidden\" name=\"func\" value=\"addVote\" />
        </form>";
      }
      return $html;
  }

  
  function product_reviews( $product_id, $limit=0 ) {
      global $db, $my, $VM_LANG;
      $html = "";
      if (PSHOP_ALLOW_REVIEWS == "1" ) {
		  $html = "<h4>".$VM_LANG->_PHPSHOP_REVIEWS.":</h4>";      
		  $dbc = &new ps_DB;
          $showall = mosgetparam( $_REQUEST, 'showall', 0);
          $q = "SELECT comment, time, userid, user_rating FROM #__{vm}_product_reviews WHERE product_id='$product_id' ORDER BY `time` DESC ";
          $count = "SELECT COUNT(*) as num_rows FROM #__{vm}_product_reviews WHERE product_id='$product_id'";
           
          if( $limit > 0 ) {
          	$q .= " LIMIT ".intval($limit);
          }
          elseif( !$showall ) {
          	$q .= " LIMIT 0, 5";
          }
          
          $dbc->query( $count );
          $num_rows = $dbc->f('num_rows');
          $dbc->query( $q );
          
          while( $dbc->next_record() ) {
				$i=0;
				$db->query("SELECT name FROM #__users WHERE id='".$dbc->f("userid")."'");
				$db->next_record();
				$html .= "<strong>". $db->f("name")."&nbsp;&nbsp;(". strftime (_DATE_FORMAT_LC, $dbc->f("time")).")</strong><br />";
				$html .= $VM_LANG->_PHPSHOP_RATE_NOM.": <img src=\"".IMAGEURL."stars/".$dbc->f("user_rating").".gif\" border=\"0\" alt=\"".$dbc->f("user_rating")."\" />";
				$html .= "<br />".$dbc->f("comment")."<br /><br />";
          }
          if( $num_rows < 1 ) {
              $html .= $VM_LANG->_PHPSHOP_NO_REVIEWS." <br />";
              if (!empty($my->id)) 
                $html .= $VM_LANG->_PHPSHOP_WRITE_FIRST_REVIEW;
              else 
                $html .= $VM_LANG->_PHPSHOP_REVIEW_LOGIN;
          }
          if( !$showall && $num_rows >=5 )
            $html .= "<a href=\"".$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']."&showall=1\">"._MORE."</a>";
      }
      return $html;
  }
  
  function reviewform( $product_id ) {
      global $db, $my, $page, $VM_LANG, $option;
      $html = "";
      
      $db->query("SELECT userid FROM #__{vm}_product_reviews WHERE product_id='$product_id' AND userid='".$my->id."'");
      $db->next_record();
      $alreadycommented = $db->num_rows() > 0;
	  
      if (PSHOP_ALLOW_REVIEWS == "1" && !empty($my->id) && !$alreadycommented) { 
        $html = "<script language=\"JavaScript\" type=\"text/javascript\">//<![CDATA[
        function check_reviewform() {
            var form = document.getElementById('reviewform');

            var ausgewaehlt = false;
            for (var i=0; i<form.user_rating.length; i++)
               if (form.user_rating[i].checked)
                  ausgewaehlt = true;
            if (!ausgewaehlt)  {
              alert('".html_entity_decode($VM_LANG->_PHPSHOP_REVIEW_ERR_RATE) ."');
              return false;
            }
            else if (form.comment.value.length < 100) {
              alert('". html_entity_decode($VM_LANG->_PHPSHOP_REVIEW_ERR_COMMENT1) ."');
              return false;
            }
            else if (form.comment.value.length > 2000) {
              alert('". html_entity_decode($VM_LANG->_PHPSHOP_REVIEW_ERR_COMMENT2) ."');
              return false;
            }
            else {
              return true;
            }
        }
        function refresh_counter() {
          var form = document.getElementById('reviewform');
          form.counter.value= form.comment.value.length;
        }
      //]]></script>
            <h4>". $VM_LANG->_PHPSHOP_WRITE_REVIEW ."</h4>
            <br />". $VM_LANG->_PHPSHOP_REVIEW_RATE ."
            <form method=\"post\" action=\"". URL ."index.php\" name=\"reviewForm\" id=\"reviewform\">
            <table cellpadding=\"5\" summary=\"".$VM_LANG->_PHPSHOP_REVIEW_RATE."\">
              <tr>
                <th id=\"five_stars\">
                	<label for=\"user_rating5\"><img alt=\"5 stars\" src=\"".IMAGEURL."stars/5.gif\" border=\"0\" /></label>
                </th>
                <th id=\"four_stars\">
                	<label for=\"user_rating4\"><img alt=\"4 stars\" src=\"".IMAGEURL."stars/4.gif\" border=\"0\" /></label>
                </th>
                <th id=\"three_stars\">
                	<label for=\"user_rating3\"><img alt=\"3 stars\" src=\"".IMAGEURL."stars/3.gif\" border=\"0\" /></label>
                </th>
                <th id=\"two_stars\">
                	<label for=\"user_rating2\"><img alt=\"2 stars\" src=\"".IMAGEURL."stars/2.gif\" border=\"0\" /></label>
                </th>
                <th id=\"one_star\">
                	<label for=\"user_rating1\"><img alt=\"1 star\" src=\"".IMAGEURL."stars/1.gif\" border=\"0\" /></label>
                </th>
                <th id=\"null_stars\">
                	<label for=\"user_rating0\"><img alt=\"0 stars\" src=\"".IMAGEURL."stars/0.gif\" border=\"0\" /></label>
                </th>
              </tr>
              <tr>
                <td headers=\"five_stars\" style=\"text-align:center;\">
                  <input type=\"radio\" id=\"user_rating5\" name=\"user_rating\" value=\"5\" />
                </td>
                <td headers=\"four_stars\" style=\"text-align:center;\">
                	<input type=\"radio\" id=\"user_rating4\" name=\"user_rating\" value=\"4\" />
                </td>
                <td headers=\"three_stars\" style=\"text-align:center;\">
                	<input type=\"radio\" id=\"user_rating3\" name=\"user_rating\" value=\"3\" />
                </td>
                <td headers=\"two_stars\" style=\"text-align:center;\">
                	<input type=\"radio\" id=\"user_rating2\" name=\"user_rating\" value=\"2\" />
                </td>
                <td headers=\"one_star\" style=\"text-align:center;\">
                	<input type=\"radio\" id=\"user_rating1\" name=\"user_rating\" value=\"1\" />
                </td>
                <td headers=\"null_stars\" style=\"text-align:center;\">
                	<input type=\"radio\" id=\"user_rating0\" name=\"user_rating\" value=\"0\" />
                </td>
              </tr>
            </table>
            <br /><br />". $VM_LANG->_PHPSHOP_REVIEW_COMMENT ."<br />
            <textarea title=\"".$VM_LANG->_PHPSHOP_REVIEW_COMMENT."\" class=\"inputbox\" id=\"comment\" onblur=\"refresh_counter();\" onfocus=\"refresh_counter();\" onkeypress=\"refresh_counter();\" name=\"comment\" rows=\"10\" cols=\"55\"></textarea>
            <br />
            <input class=\"button\" type=\"submit\" onclick=\"return( check_reviewform());\" name=\"submit_review\" title=\"". $VM_LANG->_PHPSHOP_REVIEW_SUBMIT ."\" value=\"". $VM_LANG->_PHPSHOP_REVIEW_SUBMIT ."\" />
            
            <div align=\"right\">". $VM_LANG->_PHPSHOP_REVIEW_COUNT ."
            <input type=\"text\" value=\"0\" size=\"4\" class=\"inputbox\" name=\"counter\" maxlength=\"4\" readonly=\"readonly\" />
            </div>
            
            <input type=\"hidden\" name=\"product_id\" value=\"$product_id\" />
            <input type=\"hidden\" name=\"option\" value=\"$option\" />
            <input type=\"hidden\" name=\"page\" value=\"$page\" />
            <input type=\"hidden\" name=\"category_id\" value=\"". @$_REQUEST['category_id'] ."\" />
            <input type=\"hidden\" name=\"Itemid\" value=\"". @$_REQUEST['Itemid'] ."\" />
            <input type=\"hidden\" name=\"func\" value=\"addReview\" />
        </form>";
        
      }
      if ($alreadycommented) {
          $html .= $VM_LANG->_PHPSHOP_REVIEW_ALREADYDONE;
      }
      return $html;
  }
  
  function process_vote( &$d ) {
    global $db, $my, $VM_LANG;
    
    if (PSHOP_ALLOW_REVIEWS == "1" && !empty($my->id)) {
    
        if (($d["user_rating"]>=0) && ($d["user_rating"]<=5)) {
          $sql = "SELECT votes,allvotes FROM #__{vm}_product_votes WHERE product_id = '". $d["product_id"]."'";
          $db->query( $sql );
          $db->next_record();
		  
          if( $db->num_rows() < 1 ){
            $sql="INSERT INTO #__{vm}_product_votes (product_id) VALUES (".$d["product_id"].")";
            $db->query( $sql );
            $votes = $d["user_rating"];
            $lastip = '';
            $allvotes = 0;
          }
		  else {
			$allvotes=intval( $db->f("allvotes") );
			$votes = $d["user_rating"].','.$db->f("votes");
		  }
          $currip = getenv("REMOTE_ADDR");

          $votes_arr=explode(",", $votes);
          $votes_count=array_sum($votes_arr);
          $newrating=$votes_count / ( ( $allvotes )+1 );
          $newrating = round( $newrating );
          $sql="UPDATE #__{vm}_product_votes SET allvotes=allvotes+1, rating=$newrating, votes='$votes', lastip='$currip' WHERE product_id='".$d["product_id"]."'";
          $db->query( $sql );

        }
        
    }
    return true;
  }
  
  function process_review( &$d ) {
      global $db, $my, $VM_LANG;
      
      if (PSHOP_ALLOW_REVIEWS == "1" && !empty($my->id) ) {
          if( strlen( $d["comment"] ) < 100 ) {
            $_REQUEST['mosmsg'] = $VM_LANG->_PHPSHOP_REVIEW_ERR_COMMENT1;
            return true;
          }
          if( strlen ( $d["comment"] ) > 2000 ) {
            $_REQUEST['mosmsg'] = $VM_LANG->_PHPSHOP_REVIEW_ERR_COMMENT2;
            return true;
          }
          if( empty( $d["user_rating"] ) || intval( $d["user_rating"] ) < 0 || intval( $d["user_rating"] ) > 5) {
            $_REQUEST['mosmsg'] = $VM_LANG->_PHPSHOP_REVIEW_ERR_RATE;
            return true;
          }
          $commented=false;
          $sql = "SELECT userid FROM #__{vm}_product_reviews WHERE product_id = '".$d["product_id"]."'";
          $db->query( $sql );
		  
          while( $db->next_record() ) {
			  $uid = $db->f("userid");
              if ($db->f("userid") == $my->id){
                  $commented=true;
                  break;
              }
		  }
          if ($commented==false) {
            $comment=$db->getEscaped( nl2br(htmlspecialchars(strip_tags($d["comment"]))) );
            $sql="INSERT INTO #__{vm}_product_reviews (product_id, comment, userid, time, user_rating) VALUES 
                      ('".$d["product_id"]."', '$comment', '".$my->id."', '".time()."', '".$d["user_rating"]."')";
            $db->query( $sql );
            $this->process_vote( $d );
          } 
          else {
            $_REQUEST['mosmsg'] = $VM_LANG->_PHPSHOP_REVIEW_ALREADYDONE;
          }
          
          $_REQUEST['mosmsg'] = $VM_LANG->_PHPSHOP_REVIEW_THANKYOU;
      }
      return true;
  }
  
  	/**
	* Controller for Deleting Records.
	*/
	function delete_review( &$d ) {
	
		$record_id = $d["userid"];
		
		if( is_array( $record_id)) {
			foreach( $record_id as $record) {
				if( !ps_reviews::delete_record( $record, $d ))
					return false;
			}
			return true;
		}
		else {
			return ps_reviews::delete_record( $record_id, $d );
		}
	}
	/**
	* Deletes one Record.
	*/
	function delete_record( $record_id, &$d ) {
	
      global $db, $my;
      
      $db->query("SELECT user_rating FROM #__{vm}_product_reviews "
                                        ."WHERE product_id='".$d["product_id"]."' AND userid='$record_id'");
      $db->next_record();
      $user_rating = $db->f("user_rating");
      
      $db->query("SELECT allvotes,votes FROM #__{vm}_product_votes WHERE product_id='".$d["product_id"]."'");
      $db->next_record();
      $votes = $db->f("votes");
      $allvotes = $db->f("allvotes");
      
      /** Exclude one vote with the value of the user_rating 
      * of the user, we delete the review of  **/
      if (strpos($votes, $user_rating)==0)
          $votes = substr($votes, 2);
      else {
          $votes = substr( $votes, 0, strpos($votes, $user_rating))
                  . substr( $votes, strpos($votes, $user_rating)+2);
      }
      $votes_arr=explode(",", $votes);
      $votes_count=array_sum($votes_arr);
	  if( ( $allvotes )-1 < 1 )
		$newrating=0;
	  else 
		$newrating=$votes_count / ( ( $allvotes )-1 );
      $newrating = round( $newrating );
      $db->query("UPDATE #__{vm}_product_votes SET allvotes=allvotes-1, votes = '$votes', rating='$newrating'"
                                        ." WHERE product_id='".$d["product_id"]."'");
      
      /** Now delete the review ***/
      $db->query("DELETE FROM #__{vm}_product_reviews WHERE userid='$record_id' AND product_id='".$d["product_id"]."'");
      
      return true;
  }
}
