include ../../../make.env

LDFLAGS=-lm -lz
MYSQL_LIBDIR=-L/usr/lib/mysql

CFLAGS=-Wall

all: mysql_connect

mysql_connect: mysql_connect.o
	g++ -o mysql_connect mysql_connect.o $(LDFLAGS) $(MYSQL_LIBDIR) -lmysqlclient

clean:
	rm -f *.o *~ mysql_connect

mysql_connect.o: mysql_connect.cpp
	g++ -c -o mysql_connect.o $(CFLAGS) mysql_connect.cpp

install: all
	mkdir -p $(UTIL_BIN)
	cp -f mysql_connect $(UTIL_BIN)

uninstall: dummy
	rm -f $(UTIL_BIN)/mysql_connect

package: all
	mkdir -p $(PACKAGE_DIR)/supplemental/mysql_connect
	cp -f Makefile.inst $(PACKAGE_DIR)/mysql_connect/Makefile
	cp -f mysql_connect $(PACKAGE_DIR)/mysql_connect

dummy:

