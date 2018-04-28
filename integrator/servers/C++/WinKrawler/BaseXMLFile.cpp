/**
 **	File ......... BaseXMLFile.cpp
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
//#include <stdio.h>

#include "BaseXMLFile.h"




BaseXMLFile::BaseXMLFile(const std::string& filename,const std::string& verify_ns,const std::string& verify_root)
: m_doc(NULL)
,m_current(NULL)
{
	xmlNodePtr cur;
	xmlNsPtr ns;

	if (!(m_doc = xmlParseFile(filename.c_str() )))
	{
		fprintf(stderr,"xmlParseFile('%s') failed\n",filename.c_str());
		return;
	}

	if (!(cur = xmlDocGetRootElement(m_doc)))
	{
		fprintf(stderr, "Empty document\n");
		xmlFreeDoc(m_doc);
		m_doc = NULL;
		return;
	}

	if (!verify_ns.size())
	{
		return; // we're ok
	}
	if (!(ns = xmlSearchNsByHref(m_doc, cur, (const xmlChar *) verify_ns.c_str() )))
	{
		fprintf(stderr, "Document namespace != '%s'\n", verify_ns.c_str());
		xmlFreeDoc(m_doc);
		m_doc = NULL;
		return;
	}

	if (!verify_root.size())
	{
		return;
	}
	if (xmlStrcmp(cur -> name, (const xmlChar *) verify_root.c_str() ))
	{
		fprintf(stderr, "Document root != '%s'\n",verify_root.c_str());
		xmlFreeDoc(m_doc);
		m_doc = NULL;
		return;
	}

}


BaseXMLFile::~BaseXMLFile()
{
	if (m_doc)
	{
		xmlFreeDoc(m_doc);
	}
}


xmlNodePtr BaseXMLFile::GetRootElement()
{
	m_current = m_doc ? xmlDocGetRootElement(m_doc) : NULL;
	return m_current;
}


std::string BaseXMLFile::GetProperty(const std::string& name)
{
	xmlChar *p = m_current ? xmlGetProp(m_current, (const xmlChar *) name.c_str() ) : NULL;
	if (!p)
	{
		return "";
	}
	std::string str = (char *)p;
	xmlFree(p);
	return str;
}


xmlNodePtr BaseXMLFile::GetChildrenNode()
{
	m_current = m_current ? m_current -> xmlChildrenNode : NULL;
	return m_current;
}


xmlNodePtr BaseXMLFile::GetNextNode()
{
	do
	{
		m_current = m_current ? m_current -> next : NULL;
	} while (m_current && xmlIsBlankNode( m_current ));
	return m_current;
}


const std::string& BaseXMLFile::GetNodeName()
{
	if (m_current)
	{
		m_current_name = (char *)m_current -> name;
	}
	else
	{
		m_current_name = "";
	}
	return m_current_name;
}


xmlNsPtr BaseXMLFile::GetNodeNs()
{
	if (m_current)
		return m_current -> ns;
	return NULL;
}


const std::string& BaseXMLFile::GetNodeNsPrefix()
{
	if (m_current && m_current -> ns && m_current -> ns -> prefix)
	{
		m_ns_prefix = (char *)m_current -> ns -> prefix;
	}
	else
	{
		m_ns_prefix = "";
	}
	return m_ns_prefix;
}


const std::string& BaseXMLFile::GetNodeNsHref()
{
	if (m_current && m_current -> ns && m_current -> ns -> href)
	{
		m_ns_href = (char *)m_current -> ns -> href;
	}
	else
	{
		m_ns_href = "";
	}
	return m_ns_href;
}


xmlNodePtr BaseXMLFile::GetFirstElement(const std::string& name)
{
	GetRootElement();
	xmlNodePtr p = GetChildrenNode();
	while (p)
	{
		if (name == GetNodeName())
		{
			return p;
		}
		p = GetNextNode();
	}
	return NULL;
}


xmlNodePtr BaseXMLFile::GetFirstElement(xmlNodePtr base,const std::string& name)
{
	SetCurrent(base);
	xmlNodePtr p = GetChildrenNode();
	while (p)
	{
		if (name == GetNodeName())
		{
			return p;
		}
		p = GetNextNode();
	}
	return NULL;
}


xmlNodePtr BaseXMLFile::GetNextElement(xmlNodePtr p,const std::string& name)
{
	SetCurrent(p);
	p = GetNextNode();
	while (p)
	{
		if (name == GetNodeName())
		{
			return p;
		}
		p = GetNextNode();
	}
	return NULL;
}


