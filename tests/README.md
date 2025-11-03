# Unit тесты и PSALM проверки

---

## Тестирование и проверки через Docker

Необходимо собрать образ
```bash
make docker-build
```

Запуск всех тестов
```bash
make test
```

Запускает тесты из указанного класса (пример)
```bash
make test arguments=tests/ExceptionTools/ResultExceptionTest.php
```

---

## Прямая работа с тестами и проверками

Запуск всех тестов
```bash
php tests/run.php tests
```

Запускает тесты из указанного класса (пример)
```bash
php tests/run.php tests/ExceptionTools/ResultExceptionTest.php
```

Для запуска PSALM проверок необходимо запустить
```shell
./vendor/bin/psalm
```