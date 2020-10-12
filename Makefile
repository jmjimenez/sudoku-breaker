# force make to use a single shell process
.ONE_SHELL:

#
# User rules
#
install: ## Install all dependencies using composer
	@docker build --tag sudoku-breaker-php-cli:1.0 .
	@docker run --name sudoku-breaker-composer --rm --interactive --tty --volume "$$PWD":/usr/src/myapp sudoku-breaker-php-cli:1.0 composer install

unit-test: ## Run phpunits
	@docker run -it --rm --name sudoku-breaker-phpunit -v "$$PWD":/usr/src/myapp -w /usr/src/myapp sudoku-breaker-php-cli:1.0  php bin/phpunit

run: ## Run console app
	@docker run -it --rm --name sudoku-breaker-run -v "$$PWD":/usr/src/myapp -w /usr/src/myapp php:7.4-cli php bin/console sudoku:solve ./data/samples/$(file)
