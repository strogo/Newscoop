include ../../make.env

ALL=$(shell if [ "$(PACKAGE_TYPE)" = "source" ]; then echo all; fi)

PACKAGE_DIR=$(BIN_PACKAGE_DIR)/implementation/mailnotify

DEPENDENCES= sql_macros.h sql_connect.h

CFLAGS= -Wall

all: notifyendsubs notifyevents
	$(MAKE) -C smtp_wrapper

notifyendsubs: notifyendsubs.o
	if [ "$(PACKAGE_TYPE)" = "source" ]; then \
	    $(GPP) -o notifyendsubs notifyendsubs.o -L$(LIBMYSQLCLIENT_PATH) -lmysqlclient -lnsl -lm -lz -lcrypt; \
	    strip notifyendsubs; \
	fi

notifyevents: notifyevents.o
	if [ "$(PACKAGE_TYPE)" = "source" ]; then \
	    $(GPP) -o notifyevents notifyevents.o -L$(LIBMYSQLCLIENT_PATH) -lmysqlclient -lnsl -lm -lz -lcrypt; \
	    strip notifyevents; \
	fi

clean: dummy
	$(MAKE) -C smtp_wrapper clean
	rm -f *.o *~ sql_connect.h
	if [ "$(PACKAGE_TYPE)" = "source" ]; then \
	    rm -f notifyendsubs notifyevents; \
	fi

%.o: %.C $(DEPENDENCES)
	if [ "$(PACKAGE_TYPE)" = "source" ]; then \
	    $(GPP) -c -o $@ $(CFLAGS) $<; \
	fi

install: $(ALL)
	$(MAKE) -C smtp_wrapper install
	mkdir -p $(BIN_DIR)
	cp -f notifyendsubs $(BIN_DIR)
	cp -f notifyevents $(BIN_DIR)
	chown $(ROOT_USER).$(HTTP_GROUP) $(BIN_DIR) -R
	chmod u+rwx $(BIN_DIR) -R
	chmod g+rx $(BIN_DIR) -R
	chmod o-rwx $(BIN_DIR)/notifyendsubs
	chmod o-rwx $(BIN_DIR)/notifyevents
	cat /etc/crontab | grep "$(BIN_DIR)/notifyendsubs" > /dev/null || echo "0 0 * * * root $(BIN_DIR)/notifyendsubs" >> /etc/crontab
	cat /etc/crontab | grep "$(BIN_DIR)/notifyevents" > /dev/null || echo "* * * * * root $(BIN_DIR)/notifyevents" >> /etc/crontab
	killall -1 crond
	$(MAKE) -C smtp_wrapper install

uninstall: dummy
	$(MAKE) -C smtp_wrapper uninstall
	./uninstall

package: $(ALL)
	@if [ "$(PACKAGE_TYPE)" = "binary" ]; then echo "This is a binary package"; exit 1; fi
	mkdir -p $(PACKAGE_DIR)
	cp -f Makefile $(PACKAGE_DIR)
	cp -f uninstall $(PACKAGE_DIR)
	cp -f notifyendsubs $(PACKAGE_DIR)
	cp -f notifyevents $(PACKAGE_DIR)
	$(MAKE) -C smtp_wrapper package PACKAGE_DIR=$(PACKAGE_DIR)

dummy:
