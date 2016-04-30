#ifndef _IPV4ADDRESS_H
#define _IPV4ADDRESS_H

#include "SocketAddress.h"


#ifdef SOCKETS_NAMESPACE
namespace SOCKETS_NAMESPACE {
#endif



class Ipv4Address : public SocketAddress
{
public:
	/** Create empty Ipv4 address structure.
		\param port Port number */
	Ipv4Address(port_t port = 0);
	/** Create Ipv4 address structure.
		\param a Socket address in network byte order (as returned by Utility::u2ip)
		\param port Port number in host byte order */
	Ipv4Address(ipaddr_t a,port_t port);
	/** Create Ipv4 address structure.
		\param a Socket address in network byte order
		\param port Port number in host byte order */
	Ipv4Address(struct in_addr& a,port_t port);
	/** Create Ipv4 address structure.
		\param host Hostname to be resolved
		\param port Port number in host byte order */
	Ipv4Address(const std::string& host,port_t port);
	~Ipv4Address();

	// SocketAddress implementation

	operator struct sockaddr *();
	operator socklen_t();
	bool operator==(SocketAddress&);

	void SetPort(port_t port);
	port_t GetPort();

	void SetAddress(struct sockaddr *sa);
	int GetFamily();

	bool IsValid();
	SocketAddress *GetCopy();

	/** Convert address struct to text. */
	std::string Convert(bool include_port = false);
	std::string Reverse();

	/** Resolve hostname. */
static	bool Resolve(const std::string& hostname,struct in_addr& a);
	/** Reverse resolve (IP to hostname). */
static	bool Reverse(struct in_addr& a,std::string& name);
	/** Convert address struct to text. */
static	std::string Convert(struct in_addr& a);

private:
	Ipv4Address(const Ipv4Address& ) {} // copy constructor
	Ipv4Address& operator=(const Ipv4Address& ) { return *this; } // assignment operator
	struct sockaddr_in m_addr;
	bool m_valid;
};




#ifdef SOCKETS_NAMESPACE
} // namespace SOCKETS_NAMESPACE {
#endif
#endif // _IPV4ADDRESS_H
