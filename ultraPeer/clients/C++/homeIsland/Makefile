VERSION =	0.0.0.6
DIFF_VERSION =	0.0..0.5

INCLUDE =	-I/root/islandsoft/trunk/framework/client/desktop/_nix/linux/RHEL/C++/IslandLinks/homeIsland/HomeIslandManager/HomeIslandManager/Include \
		-I/root/islandsoft/trunk/framework/open/C++/Crypto++/include \
		-I/root/islandsoft/trunk/framework/open/C++/boost_1_37_0 \
		-I/usr/include
LIBDIR = 	-L./lib 
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

#CFLAGS +=	-DMACOSX 
# solaris > 8
#CFLAGS +=	-DSOLARIS
# solaris 8
#CFLAGS +=	-DSOLARIS8
# windows and/or cygwin
#CFLAGS +=	-D_WIN32
# cygwin
#CFLAGS +=	-D__CYGWIN__

# Enable insane amounts of debug output to stdout/stderr:
CFLAGS +=       -D_DEBUG
CFLAGS +=	-DNDEBUG

# Console
CFLAGS +=       -D_CONSOLE

# if you want the library to use openssl, make sure the next line is uncommented
CFLAGS += 	-DHAVE_OPENSSL

# have to include crypto package for profile
#CFLAGS += 	-DCRYPTOPP_DLL_ONLY

#imports 
CFLAGS +=       -DCRYPTOPP_IMPORTS

#exports
#CFLAGS +=       -DCRYPTOPP_EXPORTS

#xtra
#CFLAGS +=	-DCRYPTOPP_MANUALLY_INSTANTIATE_TEMPLATES

CPPFLAGS =	$(CFLAGS) 

all:		libHomeIslandManager.a HomeIslandManager
		
HomeIslandManager: homeIslandSocket.o ISocketHandler.o profile.o IslandShell.o 
		   $(CXX) -o $@ $^ $(CPPFLAGS) -L./lib -L/usr/lib -lcryptopp -lgnutls-openssl -lSockets -lpthread -lssl

OBJS =		homeIslandSocket.o \
		Ipv4Address.o \
		ISocketHandler.o \
		integer.o \
		eccrypto.o \
		profile.o \
		IslandShell.o \


libHomeIslandManager.a:	$(OBJS)
		ar cru $@ $(OBJS)
		ranlib $@

clean:
		rm -f *.o *~ slask *.d $(PROGS) *.a */*~

-include        *.d



