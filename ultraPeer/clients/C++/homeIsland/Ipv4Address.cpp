#include "Ipv4Address.h"
#include "Utility.h"
#include "Parse.h"


#ifdef SOCKETS_NAMESPACE
namespace SOCKETS_NAMESPACE {
#endif



Ipv4Address::Ipv4Address(port_t port) : m_valid(true)
{
	memset(&m_addr, 0, sizeof(m_addr));
	m_addr.sin_family = AF_INET;
	m_addr.sin_port = htons( port );
}


Ipv4Address::Ipv4Address(ipaddr_t a,port_t port) : m_valid(true)
{
	memset(&m_addr, 0, sizeof(m_addr));
	m_addr.sin_family = AF_INET;
	m_addr.sin_port = htons( port );
	memcpy(&m_addr.sin_addr, &a, sizeof(struct in_addr));
}


Ipv4Address::Ipv4Address(struct in_addr& a,port_t port) : m_valid(true)
{
	memset(&m_addr, 0, sizeof(m_addr));
	m_addr.sin_family = AF_INET;
	m_addr.sin_port = htons( port );
	m_addr.sin_addr = a;
}


Ipv4Address::Ipv4Address(const std::string& host,port_t port) : m_valid(false)
{
	memset(&m_addr, 0, sizeof(m_addr));
	m_addr.sin_family = AF_INET;
	m_addr.sin_port = htons( port );
	{
		ipaddr_t a;
		if (Utility::u2ip(host, a))
		{
			memcpy(&m_addr.sin_addr, &a, sizeof(struct in_addr));
			m_valid = true;
		}
	}
}


Ipv4Address::~Ipv4Address()
{
}


Ipv4Address::operator struct sockaddr *()
{
	return (struct sockaddr *)&m_addr;
}


Ipv4Address::operator socklen_t()
{
	return sizeof(struct sockaddr_in);
}


void Ipv4Address::SetPort(port_t port)
{
	m_addr.sin_port = htons( port );
}


port_t Ipv4Address::GetPort()
{
	return ntohs( m_addr.sin_port );
}


bool Ipv4Address::Resolve(const std::string& hostname,struct in_addr& a)
{
	if (Utility::isipv4(hostname))
	{
		Parse pa((char *)hostname.c_str(), ".");
		union {
			struct {
				unsigned char b1;
				unsigned char b2;
				unsigned char b3;
				unsigned char b4;
			} a;
			ipaddr_t l;
		} u;
		u.a.b1 = static_cast<unsigned char>(pa.getvalue());
		u.a.b2 = static_cast<unsigned char>(pa.getvalue());
		u.a.b3 = static_cast<unsigned char>(pa.getvalue());
		u.a.b4 = static_cast<unsigned char>(pa.getvalue());
		memcpy(&a, &u.l, sizeof(struct in_addr));
		return true;
	}
#ifndef LINUX
	struct hostent *he = gethostbyname( hostname.c_str() );
	if (!he)
	{
		return false;
	}
	memcpy(&a, he -> h_addr, sizeof(struct in_addr));
#else
	struct hostent he;
	struct hostent *result;
	int myerrno;
	char buf[2000];
	int n = gethostbyname_r(hostname.c_str(), &he, buf, sizeof(buf), &result, &myerrno);
	if (n)
	{
		return false;
	}
	memcpy(&a, he.h_addr, sizeof(struct in_addr));
#endif
	return true;
}


bool Ipv4Address::Reverse(struct in_addr& a,std::string& name)
{
	struct hostent *h = gethostbyaddr( (const char *)&a, sizeof(a), AF_INET);
	if (h)
	{
		name = h -> h_name;
		return true;
	}
	return false;
}


std::string Ipv4Address::Convert(bool include_port)
{
	if (include_port)
		return Convert(m_addr.sin_addr) + ":" + Utility::l2string(GetPort());
	return Convert(m_addr.sin_addr);
}


std::string Ipv4Address::Convert(struct in_addr& a)
{
	union {
		struct {
			unsigned char b1;
			unsigned char b2;
			unsigned char b3;
			unsigned char b4;
		} a;
		ipaddr_t l;
	} u;
	memcpy(&u.l, &a, sizeof(struct in_addr));
	char tmp[100];
	sprintf(tmp, "%u.%u.%u.%u", u.a.b1, u.a.b2, u.a.b3, u.a.b4);
	return tmp;
}


void Ipv4Address::SetAddress(struct sockaddr *sa)
{
	memcpy(&m_addr, sa, sizeof(struct sockaddr_in));
}


int Ipv4Address::GetFamily()
{
	return m_addr.sin_family;
}


bool Ipv4Address::IsValid()
{
	return m_valid;
}


bool Ipv4Address::operator==(SocketAddress& a)
{
	if (a.GetFamily() != GetFamily())
		return false;
	if ((socklen_t)a != sizeof(m_addr))
		return false;
	struct sockaddr *sa = a;
	struct sockaddr_in *p = (struct sockaddr_in *)sa;
	if (p -> sin_port != m_addr.sin_port)
		return false;
	if (memcmp(&p -> sin_addr, &m_addr.sin_addr, 4))
		return false;
	return true;
}


SocketAddress *Ipv4Address::GetCopy()
{
	Ipv4Address *p = new Ipv4Address(m_addr.sin_addr, ntohs(m_addr.sin_port));
	return p;
}


std::string Ipv4Address::Reverse()
{
	std::string tmp;
	Reverse(m_addr.sin_addr, tmp);
	return tmp;
}


#ifdef SOCKETS_NAMESPACE
} // namespace SOCKETS_NAMESPACE {
#endif
