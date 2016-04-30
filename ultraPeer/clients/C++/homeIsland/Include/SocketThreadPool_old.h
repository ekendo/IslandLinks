

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
#endif

class ThreadPool
{
	ThreadPool();
	~ThreadPool();
	AddWork();
	static threadfunc_t STDPREFIX DoThreadWork(threadparam_t);

}

#ifdef SOCKETS_NAMESPACE
}
#endif

#endif // _SOCKETS_THREADPOOL_H