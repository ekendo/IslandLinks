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

<%@ include file="/html/portlet/layout_prototypes/init.jsp" %>

<%
SearchContainer searchContainer = (SearchContainer)request.getAttribute("liferay-ui:search:searchContainer");

String redirect = searchContainer.getIteratorURL().toString();

ResultRow row = (ResultRow)request.getAttribute(WebKeys.SEARCH_CONTAINER_RESULT_ROW);

LayoutPrototype layoutPrototype = (LayoutPrototype)row.getObject();

long layoutPrototypeId = layoutPrototype.getLayoutPrototypeId();
%>

<liferay-ui:icon-menu>
	<c:if test="<%= LayoutPrototypePermissionUtil.contains(permissionChecker, layoutPrototypeId, ActionKeys.UPDATE) %>">
		<portlet:renderURL var="editURL">
			<portlet:param name="struts_action" value="/layout_prototypes/edit_layout_prototype" />
			<portlet:param name="redirect" value="<%= redirect %>" />
			<portlet:param name="layoutPrototypeId" value="<%= String.valueOf(layoutPrototypeId) %>" />
		</portlet:renderURL>

		<liferay-ui:icon
			image="edit"
			url="<%= editURL %>"
		/>
	</c:if>

	<c:if test="<%= LayoutPrototypePermissionUtil.contains(permissionChecker, layoutPrototypeId, ActionKeys.PERMISSIONS) %>">
		<liferay-security:permissionsURL
			modelResource="<%= LayoutPrototype.class.getName() %>"
			modelResourceDescription="<%= layoutPrototype.getName(locale) %>"
			resourcePrimKey="<%= String.valueOf(layoutPrototypeId) %>"
			var="permissionsURL"
		/>

		<liferay-ui:icon
			image="permissions"
			url="<%= permissionsURL %>"
		/>
	</c:if>

	<c:if test="<%= LayoutPrototypePermissionUtil.contains(permissionChecker, layoutPrototypeId, ActionKeys.DELETE) %>">
		<portlet:actionURL var="deleteURL">
			<portlet:param name="struts_action" value="/layout_prototypes/edit_layout_prototype" />
			<portlet:param name="<%= Constants.CMD %>" value="<%= Constants.DELETE %>" />
			<portlet:param name="redirect" value="<%= redirect %>" />
			<portlet:param name="layoutPrototypeIds" value="<%= String.valueOf(layoutPrototypeId) %>" />
		</portlet:actionURL>

		<liferay-ui:icon-delete
			url="<%= deleteURL %>"
		/>
	</c:if>
</liferay-ui:icon-menu>