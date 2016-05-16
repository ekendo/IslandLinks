#include <map>


class Hit
{

public:

	Hit(std::string host, std::string protocol, std::string method, 
		std::string url, std::string mimeType, std::string session, 
		std::string contents, std::string req, std::string res, 
		std::string postData, std::string sourceIp, std::string destIp,
		int requestSize, int responseSize, double responseTime)
	{
		Host = host;
		Protocol = protocol;
		Method = method;
		Url = url;
		MimeType = mimeType;
		ResponseTime = responseTime;
		Session = session;
		Contents = contents;
		RequestHeader = req;
		ResponseHeader = res;
		PostData = postData;
		SourceIPAddress = sourceIp;
		DestinationIPAddress = destIp;
		RequestSize = requestSize;
		ResponseSize = responseSize;
	}
		
	std::string Host;
	std::string Protocol;
	std::string Method;
	std::string Url;
	std::string MimeType;
	std::string Session;
	std::string Contents;
	std::string RequestHeader;
	std::string ResponseHeader;
	std::string PostData;
	int RequestSize;
	int ResponseSize;
	std::string SourceIPAddress;
	std::string DestinationIPAddress;
	double ResponseTime;
};