export PRIV_DIR2=$(PRIV_DIR)/classes

all: index.xql add.xql do_add.xql del.xql do_del.xql translate.xql do_translate.xql

%.xql: %.m4 ../macros.m4
	m4 ../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR2)
	cp *.xql $(PRIV_DIR2)/

clean:
	rm -f *.xql *~

package: $(ALL)
	@if [ "$(PACKAGE_TYPE)" = "binary" ]; then echo "This is a binary package"; exit 1; fi
	mkdir -p $(PACKAGE_DIR)/classes
	cp -f Makefile $(PACKAGE_DIR)/classes
	cp -f *.xql $(PACKAGE_DIR)/classes
