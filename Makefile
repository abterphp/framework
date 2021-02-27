install:
	./bin/composer-install.sh
	php composer.phar install --no-progress --prefer-dist --optimize-autoloader

update:
	php composer.phar update

build:
	XDEBUG_MODE=off ./vendor/bin/phpunit
	XDEBUG_MODE=off ./vendor/bin/phpcs src
	XDEBUG_MODE=off ./vendor/bin/phpcs -p --colors --cache --standard=PSR12 tests
	XDEBUG_MODE=off ./vendor/bin/phpmd src text .phpmd.xml
	XDEBUG_MODE=off  ./vendor/bin/phan --color

precommit:
	git diff --cached --name-only --diff-filter=ACM | grep \\.php | xargs -n 1 php -l
	XDEBUG_MODE=off ./vendor/bin/phpunit
	XDEBUG_MODE=off ./vendor/bin/phpcs src
	XDEBUG_MODE=off ./vendor/bin/phpcs -p --colors --cache --standard=PSR12 tests

unit:
	./vendor/bin/phpunit

coverage:
	 XDEBUG_MODE=coverage ./vendor/bin/phpunit -c phpunit-cov.xml

.PHONY: install update build precommit unit integration coverage
