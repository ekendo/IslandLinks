/**
 **	File ......... Gemensamt.h
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
#ifndef _GEMENSAMT_H
#define _GEMENSAMT_H

#ifdef _DEBUG
#define DEB(x) x
#else
#define DEB(x) 
#endif

#include <string>
#include "Parse.h"


class Gemensamt
{
public:
	Gemensamt() 
	{
		m_user_agent = "EKenDo/5.0";
		m_contentLength = 0;
	}

	void SetPostData(const char* x){ m_postData = x;}
	void SetServer(const std::string& x) { m_server = x; }
	void SetHttpReferer(const std::string& x) { m_http_referer = x; }
	void SetUserAgent(const std::string& x) { m_user_agent = x; }
	void SetContentType(const std::string x) { m_contentType = x; }
	void SetContentLength(const int x) 
	{ 
		DEB(printf("this is the ContentLen%d\n", x);)
		m_contentLength = x; 
	}
	void SetHexContentLen(const std::string x)
	{
		char * p;
		int contentLen;
		contentLen = strtol(x.c_str(), &p,16);
		
		if(this->m_debugMode)
		{
			//printf("this is the Hex%d\n", x.c_str());
			printf("this is the HexContentLen%d\n", contentLen);
		}

		this->m_contentLength = contentLen;
	}
	void SetKeepAlive(const int x){m_keepAlive = x;}
	void SetConnection(const std::string x) { m_connection = x;}
	void SetCookie(const std::string x) 
	{
		m_cookieValue = x;
	}

	void SetDebugMode(bool value)
	{
		this->m_debugMode = value;	
	}


	std::string GetServer() 
	{
		Parse pa(m_server,":");
		std::string str = pa.getword();
		return str;
	}
	std::string GetUserAgent() { return m_user_agent; }
	std::string GetHttpReferer() { return m_http_referer; }
	std::string GetHttpContentType() { return m_contentType; }
	int GetHttpContentLength() 
	{ 
		return m_contentLength; 
	}

	int GetKeepAlive() { return m_keepAlive; }
	std::string GetPostData() {return m_postData; }
	std::string GetConnection() {return m_connection; }
	std::string GetCookie() { return m_cookieValue; }
	std::string m_postData;
	bool m_debugMode;
private:
	std::string m_server;
	std::string m_user_agent;
	std::string m_http_referer;
	std::string m_contentType;
	std::string m_connection;
	std::string m_cookieValue;
	int m_contentLength;
	int m_keepAlive;
	bool m_contentLenSet;
	
};




#endif // _GEMENSAMT_H
