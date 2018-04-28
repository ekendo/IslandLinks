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

<%@ include file="/html/taglib/ui/panel_floating_container/init.jsp" %>

</div>

<aui:script use="liferay-panel-floating">
	var panel = new Liferay.PanelFloating(
		{
			accordion: <%= accordion %>,
			container: '#<%= id %>',
			paging: <%= paging %>,
			pagingElements: '<%= pagingElements %>',
			persistState: <%= persistState %>,
			resultsPerPage: <%= resultsPerPage %>,
			trigger: '<%= trigger %>',
			width: <%= width %>
		}
	);

	Liferay.Panel.register('<%= id %>', panel);
</aui:script>