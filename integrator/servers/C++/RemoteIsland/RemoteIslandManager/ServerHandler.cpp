/**
 **	File ......... ServerHandler.cpp
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
#include <SocketHandler.h>
#include <YattaListenSocket.h>
#include <ListenSocket.h>
#include <iostream>
#include <exception>
#include "ServerHandler.h"
//#include <tchar.h>
//#include <process.h>
#include <Lock.h>
#include "string.h"
#include <string>

using namespace std;

std::list<SocketThread*> ServerHandler::runningThreads;


ServerHandler::ServerHandler(const std::string& filename)
:SocketHandler()
,m_config(filename)
,m_mime(GetString("mime/filename"))
{
	//_beginthread( RunDataArchive, 0,  this );
	//::MessageBoxA(0,"i ","am here",0);
}


ServerHandler::ServerHandler(Mutex& mutex, const std::string& filename)
:SocketHandler(mutex)
,m_config(filename)
,m_mime(GetString("mime/filename"))
{
	//_beginthread( RunDataArchive, 0,  this );
}


ServerHandler::~ServerHandler()
{
	
}


int ServerHandler::GetInt(const std::string& path)
{
	xmlNodePtr p = m_config.Find(m_config.GetRootElement(),path);
	if (p)
	{
		m_config.SetCurrent(p);
		std::string str = m_config.GetProperty("value");
		return atoi(str.c_str());
	}
	else
	{
		fprintf(stderr,"config path not found: %s\n",path.c_str());
	}
	return 0;
}


std::string ServerHandler::GetString(const std::string& path)
{
	xmlNodePtr p = m_config.Find(m_config.GetRootElement(),path);
	if (p)
	{
		m_config.SetCurrent(p);
		std::string str = m_config.GetProperty("value");
		return str;
	}
	fprintf(stderr,"config path not found: %s\n",path.c_str());
	return "";
}


bool ServerHandler::GetBoolean(const std::string& path)
{
	xmlNodePtr p = m_config.Find(m_config.GetRootElement(),path);
	std::string str;
	if (p)
	{
		m_config.SetCurrent(p);
		str = m_config.GetProperty("value");
		if (str.size() && (str[0] == '1' ||
			str[0] == 'y' || str[0] == 'Y' ||
			str[0] == 't' || str[0] == 'T'))
		{
			return true;
		}
		return false;
	}
	fprintf(stderr,"config path not found: %s\n",path.c_str());
	return false;
}


std::string ServerHandler::GetMimetype(const std::string& ext)
{
	return m_mime.GetMimeFromExtension(ext);
}

std::map<std::string,std::string> ServerHandler::GetPairList(const std::string& path1, const std::string& path2 )
{
	std::map<std::string,std::string> valuePairList;

	// Get main node.
	xmlNodePtr p = m_config.Find(m_config.GetRootElement(),path1);
	xmlNodePtr q;

	while (p)
	{
		m_config.SetCurrent(p);
		std::string str1 = m_config.GetProperty("value");
		
		q = m_config.Find(p,path2);
		m_config.SetCurrent(q);
		std::string str2 =  m_config.GetProperty("value");
		
		if((!str1.empty())&&(!str2.empty()))
		{
			valuePairList.insert(make_pair(str1,str2));
		}
		//p->next = m_config.Find(p,path1);
		p = p->next;
		//return str;
	}
	//fprintf(stderr,"config path not found: %s\n",path1.c_str());
	return valuePairList;
}
