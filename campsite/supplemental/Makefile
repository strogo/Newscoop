include ../make.env

ALL=$(shell if [ "$(PACKAGE_TYPE)" = "source" ]; then echo all; fi)

WWWSQL_DIR=www-sql-0.5.7-patch

all:
	@if [ ! -f ./$(WWWSQL_DIR)/Makefile ]; then \
	    (cd ./www-sql-0.5.7-patch; rm -f config.cache; ./configure --enable-apache-action-check --enable-unsafe; cd ..); \
	fi
	$(MAKE) -C $(WWWSQL_DIR) all
	$(MAKE) -C mysql_connect

install: $(ALL)
	cp -f $(WWWSQL_DIR)/www-sql $(CGI_DIR)
	chmod 755 $(CGI_DIR)/www-sql
	$(MAKE) -C mysql_connect install
	if [ -f swingall.jar ]; then \
	    cp swingall.jar $(CLASS_DIR); \
	fi

clean:
	@if [ -f ./$(WWWSQL_DIR)/Makefile ]; then \
	    $(MAKE) -C $(WWWSQL_DIR) distclean; \
	fi
	$(MAKE) -C mysql_connect clean

uninstall: dummy
	rm -f $(CGI_BIN)/www-sql
	$(MAKE) -C mysql_connect uninstall

package: $(ALL)
	@if [ "$(PACKAGE_TYPE)" = "binary" ]; then echo "This is a binary package"; exit 1; fi
	mkdir -p $(PACKAGE_DIR)/supplemental/$(WWWSQL_DIR)
	cp -f Makefile $(PACKAGE_DIR)/supplemental
	cp -f $(WWWSQL_DIR)/www-sql $(PACKAGE_DIR)/supplemental/$(WWWSQL_DIR)
	$(MAKE) -C mysql_connect package

dummy:
