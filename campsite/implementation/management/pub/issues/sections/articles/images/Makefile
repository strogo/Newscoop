export PRIV_DIR6=$(PRIV_DIR5)/images

all: view.xql del.xql do_del.xql add.xql do_add.xql edit.xql do_edit.xql upload_i.sh index.xql select.xql viewsel.xql do_sel.xql clean.sh

%.xql: %.m4 ../../../../../macros.m4
	m4 ../../../../../macros.m4 $< >$@

%.sh: %.m4 ../../../../../macros.m4
	m4 ../../../../../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR6)
	cp -f *.xql $(PRIV_DIR6)/
	cp -f upload_i.sh $(CGI_DIR)/upload_i
	chmod 755 $(CGI_DIR)/upload_i

clean:
	rm -f *.xql *~ *.sh

package: $(ALL)
	@if [ "$(PACKAGE_TYPE)" = "binary" ]; then echo "This is a binary package"; exit 1; fi
	mkdir -p $(PACKAGE_DIR)/images
	cp -f Makefile $(PACKAGE_DIR)/images
	cp -f *.xql $(PACKAGE_DIR)/images
	cp -f *.sh $(PACKAGE_DIR)/images
