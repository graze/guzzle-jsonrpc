.PHONY: deps deps-js deps-php help
.PHONY: lint test test-unit test-functional test-coverage test-coverage-clover
.PHONY: server-start server-stop

all: deps

lint: ## Run phpcs against the code.
	@docker-compose run --rm test vendor/bin/phpcs -p --warning-severity=0 --ignore=test/server src/ test/

deps: deps-php deps-js

deps-js:
	@docker-compose run --rm node yarn install

deps-php:
	@docker-compose run --rm composer install --prefer-dist

server-start:
	@docker-compose up -d node

server-stop:
	@docker-compose stop node

test: test-unit test-functional

test-functional: server-start
	@docker-compose run --rm test vendor/bin/phpunit --testsuite functional
	@$(MAKE) server-stop

test-unit:
	@docker-compose run --rm test vendor/bin/phpunit --testsuite unit

test-coverage: ## Run all tests and output coverage to the console.
	@docker-compose run --rm test phpdbg7 -qrr vendor/bin/phpunit --coverage-text

test-coverage-clover: ## Run all tests and output clover coverage to file.
	@docker-compose run --rm test phpdbg7 -qrr vendor/bin/phpunit --coverage-clover=./tests/report/coverage.clover

help: ## Show this help message.
	@echo "usage: make [target] ..."
	@echo ""
	@echo "targets:"
	@egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -c 2 -s ':#'
