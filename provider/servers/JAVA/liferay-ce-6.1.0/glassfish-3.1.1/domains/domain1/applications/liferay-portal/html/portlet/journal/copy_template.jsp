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

<%@ include file="/html/portlet/journal/init.jsp" %>

<%
String redirect = ParamUtil.getString(request, "redirect");

long groupId = ParamUtil.getLong(request, "groupId");
String oldTemplateId = ParamUtil.getString(request, "oldTemplateId");
String newTemplateId = ParamUtil.getString(request, "newTemplateId");
%>

<portlet:actionURL var="copyTemplateURL">
	<portlet:param name="struts_action" value="/journal/copy_template" />
</portlet:actionURL>

<aui:form action="<%= copyTemplateURL %>" method="post" name="fm">
	<aui:input name="<%= Constants.CMD %>" type="hidden" value="<%= Constants.COPY %>" />
	<aui:input name="redirect" type="hidden" value="<%= redirect %>" />
	<aui:input name="groupId" type="hidden" value="<%= groupId %>" />
	<aui:input name="oldTemplateId" type="hidden" value="<%= oldTemplateId %>" />

	<liferay-ui:header
		backURL="<%= redirect %>"
		title="template"
	/>

	<liferay-ui:error exception="<%= DuplicateTemplateIdException.class %>" message="please-enter-a-unique-id" />
	<liferay-ui:error exception="<%= TemplateIdException.class %>" message="please-enter-a-valid-id" />

	<aui:fieldset>
		<aui:field-wrapper label="id">
			<%= oldTemplateId %>
		</aui:field-wrapper>

		<c:choose>
			<c:when test="<%= PropsValues.JOURNAL_TEMPLATE_FORCE_AUTOGENERATE_ID %>">
				<liferay-ui:message key="autogenerate-id" />

				<aui:input name="newTemplateId" type="hidden" />
				<aui:input name="autoTemplateId" type="hidden" value="<%= true %>" />
			</c:when>
			<c:otherwise>
				<aui:input bean="<%= null %>" cssClass="lfr-input-text-container" field="templateId" fieldParam="newTemplateId" label="new-id" model="<%= JournalTemplate.class %>" name="newTemplateId" value="<%= newTemplateId %>" />
			</c:otherwise>
		</c:choose>
	</aui:fieldset>

	<aui:button-row>
		<aui:button type="submit" value="copy" />

		<aui:button href="<%= redirect %>" type="cancel" />
	</aui:button-row>
</aui:form>

<c:if test="<%= windowState.equals(WindowState.MAXIMIZED) %>">
	<aui:script>
		Liferay.Util.focusFormField(document.<portlet:namespace />fm.<portlet:namespace />newTemplateId);
	</aui:script>
</c:if>