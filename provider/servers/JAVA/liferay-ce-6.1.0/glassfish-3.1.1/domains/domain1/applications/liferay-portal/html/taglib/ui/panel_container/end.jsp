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

<%@ include file="/html/taglib/ui/panel_container/init.jsp" %>

</div>

<aui:script use="liferay-panel">
	var panel = new Liferay.Panel(
		{
			accordion: <%= accordion %>,
			container: '#<%= id %>',
			persistState: <%= persistState %>
		}
	);

	Liferay.Panel.register('<%= id %>', panel);
</aui:script>