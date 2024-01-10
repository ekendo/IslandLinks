#ifndef _SOCKETS_THREADPOOL_H
#define _SOCKETS_THREADPOOL_H

#ifdef SOCKETS_NAMESPACE
namespace SOCKETS_NAMESPACE {
#endif

#ifdef _WIN32
	typedef unsigned threadfunc_t;
	typedef void * threadparam_t;
	#define STDPREFIX __stdcall
#else
	#include <pthread.h>
	typedef void * threadfunc_t;
	typedef void * threadparam_t;
	#define STDPREFIX
	#define HANDLE unsigned int
#endif



#ifdef _DEBUG
	#define DEB(x) x
#else
	#define DEB(x) 
#endif


class SocketThreadPool
{
	struct THREAD
	{
		THREAD(const HANDLE& m, const HANDLE& e, const HANDLE& t, bool w, bool d, bool i, Socket* p) : 
			hMutex(m)
			,hEvent(e)
			,hThread(t)
			,threadIsWorking(w)
			,shutDownThread(d)
			,suspended(i)
			,sock(p){}
		HANDLE hMutex;
		HANDLE hEvent; 
		HANDLE hThread;
		Socket *sock;
		bool threadIsWorking;
		bool shutDownThread; 
		bool suspended;

		
	};

	/** list of threads. */
	typedef std::list<THREAD *> threadPool_v;

public:
	SocketThreadPool()
	{
		#ifdef _WIN32
		hListMutex = CreateMutex(NULL, FALSE, NULL);
		#else
		pthread_mutex_t      mutex;
		pthread_mutex_init(&mutex, NULL);
		#endif

		this->m_getNewInstance = false;
	}
	
	SocketThreadPool(int threadNum)
	{
		#ifdef _WIN32
		hListMutex = CreateMutex(NULL, FALSE, NULL);
		#else
		pthread_mutex_t      mutex;
                pthread_mutex_init(&mutex, NULL);
		#endif
		this->m_threadNum = threadNum;
		this->m_getNewInstance = false;
	}
	
	~SocketThreadPool();
	void AddWork(Socket *p);
	void StartPool();
	void SetThreadNum(int value);
	bool NewInstanceNeeded();
	static threadfunc_t STDPREFIX DoThreadWork(threadparam_t);
private:
	static threadPool_v threads;
	static threadPool_v::iterator iter;
	bool m_getNewInstance;
	int m_threadNum;
	HANDLE hListMutex;
	
};

#ifdef SOCKETS_NAMESPACE
}
#endif

#endif // _SOCKETS_THREADPOOL_H
