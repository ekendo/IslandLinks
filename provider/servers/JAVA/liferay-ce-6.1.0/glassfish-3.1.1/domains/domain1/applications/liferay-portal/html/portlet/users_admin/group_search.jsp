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

<%@ include file="/html/portlet/users_admin/init.jsp" %>

<%
GroupSearch searchContainer = (GroupSearch)request.getAttribute("liferay-ui:search:searchContainer");
boolean showAddButton = GetterUtil.getBoolean((String)request.getAttribute("liferay-ui:search:showAddButton"));

GroupDisplayTerms displayTerms = (GroupDisplayTerms)searchContainer.getDisplayTerms();
%>

<span class="aui-search-bar lfr-display-terms-search">
	<aui:input inlineField="<%= true %>" label="" name="<%= displayTerms.NAME %>" size="30" type="text" value="<%= displayTerms.getName() %>" />

	<aui:button type="submit" value="search" />
</span>

<br />

<div>
	<c:if test="<%= showAddButton && PortalPermissionUtil.contains(permissionChecker, ActionKeys.ADD_COMMUNITY) %>">
		<aui:button onClick='<%= renderResponse.getNamespace() + "addGroup();" %>' value="add-site" />
	</c:if>
</div>

<aui:script>
	function <portlet:namespace />addGroup() {
		document.<portlet:namespace />fm.method = 'post';
		submitForm(document.<portlet:namespace />fm, '<portlet:renderURL><portlet:param name="struts_action" value="/sites_admin/edit_site" /><portlet:param name="redirect" value="<%= currentURL %>" /></portlet:renderURL>');
	}

	<c:if test="<%= windowState.equals(WindowState.MAXIMIZED) %>">
		Liferay.Util.focusFormField(document.<portlet:namespace />fm.<portlet:namespace /><%= displayTerms.NAME %>);
	</c:if>
</aui:script>