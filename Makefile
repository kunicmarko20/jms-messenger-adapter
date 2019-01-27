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
	mkdir --parents "${HOME}/bin"
	wget https://github.com/satooshi/php-coveralls/releases/download/v1.0.1/coveralls.phar --output-document="${HOME}/bin/coveralls"
	chmod u+x "${HOME}/bin/coveralls"
	bin/phpunit --colors --coverage-clover build/logs/clover.xml
	coveralls -v
.PHONY: coverage
