#include "HTTPServerSocket.h"
#include "PortForwardSocket.h"
#include "SocketAddress.h"
#include <process.h>
#include <Lock.h>

#ifdef _DEBUG
#define DEB(x) x
#else
#define DEB(x) 
#endif

	PortForwardSocket::PortForwardSocket(ISocketHandler& h, int inBuf, int outBuf):TcpSocket(h, inBuf, outBuf)
	{
		//this->m_httpResponse = "";
		m_sourceIpSet = false;
	}

	
	void PortForwardSocket::OnRawData(const char *p,size_t l)
	{
		
		//printf("PortForwrd-OnRawData\n");

		if (Handler().Valid(this->m_remote))
		{
			this->m_remote -> SendBuf(p, l);
		}
		else
		{
			Handler().LogError(this, "OnRawData", 0, "m_remote not valid");
		}
		
	}
	

	void PortForwardSocket::DoPortForward(std::string host, int port)
	{

		if (!Open(host,port))
		{
			if (!Connecting())
			{
				Handler().LogError(this, "PortForwardSocket", -1, "connect() failed miserably", LOG_LEVEL_FATAL);
				
				if(this->m_debugMode)
				{
					printf("connect() failed\n");
				}

				SetCloseAndDelete();
			}
		}

		this->m_port = port;
		this->m_host = host;
	}

	void PortForwardSocket::OnConnect()
	{
		if(this->m_debugMode)
		{
			//printf("portforward onconnect\n");
		}

		if (Handler().Valid(m_remote))
		{
			if (m_remote -> tmpl)
			{
				if(!m_sourceIpSet)
				{
					printf("PF-RemoteAddy:%s\n",m_sourceIp.c_str());
					//SendBuf(m_sourceIp.c_str(),m_sourceIp.size());
					m_sourceIpSet = true;
				}

				//printf("Sending %d early bytes\n",m_remote -> tmpl);
				SendBuf(m_remote -> tmps, m_remote -> tmpl);
				m_remote -> tmpl = 0;
			}
			else
			{
				//printf("!remote tmpl\n");
			}
		}
		else
		{
			if(!m_sourceIpSet)
			{
				printf("PF-RemoteAddy:%s\n",m_sourceIp.c_str());
				//SendBuf(m_sourceIp.c_str(),m_sourceIp.size());
				m_sourceIpSet = true;
			}

			//printf("m_remote ! valid\n");
			
		}
		
	}

	void PortForwardSocket::SetHost(std::string host)
	{
		this->m_host = host;
	}

	void PortForwardSocket::OnDelete()
	{
		//printf("OnDelete\n");

		if (Handler().Valid(m_remote))
		{
			m_remote -> SetCloseAndDelete();
		}
		
	}

	void PortForwardSocket::SetPort(int port)
	{
		this->m_port = port;
	}

	void PortForwardSocket::SetRemote(HTTPServerSocket* p) 
	{ 
		m_remote = p; 
	}

	void PortForwardSocket::SetSourceIp(std::string value)
	{
		m_sourceIp = "sourceIP:";
		m_sourceIp += value;
		m_sourceIp += "\n";

		printf("sourceIP Set!%s\n",m_sourceIp.c_str());
	}