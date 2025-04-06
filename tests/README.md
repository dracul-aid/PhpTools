# Unit тесты

## Запуск конкретного теста

Запускает тесты из указанного класса (пример)
```bash
php tests/run.php tests/ExceptionTools/ResultExceptionTest.php
```

Запуск теста, с очисткой предыдущего результата в консоли (пример)
```bash
clear && php tests/run.php tests/ExceptionTools/ResultExceptionTest.php
```

## Запуск всех тестов

```bash
php tests/run.php tests
```

Запуск теста, с очисткой предыдущего результата в консоли
```bash
clear && php tests/run.php tests
```

## Docker для тестов

`docker-compose.yml` содержит все необходимое для проведения тестирования (PHP 8.1.x)

```shell
cd PhpTools/tests
docker-compose build
docker-compose up -d
```

Для входа в консоль запущенного контейнера
```shell
docker-compose exec php81 bash
```

## Запуск PSALM проверок

Для запуска PSALM проверок необходимо запустить
```shell
./vendor/bin/psalm
```