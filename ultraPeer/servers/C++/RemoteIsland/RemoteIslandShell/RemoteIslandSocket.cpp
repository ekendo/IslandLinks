#include "stdafx.h"
#include "RemoteIslandSocket.h"
#include "ServerHandler.h"


#define RSIZE TCP_BUFSIZE_READ

#ifdef _DEBUG
#define DEB(x) x
#else
#define DEB(x) 
#endif

RemoteIslandSocket::RemoteIslandSocket(ISocketHandler& h) : YattaConnectSocket(h)
{
	commandEvent = ::CreateEventA(NULL,FALSE,FALSE,(LPCSTR)this->GetCommandEvent().c_str());

	this->serviceCommunicationPort = 32920;
	this->serviceCommandPort = 32920;
	header = true;
	first = true;
	peerIsOnYatta = false;
	isPeerContacting = false;
	isPeerQuery = false;
	isPeerRunningServiceAction = false;
	this->debugMode = this->debugValue;
}

RemoteIslandSocket::RemoteIslandSocket(ISocketHandler& h, std::string ip) : YattaConnectSocket(h)
{
	commandEvent = ::CreateEventA(NULL,FALSE,FALSE,(LPCSTR)this->GetCommandEvent().c_str());

	this->serviceCommunicationPort = 32920;
	this->serviceCommandPort = 32920;
	this->debugMode = this->debugValue;
	header = true;
	first = true;
	peerIsOnYatta = false;
	isPeerContacting = false;
	isPeerQuery = false;
	isPeerRunningServiceAction = false;
}

RemoteIslandSocket::RemoteIslandSocket(ISocketHandler& h, std::string ip, int port) : YattaConnectSocket(h)
{
	commandEvent = ::CreateEventA(NULL,FALSE,FALSE,(LPCSTR)this->GetCommandEvent().c_str());

	this->serviceCommunicationPort = port;
	this->serviceCommandPort = port;
	this->debugMode = this->debugValue;
	header = true;
	first = true;
	peerIsOnYatta = false;
	isPeerContacting = false;
	isPeerQuery = false;
	isPeerRunningServiceAction = false;
}

/*
void RemoteIslandSocket::OnRead()
{
	// OnRead of TcpSocket actually reads the data from the socket
	// and moves it to the input buffer (ibuf)
	TcpSocket::OnRead();
	// get number of bytes in input buffer
	size_t n = ibuf.GetLength();
	if (n > 0)
	{
		char tmp[RSIZE]; // <--- tmp's here
		ibuf.Read(tmp,n);
		printf("Read %d bytes:\n",n);
		for (size_t i = 0; i < n; i++)
		{
			printf("%c",isprint(tmp[i]) ? tmp[i] : '.');
		}
		printf("\n");
	}
}
*/
void RemoteIslandSocket::OnHeader(const std::string& key,const std::string& value)
{
	
	if(this->debugMode)
	{
		printf("OnHeader(): %s: %s\n",key.c_str(),value.c_str());
	}
	
	if (!strcasecmp(key.c_str(),"peerContentLength"))
	{
		SetPeerContentLength(value);
	}
	
	if (!strcasecmp(key.c_str(),"peerContact"))
	{
		SetPeerContactInfo(value);
	}

	if(!strcasecmp(key.c_str(),"peerQuery"))
	{
		SetPeerServiceRequest(value);
	}

	if(!strcasecmp(key.c_str(),"peerAction"))
	{
		SetPeerServiceAction(value);
	}

}	

void RemoteIslandSocket::OnRawData(const char *buf,size_t len)
{
	DEB(
	printf("RemoteIslandSocket(cpp)OnRaw\n");
	)
	
	if (!header)
	{
		OnData(buf, len);
		return;
	}
	for (size_t i = 0; i < len; i++)
	{
		if (!header)
		{
			OnData(buf + i,len - i);
			break;
		}
		switch (buf[i])
		{
		case 13: // ignore
			break;
		case 10: // end of line
			OnLine(line);
			line = "";
			break;
		default:
			line += buf[i];
		}
	}
	
}

void RemoteIslandSocket::OnLine(const std::string& line)
{
	if (first)
	{
		Parse pa(line);
		std::string str = pa.getword();
		
		if (str.substr(0,5) == "Yatta") // response
		{
			peerIsOnYatta = true;
		}
		else
		{
			peerIsOnYatta = false;
		}

		/*
		else // request
		{
			m_method = str;
			m_url = pa.getword();
			size_t spl = m_url.find("?");
			if (spl != std::string::npos)
			{
				m_uri = m_url.substr(0,spl);
				m_query_string = m_url.substr(spl + 1);
			}
			else
			{
				m_uri = m_url;
			}
			m_http_version = pa.getword();
			m_request = true;
		}
		*/
		first = false;
		OnFirst();
		return;
	}

	if (!line.size())
	{
		//SetLineProtocol(false);
		header = false;
		OnHeaderComplete();
		return;
	}

	Parse pa(line,":");
	std::string key = pa.getword();
	std::string value = pa.getrest();
	OnHeader(key,value);
	
}

void RemoteIslandSocket::OnData(const char * data,size_t size)
{
	DEB(
		printf("in OnData\n");
	)

	for(int i=0;i<size;i++)
	{
		this->peerData+= data[i];
	}

	if(this->debugMode)
	{
		printf("Data:%s\n",this->peerData.c_str());
		printf("Data Size:%d\n",size);
		printf("peerDataSize:%d\n",strlen(this->peerData.c_str()));
	}
	
	if(strlen(this->peerData.c_str()) >= this->GetPeerContentLength())
	{
		if(this->isPeerRunningServiceAction)
		{
			if(!strcasecmp(this->peerServiceAction.c_str(),"run"))
			{
				this->command = this->peerData;
			}

			if(!strcasecmp(this->peerServiceRequest.c_str(),"usage"))
			{
				SendQueryResponse();
			}

			/** detach if the action may take a while*/
			if (!Detach())
			{
				if(this->debugMode)
				{
					printf("Detach() failed\n");
				}

				peerCommandResult = "Service was unable to run peer action.";

				this->SendPeerActionResults();
			}
		}
	}
}


void RemoteIslandSocket::OnFirst()
{
	if(this->debugMode)
	{
		printf("in OnFirst\n");
	}
}

void RemoteIslandSocket::OnAccept()
{
	DEB(
		printf("Accept\n");
	)


	DEB(
		printf("RemoteAddress:%s\n",this->GetRemoteAddress().c_str());
	)
}

void RemoteIslandSocket::OnHeaderComplete()
{
	DEB(
		printf("in OnHeaderComplete\n");
	)

	/*
	if(this->GetForwardPort()==0)
	{
		if (!Detach())
		{
			DEB(printf("!Detach()\n");)
			SetCloseAndDelete();
		}
	}
	*/

	if(this->isPeerContacting)
	{
		if(this->debugMode)
		{
			printf("this is a peer contacting!\n");
		}

		SendContactMessage();
	}

	if(atoi(peerContentLength.c_str()) == 0)
	{
		if(this->debugMode)
		{
			printf("peer contentLen is 0\n");
		}

		if(this->isPeerRunningServiceAction)
		{
			if(this->debugMode)
			{
				printf("is peer running Action!\n");
			}

			this->RunPeerCommand();
		}

		if(this->isPeerQuery)
		{
			if(this->debugMode)
			{
				printf("this is a peer querying!\n");
			}

			if(!strcasecmp(this->peerServiceRequest.c_str(),"usage"))
			{
				SendQueryResponse();
			}
		}
	}
}

void RemoteIslandSocket::OnDetached()
{
	DEB(
		printf("in OnDetached\n");
	)

	if(this->debugMode)
	{
		printf("%s","about to handle request\n");
	}

	RunPeerCommand();
}


void RemoteIslandSocket::Init()
{
	DEB(
		printf("Init\n");
	)
}

void RemoteIslandSocket::SendContactMessage()
{
	char buffer[33];

	this->AddServiceHeader("serviceContentLength", itoa(this->GetServiceWelcome().length(),buffer,10));
	this->SendServiceResponse();
	this->Send(this->GetServiceWelcome());	
	this->header = true;
	this->isPeerContacting = false;
	this->isPeerQuery = false;
	this->isPeerRunningServiceAction = false;
	//this->SetCloseAndDelete();
}

void RemoteIslandSocket::SendServiceResponse()
{
	std::string msg;
	msg = "";
	for (string_m::iterator it = this->service_header.begin(); it != service_header.end(); it++)
	{
		std::string key = (*it).first;
		std::string val = (*it).second;
		msg += key + ": " + val + "\r\n";
	}
	msg += "\r\n";
	//msg += this->GetServiceUsage();
	Send( msg );
	this->header = true;
	this->isPeerContacting = false;
	this->isPeerQuery = false;
	this->isPeerRunningServiceAction = false;
	
}

void RemoteIslandSocket::SendQueryResponse()
{
	char buffer[33];

	this->AddServiceHeader("serviceContentLength", itoa(this->GetServiceUsage().length(),buffer,10));
	this->SendServiceResponse();
	this->Send(this->GetServiceUsage());	
	this->header = true;
	this->isPeerContacting = false;
	this->isPeerQuery = false;
	this->isPeerRunningServiceAction = false;
	//this->SetCloseAndDelete();
}

void RemoteIslandSocket::SendPeerActionResults()
{
	char buffer[33];

	this->AddServiceHeader("serviceContentLength", itoa(this->peerCommandResult.length(),buffer,10));
	this->SendServiceResponse();
	this->Send(this->peerCommandResult);
	this->header = true;
	this->isPeerContacting = false;
	this->isPeerQuery = false;
	this->isPeerRunningServiceAction = false;
}

void RemoteIslandSocket::RunPeerCommand()
{
	/* take data parse out command and */
	//ShellExecuteA(NULL, "open", serviceName.c_str(), command, NULL, SW_SHOWNORMAL);
	if(this->debugMode)
	{
		printf("RunPeerCommand:*%s*\n",this->command.c_str());
	}


	if(!strcasecmp(this->peerServiceAction.c_str(),"done"))
	{
		if(this->debugMode)
		{
			printf("peer is Done..\n");
		}

		this->peerCommandResult  = "Service Session Ended!";

		if(this->debugMode)
		{
			printf("after command result\n");
		}

		SendPeerActionResults();

		if(this->debugMode)
		{
			printf("after sending peer action results\n");
		}

		this->SetCloseAndDelete();
	
		if(this->debugMode)
		{
			printf("set close and delete\n");
		}
	}
	else
	{
		if(this->debugMode)
		{
			printf("actually running command:%s %s\n",this->GetServiceName().c_str(),command.c_str());
		}

		//ShellExecuteA(NULL, "open", "notepad", NULL, NULL, SW_SHOWNORMAL);
		
		ShellExecuteA(NULL, "open", this->GetServiceName().c_str(), command.c_str(), this->GetServiceRoot().c_str(), SW_SHOWNORMAL);
	
		/* this always returns failure
		if(::WaitForSingleObject(commandEvent, 45000) != WAIT_TIMEOUT)
		{
			peerCommandResult = "success";
		}
		else
		{
			peerCommandResult = "failure[no status after 45 seconds]";
		}
		*/

		peerCommandResult = "command executed,...";
		
		SendPeerActionResults();
		
	}
}

void RemoteIslandSocket::AddServiceHeader(const std::string& x,const std::string& y)
{
	service_header[x] = y;
}

void RemoteIslandSocket::SetPeerContentLength(std::string len)
{
	this->peerContentLength = len;
}

void RemoteIslandSocket::SetPeerServiceRequest(std::string peerRequest)
{
	this->peerServiceRequest = peerRequest;
	this->isPeerQuery = true;
}

void RemoteIslandSocket::SetPeerServiceAction(std::string action)
{
	this->peerServiceAction = action;
	this->isPeerRunningServiceAction = true;
}

void RemoteIslandSocket::SetPeerContactInfo(std::string info)
{
	this->peerContactInfo = info;
	this->isPeerContacting = true;
}

void RemoteIslandSocket::SetDebugMode(bool d)
{
	this->debugMode = d;
}

int RemoteIslandSocket::GetPeerContentLength()
{
	return atoi(this->peerContentLength.c_str());
}

std::string RemoteIslandSocket::GetIp()
{
	return this->serviceAddress;
}

port_t RemoteIslandSocket::GetPort()
{
	return this->serviceCommunicationPort;
}