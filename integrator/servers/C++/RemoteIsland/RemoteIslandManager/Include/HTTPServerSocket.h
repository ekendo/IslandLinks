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

#include <sys/ioctl.h>
#include "ProxyHttpGetSocket.h"
#include <HTTPSocket.h>
#include "Gemensamt.h"
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
	void OnFailure();
	void OnPortProxyAccept(std::string forwardAddress, std::string forwardParam, int forwardPort);
	void OnDelete();

	std::string MyUseragent()
	{
		return GetUserAgent();
	}
	
	std::string ReceiveBytes(Socket * currentSocket) 
	{
		std::string ret;
		char buf[1024];
	 
		while (1) 
		{
			u_long arg = 0;
			#ifdef _WIN32
			if (ioctlsocket(currentSocket->GetSocket(), FIONREAD, &arg) != 0)
				break;
			#else
			if (ioctl(currentSocket->GetSocket(), FIONREAD, &arg) != 0)
                                break;
			#endif

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

	//based on javascript encodeURIComponent()
	std::string urlencode(const std::string &c)
	{
		std::string escaped="";
		int max = c.length();
	
		for(int i=0; i<max; i++)
		{
			if ( (48 <= c[i] && c[i] <= 57) ||//0-9
				 (65 <= c[i] && c[i] <= 90) ||//abc...xyz
				 (97 <= c[i] && c[i] <= 122) || //ABC...XYZ
				 (c[i]=='~' || c[i]=='!' || c[i]=='*' || c[i]=='.' || c[i]=='(' || c[i]==')' || c[i]=='\'') //~!*()'
			)
			{
				escaped.append( &c[i], 1);
			}
			else
			{
				escaped.append("%");
				escaped.append( char2hex(c[i]) );//converts char 255 to string "ff"
			}
		}
		return escaped;
	}

	std::string char2hex( char dec )
	{
		char dig1 = (dec&0xF0)>>4;
		char dig2 = (dec&0x0F);
		if ( 0<= dig1 && dig1<= 9) dig1+=48;    //0,48inascii
		if (10<= dig1 && dig1<=15) dig1+=97-10; //a,97inascii
		if ( 0<= dig2 && dig2<= 9) dig2+=48;
		if (10<= dig2 && dig2<=15) dig2+=97-10;

		std::string r;
		r.append( &dig1, 1);
		r.append( &dig2, 1);
		return r;
	}

	bool m_debugValue;
	std::map<std::string,std::string> m_trackedUrlMap;
	std::string m_trackedUrls;
	std::string reqHeader;
	std::string m_hostDomain;
	std::string m_hostRedirect;
	std::string m_portForwardAddress;
	std::string m_actualSourceIP;
	std::string m_ignoreList;
	std::string m_doNotChangeList;
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
	PortForwardSocket *f;
	char tmps[250000];
	size_t tmpl;
	
};




#endif // _SERVERSOCKET_H
