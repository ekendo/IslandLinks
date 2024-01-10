/** \file HttpGetSocket.h
 **	\date  2004-02-13
 **	\author grymse@alhem.net
**/
/*
Copyright (C) 2004-2006  Anders Hedstrom

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
#ifndef _HTTPGETSOCKET_H
#define _HTTPGETSOCKET_H

#include "HttpClientSocket.h"

#ifdef SOCKETS_NAMESPACE
namespace SOCKETS_NAMESPACE {
#endif


/** Get http page. 
	\ingroup http */
class HttpGetSocket : public HttpClientSocket
{
public:
	HttpGetSocket(ISocketHandler&);
	HttpGetSocket(ISocketHandler&,const std::string& url,const std::string& to_file = "");
	HttpGetSocket(ISocketHandler&,const std::string& host,port_t port,const std::string& url,const std::string& to_file = "");
	~HttpGetSocket();

	void OnConnect();

protected:
	HttpGetSocket& operator=(const HttpGetSocket& ) { return *this; }
	HttpGetSocket(const HttpGetSocket& s) : HttpClientSocket(s) {}
};




#ifdef SOCKETS_NAMESPACE
}
#endif

#endif // _HTTPGETSOCKET_H