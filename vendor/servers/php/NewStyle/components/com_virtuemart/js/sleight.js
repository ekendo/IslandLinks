/**********************************************************
Sleight
(c) 2001, Aaron Boodman
http://www.youngpup.net
**********************************************************/

if (navigator.platform == "Win32" && navigator.appName == "Microsoft Internet Explorer" && window.attachEvent)
{
	document.writeln('<style type="text/css">img { visibility:hidden; } </style>');
	window.attachEvent("onload", fnLoadPngs);
}

function fnLoadPngs()
{
	var rslt = navigator.appVersion.match(/MSIE (\d+\.\d+)/, '');
	var itsAllGood = (rslt != null && Number(rslt[1]) >= 5.5);

	for (var i = document.images.length - 1, img = null; (img = document.images[i]); i--)
	{
		if (itsAllGood && img.src.match(/(.*)\/com_virtuemart\/(.*)\.png$/i) != null)
		{
			var src = img.src;
			var span = document.createElement("span");
			span.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + src + "', sizing='scale')"
			span.style.width = img.width + "px";
			span.style.height = img.height + "px";
			span.style.cursor = "pointer";
			span.title = img.title;
			img.replaceNode(span);
		}
		img.style.visibility = "visible";
	}
}
