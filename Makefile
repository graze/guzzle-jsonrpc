# Default task
all: install

# Install dependencies
install:
	@composer install --dev

# Run test suites
tests: tests-unit

# Run the unit tests
tests-unit:
	@./vendor/bin/phpunit --testsuite unit

# Run the unit tests
tests-unit-coverage:
	@./vendor/bin/phpunit --testsuite unit --coverage-text --coverage-html ./.report
