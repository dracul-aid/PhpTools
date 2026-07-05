# Правила тестирования

## Где находятся тесты

Юнит-тесты находятся в `tests/`.

Тесты используют namespace `DraculAid\PhpTools\tests\` и почти всегда повторяют структуру каталога `src/`.

## Запуск тестов

Юнит-тесты и Psalm-проверки нужно запускать только через WSL и Docker,
используя образ `test-php-tools` и не добавляя флаг `-it`.
Команды `make test` и `make psalm` из `tests/Makefile` используют `docker run -it`, поэтому могут падать
в неинтерактивной сессии с ошибкой `the input device is not a TTY`.

Перед запуском команд из PowerShell перейди в корень репозитория и получи путь к нему в формате WSL:

```powershell
$projectPath = wsl -e wslpath -a (Get-Location).Path
```

Сборка Docker-образа из PowerShell:

```powershell
wsl -e bash -lc "cd '$projectPath/tests' && docker build -t test-php-tools ."
```

Запуск одного тестового файла из PowerShell:

```powershell
wsl -e bash -lc "docker run --rm -v '${projectPath}:/app' test-php-tools php tests/run.php tests/Numeric/IntToStringToolsTest.php"
```

Запуск всех тестов из PowerShell:

```powershell
wsl -e bash -lc "docker run --rm -v '${projectPath}:/app' test-php-tools php tests/run.php tests"
```

Psalm-проверка:

```powershell
wsl -e bash -lc "docker run --rm -v '${projectPath}:/app' test-php-tools ./vendor/bin/psalm"
```

## Правила для новых и измененных тестов

- Для новой логики добавляй тесты.
- Для багфикса добавляй регрессионный тест, если это возможно.
- Размещай тест рядом с тестами той же области проекта.
- Называй тестовые классы с суффиксом `Test`.
- Не удаляй и не ослабляй существующие проверки без причины.
- Если тест зависит от даты и времени, учитывай, что `tests/run.php` устанавливает timezone `UTC`.
- Если тесты не запускались, в ответе явно укажи причину.
