/**********************************************************
Sleight for Backgrounds
Original code (c) 2001 Aaron Boodman, http://www.youngpup.net
This version (c) 2003 Drew McLellan, http://www.allinthehead.com
**********************************************************/
if (navigator.platform == "Win32" && navigator.appName == "Microsoft Internet Explorer" && window.attachEvent) {
	window.attachEvent("onload", alphaBackgrounds);
}

function alphaBackgrounds(){
	var rslt = navigator.appVersion.match(/MSIE (\d+\.\d+)/, '');
	var itsAllGood = (rslt != null && Number(rslt[1]) >= 5.5);
	for (i=0; i<document.all.length; i++){
		var bg = document.all[i].currentStyle.backgroundImage;
		if (itsAllGood && bg){
			if (bg.match(/(.*)\/com_virtuemart\/(.*)\.png$/i) != null){
				var mypng = bg.substring(5,bg.length-2);
				document.all[i].style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+mypng+"', sizingMethod='scale')";
				document.all[i].style.backgroundImage = "url('components/com_virtuemart/shop_image/blank.gif')";
			}
		}
	}
}
