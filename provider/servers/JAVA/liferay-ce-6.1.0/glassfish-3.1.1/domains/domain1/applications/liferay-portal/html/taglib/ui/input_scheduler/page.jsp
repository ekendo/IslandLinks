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

<%@ page import="com.liferay.portlet.calendar.model.CalEvent" %>

<aui:fieldset>

	<%
	Calendar cal = CalendarFactoryUtil.getCalendar(timeZone, locale);
	%>

	<aui:field-wrapper label="start-date">
		<div class="aui-field-row">
			<liferay-ui:input-date
				dayParam="schedulerStartDateDay"
				dayValue="<%= cal.get(Calendar.DATE) %>"
				disabled="<%= false %>"
				firstDayOfWeek="<%= cal.getFirstDayOfWeek() - 1 %>"
				monthParam="schedulerStartDateMonth"
				monthValue="<%= cal.get(Calendar.MONTH) %>"
				yearParam="schedulerStartDateYear"
				yearValue="<%= cal.get(Calendar.YEAR) %>"
				yearRangeStart="<%= cal.get(Calendar.YEAR) %>"
				yearRangeEnd="<%= cal.get(Calendar.YEAR) + 5 %>"
			/>

			&nbsp;

			<liferay-ui:input-time
				amPmParam="schedulerStartDateAmPm"
				amPmValue="<%= cal.get(Calendar.AM_PM) %>"
				hourParam="schedulerStartDateHour"
				hourValue="<%= cal.get(Calendar.HOUR) %>"
				minuteParam="schedulerStartDateMinute"
				minuteValue="<%= cal.get(Calendar.MINUTE) %>"
				minuteInterval="<%= 1 %>"
			/>
		</div>
	</aui:field-wrapper>

	<aui:field-wrapper label="end-date">
		<div class="aui-field-row">
			<aui:input checked="<%= true %>" label="no-end-date" name="endDateType" type="radio" value="0" />
		</div>

		<div class="aui-field-row">
			<aui:input first="<%= true %>" inlineField="<%= true %>" label="end-by" name="endDateType" type="radio" value="1" />

			<liferay-ui:input-date
				dayParam="schedulerEndDateDay"
				dayValue="<%= cal.get(Calendar.DATE) %>"
				disabled="<%= false %>"
				firstDayOfWeek="<%= cal.getFirstDayOfWeek() - 1 %>"
				monthParam="schedulerEndDateMonth"
				monthValue="<%= cal.get(Calendar.MONTH) %>"
				yearParam="schedulerEndDateYear"
				yearValue="<%= cal.get(Calendar.YEAR) %>"
				yearRangeStart="<%= cal.get(Calendar.YEAR) %>"
				yearRangeEnd="<%= cal.get(Calendar.YEAR) + 5 %>"
			/>

			&nbsp;

			<liferay-ui:input-time
				hourParam="schedulerEndDateHour"
				hourValue="<%= cal.get(Calendar.HOUR) %>"
				minuteParam="schedulerEndDateMinute"
				minuteValue="<%= cal.get(Calendar.MINUTE) %>"
				minuteInterval="<%= 1 %>"
				amPmParam="schedulerEndDateAmPm"
				amPmValue="<%= cal.get(Calendar.AM_PM) %>"
			/>
		</div>

	</aui:field-wrapper>
</aui:fieldset>

<liferay-ui:input-repeat />

<aui:script>
	function <portlet:namespace />showTable(id) {
		document.getElementById("<portlet:namespace />neverTable").style.display = "none";
		document.getElementById("<portlet:namespace />dailyTable").style.display = "none";
		document.getElementById("<portlet:namespace />weeklyTable").style.display = "none";
		document.getElementById("<portlet:namespace />monthlyTable").style.display = "none";
		document.getElementById("<portlet:namespace />yearlyTable").style.display = "none";

		document.getElementById(id).style.display = "block";
	}
</aui:script>