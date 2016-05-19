#if ($browserSniffer.isIe($request) && $browserSniffer.getMajorVersion($request) < 8)
	<div class="columns-1" id="main-content" role="main">
		<table class="portlet-layout">
		<tr>
			<td class="portlet-column portlet-column-only" id="column-1">
				$processor.processColumn("column-1", "portlet-column-content portlet-column-content-only")
			</td>
		</tr>
		</table>
	</div>
#else
	<div class="columns-1" id="main-content" role="main">
		<div class="portlet-layout">
			<div class="portlet-column portlet-column-only" id="column-1">
				$processor.processColumn("column-1", "portlet-column-content portlet-column-content-only")
			</div>
		</div>
	</div>
#end