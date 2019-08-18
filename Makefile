install:
	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
	php -r "if (hash_file('sha384', 'composer-setup.php') === 'a5c698ffe4b8e849a443b120cd5ba38043260d5c4023dbf93e1558871f1f07f58274fc6f4c93bcfd858c6bd0775cd8d1') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
	php composer-setup.php
	php -r "unlink('composer-setup.php');"
	php composer.phar install

update:
	php composer.phar update

build:
	./vendor/bin/phpunit
	./vendor/bin/phpcs
	./vendor/bin/phpcs -p --colors --cache --standard=PSR12 tests
	./vendor/bin/phpmd src text .phpmd.xml
	PHAN_DISABLE_XDEBUG_WARN=1 ./vendor/bin/phan --color

precommit:
	git diff --cached --name-only --diff-filter=ACM | grep \\.php | xargs -n 1 php -l
	./vendor/bin/phpunit
	git diff --cached --name-only --diff-filter=ACM | grep \\.php | xargs -n 1 ./vendor/bin/phpcs -q
	git diff --cached --name-only --diff-filter=ACM | grep Test | grep \\.php | xargs -n 1 ./vendor/bin/phpcs -q -p --colors --cache --standard=PSR12

unit:
	./vendor/bin/phpunit

coverage:
	./vendor/bin/phpunit -c phpunit-cov.xml

.PHONY: install update build precommit unit integration coverage
