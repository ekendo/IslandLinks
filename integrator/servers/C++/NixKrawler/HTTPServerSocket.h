/**
 **	File ......... ServerSocket.h
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
#ifndef _SERVERSOCKET_H
#define _SERVERSOCKET_H

#ifdef _DEBUG
#define DEB(x) x
#else
#define DEB(x) 
#endif

#include "ProxyHttpGetSocket.h"
#include <HTTPSocket.h>
#include "Gemensamt.h"
#include "Deliver.h"
#include <string>

class PortForwardSocket;

class HTTPServerSocket : public HTTPSocket, public Gemensamt
{
public:
	HTTPServerSocket(ISocketHandler&);
	~HTTPServerSocket();

	void Init();
	void OnAccept();
	void OnFirst();
	void OnRawData(const char *buf,size_t len);
	void OnHeader(const std::string& ,const std::string& );
	void OnHeaderComplete();
	void OnData(const char *,size_t);
	void OnDetached();
	void OnHit(ProxyHttpGetSocket& proxy, std::string& userSession,const std::string actualContent, std::string proxyContent);
	void OnFailure();
	void OnPortProxyAccept(std::string forwardAddress, std::string forwardParam, int forwardPort);
	void OnDelete();

	std::string MyUseragent()
	{
		return GetUserAgent();
	}
	
	bool SaveToDBArchive(Hit hit);
	bool LoadInGlobalMemory(std::string messageName, std::string messageValue);
	std::string ReceiveBytes(Socket * currentSocket) 
	{
		std::string ret;
		char buf[1024];
	 
		while (1) 
		{
			u_long arg = 0;
			if (ioctlsocket(currentSocket->GetSocket(), FIONREAD, &arg) != 0)
				break;

			if (arg == 0)
				break;

			if (arg > 1024) arg = 1024;

			int rv = recv (currentSocket->GetSocket(), buf, arg, 0);
			if (rv <= 0) break;

			std::string t;

			t.assign (buf, rv);
			ret += t;
		}

		return ret;
	}

	bool m_debugValue;
	std::string m_trackedUrls;
	std::string reqHeader;
	std::string m_hostDomain;
	std::string m_hostRedirect;
	std::string m_portForwardAddress;
	std::string m_actualSourceIP;
	HANDLE winMutex;
	HANDLE archiveEvent;
	HANDLE trackerEvent;
	Mutex hMutex;
	DWORD dwWaitResult; 
	int m_readBuf;
	int m_writeBuf;
	int m_portForwardValue;
	std::string m_addressForwardValue; 
	std::string m_forwardParamValue;
	std::list<Hit> hits;
	PortForwardSocket *f;
	char tmps[250000];
	size_t tmpl;
	
};




#endif // _SERVERSOCKET_H
