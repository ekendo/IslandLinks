#include <ISocketHandler.h>
#include <TcpSocket.h>
#include <HTTPSocket.h>
#include <process.h>

#ifdef SOCKETS_NAMESPACE
namespace SOCKETS_NAMESPACE {
#endif

class MQServerSocket : public HTTPSocket
{
public:

	MQServerSocket(ISocketHandler& h):HTTPSocket(h)
	{
		
	}

	~MQServerSocket()
	{
	
	}

	void SetHost(std::string host)
	{
		this->m_host = host;
	}

	void SetPort(int port)
	{
		this->m_port = port;
	}

	void Init()
	{
		m_tempQData = "";
		m_isOnQueue = false;
		m_isPing = false;
		this->m_debugValue = this->debugValue;

		/*
		winMutex = CreateMutexA( 
		NULL,                       // default security attributes
		FALSE,                      // initially not owned
		"CanWriteData");      // unnamed mutex
		*/
	}
		

	void OnDeQueue()
	{
		std::string actualData = "";

		if(m_debugValue)
		{
			printf("in the deQ\n");
		}

		if(m_dataQ.size()>0)
		{
			actualData = ((std::string) m_dataQ.front()).c_str();
		
			if(m_debugValue)
			{
				printf("Q-out value:%s\n", ((std::string) m_dataQ.front()).c_str());
			}

			m_dataQ.pop_front();
			
		}
		else
		{
			actualData = "Empty Q!";
		}
		
		this->SetStatus("200");
		this->SetStatusText("OK");
		this->SendResponse();
		this->Send(actualData);
		//SetCloseAndDelete();

		if(m_debugValue)
		{
			printf("just dequeued value\n");
		}
		
	}

	void OnEnQueue(std::string value)
	{
		if(m_debugValue)
		{
			printf("in the onQ\n");
		}

		this->SetStatus("200");
		this->SetStatusText("OK");
		this->SendResponse();
		this->Send("Q Succeeded");
		SetCloseAndDelete();

		if(m_debugValue)
		{
			printf("Q-in value:%s\n", value.c_str());
		}

		//if(::WaitForSingleObject(winMutex,INFINITE ) == WAIT_OBJECT_0)
		{
			m_dataQ.push_back(value);
		
		//	::ReleaseMutex(winMutex);
		}
	}

	void OnData(const char * data,size_t len)
	{
		//if(m_debugValue)
		{
			printf("MQ-in OnData\n");
			printf("before-QDataLen:%d-contentLen:%d\n",m_tempQData.length(),m_contentLen);
		}

		for (size_t i = 0; i < len; i++)
		{
			//m_tempQData += data;

			m_tempQData += data[i];

			if(m_tempQData.length() == m_contentLen)
			{
				break;
			}
			
		}

		//if(m_debugValue)
		{
			printf("before-QDataLen:%d-contentLen:%d\n",m_tempQData.length(),m_contentLen);
		}

		if(m_tempQData.length() >= m_contentLen)
		{
			OnEnQueue(m_tempQData);	
			m_tempQData = "";
		}
	}
	
	void OnDetached()
	{
	
	}

	void OnHeader(const std::string& key,const std::string& value)
	{
		if(this->m_debugValue)
		{
			printf("OnHeader-%s:%s\n",key.c_str(),value.c_str());
		}

		if (!strcasecmp(key.c_str(),"Enqueue"))
		{
			//OnEnQueue(value);
			m_isOnQueue = true;
			m_isPing = false;
			
			if(this->m_debugValue)
			{
				printf("just set Enqueued \n");
			}
		}

		if(!strcasecmp(key.c_str(),"Dequeue"))
		{
			m_isOnQueue = false;
			m_isPing = false;
		}

		if(!strcasecmp(key.c_str(),"Ping"))
		{
			m_isOnQueue = false;
			m_isPing = true;
		}

		if (!strcasecmp(key.c_str(),"content-length"))
		{
		
			m_contentLen = atoi(value.c_str());
		}
	}
	
	void OnHeaderComplete()
	{
		if(m_debugValue)
		{
			printf("onHeader Complete\n");
		}

		if(!m_isOnQueue)
		{
			if(!m_isPing)
			{
				OnDeQueue();
			}
			else
			{
				this->SetStatus("200");
				this->SetStatusText("OK");
				this->SendResponse();
				this->Send("ServerUp");
			}

			SetCloseAndDelete();
		}
	}

	void OnFirst()
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
	
	void OnAccept()
	{
		if(this->m_debugValue)
		{
			printf("Q Accept\n");
		}
	}

	static std::list<std::string> m_dataQ;

private:
	bool m_isPing;
	bool m_isOnQueue;
	bool m_debugValue;
	int m_port;
	int m_contentLen;
	std::string m_host;
	std::string m_portForwardData; 
	std::string m_tempQData;
	HANDLE winMutex;


};

std::list<std::string> MQServerSocket::m_dataQ;

#ifdef SOCKETS_NAMESPACE
}
#endif