export PRIV_DIR2=$(PRIV_DIR)/templates

MY_TARGETS=index.php idx.php new_dir.php do_new_dir.php list.sh stempl.sh upload_templ.php process_t.php del.php do_del.php do_upload_templ.php

all: $(MY_TARGETS)

%.php: %.m4 ../macros.m4
	m4 ../macros.m4 $< > $@

%.sh: %.m4 ../macros.m4
	m4 ../macros.m4 $< > $@

%.html: %.m4 ../macros.m4
	m4 ../macros.m4 $< > $@

install: $(ALL)
	mkdir -p $(PRIV_DIR2)
	cp -f *.php $(PRIV_DIR2)/
	cp -f list.sh $(SCRIPT_DIR)/list
	cp -f make_dir.sh $(SCRIPT_DIR)/make_dir
	cp -f stempl.sh $(SCRIPT_DIR)/stempl
	cp -f upload_t.sh $(CGI_DIR)/upload_t
	cp -f delete.sh $(SCRIPT_DIR)/delete
	chmod 755 $(SCRIPT_DIR)/list
	chmod 755 $(SCRIPT_DIR)/make_dir
	chmod 755 $(SCRIPT_DIR)/stempl
	chmod 755 $(CGI_DIR)/upload_t
	chmod 755 $(SCRIPT_DIR)/delete

clean:
	rm -f *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f $(MY_TARGETS); fi

uninstall: dummy
	rm -f $(SCRIPT_DIR)/list $(SCRIPT_DIR)/make_dir $(SCRIPT_DIR)/stempl $(CGI_DIR)/upload_t $(SCRIPT_DIR)/delete

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/templates
	cp -f Makefile $(PACKAGE_DIR)/templates
	cp -f *.php $(PACKAGE_DIR)/templates
	cp -f *.sh $(PACKAGE_DIR)/templates

dummy:
