include ../../make.env

PACKAGE_DIR=$(BIN_PACKAGE_DIR)/implementation/database

all: dummy

install: all
	@./install-db

drop-db: uninstall

clean: dummy

uninstall: dummy
	./uninstall

package: all
	@if [ "$(PACKAGE_TYPE)" = "binary" ]; then echo "This is a binary package"; exit 1; fi
	mkdir -p $(PACKAGE_DIR)
	cp -f Makefile $(PACKAGE_DIR)
	cp -f install-db $(PACKAGE_DIR)
	cp -f uninstall $(PACKAGE_DIR)
	cp -f campsite-db.sql $(PACKAGE_DIR)

dummy:
