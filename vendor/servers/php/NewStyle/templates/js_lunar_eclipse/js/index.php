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
            <div class="logo"><a href="index.php"><img alt="" src="<?php echo $mosConfig_live_site;?>/templates/template_636/images/logo.jpg" /></a></div>
            <ul class="top-links">
               <li><a href="index.html"><img alt="" src="<?php echo $mosConfig_live_site;?>/templates/template_636/images/top-icon1.jpg" /></a></li>
               <li><a href="#"><img alt="" src="<?php echo $mosConfig_live_site;?>/templates/template_636/images/top-icon2.jpg" /></a></li>
               <li><a href="contact-us.html"><img alt="" src="<?php echo $mosConfig_live_site;?>/templates/template_636/images/top-icon3.jpg" /></a></li>
            </ul>
         </div>
         <div class="row-2">
         	<div class="nav-box">
            	<div class="left">
               	<div class="right">
                  	<ul>
                     	<li><a href="index.php" class="first"><em><b>HOME</b></em></a></li>
                        <li><a href="about-us.html"><em><b>ABOUT US</b></em></a></li>
                        <li><a href="solutions.html"><em><b>SOFTWARE SOLUTIONS</b></em></a></li>
                        <li><a href="products.html"><em><b>HOT PRODUCTS</b></em></a></li>
                        <li><a href="partners.html"><em><b>PARTNERS</b></em></a></li>
                        <li><a href="contact-us.html" class="last"><em><b>CONTACT US</b></em></a></li>
                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- content -->
   <div id="content">
      <div class="container">
      	<!-- main-banner-big begin -->
         <div class="main-banner-big">
         	<div class="inner">
            	<h1>Even the biggest success Starts with a first step</h1>
               <a href="#" class="button">Learn More</a>
            </div>
         </div>
         <!-- main-banner-big end -->
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
                                             <h2>News Headlines</h2>
                                             <ul class="list1">
                                             	<li>
                                                   <p>BigBiz LLC announces a partnership with Clayton-Roberts - the leading financial consulting player.</p>
                                                   <a href="#">Read More</a>
                                                </li>
                                                <li>
                                                   <p>BigBiz LLC announces some other kind of thing, not necessarily a partnership.</p>
                                                   <a href="#">Read More</a>
                                                </li>
                                                <li>
                                                   <p>The third news item is usually not even being read.</p>
                                                   <a href="#">Read More</a>
                                                </li>
                                                <li>
                                                   <p>However these news items look so awesome.</p>
                                                   <a href="#">Read More</a>
                                                </li>
                                                <li>
                                                   <p>There’s no way  we could ignore them - so here they are.</p>
                                                   <a href="#">Read More</a>
                                                </li>
                                             </ul>
                                             <a href="#" class="button"><em><b>GO TO NEWS SECTION</b></em></a>
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
                                             <h2>Our Team</h2>
                                             <ul class="list2">
                                             	<li>
                                                	<img alt="" src="<?php echo $mosConfig_live_site;?>/templates/template_636/images/image_66x66.gif" />
                                                   <h5>John Doe</h5>
                                                   President or something
                                                </li>
                                                <li>
                                                	<img alt="" src="<?php echo $mosConfig_live_site;?>/templates/template_636/images/image_66x66.gif" />
                                                   <h5>Jane Doe</h5>
                                                   Assistant President
                                                </li>
                                                <li>
                                                	<img alt="" src="<?php echo $mosConfig_live_site;?>/templates/template_636/images/image_66x66.gif" />
                                                   <h5>Sam Cohen</h5>
                                                   Financial Vice President
                                                </li>
                                                <li>
                                                	<img alt="" src="<?php echo $mosConfig_live_site;?>/templates/template_636/images/image_66x66.gif" />
                                                   <h5>Jebediah Ray Tergesen</h5>
                                                   Whatever, farmer perhaps
                                                </li>
                                                <li>
                                                	<img alt="" src="<?php echo $mosConfig_live_site;?>/templates/template_636/images/image_66x66.gif" />
                                                   <h5>John Doe</h5>
                                                   President or something
                                                </li>
                                             </ul>
                                             <a href="#" class="button"><em><b>The Rest of Team</b></em></a>
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
                                             <h2>Featured Partners</h2>
                                             <ul class="list2">
                                             	<li>
                                                	<img alt="" src="<?php echo $mosConfig_live_site;?>/templates/template_636/images/image_51x51.gif" />
                                                   <h6><a href="#">Dooodle Inc.</a></h6>
                                                </li>
                                                <li>
                                                	<img alt="" src="<?php echo $mosConfig_live_site;?>/templates/template_636/images/image_51x51.gif" />
                                                   <h6><a href="#">Macrohard</a></h6>
                                                </li>
                                                <li>
                                                	<img alt="" src="<?php echo $mosConfig_live_site;?>/templates/template_636/images/image_51x51.gif" />
                                                   <h6><a href="#">A-bode</a></h6>
                                                </li>
                                                <li>
                                                	<img alt="" src="<?php echo $mosConfig_live_site;?>/templates/template_636/images/image_51x51.gif" />
                                                   <h6><a href="http://www.templatemonster.com" target="_blank">TemplateMonster.com</a> - Why not?</h6>
                                                </li>
                                                <li>
                                                	<img alt="" src="<?php echo $mosConfig_live_site;?>/templates/template_636/images/image_51x51.gif" />
                                                   <h6><a href="#">GoMommy</a></h6>
                                                </li>
                                                <li>
                                                	<img alt="" src="<?php echo $mosConfig_live_site;?>/templates/template_636/images/image_51x51.gif" />
                                                   <h6><a href="#">Lola-cola</a></h6>
                                                </li>
                                             </ul>
                                             <a href="#" class="button"><em><b>View All Partners</b></em></a>
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
                                          	<!--
                                             <h2>About Us</h2>
                                             <img class="img-indent" alt="" src="<?php echo $mosConfig_live_site;?>/templates/template_636/images/image_87x66.gif" />
                                             A perfect place to tell a couple of words about your company - just a little bit of introduction, leave the rest for a proper page.
                                             <div class="clear"></div>
                                             //-->
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
         	<li><a href="index.html">Home</a>|</li>
            <li><a href="about-us.html">About Us</a>|</li>
            <li><a href="solutions.html">Software Solutions</a>|</li>
            <li><a href="partners.html">Partners</a>|</li>
            <li><a href="consulting.html">Hot Products</a>|</li>
            <li><a href="contact-us.html">Contact Us</a></li>
         </ul>
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