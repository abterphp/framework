install:
ifeq (,$(wildcard /usr/local/bin/composer))
	./bin/composer-install.sh
	mv composer.phar /usr/local/bin/composer
endif
	XDEBUG_MODE=off composer install --no-progress --prefer-dist --optimize-autoloader

update:
	XDEBUG_MODE=off composer update

build:
	XDEBUG_MODE=off ./vendor/bin/phpunit
	XDEBUG_MODE=off ./vendor/bin/phpcs -p --colors --cache src
	XDEBUG_MODE=off ./vendor/bin/phpcs -p --colors --cache --standard=PSR12 tests
	XDEBUG_MODE=off ./vendor/bin/phpmd src text .phpmd.xml
	XDEBUG_MODE=off ./vendor/bin/phan --color

precommit:
	git diff --cached --name-only --diff-filter=ACM | grep \\.php | xargs -n 1 php -l
	XDEBUG_MODE=off ./vendor/bin/phpunit
	XDEBUG_MODE=off ./vendor/bin/phpcs -p --colors --cache src
	XDEBUG_MODE=off ./vendor/bin/phpcs -p --colors --cache --standard=PSR12 tests

unit:
	XDEBUG_MODE=off ./vendor/bin/phpunit

coverage:
	XDEBUG_MODE=coverage ./vendor/bin/phpunit -c phpunit-cov.xml

pull:
	git pull
	git submodule update --recursive --remote

.PHONY: install update build precommit unit integration coverage pull
