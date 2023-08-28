# PhpTools v0.2.0 | PHP 7.4+ | 26 августа 2023

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
* [DateTime](/src/DateTime) Для работы с датой и временем
  * [Dictionary](/src/DateTime/Dictionary) Словари
    * [DateConstants](/src/DateTime/Dictionary/DateConstants.php) Константы элементов даты (года, месяца, недели)
    * [DateTimeFormats](/src/DateTime/Dictionary/DateTimeFormats.php) Маски форматирования даты для date()
    * [TimestampConstants](/src/DateTime/Dictionary/TimestampConstants.php) Константы для работы с таймштампами (кол-во
      секунд в году, месяце, дне...)
  * [Types](/src/DateTime/Types) Объекты
    * [Ranges](/src/DateTime/Types/Ranges) Диапазоны даты-времени
      * [DateTimeRangeInterface](/src/DateTime/Types/Ranges/DateTimeRangeInterface.php) Интерфейс для диапазонов даты-времени
      * [TimestampRangeType](/src/DateTime/Types/Ranges/TimestampRangeType.php) Диапазон на основе таймштампов
      * [DateTimeRangeType](/src/DateTime/Types/Ranges/DateTimeRangeType.php) Диапазон на основе объектов `\DateTime`
      * [DateTimeExtendedRangeType](/src/DateTime/Types/Ranges/DateTimeExtendedRangeType.php) Диапазон на основе объектов
        `...\DateTime\Types\PhpExtended\DateTimeExtendedRangeType`
    * [PhpExtended](/src/DateTime/Types/PhpExtended) Расширение базовых PHP объектов даты-времени (в том числе и `\DateTime`)
      * [DateTimeExtendedType](/src/DateTime/Types/PhpExtended/DateTimeExtendedType.php) Расширение объекта `\DateTime`
    * [GetTimestampInterface](/src/DateTime/Types/GetTimestampInterface.php) Интерфейс для объектов, возвращающих таймштампы
      или текстовое представление даты-времени
    * [DateTimeLightDto](/src/DateTime/Types/DateTimeLightDto.php) DTO для хранения даты-времени (ГГГГ-ММ-ДД ЧЧ-ММ-СС)
    * [TimestampType](/src/DateTime/Types/TimestampType.php) Объект для работы с таймштампом
  * [DateTimeObjectHelper](/src/DateTime/DateTimeObjectHelper.php) Функции для работы с объектами даты-времени
  * [DateTimeValidator](/src/DateTime/DateTimeValidator.php) Валидация даты-времени
  * [NowTimeGetter](/src/DateTime/NowTimeGetter.php) Предоставление элементов текущей даты-времени
  * [TimestampHelper](/src/DateTime/TimestampHelper.php) Функции для работы с таймштампами
  * [DateTimeHelper](/src/DateTime/DateTimeHelper.php) Прочие функции для работы с датой временем
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
    "draculaid/phptools": "^0.2"
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
* [LICENSE](LICENSE) Файл с лицензией (Apache License Version 2.0)
