#include "DisplaySocket.h"
#include <Utility.h>
#include <string>

using namespace std;

// the constant TCP_BUFSIZE_READ is the maximum size of the standard input
// buffer of TcpSocket
#define RSIZE TCP_BUFSIZE_READ

DisplaySocket::DisplaySocket(ISocketHandler& h) : TcpSocket(h)
{
	proxyUrl = "";
}


void DisplaySocket::OnRead()
{
	// OnRead of TcpSocket actually reads the data from the socket
	// and moves it to the input buffer (ibuf)
	TcpSocket::OnRead();
	// get number of bytes in input buffer
	size_t n = ibuf.GetLength();
	if (n > 0)
	{
		char tmp[RSIZE]; // <--- tmp's here
		std::string HTTPHeader = "";
		ibuf.Read(tmp,n);
		printf("Read %d bytes:\n",n);
		for (size_t i = 0; i < n; i++)
		{
			HTTPHeader+=tmp[i];

			if((HTTPHeader.find("User-Agent:",0)!= std::string::npos )&&(strlen(this->proxyUrl.c_str())<1))
			{
				printf("%s",HTTPHeader.c_str());
				std::string::size_type beginUrlPos  = HTTPHeader.find("GET",0) + 18; 
				std::string beginString = HTTPHeader.substr(beginUrlPos);
				std::string::size_type endUrlPos = beginString.find("HTTP/",0);
				this->proxyUrl = beginString.substr(0,endUrlPos); 
				printf("%s",this->proxyUrl.c_str());
				//break;
			}
		}
		printf("\nDone\n");
	}
}

void DisplaySocket::OnAccept()
{
	printf("%s","in accept");
	Send("Local hostname : \n");
	Send("Local address : \n");
	Send("Number of sockets in list : \n");
	Send("\n");
	printf("%s","Accept done");
}