.PHONY: lint test test-unit test-functional test-coverage test-coverage-clover
.PHONY: server-start server-stop


deps: ## Install php dependencies
	@docker-compose run --rm composer install --prefer-dist

deps-update: ## Update php dependencies
	@docker-compose run --rm composer install --prefer-dist


lint: ## Run phpcs against the code.
	@docker-compose run --rm test vendor/bin/phpcs -p --warning-severity=0 --ignore=test/server src/ test/


test: ## Run the unit tests
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
