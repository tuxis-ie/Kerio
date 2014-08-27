PREFIX=${CURDIR}/debian/python-kerio-api

install: 
	python setup.py install --force --root=$(PREFIX) --no-compile -O0 --install-layout=deb

clean:
	rm -rf $(CURDIR)/build
