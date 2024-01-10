#include <ISocketHandler.h>
#include <TcpSocket.h>
//#include "HTTPServerSocket.h"

#ifdef SOCKETS_NAMESPACE
namespace SOCKETS_NAMESPACE {
#endif

class HTTPServerSocket;

class PortForwardSocket : public TcpSocket
{
public:

	PortForwardSocket(ISocketHandler& h, int inBuf, int outBuf);	
	void OnRawData(const char *p,size_t l);
	void DoPortForward(std::string host, int port);
	void OnConnect();
	void SetHost(std::string host);
	void OnDelete();
	void SetPort(int port);
	void SetRemote(HTTPServerSocket* p); 
	void SetSourceIp(std::string value);

private:
	bool m_debugMode;
	bool m_sourceIpSet;
	std::string m_host;
	std::string m_portForwardData; 
	std::string m_sourceIp;
	int m_port;
	HTTPServerSocket *m_remote;


};

#ifdef SOCKETS_NAMESPACE
}
#endif