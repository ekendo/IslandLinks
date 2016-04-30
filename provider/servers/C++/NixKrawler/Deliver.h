/**
 **	File ......... Deliver.h
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
#ifndef _DELIVER_H
#define _DELIVER_H

#include <map>
#include <iostream>
#include <iomanip>
#include <stdlib.h>
#include <HTTPSocket.h>
#include "ServerHandler.h"
#include "ProxyHttpGetSocket.h"
#include "HttpdCookies.h"
#include <time.h>

class Deliver
{
public:
	Deliver(ProxyHttpGetSocket&, HTTPSocket&,const std::string& host,const std::string& protocol,const std::string& method,const std::string& url, const std::string& hostRedirect);
	~Deliver();
	void Run();
	void Url_This(const std::string& url_in, std::string& host,int& port,std::string& url,std::string& file,std::string& ext);
	
	/* Settors */
	void SetPostData(std::string data)
	{
		m_postData = data;
	}
	void SetUserAgent(std::string value)
	{
		m_userAgent = value;
	}
	void SetContentType(std::string value)
	{
		m_contentType = value;
	}
	void SetConnection(std::string value)
	{
		m_connection = value;
	}
	void SetCookie(std::string value)
	{	
		if(this->debugMode)
		{
			printf("Deliver.h:current cookie Value=%s\n",this->m_cookieValue.c_str());
		}

		//if(!this->cookieSet)
		{
			if(this->debugMode)
			{
				printf("before set new cookie\n");
			}

			this->m_cookieValue = value;

			HttpdCookies* m_proxyCookies = new HttpdCookies(value,this->m_baseProxyUrl,"CampaignSession");
		
		}

		std::string campaignCookie, newCookie;
		if(this->debugMode)
		{
			printf("checking cookie Value\n");	
		}

		if(!m_proxyCookies->getvalue("CampaignSession",campaignCookie))
		{
			this->setHostCookie = true;
			
			if(this->debugMode)
			{
				printf("Need to set the campaign Cookie\n");
			}
		}
		else
		{
			this->setHostCookie = false;
			SetCampaignCookieValue(campaignCookie);
			if(this->debugMode)
			{
				printf("campaign Cookie already set to:%s\n",campaignCookie.c_str());
			}
		}
		
		//delete m_proxyCookies;
		DEB(printf("Done setting new cookie\n");)
	}

	void SetContentLength(int value)
	{
		m_contentLength = value;
	}

	void SetKeepAlive(int value)
	{
		m_keepAlive = value;
	}

	void SetReferer(std::string value)
	{
		this->m_referer = value;
	}

	void SetHost(std::string value)
	{
		this->m_host = value;
	}
	
	void SetDebug(bool value)
	{
		this->debugMode = value;
	}

	void SetIsThread(bool value)
	{
		this->isThread = value;
	}

	void SetCampaignCookieValue(std::string value)
	{
		this->m_campaignCookieValue = value;
	}

	void SetTrackedUrls(std::string value)
	{
		this->m_trackedUrls = value;
	}

	void SetHostRedirect(std::string value)
	{
		this->m_hostRedirect = ".";
		this->m_hostRedirect+= value;
	}

	void SetReadBuffer(int value)
	{
		this->m_readBuffer = value;
	}

	void SetWriteBuffer(int value)
	{
		this->m_writeBuffer = value;
	}

	/* Gettors */

	bool Get(const std::string& url_in,ProxyHttpGetSocket& ref);
	void GetRedirect(ProxyHttpGetSocket& ref);
	
	std::string GetContentType()
	{
		return m_contentType;
	}
	int GetContentLength()
	{
		return m_contentLength;
	}

	std::string GetHost()
	{
		return this->m_host;
	}

	std::string GetProtocol()
	{
		return this->m_protocol;
	}

	std::string GetMethod()
	{
		return this->m_method;
	}

	std::string GetUrl()
	{
		return this->m_proxyUrl;
	}

	std::string GetContents()
	{
		return this->m_actualUrlContents;
	}

	bool InDebugMode()
	{
		return this->debugMode;
	}

	/* Helpers */
	void Replace(std::string replaceHost);

	/* Variables */
	int m_serverType;
	
private:
	HTTPSocket& m_socket;
	ProxyHttpGetSocket& m_response;
	bool contentEncoded;
	bool setHostCookie;
	bool debugMode;
	bool isThread;
	bool cookieSet;
	int m_contentLength;
	int m_keepAlive;
	int m_readBuffer;
	int m_writeBuffer;
	std::string m_referer;
	std::string m_userAgent;
	std::string m_contentType;
	std::string m_connection;
	std::string m_host;
	std::string m_protocol; // http / https
	std::string m_method;
	std::string m_url;
	std::string m_actualUrlContents;
	std::string m_proxyUrlContents;
	std::string m_proxyUrl;
	std::string m_proxySubdomain;
	std::string m_postData;
	std::string m_cookieValue;
	std::string m_baseProxyUrl;
	std::string m_campaignCookieValue;
	std::string m_trackedUrls;
	std::string m_hostRedirect;
	HttpdCookies *m_proxyCookies;
	
};




#endif // _DELIVER_H
