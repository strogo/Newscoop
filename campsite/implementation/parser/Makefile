include ../../make.env

ALL=$(shell if [ "$(PACKAGE_TYPE)" = "source" ]; then echo all; fi)

PACKAGE_DIR=$(BIN_PACKAGE_DIR)/implementation/parser

all:
	$(MAKE) -C parser

install:
	$(MAKE) -C parser install

clean:
	$(MAKE) -C parser clean

uninstall: dummy
	$(MAKE) -C parser uninstall

package: dummy
	@if [ "$(PACKAGE_TYPE)" = "binary" ]; then echo "This is a binary package"; exit 1; fi
	$(MAKE) -C parser package PACKAGE_DIR=$(PACKAGE_DIR)

dummy:
