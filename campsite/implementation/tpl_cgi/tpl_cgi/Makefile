OBJ_FILES=main.o csocket.o

LDFLAGS=
LDLIBS=

DEPENDENCES=global.h exceptions.h csocket.h tol_srvdef.h srv_def.h

CFLAGS=-Wall
# -D_DEBUG -g

TPL_CGI=tpl_cgi

LINKS=csocket.h csocket.cpp exceptions.h global.h tol_srvdef.h

all: links $(TPL_CGI)

links: dummy
	for l in $(LINKS); do \
	    if [ ! -L $$l ]; then ln -s ../../parser/parser/$$l; fi; \
	done

$(TPL_CGI): $(OBJ_FILES)
	g++ -o $(TPL_CGI) $(OBJ_FILES) $(LDFLAGS) $(LDLIBS)
	strip $(TPL_CGI)

clean: dummy
	rm -f *.o *~ srv_def.h
	for l in $(LINKS); do rm -f $$l; done
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f $(TPL_CGI); fi

main.o: main.cpp $(DEPENDENCES)
	g++ -c -o main.o $(CFLAGS) main.cpp

csocket.o: csocket.cpp $(DEPENDENCES)
	g++ -c -o csocket.o $(CFLAGS) csocket.cpp

install: $(ALL)
	mkdir -p $(CGI_DIR)
	cp -f $(TPL_CGI) $(CGI_DIR)
	chmod 750 $(CGI_DIR)/$(TPL_CGI)

uninstall: dummy
	rm -f $(CGI_DIR)/$(TPL_CGI)

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/tpl_cgi
	cp -f Makefile $(PACKAGE_DIR)/tpl_cgi
	cp -f $(TPL_CGI) $(PACKAGE_DIR)/tpl_cgi

dummy:
