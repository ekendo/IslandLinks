#include "homeIslandSocket.h"
#include <stdarg.h>
#include <Parse.h>

#ifndef _WIN32
/**
 * C++ version char* style "itoa":	
 */
char* itoa( int value, char* result, int base ) {
	
	// check that the base if valid
	
	if (base < 2 || base > 16) { *result = 0; return result; }
	

	
	char* out = result;
	
	int quotient = value;
	

	
	do {
	
		*out = "0123456789abcdef"[ std::abs( quotient % base ) ];
	
		++out;
	
		quotient /= base;
	
	} while ( quotient );
	

	
	// Only apply negative sign for base 10
	
	if ( value < 0 && base == 10) *out++ = '-';
	

	
	std::reverse( result, out );
	
	*out = 0;
	
	return result;
	
}

#endif

#define RSIZE TCP_BUFSIZE_READ

#ifdef _DEBUG
#define DEB(x) x
#else
#define DEB(x) 
#endif

HomeIslandSocket::HomeIslandSocket(ISocketHandler& h) : YattaConnectSocket(h)
{
	this->serviceCommunicationPort = 32920;
	this->serviceCommandPort = 32920;
	this->header = true;
	this->getNextMove = false;
	this->sessionIsEnded = false;
	this->rawLine = "";
	this->command = "";
}

HomeIslandSocket::HomeIslandSocket(ISocketHandler& h, std::string ip) : YattaConnectSocket(h)
{
	this->serviceCommunicationPort = 32920;
	this->serviceCommandPort = 32920;
	this->serviceAddress = ip;
	this->header = true;
	this->getNextMove = false;
	this->sessionIsEnded = false;
	this->rawLine = "";
	this->command = "";
}

HomeIslandSocket::HomeIslandSocket(ISocketHandler& h, std::string ip, int port) : YattaConnectSocket(h)
{
	this->serviceCommunicationPort = port;
	this->serviceCommandPort = port;
	this->serviceAddress = ip;
	this->header = true;
	this->getNextMove = false;
	this->sessionIsEnded = false;
	this->rawLine = "";
	this->command = "";
}

HomeIslandSocket::~HomeIslandSocket()
{
	
}

void HomeIslandSocket::ContactSpecificService()
{
	if(this->debugMode)
	{
		printf("DEBUG:in Contact Specific Service\n");
	}

	if (!Open(GetIp(),GetPort()))
	{
		if (!Connecting())
		{
			Handler().LogError(this, "HomeIslandSocket", -1, "connect() failed miserably", LOG_LEVEL_FATAL);
				
			if(this->debugMode)
			{
				printf("connect() failed");
			}

			SetCloseAndDelete();
		}
	}	
}

void HomeIslandSocket::AddPeerHeader(const std::string& x,const std::string& y)
{
	peer_header[x] = y;
}

void HomeIslandSocket::AddPeerHeader(const std::string& header, char *format,...)
{
	char slask[5000]; // temporary for vsprintf / vsnprintf
	va_list ap;
	va_start(ap, format);
#ifdef _WIN32
	vsprintf(slask, format, ap);
#else
	vsnprintf(slask, 5000, format, ap);
#endif
	va_end(ap);

	peer_header[header] = slask;
}

void HomeIslandSocket::OnConnect()
{
	char buffer [33];

	if(this->debugMode)
	{
		printf("homeIsland-onConnect\n");
		printf("homeIslandIP:%s\n",this->GetIp().c_str());
	}

		AddPeerHeader("peerContact",peerUser);
		AddPeerHeader("peerContentLength",GetTransactionContentLength());

		/* later for all the wanys we can talk to the service
		if(GetMethod() == "POST")
		{
			do stuff based on the protocol
		}
		*/

	if(this->debugMode)
	{
		printf("about to send Peer Contact Info\n");
	}
		SendPeerContactInfo();

	if(this->debugMode)
	{
		printf("sent Peer Contact Info\n");
	}
}

void HomeIslandSocket::OnRawData(const char *buf,size_t len)
{
	if(this->debugMode)
	{
		printf("HomeIslandSocket(cpp)OnRaw\n");
	}

	if (!header)
	{
		if(this->debugMode)
		{
			printf("!header calling ondata[outside for loop]\n");
		}

		OnData(buf, len);
		return;
	}

	// If the last command was "done"
	//if(strcmp(this->command.c_str(),"done")!=0)
	{
		for (size_t i = 0; i < len; i++)
		{
			if (!header)
			{
				if(this->debugMode)
				{
					printf("!header calling ondata[inside for loop]\n");
				}

				OnData(buf + i,len - i);
				break;
			}
			switch (buf[i])
			{
			case 13: // ignore
				break;
			case 10: // end of line
				OnLine(m_line);
				this->m_line = "";
				break;
			default:
				this->m_line += buf[i];
				//this->rawLine += buf[i];
			}
		}
	}
	
}

void HomeIslandSocket::OnData(const char * data,size_t size)
{
	DEB(
		printf("in OnData\n");
	)

	for(int i=0;i<size;i++)
	{
		this->serviceData+= data[i];
	}

	if(this->debugMode)
	{
		printf("Data:%s\n",this->serviceData.c_str());
		printf("Data Size:%d\n",size);
		printf("peerDataSize:%d\n",strlen(this->serviceData.c_str()));
		printf("contentLength:%d\n",atoi(this->GetTransactionContentLength().c_str()));
	}
	
	if(this->serviceData.length() >= atoi(this->GetTransactionContentLength().c_str()))
	{
		if(this->debugMode)
		{
			printf("need peer input!\n");
		}

		if(!strcasecmp(this->serviceData.c_str(),"Service Session Ended!"))
		{
			sessionIsEnded = true;
			this->getNextMove = true;
			#ifdef _WIN32
			GenerateConsoleCtrlEvent(CTRL_C_EVENT,0);
			#endif
		}

		// Get Input/Next step...
		if(this->peerInputType == this->SHELL)
		{
			getNextMove = true;
		}
	}
}


void HomeIslandSocket::OnLine(const std::string& line)
{
	if(this->debugMode)
	{
		printf("in OnLine with Line:%s\n", line.c_str());	
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

void HomeIslandSocket::OnHeader(const std::string &key, const std::string &value)
{
	if(this->debugMode)
	{
		printf("OnHeader(): %s: %s\n",key.c_str(),value.c_str());
	}
	
	if (!strcasecmp(key.c_str(),"serviceContentLength"))
	{
		SetTransationContentLength(value);
	}

}

void HomeIslandSocket::OnHeaderComplete()
{
	DEB(
		printf("in OnHeaderComplete\n");
	)
}

void HomeIslandSocket::SendPeerCommand()
{
	std::string msg = "";

	if(this->isPeerQuery)
	{
		msg += "peerQuery:";
		msg += this->command;
		msg += "\r\n";
	}
	else
	{
		msg += "peerAction:";
		msg += this->command;
		msg += "\r\n";
	}

	msg += "peerContentLength:";
	msg += this->transactionContentLength;
	msg += "\r\n";

	msg += "\r\n";
	peerHeader = msg;

	msg += this->arguments;

	Send( msg );

	if(this->debugMode)
	{
		printf("peerCommand sent:%s\n",msg.c_str());
	}

	this->serviceData = "";
	this->header = true;
}

void HomeIslandSocket::SendPeerContactInfo()
{
	if(this->debugMode)
	{
		printf("sending Peer Contact Info\n");
	}

	std::string msg;
	msg = "Yatta\r\n";
	for (string_m::iterator it = peer_header.begin(); it != peer_header.end(); it++)
	{
		std::string key = (*it).first;
		std::string val = (*it).second;
		msg += key + ": " + val + "\r\n";
	}
	msg += "\r\n";
	peerHeader = msg;

	if(this->debugMode)
	{
		printf("calling send\n");
	}
	Send( msg );

	this->serviceData = "";
	this->header = true;

	
}

void HomeIslandSocket::SetTransationContentLength(std::string len)
{
	this->transactionContentLength = len;
}

void HomeIslandSocket::SetUserName(std::string u)
{
	this->peerUser = u;
}

void HomeIslandSocket::SetDebugMode(bool d)
{
	this->debugMode = d;
}

void HomeIslandSocket::SetInputType(inputType inType)
{
	this->peerInputType = inType;
}

void HomeIslandSocket::SetPeerInput(std::string in)
{
	this->serviceData = "";
	this->getNextMove = false;
	this->command = in;

	if(this->debugMode)
	{
		printf("user input:*%s*\n",this->command.c_str());
	}

	if((strcasecmp(this->command.c_str(),"done")!=0) && (strcasecmp(this->command.c_str(),"usage")!=0))
	{
		if(this->debugMode)
		{
			printf("niether done or usage\n");
		}

		this->arguments = this->command;
		this->command = "run";
		this->isPeerQuery = false;
	}

	if(!strcasecmp(this->command.c_str(),"done"))
	{
		if(this->debugMode)
		{
			printf("user is done\n");
		}

		this->arguments = "";
		this->isPeerQuery = false;
	}

	if(!strcasecmp(this->command.c_str(),"usage"))
	{
		if(this->debugMode)
		{
			printf("user wants usage\n");
		}

		this->arguments = "";
		this->isPeerQuery = true;
	}

	char buffer[33];
	this->transactionContentLength = itoa(this->arguments.length(),buffer,10);

	SendPeerCommand();
}


std::string HomeIslandSocket::GetServiceMessage()
{
	return this->serviceData;
}

bool HomeIslandSocket::NextMoveNeeded()
{
	return this->getNextMove;
}

bool HomeIslandSocket::SessionHasEnded()
{
	return this->sessionIsEnded;
}

std::string HomeIslandSocket::GetTransactionContentLength()
{
	return this->transactionContentLength;
}

std::string HomeIslandSocket::GetIp()
{
	return this->serviceAddress;
}

port_t HomeIslandSocket::GetPort()
{
	return this->serviceCommunicationPort;
}
