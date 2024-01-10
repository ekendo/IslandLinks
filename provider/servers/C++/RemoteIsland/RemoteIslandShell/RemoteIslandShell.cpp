// RemoteIslandShell.cpp : Defines the entry point for the console application.
//
#include <StdoutLog.h>
#include <SocketHandler.h>
#include <YattaListenSocket.h>
#include <string>
#include <stdio.h>
#include <stdlib.h>
#include <tchar.h>
#include "ServerHandler.h"
#include "RemoteIslandSocket.h"

int _tmain(int argc, char* argv[])
{
	
		//SocketHandler h;

		//::MessageBoxA(0,"process","peer",0);
		StdoutLog log;
		ServerHandler h("config.xml");
		//SocketHandler h;
		int queue_size = h.GetInt("server/queue_size");
		//int queue_size = 20;

		h.RegStdLog(&log);
		h.SetReadBuffer(h.GetInt("server/readBufferMemory"));
		h.SetWriteBuffer(h.GetInt("server/writeBufferMemory"));

		YattaListenSocket<RemoteIslandSocket> l(h);
	
		l.serverDebugValue =  h.GetBoolean("server/debug");
#ifdef _DEBUG
		l.serverDebugValue = true;
#else
		l.serverDebugValue = false;
#endif	
		l.serverReadBuf = h.GetInt("server/readBufferMemory");
		l.serverWriteBuf = h.GetInt("server/writeBufferMemory");
		l.serviceWelcomeMessage = h.GetString("server/welcomeMessage");
		l.serviceName = h.GetString("server/serviceName");
		l.serviceEventName = h.GetString("server/eventName");
		l.serviceUsage = h.GetString("server/usage");
		l.serviceRootDir = h.GetString("server/serviceRoot");
		
		if (l.Bind(h.GetString("server/bind"),h.GetInt("server/port"),queue_size))
		{
			return(-1);
		}

		h.Add(&l);
		h.Select(0,5000);
		
		while (1)
		{
			h.Select(1,0);
		}
		

	return 0;
}

