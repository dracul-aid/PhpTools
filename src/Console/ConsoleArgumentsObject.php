<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Console;

use DraculAid\PhpTools\Arrays\Objects\Interfaces\ArrayInterface;
use DraculAid\PhpTools\Arrays\Objects\ListObject;

/**
 * Объект для работы с аргументами консольных команд.
 *
 * Поддерживает получение аргументов, как по позиции, так и по "имени":
 * <br>- Аргумент `blablabla` имеет номер и значение (`blablabla`)
 * <br>- Аргумент `age=18` имеет имя (`age`) и значение (`18`)
 * <br>- Флаги `-h` или `--help` имеет имя (`-h` или `--help`), а в качестве значения имеет TRUE или строку (если было `-h=abc`)
 *
 * См также {@see ConsoleArgumentsFromPhpArgvCreator} Вернет параметры текущего скрипта (т.е. из `$_SERVER['argv']`)
 * и {@see ConsoleArgumentsFromString} для получения объекта аргументов из строки
 *
 * Оглавление:
 * <br>- {@see self::$script} Имя запущенного скрипта
 * <br>- {@see self::count()} Вернет кол-во аргументов
 * <br>- {@see self::countNames()} Вернет кол-во аргументов с именем
 * <br>- {@see self::getIterator()} Переберет или все аргументы, или только аргументы по имени
 * <br>- {@see self::commandNameCount()} Вернет кол-во аргументов до первого именованного аргумента
 * <br>- {@see self::commandNameIterator()} Итератор, перебирающие аргументы до первого именованного аргумента
 * <br>--- Операции записи
 * <br>- {@see self::setArgument()} Установит значение аргумента по номеру позиции
 * <br>- {@see self::setName()} Установит значение аргумента по имени
 * <br>- {@see self::offsetUnset()} Удалит аргумент по имени или позиции
 * <br>- {@see self::offsetSet()} Установит значение аргументу по имени или позиции
 * <br>--- Операции чтения
 * <br>- {@see self::getNameByPosition()} Имя для аргумента конкретного позиции, может выбросить исключение
 * <br>- {@see self::getPositionByName()} Вернет позицию для имени, может выбросить исключение
 * <br>- {@see self::getByPosition()} Значение для аргумента конкретного позиции, может выбросить исключение
 * <br>- {@see self::getByName()} Значение для аргумента по имени, может выбросить исключение
 * <br>- {@see self::offsetGet()} Вернет значение аргумента по имени или позиции
 * <br>- {@see self::offsetExists()} Проверит, существует ли аргумент по имени или позиции
 * <br>--- Прочее
 * <br>- {@see self::__toString()}
 *
 * Test cases for class {@see ConsoleArgumentsObjectTest}
 *
 * @todo Реализовать IteratorInterface
 *
 * @deprecated Будет удален в не ранее v2.0.0, используйте {@see ConsoleArguments\ConsoleArgumentsObject}
 */
class ConsoleArgumentsObject extends ConsoleArguments\ConsoleArgumentsObject {}
