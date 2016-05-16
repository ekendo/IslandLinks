#include "YattaSocket.h"

YattaSocket::YattaSocket(ISocketHandler& h) : Socket(h)
{
	yattaWelcomeMessage = "";
	yattaServiceName = "";
	yattaEventName = "";
	yattaServiceUsage = "";
}

YattaSocket::~YattaSocket()
{

}

void YattaSocket::SetWelcomeMessage(std::string serviceWelcomeMessage)
{
	this->yattaWelcomeMessage = serviceWelcomeMessage;
}
		

void YattaSocket::SetServiceName(std::string serviceName)
{
	this->yattaServiceName = serviceName;
}
		
void YattaSocket::SetCommandEvent(std::string serviceEventName)
{
	this->yattaEventName = serviceEventName;
}
		
void YattaSocket::SetUsage(std::string serviceUsage)
{
	this->yattaServiceUsage = serviceUsage;
}

std::string YattaSocket::GetServiceWelcome()
{
	return this->yattaWelcomeMessage;
}

std::string YattaSocket::GetServiceName()
{
	return this->yattaServiceName;
}
			
std::string YattaSocket::GetCommandEvent()
{
	return this->yattaEventName;
}
			
std::string YattaSocket::GetServiceUsage()
{
	return this->yattaServiceUsage;
}