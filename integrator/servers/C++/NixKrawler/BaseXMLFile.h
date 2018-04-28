/**
 **	File ......... BaseXMLFile.h
 **	Published ....  2004-07-13
 **	Author ....... grymse@alhem.net
**/
/*
Copyright (C) 2004  Anders Hedstrom

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/
#ifndef _BASEXMLFILE_H
#define _BASEXMLFILE_H

#include <string>
#include <libxml/xmlmemory.h>
#include <libxml/parser.h>


class BaseXMLFile
{
public:
	BaseXMLFile(const std::string& filename,const std::string& verify_ns = "",const std::string& verify_root = "");
	~BaseXMLFile();

	xmlDocPtr GetDocument() { return m_doc; }
	xmlNodePtr GetRootElement();
	std::string GetProperty(const std::string& );
	xmlNodePtr GetChildrenNode();
	xmlNodePtr GetNextNode();
	const std::string& GetNodeName();
	void SetCurrent(xmlNodePtr p) { m_current = p; }
	xmlNsPtr GetNodeNs();
	const std::string& GetNodeNsPrefix();
	const std::string& GetNodeNsHref();

	xmlNodePtr GetFirstElement(const std::string& );
	xmlNodePtr GetFirstElement(xmlNodePtr,const std::string& );
	xmlNodePtr GetNextElement(xmlNodePtr,const std::string& );

private:
	xmlDocPtr m_doc;
	xmlNodePtr m_current;
	std::string m_current_name;
	std::string m_ns_prefix;
	std::string m_ns_href;
};




#endif // _BASEXMLFILE_H
