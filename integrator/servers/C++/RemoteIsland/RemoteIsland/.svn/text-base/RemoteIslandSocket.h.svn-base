#include <YattaConnectSocket.h>
#include <ISocketHandler.h>
#include <list>
#include <map> 
#include <Parse.h>
#include <shellapi.h>
#include <process.h>

class RemoteIslandSocket : public YattaConnectSocket
{
	typedef std::map<std::string,std::string> string_m;

public:
	RemoteIslandSocket(ISocketHandler& );
	
	RemoteIslandSocket(ISocketHandler&, std::string ip);

	RemoteIslandSocket(ISocketHandler&, std::string ip, int port);

	void SendContactMessage();

	void SendQueryResponse();

	void SendServiceResponse();

	void SendPeerActionResults();

	void RunPeerCommand();

	//void OnRead();
	
	void OnHeader(const std::string& key,const std::string& value);

	void OnHeaderComplete();
	
	void OnRawData(const char *buf,size_t len);

	void OnData(const char * data,size_t size);

	void OnAccept();

	void Init();

	void OnFirst();

	void OnLine(const std::string& line);

	void OnDetached();

	void AddServiceHeader(const std::string& x,const std::string& y);

	void SetDebugMode(bool d);

	void SetPeerContentLength(std::string len);

	void SetPeerContactInfo(std::string info);

	void SetPeerServiceRequest(std::string peerRequest);

	void SetPeerServiceAction(std::string action);

	void SetWelcomeMessage(std::string welcome);

	void SetServiceName(std::string name);

	void SetUsage(std::string use);

	void SetCommandEvent(std::string ev);
	
	int GetPeerContentLength();

	std::string GetIp();

	port_t GetPort();

private:
	std::string line;
	std::string command;
	HANDLE commandEvent;
	std::string arguments;
	std::string serviceAddress;
	std::string peerContactInfo;
	std::string peerServiceRequest;
	std::string peerServiceAction;
	std::string peerContentLength;
	std::string peerData;
	std::string peerCommandResult;
	port_t serviceCommunicationPort;
	port_t serviceCommandPort;
	string_m service_header;
	bool debugMode;
	bool useDefaultPort;
	bool header;
	bool first;
	bool peerIsOnYatta;
	bool isPeerContacting;
	bool isPeerQuery;
	bool isPeerRunningServiceAction;

};