include ../../make.env

JAVA_DIR=$(PRIV_DIR)/pub/issues/sections/articles/java

all:

install:
	mkdir -p $(JAVA_DIR)
	if [ -f *.class ]; then cp -f *.class $(JAVA_DIR); fi
	if [ -f *.jar ]; then cp -f *.jar $(JAVA_DIR); fi
	cp -fr resources $(JAVA_DIR)
	chown $(HTTP_USER).$(HTTP_GROUP) $(JAVA_DIR) -R

clean:

uninstall: dummy
	rm -fr $(JAVA_DIR)

package: dummy
	@[ "$(PACKAGE_TYPE)" = "binary" ] && echo "This is a binary package" && exit 1
	mkdir -p $(BIN_PACKAGE_DIR)/java
	cp -f Makefile $(BIN_PACKAGE_DIR)/java
	cp -f *.jar $(BIN_PACKAGE_DIR)/java
	cp -fr resources $(BIN_PACKAGE_DIR)/java

dummy:
