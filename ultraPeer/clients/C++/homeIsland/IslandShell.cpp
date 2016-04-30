#include <stdio.h>
#include <iostream>
#include <stdlib.h>
#include <string.h>
#include <string>
#include <time.h>

// socket includes
#include <SocketHandler.h>
#include "homeIslandSocket.h"
#include "profile.h"

using namespace std;

static bool shellDebug = false;
static bool verboseReporting = false;
static bool getSpecificService = false;
static bool getServiceListing = false;
static std::string serviceIp = "127.0.0.1"; 
static std::string serviceName = "";
static std::string searchCategory = "none";
static std::string searchName = "All";
static std::string userName = "guest";
static int ttl = 10; // seconds
static int seedInt = 0;

extern "C" const char * generate_KeyPair(int seed)
{
	std::string pubKey = "PublicKey=";
	std::string priKey = "PrivateKey=";
	std::string keyPair;
	std::string pubContent, priContent;
	profile * p = new profile();
	srand (time(NULL));
	int randNum = rand() % seed*1000;

	p->generatePrivateKey(randNum);
	HexEncoder privkeysink(new StringSink(priContent));
	p->priKey.DEREncode(privkeysink);
	privkeysink.MessageEnd();
	keyPair = priKey.c_str();
	keyPair+= priContent;

	p->generatePublicKey();
	HexEncoder publickeysink(new StringSink(pubContent));
	p->pubKey.DEREncode(publickeysink);
	publickeysink.MessageEnd();
	keyPair+= pubKey.c_str();
	keyPair+= pubContent;

	return keyPair.c_str();
}


void usage()
{   
	printf("islandShell, version %s\n"
	    "Use: %s [ -f | -s | -d | -v ] [ -t seconds ] \\\n"
	    "\t[ -c ] [ -n ] [ -a ] [ -u ] [ -k seed int]\n",
	    "00.00.06", "homeislandmanager");
    	printf("\t-f find specific service\n");
    	printf("\t-s search for all services\n");
   	printf("\t-d debug\n");
    	printf("\t-v verbose\n");
    	printf("\t-t time to live on a service search\n");
	printf("\t-c search category\n");
	printf("\t-n service name\n");
	printf("\t-a service address\n");
	printf("\t-u username\n");
   	printf("\t-k generate public/private key with seed int\n");
 	printf("\n");
    	exit(1);
}



int main(int argc, char *argv[])
{
    //  Run through the command-line args once, looking for debugging flags
    //  and config-file flags.  (We want to check those before we parse the
    //  config file, and we want to parse the config file before reading other
    //  command-line args which might override its contents.)

    	int i;
	bool quit = false;
	std::string userCommands = "";

	printf("testing");
	if(argc == 1)
	{
		//printf("argc==1\n");
		usage();
	}
	else
	{
		//printf("argc!=1\n");
		
		for (i = 1; i < argc; i++)
		{
			//printf("argv[i][0]=%c\n",argv[i][0]);
			if (argv[i][0] != '-' || !argv[i][1] || argv[i][2]) 
				usage();
	        
			switch (argv[i][1])
			{
				case 'f':
					getSpecificService = true;
					break;
				case 's':
					getSpecificService = false;
					break;
				case 'd':
					shellDebug = true;
					break;
				case 'v':
					verboseReporting = true;
					break;
				case 't':
					if(++i >= argc) usage();
					ttl = atoi(argv[i]);
					break;
				case 'c':
					if(++i >= argc) usage();
					searchCategory = atoi(argv[i]);
					break;
				case 'n':
					if(++i >= argc) usage();
					serviceName = argv[i];
					break;
				case 'a':
					if(++i >= argc) usage();
					serviceIp = argv[i];
					break;
				case 'u':
					if(++i >= argc) usage();
					userName = argv[i];
					break;
				case 'k':
					if(++i >= argc) usage();
					seedInt = atoi(argv[i]);
					break;				
				default:
					usage();
		
			}
		}
	}


	if(verboseReporting)
	{
		printf("\tVerbose set to ON\n");

		if(shellDebug)
		{
			printf("\tDebug set to ON\n");
		}

		if(!shellDebug)
		{
			printf("\tDebug set to OFF\n");
		}

		if(getSpecificService)
		{
			printf("\tSpecific service set to ON\n");
		}

		if((!getSpecificService))
		{
			printf("\tAny service set to ON\n");
		}

		printf("\tTtl is set to %d\n",ttl);
		printf("\tSearch category set to %s\n",searchCategory.c_str());
		printf("\tService name is set to %s\n",serviceName.c_str());
		printf("\tService IP is set to %s\n",serviceIp.c_str());
		printf("\tHome Island user name is set to %s\n", userName.c_str());
	}


	if(shellDebug)
	{
		printf("\tbefore contents and profile initialization\n");
	}

	std::string priContents, pubContents;
	profile * pf = new profile();
	srand ( time(NULL) );
	int randNum = rand() % (seedInt*1000);
	
	if(shellDebug)
	{
		printf("\tafter contents and profile initialization\n");
	}


	if(seedInt >0)
	{
		if(shellDebug)
		{
			printf("\tinside seedInt > 0 about to create private Key with randNum:%d\n",randNum);
		}

		
		pf->generatePrivateKey(seedInt);
		HexEncoder privkeysink(new StringSink(priContents));
		pf->priKey.DEREncode(privkeysink);	
		privkeysink.MessageEnd();   // Need to flush HexEncoder's buffer

		
		if(shellDebug)
		{
			printf("\tprivate Key is created and Encoded. about to create public Key\n");
		}

		printf("\t\r\nPrivate Key is:%s\n",priContents.c_str());
		
		pf->generatePublicKey();
		HexEncoder pubkeysink(new StringSink(pubContents));
		pf->pubKey.DEREncode(pubkeysink);
		pubkeysink.MessageEnd();    // Need to flush HexEncoder's buffer
		

		if(shellDebug)
		{
			printf("\tpublic Key is created and Encoded. \n");
		}

		printf("\t\r\nPublic Key is:%s\n",pubContents.c_str());
		
	}

	delete pf;


	SocketHandler h;
	HomeIslandSocket *p; 

	if(getSpecificService||getServiceListing)
	{
	
		h.SetReadBuffer(500000);
        	h.SetWriteBuffer(500000);

	
		if(getSpecificService)
		{
			p = new HomeIslandSocket(h, serviceIp);
			p ->SetDebugMode(shellDebug);
			p ->ContactSpecificService();
		}
		else
		{
			p = new HomeIslandSocket(h);
			p -> SetDebugMode(shellDebug);	
		}
	
		if(shellDebug)
		{	
			printf("created HomeIslandSocket\n");
		}
	
		p -> SetDeleteByHandler();
		p -> SetUserName(userName);
		p -> SetConnectionRetry(10);
		p -> SetConnectTimeout(ttl);
		p -> SetInputType(HomeIslandSocket::SHELL);

		h.Add(p);
		i = 0;
	
		//while(i<=ttl)
		{
			h.Select(1,0);
		
			while (h.GetCount())
			{

				if(p -> NextMoveNeeded())
				{
					if(!p ->SessionHasEnded())
					{
						//printf("%s", p ->GetServiceMessage())
						cout << "Service Message:" << p-> GetServiceMessage() << "\n";
						getline (cin, userCommands);
						p -> SetPeerInput(userCommands);	
					}
					else
					{
					
					}
				}
			
				//else
				{
					//printf("downloading info..\n");
					h.Select(1,0);
				}
			}

			//printf("no answer from service at %s. Trying again,...\n",serviceIp.c_str());
			//i++;
		}

	
	}	
}
