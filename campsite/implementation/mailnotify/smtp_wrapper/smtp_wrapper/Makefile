
LDFLAGS=

CFLAGS=-Wall

LINKS=csocket.h csocket.cpp exceptions.h global.h

all: links smtp_wrapper

links:
	if [ "$(PACKAGE_TYPE)" = "source" ]; then \
	    for l in $(LINKS); do if [ ! -L $$l ]; then ln -s ../../../parser/parser/$$l; fi; done; \
	fi

smtp_wrapper: smtp_wrapper.o csocket.o
	if [ "$(PACKAGE_TYPE)" = "source" ]; then \
	    g++ -o smtp_wrapper smtp_wrapper.o csocket.o $(LDFLAGS); \
	fi

clean:
	rm -f *.o *~ smtp_wrapper
	for l in $(LINKS); do rm -f $$l; done

smtp_wrapper.o: smtp_wrapper.cpp
	if [ "$(PACKAGE_TYPE)" = "source" ]; then \
	    g++ -c -o smtp_wrapper.o $(CFLAGS) smtp_wrapper.cpp; \
	fi

csocket.o: csocket.cpp csocket.h exceptions.h global.h
	if [ "$(PACKAGE_TYPE)" = "source" ]; then \
	    g++ -c -o csocket.o $(CFLAGS) csocket.cpp; \
	fi

install: $(ALL)
	mkdir -p $(BIN_DIR)
	cp -f smtp_wrapper $(BIN_DIR)

uninstall: dummy
	rm -f $(BIN_DIR)/smtp_wrapper

package: $(ALL)
	@if [ "$(PACKAGE_TYPE)" = "binary" ]; then echo "This is a binary package"; exit 1; fi
	mkdir -p $(PACKAGE_DIR)/smtp_wrapper/smtp_wrapper
	cp -f ../configure $(PACKAGE_DIR)/smtp_wrapper
	cp -f ../Makefile $(PACKAGE_DIR)/smtp_wrapper
	cp -f Makefile $(PACKAGE_DIR)/smtp_wrapper/smtp_wrapper
	cp -f smtp_wrapper $(PACKAGE_DIR)/smtp_wrapper/smtp_wrapper

dummy:

