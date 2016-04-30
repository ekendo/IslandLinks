#include "Ipv6Address.h"
#include "Utility.h"
#include "Parse.h"
#ifdef IPPROTO_IPV6

#ifdef SOCKETS_NAMESPACE
namespace SOCKETS_NAMESPACE {
#endif


Ipv6Address::Ipv6Address(port_t port) : m_valid(true)
{
	memset(&m_addr, 0, sizeof(m_addr));
	m_addr.sin6_family = AF_INET6;
	m_addr.sin6_port = htons( port );
}


Ipv6Address::Ipv6Address(struct in6_addr& a,port_t port) : m_valid(true)
{
	memset(&m_addr, 0, sizeof(m_addr));
	m_addr.sin6_family = AF_INET6;
	m_addr.sin6_port = htons( port );
	m_addr.sin6_addr = a;
}


Ipv6Address::Ipv6Address(const std::string& host,port_t port) : m_valid(false)
{
	memset(&m_addr, 0, sizeof(m_addr));
	m_addr.sin6_family = AF_INET6;
	m_addr.sin6_port = htons( port );
	{
		struct in6_addr a;
		if (Utility::u2ip(host, a))
		{
			m_addr.sin6_addr = a;
			m_valid = true;
		}
	}
}


Ipv6Address::~Ipv6Address()
{
}


Ipv6Address::operator struct sockaddr *()
{
	return (struct sockaddr *)&m_addr;
}


Ipv6Address::operator socklen_t()
{
	return sizeof(struct sockaddr_in6);
}


void Ipv6Address::SetPort(port_t port)
{
	m_addr.sin6_port = htons( port );
}


port_t Ipv6Address::GetPort()
{
	return ntohs( m_addr.sin6_port );
}


bool Ipv6Address::Resolve(const std::string& hostname,struct in6_addr& a)
{
	if (Utility::isipv6(hostname))
	{
		std::list<std::string> vec;
		size_t x = 0;
		for (size_t i = 0; i <= hostname.size(); i++)
		{
			if (i == hostname.size() || hostname[i] == ':')
			{
				std::string s = hostname.substr(x, i - x);
				//
				if (strstr(s.c_str(),".")) // x.x.x.x
				{
					Parse pa(s,".");
					char slask[100]; // u2ip temporary hex2string conversion
					unsigned long b0 = static_cast<unsigned long>(pa.getvalue());
					unsigned long b1 = static_cast<unsigned long>(pa.getvalue());
					unsigned long b2 = static_cast<unsigned long>(pa.getvalue());
					unsigned long b3 = static_cast<unsigned long>(pa.getvalue());
					sprintf(slask,"%lx",b0 * 256 + b1);
					vec.push_back(slask);
					sprintf(slask,"%lx",b2 * 256 + b3);
					vec.push_back(slask);
				}
				else
				{
					vec.push_back(s);
				}
				//
				x = i + 1;
			}
		}
		size_t sz = vec.size(); // number of byte pairs
		size_t i = 0; // index in in6_addr.in6_u.u6_addr16[] ( 0 .. 7 )
		for (std::list<std::string>::iterator it = vec.begin(); it != vec.end(); it++)
		{
			std::string bytepair = *it;
			if (bytepair.size())
			{
				a.s6_addr16[i++] = htons(Utility::hex2unsigned(bytepair));
			}
			else
			{
				a.s6_addr16[i++] = 0;
				while (sz++ < 8)
				{
					a.s6_addr16[i++] = 0;
				}
			}
		}
		return true;
	}
#ifdef SOLARIS
	int errnum = 0;
	struct hostent *he = getipnodebyname( hostname.c_str(), AF_INET6, 0, &errnum );
#else
	struct hostent *he = gethostbyname2( hostname.c_str(), AF_INET6 );
#endif
	if (!he)
	{
		return false;
	}
	memcpy(&a,he -> h_addr_list[0],he -> h_length);
#ifdef SOLARIS
	free(he);
#endif
	return true;
}


bool Ipv6Address::Reverse(struct in6_addr& a,std::string& name)
{
	/// \todo implement ipv6 reverse lookup
	struct hostent *h = gethostbyaddr( (const char *)&a, sizeof(a), AF_INET6);
	if (h)
	{
		name = h -> h_name;
		return true;
	}
	return false;
}


std::string Ipv6Address::Convert(bool include_port)
{
	if (include_port)
		return Convert(m_addr.sin6_addr) + ":" + Utility::l2string(GetPort());
	return Convert(m_addr.sin6_addr);
}


std::string Ipv6Address::Convert(struct in6_addr& a,bool mixed)
{
	char slask[100]; // l2ip temporary
	*slask = 0;
	unsigned int prev = 0;
	bool skipped = false;
	bool ok_to_skip = true;
	if (mixed)
	{
		unsigned int x;
		for (size_t i = 0; i < 6; i++)
		{
			x = ntohs(a.s6_addr16[i]);
			if (*slask && (x || !ok_to_skip || prev))
				strcat(slask,":");
			if (x || !ok_to_skip)
			{
				sprintf(slask + strlen(slask),"%X", x);
				if (x && skipped)
					ok_to_skip = false;
			}
			else
			{
				skipped = true;
			}
			prev = x;
		}
		x = ntohs(a.s6_addr16[6]);
		sprintf(slask + strlen(slask),":%u.%u",x / 256,x & 255);
		x = ntohs(a.s6_addr16[7]);
		sprintf(slask + strlen(slask),".%u.%u",x / 256,x & 255);
	}
	else
	{
		for (size_t i = 0; i < 8; i++)
		{
			unsigned int x = ntohs(a.s6_addr16[i]);
			if (*slask && (x || !ok_to_skip || prev))
				strcat(slask,":");
			if (x || !ok_to_skip)
			{
				sprintf(slask + strlen(slask),"%X", x);
				if (x && skipped)
					ok_to_skip = false;
			}
			else
			{
				skipped = true;
			}
			prev = x;
		}
	}
	return slask;
}


void Ipv6Address::SetAddress(struct sockaddr *sa)
{
	memcpy(&m_addr, sa, sizeof(struct sockaddr_in6));
}


int Ipv6Address::GetFamily()
{
	return m_addr.sin6_family;
}


void Ipv6Address::SetFlowinfo(uint32_t x)
{
	m_addr.sin6_flowinfo = x;
}


uint32_t Ipv6Address::GetFlowinfo()
{
	return m_addr.sin6_flowinfo;
}


void Ipv6Address::SetScopeId(uint32_t x)
{
	m_addr.sin6_scope_id = x;
}


uint32_t Ipv6Address::GetScopeId()
{
	return m_addr.sin6_scope_id;
}


bool Ipv6Address::IsValid()
{
	return m_valid;
}


bool Ipv6Address::operator==(SocketAddress& a)
{
	if (a.GetFamily() != GetFamily())
		return false;
	if ((socklen_t)a != sizeof(m_addr))
		return false;
	struct sockaddr *sa = a;
	struct sockaddr_in6 *p = (struct sockaddr_in6 *)sa;
	if (p -> sin6_port != m_addr.sin6_port)
		return false;
	if (memcmp(&p -> sin6_addr, &m_addr.sin6_addr, sizeof(struct in6_addr)))
		return false;
	return true;
}


SocketAddress *Ipv6Address::GetCopy()
{
	Ipv6Address *p = new Ipv6Address(m_addr.sin6_addr, ntohs(m_addr.sin6_port));
	p -> SetFlowinfo(GetFlowinfo());
	p -> SetScopeId(GetScopeId());
	return p;
}


std::string Ipv6Address::Reverse()
{
	std::string tmp;
	Reverse(m_addr.sin6_addr, tmp);
	return tmp;
}


#ifdef SOCKETS_NAMESPACE
} // namespace SOCKETS_NAMESPACE {
#endif
#endif // IPPROTO_IPV6
