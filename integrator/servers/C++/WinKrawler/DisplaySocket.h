#include <TcpSocket.h>
#include <ISocketHandler.h>

class DisplaySocket : public TcpSocket
{
	public:
		DisplaySocket(ISocketHandler& );
		
		void OnRead();
		
		void OnAccept();

		std::string proxyUrl;
};