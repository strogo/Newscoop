include ./make.env

all:
	$(MAKE) -C implementation all
	$(MAKE) -C supplemental all

install: dummy
	$(MAKE) -C implementation install
	$(MAKE) -C supplemental install
	chown $(ROOT_USER).$(HTTP_GROUP) $(CONF_DIR) -R
	chmod 750 $(BIN_DIR)
	chown $(ROOT_USER).$(HTTP_GROUP) $(BIN_DIR) -R
	chmod 750 $(BIN_DIR)
	chown $(ROOT_USER).$(HTTP_GROUP) $(PRIV_DIR) -R
	chmod 750 $(PRIV_DIR)
	chown $(ROOT_USER).$(HTTP_GROUP) $(SCRIPT_DIR) -R
	chmod 750 $(SCRIPT_DIR)
	chown $(HTTP_USER).$(HTTP_GROUP) $(HTML_DIR)/look -R
	chmod 755 $(HTML_DIR)/look

clean:
	$(MAKE) -C implementation clean
	$(MAKE) -C supplemental clean
	rm -f install_log uninstall_log
	rm -fr .package

distclean: clean
	rm -f make.env

uninstall: dummy
	$(MAKE) -C implementation uninstall
	$(MAKE) -C supplemental uninstall
	if [ -d $(BIN_DIR) ]; then rmdir $(BIN_DIR); fi
	if [ -d $(SCRIPT_DIR) ]; then rmdir $(SCRIPT_DIR); fi

package: dummy
	@if [ "$(PACKAGE_TYPE)" = "binary" ]; then echo "This is a binary package"; exit 1; fi
	rm -fr $(BIN_PACKAGE_DIR)
	mkdir -p $(BIN_PACKAGE_DIR)
	echo "binary" > $(BIN_PACKAGE_DIR)/.package_type
	cp -f configure $(BIN_PACKAGE_DIR)
	cp -f Makefile $(BIN_PACKAGE_DIR)
	cp -f install $(BIN_PACKAGE_DIR)
	cp -f uninstall $(BIN_PACKAGE_DIR)
	cp -f AUTHORS $(BIN_PACKAGE_DIR)
	cp -f COPYING $(BIN_PACKAGE_DIR)
	cp -f INSTALL $(BIN_PACKAGE_DIR)
	cp -f README $(BIN_PACKAGE_DIR)
	cp -f ChangeLog $(BIN_PACKAGE_DIR)
	cp -f thisisacampsite.gif $(BIN_PACKAGE_DIR)
	cp -fr ./documentation $(BIN_PACKAGE_DIR)
	cp -fr ./.install_conf $(BIN_PACKAGE_DIR)
	$(MAKE) -C implementation package
	$(MAKE) -C supplemental package
	cd .package; tar czf campsite-$(CAMPSITE_VERSION).$(PROCESSOR_TYPE).tar.gz campsite; mv campsite-*.tar.gz ..; cd ..; rm -fr .package
	echo "Packge file built: campsite-$(CAMPSITE_VERSION).$(PROCESSOR_TYPE).tar.gz."

dummy:
