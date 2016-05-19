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

<%@ include file="/html/portlet/roles_admin/init.jsp" %>

<%
ResultRow row = (ResultRow)request.getAttribute(WebKeys.SEARCH_CONTAINER_RESULT_ROW);

Object[] objArray = (Object[])row.getObject();

String target = (String)objArray[3];
Boolean supportsFilterByGroup = (Boolean)objArray[5];
%>

<c:if test="<%= supportsFilterByGroup %>">
	<portlet:renderURL var="selectCommunityURL" windowState="<%= LiferayWindowState.POP_UP.toString() %>">
		<portlet:param name="struts_action" value="/roles_admin/select_site" />
		<portlet:param name="target" value="<%= target %>" />
		<portlet:param name="includeCompany" value="<%= Boolean.TRUE.toString() %>" />
		<portlet:param name="includeUserPersonalSite" value="<%= Boolean.TRUE.toString() %>" />
	</portlet:renderURL>

	<%
	String limitScopeURL = "javascript:var groupWindow = window.open('" + selectCommunityURL + "', 'site', 'directories=no,height=640,location=no,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no,width=680'); void(''); groupWindow.focus();";
	%>

	<liferay-ui:icon
		image="add"
		label="<%= true %>"
		message="limit-scope"
		url="<%= limitScopeURL %>"
	/>
</c:if>