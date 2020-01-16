install:
	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
	php -r "if (hash_file('sha384', 'composer-setup.php') === 'c5b9b6d368201a9db6f74e2611495f369991b72d9c8cbd3ffbc63edff210eb73d46ffbfce88669ad33695ef77dc76976') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
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
