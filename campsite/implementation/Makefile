include ../make.env

all:
	for part in $(IMPLEMENTATION_PARTS); do $(MAKE) -C $$part; done

install:
	for part in $(IMPLEMENTATION_PARTS); do $(MAKE) -C $$part install; done

clean:
	for part in $(IMPLEMENTATION_PARTS); do $(MAKE) -C $$part clean; done

uninstall: dummy
	for part in $(IMPLEMENTATION_PARTS); do $(MAKE) -C $$part uninstall; done

package: dummy
	@[ "$(PACKAGE_TYPE)" = "binary" ] && echo "This is a binary package" && exit 1
	mkdir -p $(BIN_PACKAGE_DIR)/implementation
	cp -f Makefile $(BIN_PACKAGE_DIR)/implementation
	for part in $(IMPLEMENTATION_PARTS); do \
	    $(MAKE) -C $$part package BIN_PACKAGE_DIR=$(BIN_PACKAGE_DIR)/implementation \
	done

dummy:
