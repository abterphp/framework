install:
	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
	php -r "if (hash_file('sha384', 'composer-setup.php') === 'baf1608c33254d00611ac1705c1d9958c817a1a33bce370c0595974b342601bd80b92a3f46067da89e3b06bff421f182') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
	php composer-setup.php
	php -r "unlink('composer-setup.php');"
	php composer.phar install

update:
	php composer.phar update

build:
	./vendor/bin/phpunit
	./vendor/bin/phpcs src
	./vendor/bin/phpcs -p --colors --cache --standard=PSR12 tests
	./vendor/bin/phpmd src text .phpmd.xml
	PHAN_DISABLE_XDEBUG_WARN=1 ./vendor/bin/phan --color

precommit:
	git diff --cached --name-only --diff-filter=ACM | grep \\.php | xargs -n 1 php -l
	./vendor/bin/phpunit
	./vendor/bin/phpcs src
	./vendor/bin/phpcs -p --colors --cache --standard=PSR12 tests

unit:
	./vendor/bin/phpunit

coverage:
	./vendor/bin/phpunit -c phpunit-cov.xml

.PHONY: install update build precommit unit integration coverage
