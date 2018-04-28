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
ResultRow row = (ResultRow)request.getAttribute(WebKeys.SEARCH_CONTAINER_RESULT_ROW);
SearchEntry entry = (SearchEntry)request.getAttribute(WebKeys.SEARCH_CONTAINER_RESULT_ROW_ENTRY);

AnnouncementsDelivery delivery = (AnnouncementsDelivery)row.getObject();

int index = entry.getIndex();

String param = "announcementsType" + delivery.getType();
boolean defaultValue = false;
boolean disabled = false;

if (index == 1) {
	param += "Email";
	defaultValue = delivery.isEmail();
}
else if (index == 2) {
	param += "Sms";
	defaultValue = delivery.isSms();
}
else if (index == 3) {
	param += "Website";
	defaultValue = delivery.isWebsite();
	disabled = true;
}
%>

<aui:input disabled="<%= disabled %>" label="" name="<%= param %>" type="checkbox" value="<%= defaultValue %>" />