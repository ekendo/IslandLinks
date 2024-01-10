<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: ps_csv.php,v 1.7.2.3 2006/03/28 19:40:15 soeren_nb Exp $
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

/* The ps_csv class
*
* This class allows for the adding of multiple
* products and categories from a csv file
*************************************************************************/

class ps_csv {
  var $classname = "ps_csv";
  /** @var Array  Contains all fieldnames that are required on CSV Upload */
  var $reserved_words = Array( "product_sku", "product_name", "category_path" );
  /** @var Array  Contains all fieldnames for the mos_{vm}_products table which are not to be filled dynamically */
  var $dont_use_in_query = Array( "product_sku", "product_name", "product_price", "category_path", "manufacturer_id", "attributes", "attribute_values" );
  
  /**************************************************************************
  ** name: upload_csv()
  ** created by: John Syben
  ** modified by: nhyde
  ** A db table named 'mos_{vm}_csv' must exist with the product fields
  ** allocated their relative positions in the csv line
  ***************************************************************************/

  function upload_csv(&$d) {
  	global $database;
  
    $ps_vendor_id = $_SESSION['ps_vendor_id'];
    $GLOBALS[$ps_vendor_id]["default_shopper_group"] = "";
    
    // handle the upload here
    if (false == $this->handle_csv_upload($d) )
        return false;


    // Get row positions of each element as set in csv table
    $db = new ps_DB;
    $q = "SELECT * FROM #__{vm}_csv ";
    $db->query($q);
    
    $csv_fields = Array();
    $required_fields = Array();
    
    while( $db->next_record() ) {
      $csv_fields[$db->f("field_name")]["name"] = $db->f("field_name");
      $csv_fields[$db->f("field_name")]["ordering"] = $db->f("field_ordering");
      $csv_fields[$db->f("field_name")]["default_value"] = $db->f("field_default_value");
      // Filter all required fields
      if( $db->f("field_required" ) == "Y" )
        $required_fields[$db->f("field_name")] = $db->f("field_ordering");
    }
    
    $dbc = new ps_DB;
    $q = "SELECT vendor_currency FROM #__{vm}_vendor WHERE vendor_id='$ps_vendor_id' ";
    $dbc->query($q);
    $dbc->next_record();
    $product_currency = $dbc->f("vendor_currency");
    
    require_once( CLASSPATH."ps_manufacturer.php" );
    $ps_manufacturer =& new ps_manufacturer();
    $manufacturers = Array();
    
    // Open csv file
    $file = $d['csv_file'];
    $fp = fopen ($file,"r");
    $this_error = "";
    $d["message"] = "";
    $line=1;
    $enclosure = stripslashes(@$d['csv_enclosurechar']);
    
    if ( ((float)substr(phpversion(), 0, 3) >= 4.3) && !empty($enclosure)) {
      $greater43 = true;
      $data = fgetcsv($fp, 4096, $d['csv_delimiter'], $enclosure);
    } 
    else {
      $greater43 = false;
      $data = fgetcsv($fp, 4096, $d['csv_delimiter']);
    }
    $skip_first_line = false;
    if(!empty($d['skip_first_line'])) {
    	$skip_first_line = true; 
    }
      
      $dbu = new ps_DB;
      $dbp = new ps_DB;
      $dbpp = new ps_DB;
      $dbcat = new ps_DB;
      $dbsg = new ps_DB;
      // Run through each line of file
      while ($data) {
        if( $skip_first_line ) {
          // IF the first line is to be skipped, set the flag to false and continue with the second line
          $skip_first_line = false;
          
          $line++;
          // Read the next line
          if ($greater43) {
            $data = fgetcsv($fp, 4096, $d['csv_delimiter'], $enclosure);
          } 
          else {
            $data = fgetcsv($fp, 4096, $d['csv_delimiter']);
          }
          continue;
        }

        // This will prevent importing weird data because of wrong exports
        // Previous versions of this class exported an additional field (product_special), which is not wanted here
        if( $csv_fields['product_available_date']['ordering'] == 13 
        	&& $csv_fields['product_discount_id']['ordering'] == 14
        	&& ( $data[13] == 'N' || $data[13] == 'Y' || (empty($data[13]) && $data[13]!==0 && $data[13]!=='0')) 
        	) {
        	$max = count( $data )-1;
        	for( $i = 13; $i < $max; $i++ ) {
        		$data[$i] = $data[$i+1];
        	}
        }
        
        // Check for required Fields
        foreach( $required_fields as $fieldname => $ordering ) {
        	
          	if (!$data[$ordering-1]) {
            	// If no category path is there, let's check if it's an item
            	if( $fieldname == "category_path" ) {
              		// It's an item, when Parent SKU and Product SKU do no match
              		if( $data[$csv_fields["product_parent_id"]["ordering"]-1] == $data[$csv_fields["product_sku"]["ordering"]-1]) {
                		$this_error .= "No $fieldname, ";
            		}
            	}
        		else {
        			$this_error .= "No $fieldname, ";
        		}
          	}
          	else {
          		$$fieldname = $data[$ordering-1]; // This is a cool trick with dynamic variable names
          	}
        }
        
        
          // Check for Manufacturer ID and set to 1 if omitted
          if( empty($data[$csv_fields["manufacturer_id"]["ordering"]-1])) {
            $data[$csv_fields["manufacturer_id"]["ordering"]-1] = $csv_fields["manufacturer_id"]["default_value"];
          }
          // If a required field was missing, add to error to main message and start next line
          // Otherwise add or update product
          if (!empty($this_error)) {
            $d['message'] .= "Line $line: $this_error<br />";
            $this_error = "";
          }
          else { 
            $timestamp = time();

            // See if sku exists. If so, update product - otherwise add product
            $q = "SELECT product_id FROM #__{vm}_product ";
            $q .= "WHERE product_sku='$product_sku'";
            $dbp->query($q);
            
            // When the Product is an Item, we must get the ID of the Parent Product
            // This assumes that the Parent Product already has been added 
            if( $data[$csv_fields["product_parent_id"]["ordering"]-1] != $data[$csv_fields["product_sku"]["ordering"]-1] ) {
              $q = "SELECT product_id FROM #__{vm}_product WHERE product_sku='".$data[$csv_fields["product_parent_id"]["ordering"]-1]."'";
              $dbu->query( $q );
              $dbu->next_record();
              $data[$csv_fields["product_parent_id"]["ordering"]-1] = $dbu->f("product_id");
            }
            else
              $data[$csv_fields["product_parent_id"]["ordering"]-1] = 0;
            
            /****************************
            ** UPDATE PRODUCT ***********
            *****************************/
            if ($dbp->next_record()) { // SKU exists - update product
                // Update product information
                $q = "UPDATE #__{vm}_product SET ";
                foreach( $csv_fields as $fieldname ) {
                  
                  if( !in_array( $fieldname["name"], $this->dont_use_in_query )) {
                    // Use the default value, when the CSV file contains an empty value
                    if( empty($data[$fieldname["ordering"]-1]) )
                      
                      $data[$fieldname["ordering"]-1] = $csv_fields[$fieldname["name"]]["default_value"];
                    $q .= $fieldname["name"] . " = '" . $data[$fieldname["ordering"]-1] . "',";
                  }
                }
                $q .= "product_name='" . $product_name . "', ";
                $q .= "mdate='" . $timestamp . "' ";
                $q .= "WHERE product_sku='" . $product_sku . "'";
                
                $dbu->query($q);
                
                /** ATTRIBUTE HANDLING
                * Let's first search for Attributes 
                * which are then added to this Product
                * Syntax:   attribute_name::list_order|attribute_name::list_order......
                */
                if( !empty($data[$csv_fields["attributes"]["ordering"]-1])) {
                  $attributes = explode( "|", $data[$csv_fields["attributes"]["ordering"]-1] );
                  $i = 0;
                  $dbu->query( "DELETE FROM #__{vm}_product_attribute_sku WHERE product_id ='".$dbp->f("product_id")."'");
                  while(list(,$val) = each($attributes)) {
                    $values = explode( "::", $val );
                    if( empty( $values[1] ))
                      $values[1] = $i;
                    $dbu->query( "INSERT INTO #__{vm}_product_attribute_sku (`product_id`, `attribute_name`, `attribute_list`)
                                    VALUES ('".$dbp->f("product_id")."', '".$values[0]."', '".$values[1]."' )");
                    $i++;
                  }
                
                }
                /**
                * Now let's search for Attribute Values
                * which are then added to this Child Product
                * Syntax:   attribute_name::attribute_value|attribute_name::attribute_value.....
                */
                if( !empty($data[$csv_fields["attribute_values"]["ordering"]-1])) {
                  $attribute_values = explode( "|", $data[$csv_fields["attribute_values"]["ordering"]-1] );
                  $i = 0;
                  $dbu->query( "DELETE FROM #__{vm}_product_attribute WHERE product_id ='".$dbp->f("product_id")."'");
                  while(list(,$val) = each($attribute_values)) {
                    $values = explode( "::", $val );
                    if( empty( $values[1] ))
                      $values[1] = "";
                    $dbu->query( "INSERT INTO #__{vm}_product_attribute (`product_id`, `attribute_name`, `attribute_value`)
                                    VALUES ('".$dbp->f("product_id")."', '".$values[0]."', '".$values[1]."' )");
                    $i++;
                  }
                
                }
                
                
                if( !empty($data[$csv_fields["product_price"]["ordering"]-1])) {
                  // Get default shopper group ID
                  if( empty( $GLOBALS[$ps_vendor_id]["default_shopper_group"] )) {
                    $q = "SELECT shopper_group_id FROM #__{vm}_shopper_group ";
                    $q .= "WHERE `default`='1' and vendor_id='$ps_vendor_id'";
                    $dbsg = new ps_DB;
                    $dbsg->query($q);
                    $dbsg->next_record();
                    $GLOBALS[$ps_vendor_id]["default_shopper_group"] =$dbsg->f("shopper_group_id");
                  }

                  // Update product price for default shopper group   
                  $q = "UPDATE #__{vm}_product_price SET ";
                  $q .= "product_price='" . $data[$csv_fields["product_price"]["ordering"]-1] . "',";
                  $q .= "product_currency='" . $product_currency . "',";
                  $q .= "shopper_group_id='" . $GLOBALS[$ps_vendor_id]["default_shopper_group"] . "', ";
                  $q .= "mdate='" . $timestamp . "' ";
                  $q .= "WHERE product_id='" . $dbp->f("product_id") . "'";
                  $dbpp->query($q);
                }
                if( empty($data[$csv_fields["product_parent_id"]["ordering"]-1])) {
                  // Use csv_category() method to confirm/add category tree for this product
                  // Modification: $category_id now is an array
                  $category_id = $this->csv_category($data[$csv_fields["category_path"]["ordering"]-1]);
                  
                  // Delete old entries
                  $q  = "DELETE FROM #__{vm}_product_category_xref WHERE product_id =";
                  $q .= " '".$dbp->f("product_id")."'";
                  $dbcat->query($q);
                  
                  // Insert new product/category relationships
                  foreach( $category_id as $value ) {
                    $q  = "INSERT INTO #__{vm}_product_category_xref (category_id, product_id ) VALUES (";
                    $q .= "'$value', '".$dbp->f("product_id")."')";
                    $dbcat->query($q);
                  }
                }
                // Add report for this line to message
                $d["message"] .= "Line: $line Updated Product SKU: $product_sku<br />";
              }
              else {
                /*************************************
                ** SKU does not exist - add new product
                ** Add product information ***********
                **************************************/ 
                $q  = "INSERT INTO #__{vm}_product (vendor_id,product_sku,product_name,cdate,mdate,product_publish,";
                $x = 1;
                foreach( $csv_fields as $fieldname ) {
                
                  if( !in_array( $fieldname["name"], $this->dont_use_in_query )) {
                    $q .= $fieldname["name"].",";
                  }
                }
                $q .= ") ";
                $q .= "VALUES ('$ps_vendor_id','";
                $q .= $data[$csv_fields["product_sku"]["ordering"]-1] . "','" . $data[$csv_fields["product_name"]["ordering"]-1] . "'";
                $q .= ",'$timestamp','$timestamp', 'Y',";
                $x = 1;
                foreach( $csv_fields as $fieldname ) {
                  if( !in_array( $fieldname["name"], $this->dont_use_in_query )) {
                    if( empty($data[$fieldname["ordering"]-1]) )
                      $data[$fieldname["ordering"]-1] = $csv_fields[$fieldname["name"]]["default_value"];
                    $q .= "'".$data[$fieldname["ordering"]-1] . "',";
                  }
                }
                $q .= ") ";
                $q = str_replace( ",)", ")", $q );
                
                $dbu->query($q);
                
                $product_id = $dbu->last_insert_id();
                          
                // Store the manufacturer ID and create a
                // product <-> manufacturer relationship
                $q = "INSERT INTO #__{vm}_product_mf_xref VALUES (";
                $q .= "'$product_id', '".$data[$csv_fields["manufacturer_id"]["ordering"]-1]."')";
                $dbcat->setQuery($q);  $dbcat->query();
                
                // Care for the Manufacturer Entry
                if( empty( $manufacturers[$data[$csv_fields["manufacturer_id"]["ordering"]-1]] )) {
                  // Must Search for the Manufacturer ID
                  $q = "SELECT manufacturer_id FROM #__{vm}_manufacturer WHERE manufacturer_id='".$data[$csv_fields["manufacturer_id"]["ordering"]-1]."'";
                  $dbcat->query( $q );
                  if( $dbcat->next_record() ) {
                    $manufacturers[$data[$csv_fields["manufacturer_id"]["ordering"]-1]] = 1;
                  }
                  // Add The Manufacturer
                  else {
                    $d['mf_name'] = uniqid( "Generic Manufacturer_" );
                    $d['mf_category_id'] = 1;
                    $d['mf_desc'] = $d['mf_email'] = $d['mf_url'] = "";
                    $ps_manufacturer->add( $d );
                    $manufacturers[$database->insertid()] = 1;
                  }
                }
                  
                // Use csv_category() method to confirm/add category tree for this product
                if( !empty($data[$csv_fields["category_path"]["ordering"]-1]))
                  $category_id = $this->csv_category($data[$csv_fields["category_path"]["ordering"]-1]);
                
                if( empty($data[$csv_fields["product_parent_id"]["ordering"]-1])) {
                  // Insert new product/category relationships
                  foreach( $category_id as $value ) {
                    $q  = "INSERT INTO #__{vm}_product_category_xref (category_id, product_id ) VALUES (";
                    $q .= "'$value', '$product_id')";
                    $dbcat->query($q);
                  }
                }
                if( !empty($data[$csv_fields["product_price"]["ordering"]-1])) {
                  // Get default shopper group ID
                  if( empty( $GLOBALS[$ps_vendor_id]["default_shopper_group"] )) {
                    $q = "SELECT shopper_group_id FROM #__{vm}_shopper_group ";
                    $q .= "WHERE `default`='1' AND vendor_id='$ps_vendor_id'";
                    $dbsg->query($q);
                    $dbsg->next_record();
                    $GLOBALS[$ps_vendor_id]["default_shopper_group"] = $dbsg->f("shopper_group_id");
                  }
                  // Add  product price for default shopper group
                  $q = "INSERT INTO #__{vm}_product_price ";
                  $q .= "(product_price,product_currency,product_id,shopper_group_id,mdate) ";
                  $q .= "VALUES ('";
                  $q .= $data[$csv_fields["product_price"]["ordering"]-1] . "','";
                  $q .= $product_currency . "','";
                  $q .= $product_id . "','";
                  $q .= $GLOBALS[$ps_vendor_id]["default_shopper_group"] . "','";
                  $q .= $timestamp . "') ";
                  $dbpp = new ps_DB;
                  $dbpp->query($q);
                }

                /** ATTRIBUTE HANDLING
                * Let's first search for Attributes 
                * which are then added to this Product
                * Syntax:   attribute_name::list_order|attribute_name::list_order......
                */
                if( !empty($data[$csv_fields["attributes"]["ordering"]-1])) {
                  $attributes = explode( "|", $data[$csv_fields["attributes"]["ordering"]-1] );
                  $i = 0;
                  while(list(,$val) = each($attributes)) {
                    $values = explode( "::", $val );
                    if( empty( $values[1] ))
                      $values[1] = $i;
                    $dbu->query( "INSERT INTO #__{vm}_product_attribute_sku (`product_id`, `attribute_name`, `attribute_list`)
                                    VALUES ('".$product_id."', '".$values[0]."', '".$values[1]."' )");
                    $i++;
                  }
                
                }
                /**
                * Now let's search for Attribute Values
                * which are then added to this Child Product
                * Syntax:   attribute_name::attribute_value|attribute_name::attribute_value.....
                */
                if( !empty($data[$csv_fields["attribute_values"]["ordering"]-1])) {
                  $attribute_values = explode( "|", $data[$csv_fields["attribute_values"]["ordering"]-1] );
                  $i = 0;
                  while(list(,$val) = each($attribute_values)) {
                    $values = explode( "::", $val );
                    if( empty( $values[1] ))
                      $values[1] = "";
                    $dbu->query( "INSERT INTO #__{vm}_product_attribute (`product_id`, `attribute_name`, `attribute_value`)
                                    VALUES ('".$product_id."', '".$values[0]."', '".$values[1]."' )");
                    $i++;
                  }
                
                }
                
                // Add report for this line to message
                $d['message'] .=  "Line: $line Added Product SKU: $product_sku<br />";
              }
          }
        $line++;
        if ($greater43) {
          $data = fgetcsv($fp, 4096, $d['csv_delimiter'], $enclosure);
        } 
        else {
          $data = fgetcsv($fp, 4096, $d['csv_delimiter']);
        }
      } // End while
    fclose ($fp);
    return True;

  } //End function upload_csv


  /**************************************************************************
  ** name: csv_category()
  ** created by: John Syben
  ** Creates categories from slash delimited line
  ***************************************************************************/
  function csv_category($line) {
  
    // New: Get all categories in this field,
    // delimited with |
    $categories = explode("|", $line);
    foreach( $categories as $line ) {
      // Explode slash delimited category tree into array
      $category_list = explode("/", $line);
      $category_count = count($category_list);
  
      $db = new ps_DB;
      $category_parent_id = '0';
  
        // For each category in array
        for($i = 0; $i < $category_count; $i++) {
          // See if this category exists with it's parent in xref
          $q = "SELECT #__{vm}_category.category_id FROM #__{vm}_category,#__{vm}_category_xref ";
          $q .= "WHERE #__{vm}_category.category_name='" . $category_list[$i] . "' ";
          $q .= "AND #__{vm}_category_xref.category_child_id=#__{vm}_category.category_id ";
          $q .= "AND #__{vm}_category_xref.category_parent_id='$category_parent_id'";
          $db->query($q);
            // If it does not exist, create it
            if ($db->next_record()) { // Category exists
              $category_id = $db->f("category_id");
            }
            else { // Category does not exist - create it
              
              $timestamp = time();
              
              // Let's find out the last category in
              // the level of the new category
              $q = "SELECT MAX(list_order) AS list_order FROM #__{vm}_category_xref,#__{vm}_category ";
              $q .= "WHERE category_parent_id='".$category_parent_id."' ";
              $q .= "AND category_child_id=category_id ";
              $db->query( $q );
              $db->next_record();
              
              $list_order = intval($db->f("list_order"))+1;
              
              // Add category
              $q = "INSERT INTO #__{vm}_category ";
              $q .= "(vendor_id,category_name, category_publish,cdate,mdate,list_order) ";
              $q .= "VALUES ('1', '";
              $q .= $category_list[$i] . "', '";
              $q .= "Y', '";
              $q .= $timestamp . "', '";
              $q .= $timestamp . "', '$list_order')";
              $db->query($q);
              
              $category_id = $db->last_insert_id();
              
              // Create xref with parent
              $q = "INSERT INTO #__{vm}_category_xref ";
              $q .= "(category_parent_id, category_child_id) ";
              $q .= "VALUES ('";
              $q .= $category_parent_id . "', '";
              $q .= $category_id . "')";
              $db->query($q);
            }
          // Set this category as parent of next in line
          $category_parent_id = $category_id;
        } // end for
    $category[] = $category_id;
    }
    // Return an array with the last category_ids which is where the product goes
    return $category;

  } // End function csv_category
	
	/**
	  * Handle the upload of file "file".
	  *
	  * Longer, multi-line description here.
	  * 
	  * @name handle_csv_upload
	  * @author Nathan Hyde <nhyde@bigdrift.com>
	  * @param array d posted items crammed into 1 arr
	  * @returns boolean True of False
	  */
	function handle_csv_upload(&$d) {
		$allowed_suffixes_arr = array(
			0=> 'csv'
			,1 => 'txt'
			// add more here if needed
		);
		
		$allowed_mime_types_arr = array(0 => 'text/html'
                                        ,1 => 'text/plain'
                                        ,2 => 'application/octet-stream'
                                        ,3 => 'application/x-octet-stream'
                                        ,4 => 'application/vnd.ms-excel'
                                        ,5 => 'application/force-download'
                                        ,6 => 'text/comma-separated-values'
                                        ,7 => 'text/x-csv'
                                        ,8 => 'text/x-comma-separated-values'
                                        // add more here if needed
    );
		
    $error = ""; 
    if( empty($_FILES["file"]["name"]) && empty($d['local_csv_file']) ) {
        $d["error"] = "Error: No file provided.";
        return False;
    }
    if( empty( $_FILES["file"]["tmp_name"] )) {
      $d['csv_file'] = $d['local_csv_file'];
      if( !file_exists($d['csv_file'])) {
          $d["error"] = "Error: Specified local file doesn't exist.";
          return False;
      }
      $fileinfo = pathinfo($d['csv_file']); 
      $extension = $fileinfo["extension"]; 
    }
    else {
      // test the mime type here
      if (!in_array($_FILES["file"]["type"], $allowed_mime_types_arr) ) {
        $d["error"] = "Mime type not accepted. Type for file uploaded: ".$_FILES["file"]["type"];
        return False;
      }
      $d['csv_file'] = $_FILES["file"]["tmp_name"];
      $fileinfo = pathinfo($_FILES["file"]["name"]); 
      $extension = $fileinfo["extension"]; 
    }
  
      if (!in_array($extension, $allowed_suffixes_arr) ) {
        $d["error"] = "Suffix not allowed. Valid suffixes are: " . join(", ",$allowed_suffixes_arr);
        return False;
      }
      
      // do the moovin here :)
      // not necessary for us to do cause it's a temporary file, right?
      /** 
      if (is_uploaded_file($d['file_tmp_name']) ) {
        copy($d['file_tmp_name'], "/place/to/put/uploaded/file");
      } else {
        echo "Possible file upload attack. Filename: " . $_FILES['userfile']['name'];
      }
      // ...or... 
      move_uploaded_file($d["file_tmp_name"], "/place/to/put/uploaded/file");
      
      **/
      
  
      return True;
    }
    
	/**
	  * Handle the parsing of a string containing csv fields.
	  *
	  * @name fgetcsvfromline
	  * @author dawa at did-it dot com, posted at www.php.net
	  * @param string line
    * @param 
	  * @returns array $matches
    * The first field contains the whole line
	  */
    function fgetcsvfromline ($line, $columnCount, $delimiterChar = ',', $enclosureChar = '"') {
        $regExpSpecialChars = array (
            "|" => "\\|",
            "&" => "\\&",
            "$" => "\\$",
            "(" => "\\(",
            ")" => "\\)",
            "^" => "\\^",
            "[" => "\\[",
            "]" => "\\]",
            "{" => "\\{",
            "}" => "\\}",
            "." => "\\.",
            "*" => "\\*",
            "\\" => "\\\\",
            "/" => "\\/"
        );
       $matches = array();
       $delimiterChar = strtr($delimiterChar, $regExpSpecialChars);
       $enclosureChar = strtr($enclosureChar, $regExpSpecialChars);
       $cutpoint = strlen($delimiterChar)+1;
       $regExp = "/^";
       for ($i = 0; $i < $columnCount; $i++) {
            $regExp .= $enclosureChar.'?(.*?)'.$enclosureChar.'?'.$delimiterChar;
       }
       $regExp = substr($regExp,0,-$cutpoint).'/';
       if (preg_match($regExp, $line, $matches)) {
            return $matches;
       }
       return 0;
    }
    
	/**
	  * Handle the export of product records in a csv file
	  *
	  * @name export_csv
	  * @author soeren
	  * @param array d
	  * @return void
    * 
	  */
    function export_csv( &$d ) {
        global $mosConfig_sitename;
        $ps_vendor_id = $_SESSION['ps_vendor_id'];
        $use_standard_order = mosGetParam( $_REQUEST, 'use_standard_order', "N" );
        $db = new ps_DB;
        $database = new ps_DB;

        // Get default shopper group ID for prices
        $q = "SELECT shopper_group_id FROM #__{vm}_shopper_group WHERE `default`='1' and vendor_id = '$ps_vendor_id'";
        $db->query($q);
        $db->next_record();
        $shopper_group_id = $db->f("shopper_group_id");
        
        // Get row positions of each element as set in csv table
        $db = new ps_DB;
        $q = "SELECT * FROM #__{vm}_csv ";
        $db->query($q);
              
        $csv_ordering = Array();        
        while( $db->next_record() ) {
            $csv_ordering[$db->f("field_ordering")] = $db->f("field_name");
        }
        /** Export SQL Query
        * Get all products - including items
        * as well as products without a price
        **/
        $sql = 'SELECT * FROM #__{vm}_product
        		LEFT OUTER JOIN #__{vm}_product_price
        		ON #__{vm}_product.product_id = #__{vm}_product_price.product_id
        		AND #__{vm}_product.vendor_id = \'1\'
        		AND shopper_group_id = \'5\'
        		LEFT JOIN #__{vm}_product_mf_xref
        			ON #__{vm}_product.product_id = #__{vm}_product_mf_xref.product_id
        		ORDER BY product_parent_id ASC , #__{vm}_product.product_id ASC';
        
        $db->query( $sql );
        
        $delim = $d['csv_delimiter'];
        $encl = stripslashes(@$d['csv_enclosurechar']);
        
        if(empty($encl) && !isset($d['csv_enclosurechar'])) $encl = "\"";
        
        $contents = "";
        $db_attributes = new ps_DB;
        $db_attribute_values = new ps_DB;
        /** Loop through all records 
        * and create the csv file - line after line ***/
        while( $db->next_record() ) {
        
          $attributes = $attribute_values = "";
          if( $db->f("product_parent_id") == 0 ) {
            
            $db_attributes->query( "SELECT attribute_name, attribute_list FROM #__{vm}_product_attribute_sku WHERE product_id = '".$db->f("product_id")."'" );
            if( $db_attributes->next_record() ) {
              $has_attributes = true;
              $db_attributes->reset();
              while( $db_attributes->next_record() ) {
                $attributes .= $db_attributes->f("attribute_name"). "::". $db_attributes->f("attribute_list");
                // to be replaced by
                // if( !$db_attributes->is_last_record())
                if( $db_attributes->row+1 < $db_attributes->num_rows())
                  $attributes .= "|";
              }
            }
            
            $export_sku = $db->f("product_sku");
          }
          else {
            
            $db_attribute_values->query( "SELECT attribute_name, attribute_value FROM #__{vm}_product_attribute WHERE product_id = '".$db->f("product_id")."'" );
            if( $db_attribute_values->next_record() ) {
              $db_attribute_values->reset();
              while( $db_attribute_values->next_record() ) {
                $attribute_values .= $db_attribute_values->f("attribute_name")."::". $db_attribute_values->f("attribute_value");
                if( $db_attribute_values->row+1 < $db_attribute_values->num_rows())
                  $attribute_values .= "|";
              }
            }
            $database->query( "SELECT product_sku FROM #__{vm}_product WHERE product_id='".$db->f("product_parent_id")."'" );
            $database->next_record();
            $export_sku = $database->f('product_sku');
          }
            
          if( $use_standard_order == "Y" ) {
            $contents .= $encl . $db->f("product_sku"). $encl
                                . $delim . $encl . str_replace("\r", "", str_replace("\n", "", str_replace("\r\n", "", addslashes( $db->f("product_s_desc"))))) . $encl
                                . $delim . $encl . str_replace("\r", "", str_replace("\n", "", str_replace("\r\n", "", addslashes($db->f("product_desc"))))) . $encl
                                . $delim . $encl . addslashes( $db->f("product_thumb_image")) . $encl
                                . $delim . $encl . addslashes( $db->f("product_full_image")) . $encl
                                . $delim . $encl . $db->f("product_weight") . $encl
                                . $delim . $encl . $db->f("product_weight_uom") . $encl
                                . $delim . $encl . $db->f("product_length") . $encl
                                . $delim . $encl . $db->f("product_width") . $encl
                                . $delim . $encl . $db->f("product_height") . $encl
                                . $delim . $encl . addslashes( $db->f("product_lwh_uom")) . $encl
                                . $delim . $encl . $db->f("product_in_stock") . $encl
                                . $delim . $encl . $db->f("product_available_date") . $encl
                                //. $delim . $encl . $db->f("product_special") . $encl
                                . $delim . $encl . $db->f("product_discount_id") . $encl
                                . $delim . $encl . addslashes( $db->f("product_name")) . $encl
                                . $delim . $encl . $db->f("product_price") . $encl
                                . $delim . $encl . addslashes( $this->get_category_path( $db->f("product_id") ) ). $encl
                                . $delim . $encl . $db->f("manufacturer_id") . $encl 
                                . $delim . $encl . $db->f("product_tax_id") . $encl  
                                . $delim . $encl . $db->f("product_sales") . $encl 
                                . $delim . $encl . $export_sku . $encl 
                                . $delim . $encl . addslashes( $db->f("attribute") ). $encl 
                                . $delim . $encl . addslashes( $db->f("custom_attribute") ). $encl
                                . $delim . $encl . addslashes( $attributes ). $encl 
                                . $delim . $encl . addslashes( $attribute_values ). $encl ."\n";
          }
          else {
            $num = sizeof( $csv_ordering );
            for( $i = 1; $i <= $num; $i++ ) {
              if( $csv_ordering[$i] == "category_path" )
                $contents .= $encl . addslashes( $this->get_category_path( $db->f("product_id") ) ). $encl;
              elseif( $csv_ordering[$i] == "attributes" )
                $contents .= $encl . addslashes( $attributes ) . $encl;
              elseif( $csv_ordering[$i] == "attribute_values" )
                $contents .= $encl . addslashes( $attribute_values ). $encl;
              // PROBLEM: when exporting the Product Parent ID we can't be sure
              // that the Parent Product gets the same ID on re-import
              // So we just take the Parent Product's SKU!
              elseif( $csv_ordering[$i] == "product_parent_id" ) {
                $contents .= $encl . $export_sku . $encl;
              }
              else
                $contents .= $encl . str_replace("\r", "", str_replace("\n", "", str_replace("\r\n", "", addslashes( $db->f($csv_ordering[$i]))))) . $encl;
              // Add delimiter (if not line end)
              if( $i < $num )
                $contents .= $delim;
            }
            // Finish line
            $contents .=  "\n";
          }
          
        }

        $filename = "VirtueMart_" .date("jmYHis"). ".csv";

        if (ereg('Opera(/| )([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT'])) {
          $UserBrowser = "Opera";
        }
        elseif (ereg('MSIE ([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT'])) {
          $UserBrowser = "IE";
        } else {
          $UserBrowser = '';
        }
        $mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ? 'application/octetstream' : 'application/octet-stream';
        
        // dump anything in the buffer
		while( @ob_end_clean() );
		
        header('Content-Type: ' . $mime_type);
        header('Content-Encoding: none');
        header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
  
        if ($UserBrowser == 'IE') {
          	header('Content-Disposition: inline; filename="' . $filename . '"');
          	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
          	header('Pragma: public');
        } else {
          	header('Content-Disposition: attachment; filename="' . $filename . '"');
          	header('Pragma: no-cache');
        }
         /*** Now dump the data!! ***/
		echo $contents;				
		// do nothin' more
		exit();
    }
    
	/**
	  * Get the slash delimited category path of a product
	  *
	  * @name get_category_path
	  * @author soeren
	  * @param int $product_id
	  * @returns String category_path
	  */
    function get_category_path( $product_id ) {
        
        $db = new ps_DB;
        $database = new ps_DB();
        
        $q = "SELECT #__{vm}_product.product_id, #__{vm}_product.product_parent_id, category_name,#__{vm}_category_xref.category_parent_id "
                ."FROM #__{vm}_category, #__{vm}_product, #__{vm}_product_category_xref,#__{vm}_category_xref "
                ."WHERE #__{vm}_product.product_id='$product_id' "
                ."AND #__{vm}_category_xref.category_child_id=#__{vm}_category.category_id "
                ."AND #__{vm}_category_xref.category_child_id = #__{vm}_product_category_xref.category_id "
                ."AND #__{vm}_product.product_id = #__{vm}_product_category_xref.product_id";
        $database->query( $q );
        $rows = $database->record;
        $k = 1;
        $category_path = "";
        
        foreach( $rows as $row ) {
          $category_name = Array();
          
          /** Check for product or item **/
          if ( $row->category_name ) {
              $category_parent_id = $row->category_parent_id;
              $category_name[] = $row->category_name;
          }
          else {
              /** must be an item 
              * So let's search for the category path of the
              * parent product **/
              $q = "SELECT product_parent_id FROM #__{vm}_product WHERE product_id='$product_id'";
              $db->query( $q );
              $db->next_record();
              
              $q  = "SELECT #__{vm}_product.product_id, #__{vm}_product.product_parent_id, category_name,#__{vm}_category_xref.category_parent_id "
                  ."FROM #__{vm}_category, #__{vm}_product, #__{vm}_product_category_xref,#__{vm}_category_xref "
                  ."WHERE #__{vm}_product.product_id='".$db->f("product_parent_id")."' "
                  ."AND #__{vm}_category_xref.category_child_id=#__{vm}_category.category_id "
                  ."AND #__{vm}_category_xref.category_child_id = #__{vm}_product_category_xref.category_id "
                  ."AND #__{vm}_product.product_id = #__{vm}_product_category_xref.product_id";
              $db->query( $q );
              $db->next_record();
              $category_parent_id = $db->f("category_parent_id");
              $category_name[] = $db->f("category_name");
          }
          if( $category_parent_id == "") $category_parent_id = "0";
          
          while( $category_parent_id != "0" ) {
              $q = "SELECT category_name, category_parent_id "
                      ."FROM #__{vm}_category, #__{vm}_category_xref "
                      ."WHERE #__{vm}_category_xref.category_child_id=#__{vm}_category.category_id "
                      ."AND #__{vm}_category.category_id='$category_parent_id'";
              $db->query( $q );
              $db->next_record();
              $category_parent_id = $db->f("category_parent_id");
              $category_name[] = $db->f("category_name");
          }
          if ( sizeof( $category_name ) > 1 ) {
            for ($i = sizeof($category_name)-1; $i >= 0; $i--) {
                $category_path .= $category_name[$i];
                if( $i >= 1) $category_path .= "/";
            }
          }
          else
            $category_path .= $category_name[0];
            
          if( $k++ < sizeof($rows) )
            $category_path .= "|";
        }
        return $category_path;
    }
    
    
    
    /**************************************************************************
  ** name: validate_add()
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/  
   function validate_add( &$d ) {
     
     $db = new ps_DB;
    
     foreach( $d["field"] as $field ) {
       if (!$field["_name"]) {
         $this->error = "ERROR:  You must enter a name for the Field.";
         return False;	
       }
       $q = "SELECT count(*) as rowcnt from #__{vm}_csv where";
       $q .= " field_name='" .  $field["_name"] . "'";
       $db->setQuery($q);
       $db->query();
       $db->next_record();
       if ($db->f("rowcnt") > 0) {
         $this->error = "The given field name already exists.";
         return False;
       }
     }
     return True;    
   }
  
  /**************************************************************************
  ** name: validate_update
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/   
  function validate_update( &$d ) {
     global $db;
      $i = 0;
     foreach( $d["field"] as $field ) {
       if (!$field["_name"]) {
         $this->error = "ERROR:  You must enter a name for the Field.";
         return False;	
       }
       if( in_array( $field["_name"], $this->reserved_words ))
        $i++;
       $q = "SELECT count(*) as rowcnt from #__{vm}_csv where";
       $q .= " field_name='" .  $field["_name"] . "' AND field_id <> '".$field["_id"]."'";
       $db->setQuery($q);
       $db->query();
       $db->next_record();
       if ($db->f("rowcnt") > 0) {
         $this->error = "The given field name already exists.";
         return False;
       }
     }
     if( $i < sizeof($this->reserved_words)) {
      $d["error"] = sizeof($this->reserved_words) - $i . " required Field(s) is/are missing (Required Fields: ".implode(", ", $this->reserved_words).")";
      return false;
     }
     return true;
  }
   
  /**************************************************************************
  ** name: validate_delete()
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/   
  function validate_delete( &$d ) {
    
    if (!$d["field_id"]) {
      $this->error = "ERROR:  Please select a Field to delete.";
      return False;
    }
    else {
      return True;
    }
  } 
  
  /**************************************************************************
   * name: add()
   * created by: soeren
   * description: creates a new CSV Field Entry
   * parameters:
   * returns:
   **************************************************************************/
  function add(&$d) {
    
    global $db;
    
    if (!$this->validate_add($d)) {
      return False;
    }
    
    foreach( $d['field'] as $field ) {
      $q = "INSERT INTO #__{vm}_csv (field_name, field_default_value, field_ordering, field_required)";
      $q .= " VALUES ('";
      $q .= $field["_name"] . "','";
      $q .= $field["_default_value"] . "','";
      $q .= $field["_ordering"] . "','";
      $q .= $field["_required"] . "')";
      $db->query($q);
    }
    return True;

  }
  
  /**************************************************************************
   * name: update()
   * created by: pablo
   * description: updates country information
   * parameters:
   * returns:
   **************************************************************************/
  function update(&$d) {
    $db = new ps_DB;
    $timestamp = time();

    if (!$this->validate_update($d)) {
      return False;	
    }
    foreach( $d['field'] as $field ) {
      $q = "UPDATE #__{vm}_csv SET ";
      $q .= "field_name='" . $field["_name"]."',";
      $q .= "field_default_value='" . $field["_default_value"]."',";
      $q .= "field_ordering='" . $field["_ordering"]."', ";
      $q .= "field_required='" . $field["_required"]."' ";
      $q .= "WHERE field_id='".$field["_id"]."'";
      $db->query($q);
    }
    return True;
  }

  /**************************************************************************
   * name: delete()
   * created by: pablo
   * description: Should delete a country record.
   * parameters: 
   * returns:
   **************************************************************************/
  function delete(&$d) {
    $db = new ps_DB;
    
    if (!$this->validate_delete($d)) {
      $d["error"]=$this->error;
      return False;
    }
    $q = "DELETE from #__{vm}_csv WHERE field_id='" . $d["field_id"] . "'";
    $db->setQuery($q);
    $db->query();
    $db->next_record();
    return True;
  }
}
?>