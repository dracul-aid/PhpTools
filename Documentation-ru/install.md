# Установка

Установка с помощью композера ([packagist.org](https://packagist.org/packages/draculaid/phptools)):

```shell
composer require draculaid/phptools
```

Или прописать в `composer.json`
```json
{
  "require": {
    "draculaid/phptools": "^1.0.0"
  }
}
```

Обновление
```shell
composer update draculaid/phptools
```

Для установки последней нестабильной версии, прописать в `composer.json`
```json
{
  "require": {
    "draculaid/phptools": "dev-master"
  }
}
```

## Поддержка работы без композера

Библиотека не имеет зависимостей и может работать автономно без композера, но в таком случае требует реализации автоматической
загрузки классов. Для начала работы необходимо сохранить содержимое каталога [src](../src) в ваш проект (и при необходимости,
настроить ваш загрузчик классов на загрузку классов из пространства имен `draculaid/phptools`)

## Версия под PHP 7.4

* Последний релиз под PHP 7.4 - [v0.7.0](https://github.com/dracul-aid/PhpTools/releases/tag/v0.7.0)
* Ветка разработки под PHP 7.4 - [master-0x](https://github.com/dracul-aid/PhpTools/tree/master-0x)