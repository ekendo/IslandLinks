/**
 **	File ......... Deliver.cpp
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
#include "Parse.h"
#include "ServerHandler.h"
#include "ProxyHttpGetSocket.h"
#include "HTTPServerSocket.h"
#include "HTTPSocket.h"
#include "Deliver.h"
#include <process.h>
#include <exception>
#include <string>
#include <Mutex.h>
#include <Lock.h>
#include <stdexcept>
#include <time.h>

using namespace std;

Deliver::Deliver(ProxyHttpGetSocket& r, HTTPSocket& s,const std::string& h,const std::string& p,const std::string& m,const std::string& u, const std::string& hr)
:m_socket(s)
,m_response(r)
,m_host(h)
,m_protocol(p)
,m_method(m)
,m_url(u)
,m_keepAlive(0)
{
	m_serverType = 0;
	setHostCookie = false;
	isThread = false;
	setHostCookie = false;
	m_baseProxyUrl = "";
	m_campaignCookieValue = "";
	cookieSet = false;
	this->m_hostRedirect = hr;

	if (m_url.find("isUp",0) != std::string::npos)
	{
		m_serverType = 2;
	}

	if (m_url.find("campaignUrl",0) != std::string::npos)
	{
		m_serverType = 1;
		setHostCookie = true;
	}	

	if(this->m_serverType!=2)
	{
		Parse pa(m_host,".");
		m_proxySubdomain = pa.getword();
			
		if(m_url.find("campaignUrl=",0) != std::string::npos)
		{
			std::string::size_type proxyUrlStart = m_url.find("campaignUrl=",0)+12;
			m_proxyUrl = m_protocol + "://";
			m_proxyUrl += m_url.substr(proxyUrlStart);
			setHostCookie = true;
		}
		else
		{
			if((m_proxySubdomain.compare("www")!=0)&&(m_proxySubdomain.compare("127")!=0)&&(m_proxySubdomain.compare("mssql")!=0))
			{
				//this->m_baseProxyUrl = "www.";
				bool hasSubdomain = false;
				this->m_baseProxyUrl = m_proxySubdomain;
				std::string siteSubdomain = pa.getword();
				
				//printf("hostDomain=%s\n",this->m_hostRedirect.c_str());

				while((strcmp(siteSubdomain.c_str(),this->m_hostRedirect.c_str())!=0)&&(strcmp(siteSubdomain.c_str(),"com")!=0))
				{
					hasSubdomain = true;
					this->m_baseProxyUrl += ".";
					this->m_baseProxyUrl += siteSubdomain;
					siteSubdomain = pa.getword();
				}
				
				this->m_baseProxyUrl += ".com";

				if(!hasSubdomain)
				{
					
				}

				//printf("baseUrl=%s\n",m_baseProxyUrl.c_str());
				m_serverType = 1;
			}
		}
	}
}


Deliver::~Deliver()
{
}


void Deliver::Run()
{
	// use Socket::MasterHandler because we're now in a detached socket
	// Handler() will return the local sockethandler driving the socket
	// MasterHandler() returns the original sockethandler
	//ServerHandler& h = static_cast<ServerHandler&>(m_socket.MasterHandler());
	std::string url = m_protocol + "://" + m_host + m_url + "\n";
	std::string host;
	int port;
	std::string url_ut;
	std::string file;
	std::string ext;
	Url_This(url, host, port, url_ut, file, ext);
	std::string::size_type proxyUrlStart;
	std::string document;
	const char * data;
	ProxyHttpGetSocket response(this->m_response.MasterHandler());
	bool complete = false;
	std::string replaceHost = this->m_host+= "/";	

	if(this->debugMode)
	{
		printf("url:%s\n",m_url.c_str());
		printf("server Type:%d\n",m_serverType);
		printf("parse subdomain:%s\n",m_proxySubdomain.c_str());
	}

	if(m_url.find(replaceHost)!= std::string::npos)
	{
		//m_url.replace(m_url.find(replaceHost),replaceHost.length()+1,"/");
	}

	std::string mappedUrl = ",";
	mappedUrl += this->m_baseProxyUrl;

	if(this->debugMode)
	{
		printf("mappedUrl:%s\n",mappedUrl.c_str());
		printf("trackerUrls:%s\n",this->m_trackedUrls.c_str());
	}
	std::string::size_type trackLoc = this->m_trackedUrls.find(mappedUrl);
					
	if(trackLoc==std::string::npos)
	{
		this->m_baseProxyUrl = "www." + this->m_baseProxyUrl;
	}

	if(m_url.find("campaignUrl=",0) == std::string::npos)
	{
		m_proxyUrl = m_protocol + "://" + this->m_baseProxyUrl + m_url;
	}

	if(this->debugMode)
	{
		printf("url:%s\n",url.c_str());
		printf("m_url:%s\n",m_url.c_str());
		printf("proxyUrl:%s:\n",m_proxyUrl.c_str());
		printf("baseproxuUrl:%s\n",this->m_baseProxyUrl.c_str());
	}

	std::string h;
	port_t p;

	switch (m_serverType)
	{
		case 1:
			// Do proxy
			if(this->debugMode)
			{
				printf("did proxy\n");
			}

			if (!Get(m_proxyUrl, response))
			{
				if(this->debugMode)
				{
					printf("Deliver-Failed\n");
				}

				try
				{
					static_cast<HTTPServerSocket&> (m_socket).OnFailure();
				}
				catch(...)
				{
					printf("exception OnFailure from Deliver\n");
				}
				break;
			}
			else
			{
				if(response.GetResponseString().length()||response.RedirectLocationSet())
				{
					if(this->debugMode)
					{
						printf("Deliver-Succeeded\n");
					}
				}
				else
				{
					if(this->debugMode)
					{
						printf("Deliver-Failed\n");
					}

					try
					{
						static_cast<HTTPServerSocket&> (m_socket).OnFailure();
					}
					catch(...)
					{
						printf("exception OnFailure from Deliver\n");
					}
					break;
				}
			}

			this->m_actualUrlContents = response.GetResponseString();
				
			if(this->m_actualUrlContents.length()>0)
			{
				Replace("");

				if(this->m_trackedUrls.length()>0)
				{
					std::string urls = this->m_trackedUrls;
					// Get the first url
					std::string::size_type urlLocation = urls.find(",",0);

					while(urlLocation != std::string::npos)
					{
						//printf("url:%s\n",urls.substr(0,urlLocation).c_str());
						urls = urls.substr(urlLocation+1);
						//urlLocation = urls.find(",");
						//printf("substr:%s\n",urls.c_str());
						break;
					}
				}

			}

			//reset Method for reporting.
			if(response.HadPost())
			{
				response.SetMethod("POST");
			}
			else
			{
				response.SetMethod("GET");
			}

			response.Url(m_proxyUrl,h,p);	
							
			
			
			if(this->debugMode)
			{
				printf("Get Host:%s\n",response.GetUrlHost().c_str());
				printf("Get Method:%s\n",response.GetMethod().c_str());
				printf("Get server:%s\n",response.GetServer().c_str());
				printf("status:%s\n",response.GetResponseStatus().c_str());
				printf("status Text:%s\n",response.GetResponseStatusText().c_str());
				printf("response text:%s\n",response.GetResponseString().c_str());
				printf("Get Useragent:%s\n",response.GetUserAgent().c_str());
				printf("Get Content Type:%s\n",response.GetHttpContentType().c_str());
				printf("Get Content Length:%d\n",response.GetHttpContentLength());
				printf("Get Connection:%s\n",response.GetConnection().c_str());
				printf("Get KeepAlive:%d\n", response.GetKeepAlive());
				printf("Get Referer:%s\n", response.GetHttpReferer().c_str());
			
			}
	
			break;
		case 2:
			DEB(
				printf("ServerUp\n");
			)
			this->m_actualUrlContents = "ServerUp";
			break;
		case 0:
			
			std::string fileNotFound = "HTTP/1.0 404 Not Found\n"; 
			fileNotFound += "Content-type: text/html\n";
			fileNotFound += "\n<html><body><h1>404 Not Found - Resource not available</h1></html>\n";
			
			this->m_actualUrlContents = fileNotFound;
			break;
	}

	 
	try                                                 // 5 
    {                                                   // 6 
        DWORD dwWaitResult;
		//printf("after lock\n");
		
		if(this->setHostCookie)
		{
			// Set cookie flag for client.
			response.SetHostCookieSet(true);

			// copy current cookie.
			//std::string cookie = response.GetCookieSet();

			// Generate random number.
			srand( (unsigned)time( NULL ) );
			int i = rand();
			char buffer [33];
			itoa (i,buffer,16);
			
			std::string clientCookie =" CampaignSession=";
			clientCookie+=buffer;
			clientCookie+="; domain=";
			clientCookie+= this->m_hostRedirect;
			clientCookie+=".com";
			clientCookie+="; path=/; ";
			
			//cookie+=clientCookie.c_str();

			//std::string newCookie = clientCookie; 
			//newCookie += cookie;

			// Set response cookie.
			response.SetHostCookieSessionValue(clientCookie);

			// Set this request cookieValue.
			this->m_campaignCookieValue = buffer;
			
			// Reset flag.
			this->setHostCookie = false;

			DEB(printf("just set client Session cookie:\n");)
		}

		if(response.CookieSet())
		{
			if(this->debugMode)
			{
				printf("Set-Cookie:%s\n",response.GetCookieSet().c_str());
			}
		}

		try
		{
			static_cast<HTTPServerSocket&> (m_socket).OnHit(response, this->m_campaignCookieValue, this->m_actualUrlContents, this->m_proxyUrlContents);
		}
		catch(...)
		{
			printf("exception OnHit from Deliver\n");
		}
	}
	catch(...)
	{
		if(this->debugMode)
		{
			printf("problem Sending!\n");
		}
	}

	DEB(printf("done with deliver::run\n");)
}


void Deliver::Url_This(const std::string& url_in,std::string& host,int& port,std::string& url,std::string& file,std::string& ext)
{
	Parse pa(url_in,"/");
	pa.getword(); // http
	host = pa.getword();
	if (strstr(host.c_str(),":"))
	{
		Parse pa(host,":");
		pa.getword(host);
		port = pa.getvalue();
	}
	else
	{
		port = 80;
	}
	url = "/" + pa.getrest();
	{
		Parse pa(url,"/");
		std::string tmp = pa.getword();
		while (tmp.size())
		{
			file = tmp;
			tmp = pa.getword();
		}
	}
	{
		Parse pa(file,".");
		std::string tmp = pa.getword();
		while (tmp.size())
		{
			ext = tmp;
			tmp = pa.getword();
		}
	}
} // url_this

bool Deliver::Get(const std::string& url_in,ProxyHttpGetSocket& retSocket)
{
	DEB(printf("in Deliver-Get\n");)
	Parse pa(url_in,":/");
	std::string protocol = pa.getword();
	this->m_protocol = protocol;
	bool https = !strcasecmp(protocol.c_str(), "https");
	std::string host = pa.getword();
	port_t port = https ? 443 : 80;

	if(m_baseProxyUrl.empty())
	{
		m_baseProxyUrl = host;
	}

	if (strstr(host.c_str(), ":")) // detect port from url
	{
		Parse pa(host,":");
		pa.getword();
		port = pa.getvalue();
	}
	std::string url = "/" + pa.getrest();
	std::string file; // get filename at end of url
	{
		Parse pa(url,"/");
		std::string tmp = pa.getword();
		while (tmp.size())
		{
			file = tmp;
			tmp = pa.getword();
		}
	}
	
	bool complete = false;
	std::string document;
	if (!strcasecmp(protocol.c_str(),"http") || https)
	{
		if(this->debugMode)
		{
			printf("protocol:%s\n",protocol.c_str());
			printf("host:%s\n",host.c_str());
			printf("port:%d\n",port);
			printf("url:%s\n",url.c_str());
		}

		SocketHandler h;
		DEB(printf("in Deliver-Get:before settingReadBuffer\n");)
		h.SetReadBuffer(this->m_readBuffer);
		DEB(printf("in Deliver-Get:before settingWriteBuffer\n");)
		h.SetWriteBuffer(this->m_writeBuffer);
		DEB(printf("in Deliver-Get:after WriteBuffer\n");)
	

		ProxyHttpGetSocket s(h, url_in);
		//s.SetConnectTimeout(10);
		//s.SetConnectionRetry(5);
		s.SetReuse(true);
		s.SetReconnect();
		s.SetDebugMode(this->debugMode);
		s.SetMethod(this->m_method);
		s.SetHttpVersion("HTTP/1.1");
		s.SetUserAgent(this->m_userAgent);
		//printf("deliver before get cookie%s\n",this->m_cookieValue.c_str());
		s.SetCookie(this->m_cookieValue);

		if(this->debugMode)
		{
			printf("Get before connectoin:%s\n",this->m_connection.c_str());
		}

		s.SetKeepAlive(this->m_keepAlive);
		s.SetConnection(this->m_connection);
		
		// Set referrer to proxy url.
		std::string proxyUrl, proxyFile, proxyHost, proxyExt, proxyReferrer;
		int proxyPort=0;
		this->Url_This(this->m_referer,proxyHost, proxyPort,proxyUrl,proxyFile,proxyExt);
		proxyReferrer = this->m_protocol;
		proxyReferrer += "://";
		proxyReferrer += host;
		proxyReferrer += proxyUrl.c_str();
		s.SetHttpReferer(proxyReferrer);
		
		if(this->debugMode)
		{
			printf("Get before Referer:%s\n", this->m_referer.c_str());
			printf("Get after Referer:%s\n", proxyReferrer.c_str());
		}

		DEB(printf("in Deliver-Get:method-%s*\n",this->m_method.c_str());)
		clock_t clock_start=clock();
		
		if(strcmp(this->m_method.c_str(),"POST")==0)
		{
			DEB(printf("Deliver-isPOST\n");)
			s.SetContentLength(this->GetContentLength());
			s.SetContentType(this->GetContentType());
			DEB(printf("Deliver-before AddPostData\n");)
			s.AddPostData(this->m_postData);
			DEB(printf("Deliver-after AddPostData\n");)
			s.DoHttpOpperation();
		}
		
		else
		{
			DEB(printf("Deliver-isNotPOST\n");)
			s.DoHttpOpperation();
		}
		
		

		{	
			int i=0;
			DEB(printf("Deliver-ProxyAdd\n");)
			h.Add(&s);
			DEB(printf("Deliver-Before Select\n");)
			h.Select(0, 250);
			DEB(printf("Deliver-After Select\n");)
			while ((! s.Complete())&&(!s.RedirectLocationSet()))
			{
				h.Select(0, 250);
				i++;
			}
			
			double time_c=(double)(clock()- clock_start)/CLOCKS_PER_SEC;
			s.SetResponseTime(time_c);
			complete = s.Complete();
			
			//if (complete)
			{
				// update Cookie
				//this->SetCookie(s.GetCookie());
				
				if(s.RedirectLocationSet())
				{
					if(this->debugMode)
					{
						printf("RemoteAddress:%s\n",s.GetRemoteAddress().c_str());				
						printf("Handling Redirect:%s\n",s.GetRedirectLocation().c_str());	
						printf("Deliver_trackedUrls:%s\n",this->m_trackedUrls.c_str());

					}

					std::string host, url, file, ext, redirectLocation,hostDomain;
					int port;
					this->Url_This(s.GetRedirectLocation(),host, port,url,file,ext);
					
					/*
					Parse pa(host,".");
					if(pa.getword().compare("www")==0)
					{
						//printf("rest of www=%s\n",pa.getrest().c_str());
						host = pa.getrest().c_str();
					}
					*/
					
					hostDomain = ".";
					hostDomain += this->m_hostRedirect;

					if(this->m_trackedUrls.find(host)!=std::string::npos)
					{
						//printf("Redirect host:%s\n",host.c_str());
						std::string::size_type dotCom = host.find(".com");
						
						if(dotCom != std::string::npos)
						{
							host = host.insert(dotCom,hostDomain);
							redirectLocation = "";

							//if()
							redirectLocation += "http://";
							redirectLocation += host.c_str();
							redirectLocation += url.c_str();
							//printf("New redirect:%s\n",redirectLocation.c_str());
							s.SetRedirectLocation(redirectLocation);
						}


					}
					//this->GetRedirect(s);
					complete = true;			
				}
				else
				{
					/*
					if(s.GetHttpContentLength()==0)
					{
						complete = false;
					}
					*/

					if(s.GetStatus().length()==0)
					{
						complete = false;
					}
				}
		
				if(this->debugMode)
				{
					printf("status:%s\n",s.GetStatus().c_str());
					printf("status Text:%s\n",s.GetStatusText().c_str());
					printf("Get Host:%s\n",s.GetUrlHost().c_str());
					printf("Get Useragent:%s\n",s.GetUserAgent().c_str());
					printf("Get Method:%s\n",s.GetMethod().c_str());
					printf("Get Content Type:%s\n",s.GetHttpContentType().c_str());
					printf("Get Content Length:%d\n",s.GetHttpContentLength());
					printf("Get Connection:%s\n",s.GetConnection().c_str());
					printf("Get KeepAlive:%d\n", s.GetKeepAlive());
					printf("Get Referer:%s\n", s.GetHttpReferer().c_str());
				}
				
				retSocket = s;
				
				if(this->debugMode)
				{
					printf("set retSocket!\n");
				}
			}

			if(this->debugMode)
			{
				printf("Get after Cookie:%s\n",s.GetCookie().c_str());
			}
		}
	}
	else
	{
		if(this->debugMode)
		{
			printf("Unknown protocol: '%s'\n",protocol.c_str());
		}
	}

	if (complete)
	{
		if(this->debugMode)
		{
			printf("Complete%sDone\n", document.c_str());
		}
	}
	return complete;
}


void Deliver::Replace(std::string replaceHost)
{
	std::string newHardLink = this->m_protocol.c_str();
	newHardLink += "://";
	
	if(replaceHost.length()>0)
	{
		newHardLink += replaceHost.c_str();
	}
	else
	{
		newHardLink += this->m_host.c_str();
	}

	std::string full_url =  this->m_protocol.c_str();
	full_url += "://";
	full_url += this->m_baseProxyUrl.c_str();
	this->m_proxyUrlContents = this->m_actualUrlContents;

	if(this->debugMode)
	{
		printf("this is the fullUrl:%s\n",full_url.c_str());
		printf("this is the newHardLink:%s\n",newHardLink.c_str());
	}

	// Change the hard link.
	std::string::size_type fullUrlPos = this->m_proxyUrlContents.find(full_url,0);
	
	while(fullUrlPos != std::string::npos)
	{
		this->m_proxyUrlContents.replace(fullUrlPos,full_url.length(),newHardLink.c_str(),(newHardLink.length()-1));
		fullUrlPos = this->m_proxyUrlContents.find(full_url,0);
	}

	if(this->debugMode)
	{
		//printf("this is the proxy Content:%s\n",this->m_proxyUrlContents.c_str());
	}
	
	// Replace the images.
	//if((m_proxySubdomain.compare("www")!=0)&&(m_proxySubdomain.compare("127")!=0)&&(m_proxySubdomain.compare("mssql")!=0))
	{	
		std::string::size_type imagePos = this->m_proxyUrlContents.find("<img src=\"/",0);

		while(imagePos != std::string::npos)
		{
			this->m_proxyUrlContents.insert((imagePos+10),full_url);
			imagePos = this->m_proxyUrlContents.find("<img src=\"/",0);
		}
	}

	std::string::size_type docCookiePos = this->m_proxyUrlContents.find("unescape(document.cookie)",0);
	std::string cookieString = "'";
	
	cookieString+= this->m_cookieValue;
	cookieString+= "'";

	while(docCookiePos != std::string::npos)
	{
		this->m_proxyUrlContents.replace(docCookiePos+9,15,cookieString.c_str(),cookieString.length());
		docCookiePos = this->m_proxyUrlContents.find("unescape(document.cookie)",0);
	}
}

void Deliver::GetRedirect(ProxyHttpGetSocket &ref)
{
	std::string redirectLocation;

	if(ref.GetRedirectLocation().find("http")==std::string::npos)
	{
		redirectLocation = this->m_protocol;
		redirectLocation += "://";
		redirectLocation += this->m_baseProxyUrl;
		redirectLocation += ref.GetRedirectLocation().c_str();
	}
	else
	{
		redirectLocation = ref.GetRedirectLocation().c_str();
	}

	if(this->debugMode)
	{
		printf("redirection to url:%s\n",redirectLocation.c_str());
	}
	
	ProxyHttpGetSocket redirect(ref.MasterHandler()); 
	this->m_method = "GET";
	Get(redirectLocation, redirect);
	ref = redirect;
}