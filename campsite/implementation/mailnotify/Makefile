include ../../make.env

DEPENDENCES= *.h

CFLAGS= -Wall

all: links notifyendsubs notifyevents
	$(MAKE) -C smtp_wrapper

links:
	@if [ ! -L sql_connect.h ]; then /bin/ln -s ../parser/parser/sql_connect.h; fi

notifyendsubs: notifyendsubs.o
	$(GPP) -o notifyendsubs notifyendsubs.o $(LIBMYSQLCLIENT_PATH) -lmysqlclient -lnsl -lm -lz -lcrypt
	strip notifyendsubs

notifyevents: notifyevents.o
	$(GPP) -o notifyevents notifyevents.o $(LIBMYSQLCLIENT_PATH) -lmysqlclient -lnsl -lm -lz -lcrypt
	strip notifyevents

clean:
	$(MAKE) -C smtp_wrapper clean
	rm -f *.o *~ notifyendsubs notifyevents sql_connect.h

%.o: %.C $(DEPENDENCES)
	$(GPP) -c -o $@ $(CFLAGS) $<

install: all
	$(MAKE) -C smtp_wrapper install
	mkdir -p $(BIN_DIR)
	cp -f notifyendsubs $(BIN_DIR)
	cp -f notifyevents $(BIN_DIR)
	chown root.$(HTTP_GROUP) $(BIN_DIR) -R
	chmod 750 $(BIN_DIR) -R
	cat /etc/crontab | grep "$(BIN_DIR)/notifyendsubs" > /dev/null || echo "0 0 * * * root $(BIN_DIR)/notifyendsubs" >> /etc/crontab
	cat /etc/crontab | grep "$(BIN_DIR)/notifyevents" > /dev/null || echo "* * * * * root $(BIN_DIR)/notifyevents" >> /etc/crontab
	killall -1 crond
	$(MAKE) -C smtp_wrapper install

uninstall: dummy
	$(MAKE) -C smtp_wrapper uninstall
	./uninstall

package: all
	mkdir -p $(BIN_PACKAGE_DIR)/mailnotify
	cp -f Makefile.inst $(BIN_PACKAGE_DIR)/mailnotify/Makefile
	cp -f uninstall $(BIN_PACKAGE_DIR)/mailnotify
	cp -f notifyendsubs $(BIN_PACKAGE_DIR)/mailnotify
	cp -f notifyevents $(BIN_PACKAGE_DIR)/mailnotify
	$(MAKE) -C smtp_wrapper package

dummy:
