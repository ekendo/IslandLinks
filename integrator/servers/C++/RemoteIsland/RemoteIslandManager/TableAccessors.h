//*************************************************************************************
//			AFFILIATE TABLE ACCESSOR
//*************************************************************************************

#define DBINITCONSTANTS
#include <atlcom.h>
#include <atldbcli.h>
#include <msdaguid.h>

class CAffiliateAccessor
{
public:
	CHAR m_AffiliateUrl[500];
	CHAR m_Keywords[8000];

BEGIN_COLUMN_MAP(CAffiliateAccessor)
	COLUMN_ENTRY(1, m_AffiliateUrl)
	COLUMN_ENTRY(2, m_Keywords)
END_COLUMN_MAP()

};
