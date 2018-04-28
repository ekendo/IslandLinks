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
//#include <stdio.h>

#include "Deliver.h"
#include "PortForwardSocket.h"
#include "HTTPServerSocket.h"
#include "ServerHandler.h"
#include "SocketAddress.h"
#include <process.h>
#include <Lock.h>

#ifdef _DEBUG
#define DEB(x) x
#else
#define DEB(x) 
#endif

//std::list<Hit> ServerSocket::hits;


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
	
	/*
	if(strcmp(GetMethod().c_str(),"GET")==0)
	{
		_beginthread( RunDeliver, 0,  this );
		
	}

	if(strcmp(GetMethod().c_str(),"POST")==0)
	*/
	{
		DEB(printf("in post detached\n");)

		if(strlen(this->m_postData.c_str()) >= this->GetHttpContentLength())
		{
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
					
			// init variable for next time.
			this->m_postData = "";
}

void HTTPServerSocket::OnHit(ProxyHttpGetSocket& proxy, std::string& userSession,const std::string actualContent, const std::string proxyContent)
{
	//this->GetP

	if(actualContent.compare("ServerUp")==0)
	{
		this->SetStatus("200");
		this->SetStatusText("OK");
		this->SendResponse();
		this->Send(actualContent);
		this->SetCloseAndDelete();
	}
	else
	{
		this->SetStatus(proxy.GetResponseStatus());
		this->SetStatusText(proxy.GetStatusText());

		if(this->m_debugValue)
		{
			printf("proxyStatus:%s-ServerSocketStatus:%s\n",proxy.GetResponseStatus().c_str(),this->GetStatus().c_str());
		}

		if(proxy.P3pSet())
		{
			this->AddResponseHeader("P3P", proxy.GetP3pSetting());
		}

		if(proxy.CacheControlSet())
		{
			this->AddResponseHeader("Cache-Control", proxy.GetCacheControl());
		}

		if(proxy.VarySet())
		{
			this->AddResponseHeader("Vary", proxy.GetVaryValue());
		}

		if(proxy.HostCookieSet())
		{
			//printf("hostCookieSet\n");
			//this->AddResponseHeader("Set-Cookie", proxy.GetHostCookieSessionValue());		
			this->AddResponseHeader("set-cookie",proxy.GetHostCookieSessionValue());
		}

		if(proxy.CookieSet())
		{	
			//printf("proxyCookieSet\n");
			//Replace domain value in cookie.
			Parse d(proxy.GetUrlHost(),".");
			d.getword();

			std::string baseDomain = "domain=.";
			baseDomain += d.getrest();
			std::string newBaseDomain = "domain=";
			newBaseDomain+= this->GetServer();
			std::string newCookie = proxy.GetCookieSet();
			std::string::size_type domainPos = newCookie.find(baseDomain);
			

			while(domainPos!=std::string::npos)
			{
				newCookie.replace(domainPos,baseDomain.length(),newBaseDomain);
				domainPos = newCookie.find(baseDomain);
			}

			this->AddResponseHeader("Set-Cookie", newCookie);
			
			if(this->m_debugValue)
			{
				printf("proxyCookieSet:%s\n",proxy.GetCookieSet().c_str());
				printf("baseDomain:%s\n",baseDomain.c_str());
				printf("newBaseDomain:%s\n",newBaseDomain.c_str());
				printf("set-cookie value=%s\n",newCookie.c_str());
			}
		}

		if(proxy.TransferEncoded())
		{
			this->AddResponseHeader("Transfer-Encoded", proxy.GetTransferEncoding());
		}

		if(proxy.RedirectLocationSet())
		{
			this->AddResponseHeader("Location",proxy.GetRedirectLocation());
		}

		this->AddResponseHeader("Content-Type", proxy.GetContentType());

		if(proxy.ContentEncoded())
		{
			this->AddResponseHeader("Content-Encoded", proxy.GetContentEncoding());
		}

		this->AddResponseHeader("Referer", proxy.GetHttpReferer());
							
		this->SendResponse();
						
		this->Send(proxyContent);	

		this->SetCloseAndDelete();
		
		if(this->dataPort>0)
		{

			DEB(printf("set close and delete\n");)
			DEB(printf("postData is->%s\n",this->GetPostData().c_str());)
			DEB(printf("requestHeader:%s\n",proxy.GetRequestHeader().c_str());)
			DEB(printf("ServerSocket-RemoteAddress:%s\n",proxy.GetDestinationIP().c_str());)				

			int requestSize = proxy.GetRequestHeader().size() + this->GetPostData().size();
			int responseSize = proxy.GetResponseHeader().size() + actualContent.size();
			
			Hit h(proxy.GetUrlHost(), proxy.GetProtocol(), proxy.GetMethod(), 
				proxy.GetUrl(), proxy.GetContentType(), userSession, 
				actualContent,proxy.GetRequestHeader(),proxy.GetResponseHeader(),
				this->GetPostData(),this->GetRemoteAddress(), proxy.GetDestinationIP(),
				requestSize,responseSize,proxy.GetResponseTime());

				
			DEB(
				printf("about to insert HOST:%s-METHOD:%s-SESSION:%s\n",proxy.GetUrlHost().c_str(),proxy.GetMethod().c_str(),userSession.c_str());
			)
			DEB(printf("waiting for a lock on MasterHandler\n");)

		
			SaveToDBArchive(h);
		}
		/*
		hits.push_back(h);
		
		SaveToDBArchive();
	
		hits.pop_front();

		DEB(printf("popped a hit%d\n",this->hits.size());)
		*/
		/*
		if(::WaitForSingleObject(winMutex,INFINITE ) == WAIT_OBJECT_0)
		{
			if (::WaitForSingleObject(archiveEvent, INFINITE) == WAIT_OBJECT_0)
			{
				SaveToDBArchive();		
			}

			::SetEvent(trackerEvent);
			::ReleaseMutex(winMutex);
		}
		*/

		//::CloseHandle(winMutex);
		//::CloseHandle(archiveEvent);
	}
}

bool HTTPServerSocket::SaveToDBArchive(Hit hit)
{
	std::string Values = "";
	bool done = false;

	//if(this->hits.size())
	{
		//Hit data ((Hit)this->hits.pop_front());
		Values = "'" + hit.Host + "',";
		Values += "'";
		Values += hit.Protocol;
		Values += "',";
		Values += "'";
		Values += hit.Method;
		Values += "',";
		Values += "'";
		Values += hit.Url;
		Values += "',";
		Values += "'";
		Values += hit.MimeType;
		Values += "',";
		Values += "'";
		Values += hit.Session;
		Values += "',";
		Values += "'";

		std::string insertContent = hit.Contents.c_str();
		std::string::size_type singleQuot = insertContent.find("'");

		while(singleQuot!= std::string::npos)
		{
			insertContent.replace(singleQuot,1,"s");
			insertContent.insert(singleQuot+1,"quot");

			singleQuot = insertContent.find("'");
		}


		std::string::size_type doubleQuot = insertContent.find("\"");

		while(doubleQuot!= std::string::npos)
		{
			insertContent.replace(doubleQuot,1,"d");
			insertContent.insert(doubleQuot+1,"quot");

			doubleQuot = insertContent.find("\"");
		}
		
		Values += insertContent.c_str();
		Values += "',";
		Values += "'";
		Values += hit.RequestHeader;
		Values += "',";
		Values += "'";
		Values += hit.ResponseHeader;
		Values += "',";
		Values += "'";
		Values += hit.PostData;
		Values += "',";
		char value[20];
		sprintf(value,"%6.3f",hit.ResponseTime);
		Values += value;
		Values += ",";
		sprintf(value,"%d",hit.RequestSize);
		Values += value;
		Values += ",";
		sprintf(value,"%d",hit.ResponseSize);
		Values += value;
		Values += ",";
		Values += "'";
		Values += hit.SourceIPAddress;
		Values += "',";
		Values += "'";
		Values += hit.DestinationIPAddress;
		Values += "'";

		//::MessageBoxA(0,Values.c_str(),Values.c_str(),0);

		/*
		if(LoadInGlobalMemory("hitTrackerValues", Values))
		{
			this->hits.pop_front();

			DEB(printf("popped a hit%d\n",this->hits.size());)

			done = true;
		}
		*/

		int i =0;
		SocketHandler h;
		h.SetReadBuffer(this->m_readBuf);
		h.SetWriteBuffer(this->m_writeBuf);
		char buffer [33];
		std::string dataUrl = "http://";
		dataUrl += this->dataAddress.c_str();
		dataUrl += ":";
		dataUrl += itoa(this->dataPort,buffer,10);

		if(this->m_debugValue)
		{
			printf("before Q request:%s\n",dataUrl.c_str());
		}

		ProxyHttpGetSocket *q = new ProxyHttpGetSocket(h,dataUrl);
		q->SetMethod("POST");
		q->SetHttpVersion("HTTP/1.1");
		q->AddResponseHeader("Enqueue", "OnHit");
		q->SetContentLength(Values.length());
		q->SetDebugMode(this->m_debugValue);
		q->AddPostData(Values.c_str());
		
		//Values.cclear();

		//while(i < 10)
		{
			q->DoHttpOpperation();
			
			h.Add(q);

			//while (h.GetCount())
			while(!q->Complete())
			{
				h.Select(0, 10);
			}

			if(m_debugValue)
			{
				printf("response Srting:%s\n",q->GetResponseString().c_str());
			}

			//if(q->GetResponseString().compare("Q Succeeded")==0)
			if(strcmp(q->GetResponseString().c_str(),"Q Succeeded")==0)
			{
				if(this->m_debugValue)
				{
					printf("data Q'd\n");
					//break;
				}
			}
			else
			{
				if(this->m_debugValue)
				{
					printf("not succeeded:%s\n",q->GetResponseString().c_str());
				}
			}
			
			i++;
		}

		if(m_debugValue)
		{
			printf("after Q request\n");
			//printf(Values);
		}

		delete q;
	}
	/*
	else
	{
		done = true;
	}
	*/

	return done;
}


bool HTTPServerSocket::LoadInGlobalMemory(std::string messageName, std::string messageValue)
{
	#define BUF_SIZE 500000

	TCHAR * szName = (TCHAR *) ::SysAllocStringLen(NULL, (sizeof(TCHAR) * messageName.length()));
	TCHAR * szMsg = (TCHAR *) ::SysAllocStringLen(NULL, (sizeof(TCHAR) * messageValue.length()));
	bool objectLoaded = false;
	bool memoryCreated = false;
	bool memoryFound = false;

	HANDLE hMapFile;
	LPCTSTR pBuf = NULL;

	char buffer [50];
	int n=sprintf (buffer, "buff size: %d",messageValue.length());

	if(messageValue.length() > 0 )
	{
		MultiByteToWideChar(CP_ACP,0,(LPCSTR) messageName.c_str(),-1,szName,(sizeof(TCHAR)* messageName.length()));
		MultiByteToWideChar(CP_ACP,0,(LPCSTR) messageValue.c_str(),-1,szMsg,(sizeof(TCHAR)* messageValue.length()));
		
		//::MessageBox(0,L"*",szMsg,0);

		hMapFile = OpenFileMapping(
						   FILE_MAP_ALL_ACCESS,   // read/write access
						   FALSE,                 // do not inherit the name
						   (LPCWSTR) szName); 

		if((hMapFile== NULL)||(hMapFile == INVALID_HANDLE_VALUE))
		{
			
			memoryCreated = false;
		}
		else
		{
			memoryCreated = true;
			//return memoryCreated;
		}

		if (hMapFile != NULL && hMapFile != INVALID_HANDLE_VALUE) 
		{ 
			pBuf = (LPTSTR) MapViewOfFile(hMapFile,   // handle to map object
					FILE_MAP_ALL_ACCESS, // read/write permission
					0,                   
					0,                   
					BUF_SIZE); 
							
			//::MessageBoxA(0,"hMapFile!=NULL", "observer",0);

			if(pBuf!=NULL)
			{		
				// Len will hold the required length of converted string.
				int len = WideCharToMultiByte(CP_ACP, 0, (LPCWSTR) pBuf, -1, 0, 0, 0, 0);

				// Allocate the buffer to hold the converted string.
				LPSTR result = new char[len];

				// Do the conversion.
				WideCharToMultiByte(CP_ACP, 0, (LPCWSTR) pBuf, -1, result, len, 0, 0);

				LPSTR szANSIString = result;


				//printf("length of empty string%d\n",len);
				
				//::MessageBoxA(0,szANSIString,"mapped\n",0);
				
				if((memoryCreated))
				{
					if(len>1)
					{
						UnmapViewOfFile(pBuf);
						CloseHandle(hMapFile);
						::SysFreeString(szName);
						::SysFreeString(szMsg);
						//pBuf = NULL;
						return false;
					}
				}

				delete []result;
			}

			if((pBuf != NULL)&&(!IsBadWritePtr((PVOID)pBuf, sizeof(TCHAR)* messageValue.length())))
			{
				
					//ZeroMemory((PVOID)pBuf, sizeof(TCHAR)* messageValue.length());

					//if(sizeof(pBuf)>sizeof(szMsg))
					{
										
						CopyMemory((PVOID)pBuf, szMsg, sizeof(TCHAR)* messageValue.length());
						UnmapViewOfFile(pBuf);
						CloseHandle(hMapFile);				
						::SysFreeString(szName);
						::SysFreeString(szMsg);
						//::SysFreeString((BSTR)pBuf);
					}
					//::MessageBoxA(0,"pBuf!=NULL", "observer",0);
					objectLoaded = true;
			}
			else
			{
				//::MessageBoxA(0,"pBuff==NULL", "observer",0);
				objectLoaded = false;
			}
		}
		else
		{
			//::MessageBoxA(0,"hMap==NULL", "observer",0);
			objectLoaded =false;
		}
	}
	else
	{
		//::MessageBoxA(0,"no data length", "testing",0);	
	}

	
	return objectLoaded;
}
