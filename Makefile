build:
	XDEBUG_MODE=off ./vendor/bin/phpunit
	XDEBUG_MODE=off ./vendor/bin/phpcs -p --colors --cache src
	XDEBUG_MODE=off ./vendor/bin/phpcs -p --colors --cache --standard=PSR12 tests
	XDEBUG_MODE=off ./vendor/bin/phpmd src text .phpmd.xml
	XDEBUG_MODE=off ./vendor/bin/phan --color
	XDEBUG_MODE=off composer install --no-progress --prefer-dist --optimize-autoloader

precommit:
	XDEBUG_MODE=off ./vendor/bin/phpunit
	XDEBUG_MODE=off ./vendor/bin/phpcs -p --colors --cache src
	XDEBUG_MODE=off ./vendor/bin/phpcs -p --colors --cache --standard=PSR12 tests
	XDEBUG_MODE=off ./vendor/bin/phpmd src text .phpmd.xml
	XDEBUG_MODE=off composer install --no-progress --prefer-dist --optimize-autoloader

install:
ifeq (,$(wildcard /usr/local/bin/composer))
	./bin/composer-install.sh
	mv composer.phar /usr/local/bin/composer
endif
ifeq (,$(wildcard /usr/local/bin/php-coveralls))
	curl -L --output php-coveralls.phar https://github.com/php-coveralls/php-coveralls/releases/download/v2.4.3/php-coveralls.phar
	mv php-coveralls.phar /usr/local/bin/php-coveralls
	chmod +x /usr/local/bin/php-coveralls
endif
	XDEBUG_MODE=off composer install --no-progress --prefer-dist --optimize-autoloader

update:
	XDEBUG_MODE=off composer update

unit:
	XDEBUG_MODE=off ./vendor/bin/phpunit -v

coverage:
	XDEBUG_MODE=coverage ./vendor/bin/phpunit -c phpunit-cov.xml
	XDEBUG_MODE=off php-coveralls -vvv --coverage_clover=./tmp/report/clover.xml --json_path=./tmp/report/coveralls-upload.json

pull:
	git pull
	git submodule update --recursive --remote

.PHONY: install update build precommit unit integration coverage pull
