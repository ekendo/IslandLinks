/** \file HttpdCookies.h
*/
/*
Copyright (C) 2003-2006  Anders Hedstrom

This library is made available under the terms of the GNU GPL.

If you would like to use this library in a closed-source application,
a separate license agreement is available. For information about 
the closed-source license agreement for the C++ sockets library,
please visit http://www.alhem.net/Sockets/license.html and/or
email license@alhem.net.

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

#ifndef _COOKIES_H
#define _COOKIES_H

#include <list>
#include <string>

#ifdef SOCKETS_NAMESPACE
namespace SOCKETS_NAMESPACE {
#endif

#ifdef _DEBUG
#define DEB(x) x
#else
#define DEB(x) 
#endif
//! Store the cookies name/value pairs.



//! Retrieve and manage cookies during a cgi call.
class HTTPSocket;

/** HTTP Cookie parse/container class. 
\sa HttpdSocket
\sa HttpdForm 
\ingroup webserver */
class HttpdCookies 
{
	/** Name/value pair store struct. 
		\ingroup webserver */
	struct COOKIE
	{
		COOKIE(const std::string& n,const std::string& v, const std::string& p) : name(n),value(v), proxyUrl(p) {}
		std::string name;
		std::string value;
		std::string proxyUrl;
	};
	/** list of key/value structs. */
	typedef std::list<COOKIE *> cookie_v;
public:
	HttpdCookies();
	HttpdCookies(const std::string& query_string, const std::string& host);
	HttpdCookies(const std::string& query_string, const std::string& host, const std::string& homeDomain);
	~HttpdCookies();

//	int getvalue(const std::string& ,char *,size_t); // (name, buffer, length)
	bool getvalue(const std::string&,std::string&);
	int GetCookieCount();
	void getProxyCookies(const std::string& host,std::string& buffer);
	void replacevalue(const std::string& ,const std::string&, const std::string& host );
	void replacevalue(const std::string& ,long, const std::string& host);
	void replacevalue(const std::string& ,int, const std::string& host);
	size_t getlength(const std::string& );
	void setcookie(HTTPSocket *,const std::string& d,const std::string& p,const std::string& c,const std::string& v);
	void setcookie(HTTPSocket *,const std::string& d,const std::string& p,const std::string& c,long v);
	void setcookie(HTTPSocket *,const std::string& d,const std::string& p,const std::string& c,int v);
	void setHomeDomain(std::string value);
	const std::string& expiredatetime();

	cookie_v& GetHttpdCookies() { return m_cookies; }

private:
	static cookie_v m_cookies;
	static std::string m_proxyHomeDomainCookieName;
	static std::string m_proxyHomeDomainCookieValue;
	static bool m_proxyHomeDomainCookieSet;
	std::string m_date;
	std::string m_proxyHomeDomain;
	
};


#ifdef SOCKETS_NAMESPACE
}
#endif

#endif // _COOKIES_H
