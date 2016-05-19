<%--
/**
 * Copyright (c) 2000-2012 Liferay, Inc. All rights reserved.
 *
 * This library is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Lesser General Public License as published by the Free
 * Software Foundation; either version 2.1 of the License, or (at your option)
 * any later version.
 *
 * This library is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more
 * details.
 */
--%>

<%@ include file="/html/taglib/init.jsp" %>

<%
String bulletStyle = ((String)request.getAttribute("liferay-ui:navigation:bulletStyle")).toLowerCase();
String displayStyle = (String)request.getAttribute("liferay-ui:navigation:displayStyle");

String headerType = null;
String rootLayoutType = null;
int rootLayoutLevel = 0;
String includedLayouts = null;
boolean nestedChildren = true;

String[] displayStyleDefinition = _getDisplayStyleDefinition(displayStyle);

if ((displayStyleDefinition != null) && (displayStyleDefinition.length != 0)) {
	headerType = displayStyleDefinition[0];
	rootLayoutType = displayStyleDefinition[1];
	rootLayoutLevel = GetterUtil.getInteger(displayStyleDefinition[2]);
	includedLayouts = displayStyleDefinition[3];

	if (displayStyleDefinition.length > 4) {
		nestedChildren = GetterUtil.getBoolean(displayStyleDefinition[4]);
	}
}
else {
	headerType = (String)request.getAttribute("liferay-ui:navigation:headerType");
	rootLayoutType = (String)request.getAttribute("liferay-ui:navigation:rootLayoutType");
	rootLayoutLevel = GetterUtil.getInteger((String)request.getAttribute("liferay-ui:navigation:rootLayoutLevel"));
	includedLayouts = (String)request.getAttribute("liferay-ui:navigation:includedLayouts");
	nestedChildren = GetterUtil.getBoolean((String)request.getAttribute("liferay-ui:navigation:nestedChildren"));
}
%>

<%!
private String[] _getDisplayStyleDefinition(String displayStyle) {
	return PropsUtil.getArray("navigation.display.style", new Filter(displayStyle));
}
%>