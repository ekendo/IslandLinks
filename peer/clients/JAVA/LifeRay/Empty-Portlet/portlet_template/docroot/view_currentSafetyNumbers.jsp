<%
/**
 * Copyright (c) 2000-2008 Liferay, Inc. All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
%>

<%@ taglib uri="http://java.sun.com/portlet_2_0" prefix="portlet" %>

<portlet:defineObjects />
<%
        String caseDate ="05/30/2010";
        String headCount = "609";
        String incidentDate = "05/30/2010";

        // get values from properties
        try
        {
             Properties props = new Properties();

             props.load(new FileInputStream("currentSafetyNumbers.properties"));

             caseDate = props.getProperty("CaseDate");
             incidentDate = props.getProperty("IncidentDate");
             headCount = props.getProperty("HeadCount");
             //props.close();
        }
        catch(Exception exc)
        {

        }
%>
<form id="frmSafetyCalc" method="post" action="view_updatedSafetyNumbers.jsp">
        <table cellpadding="10" cellspacing="10" align="center">
            <tr>
                <th>
                    Last OSHA Recordable
                </th>
                <th>
                    Last Workday Case
                </th>
                <th>
                    Head Count
                </th>
            </tr>
            <tr>
                <td>
                    <input id="txtIncidentDate" name="incidentDate" type="text" value="<%=incidentDate%>" />
                </td>
                <td >
                    <input id="txtCaseDate" name="caseDate" type="text" value="<%=caseDate%>" />
                </td>
                <td>
                    <input id="txtHeadCount" name="headCount" type="text" value="<%=headCount%>" />
                </td>
            </tr>
            <tr>
                <td colspan="3" align="center">
                    <input type="submit" value="calculate"/>
                </td>
            </tr>
        </table>
        </form>