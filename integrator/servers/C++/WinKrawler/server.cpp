/**
 **	File ......... server.cpp
 **	Published ....  2004-07-13
 **	Author ....... grymse@alhem.net
**/
/*
Copyright (C) 2004  Anders Hedstrom

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/
#include "ServerHandler.h"
#include <ListenSocket.h>
#include "HTTPServerSocket.h"
#include "MQServerSocket.h"
#include <iostream>
#include <Mutex.h>
#include <Lock.h>
#include <process.h>


int main(int argc, char* argv[])
{
	int scaleTo = 0;
	int balancer = 0;
	int selectNum = 1;
	bool dataTracker = false;
	std::string scaleVal = "";
	std::string forwardNumString = "";

	if(argc>1)
	{
		scaleVal = argv[1];
		scaleVal+=" ";
		scaleVal+=argv[2];
		scaleVal+=" ";
		scaleVal+=argv[3];

		if(strcmp(argv[1],"-s")==0)
		{
			balancer = atoi(argv[2]);
			scaleTo = atoi(argv[3]);
		}

		if(strcmp(argv[1],"-q")==0)
		{
			dataTracker = true;
		}

		switch(balancer)
		{
			case 1:
				forwardNumString = "one";
				break;
			case 2:
				forwardNumString = "two";
				break;
			case 3:
				forwardNumString = "three";
				break;
			case 4:
				forwardNumString = "four";
				break;
			case 5:
				forwardNumString = "five";
				break;
			case 6:
				forwardNumString = "six";
				break;
			case 7:
				forwardNumString = "seven";
				break;
			case 8:
				forwardNumString = "eight";
				break;
			case 9:
				forwardNumString = "nine";
				break;
			case 10:
				forwardNumString = "ten";
				break;
		}		
	}
	DEB(
	printf("scaleVal:%s\n",scaleVal.c_str());
	)

	Mutex lock;
	ServerHandler h("config.xml");
	h.EnablePool();
	h.SetForwardMax(scaleTo);
	h.SetReadBuffer(h.GetInt("server/readBufferMemory"));
	h.SetWriteBuffer(h.GetInt("server/writeBufferMemory"));
	h.SetProxyHost(h.GetString("open/bind"));
	int queue_size = h.GetInt("open/queue_size");
	int neededMemory = h.GetInt("server/mustHaveMemory");
	int maxThreadSize = h.GetInt("server/maxThreadVolume");
	int n;
	HANDLE hProcess;

	ListenSocket<HTTPServerSocket> ll_open(h);
	ListenSocket<MQServerSocket> mq_open(h);
	

	if(strcmp(argv[1],"-s")==0)
	{
		/* init handler variables */
		ll_open.serverSecurePort  = h.GetInt("secure/port");
		ll_open.serverDebugValue =  h.GetBoolean("server/debug");
		ll_open.serverTrackerUrls = h.GetString("server/trackedUrls");
		ll_open.serverReadBuf = h.GetInt("server/readBufferMemory");
		ll_open.serverWriteBuf = h.GetInt("server/readBufferMemory");
		ll_open.serverHostRedirect = h.GetString("hostDomain");
		ll_open.serverDataAddress = h.GetString("data/bind");
		ll_open.serverDataPort = h.GetInt("data/port");
		
		FORWARD f;
		
		f.address = h.GetString("loadBalancing/addresses/one");
		f.parameter = " -s 1 0";;
		f.port = h.GetInt("loadBalancing/ports/one");

		ll_open.v_forward.push_back(f);

		f.address = h.GetString("loadBalancing/addresses/two");
		f.parameter = " -s 2 0";;
		f.port = h.GetInt("loadBalancing/ports/two");

		ll_open.v_forward.push_back(f);

		f.address = h.GetString("loadBalancing/addresses/three");
		f.parameter = " -s 3 0";;
		f.port = h.GetInt("loadBalancing/ports/three");

		ll_open.v_forward.push_back(f);

		f.address = h.GetString("loadBalancing/addresses/four");
		f.parameter = " -s 4 0";;
		f.port = h.GetInt("loadBalancing/ports/four");

		ll_open.v_forward.push_back(f);

		f.address = h.GetString("loadBalancing/addresses/five");
		f.parameter = " -s 5 0";;
		f.port = h.GetInt("loadBalancing/ports/five");

		ll_open.v_forward.push_back(f);

		f.address = h.GetString("loadBalancing/addresses/six");
		f.parameter = " -s 6 0";;
		f.port = h.GetInt("loadBalancing/ports/six");

		ll_open.v_forward.push_back(f);

		f.address = h.GetString("loadBalancing/addresses/seven");
		f.parameter = " -s 7 0";;
		f.port = h.GetInt("loadBalancing/ports/seven");

		ll_open.v_forward.push_back(f);

		f.address = h.GetString("loadBalancing/addresses/eight");
		f.parameter = " -s 8 0";;
		f.port = h.GetInt("loadBalancing/ports/eight");

		ll_open.v_forward.push_back(f);

		f.address = h.GetString("loadBalancing/addresses/nine");
		f.parameter = " -s 9 0";;
		f.port = h.GetInt("loadBalancing/ports/nine");

		ll_open.v_forward.push_back(f);

		f.address = h.GetString("loadBalancing/addresses/ten");
		f.parameter = " -s 10 0";;
		f.port = h.GetInt("loadBalancing/ports/ten");

		ll_open.v_forward.push_back(f);

		if (!h.GetBoolean("open/disable"))
		{
			if(balancer==0)
			{
				//DEB(
					printf("Main Tracker\n");
				//	)
				
				if(ll_open.Bind(h.GetString("open/bind"),h.GetInt("open/port"),queue_size))
				{
					printf("could not bind to port%d\n",n);
					return -1;
				}

				
			}
			
			else
			{
				//DEB(
					printf("Scale Tracker\n");
				//	)

				std::string address = "loadBalancing/addresses/";
				address += forwardNumString;

				std::string port = "loadBalancing/ports/";
				port += forwardNumString;

				printf("address:%s-port:%d\n",h.GetString(address).c_str(),h.GetInt(port));

				if(ll_open.Bind(h.GetString(address),h.GetInt(port),queue_size))
				{
					printf("could not bind to port%d\n",n);
					return -1;
				}
			}
			

			h.Add(&ll_open);

			DEB(
			printf("opened scalable tracker\n");
			)
		}
		else
		{
			printf("open server disabled\n");
		}
	}

	if(strcmp(argv[1],"-q")==0)
	{
		if (!h.GetBoolean("data/disable"))
		{	
			if(mq_open.Bind(h.GetString("data/bind"),h.GetInt("data/port"),queue_size))
			{
				printf("could not bind to port%d\n",n);
				return -1;
			}
		
			h.Add(&mq_open);

			DEB(
			printf("opened Q\n");
			)
		}
		else
		{
			printf("open Q disabled\n");
		}
	}


	bool quit = false;
	bool error = false;
	bool spunNew = false;
	bool needTraffic = true;
	//MEMORYSTATUS stat;
	#define DIV 1024
	#define WIDTH 7
	//GlobalMemoryStatus(&stat);
	//hProcess = GetCurrentProcess();
	int i = 0;
		
	//if((stat.dwAvailPageFile/DIV)> neededMemory)
	{
		while ((!quit)&&(!error))
		{
			try
			{
				if((balancer==0)&&(!dataTracker))
				{
					DEB(
					printf("Main Daemon\n");
					)


					//if(h.GetThreadList().size()>=(maxThreadSize/scaleTo))
					if(1)
					{
						DEB(
						printf("maxThreadSize/scaleTo size exceeded\n");
							);

						DEB(
						printf("calling socketBalanced with %d\n",scaleTo);
						)

						h.SelectBalanced(0,25,scaleTo);

						i= scaleTo;

						while(i!=0)
						{
							h.SelectBalanced(0,25,(i--));
						}
					}
					else
					{
						DEB(
						printf("calling regular select\n");
						)
						h.Select(0,25);
					}
				}
				else
				{
					h.Select(0,50);
				}
				
				if(h.GetThreadList().size()>=(maxThreadSize))
				{
					if(strcmp(argv[1],"-s")==0)
					{
						quit = true;

						ShellExecuteA(NULL, "open", "C:\\inetpub\\ftproot\\ReverseProxy\\Release\\CampaignLocal.Tracker.exe", scaleVal.c_str(), NULL, SW_NORMAL );

					}

					if(strcmp(argv[1],"-q")==0)
					{

						//quit = true;
						ShellExecuteA(NULL, "open", "C:\\inetpub\\ftproot\\ReverseProxy\\Release\\CampaignLocal.Tracking.exe", scaleVal.c_str(), NULL, SW_NORMAL );

					}



					h.Select(0,50);
				}

				//GlobalMemoryStatus(&stat);
				
				if(h.SpinNewInstance())
				{
					if(strcmp(argv[1],"-s")==0)
					{
						quit = true;
					
						ShellExecuteA(NULL, "open", "C:\\inetpub\\ftproot\\ReverseProxy\\Release\\CampaignLocal.Tracker.exe", scaleVal.c_str(), NULL, SW_NORMAL );


					}
					
					if(strcmp(argv[1],"-q")==0)
					{

						//quit = true;
					
						ShellExecuteA(NULL, "open", "C:\\inetpub\\ftproot\\ReverseProxy\\Release\\CampaignLocal.Tracking.exe", scaleVal.c_str(), NULL, SW_NORMAL );
						
					}

					DEB(
						printf("h.SpinNewInstance()=true\n");
						);		
					h.Select(0,50);
				}
				
				/*
				//if((stat.dwAvailPageFile/DIV)<neededMemory)
				{
					if(strcmp(argv[1],"-s")==0)
					{
						quit = true;
					}

					if(strcmp(argv[1],"-q")==0)
					{

						//quit = true;
					
						ShellExecuteA(NULL, "open", "C:\\inetpub\\ftproot\\ReverseProxy\\Release\\CampaignLocal.Tracking.exe", "-q 0 0", NULL, SW_NORMAL );
						
					}

					DEB(
						printf ("There are only %*ld free Kbytes of paging file.\n",WIDTH, stat.dwAvailPageFile/DIV);
						);
					h.Select(0,50);
				}
				*/
			}
			catch(...)
			{
					DEB(
						printf("select frm socket Broke!!\n");
					);

					if(strcmp(argv[1],"-s")==0)
					{
						quit = true;
					}

					if(strcmp(argv[1],"-q")==0)
					{

						//quit = true;
					
						ShellExecuteA(NULL, "open", "C:\\inetpub\\ftproot\\ReverseProxy\\Release\\CampaignLocal.Tracking.exe", "-q 0 0", NULL, SW_NORMAL );
						
					}

					h.Select(0,50);
			}

			//i++;
		}
		
		if(quit)
		{
			DEB(
			printf("quit set for some reason\n");
			)
		
			while(h.GetCount())
			{
				h.Select(0,50);		
			}
		}
	}
	/*
	else
	{
		DEB(
		printf("'mustHaveMemory' is set too High! Cannot start.");
		)
	}
	*/
}

