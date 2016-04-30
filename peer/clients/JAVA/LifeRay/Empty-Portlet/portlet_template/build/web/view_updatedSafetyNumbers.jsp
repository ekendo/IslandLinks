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
        long diffDays_I = 0;
        long diffDays_C = 0;
        long diffHours_I = 0;
        long diffHours_C = 0;

        String inct_date = "";
        String case_date = "";
        String head_count = 0;
        // do the math
        try
        {
            inct_date=request.getParameter("incidentDate");
            case_date=request.getParameter("caseDate");
            Date nowDate = Calendar.getInstance().getTime();
            DateFormat formatter = new SimpleDateFormat("MM/dd/yyyy");
            Date incidentDate = (Date)formatter.parse(inct_date);
            Date caseDate = (Date)formatter.parse(case_date);

            long diff_I = nowDate.getTime() - incidentDate.getTime();
            long diff_C = nowDate.getTime() - caseDate.getTime();
            // Calculate difference in days
            diffDays_I = diff_I / (24 * 60 * 60 * 1000);
            diffDays_C = diff_C / (24 * 60 * 60 * 1000);
            // Calculate difference in hours
            //diffHours = diff / (60 * 60 * 1000);
            head_count = request.getParameter("headCount");
            int headCount = Integer.parseInt(request.getParameter("headCount"));
            long numHours = (headCount*2080)/365;
            diffHours_I = diffDays_I*numHours;
            diffHours_C = diffDays_C*numHours;

        }
        catch(Exception ex)
        {

        }

        // save values to a file
        try
        {
             Properties props = new Properties();

             props.setProperty("HeadCount",head_count);
             props.setProperty("IncidentDate",inct_date);
             props.setProperty("CaseDate",case_date);

             OutputStream propOut = new FileOutputStream(new File("currentSafetyNumbers.properties"));

             props.store(propOut, "Safety Stats Properties");
        }
        catch(Exception exc)
        {

        }

        %>
        <table cellpadding="5" cellspacing="5" border="1">
            <tr>
                <th cellspacing="2" align="center" colspan="2">
                    Safety Statistics
                </th>
            </tr>
            <tr>
                <td>
                    Days Since Last OSHA Recordable
                </td>
                <td>
                    <font color="blue" size="14"><%=diffDays_I%></font>
                </td>
            </tr>
            <tr>
                <td>
                    Hours Since Last OSHA Recordable
                </td>
                <td>
                    <font color="blue" size="14"><%=diffHours_I%></font>
                </td>
            </tr>
            <tr>
                <td>
                    Days Since Last Workday Case
                </td>
                <td>
                    <font color="blue" size="14"><%=diffDays_C%></font>
                </td>
            </tr>
            <tr>
                <td>
                    Hours Since Last Workday Case
                </td>
                <td>
                    <font color="blue" size="14"><%=diffHours_C%></font>
                </td>
            </tr>
        </table>