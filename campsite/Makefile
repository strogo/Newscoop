include ./make.env

all:
	$(MAKE) -C implementation all
	$(MAKE) -C supplemental all

install: all
	$(MAKE) -C implementation install
	$(MAKE) -C supplemental install

clean:
	$(MAKE) -C implementation clean
	$(MAKE) -C supplemental clean
	rm -f install_log uninstall_log

distclean: clean
	rm -f make.env

uninstall: dummy
	$(MAKE) -C implementation uninstall
	$(MAKE) -C supplemental uninstall
	if [ -d $(BIN_DIR) ]; then rmdir $(BIN_DIR); fi
	if [ -d $(SCRIPT_DIR) ]; then rmdir $(SCRIPT_DIR); fi

package: dummy
	@[ "$(PACKAGE_TYPE)" = "binary" ] && echo "This is a binary package" && exit 1
	rm -fr $(BIN_PACKAGE_DIR)
	mkdir -p $(BIN_PACKAGE_DIR)
	cp -f Makefile $(BIN_PACKAGE_DIR)
	cp -f install $(BIN_PACKAGE_DIR)
	cp -f .inst_components-package $(BIN_PACKAGE_DIR)/.inst_components
	cp -f uninstall $(BIN_PACKAGE_DIR)
	cp -f AUTHORS $(BIN_PACKAGE_DIR)
	cp -f COPYING $(BIN_PACKAGE_DIR)
	cp -f INSTALL $(BIN_PACKAGE_DIR)
	cp -f README $(BIN_PACKAGE_DIR)
	cp -f ChangeLog $(BIN_PACKAGE_DIR)
	cp -f thisisacampsite.gif $(BIN_PACKAGE_DIR)
	cp -fr ./documentation $(BIN_PACKAGE_DIR)
	$(MAKE) -C implementation package
	$(MAKE) -C supplemental package
	echo "version: [$(CAMPSITE_VERSION)]"
	cd .package; tar czf campsite-$(CAMPSITE_VERSION).$(PROCESSOR_TYPE).tar.gz campsite; mv campsite-*.tar.gz ..; cd ..; rm -fr .package
	echo "Packge file built: campsite-$(CAMPSITE_VERSION).$(PROCESSOR_TYPE).tar.gz."

dummy:
