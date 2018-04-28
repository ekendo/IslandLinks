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
CalEvent event = (CalEvent)request.getAttribute(WebKeys.CALENDAR_EVENT);
%>

<liferay-ui:custom-attributes-available className="<%= CalEvent.class.getName() %>">
	<liferay-ui:custom-attribute-list
		className="<%= CalEvent.class.getName() %>"
		classPK="<%= (event != null) ? event.getEventId() : 0 %>"
		editable="<%= false %>"
		label="<%= true %>"
	/>
</liferay-ui:custom-attributes-available>

<%= event.getDescription() %><br />

<liferay-ui:icon
	image="../common/calendar"
/>
<liferay-ui:message key="start-date" />:

<c:choose>
	<c:when test="<%= event.isTimeZoneSensitive() %>">
		<%= dateFormatDate.format(Time.getDate(event.getStartDate(), timeZone)) %>
	</c:when>
	<c:otherwise>
		<%= dateFormatDate.format(event.getStartDate()) %>
	</c:otherwise>
</c:choose>