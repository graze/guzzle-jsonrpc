DOCKER ?= $(shell which docker)
PHP_VER := 8.0
IMAGE := graze/php-alpine:${PHP_VER}-test
VOLUME := /srv
DOCKER_RUN_BASE := ${DOCKER} run --rm -t -v $$(pwd):${VOLUME} -w ${VOLUME}
DOCKER_RUN := ${DOCKER_RUN_BASE} ${IMAGE}

PREFER_LOWEST ?=

.PHONY: deps deps-js deps-php help
.PHONY: lint test test-unit test-functional test-coverage test-coverage-clover
.PHONY: server-start server-stop

deps: ## Install all dependencies
deps: deps-php deps-js

deps-js: ## Install javascript dependencies
	@docker-compose run --rm node yarn install

deps-php: ## Install php dependencies
deps-php: build

deps-php-update: ## Update php dependencies
deps-php-update: build-update

server-start: ## Start the test server
	@docker-compose up -d node

server-stop: ## Stop the test server
	@docker-compose stop node


## newer build

build: ## Install the dependencies
build: ensure-composer-file
	make 'composer-install --optimize-autoloader --prefer-dist ${PREFER_LOWEST}'

build-update: ## Update the dependencies
build-update: ensure-composer-file
	make 'composer-update --optimize-autoloader --prefer-dist ${PREFER_LOWEST}'

ensure-composer-file: # Update the composer file
	make 'composer-config platform.php ${PHP_VER}'

composer-%: ## Run a composer command, `make "composer-<command> [...]"`.
	${DOCKER} run -t --rm \
        -v $$(pwd):/app:delegated \
        -v ~/.composer:/tmp:delegated \
        -v ~/.ssh:/root/.ssh:ro \
        composer --ansi --no-interaction $* $(filter-out $@,$(MAKECMDGOALS))

##

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
