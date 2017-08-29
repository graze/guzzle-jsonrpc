.PHONY: deps deps-js deps-php help
.PHONY: lint test test-unit test-functional test-coverage test-coverage-clover
.PHONY: server-start server-stop


deps: ## Install all dependencies
deps: deps-php deps-js

deps-js: ## Install javascript dependencies
	@docker-compose run --rm node yarn install

deps-php: ## Install php dependencies
	@docker-compose run --rm composer install --prefer-dist

deps-php-update: ## Update php dependencies
	@docker-compose run --rm composer update --prefer-dist

server-start: ## Start the test server
	@docker-compose up -d node

server-stop: ## Stop the test server
	@docker-compose stop node


lint: ## Run phpcs against the code.
	@docker-compose run --rm test vendor/bin/phpcs -p --warning-severity=0 --ignore=test/server src/ test/

test: ## Run all the tests
test: test-unit test-functional

test-functional: ## Test the functionality
test-functional: server-start
	@docker-compose run --rm test vendor/bin/phpunit --testsuite functional
	@$(MAKE) server-stop

test-unit: ## Test the units
	@docker-compose run --rm test vendor/bin/phpunit --testsuite unit

test-coverage: ## Run all tests and output coverage to the console.
test-coverage: server-start
	@docker-compose run --rm test phpdbg7 -qrr vendor/bin/phpunit --coverage-text
	@$(MAKE) server-stop

test-coverage-clover: ## Run all tests and output clover coverage to file.
test-coverage-clover: server-start
	@docker-compose run --rm test phpdbg7 -qrr vendor/bin/phpunit --coverage-clover=./tests/report/coverage.clover
	@$(MAKE) server-stop


help: ## Show this help message.
	@echo "usage: make [target] ..."
	@echo ""
	@echo "targets:"
	@egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -c 2 -s ':#'
