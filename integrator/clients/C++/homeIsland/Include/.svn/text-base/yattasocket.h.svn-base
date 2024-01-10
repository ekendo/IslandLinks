#ifndef _YATTASOCKET_H
#define _YATTASOCKET_H

#include "Socket.h"

#ifdef SOCKETS_NAMESPACE
namespace SOCKETS_NAMESPACE {
#endif

	class SocketAddress;

	class YattaSocket : public Socket
	{

		public :
			
			/** Constructor */
			YattaSocket(ISocketHandler& );
	
			/** Destructor */
			~YattaSocket();
		
			/** Funny constructor */
			YattaSocket * Create();

			// Set Methods
			void SetWelcomeMessage(std::string serviceWelcomeMessage);
			void SetServiceName(std::string serviceName);
			void SetCommandEvent(std::string serviceEventName);
			void SetUsage(std::string serviceUsage);

			// Get Methods
			std::string GetServiceWelcome();
			std::string GetServiceName();
			std::string GetCommandEvent();
			std::string GetServiceUsage();
	
		private:
			std::string yattaWelcomeMessage;
			std::string yattaServiceName;
			std::string yattaEventName;
			std::string yattaServiceUsage;

	};


#ifdef SOCKETS_NAMESPACE
}
#endif

#endif // _YATTASOCKET_H