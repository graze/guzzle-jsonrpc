.PHONY: lint test test-unit test-functional test-coverage test-coverage-clover
.PHONY: server-start server-stop

all: deps

lint: ## Run phpcs against the code.
	@docker-compose run --rm test vendor/bin/phpcs -p --warning-severity=0 --ignore=test/server src/ test/

deps:
	@docker-compose run --rm composer install --prefer-dist

test:
	@docker-compose run --rm test vendor/bin/phpunit --testsuite unit

test-coverage: ## Run all tests and output coverage to the console.
	@docker-compose run --rm test phpdbg7 -qrr vendor/bin/phpunit --coverage-text

test-coverage-clover: ## Run all tests and output clover coverage to file.
	@docker-compose run --rm test phpdbg7 -qrr vendor/bin/phpunit --coverage-clover=./tests/report/coverage.clover
