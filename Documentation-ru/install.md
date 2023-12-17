# Установка

Установка с помощью композера ([packagist.org](https://packagist.org/packages/draculaid/phptools)):

```shell
composer require draculaid/phptools
```

Или прописать в `composer.json`
```json
{
  "require": {
    "draculaid/phptools": "^0.3"
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
загрузки классов.