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

<%@ include file="/html/portlet/currency_converter/init.jsp" %>

<form action="<portlet:actionURL><portlet:param name="struts_action" value="/currency_converter/edit" /></portlet:actionURL>" method="post" name="<portlet:namespace />fm">
<input name="<portlet:namespace /><%= Constants.CMD %>" type="hidden" value="<%= Constants.UPDATE %>" />
<input name="<portlet:namespace />symbols" type="hidden" value="" />

<%

// Left list

List leftList = new ArrayList();

for (int i = 0; i < symbols.length; i++) {
	leftList.add(new KeyValuePair(symbols[i], LanguageUtil.get(pageContext, "currency." + symbols[i])));
}

//leftList = ListUtil.sort(leftList, new KeyValuePairComparator(false, true));

// Right list

List rightList = new ArrayList();

Arrays.sort(symbols);

Iterator itr = allSymbols.entrySet().iterator();

while (itr.hasNext()) {
	Map.Entry entry = (Map.Entry)itr.next();

	String symbol = (String)entry.getValue();
	String currencyValue = (String)entry.getKey();

	if (Arrays.binarySearch(symbols, symbol) < 0) {
		rightList.add(new KeyValuePair(symbol, LanguageUtil.get(pageContext, "currency." + currencyValue)));
	}
}

rightList = ListUtil.sort(rightList, new KeyValuePairComparator(false, true));
%>

<liferay-ui:input-move-boxes
	leftTitle="current"
	rightTitle="available"
	leftBoxName="current_actions"
	rightBoxName="available_actions"
	leftReorder="true"
	leftList="<%= leftList %>"
	rightList="<%= rightList %>"
/>

<br />

<input type="button" value="<liferay-ui:message key="save" />" onClick="<portlet:namespace />saveCurrency();" />

</form>

<aui:script>
	Liferay.provide(
		window,
		'<portlet:namespace />saveCurrency',
		function() {
			document.<portlet:namespace />fm.<portlet:namespace />symbols.value = Liferay.Util.listSelect(document.<portlet:namespace />fm.<portlet:namespace />current_actions);
			submitForm(document.<portlet:namespace />fm);
		},
		['liferay-util-list-fields']
	);
</aui:script>