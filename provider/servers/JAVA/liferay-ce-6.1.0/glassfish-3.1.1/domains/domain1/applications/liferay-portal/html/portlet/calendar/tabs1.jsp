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

<%@ include file="/html/portlet/calendar/init.jsp" %>

<%
String tabs1 = ParamUtil.getString(request, "tabs1", tabs1Default);

PortletURL tabs1URL = renderResponse.createRenderURL();

tabs1URL.setParameter("struts_action", "/calendar/view");
tabs1URL.setParameter("month", String.valueOf(selMonth));
tabs1URL.setParameter("day", String.valueOf(selDay));
tabs1URL.setParameter("year", String.valueOf(selYear));
%>

<liferay-ui:tabs
	names="<%= tabs1Names %>"
	url="<%= tabs1URL.toString() %>"
	value="<%= tabs1 %>"
/>