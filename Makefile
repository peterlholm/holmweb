# makefile for www.holmnet

env:
	python3 -m venv .venv
	. .venv/bin/activate
	pip install -r requirements.txt

	
.PHONY: django
django:
	. .venv/bin/activate
	python manage.py migrate
	python manage.py collectstatic


.PHONY: apache
apache:
	ln -s /var/www/holmnet/www/apache/apache.conf /etc/apache2/sites-available/www.holmnet.conf
	