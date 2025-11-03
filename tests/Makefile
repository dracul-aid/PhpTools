# Имя Docker-образа
IMAGE_NAME = test-php-tools

# Переменная с общими опциями docker run
DOCKER_RUN = docker run --rm -it -v $(PWD)/../:/app $(IMAGE_NAME)

# Автоматическая помощь: ищет ## после цели
.PHONY: help
help:
	@echo "Используйте команды:"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36mmake %-15s\033[0m %s\n", $$1, $$2}'
	@echo ""

.DEFAULT_GOAL := help

.PHONY: docker-build
docker-build: ## Собрать Docker-образ
	docker build -t $(IMAGE_NAME) .

.PHONY: docker-remove
docker-remove: ## Удалить Docker-образ
	docker rmi $(IMAGE_NAME)

.PHONY: console
console: ## Войти в консоль запущенного контейнера
	$(DOCKER_RUN) /bin/bash

.PHONY: test
test: ## Запустить юнит-тесты `make test` Все тесты или `make test arguments=tests/ExceptionTools/ResultExceptionTest.php` Конкретно указанный тест
	$(DOCKER_RUN) php tests/run.php $(if $(arguments),$(arguments),tests)

.PHONY: psalm
psalm: ## Запустить PSALM проверки
	$(DOCKER_RUN) ./vendor/bin/psalm

.PHONY: exe
exe: ## Запустить PHP-скрипт: make exe script=test.php
	@if [ -z "$(script)" ]; then \
		echo "Ошибка: укажите script=..." && exit 1; \
	fi
	$(DOCKER_RUN) php $(script)