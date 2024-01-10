<?php
defined( '_VALID_MOS' ) or die( 'Restricted access' );
// needed to seperate the ISO number from the language file constant _ISO
$iso = explode( '=', _ISO );
// xml prolog
echo '<?xml version="1.0" encoding="'. $iso[1] .'"?' .'>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php mosShowHead(); ?>
<?php
if ( $my->id ) {
	initEditor();
}

?>
<meta http-equiv="Content-Type" content="text/html; <?php echo _ISO; ?>" />
<link href="<?php echo $mosConfig_live_site;?>/templates/template_636/css/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $mosConfig_live_site;?>/templates/template_636/css/layout.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $mosConfig_live_site;?>/templates/template_636/js/maxheight.js" type="text/javascript"></script>
<!--[if lt IE 7]>
	<link href="<?php echo $mosConfig_live_site;?>/templates/template_636/css/ie_style.css" rel="stylesheet" type="text/css" />
<![endif]-->
</head>

<body id="page1" onload="new ElementMaxHeight();">
   <!-- header -->
   <div id="header">
      <div class="container">
         <div class="row-1">
            <div class="logo"><a href="index.php"><img alt="" src="<?php echo $mosConfig_live_site;?>/images/636_images/logo.jpg" /></a></div>
            <!--
            <ul class="top-links">
               <li><a href="index.html"><img alt="" src="<?php echo $mosConfig_live_site;?>/templates/template_636/images/top-icon1.jpg" /></a></li>
               <li><a href="#"><img alt="" src="<?php echo $mosConfig_live_site;?>/templates/template_636/images/top-icon2.jpg" /></a></li>
               <li><a href="contact-us.html"><img alt="" src="<?php echo $mosConfig_live_site;?>/templates/template_636/images/top-icon3.jpg" /></a></li>
            </ul>
            //-->
            <div class="top-links">
            	<?php if ( mosCountModules ('banner') ) {?>
				<div id="banner_inner">
					<?php mosLoadModules( 'banner', -2 ); ?>
				</div><?php } ?>
			<!-- different Banners //-->
			</div>
         </div>
         <div class="row-2">
         	<div class="nav-box">
            	<div class="left">
               	<div class="right">
                     <?php
					 				if ( mosCountModules( 'user3' ) <1)
					 				{
					 					mosLoadModules ( 'user3', -1 );
					 				}
					 				else
					 				{
					 					?>
					 					<ul>
											<li><a href="index.php" class="first"><em><b>HOME</b></em></a></li>
										    <li><a href="http://www.ekendotech.com/NewStyle/index.php?option=com_content&sectionid=7&task=view&id=119"><em><b>CUSTOM SOLUTIONS</b></em></a></li>
										    <li><a href="http://www.ekendotech.com/NewStyle/index.php?option=com_content&task=category&id=104&Itemid=36&sectionid=12"><em><b>LAPTOPS & DESKTOPS</b></em></a></li>
										    <li><a href="http://www.ekendotech.com/NewStyle/index.php?option=com_content&sectionid=7&task=view&id=120"><em><b>SMARTPHONES & TABLETS</b></em></a></li>
                     						<li><a href="http://www.ekendotech.com/NewStyle/index.php?option=com_content&task=category&id=107&Itemid=37&sectionid=5"><em><b>PARTNERS</b></em></a></li>
										    <li><a href="http://www.ekendotech.com/NewStyle/index.php?option=com_contact&Itemid=3" class="last"><em><b>CONTACT US</b></em></a></li>
                     					</ul>
					 					<?php
					 				}
					?>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- content -->
   <div id="content">
      <div class="container">

         <?php
         	if($_REQUEST['option']==="com_frontpage")
         	{
         		//echo 'yeh:'.$_REQUEST['option'];
         ?>
         <!-- main-banner-big begin -->
		          <div class="main-banner-big">
		          	<div class="inner">
		             	<!--<h1>Even the biggest success Starts with a first step</h1>//-->
		                <!--<a href="#" class="button">Learn More</a>//-->
		             </div>
		          </div>
         <!-- main-banner-big end -->
         <?php
         	}
         ?>

         <?php
		          	if(($_REQUEST['option']==="com_contact")||($_REQUEST['option']==="com_content")||($_REQUEST['option']=== "com_virtuemart"))
		          	{
		          		//echo 'yeh:'.$_REQUEST['option'];
		          ?>
		          <?php

					if(($_REQUEST["task"] === 'category')&&($_REQUEST["id"] === '104'))
					{
				  ?>
						  <!-- main-banner-small-cami begin -->
								  <div class="main-banner-small-cami">
									<div class="inner">
										<h1>Laptops & Desktops</h1>
									</div>
								  </div>
						  <!-- main-banner-small end -->
				  <?php
					}
					else if(($_REQUEST["task"] === 'view')&&($_REQUEST["id"] === '107'))
															{
									  ?>
											  <!-- main-banner-small-jenn begin -->
													  <div class="main-banner-small-jenn">
														<div class="inner">
															<h1>Smartphones & Tablets</h1>
														</div>
													  </div>
											  <!-- main-banner-small end -->
									  <?php
					}
					else if(($_REQUEST["task"] === 'view')&&($_REQUEST["id"] === '120'))
										{
									  ?>
											  <!-- main-banner-small-meli begin -->
													  <div class="main-banner-small-meli">
														<div class="inner">
															<h1>Smartphones & Tablets</h1>
														</div>
													  </div>
											  <!-- main-banner-small end -->
									  <?php
					}
					else
					{
				  ?>
						  <!-- main-banner-small begin -->
								  <div class="main-banner-small">
									<div class="inner">
										<!--<h1>*Even the biggest success Starts with a first step</h1>//-->
									 </div>
								  </div>
						  <!-- main-banner-small end -->
				  <?php
					 }
		          ?>
		          <div class="section">
				  			         	<!-- box begin -->
				  			            <div class="box">
				  			               <div class="border-top">
				  			                  <div class="border-right">
				  			                     <div class="border-bot">
				  			                        <div class="border-left">
				  			                           <div class="left-top-corner">
				  			                              <div class="right-top-corner">
				  			                                 <div class="right-bot-corner">
				  			                                    <div class="left-bot-corner">
				  			                                       <div class="inner">
				  			                                         <?php

				  			                                         	if(($_REQUEST["task"] === 'category')&&($_REQUEST["id"] === '107'))
				  			                                         	{
				  			                                         		// partners page
				  			                                         		//echo 'testing partner page';
																			echo '<ul class="list2">';

				  			                                         		$connection = mysql_connect("server-mysql1","ekendojoomla1","phpcms");
																			mysql_select_db('ekendocms1',$connection);
																			$query = " SELECT a.cid, a.name, a.extrainfo, b.clickurl FROM jos_bannerclient a, jos_banner b ".
																					"WHERE a.cid = b.cid AND b.showbanner >0 order by a.cid DESC";

																			$result = mysql_query($query,$connection) or die("SELECT Error: ".mysql_error());
																			while ($row = mysql_fetch_object($result))
																			{
																			    //echo '<li>'.$row->name.'</li>';
																				//$i++;

																					echo	'<li>';
																				    echo  	'	<img alt="" src="'.$mosConfig_live_site.'/templates/template_636/images/image_76x76.gif" />';
																				    echo 	'	<h4><strong><a href="'.$row->clickurl.'">'.$row->name.'</a></strong></h4>';
																					echo 		$row->extrainfo;
																					echo 	'</li>';
																			}
																			mysql_free_result($result);
																			echo '</ul>';
				  			                                         	}
				  			                                         	else
				  			                                         	{
				  			                                         		//echo 'testing';
				  			                                         		mosMainBody();
				  			                                         		//echo 'testing again';

				  			                                         		if($_REQUEST["id"] === '120')
				  			                                         		{
				  			                                         			//echo 'test';

				  			                                         			if ( mosCountModules( 'user8' ) )
																				{
																					mosLoadModules ( 'user8', -1 );
																					//echo 'yeh';
					 															}

				  			                                         		}

				  			                                         	}

				  			                                         ?>
				  			                                       </div>
				  			                                    </div>
				  			                                 </div>
				  			                              </div>
				  			                           </div>
				  			                        </div>
				  			                     </div>
				  			                  </div>
				  			               </div>
				  			            </div>
				  			            <!-- box end -->
         </div>
		          <?php
		          	}


         ?>

         <!--front page -->
         <div class="wrapper">
         	<div class="col-1 maxheight">
            	<!-- box begin -->
               <div class="box maxheight">
                  <div class="border-top maxheight">
                     <div class="border-right maxheight">
                        <div class="border-bot maxheight">
                           <div class="border-left maxheight">
                              <div class="left-top-corner maxheight">
                                 <div class="right-top-corner maxheight">
                                    <div class="right-bot-corner maxheight">
                                       <div class="left-bot-corner maxheight">
                                          <div class="inner">

											 <?php

											 	if(($_REQUEST["task"] != 'blogcategory')&&($_REQUEST["Itemid"] == '1'))
											 	{
											 		echo '<h2><a href="http://www.ekendotech.com/NewStyle/index.php?option=com_content&task=blogcategory&id=0&Itemid=35">Nerd News</a></h2>';
													$connection = mysql_connect("server-mysql1","ekendojoomla1","phpcms");
													mysql_select_db('ekendocms1',$connection);
													$query = " SELECT a.id, a.title FROM jos_content a, jos_sections b ".
															"WHERE b.id = 1 AND a.sectionid = b.id AND a.state>0 order by a.id DESC";

													$result = mysql_query($query,$connection) or die("SELECT Error: ".mysql_error());
													$i = 0;
													while ($row = mysql_fetch_object($result))
													{
													    echo '<li>'.$row->title.'</li>';
														$i++;

														if($i>10)
														{
															break;
														}
													}
													mysql_free_result($result);
												}
												else
												{
													if($_REQUEST["id"] === '119')
													{
														echo '<h2>Free Software</h2>';

														// Free stuff (story links)
														$connection = mysql_connect("server-mysql1","ekendojoomla1","phpcms");
														mysql_select_db('ekendocms1',$connection);
														$query = " SELECT a.id, a.title, a.introtext, a.metadesc FROM jos_content a, jos_sections b ".
																"WHERE b.id = 10 AND a.sectionid = b.id AND a.state>0 order by a.id DESC";

														//echo 'Q:'.$query;
														$result = mysql_query($query,$connection) or die("SELECT Error: ".mysql_error());
														$i = 0;
														while ($row = mysql_fetch_object($result))
														{
															echo '<li>';
															echo '	<a href="http://www.ekendotech.com/NewStyle/index.php?option=com_content&task=view&id='.$row->id.'">'.$row->title.'</a>';
															//echo 	$row->introtext;
															//echo '	<href="'.trim($row->metadesc).'">Download</a>';
															if($row->metadesc != '')
															{
																echo '<p >*<a href="'.trim($row->metadesc).'">Download Here</a>*</p>';
															}
															else
															{
																echo '<p >*No Download Link*</p>';
															}
															echo '</li>';

														}

										  				mysql_free_result($result);
													}
													else
													{
														echo '<b>Nerd News Mailing List</b>';
														echo '<p>';
														echo '<a href="http://www.ekendotech.net/phplist-2.10.12/public_html/lists/?p=subscribe&id=3">subscribe now</a>';
														echo '</p>';
													}
												}
											?>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- box end -->
            </div>
            <div class="col-2 maxheight">
            	<!-- box begin -->
               <div class="box maxheight">
                  <div class="border-top maxheight">
                     <div class="border-right maxheight">
                        <div class="border-bot maxheight">
                           <div class="border-left maxheight">
                              <div class="left-top-corner maxheight">
                                 <div class="right-top-corner maxheight">
                                    <div class="right-bot-corner maxheight">
                                       <div class="left-bot-corner maxheight">
                                          <div class="inner">
										   	<?php
										   		//echo 'Item:'.$_REQUEST["Itemid"];
												if(($_REQUEST["task"] != 'blogcategory')&&($_REQUEST["Itemid"] == '1'))
											 	{
											 		echo '<h2><a href="http://www.ekendotech.com/NewStyle/index.php?option=com_content&task=blogcategory&id=0&Itemid=30">Free Software</a></h2>';
										  			$connection = mysql_connect("server-mysql1","ekendojoomla1","phpcms");
										  			mysql_select_db('ekendocms1',$connection);
										  			$query = " SELECT a.id, a.title FROM jos_content a, jos_sections b ".
										  					"WHERE b.id = 10 AND a.sectionid = b.id AND a.state>0 order by a.id DESC";

										  			$result = mysql_query($query,$connection) or die("SELECT Error: ".mysql_error());
										  			$i = 0;
										  			while ($row = mysql_fetch_object($result))
										  			{
										  				echo '<li>'.$row->title.'</li>';
										  				$i++;

										  				if($i>10)
										  				{
										  							break;
										  				}
										  			}
										  			mysql_free_result($result);
										  		}
										  		else
												{
													if($_REQUEST["id"] === '119')
													{
														echo '<h2>Trial Software</h2>';

														// trial stuff (try link/buy link)
														$connection = mysql_connect("server-mysql1","ekendojoomla1","phpcms");
														mysql_select_db('ekendocms1',$connection);
														$query = " SELECT a.id, a.title, a.introtext, a.metadesc, a.metakey FROM jos_content a, jos_sections b ".
																"WHERE b.id = 14 AND a.sectionid = b.id AND a.state>0 order by a.id DESC";

														//echo 'Q:'.$query;
														$result = mysql_query($query,$connection) or die("SELECT Error: ".mysql_error());
														$i = 0;
														while ($row = mysql_fetch_object($result))
														{
															echo '<li>';
															echo '	<a href="http://www.ekendotech.com/NewStyle/index.php?option=com_content&task=view&id='.$row->id.'">'.$row->title.'</a>';
															//echo 	$row->introtext;
															//echo '	<href="'.trim($row->metadesc).'">Download</a>';
															if($row->metadesc != '')
															{
																echo '<p>*<a href="'.trim($row->metadesc).'">Try Now</a>* | *<a href="'.trim($row->metakey).'">Buy Now</a>*</p>';
															}
															else
															{
																echo '<p>*No Trial Link* | *No Buy Link*</p>';
															}
															echo '</li>';

														}

										  				mysql_free_result($result);

													}
													else
													{
														echo '<b>(Free & Trial) Software Mailing List</b>';
														echo '<p>';
														echo '	* Coming Soon *';
														echo '</p>';
													}
												}
										  	?>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- box end -->
            </div>
            <div class="col-3 maxheight">
            	<!-- box begin -->
               <div class="box maxheight">
                  <div class="border-top maxheight">
                     <div class="border-right maxheight">
                        <div class="border-bot maxheight">
                           <div class="border-left maxheight">
                              <div class="left-top-corner maxheight">
                                 <div class="right-top-corner maxheight">
                                    <div class="right-bot-corner maxheight">
                                       <div class="left-bot-corner maxheight">
                                          <div class="inner">
                                          	<?php
                                          	//echo 'task:'.$_REQUEST["task"];
                                          	if(($_REQUEST["task"] != 'blogcategory')&&($_REQUEST["Itemid"] == '1'))
                                          	{
												echo '<h2><a href="http://www.ekendotech.com/NewStyle/index.php?option=com_content&task=blogcategory&id=0&Itemid=34">Trial Software</a></h2>';
                                          		$connection = mysql_connect("server-mysql1","ekendojoomla1","phpcms");
												mysql_select_db('ekendocms1',$connection);
												$query = " SELECT a.id, a.title FROM jos_content a, jos_sections b ".
														"WHERE b.id = 14 AND a.sectionid = b.id AND a.state>0 order by a.id DESC";

												$result = mysql_query($query,$connection) or die("SELECT Error: ".mysql_error());
												$i=0;
												while ($row = mysql_fetch_object($result))
												{
													echo '<li>'.$row->title.'</li>';
													$i++;

													if($i>10)
													{
														break;
													}
												}
												mysql_free_result($result);
                                          	}
                                          	else
											{
												if($_REQUEST["id"] === '119')
												{
													echo '<h2>Premium Products</h2>';

													// virtue mart stuff (buy link)
														$connection = mysql_connect("server-mysql1","ekendojoomla1","phpcms");
														mysql_select_db('ekendocms1',$connection);
														$query = " SELECT a.product_id, a.product_name, a.product_s_desc, a.product_url FROM jos_vm_product a ".
																"WHERE a.product_publish = 'Y' order by a.product_id DESC";

														//echo 'Q:'.$query;
														$result = mysql_query($query,$connection) or die("SELECT Error: ".mysql_error());
														$i = 0;
														while ($row = mysql_fetch_object($result))
														{
															echo '<li>';
															echo '	<a href="http://ekendotech.com/NewStyle/index.php?page=shop.product_details&flypage=shop.flypage&option=com_virtuemart&product_id='.$row->product_id.'">'.$row->product_name.'</a>';
															if($row->product_url != '')
															{
																echo '<p >*<a href="'.trim($row->product_url).'">Buy Now</a>*</p>';
															}
															else
															{
																echo '<p >*No Buy Link*</p>';
															}
															echo '</li>';

														}

										  				mysql_free_result($result);

												}
												else
												{
													echo '<b>Hot Products Mailing List</b>';
													echo '<p>';
													echo '	* Coming Soon *';
													echo '</p>';
												}

											}
                                          	?>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- box end -->
            </div>
         </div>
      </div>
   </div>
   <!-- extra-content -->
   <div id="extra-content">
     	<div class="container">
      	<div class="wrapper">
         	<div align="center">
            	<!-- box begin -->
               <div class="box maxheight">
                  <div class="border-top maxheight">
                     <div class="border-right maxheight">
                        <div class="border-bot maxheight">
                           <div class="border-left maxheight">
                              <div class="left-top-corner maxheight">
                                 <div class="right-top-corner maxheight">
                                    <div class="right-bot-corner maxheight">
                                       <div class="left-bot-corner maxheight">
                                          <div class="inner">
                                          	<?php
											if ( mosCountModules ('banner') ) {
											?>
												<div id="banner_inner">
												<?php mosLoadModules( 'banner', -1 ); ?>
												</div><?php } ?>
											<!-- different Banners //-->
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- box end -->
            </div>
        </div>
      </div>
   </div>
   <!-- footer -->
   <div id="footer">
   	<div class="container">
      	<ul class="nav">
         	<li><a href="index.php">Home</a>|</li>
         	<li><a href="http://www.ekendotech.com/NewStyle/index.php?option=com_content&sectionid=7&task=view&id=119">Software Solutions</a>|</li>
			<li><a href="http://www.ekendotech.com/NewStyle/index.php?option=com_content&task=category&id=104&Itemid=36&sectionid=12">Hot Products</a>|</li>
			<li><a href="http://www.ekendotech.com/NewStyle/index.php?option=com_content&task=category&id=107&Itemid=37&sectionid=5">Partners</a>|</li>
         	<li><a href="http://www.ekendotech.com/NewStyle/index.php?option=com_contact&Itemid=3">Contact Us</a></li>
         </ul>
         <!--</br>//-->
         <ul class="nav">
         <div class="wrapper">
         	<!--
         	<div class="fleft">Copyright &copy; 2009</div>
            <div class="fright">Designed by TemplateMonster - <a href="http://www.templatemonster.com" target="_blank">Website templates</a> provider</div>
         	//-->
         </div>
      </div>
   </div>
</body>
</html>