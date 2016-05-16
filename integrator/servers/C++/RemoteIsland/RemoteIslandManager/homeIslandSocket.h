#include <YattaConnectSocket.h>
#include <ISocketHandler.h>
#include <list>
#include <map> 

class HomeIslandSocket : public YattaConnectSocket
{
	/** map to hold island header values. */
	typedef std::map<std::string,std::string> string_m;

public:

	enum inputType { GUI, SHELL, MEMORY, ROBOT };

	HomeIslandSocket(ISocketHandler& );
	
	HomeIslandSocket(ISocketHandler&, std::string ip);

	HomeIslandSocket(ISocketHandler&, std::string ip, int port);

	~HomeIslandSocket();

	void OnConnect();

	void OnRawData(const char *buf,size_t len);

	void OnData(const char * data,size_t size);

	void OnLine(const std::string& line);

	void OnHeader(const std::string& key,const std::string& value);

	void OnHeaderComplete();

	void ContactSpecificService();

	void SendPeerCommand();

	void SendPeerQuery();

	void SendPeerContactInfo();

	void AddPeerHeader(const std::string& x,const std::string& y);
	
	void AddPeerHeader(const std::string& header, char *format);

	void SetDebugMode(bool d);

	void SetUserName(std::string u);

	void SetTransationContentLength(std::string len);

	void SetInputType(inputType inType);

	void SetPeerInput(std::string in);

	// Gets ,...
	std::string GetServiceMessage();

	bool NextMoveNeeded();

	bool SessionHasEnded();

	std::string GetTransactionContentLength();

	std::string GetIp();

	port_t GetPort();

private:
	std::string rawLine;
	std::string command;
	std::string arguments;
	std::string serviceAddress;
	std::string peerHeader;
	std::string transactionContentLength;
	std::string peerUser;
	std::string serviceData;
	port_t serviceCommunicationPort;
	port_t serviceCommandPort;
	bool debugMode;
	bool useDefaultPort;
	bool header;
	bool getNextMove;
	bool isPeerQuery;
	bool sessionIsEnded;
	string_m peer_header;
	inputType peerInputType;


};