include ../../make.env

all:
	$(MAKE) -C tpl_cgi

install:
	$(MAKE) -C tpl_cgi install

clean:
	$(MAKE) -C tpl_cgi clean

uninstall: dummy
	$(MAKE) -C tpl_cgi uninstall

package: dummy
	@if [ "$(PACKAGE_TYPE)" = "binary" ]; then echo "This is a binary package"; exit 1; fi
	$(MAKE) -C tpl_cgi package

dummy:
