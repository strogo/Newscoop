include ../../make.env

all:

install: all
	@./install-db

drop-db:
	@echo -n "Dropping old database..."
	@echo "y" | mysqladmin drop campsite -u root 1> /dev/null
	@echo "done."

clean:

uninstall: dummy
	./uninstall

package: all
	@[ "$(PACKAGE_TYPE)" = "binary" ] && echo "This is a binary package" && exit 1
	mkdir -p $(BIN_PACKAGE_DIR)/database
	cp -f Makefile $(BIN_PACKAGE_DIR)/database
	cp -f install-db $(BIN_PACKAGE_DIR)/database
	cp -f uninstall $(BIN_PACKAGE_DIR)/database
	cp -f campsite-db.sql $(BIN_PACKAGE_DIR)/database

dummy:
