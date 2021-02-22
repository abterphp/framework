install:
	./bin/composer-install.sh
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
