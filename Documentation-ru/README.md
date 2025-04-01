# Оглавление - PhpTools v 0.7

* [Установка](install.md)
* [Лицензия](../LICENSE) (Apache License Version 2.0)
* [/src](../src) Каталог с классами
* [/tests](../tests) Каталог с Юнит-тестами, см [Запуск юнит тестов](/tests/README.md)

## Содержимое библиотеки

### [Classes](../src/Classes) Для работы с классами и объектами

* [Patterns](../src/Classes/Patterns) Заготовки для реализации паттернов
  * [Singleton](../src/Classes/Patterns/Singleton) Для работы с Singleton-ами
      * [SingletonFactory](../src/Classes/Patterns/Singleton/SingletonFactory.php) Фабрика, для получения синглтон
        объектов, для любых классов
      * [SingletonInterface](../src/Classes/Patterns/Singleton/SingletonInterface.php) Интерфейс, для типизации
        синглтон классов
      * [SingletonTrait](../src/Classes/Patterns/Singleton/SingletonTrait.php) Трейт, для реализации синглтон классов
  * [Runner](../src/Classes/Patterns/Runner) Классы-раннеры (т.е. классы "функции", выполняют одно конкретное действие)
    * [RunnerInterface](../src/Classes/Patterns/Runner/RunnerInterface.php) Раннер, метод `run()`
    * [StaticRunnerInterface](../src/Classes/Patterns/Runner/StaticRunnerInterface.php) Статический раннер, метод `exe()`
  * [Iterator](../src/Classes/Patterns/Iterator) Классы-итераторы
      * [IteratorInterface](../src/Classes/Patterns/Iterator/IteratorInterface.php) Интерфейс для итераторов
      * [AbstractIterator](../src/Classes/Patterns/Iterator/AbstractIterator.php) Абстрактный класс для итераторов
      * [IteratorTrait](../src/Classes/Patterns/Iterator/IteratorTrait.php) Трейт для реализации "сахара" для итераторов
* [ClassNotPublicManager](../src/Classes/ClassNotPublicManager.php) Набор функций для взаимодействия с непубличными
  элементами классов (методами, свойствами и константами), в том числе и со статическими
* [ClassParents](../src/Classes/ClassParents.php) Позволяет получить информацию о родителях класса (включая информацию по трейтам)
* [ClassTools](../src/Classes/ClassTools.php) Инструменты для работы с классами
* [ObjectTools](../src/Classes/ObjectTools.php) Инструменты для работы с объектами

### [Code](../src/Code) Функционал для работы с кодом

* [DebugVarTools](../src/Code/DebugVarTools.php) Инструменты для отладки значений
* [DebugVarHtmlTools](../src/Code/DebugVarHtmlTools.php) Инструменты для отладки значений в HTML формате
* [CallFunctionHelper](../src/Code/CallFunctionHelper.php) Хэлпер для вызова функций и языковых конструкций
* [FunctionAsPropertyObject](../src/Code/FunctionAsPropertyObject.php) Объект-функция, позволяет использовать функции и
  конструкции языка в качестве типа свойств классов
* [ObHelper](../src/Code/ObHelper.php) Позволяет вызывать функции с перехватом потока вывода
* [ScriptLoader](../src/Code/ScriptLoader.php) Позволяет вызывать скрипты с перехватом потока вывода

### [ExceptionTools](../src/ExceptionTools) Для работы с исключениями

* [PhpErrorCode](../src/ExceptionTools/PhpErrorCode) Полезные функции для работы с кодами PHP ошибок
  * [Errors](../src/ExceptionTools/PhpErrorCode/Errors) Классы ошибок (для кодов ошибок)
  * [Descriptions](../src/ExceptionTools/PhpErrorCode/Descriptions) Описания кодов ошибок
    * [PhpErrorCodeRuDescriptionsConstants](../src/ExceptionTools/PhpErrorCode/Descriptions/PhpErrorCodeRuDescriptionsConstants.php) Описание на Русском
    * [PhpErrorCodeEnDescriptionsConstants](../src/ExceptionTools/PhpErrorCode/Descriptions/PhpErrorCodeEnDescriptionsConstants.php) Описание на Английском
  * [PhpErrorCodeConstants](../src/ExceptionTools/PhpErrorCode/PhpErrorCodeConstants.php) Константы с кодами ошибок, разбивка ошибок по типам
  * [PhpErrorCodeThrowableTools](../src/ExceptionTools/PhpErrorCode/PhpErrorCodeThrowableTools.php) Получение объектов ошибок по коду ошибки
* [ExceptionTools](../src/ExceptionTools/ExceptionTools.php) Набор функций облегчающий перехват исключений
* [ResultException](../src/ExceptionTools/ResultException.php) Всплывающий результат работы

### [DateTime](../src/DateTime) Для работы с датой и временем

* [Dictionary](../src/DateTime/Dictionary) Словари
    * [DateConstants](../src/DateTime/Dictionary/DateConstants.php) Константы элементов даты (года, месяца, недели)
    * [DateTimeFormats](../src/DateTime/Dictionary/DateTimeFormats.php) Маски форматирования даты для date()
    * [DaysDictionary](../src/DateTime/Dictionary/DaysDictionary.php) Константы дней недели
    * [TimestampConstants](../src/DateTime/Dictionary/TimestampConstants.php) Константы для работы с таймштампами (кол-во
      секунд в году, месяце, дне...)
* [Types](../src/DateTime/Types) Объекты
    * [Ranges](../src/DateTime/Types/Ranges) Диапазоны даты-времени
        * [DateTimeRangeInterface](../src/DateTime/Types/Ranges/DateTimeRangeInterface.php) Интерфейс для диапазонов даты-времени
        * [TimestampRangeType](../src/DateTime/Types/Ranges/TimestampRangeType.php) Диапазон на основе таймштампов
        * [DateTimeRangeType](../src/DateTime/Types/Ranges/DateTimeRangeType.php) Диапазон на основе объектов `\DateTime`
        * [DateTimeExtendedRangeType](../src/DateTime/Types/Ranges/DateTimeExtendedRangeType.php) Диапазон на основе объектов
          `...\DateTime\Types\PhpExtended\DateTimeExtendedRangeType`
    * [PhpExtended](../src/DateTime/Types/PhpExtended) Расширение базовых PHP объектов даты-времени (в том числе и `\DateTime`)
        * [DateTimeExtendedType](../src/DateTime/Types/PhpExtended/DateTimeExtendedType.php) Расширение объекта `\DateTime`
    * [GetTimestampInterface](../src/DateTime/Types/GetTimestampInterface.php) Интерфейс для объектов, возвращающих таймштампы
      или текстовое представление даты-времени
    * [DateTimeLightDto](../src/DateTime/Types/DateTimeLightDto.php) DTO для хранения даты-времени (ГГГГ-ММ-ДД ЧЧ-ММ-СС)
    * [TimestampType](../src/DateTime/Types/TimestampType.php) Объект для работы с таймштампом
* [DateTimeObjectHelper](../src/DateTime/DateTimeObjectHelper.php) Функции для работы с объектами даты-времени
* [DateTimeValidator](../src/DateTime/DateTimeValidator.php) Валидация даты-времени
* [NowTimeGetter](../src/DateTime/NowTimeGetter.php) Предоставление элементов текущей даты-времени
* [TimestampHelper](../src/DateTime/TimestampHelper.php) Функции для работы с таймштампами
* [SecondsToHelper](../src/DateTime/SecondsToHelper.php) Получение из секунд минут, часов и дней
* [DateTimeHelper](../src/DateTime/DateTimeHelper.php) Прочие функции для работы с датой временем

### [Strings](../src/Strings) Инструменты для работы со строками и символами

* [Components](../src/Strings/Components) Компоненты инструментов
* [Objects/StringIterator](../src/Strings/Objects/StringIterator) Объекты для перебора строк
    * [StringIteratorInterface](../src/Strings/Objects/StringIterator/StringIteratorInterface.php) Интерфейс итераторов-строк
    * [StringIteratorObject](../src/Strings/Objects/StringIterator/StringIteratorObject.php) Итератор для строк с фиксированным размером символа 
    * [Utf8IteratorObject](../src/Strings/Objects/StringIterator/Utf8IteratorObject.php) Итератор для PHP8 строк
* [ArrayAndStringTools](../src/Strings/ArrayAndStringTools.php) Преобразование массивов в строки 
* [CharTools](../src/Strings/CharTools.php) Полезные функции для работы с символами (до 127 кода)
* [Utf8Tools](../src/Strings/Utf8Tools.php) Полезные функции для работы с UTF-8
* [StringCutTools](../src/Strings/StringCutTools.php) Обрезание строк
* [StringSearchTools](../src/Strings/StringSearchTools.php) Поиск подстрок внутри строки
* [StringTools](../src/Strings/StringTools.php) Полезные функции для работы со строками
* [TranslitConverter](../src/Strings/TranslitConverter.php) Конвертер в транслит

### [Arrays](../src/Arrays) Инструменты для работы с массивами и массивоподобными объектами

* [Objects](../src/Arrays/Objects) Объекты для работы с массивами
    * [ArrayInterface](../src/Arrays/Objects/Interfaces) Интерфейсы
        * [ArrayInterface](../src/Arrays/Objects/Interfaces/ArrayInterface.php) Интерфейс для типизации объектов, схожих с массивами
    * [ListObject](../src/Arrays/Objects/ListObject.php) Списки (массив, с последовательными числовыми ключами)
    * [IteratorSafeRunner](../src/Arrays/Objects/IteratorSafeRunner.php) Безопасный перебор итераторов \Iterator
* [ArrayHelper](../src/Arrays/ArrayHelper.php) Хэлпер для работы с массивами
* [ArrayIterator](../src/Arrays/ArrayIterator.php) Итераторы, для облегчения перебора массивов

### [Console](../src/Console) Для работы с консолью

* [ConsoleArgumentsObject](../src/Console/ConsoleArgumentsObject.php) Объекты для работы с аргументами консольных команд
* [ConsoleArgumentsFromPhpArgvCreator](../src/Console/ConsoleArgumentsFromPhpArgvCreator.php) Получение объекта с аргументами консольных команд из `$_SERVER['argv']` 

### [TestTools](../src/TestTools) Инструменты для облегчения юнит-тестов

* [PhpUnit](../src/TestTools/PhpUnit) Инструменты для облегчения работы с PhpUnit
    * [PhpUnitExtendTestCase](../src/TestTools/PhpUnit/PhpUnitExtendTestCase.php) Базовый класс для PhpUnit юнит-тестов 