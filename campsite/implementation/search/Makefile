include ../../make.env

PACKAGE_DIR=$(BIN_PACKAGE_DIR)/implementation

GCC=gcc
CFLAGS=-Wall -O3 -fomit-frame-pointer -malign-jumps=4 -malign-loops=4 -malign-functions=4
LDFLAGS=-L$(LIBMYSQLCLIENT_PATH)
LDLIBS=-lmysqlclient -lm -lz -lcrypt

all: gather

gather: gather.o kwd.o
	$(GCC) -o gather gather.o kwd.o $(LDFLAGS) $(LDLIBS)

broker: broker.o kwd.o
	$(GCC) -o broker broker.o kwd.o $(LDFLAGS) $(LDLIBS)

gather.o: gather.c kwd.h

kwd.o: kwd.c kwd.h

broker.o: broker.c kwd.h broker.h

clean:
	rm -f *.o *~ sql_connect.h
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f gather broker; fi

install: $(ALL)
	mkdir -p $(BIN_DIR)
	cp -f gather $(BIN_DIR)
	chmod 750 $(BIN_DIR)/gather
	cat /etc/crontab | grep -w gather > /dev/null || echo "0 0 * * * $(ROOT_USER) $(BIN_DIR)/gather" >> /etc/crontab
	killall -1 crond

uninstall: dummy
	./uninstall

package: $(ALL)
	@if [ "$(PACKAGE_TYPE)" = "binary" ]; then echo "This is a binary package"; exit 1; fi
	mkdir -p $(PACKAGE_DIR)/search
	cp -f Makefile $(PACKAGE_DIR)/search
	cp -f gather $(PACKAGE_DIR)/search
	cp -f uninstall $(PACKAGE_DIR)/search

dummy:
