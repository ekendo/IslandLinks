/**
 **	File ......... ServerSocket.cpp
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
#include "PortForwardSocket.h"
#include "HTTPServerSocket.h"
#include "SocketAddress.h"
#include "SocketHandler.h"
#include "ServerHandler.h"
//#include <process.h>
#include <Lock.h>
#include <stdio.h>
#include <cctype>
#include <algorithm>
#include <cstdlib> 
#include <ctime> 

#ifdef _DEBUG
#define DEB(x) x
#else
#define DEB(x) 
#endif



HTTPServerSocket::HTTPServerSocket(ISocketHandler& h):
HTTPSocket(h)
,Gemensamt()
,tmpl(0)
{
	m_actualSourceIP = "";
}


HTTPServerSocket::~HTTPServerSocket()
{
}


void HTTPServerSocket::OnFirst()
{
	if (IsRequest())
	{
		if(this->m_debugValue)
		{
			printf(" Method: %s\n",GetMethod().c_str());
			printf(" URL: %s\n",GetUrl().c_str());
			printf(" Http version: %s\n",GetHttpVersion().c_str());
		}
	}

	if (IsResponse())
	{
		if(this->m_debugValue)
		{
			printf(" Http version: %s\n",GetHttpVersion().c_str());
			printf(" Status: %s\n",GetStatus().c_str());
			printf(" Status text: %s\n",GetStatusText().c_str());
		}
	}

}

void HTTPServerSocket::OnRawData(const char *buf,size_t len)
{
	if(m_debugValue)
	{
		printf("ss->OnRaw forwardPort:%d\n",this->GetForwardPort());
	}

	if(this->GetForwardPort()>0)
	{
		if(m_debugValue)
		{
			printf("portForwardRawDataIN\n");
		}
		if (Handler().Valid(f) && f -> Ready())
		{
			if(m_debugValue)
			{
				printf("f ready and valid.\n");
			}

			if (tmpl)
			{
				f -> SendBuf(tmps, tmpl);
				tmpl = 0;
			}
			f -> SendBuf(buf, len);
		}
		else
		{
			if(m_debugValue)
			{
				printf("f not ready and not valid.\n");
			}

			Handler().LogError(this, "OnRawData", 0, "m_remote not ready");
			memcpy(tmps + tmpl, buf, len);
			tmpl += len;
		}
		
	}
	else
	{
		if(m_debugValue)
		{
			printf("HTTPServerSocket onRawData\n");
		}

		std::string bufString = buf;
		std::string::size_type sourceLocStart = bufString.find("sourceIP:") + 9;
		std::string::size_type sourceLocEnd = bufString.find("\n");
		if((sourceLocStart!=std::string::npos)&&(sourceLocEnd!=std::string::npos))
		{
			//this->m_actualSourceIP = bufString.substr(sourceLocStart,sourceLocEnd);
			//printf("actual source IP\n",bufString.c_str());
			//HTTPSocket::OnRawData(bufString.substr(sourceLocEnd+1).c_str(),len);

		}
		

			HTTPSocket::OnRawData(buf,len);
		
	}
}
	

void HTTPServerSocket::OnHeader(const std::string& key,const std::string& value)
{
	
	if(this->m_debugValue)
	{
		printf("OnHeader(): %s: %s\n",key.c_str(),value.c_str());
	}
	
	if (!strcasecmp(key.c_str(),"host"))
	{
		SetServer(value);
	}
	
	if (!strcasecmp(key.c_str(),"user-agent"))
	{
		SetUserAgent(value);
	}
	
	if (!strcasecmp(key.c_str(),"referer"))
	{
		SetHttpReferer(value);
	}

	if (!strcasecmp(key.c_str(),"content-type"))
	{
		SetContentType(value);
	}

	if(!strcasecmp(key.c_str(),"connection"))
	{
		SetConnection(value);
	}	

	if (!strcasecmp(key.c_str(),"content-length"))
	{
		SetContentLength(atoi(value.c_str()));
	}

	if(!strcasecmp(key.c_str(),"keep-alive"))
	{
		SetKeepAlive(atoi(value.c_str()));
	}

	if(!strcasecmp(key.c_str(),"cookie"))
	{
		std::string currentCookie = this->GetCookie();
		if(this->GetCookie().length()>0)
		{
			currentCookie+=";";
		}
		currentCookie+= value.c_str();
		SetCookie(currentCookie);

		DEB(printf("currentCookie:%s\n",currentCookie.c_str());)
	}

	if(!strcasecmp(key.c_str(),"set-cookie"))
	{
		DEB(printf("serverSocket getCookieValue%s\n",this->GetCookie().c_str());)
		std::string currentCookie = this->GetCookie();
		if(this->GetCookie().length()>0)
		{
			currentCookie+=";";
		}
		currentCookie+= value.c_str();
		SetCookie(currentCookie);
	}

	reqHeader+=key.c_str();
	reqHeader+=":";
	reqHeader+=value.c_str();
	reqHeader+="\r\n";
}


void HTTPServerSocket::OnData(const char * data,size_t size)
{
	DEB(
		printf("in OnData\n");
	)

	for(int i=0;i<size;i++)
	{
		this->m_postData+= data[i];
	}

	if(this->m_debugValue)
	{
		printf("Data:%s\n",this->m_postData.c_str());
		printf("Data Size:%d\n",size);
		printf("postDataSize:%d\n",strlen(this->m_postData.c_str()));
	}
	
	if(strlen(this->m_postData.c_str()) >= this->GetHttpContentLength())
	{
		SetPostData(this->m_postData.c_str());
		
		if (!Detach())
		{
			if(this->m_debugValue)
			{
				printf("Detach() failed\n");
			}

			if(strcmp(GetMethod().c_str(),"POST")==0)
			{
				
				DEB(printf("in post on data\n");)

				if(strlen(this->m_postData.c_str()) >= this->GetHttpContentLength())
				{
					// Do Somethin
					/*
					//if (!Detach())
					{
						try
						{
							SocketHandler handler;
							ProxyHttpGetSocket r(this->MasterHandler());
							DEB(printf("in OnData for POST\n");)
							Deliver d(r,*this,GetServer(),"http",GetMethod(),GetUrl(),this->hostRedirect);
							d.SetDebug(this->m_debugValue);
							d.SetUserAgent(MyUseragent());
							d.SetContentLength(GetHttpContentLength());
							d.SetContentType(GetHttpContentType());
							d.SetKeepAlive(this->GetKeepAlive());
							d.SetConnection(this->GetConnection());
							d.SetReferer(this->GetHttpReferer());
							d.SetPostData(GetPostData());
							d.SetTrackedUrls(this->m_trackedUrls);	
							d.SetCookie(GetCookie());
							d.SetReadBuffer(this->m_readBuf);
							d.SetWriteBuffer(this->m_writeBuf);
							DEB(printf("ServerSocket-before RUN\n");)
							d.Run();
							
						}
						catch(...)
						{
							if(this->m_debugValue)
							{
								printf("Exception Running GET Handler!\n");
							}
						}

						this->m_postData = "";				
					}
					*/
				}
			}
		}
	}
}


void HTTPServerSocket::OnHeaderComplete()
{
	DEB(
		printf("in OnHeaderComplete\n");
	)

	if(this->GetForwardPort()==0)
	{
		if (!Detach())
		{
			DEB(printf("!Detach()\n");)
			SetCloseAndDelete();
		}
	}

}

void HTTPServerSocket::OnDelete()
{
	if(this->GetForwardPort()==0)
	{
		if (Handler().Valid(f))
		{
			f -> SetCloseAndDelete();
		}
	}
}

void HTTPServerSocket::OnDetached()
{
	DEB(
		printf("in OnDetached\n");
	)

	if(this->m_debugValue)
	{
		printf("%s","about to run deliver\n");
		printf("contentLen:%d\n",this->GetHttpContentLength());
		printf("method:%s\n",GetMethod().c_str());
	}
	
	
	if(strcmp(GetMethod().c_str(),"GET")==0)
	{
		
		if(this->debugValue)
		{
			printf("Host:%s\n",this->GetServer().c_str());
			printf("QueryString:%s\n",this->GetQueryString().c_str());
			if(this->GetClientRemoteAddress()!=NULL)
			{
				printf("clientRemoteIp:%s\n",this->GetClientRemoteAddress()->Convert(false).c_str());
			}

			if(this->GetRemoteSocketAddress()!=NULL)
			{
				printf("remoteSocketAddress:%s\n",this->GetRemoteSocketAddress()->Convert(false).c_str());
			}
			
			printf("url%s:\n",this->GetUrl().c_str());

			if(!this->GetHttpReferer().empty())
			{
				printf("referrer:%s",this->GetHttpReferer().c_str());
			}
		}

		// 4 for loc
		//std::string matchUrl = this->GetQueryString().substr(4);
		//std::string matchUrl = this->GetQueryString();
		std::string matchUrl = this->GetUrl().c_str();
		std::string matchEncodedUrl = ""; 
		std::string matchUrlQString = "";
		std::string encodedTempUrl = "";
		std::string tempUrl = "";

		
		std::string::size_type ampersand = matchUrl.find("&");
		std::string::size_type affUrl = matchUrl.find("affiliateUrl=");

		if(affUrl!=std::string::npos)
		{
			if(ampersand==std::string::npos)
			{
				tempUrl+= matchUrl.substr(affUrl+13);
					
				encodedTempUrl = matchUrl.substr(affUrl+13);
				
				matchUrl = "http://";
				matchUrl += tempUrl.c_str();
				
			}
			else
			{
				matchUrlQString += matchUrl.substr(ampersand);

				tempUrl+= matchUrl.substr(affUrl+13,ampersand - (affUrl+13));
				
				encodedTempUrl = matchUrl.substr(affUrl+13,ampersand - (affUrl+13));
				encodedTempUrl+= this->urlencode(matchUrlQString);
				
				matchUrl = "http://";
				matchUrl += tempUrl.c_str();
				matchUrl += "?";
				matchUrl += matchUrlQString.substr(1).c_str();
			}
		}

		// Default track Map
		std::map<std::string,std::string>::const_iterator it;
		//= this->m_trackedUrlMap.find(matchUrl);
		
		for(it = this->m_trackedUrlMap.begin(); it!=this->m_trackedUrlMap.end(); ++it)
		{
			// Lcase match Url
			std::string tmp = matchUrl;
			std::transform(tmp.begin(), tmp.end(),tmp.begin(),(int(*)(int)) std::tolower);

			// Lcase match Url
			std::string etmp = encodedTempUrl;
			std::transform(etmp.begin(), etmp.end(),etmp.begin(),(int(*)(int)) std::tolower);
			
			// LCase stored Url
			std::string tmpCompare = it->first;
			std::transform(tmpCompare.begin(), tmpCompare.end(), tmpCompare.begin(),(int(*)(int))std::tolower);

			//std::string::size_type matched = tmp.find(tmpCompare);
			std::string::size_type matched = tmpCompare.find(tmp);
			std::string::size_type encodedMatched = tmpCompare.find(etmp);
			
			if(matched !=std::string::npos)
			{
				break;
			}
			
			if(encodedMatched !=std::string::npos)
			{
				break;
			}
		}

		std::string::size_type http = matchUrl.find("http://");

		if(http==std::string::npos)
		{
			tempUrl+= matchUrl; 
			matchUrl = tempUrl.c_str();
		}

		// Get current track Map
		/*
		//if(static_cast<ServerHandler&> (this->MasterHandler()))
		{
			ServerHandler &sh = static_cast<ServerHandler &> (this->MasterHandler());
			
			//if(sh)
			{
				it = sh.GetPairList("server/affiliateUrls/affiliateUrl", "keywords").find(matchUrl);
			}
		}
		*/

		SocketHandler handler;
		handler.SetReadBuffer(this->readBuf);
		handler.SetWriteBuffer(this->writeBuf);
		std::string url = "";
		std::string newReferrer = "";
		std::string response = "";
		bool theSameUrl = false;
		
		if(it==this->m_trackedUrlMap.end())
		{
			//url += "http://";
			url += matchUrl;
		}
		else
		{
			/*
			std::string::size_type ampersand = matchUrl.find("&");

			if(ampersand!=std::string::npos)
			{
				tempUrl+= matchUrl.substr(0,ampersand);
				tempUrl+= "?";
				tempUrl+= matchUrl.substr(ampersand+1);
			
				int separator = 0;

				url += tempUrl.substr(separator);
			}
			else
			*/
			{

				//url += "http://";
				url += matchUrl;
			}
		}
		
		
		ProxyHttpGetSocket r(handler,url);

		while(url.length()>0)
		{
			r.SetDebugMode(this->m_debugValue);
			r.SetMethod(this->GetMethod());
			r.SetHttpVersion("HTTP/1.1");
			r.SetUserAgent(MyUseragent());
			r.SetContentLength(GetHttpContentLength());
			r.SetContentType(GetHttpContentType());
			r.SetKeepAlive(this->GetKeepAlive());
			r.SetConnection(this->GetConnection());
			r.SetSpecificLineMatch("window.location.replace('","')");

			if(it==this->m_trackedUrlMap.end())
			{
				printf("naw\n");
				r.SetHttpReferer(this->GetHttpReferer());
				newReferrer = this->GetHttpReferer();
			}
			else
			{
				printf("yeah\n");
				
				
				if(!this->GetHttpReferer().empty())
				{
					std::string referer = this->GetHttpReferer();
					//std::string referer = "http://www.google.com/search?q=ink+jets&ie=utf-8&aq=t";
					std::string::size_type googleStartKeywords = referer.find("q=")+2;
					std::string::size_type googleEndKeywords  = referer.find("&",googleStartKeywords);
					std::string newKeywords, formattedKeywords = "";
					std::vector<std::string> replacementKeywords;

					std::string::size_type newKeywordSeparator = it->second.find("|");
					int separator = 0;

					int i=0;
					srand((unsigned)time(0)); 
					int random_integer; 
					
					// load up our keyword vector
					while(newKeywordSeparator!=std::string::npos)
					{
						//newKeywords = it->second.substr(separator,(newKeywordSeparator-separator));
						//newKeywords += "+";
						replacementKeywords.push_back(it->second.substr(separator,(newKeywordSeparator-separator)));

						separator = newKeywordSeparator+1;
						newKeywordSeparator = it->second.find("|",newKeywordSeparator+1);
						i++;
					}

					//newKeywords += it->second.substr(separator,(it->second.length()-separator));
					replacementKeywords.push_back(it->second.substr(separator,(it->second.length()-separator)));
					random_integer = (rand()%(i));
					newKeywords += replacementKeywords.at(random_integer);
					
					std::string::size_type keywordSeparator = newKeywords.find(" ");
					separator = 0;

					// Replacing spaces with +
					while((keywordSeparator!=std::string::npos))
					{
						// Ignore the first one
						if(keywordSeparator>0)
						{
							formattedKeywords += newKeywords.substr(separator,(keywordSeparator-separator));
							formattedKeywords += "+";
						}

						separator = keywordSeparator+1;
						keywordSeparator = newKeywords.find(" ",keywordSeparator+1);
					}

					formattedKeywords += newKeywords.substr(separator);

					newReferrer += referer.substr(0,googleStartKeywords);
					newReferrer += formattedKeywords;
					newReferrer += referer.substr(googleEndKeywords);
					//newReferrer += "\n";

					std::string::size_type oldKeywordSeparator = newReferrer.find("\r");
					if(oldKeywordSeparator!=std::string::npos)
					{
						newReferrer.replace(oldKeywordSeparator,1,"");
			
						//oldKeywordSeparator = keywords.find("\n",oldKeywordSeparator+1);

					}

					r.SetHttpReferer(this->GetHttpReferer());
					r.SetHttpReferer(newReferrer);
					//r.SetHttpReferer("http://www.google.com/search?hl=en&q=canadian+tshirt&btnG=Search");

				}

			}
				
			//r.SetPostData(GetPostData());
			//r.SetTrackedUrls(this->m_trackedUrls);	
			r.SetCookie(GetCookie());
			r.DoHttpOpperation();

			handler.Add(&r);

			handler.Select(0,5000);

			//int i=0;
			bool complete = false;
			while((!complete)&&(!r.RedirectLocationSet()))
			{
				handler.Select(0,5000);		
				
				if(r.Complete() )
				{
					complete =true;
				}

			}

			char buffer[30];
			//this->AddResponseHeader("Content-Length",itoa(r.GetContentLength(),buffer,16));

			if(r.P3pSet())
			{
				this->AddResponseHeader("P3P",r.GetP3pSetting());
			}
			
			if(strcmp(r.GetRedirectLocation().c_str(),url.c_str())==0)
			{
				theSameUrl = true;
			}
			else
			{
				theSameUrl = false;
			}
						
			url = "";

			if(r.RedirectLocationSet()&&(!theSameUrl))
			{
				

				std::string::size_type actualAffiliate;

				if(r.GetRedirectLocation().find("http") != std::string::npos)
				{
					if(r.GetRedirectLocation().find("affiliateUrl=") == std::string::npos)
					{
						this->AddResponseHeader("Location",r.GetRedirectLocation());
						url = r.GetRedirectLocation();
					}
					else
					{
						actualAffiliate = r.GetRedirectLocation().find("affiliateUrl=");

						this->AddResponseHeader("Location",r.GetRedirectLocation().substr(actualAffiliate+13));

						url = r.GetRedirectLocation().substr(actualAffiliate+13);
					}
				}

				if(r.GetRedirectLocation().find("https") != std::string::npos)
				{
					if(r.GetRedirectLocation().find("affiliateUrl=") == std::string::npos)
					{
						this->AddResponseHeader("Location",r.GetRedirectLocation());
						url = r.GetRedirectLocation();
					}
					else
					{
						actualAffiliate = r.GetRedirectLocation().find("affiliateUrl=");

						this->AddResponseHeader("Location",r.GetRedirectLocation().substr(actualAffiliate+13));
						url = r.GetRedirectLocation().substr(actualAffiliate+13);
					}

					
				}
				
				if(r.GetRedirectLocation().find("http") == std::string::npos)
				{
					std::string newLocation = "http://";
					newLocation+= r.GetUrlHost();
					newLocation+= r.GetRedirectLocation();

					if(newLocation.find("affiliateUrl=") == std::string::npos)
					{
						//this->AddResponseHeader("Location",newLocation);
						url  = newLocation;
					}
					else
					{
						actualAffiliate = newLocation.find("affiliateUrl=");

						//this->AddResponseHeader("Location",newLocation.substr(actualAffiliate+13));
						url = newLocation.substr(actualAffiliate+13);
					}
				}
			}
		}
		
		if(!r.GetSpecificLineMatchValue().empty())
		{
			// for shareasale
			response +="<body bgcolor='white'><script language='javascript1.2'>window.location.replace('";
			response += r.GetSpecificLineMatchValue();
			response += "')</script></body>";
		}

		this->AddResponseHeader("Content-Type", r.GetContentType());


		this->AddResponseHeader("Referer",newReferrer);

		if(r.HostCookieSet())
		{
			this->AddResponseHeader("Set-Cookie",r.GetHostCookieSessionValue());
		}

		if(r.CookieSet())
		{
			Parse d(r.GetUrlHost(),".");
			d.getword();

			std::string baseDomain = "domain=.";
			baseDomain += d.getrest();
			std::string newBaseDomain = "domain=";
			newBaseDomain+= this->GetServer();
			std::string newCookie = r.GetCookieSet();
			std::string::size_type domainPos = newCookie.find(baseDomain);
			

			while(domainPos!=std::string::npos)
			{
				newCookie.replace(domainPos,baseDomain.length(),newBaseDomain);
				domainPos = newCookie.find(baseDomain);
			}

			this->AddResponseHeader("Set-Cookie", newCookie);
			
			if(this->m_debugValue)
			{
				printf("proxyCookieSet:%s\n",r.GetCookieSet().c_str());
				printf("baseDomain:%s\n",baseDomain.c_str());
				printf("newBaseDomain:%s\n",newBaseDomain.c_str());
				printf("set-cookie value=%s\n",newCookie.c_str());
			}

			//this->AddResponseHeader("Set-Cookie",r.GetCookieSet());
		}

		this->AddResponseHeader("Connection",r.GetConnection());
		
		if(r.CacheControlSet())
		{
			this->AddResponseHeader("Cache-Control",r.GetCacheControl());
		}

		if(r.ContentEncoded())
		{
			this->AddResponseHeader("Content-Encoded",r.GetContentEncoding());
		}

		if(r.TransferEncoded())
		{
			this->AddResponseHeader("Transfer-Encoding",r.GetTransferEncoding());
		}

		if(r.VarySet())
		{
			this->AddResponseHeader("Vary",r.GetVaryValue());
		}
		
		
		this->SetStatus(r.GetResponseStatus());
		this->SetStatusText(r.GetStatusText());
		this->SendResponse();

		if(!r.GetResponseString().empty())
		{
			this->Send(r.GetResponseString());
		}
		else
		{
			this->Send(response);
		}
		

		
		this->SetCloseAndDelete();
						
	}

	if(strcmp(GetMethod().c_str(),"POST")==0)
	{
		DEB(printf("in post detached\n");)

		if(strlen(this->m_postData.c_str()) >= this->GetHttpContentLength())
		{
			// Do Somethin
			/*
			try
			{
					
				SocketHandler handler;
				ProxyHttpGetSocket r(this->MasterHandler());
				Deliver d(r,*this,GetServer(),"http",GetMethod(),GetUrl(),this->hostRedirect);
				d.SetDebug(this->m_debugValue);
				d.SetUserAgent(MyUseragent());
				d.SetContentLength(GetHttpContentLength());
				d.SetContentType(GetHttpContentType());
				d.SetKeepAlive(this->GetKeepAlive());
				d.SetConnection(this->GetConnection());
				d.SetReferer(this->GetHttpReferer());
				d.SetPostData(GetPostData());
				d.SetTrackedUrls(this->m_trackedUrls);	
				d.SetCookie(GetCookie());
				d.SetReadBuffer(this->m_readBuf);
				d.SetWriteBuffer(this->m_writeBuf);
				d.Run();
			}
			catch(...)
			{
				if(this->m_debugValue)
				{
					printf("Exception Running POST handler!\n");
				}
			}
			*/		
			// init variable for next time.
			this->m_postData = "";
		}
	}
}


void HTTPServerSocket::Init() 
{
	
	/*
	if(this->securePort)
	{
		EnableSSL();
	}
	*/

	
	this->m_debugValue = this->debugValue;
	this->m_trackedUrls = this->trackerUrls;
	this->m_readBuf = this->readBuf;
	this->m_writeBuf = this->writeBuf;
	this->m_hostRedirect = this->hostRedirect;

	DEB(
		printf("forwardport:%d\n",this->GetForwardPort());
		printf("trackedUrls:%s\n",this->m_trackedUrls.c_str());
		printf("hostDmain:%s\n",this->hostRedirect.c_str());
	)

	/*
	winMutex = CreateMutexA( 
    NULL,                       // default security attributes
    FALSE,                      // initially not owned
    "CanWriteData");      // unnamed mutex	

	archiveEvent = ::CreateEventA(
	NULL,
	FALSE,
	FALSE,
	"ArchiveServiceReady");

	trackerEvent = ::CreateEventA(
	NULL,
	FALSE,
	FALSE,
	"TrackerDataReady");
	*/
	reqHeader = "";
}


void HTTPServerSocket::OnAccept() 
{
	DEB(
		printf("Accept\n");
	)
	
	HTTPSocket::OnAccept();

	DEB(
		printf("RemoteAddress:%s\n",this->GetRemoteAddress().c_str());
	)
}

void HTTPServerSocket::OnPortProxyAccept(std::string forwardAddress, std::string forwardParam, int forwardPort)
{
	this->m_portForwardValue = forwardPort;
	this->m_addressForwardValue = forwardAddress;
	this->m_forwardParamValue = forwardParam;
	
	DEB(
	printf("before portforward stuff\n");
	printf("forwardAddress:%s\n",forwardAddress.c_str());
	printf("forwardParam:%s\n",forwardParam.c_str());
	printf("forwardAddress:%d\n",forwardPort);
	)
	
	f = new PortForwardSocket(Handler(),this->m_readBuf, this->m_writeBuf);
	//f -> SetSourceIp(this->GetRemoteAddress());
	//printf("Main proxtAccept,SS-RemoteAddy:%s\n",this->GetRemoteAddress().c_str());
	f -> DoPortForward(this->m_addressForwardValue,this->m_portForwardValue);
	f -> SetDeleteByHandler();
	f -> SetRemote(this);
	//f ->SetConnectionRetry(5);
	//f ->SetReconnect();
	Handler().Add(f);

}

/** try, try again */
void HTTPServerSocket::OnFailure()
{

			DEB(
			printf("onfailure\n");
			)

			// Do Somethin
			/*
			try
			{
					
				SocketHandler handler;
				ProxyHttpGetSocket r(this->MasterHandler());
				Deliver d(r,*this,GetServer(),"http",GetMethod(),GetUrl(),this->hostRedirect);
				d.SetDebug(this->m_debugValue);
				d.SetUserAgent(MyUseragent());
				d.SetContentLength(GetHttpContentLength());
				d.SetContentType(GetHttpContentType());
				d.SetKeepAlive(this->GetKeepAlive());
				d.SetConnection(this->GetConnection());
				d.SetReferer(this->GetHttpReferer());
				d.SetPostData(GetPostData());
				d.SetTrackedUrls(this->m_trackedUrls);	
				d.SetCookie(GetCookie());
				d.SetReadBuffer(this->m_readBuf);
				d.SetWriteBuffer(this->m_writeBuf);
				d.Run();
			}
			catch(...)
			{
				if(this->m_debugValue)
				{
					printf("Exception Running POST handler!\n");
				}
			}
			*/		
			// init variable for next time.
			this->m_postData = "";
}





