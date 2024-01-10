// RemoteIslandManager.cpp : Implementation of WinMain


#include <SocketHandler.h>
#include <YattaListenSocket.h>
#include <ListenSocket.h>

#include "ServerHandler.h"
#include "RemoteIslandSocket.h"
#include "HTTPServerSocket.h"
//#include "RemoteIslandManager.h"

#include <iostream>
#include <stdio.h>
#include <string>
#include <sys/types.h>
#include <sys/stat.h>
#include <stdio.h>
#include <stdlib.h>
#include <fcntl.h>
#include <errno.h>
#include <unistd.h>
#include <syslog.h>
#include <string.h>

static bool daemonDebug = false;
static bool verboseReporting = false;
static std::string serviceIp = "127.0.0.1";
static std::string serviceName = "";
static std::string userName = "guest";
static int ttl = 10;
static bool daemonized = false;


class CRemoteIslandManagerModule
{
public:

    enum TrafficType
    {
        HTTP,
        YATTA,
        CUSTOM,
        ALL
    };


    enum DBType
    {
    	MSSQL,
        MYSQL,
        ORACLE,
        POSTGRES
    };

  	TrafficType ttListener;
        DBType dbConfig;
        unsigned int uID[3];
        int ConfigReadBufferMemory;
        int ConfigWriteBufferMemory;
        int QueueSize;
        int YATTAPort;
        int HTTPPort;
        int CUSTOMPort;
        int DBFreq;
        int MultipleBindNumber;
        bool UsingMultipleBind;
        bool Debug;
        bool StopListener;
        bool UsesDB;
        std::string BindAddress;
        std::string ConfigFileName;
        std::string DBName;
        std::string DBUser;
        std::string DBPass;
        std::string DBDsn;

	
	/* Constructor */
	CRemoteIslandManagerModule(TrafficType tt)
	{
		ttListener = tt;
	}
	
	/*	Main Message Loop	*/
	void RunMessageLoop() throw()
        {

                ServerHandler H(this->ConfigFileName);
                this->ConfigReadBufferMemory = H.GetInt("server/readBufferMemory");

                if(this->ConfigReadBufferMemory!=0)
                {
                }
                else
                {
                }

                if(!H.GetString("database/type").empty())
                {
                        this->UsesDB = true;

                        if(strcmp(H.GetString("database/type").c_str(),"MSSQL")==0)
                        {
                                this->dbConfig = MSSQL;
                                this->DBUser = H.GetString("database/user").c_str();
                                this->DBPass = H.GetString("database/password").c_str();
                                this->DBDsn = H.GetString("database/host").c_str();
                                this->DBName = H.GetString("database/database").c_str();
                                this->DBFreq = H.GetInt("database/frequency");
                        }

                        if(strcmp(H.GetString("database/type").c_str(),"MYSQL")==0)
                        {
                                this->dbConfig = MYSQL;
				this->DBUser = H.GetString("database/user").c_str();
                                this->DBPass = H.GetString("database/password").c_str();
                                this->DBDsn = H.GetString("database/host").c_str();
                                this->DBName = H.GetString("database/database").c_str();

                        }
                }
		else
                {
                        this->UsesDB = false;
                }

                this->ConfigWriteBufferMemory = H.GetInt("server/writeBufferMemory");
                this->Debug = H.GetBoolean("server/debug");
                H.SetReadBuffer(this->ConfigReadBufferMemory);
                H.SetWriteBuffer(this->ConfigWriteBufferMemory);

                YattaListenSocket<RemoteIslandSocket> l(H);
                ListenSocket<HTTPServerSocket> l1(H);
                ListenSocket<HTTPServerSocket> l2(H);
                ListenSocket<HTTPServerSocket> l3(H);
                ListenSocket<HTTPServerSocket> l4(H);
                ListenSocket<HTTPServerSocket> l5(H);

                if((this->ttListener == YATTA)||(this->ttListener == ALL))
                {
                	this->QueueSize = H.GetInt("server/queue_size");
                        this->BindAddress = H.GetString("server/bind");

		    	#ifdef _DEBUG
                        l.serverDebugValue = true;
                        #else
                        l.serverDebugValue = false;
                        #endif
                        l.serverDebugValue =  this->Debug;
                        l.serverReadBuf = this->ConfigReadBufferMemory;
                        l.serverWriteBuf = this->ConfigWriteBufferMemory;

                        l.serviceWelcomeMessage = H.GetString("server/welcomeMessage");
                        l.serviceName = H.GetString("server/serviceName");
                        l.serviceEventName = H.GetString("server/eventName");
                        l.serviceUsage = H.GetString("server/usage");
                        l.serviceRootDir = H.GetString("server/serviceRoot");

                        if (!l.Bind(this->BindAddress,this->YATTAPort,this->QueueSize))
                        {
                                H.Add(&l);
                        }
                        else
                        {
                        }
                }
	
		if((this->ttListener == HTTP)||(this->ttListener == ALL))
                {

                        if(H.GetInt("open/multipleBind")==1)
                        {
                                this->QueueSize = H.GetInt("open/bindOne/queue_size");
                                this->BindAddress = H.GetString("open/bindOne/bind");
                                this->UsingMultipleBind = false;

                        }
                        else
                        {
                                this->QueueSize = H.GetInt("open/bindOne/queue_size");
                                this->BindAddress = H.GetString("open/bindOne/bind");
                                this->MultipleBindNumber = H.GetInt("open/multipleBind");

                        }

			//1
                        #ifdef _DEBUG
                        l1.serverDebugValue = true;
                        #else
                        l1.serverDebugValue = false;
                        #endif
                        l1.serverDebugValue =  this->Debug;
                        l1.serverReadBuf = this->ConfigReadBufferMemory;
                        l1.serverWriteBuf = this->ConfigWriteBufferMemory;

                        l1.serverDoNotChangeList = H.GetString("server/doNotChangeList");
                        l1.serverIgnoreList = H.GetString("server/ignoreList");
                        l1.serverTrackerUrlMap = H.GetPairList("server/affiliateUrls/affiliateUrl", "keywords");

                        if(!this->UsingMultipleBind)
                        {
                                if (!l1.Bind(this->BindAddress,this->HTTPPort,this->QueueSize))
                                {
                                        H.Add(&l1);
                                }
                                else
                                {
                                }
                        }
			else
                        {

                                switch(this->MultipleBindNumber)
                                {

                                case 5:
                                        #ifdef _DEBUG
                                        l5.serverDebugValue = true;
                                        #else
                                        l5.serverDebugValue = false;
                                        #endif
                                        l5.serverDebugValue =  this->Debug;
                                        l5.serverReadBuf = this->ConfigReadBufferMemory;
                                        l5.serverWriteBuf = this->ConfigWriteBufferMemory;
                                        l5.serverDoNotChangeList = H.GetString("server/doNotChangeList");
                                        l5.serverIgnoreList = H.GetString("server/ignoreList");
                                        l5.serverTrackerUrlMap = H.GetPairList("server/affiliateUrls/affiliateUrl", "keywords");

                       			if (!l5.Bind(H.GetString("open/multipleBind/bindFive/bind"),this->HTTPPort,H.GetInt("open/multipleBind/bindFive/queue_size")))
                                        {
                                        	H.Add(&l5);
                                        }
                                        else
                                        {
                                        }
					break;
                                case 4:
                                        #ifdef _DEBUG
                                        l4.serverDebugValue = true;
                                        #else
                                        l4.serverDebugValue = false;
                                        #endif
                                        l4.serverDebugValue =  this->Debug;
                                        l4.serverReadBuf = this->ConfigReadBufferMemory;
                                        l4.serverWriteBuf = this->ConfigWriteBufferMemory;
                                        l4.serverDoNotChangeList = H.GetString("server/doNotChangeList");
                                        l4.serverIgnoreList = H.GetString("server/ignoreList");
                                        l4.serverTrackerUrlMap = H.GetPairList("server/affiliateUrls/affiliateUrl", "keywords");

                                        if (!l4.Bind(H.GetString("open/multipleBind/bindFour/bind"),this->HTTPPort,H.GetInt("open/multipleBind/bindFour/queue_size")))
                                        {
                                                H.Add(&l4);
                                        }
                                        else
                                        {
                                        }
					break;
                                case 3:
				        #ifdef _DEBUG
                                        l3.serverDebugValue = true;
                                        #else
                                        l3.serverDebugValue = false;
                                        #endif
                                        l3.serverDebugValue =  this->Debug;
                                        l3.serverReadBuf = this->ConfigReadBufferMemory;
                                        l3.serverWriteBuf = this->ConfigWriteBufferMemory;
                                        l3.serverDoNotChangeList = H.GetString("server/doNotChangeList");
                                        l3.serverIgnoreList = H.GetString("server/ignoreList");
                                        l3.serverTrackerUrlMap = H.GetPairList("server/affiliateUrls/affiliateUrl", "keywords");

                                        if (!l3.Bind(H.GetString("open/multipleBind/bindThree/bind"),this->HTTPPort,H.GetInt("open/multipleBind/bindThree/queue_size")))
                                        {
                                                H.Add(&l3);
                                        }
                                        else
                                        {
                                        }
					break;
                                case 2:
					#ifdef _DEBUG
                                        l2.serverDebugValue = true;
                                        #else
                                        l2.serverDebugValue = false;
                                        #endif
                                        l2.serverDebugValue =  this->Debug;
                                        l2.serverReadBuf = this->ConfigReadBufferMemory;
                                        l2.serverWriteBuf = this->ConfigWriteBufferMemory;
                                        l2.serverDoNotChangeList = H.GetString("server/doNotChangeList");
                                        l2.serverIgnoreList = H.GetString("server/ignoreList");
                                        l2.serverTrackerUrlMap = H.GetPairList("server/affiliateUrls/affiliateUrl", "keywords");

                                        if (!l2.Bind(H.GetString("open/multipleBind/bindTwo/bind"),this->HTTPPort,H.GetInt("open/multipleBind/bindTwo/queue_size")))
                                        {
                                                H.Add(&l2);
                                        }
                                        else
                                        {
                                        }
					break;
				case 1:
                                        if (!l1.Bind(H.GetString("open/multipleBind/bindOne/bind"),this->HTTPPort,H.GetInt("open/multipleBind/bindOne/queue_size")))
                                        {
                                                H.Add(&l1);
                                        }
                                        else
                                        {
                                        }
                                }
                        }

                }

		
		H.Select(0,5000);
                int i =0;
                while (!StopListener)
                {
                                H.Select(0,5000);

                                if(!this->UsesDB)
                                {
                                        // Release lock on config file?
                                        sleep(10);
                                }
                                else
                                {
                                        // Connect and reload affiliate urls etc,...
                                        if((i==0)||(i==this->DBFreq))
                                        {
                                                i = 0;


                                                std::map<std::string,std::string> valuePairList;
                                                std::string affiliateUrl;
                                                std::string keywords = "";
		
						 // Process data now
                                                try
                                                {
                                                        // Open Connection

                                                        // Open a session.

							 while(1)
                                                         {
                                                                 //affiliateUrl = m_Rs.m_AffiliateUrl;
                                                                 //keywords = m_Rs.m_Keywords;

                                                                 if(!keywords.empty())
                                                                 {
                                                                        std::string::size_type oldKeywordSeparator = keywords.find("\n");

                                                                        while(oldKeywordSeparator!=std::string::npos)
                                                                        {
                                                                                keywords.replace(oldKeywordSeparator,1,"|",1);

                                                                                oldKeywordSeparator = keywords.find("\n",oldKeywordSeparator+1);

                                                                        }
                                                                 }

                                                                 valuePairList.insert(make_pair(affiliateUrl,keywords));


                                                         }

                                                        //this->DBSession.Close();
                                                        //this->DBConn.Close();
                                                }
                                                catch(...)
                                                {
                                                         //e.DisplayError();
                                                         return;
                                                }

                                                if(!valuePairList.empty())
                                                {
                                                        l1.serverTrackerUrlMap = valuePairList;
                                                }
                                        }

                                        i++;
                                }
        	}
		

 	}// End RunMessageLoop

        int Start()
        {

                // Load up the threads we want.
                switch(ttListener)
                {
                case HTTP:
                	this->ConfigFileName = "HTTPConfig.xml";
                        this->HTTPPort = 80;
                        break;
                case YATTA:
                        this->ConfigFileName = "YATTAConfig.xml";
                        this->YATTAPort = 32920;
                        break;
                case ALL:
                        this->ConfigFileName = "RemoteIslandConfig.xml";
                        break;
		 case CUSTOM:
                        this->ConfigFileName = "config.xml";
                        break;

                }

                StopListener = false;
		RunMessageLoop();
                
		return 1;
        }
};


void usage()
{
        printf("RemoteIslandManager, version %s\n"
            "Use: %s [ -d | -b | -v ] [ -t seconds ] \\\n"
            "\t[ -n ] [ -a ] [ -u ] 		\\\n",
            "00.00.03", "remoteislandmanager");

	printf("\t-d run as daemon");
        printf("\t-b debug\n");
        printf("\t-v verbose\n");
        printf("\t-t time to live on a service search\n");
        printf("\t-n service name\n");
        printf("\t-a service address\n");
        printf("\t-u username\n");
        printf("\n");
        exit(1);
}


int main(int argc, char *argv[]) 
{
	int i;
        //bool quit = false;
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
                                case 'b':
                                        daemonDebug = true;
                                        break;
                                case 'v':
                                        verboseReporting = true;
                                        break;
                                case 't':
                                        if(++i >= argc) usage();
                                        ttl = atoi(argv[i]);
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
				 case 'd':
                                        if(++i >= argc) usage();
                                        daemonized = atoi(argv[i]);
                                        break;
                                default:
                                        usage();

                        }
                }
        }

	pid_t pid, sid;
	
	/* set manager object */
        CRemoteIslandManagerModule *man = new CRemoteIslandManagerModule(CRemoteIslandManagerModule::HTTP);
	
	if(daemonized)
	{
        	/* Our process ID and Session ID */
        	//pid_t pid, sid;

        	/* Fork off the parent process */
        	pid = fork();
        	if (pid < 0)
        	{
               		exit(EXIT_FAILURE);
        	}

        	/* If we got a good PID, then
           	we can exit the parent process. */
        	if (pid > 0)
        	{
                	exit(EXIT_SUCCESS);
        	}

        	/* Change the file mode mask */
        	umask(0);

        	/* Open any logs here */

        	/* Create a new SID for the child process */
        	sid = setsid();
        	if (sid < 0)
        	{
                	/* Log the failure */
                	exit(EXIT_FAILURE);
        	}

        	/* Change the current working directory */

		if ((chdir("/")) < 0)
        	{
                	/* Log the failure */
                	exit(EXIT_FAILURE);
        	}

        	/* Close out the standard file descriptors */
        	close(STDIN_FILENO);
        	close(STDOUT_FILENO);
        	close(STDERR_FILENO);

        	/* Daemon-specific initialization goes here */

        	/* The Big Loop */
        	while (1)
        	{
           		/* Do some task here ... */
           		man->Start();
           		sleep(30); /* wait 30 seconds */
        	}

        	exit(EXIT_SUCCESS);
	}        
}



