<?php
defined( '_VALID_MOS' ) or die( 'Restricted access' );
// needed to seperate the ISO number from the language file constant _ISO
$iso = explode( '=', _ISO );
// xml prolog
echo '<?xml version="1.0" encoding="'. $iso[1] .'"?' .'>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">		
<!-- Start Open Web Analytics Tracker -->
<script type="text/javascript">
//<![CDATA[
var owa_baseUrl = 'http://data.ekendotech.com/Data/php/owa/';
var owa_cmds = owa_cmds || [];
owa_cmds.push(['setSiteId', 'a9d4bfe0021e90b7fbf57e072f4e15cb']);
owa_cmds.push(['trackPageView']);
owa_cmds.push(['trackClicks']);
owa_cmds.push(['trackDomStream']);

(function() {
	var _owa = document.createElement('script'); _owa.type = 'text/javascript'; _owa.async = true;
	owa_baseUrl = ('https:' == document.location.protocol ? window.owa_baseSecUrl || owa_baseUrl.replace(/http:/, 'https:') : owa_baseUrl );
	_owa.src = owa_baseUrl + 'modules/base/js/owa.tracker-combined-min.js';
	var _owa_s = document.getElementsByTagName('script')[0]; _owa_s.parentNode.insertBefore(_owa, _owa_s);
}());
//]]>
</script>
<!-- End Open Web Analytics Code -->
<head>
<title>#YourNerds!</title>
<meta name="description" content="#Your Nerds!: Sharing place for common sense technology from hackers, programmers, makers, designers and inventors different hardware and software communities. If you're looking for alternatives to the proprietary, subsidized consumer model, come hang out with us and share!" />
<meta name="keywords" content=" laptops, desktops, settops, nettops, netbooks, tablets, hardware, circuits, makers, software, DIY, electronics, products, nerds, neets, geeks, Software, Hardware, custom, Solutions, tutorials, web links, Hacker, Designer, developer, programmer, software engineer, open source, open source hardware, source, #YourNerds!, #EkendoTech!, pcb" />
<meta name="robots" content="index, follow" />
<?php
if ( $my->id ) {
	initEditor();
}

?>
<meta http-equiv="Content-Type" content="text/html; <?php echo _ISO; ?>" />
<link href="<?php echo $mosConfig_live_site;?>/templates/template_636/css/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $mosConfig_live_site;?>/templates/template_636/css/layout.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="http://ekendotech.com/NewStyle/templates/template_636/css/global.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
<script src="http://yournerds.ekendotech.com/NewStyle/templates/template_636/js/jquery/easing/jquery.easing.1.3.js"></script>
<script src="http://yournerds.ekendotech.com/NewStyle/templates/template_636/js/slides.min.jquery.js"></script>
<script>
	$(function(){
		$('#slides').slides({
			preload: true,
			preloadImage: 'http://ekendotech.com/NewStyle/templates/template_636/images/loading.gif',
			play: 5000,
			pause: 2500,
			hoverPause: true
		});
	});
</script>
<script src="<?php echo $mosConfig_live_site;?>/templates/template_636/js/maxheight.js" type="text/javascript"></script>
<!--[if lt IE 7]>
	<link href="<?php echo $mosConfig_live_site;?>/templates/template_636/css/ie_style.css" rel="stylesheet" type="text/css" />
<![endif]-->
<script src="https://apis.google.com/js/platform.js" async defer></script>
</head>

<body id="page1" onload="new ElementMaxHeight();">
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
	</script>
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
										    <li><a href="http://yournerds.ekendotech.com/NewStyle/index.php?option=com_content&sectionid=7&task=view&id=119"><em><b>CUSTOM SOLUTIONS</b></em></a></li>
										    <li><a href="http://yournerds.ekendotech.com/NewStyle/index.php?option=com_content&task=category&id=104&sectionid=12&s=Gpls"><em><b>LAPTOPS & DESKTOPS</b></em></a></li>
										    <li><a href="http://yournerds.ekendotech.com/NewStyle/index.php?option=com_content&task=category&id=109&sectionid=12&s=twtr"><em><b>SMARTPHONES & TABLETS</b></em></a></li>
                     						<li><a href="http://yournerds.ekendotech.com/NewStyle/index.php?option=com_content&task=category&id=114&sectionid=12&s=Gpls"><em><b>NETBOOKS & SETTOPS</b></em></a></li>
										    <li><a href="http://yournerds.ekendotech.com/NewStyle/index.php?option=com_content&section=7&task=view&id=162&s=twtr" class="last"><em><b>ABOUT US</b></em></a></li>
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
         <!-- main-banner-big begin
		          <div class="main-banner-big">
		          	<div class="inner">-->
		             	<!--<h1>Even the biggest success Starts with a first step</h1>//-->
		             	<div id="container">
							<div id="example">
								<div id="slides">
									<div class="slides_container">
										<!--<img src="http://ekendotech.com/Joomla/images/636_images/big_banner_weekly_custom_solutions.png" width="950" height="303" alt="Custom Solutions">//-->
										<img src="http://ekendotech.com/Joomla/images/636_images/big_banner_weekly_hardware_recs.png" width="950" height="303" alt="Hardware Recommendations">
										<img src="http://ekendotech.com/Joomla/images/636_images/big_banner_weekly_server_software_recs.png" width="950" height="303" alt="Server Software Recommendations">
										<img src="http://ekendotech.com/Joomla/images/636_images/big_banner_weekly_mobile_software_recs.png" width="950" height="303" alt="Mobile Software Recommendations">
										<img src="http://ekendotech.com/Joomla/images/636_images/big_banner_weekly_software_recs.png" width="950" height="303" alt="Desktop Software Recommendations">
										<!--<img src="http://ekendotech.com/Joomla/images/636_images/big_banner_weekly_ultra-peer.png" width="950" height="303" alt="Ultra-Peer Workstations">//-->
									</div>
									<a href="#" class="prev"><img src="http://ekendotech.com/NewStyle/templates/template_636/images/arrow-prev.png" width="24" height="43" alt="Arrow Prev"></a>
									<a href="#" class="next"><img src="http://ekendotech.com/NewStyle/templates/template_636/images/arrow-next.png" width="24" height="43" alt="Arrow Next"></a>
								</div>
								<img src="http://ekendotech.com/NewStyle/templates/template_636/images/example-frame.png" width="739" height="341" alt="" id="frame">
							</div>
						</div>
		                <!--<a href="#" class="button">Learn More</a>
		             </div>
		          </div>//-->
         <!-- main-banner-big end -->
         <?php
         	}
         ?>

         <?php
		   if(($_REQUEST['option']==="com_contact")||($_REQUEST['option']==="com_content")||($_REQUEST['option']=== "com_virtuemart")||
		   	  ($_REQUEST['option']=== "com_weblinks")||($_REQUEST['option']=== "com_login")||($_REQUEST['option']=== "com_user")||($_REQUEST['option']=== "com_registration"))
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
					else if(($_REQUEST["task"] === 'category')&&($_REQUEST["id"] === '114'))
															{
									  ?>
											  <!-- main-banner-small-jenn begin -->
													  <div class="main-banner-small-jenn">
														<div class="inner">
															<h1>Netbooks & SetTops</h1>
														</div>
													  </div>
											  <!-- main-banner-small end -->
									  <?php
					}
					else if(($_REQUEST["task"] === 'category')&&($_REQUEST["id"] === '109'))
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
					else if(($_REQUEST["task"] === 'view')&&($_REQUEST["id"] === '119'))
															{
									  ?>
											  <!-- main-banner-small-angl begin -->
													  <div class="main-banner-small-angl">
														<div class="inner">
															<h1>Custom Solutions</h1>
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
					else if((($_REQUEST["option"] === 'com_contact')&&($_REQUEST["Itemid"] === '3'))||(($_REQUEST["task"] === 'view')&&($_REQUEST["id"] === '162')))
										{
									  ?>
											  <!-- main-banner-small-errol begin -->
													<div class="main-banner-small-errol">
														<div class="inner">
															<h1>About Us</h1>
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

				  			                                         		$connection = mysql_connect($mosConfig_host,"ekendojoomla1","phpcms");
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
											 		echo '<h2><a href="http://yournerds.ekendotech.com/NewStyle/index.php?option=com_content&task=blogcategory&id=0&Itemid=35&s=twtr">Nerd News Blog</a></h2>';
													$connection = mysql_connect("$mosConfig_host","ekendojoomla1","phpcms");
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
														$connection = mysql_connect("$mosConfig_host","ekendojoomla1","phpcms");
														mysql_select_db('ekendocms1',$connection);
														$query = " SELECT a.id, a.title, a.introtext, a.metadesc FROM jos_content a, jos_sections b ".
																"WHERE b.id = 10 AND a.sectionid = b.id AND a.state>0 order by a.id DESC";

														//echo 'Q:'.$query;
														$result = mysql_query($query,$connection) or die("SELECT Error: ".mysql_error());
														$i = 0;
														while ($row = mysql_fetch_object($result))
														{
															echo '<li>';
															echo '	<a href="http://yournerds.ekendotech.com/NewStyle/index.php?option=com_content&task=view&id='.$row->id.'">'.$row->title.'</a>';
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
													else //if($_REQUEST["id"] === '162')
													{
														echo '<div align="center"><b>Web Links & Updates</b></div>';
														echo '<br>&nbsp;</br>';
														// Main Menu
														mosLoadModules ( 'left' );

														//top Menu
														//mosLoadModules ( 'user3' );

														//search
														//mosLoadModules ( 'user4' );
														echo '<p>';
														echo '<br>&nbsp;</br>';
														echo '</p>';
														echo '<p>';
														echo '<br>&nbsp;</br>';
														echo '</p>';
														echo '<p>';
														echo '<br>&nbsp;</br>';
														echo '</p>';
														echo '<p>';
														echo '<br>&nbsp;</br>';
														echo '</p>';
														echo '<p>';
														echo '<br>&nbsp;</br>';
														echo '</p>';
														echo '<p>';
														echo '<br>&nbsp;</br>';
														echo '</p>';

													}

													/*
													else
													{
														echo '<b>Tech (News) Blog</b>';
														echo '</br>';
														//echo '<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>';
														//echo '<g:plus href="https://plus.google.com/110909329644672397966" rel="author" width="170" height="69"></g:plus>';
														//echo '</br>';
														//echo '<div class="fb-like" data-href="http://www.facebook.com/groups/yournerds/" data-send="false" data-layout="button_count" data-width="450" data-height="200" data-show-faces="true" data-font="verdana"></div>';
														//echo '&nbsp;|&nbsp;';
														//echo '<font style="verdana"><a href="http://www.facebook.com/groups/yournerds/" target="_blank">FaceBook Group</a></font>';
														//echo '</p>';
														//echo '<font style="verdana"><a href="http://identi.ca/group/yournerds" target="_blank">Identi.ca Group</a></font>';
														//echo '</p>';
														//echo '<font style="verdana"><a href="http://www.ekendotech.net/phplist/lists/?p=subscribe&id=3" target="_blank">Mailing List</a></font>';
														//echo '</p>';

													}
													*/
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
											 		echo '<h2><a href="http://yournerds.ekendotech.com/NewStyle/index.php?option=com_content&task=blogcategory&id=0&Itemid=30&s=Gpls">Free Software</a></h2>';
										  			$connection = mysql_connect("$mosConfig_host","ekendojoomla1","phpcms");
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
														echo '<h2>Spare Parts</br>(Used Hardware)</h2>';

														// trial stuff (try link/buy link)
														$connection = mysql_connect("$mosConfig_host","ekendojoomla1","phpcms");
														mysql_select_db('ekendocms1',$connection);
														$query = " SELECT a.id, a.title, a.introtext, a.metadesc, a.metakey FROM jos_content a, jos_sections b ".
																"WHERE b.id = 17 AND a.sectionid = b.id AND a.state>0 order by a.id DESC";

														//echo 'Q:'.$query;
														$result = mysql_query($query,$connection) or die("SELECT Error: ".mysql_error());
														$i = 0;
														while ($row = mysql_fetch_object($result))
														{
															echo '<li>';
															echo '	<a href="http://yournerds.ekendotech.com/NewStyle/index.php?option=com_content&task=view&id='.$row->id.'">'.$row->title.'</a>';
															// trade
															// buy
															// pay shipng $2


														}

										  				mysql_free_result($result);
														/*
														echo '<h2>Trial Software</h2>';

														// trial stuff (try link/buy link)
														$connection = mysql_connect("$mosConfig_host","ekendojoomla1","phpcms");
														mysql_select_db('ekendocms1',$connection);
														$query = " SELECT a.id, a.title, a.introtext, a.metadesc, a.metakey FROM jos_content a, jos_sections b ".
																"WHERE b.id = 14 AND a.sectionid = b.id AND a.state>0 order by a.id DESC";

														//echo 'Q:'.$query;
														$result = mysql_query($query,$connection) or die("SELECT Error: ".mysql_error());
														$i = 0;
														while ($row = mysql_fetch_object($result))
														{
															echo '<li>';
															echo '	<a href="http://yournerds.ekendotech.com/NewStyle/index.php?option=com_content&task=view&id='.$row->id.'">'.$row->title.'</a>';
															//echo 	$row->introtext;
															//echo '	<href="'.trim($row->metadesc).'">Download</a>';
															if($row->metadesc != '')
															{
																//echo '<p>*<a href="'.trim($row->metadesc).'">Try Now</a>* | *<a href="'.trim($row->metakey).'">Buy Now</a>*</p>';
															}
															else
															{
																//echo '<p>*No Trial Link* | *No Buy Link*</p>';
															}
															echo '</li>';

														}

										  				mysql_free_result($result);
										  				*/

													}
													else //if($_REQUEST["id"] === '162')
													{
														echo '<div align="center"><b>Submit Links to:<br>Teardowns, Tutorials, Inventions, etc</br> <!--Links, Parts, Solutions,  Teardowns or Tutorials//--></b></div>';
														echo '<br>&nbsp;</br>';
														// Main Menu
														//mosLoadModules ( 'left' );

														//Login
														mosLoadModules ( 'cpanel' );
														echo '<br>&nbsp;</br>';
														//User Menu
														mosLoadModules ( 'toolbar' );

														echo '<p>';
														echo '<br>&nbsp;</br>';
														echo '</p>';
														echo '<p>';
														echo '<br>&nbsp;</br>';
														echo '</p>';
														echo '<p>';
														echo '<br>&nbsp;</br>';
														echo '</p>';
														echo '<p>';
														echo '<br>&nbsp;</br>';
														echo '</p>';
														echo '<p>';
														echo '<br>&nbsp;</br>';
														echo '</p>';
														echo '<p>';
														echo '<br>&nbsp;</br>';
														echo '</p>';


													}

													/*
													else
													{
														echo '<b>Free, Trial & Premium (Software) </b>';
														echo '</p>';
														//echo '<font style="verdana"><a href="http://www.ekendotech.net/phplist/lists/?p=subscribe&id=4" target="_blank">Mailing List</a></font>';
														//echo '</p>';
													}
													*/
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
												echo '<h2><a href="http://yournerds.ekendotech.com/NewStyle/index.php?option=com_content&sectionid=17&id=170&task=view&s=twtr">Used Hardware</a></h2>';
                                          		$connection = mysql_connect("$mosConfig_host","ekendojoomla1","phpcms");
												mysql_select_db('ekendocms1',$connection);
												$query = " SELECT a.id, a.title FROM jos_content a, jos_sections b ".
														"WHERE b.id = 17 AND a.sectionid = b.id AND a.state>0 order by a.id DESC";

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
													echo '<h2><a name="PremiumProducts">Premium Products</a></h2>';

													// virtue mart stuff (buy link)
														$connection = mysql_connect("$mosConfig_host","ekendojoomla1","phpcms");
														mysql_select_db('ekendocms1',$connection);
														$query = " SELECT a.product_id, a.product_name, a.product_s_desc, a.product_url FROM jos_vm_product a ".
																"WHERE a.product_publish = 'Y' order by a.product_id DESC";

														//echo 'Q:'.$query;
														$result = mysql_query($query,$connection) or die("SELECT Error: ".mysql_error());
														$i = 0;
														while ($row = mysql_fetch_object($result))
														{
															echo '<li>';
															echo '	<a href="http://ekendotech.com/NewStyle/index.php?page=shop.product_details&flypage=shop.flypage&option=com_virtuemart&s=twtr&product_id='.$row->product_id.'">'.$row->product_name.'</a>';
															if($row->product_url != '')
															{
																echo '<p ></p>';//*<a href="'.trim($row->product_url).'">Buy Now</a>*</p>';
															}
															else
															{
																echo '<p ></p>';//*No Buy Link*</p>';
															}
															echo '</li>';

														}
                                                                  
										  				mysql_free_result($result);

												}
												else //if($_REQUEST["id"] === '162')
												{
													echo '<div align="center"><b>Social Updates</b></div>';  
													
													if($_REQUEST["s"] =='Dspr')
													{
														//Diaspora
														echo '<iframe id="DsprFrame" width="225" src="https://diasp.org/tags/yournerds"></iframe>';
														echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
													}
													
                                                                                                       	if($_REQUEST["s"] =='twtr')
                                                                                                       	{
												        	//twitter
														echo '<a class="twitter-timeline"  href="https://twitter.com/search?q=YourNerds"  data-widget-id="376105231601250306">Tweets about "#YourNerds"</a>';
														echo "<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id))";
														echo '{js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}';
														echo '}(document,"script","twitter-wjs");</script>';
														echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                                                                                       	}
                                                                                                        //echo 'S:='.$_REQUEST["s"];
                                                                                                        
                                                                                                       if($_REQUEST["s"]=="Gpls")
                                                                                                       {
                                                                                                               echo '<div class="g-page" data-width="225" data-href="//plus.google.com/u/0/110909329644672397966" data-rel="publisher"></div>';
                                                                                                       }
                                                                                                       

													echo '<div align="center">';
													//echo '</br>';
													echo '<a href="https://diasp.org/tags/yournerds" target="_blank"><img src="http://yournerds.ekendotech.com/Joomla/images/stories/200px-logo.png" align="left" hspace="2" alt="Diaspora" width="25" height"25"/></a>&nbsp;&nbsp;';
													echo '<a href="https://www.reddit.com/r/yournerds/" target="_blank"><img src="http://yournerds.ekendotech.com/Joomla/images/stories/spreddit5.gif.png" align="left" hspace="2" alt="Reddit" width="25" height"25"/></a>&nbsp;&nbsp;';
													echo '<a href="http://i.youku.com/yournerds" target="_blank"><img src="http://yournerds.ekendotech.com/Joomla/images/stories/imagesyouku.jpg" align="left" hspace="2" alt="Youku" width="25" height"25"/></a>&nbsp;&nbsp;';
													echo '<a href="index.php?option=com_content&section=7&task=view&id=162&s=twtr"><img src="http://yournerds.ekendotech.com/Joomla/images/stories/tc350.13-twitter-logo1.jpg" align="left" hspace="2" alt="Twitter" width="25" height"25"/></a>&nbsp;&nbsp;';
													echo '<a href="index.php?option=com_content&section=7&task=view&id=162&s=Gpls"><img src="http://yournerds.ekendotech.com/Joomla/images/stories/google-plus-icon.png" align="left" hspace="2" alt="Google+" width="25" height"25"/></a>&nbsp;&nbsp;';
													echo '<a href="http://weibo.com/yournerds?is_all=1" target="_blank"><img src="http://yournerds.ekendotech.com/Joomla/images/weibo_index.jpg" align="left" hspace="2" alt="Weibo" width="25" height"25" /></a>&nbsp;&nbsp;';
													echo '<a href="https://www.facebook.com/groups/yournerds/" target="_blank"><img src="http://yournerds.ekendotech.com/Joomla/images/stories/logo_facebook.png" align="left" hspace="2" alt="FaceBook" width="25" height"25"/></a>&nbsp;&nbsp;';
													//echo '</br>';
													echo '</div>';
													echo '<p>';
													echo '<br>&nbsp;</br>';
													echo '</p>';

												}

												/*
												else
												{
													echo '<b>Hot Products (Hardware)</b>';
													echo '</p>';
													//echo '<font style="verdana"><a href="http://www.ekendotech.net/phplist/lists/?p=subscribe&id=5" target="_blank">Mailing List</a></font>';
													//echo '</p>';

												}
												*/

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
   	<div class="center">
      	<ul class="nav">
         	<li><a href="http://yournerds.ekendotech.com/NewStyle/index.php?option=com_content&sectionid=7&task=view&id=119">Custom Solutions</a>|</li>
			<li><a href="http://yournerds.ekendotech.com/NewStyle/index.php?option=com_content&task=category&id=104&Itemid=36&sectionid=12&s=Gpls">Desktops & Laptops</a>|</li>
			<li><a href="http://yournerds.ekendotech.com/NewStyle/index.php?option=com_content&task=category&id=109&Itemid=36&sectionid=12&s=twtr">SmartPhones & Tablets</a>|</li>
			<li><a href="http://yournerds.ekendotech.com/NewStyle/index.php?option=com_content&task=category&id=114&Itemid=36&sectionid=12&s=Gpls">NetBooks & SetTops</a>|</li>
			<li><a href="index.php">Home</a>|</li>
         	<li><a href="http://yournerds.ekendotech.com/NewStyle/index.php?option=com_content&section=7&task=view&id=162s=twtr">About Us</a></li>
         </ul>
         <!--</br>//-->
         <ul class="nav">
         <div class="wrapper">

         	<div class="center"><a href="https://plus.google.com/110909329644672397966" rel="publisher" target="_blank">Google+</a> | <a href="http://www.weibo.com/yournerds" target="_blank">Weibo</a> | <a href="http://www.reddit.com/r/yournerds/" target="_blank">Reddit</a></div>
            <div class="center"><a href="https://joindiaspora.com/tags/yournerds"  target="_blank">JoinDiaspora</a> | <a href="http://i.youku.com/yournerds"  target="_blank">YouKu</a> | <a href="https://www.facebook.com/groups/yournerds/"  target="_blank">FaceBook Group</a> | <a href="https://twitter.com/hashtag/yournerds?src=hash"  target="_blank">Twitter</a></div>
         </div>
      </div>
   </div>
</body>
</html>