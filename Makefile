PWD := `pwd`
PHP := `which php`
PID := $(PWD)/.pid

.PHONY: cs test

all: deps

cs:
	@vendor/bin/php-cs-fixer fix src

deps:
	@composer install

server-start:
	@start-stop-daemon -S -b -m -o -p $(PID) -x $(PHP) -- -S 0.0.0.0:8000 $(PWD)/test/server/index.php

server-stop:
	@start-stop-daemon -K -p $(PID)

test: test-unit test-functional

test-functional: server-start
	@vendor/bin/phpunit --testsuite functional
	@$(MAKE) server-stop

test-unit:
	@vendor/bin/phpunit --testsuite unit
