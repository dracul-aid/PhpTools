# PhpTools

Библиотека с набором полезных инструментов для PHP

* [Classes](/src/Classes) Для работы с классами и объектами
  * [Patterns](/src/Classes/Patterns) Заготовки для реализации паттернов
    * [Singleton\SingletonFactory](/src/Classes/Patterns/Singleton/SingletonFactory.php) Фабрика, для получения синглтон
      объектов, для любых классов 
    * [Singleton\SingletonInterface](/src/Classes/Patterns/Singleton/SingletonInterface.php) Интерфейс, для типизации
      синглтон классов
    * [Singleton\SingletonTrait](/src/Classes/Patterns/Singleton/SingletonTrait.php) Трейт, для реализации синглтон классов
  * [ClassNotPublicManager](/src/Classes/ClassNotPublicManager.php) Набор функций для взаимодействия с непубличными
    элементами классов (методами, свойствами и константами), в том числе и со статическими
  * [ClassParents](/src/Classes/ClassParents.php) Позволяет получить информацию о родителях класса (включая информацию по трейтам) 
  * [ClassTools](/src/Classes/ClassTools.php) Инструменты для работы с классами
  * [ObjectTools](/src/Classes/ObjectTools.php) Инструменты для работы с объектами
* [ExceptionTools](/src/ExceptionTools) Для работы с исключениями
  * [ExceptionTools](/src/ExceptionTools/ExceptionTools.php) Набор функций облегчающий перехват исключений
  * [ResultException](/src/ExceptionTools/ResultException.php) Всплывающий результат работы
* [Strings](/src/Strings) Инструменты для работы со строками и символами
  * [Utf8Iterator](/src/Strings/Utf8Iterator.php) Итератор и генератор, для перебора UTF8 строк
  * [CharTools](/src/Strings/CharTools.php) Полезные функции для работы с символами (до 127 кода)

---

## Установка

Установка с помощью композера ([packagist.org](https://packagist.org/packages/draculaid/phptools)):

```shell
composer require draculaid/phptools
```

Или прописать в `composer.json`
```json
{
  "require": {
    "draculaid/phptools": "^0.0"
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

---

## Дерево каталогов

* [src](/src) Каталог с классами
* [tests](/tests) Юнит-тесты, см [Запуск юнит тестов](/tests/README.md)
