# force make to use a single shell process
.ONE_SHELL:

#
# User rules
#
install: ## Install all dependencies using composer
	@docker run --name sudoku-breaker-composer --rm --interactive --tty --volume $PWD:/app  composer install

unit-test: ## Run phpunits
	@docker run -it --rm --name sudoku-breaker-phpunit -v "$$PWD":/usr/src/myapp -w /usr/src/myapp php:7.4-cli php bin/phpunit

run: ## Run console app
	@docker run -it --rm --name sudoku-breaker-run -v "$$PWD":/usr/src/myapp -w /usr/src/myapp php:7.4-cli php bin/console sudoku:solve ./data/samples/$(file)
