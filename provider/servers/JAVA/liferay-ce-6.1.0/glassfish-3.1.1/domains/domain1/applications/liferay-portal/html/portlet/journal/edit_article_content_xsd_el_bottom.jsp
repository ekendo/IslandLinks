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
boolean closeDroppableTag = GetterUtil.getBoolean((String)request.getAttribute(WebKeys.JOURNAL_STRUCTURE_CLOSE_DROPPABLE_TAG));
%>

<c:choose>
	<c:when test="<%= closeDroppableTag %>">
		</ul>
	</c:when>
	<c:otherwise>
		</span></li>
	</c:otherwise>
</c:choose>