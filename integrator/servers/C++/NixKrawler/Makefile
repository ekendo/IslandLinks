#
#   Makefile for OSX Krawler
#
VERSION =	0.0.0.1
DIFF_VERSION =	0.0..0.0

INCLUDE =	-I./include \
		-I/usr/include \
 		-I/root/islandsoft/trunk/framework/open/C++/Sockets-2.1
LIBDIR = 	-L./lib \
PREFIX =	./

# .h files installed to $(PREFIX)/Include
# static lib .a files installed to $(PREFIX)/lib

CXX =		g++
CFLAGS =	-Wall -g $(INCLUDE) $(LIBDIR) -MD -D_VERSION='"$(VERSION)"'

# turn off optimization if compiling with gcc 2.9.x:
#CFLAGS +=	-O0
CFLAGS +=	-O2

# uncomment depending on your operating system
CFLAGS +=	-DLINUX

# Enable insane amounts of debug output to stdout/stderr:
CFLAGS +=       -D_DEBUG
CFLAGS +=	-DNDEBUG

# Console
CFLAGS +=       -D_CONSOLE

# if you want the library to use openssl, make sure the next line is uncommented
CFLAGS += 	-DHAVE_OPENSSL

#imports 
CFLAGS +=       -DCRYPTOPP_IMPORTS

#exports
#CFLAGS +=       -DCRYPTOPP_EXPORTS

#xtra
#CFLAGS +=	-DCRYPTOPP_MANUALLY_INSTANTIATE_TEMPLATES

CPPFLAGS =	$(CFLAGS) 

all:		libKrawler.a Krawler
		
Krawler: BaseXMLFile.o Configuration.o ServerHandler.o Mime.o HttpKrawlSocket.o
		   $(CXX) -o $@ $^ $(CPPFLAGS) -g -L./lib -L/usr/lib -lgnutls-openssl -lSockets \
			-lpthread -lssl -lxml2 


OBJS =		Krawler.o \
		Ipv4Address.o \
		ServerHandler.o \
		BaseXMLFile.o \
		Configuration.o \
		Mime.o 

libKrawler.a:	$(OBJS)
		ar cru $@ $(OBJS)
		ranlib $@

clean:
		rm -f *.o *~ slask *.d $(PROGS) *.a */*~

-include        *.d




