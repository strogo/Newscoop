export PRIV_DIR2=$(PRIV_DIR)/languages

all: index.xql add.xql do_add.xql del.xql do_del.xql modify.xql do_modify.xql

%.xql: %.m4 ../macros.m4
	m4 ../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR2)
	cp *.xql $(PRIV_DIR2)/

clean:
	rm -f *.xql *~

package: $(ALL)
	@if [ "$(PACKAGE_TYPE)" = "binary" ]; then echo "This is a binary package"; exit 1; fi
	mkdir -p $(PACKAGE_DIR)/languages
	cp -f Makefile $(PACKAGE_DIR)/languages
	cp -f *.xql $(PACKAGE_DIR)/languages
