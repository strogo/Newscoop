CFLAGS=-Wall
LDFLAGS=-L$(LIBMYSQLCLIENT_PATH)
LIBS=-lmysqlclient -lm -lz -lcrypt

GCC=gcc
OBJ=get_img.o ls_url.o url_util.o parse_file.o process_i.o process_t.o cleanb.o
TARGET=get_img ls_url process_i process_t cleanb

all: $(TARGET)

get_img: get_img.o
	$(GCC) -o $@ get_img.o $(LDFLAGS) $(LIBS)
	strip get_img

process_t: process_t.o parse_file.o url_util.o
	$(GCC) -o $@ process_t.o parse_file.o url_util.o
	strip process_t

process_i: process_i.o parse_file.o
	$(GCC) -o $@ process_i.o parse_file.o $(LDFLAGS) $(LIBS)
	strip process_i

ls_url: ls_url.o url_util.o
	$(GCC) -o $@ ls_url.o url_util.o
	strip ls_url

cleanb:	cleanb.o parse_file.o
	$(GCC) -o $@ cleanb.o parse_file.o
	strip cleanb

get_img.o: get_img.c sql_connect.h

url_util.o: url_util.c url_util.h

parse_file.o: parse_file.c parse_file.h

process_i.o: process_i.c parse_file.h sql_connect.h dir_conf.h

process_t.o: process_t.c parse_file.h url_util.h dir_conf.h

cleanb.o: cleanb.c parse_file.h dir_conf.h

clean:
	rm -f *~ core $(OBJ) sql_connect.h dir_conf.h
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f $(TARGET); fi

install: $(ALL)
	cp -f ls_url $(SCRIPT_DIR)/
	chmod +x $(SCRIPT_DIR)/ls_url
	cp -f get_img $(CGI_DIR)/
	chmod 750 $(CGI_DIR)/get_img
	cp -f process_i $(SCRIPT_DIR)/
	chmod 750 $(SCRIPT_DIR)/process_i
	cp -f process_t $(SCRIPT_DIR)/
	chmod 755 $(SCRIPT_DIR)/process_t
	cp -f cleanb $(CGI_DIR)/
	chmod 755 $(CGI_DIR)/cleanb
	chmod u+s $(CGI_DIR)/cleanb

uninstall:
	rm -f $(SCRIPT_DIR)/ls_url $(CGI_DIR)/get_img $(SCRIPT_DIR)/process_i $(SCRIPT_DIR)/process_t $(CGI_DIR)/cleanb

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/src
	cp -f Makefile $(PACKAGE_DIR)/src
	cp -f ls_url $(PACKAGE_DIR)/src
	cp -f get_img $(PACKAGE_DIR)/src
	cp -f process_i $(PACKAGE_DIR)/src
	cp -f process_t $(PACKAGE_DIR)/src
	cp -f cleanb $(PACKAGE_DIR)/src
