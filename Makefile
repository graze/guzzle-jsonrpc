NJS := `which node`
PWD := `pwd`
PID := $(PWD)/.pid

.PHONY: cs test

all: deps

cs:
	@vendor/bin/php-cs-fixer fix src

deps: deps-php deps-js

deps-js:
	@cd test/server && npm install

deps-php:
	@composer install

server-start:
	@start-stop-daemon -S -b -m -o -p $(PID) -d $(PWD)/test/server -x $(NJS) -- index.js

server-stop:
	@start-stop-daemon -K -p $(PID)

test: test-unit test-functional

test-functional: server-start
	@vendor/bin/phpunit --testsuite functional
	@$(MAKE) server-stop

test-unit:
	@vendor/bin/phpunit --testsuite unit
