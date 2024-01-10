#include "ServerHandler.h"
#include "HttpKrawlSocket.h"
#include <SocketHandler.h>
#include <Parse.h>
#include <stdlib.h>
#include <fstream>
#include <iostream>
#include <list>
#include <time.h>

#ifdef _DEBUG
#define DEB(x) x
#else
#define DEB(x)
#endif

static std::string CrawlUrls = "";
static std::string Cookie = "";
static std::string KrawlerIp = "";
static std::string SiteIP = "";
static std::string KrawlerOperator = "";
static std::string KrawlerHostName = "";
static std::string SiteHostName = "";
static bool UseConfigUrls = true;
static int SpiderLevelSetting = 0;
HANDLE kaptureEvent;
ofstream logfile ( "krawlerLog.txt" );
char dateStr [9];
char timeStr [9];

std::list<std::string> CreateSiteList(std::string content, std::string currentUrl, std::string host, std::string protocol, int currentSpiderLevel)
{
	std::string wwwHost = "www.";
	std::string domainHost = "";
	std::string hardLink = protocol.c_str();
	std::string hostHardLink = protocol.c_str();
	std::list<std::string> urlsToKrawl;

	if(host.find(wwwHost,0)==std::string::npos)
	{
		wwwHost += host;
		hardLink += "://";
		hardLink += wwwHost.c_str();

		domainHost = host;
	}
	else
	{
		hardLink += "://";
		hardLink += host.c_str();

		hostHardLink += "://";
		hostHardLink += host.c_str();

		Parse pa(host,"www.");
		domainHost = pa.getrest();

		std::string::size_type lastSlash = currentUrl.find_last_of("\/");

		if(lastSlash!=std::string::npos)
		{
			hardLink = currentUrl.substr(0,lastSlash);
		}

	}
	DEB(
		printf("this is the newHardLink:%s\n",hardLink.c_str());
		)

	std::string::size_type hrefPos = content.find("href=\"",0);
	std::string::size_type domainPos = content.find(domainHost,0);

	if(currentSpiderLevel <= SpiderLevelSetting)
	{
		DEB(
			printf("currentSpiderLevel:%d\n",currentSpiderLevel);
		)

		// replace relative links
		while(hrefPos != std::string::npos)
		{
			std::string newHardLink = hardLink;

			if(content.substr(hrefPos+6,1).compare("/")!=0)
			{
				newHardLink+="/";
			}
			else
			{
				newHardLink = hostHardLink;
			}

			if((content.substr(hrefPos+6,7).compare("http://") == 0)||
				(content.substr(hrefPos+6,1).compare("#") == 0)||
				(content.substr(hrefPos+6,7).compare("https:/") == 0))
			{
				printf("1st filter substr:%s\n",content.substr(hrefPos+6,7).c_str());

				if(content.substr(hrefPos+6,7).compare("http://") == 0)
				{
					if(content.substr(hrefPos+13,domainHost.length()).compare(domainHost) ==0)
					{
						content.insert((hrefPos+13),"www.");
					}
				}
			}
			else
			{
				content.insert((hrefPos+6),newHardLink);
			}

			hrefPos = content.find("href=\"",hrefPos+7);
		}

		// replace non www links
		//if(host.find(wwwHost,0)==std::string::npos)
		{
			while(domainPos != std::string::npos)
			{
				printf("Item to the left of the domain:%s\n",content.substr(domainPos-4,4).c_str());
				printf("Item to the right of the domain:%s\n",content.substr(domainPos,domainHost.length()).c_str());

				if((content.substr(domainPos-1,1).compare(".") != 0)&&(content.substr(domainPos,1).compare("w") != 0))
				{
					content.insert((domainPos),"www.");
				}

				domainPos = content.find(host,domainPos+4+(host.length()));
			}
		}

		DEB(printf("============\ncurrentContent:%s\n==========\n",content.c_str());)

		std::string::size_type fullUrlStartPos = content.find(hostHardLink,0);
		std::string::size_type fullUrlEndPos = 0;

		while(fullUrlStartPos != std::string::npos)
		{
			// find link endin
			std::string::size_type endLinkPos = content.find_first_of(" \"'>",fullUrlStartPos);

			urlsToKrawl.push_back(content.substr(fullUrlStartPos,(endLinkPos-fullUrlStartPos)));
			DEB(printf("added to krawl list:%s\n",content.substr(fullUrlStartPos,(endLinkPos-fullUrlStartPos)).c_str());)
			DEB(printf("link len:%d\n",(endLinkPos-fullUrlStartPos));)
			DEB(printf("urlsToKrawl Len:%d\n",urlsToKrawl.size());)
			content.replace(fullUrlStartPos,(endLinkPos-fullUrlStartPos),"*",1);
			fullUrlStartPos = content.find(hardLink,0);
		}


	}


	return urlsToKrawl;

}


void Krawl(SocketHandler& h,const std::string& url_in, int currentIndex)
{


	std::list<std::string> siteSpiderList;
	Parse pa(url_in,":/");
	std::string protocol = pa.getword();
	std::string host = pa.getword();
	int port;

	{
		Parse pa(host,":");
		pa.getword();
		port = pa.getvalue();
	}

	std::string url = "/" + pa.getrest();
	std::string file;

	{
		Parse pa(url,"/");
		std::string tmp = pa.getword();
		while (tmp.size())
		{
			file = tmp;
			tmp = pa.getword();
		}


	}

	
		

	std::string urlbase = host;
	std::string::size_type lastSlash = url.find_last_of("/");
	urlbase += url.substr(0,lastSlash);

	DEB(printf("url:%s\n",urlbase.c_str());)

	// Get this site and drill in for more

		DEB(printf("after getting single object\n");)

		if (!strcasecmp(protocol.c_str(),"http") || !strcasecmp(protocol.c_str(),"https"))
		{
			HttpKrawlSocket *k = new HttpKrawlSocket(h, url_in);
			DEB(printf("Current Url:%s\n",k->GetUrl().c_str());)
			k->SetMethod("GET");
			k->SetHttpVersion("HTTP/1.1");
			k->SetUserAgent(" Mozilla/4.0 (compaible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)");
			k->SetCookie(Cookie);
			k->SetConnection("Keep-Alive");
			k->SetKeepAlive(300);
			k->SpiderSite();
			h.Add(k);
			h.Select(0, 250);

			while ((! k->Complete())&&(!k->RedirectLocationSet())&&(!k->FoundEndHtml()))
			{
				h.Select(0, 250);
			}

			SiteIp = k->GetRemoteAddress().c_str();
			SiteHostName = host.c_str();

			if(k->RedirectLocationSet())
			{
				Cookie = k->GetCookie();
				DEB(printf("Krawl Url Redirected\n");)


				_strdate( dateStr);
				_strtime( timeStr );
				//fprintf(logfile,"%s-%s=>Krawl Url Redirected\n",dateStr,timeStr);
				logfile<<dateStr<<"-"<<timeStr<<":Krawl Url Redirected to:"<<k->GetRedirectLocation().c_str()<<"\n";

				Krawl(h,k->GetRedirectLocation(),currentIndex);
			}
			else if((k->GetResponseString().length()>0))
			{
				DEB(printf(":Krawl-Succeeded\n");)

				_strdate( dateStr);
				_strtime( timeStr );
				//fprintf(logfile,"%s-%s=>Krawl-Succeeded\n",dateStr,timeStr);
				logfile<<dateStr<<"-"<<timeStr<<":Krawl-Succeeded\n";

				if((url_in.find(".css",0)==std::string::npos)&&
					(url_in.find(".ico",0)==std::string::npos)&&
					(url_in.find(".js\"",0)==std::string::npos)&&
					(url_in.find(".jpg",0)==std::string::npos)&&
					(url_in.find("javascript",0)==std::string::npos))
				{
					Cookie = k->GetCookie();
					DEB(printf(":before shellexecute:%s\n",url_in.c_str());)

					_strdate( dateStr);
					_strtime( timeStr );
					//fprintf(logfile,"%s-%s=>before shellexecute:%s\n",dateStr,timeStr,url_in.c_str());
					logfile<<dateStr<<"-"<<timeStr<<":before shellexecute:"<<url_in.c_str()<<"\n";


					// If windows open ie
					ShellExecuteA(NULL, NULL, (LPCSTR)url_in.c_str(), NULL, NULL, SW_SHOWNORMAL);
					// Wait 60 seconds
					::WaitForSingleObject(kaptureEvent, 90000);


 					_strdate( dateStr);
					_strtime( timeStr );
					//fprintf(logfile,"%s-%s=>done waitin 30 seconds\n",dateStr,timeStr);
					logfile<<dateStr<<"-"<<timeStr<<"done waitin 30 seconds\n";

					// Open Documentum client
					ShellExecuteA(NULL, "open", "docInsert.bat", NULL, NULL, SW_SHOWNORMAL);

					::WaitForSingleObject(kaptureEvent, 6000);

					_strdate( dateStr);
					_strtime( timeStr );
					//fprintf(logfile,"%s-%s=>opened documentum client\n",dateStr,timeStr);
					logfile<<dateStr<<"-"<<timeStr<<":opened documentum client\n";


					//if (::WaitForSingleObject(kaptureEvent, INFINITE) == WAIT_OBJECT_0)
					{
						// Create a list from the links in the response String
						if(k->GetResponseString().length()>0)
						{
							// Get next level and repeat
							currentIndex++;
							siteSpiderList = CreateSiteList(k->GetResponseString(), url_in, host, protocol,currentIndex);


							if(!siteSpiderList.empty())
							{
								printf("spiderList not empty!\n");
								for(list<std::string>::iterator iter = siteSpiderList.begin(); iter != siteSpiderList.end(); iter++)
								{
									std::string nextUrlToKrawl = *iter;

									// take url encoded stuff out and replace them
									while(nextUrlToKrawl.find("&amp;") != std::string::npos)
									{
										nextUrlToKrawl.replace(nextUrlToKrawl.find("&amp;"),5,"&",1);
									}

									Krawl(h,nextUrlToKrawl,currentIndex);
								}

							}
							else
							{
								printf("spiderList empty!\n");
							}
						}
					}
				}
				else
				{
					_strdate( dateStr);
					_strtime( timeStr );
					//fprintf(logfile,"%s-%s=>done waitin 30 seconds\n",dateStr,timeStr);
					logfile<<dateStr<<"-"<<timeStr<<"skipping .css/js/ico file:"<<url_in.c_str()<<"\n";
				}
			}
			else
			{
				DEB(printf("Krawl-Failed\n");)

				_strdate( dateStr);
				_strtime( timeStr );
				//fprintf(logfile,"%s-%s=>Krawl-Failed\n",dateStr,timeStr);
				logfile<<dateStr<<"-"<<timeStr<<":Krawl-Failed\n";

			}
		}
		else
		{
			printf("Unknown protocol: '%s'\n",protocol.c_str());
		}
}

void usage()
{   printf("Kapture Krawler, version %s\n"
	    "Use: %s [ -u | -c | -h | -l] \\\n",
	    "00.00.01", "krawler");
	printf("\t-u urls to krawl (comma delimited ex: google.com,finance.yahoo.com)\n");
	printf("\t-l levels to krawl on site\n");
    printf("\t-c config location (default is .)\n");
	printf("\t-h help \n");
    printf("\n");
    exit(1);
}




int main(int argc, char* argv[])
{
	_strdate( dateStr);
    _strtime( timeStr );
	//fprintf(logfile,"%s-%s=>before arguments\n",dateStr,timeStr);
	logfile<<dateStr<<"-"<<timeStr<<":starting\n";

	std::string warcFileName = dateStr;
	warcFileName += "_";
	warcFileName += timeStr;
	warcFileName += ".warc";
	ofstream warcfile (warcFileName.c_str());

	// declarations
	ServerHandler h("config.xml");
	SpiderLevelSetting = h.GetInt("server/spiderLevel");
	KrawlerIp = h.GetString("server/krawlerIp");
	KrawlerOperator = h.GetString("server/operatorId");
	KrawlerHostName = h.GetString("server/krawlerHostName");
	CrawlUrls = h.GetString("server/spiderUrls");
	std::string crawlProtocol = h.GetString("server/spiderProtocol");
	h.SetReadBuffer(h.GetInt("server/readBufferMemory"));
	h.SetWriteBuffer(h.GetInt("server/writeBufferMemory"));
	bool debug = h.GetBoolean("server/debug");
	Cookie = "";
	std::string comma = ",";
	std::string siteToKrawl = "";


	_strdate( dateStr);
    _strtime( timeStr );
	//fprintf(logfile,"%s-%s=>before arguments\n",dateStr,timeStr);
	logfile<<dateStr<<"-"<<timeStr<<":before arguments\n";


	if(argc >1)
	{
		for (int i = 1; i < argc; i++)
		{
			if (argv[i][0] != '-' || !argv[i][1])
				usage();

			switch (argv[i][1])
			{
				case 'h':
					usage();
					break;
				case 'c':
					printf("feature not implemented yet\n");
					break;
				case 'u':
					printf("url setting\n");
					UseConfigUrls = false;
					//CrawlUrls = argv[i+1];
					if(++i >= argc) usage();
					CrawlUrls = argv[i];
					break;
				case 'l':
					//SpiderLevelSetting = atoi(argv[i+1]);
					printf("level setting\n");
					if(++i >= argc) usage();
					SpiderLevelSetting = atoi(argv[i]);
					break;
				default:
					break;
			}
		}
	}

	kaptureEvent = ::CreateEventA(NULL,FALSE,FALSE,"KaptureReady");

	std::string::size_type commaPos = CrawlUrls.find(comma,0);
	std::string::size_type oldCommaPos = 0;

	/*
	if(commaPos == std::string::npos)
	{
		commaPos = CrawlUrls.length();
	}
	*/

	_strdate( dateStr);
    _strtime( timeStr );
	//fprintf(logfile,"%s-%s=>after arguments\n",dateStr,timeStr);
	logfile<<dateStr<<"-"<<timeStr<<":after arguments:-u "<<CrawlUrls<<" -l "<<SpiderLevelSetting<<"\n";


	int iMyCounter = 0, iReturnVal = 0;
	DWORD dwExitCode;

	std::string Parameters = "about:blank";
	std::string FullPathToExe = "C:\\Program Files\\Internet Explorer\\iexplore.exe";
	std::string ExeName = "iexpore.exe";
	bool browserUp = false;

	/* Add a space to the beginning of the Parameters
	Parameters.insert(0," ");

   /*
     When using CreateProcess the first parameter needs to be
     the exe itself

   Parameters = ExeName.append(Parameters);

   /*
     The second parameter to CreateProcess can not be anything
     but a char!! Since we are wrapping this C function with
     strings, we will create the needed memory to hold the parameters


   /* Dynamic Char
   char * pszParam = new char[Parameters.size() + 1];

   /* Verify memory availability
   if (pszParam == 0)
   {
      /* Unable to obtain (allocate) memory
      return 1;
   }
   const char* pchrTemp = Parameters.c_str();
   strcpy(pszParam, pchrTemp);

   /* CreateProcess API initialization
   STARTUPINFOA siStartupInfo;
   PROCESS_INFORMATION piProcessInfo;
   memset(&siStartupInfo, 0, sizeof(siStartupInfo));
   memset(&piProcessInfo, 0, sizeof(piProcessInfo));
   siStartupInfo.cb = sizeof(siStartupInfo);

   /*
   _strdate( dateStr);
    _strtime( timeStr );
	//fprintf(logfile,"%s-%s=>before execute\n",dateStr,timeStr);
	logfile<<dateStr<<"-"<<timeStr<<"before execute\n";
   */

   /* Execute
   if (::CreateProcessA(FullPathToExe.c_str(), pszParam, 0, 0, false,
   CREATE_DEFAULT_ERROR_MODE, 0, 0, &siStartupInfo,
   &piProcessInfo) != false)
   {
		browserUp = true;
   }

	/*
    _strdate( dateStr);
    _strtime( timeStr );
    //fprintf(logfile,"%s-%s=>After open ie\n",dateStr,timeStr);
	logfile<<dateStr<<"-"<<timeStr<<"After open ie\n";
	*/

	while(commaPos != std::string::npos)
	{
		if(debug)
		{
			printf("*****************\n");

			_strdate( dateStr);
			_strtime( timeStr );
			//fprintf(logfile,"%s-%s=>*****************\n",dateStr,timeStr);
			logfile<<dateStr<<"-"<<timeStr<<":*****************\n";


			//printf("startPosition:%d\n",oldCommaPos);
			printf("url to krawl:%s\n",CrawlUrls.substr(oldCommaPos,(commaPos-oldCommaPos)).c_str());

			_strdate( dateStr);
			_strtime( timeStr );
			//fprintf(logfile,"%s-%s=>url to krawl:%s\n",dateStr,timeStr,CrawlUrls.substr(oldCommaPos,(commaPos-oldCommaPos)).c_str());
			logfile<<dateStr<<"-"<<timeStr<<":url to krawl:"<<CrawlUrls.substr(oldCommaPos,(commaPos-oldCommaPos)).c_str()<<"\n";

			//printf("commaPosition:%d\n",commaPos);
			//printf("newstring:%s\n",crawlUrls.substr(commaPos+1,CrawlUrls.length()+1).c_str());
			printf("*****************\n");

			_strdate( dateStr);
			_strtime( timeStr );
			//fprintf(logfile,"%s-%s=>*****************\n",dateStr,timeStr);
			logfile<<dateStr<<"-"<<timeStr<<":*****************\n";

		}

		siteToKrawl = "http://";
		siteToKrawl += CrawlUrls.substr(oldCommaPos,(commaPos-oldCommaPos)).c_str();

		// warc file info
		GUID guid;
		CoCreateGuid(&guid);
		unsigned char* stringUUID;
		UuidToStringA((UUID*)&guid, &stringUUID);
		std::string uuid = (char* stringUUID);
		printf("warc file uuid is:%s",uuid.c_str());
		warcfile<<"warc/0.9 1004 warcinfo filedesc://"<<"kapture-"<<dateStr<<timeStr<<"-krawl.kapture.info.warc "<<dateStr<<timeStr<<" text/xml uuid://"<<uuid.c_str()<<"\n\n";
		warcfile<<"<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>"<<"\n";
		warcfile<<"<warcmetadata "<<"\n";
		warcfile<<"xmlns:dc=http://purl.org/dc/elements/1.1/ "<<"\n";
		warcfile<<"xmlns:dcterms=http://purl.org/dc/terms/ "<<"\n";
		warcfile<<"xmlns:warc=\"http://archive.org/warc/0.9/\"> "<<"\n";
		warcfile<<"<warc:software> "<<"\n";
		warcfile<<"KaptureKrawler 1.0.2 http://krawler.kapture.info "<<"\n";
		warcfile<<"</warc:software> "<<"\n";
		

		Krawl(h,siteToKrawl,0);
		//CurrentSpiderLevel = 0; 

		warcfile<<"<warc:hostname>"<<SiteHostName.c_str()<<"</warc:hostname> "<<"\n";
		warcfile<<"<warc:ip>"<<SiteIp.c_str()<<"</warc:ip> "<<"\n";
		warcfile<<"<dcterms:isPartOf>"<<"kapturekrawl-"<<dateStr<<"</dcterms:isPartOf>"<<"\n";
		warcfile<<"<dc:description>kapturekrawl with WARC output</dc:description>"<<"\n";
		warcfile<<"<warc:operator>"<<KrawlerOpertor<<"</warc:operator>"<<"\n";
		warcfile<<"<warc:http-header-user-agent>"<<"\n";
		warcfile<<" Mozilla/4.0 (compaible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)"<<"\n";
		warcfile<<"</warc:http-header-user-agent>"<<"\n";
		warcfile<<"<dc:format>WARC file version 0.9</dc:format>"<<"\n";
		warcfile<<"<dcterms:conformsTo xsi:type=\"dcterms:URI\">"<<"\n";
		warcfile<<siteToKrawl<<"\n";
		warcfile<<"</dcterms:conformsTo>"<<"\n";
		warcfile<<"</warcmetadata>"<<"\n";

		oldCommaPos = commaPos+1;
		commaPos = CrawlUrls.find(comma,commaPos+1);

	}

	if(commaPos != std::string::npos)
	{
		if(debug)
		{
			printf("last url to krawl:%s\n",CrawlUrls.substr(oldCommaPos,CrawlUrls.length()+1).c_str());
		}

	siteToKrawl = "http://";
	siteToKrawl += CrawlUrls.substr(oldCommaPos,CrawlUrls.length()+1).c_str();

	}
	else
	{
		siteToKrawl = "http://";
		siteToKrawl += CrawlUrls;
	}

	Krawl(h,siteToKrawl,0);
	//CurrentSpiderLevel = 0;
	siteToKrawl += "/";
	Krawl(h,siteToKrawl,0);
	//Krawl(h,"about:blank",0);
	/*
	// If windows open ie
	ShellExecuteA(NULL, NULL, "about:blank", NULL, NULL, SW_SHOWNORMAL);
	// Wait 30 seconds
	::WaitForSingleObject(kaptureEvent, 30000);

	_strdate( dateStr);
	_strtime( timeStr );
	logfile<<dateStr<<"-"<<timeStr<<"done waitin 30 seconds\n";

	// Open Documentum client
	ShellExecuteA(NULL, "open", "docInsert.bat", NULL, NULL, SW_SHOWNORMAL);

	_strdate( dateStr);
	_strtime( timeStr );
	logfile<<dateStr<<"-"<<timeStr<<":opened documentum client\n";
	*/
	
	/*
	GetExitCodeProcess(piProcessInfo.hProcess, &dwExitCode);

	if(dwExitCode == STILL_ACTIVE)
	{
		dwExitCode = 0;
	}

	/* Release handles
	CloseHandle(piProcessInfo.hProcess);
	CloseHandle(piProcessInfo.hThread);

	/* Free memory
	delete[]pszParam;
	pszParam = 0;
	*/

	printf("done\n");

	_strdate( dateStr);
    _strtime( timeStr );
	//fprintf(logfile,"%s-%s=>before arguments\n",dateStr,timeStr);
	logfile<<dateStr<<"-"<<timeStr<<":done\n";
}