export PRIV_DIR2=$(PRIV_DIR)/dictionary

MY_TARGETS=index.php add.php do_add.php translate.php do_translate.php del.php do_del.php

subsystem: all
	$(MAKE) -C keyword

all: $(MY_TARGETS)

%.php: %.m4 ../macros.m4
	m4 ../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR2)
	cp -f *.php $(PRIV_DIR2)/
	$(MAKE) -C keyword install

clean:
	rm -f *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f $(MY_TARGETS); fi
	$(MAKE) -C keyword clean

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/dictionary
	cp -f Makefile $(PACKAGE_DIR)/dictionary
	cp -f *.php $(PACKAGE_DIR)/dictionary
	$(MAKE) -C keyword package PACKAGE_DIR=$(PACKAGE_DIR)/dictionary
