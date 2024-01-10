#include <SocketHandler.h>
#include <ListenSocket.h>

#include "DisplaySocket.h"


static	bool quit = false;

int main()
{
	SocketHandler h;
	ListenSocket<DisplaySocket> l(h);

	if (l.Bind(9001))
	{
		exit(-1);
	}

	h.Add(&l);
	h.Select(1,0);
	while (h.GetCount())
	{
		h.Select(1,0);
	}
}