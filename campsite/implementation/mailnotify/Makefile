include ../../make.env

ALL=$(shell if [ "$(PACKAGE_TYPE)" = "source" ]; then echo all; else echo dummy; fi)

PACKAGE_DIR=$(BIN_PACKAGE_DIR)/implementation/mailnotify

DEPENDENCES= sql_macros.h

CFLAGS= -Wall

all: notifyendsubs notifyevents
	$(MAKE) -C smtp_wrapper

notifyendsubs: notifyendsubs.o
	$(GPP) -o notifyendsubs notifyendsubs.o -L$(LIBMYSQLCLIENT_PATH) -lmysqlclient -lnsl -lm -lz -lcrypt
	strip notifyendsubs

notifyevents: notifyevents.o
	$(GPP) -o notifyevents notifyevents.o -L$(LIBMYSQLCLIENT_PATH) -lmysqlclient -lnsl -lm -lz -lcrypt
	strip notifyevents

clean: dummy
	$(MAKE) -C smtp_wrapper clean
	rm -f *.o *~ sql_connect.h
	if [ "$(PACKAGE_TYPE)" = "source" ]; then \
	    rm -f notifyendsubs notifyevents; \
	fi

%.o: %.C $(DEPENDENCES)
	$(GPP) -c -o $@ $(CFLAGS) $<

install: $(ALL)
	mkdir -p $(CONF_DIR)
	echo -e "SERVER $(DATABASE_SERVER)\nPORT $(DATABASE_PORT)\nUSER $(DATABASE_USER)\nPASSWORD $(DATABASE_PASSWORD)\nNAME $(DATABASE_NAME)" > $(CONF_DIR)/database.conf
	echo -e "SERVER $(SMTP_SERVER)\nWRAPPER $(BIN_DIR)/smtp_wrapper" > $(CONF_DIR)/smtp.conf
	$(MAKE) -C smtp_wrapper install
	mkdir -p $(BIN_DIR)
	cp -f notifyendsubs $(BIN_DIR)
	cp -f notifyevents $(BIN_DIR)
	chown $(ROOT_USER).$(HTTP_GROUP) $(BIN_DIR)/notifyendsubs $(BIN_DIR)/notifyevents
	chmod 750 $(BIN_DIR)/notifyendsubs
	chmod 750 $(BIN_DIR)/notifyevents
	cat /etc/crontab | grep "$(BIN_DIR)/notifyendsubs" > /dev/null || echo "0 0 * * * $(ROOT_USER) $(BIN_DIR)/notifyendsubs" >> /etc/crontab
	cat /etc/crontab | grep "$(BIN_DIR)/notifyevents" > /dev/null || echo "* * * * * $(ROOT_USER) $(BIN_DIR)/notifyevents" >> /etc/crontab
	killall -1 crond

uninstall: dummy
	$(MAKE) -C smtp_wrapper uninstall
	./uninstall_mailnotify

package: $(ALL)
	@if [ "$(PACKAGE_TYPE)" = "binary" ]; then echo "This is a binary package"; exit 1; fi
	mkdir -p $(PACKAGE_DIR)
	cp -f Makefile $(PACKAGE_DIR)
	cp -f uninstall_mailnotify $(PACKAGE_DIR)
	cp -f notifyendsubs $(PACKAGE_DIR)
	cp -f notifyevents $(PACKAGE_DIR)
	$(MAKE) -C smtp_wrapper package PACKAGE_DIR=$(PACKAGE_DIR)

dummy:
