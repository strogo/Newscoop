include ../../../../make.env

LDFLAGS=

CFLAGS=-Wall

LINKS=csocket.h csocket.cpp exceptions.h global.h

all: links smtp_wrapper

links: $(LINKS)

csocket.h:
	if [ ! -L csocket.h ]; then ln -s ../../../parser/parser/csocket.h; fi

csocket.cpp:
	if [ ! -L csocket.cpp ]; then ln -s ../../../parser/parser/csocket.cpp; fi

exceptions.h:
	if [ ! -L exceptions.h ]; then ln -s ../../../parser/parser/exceptions.h; fi

global.h:
	if [ ! -L global.h ]; then ln -s ../../../parser/parser/global.h; fi

smtp_wrapper: smtp_wrapper.o csocket.o
	g++ -o smtp_wrapper smtp_wrapper.o csocket.o $(LDFLAGS)

clean:
	rm -f *.o *~ smtp_wrapper
	for l in $(LINKS); do rm -f $$l; done

smtp_wrapper.o: smtp_wrapper.cpp
	g++ -c -o smtp_wrapper.o $(CFLAGS) smtp_wrapper.cpp

csocket.o: csocket.cpp
	g++ -c -o csocket.o $(CFLAGS) csocket.cpp

install: all
	mkdir -p $(UTIL_BIN)
	cp -f smtp_wrapper $(UTIL_BIN)

uninstall: dummy
	rm -f $(UTIL_BIN)/smtp_wrapper

package: all
	mkdir -p $(PACKAGE_DIR)/supplemental/smtp_wrapper
	cp -f Makefile.inst $(PACKAGE_DIR)/smtp_wrapper/Makefile
	cp -f smtp_wrapper $(PACKAGE_DIR)/smtp_wrapper

dummy:

