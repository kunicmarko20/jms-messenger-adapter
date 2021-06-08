all:
	@echo "Please choose a task."
.PHONY: all

e2e:
	bin/behat --strict --colors -vvv
.PHONY: e2e

unit:
	bin/phpunit --colors
.PHONY: unit

coverage:
	bin/phpunit --colors --coverage-clover build/logs/clover.xml
.PHONY: coverage
