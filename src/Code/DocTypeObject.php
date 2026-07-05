<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Code;

use DraculAid\PhpTools\Code\Objects\DocBlockTypeStorage;
use DraculAid\PhpTools\tests\Code\CodeTypeObjectTest;

/**
 * Класс, для хранения типов данных (совместим с DocBlock/PhpBlock)
 *
 * (!) Является интератором, перебирает все типы (см {@see self::$types})
 * (!) При преобразовании в строку, вернет строку с типами пригодными к использованию в PHP коде
 *
 * Оглавление
 * <br>--- Создание объекта
 * <br>{@see DocTypeObject::createFromPhp()} Создает с переданными PHP типами
 * <br>{@see DocTypeObject::createFromSql()} Создает с переданным SQL типом
 * <br>{@see DocTypeObject::createFromDocBlock()} Установит тип(ы) данных принятых в DocBlock / PhpDoc
 * <br>--- Установка типов
 * <br>{@see self::set()} Установит тип данных
 * <br>{@see self::setFromSql()} Установит тип по SQL типу
 * <br>{@see self::setFromDocBlock()} Установит тип(ы) данных принятых в DocBlock / PhpDoc
 * <br>--- Проверка типов
 * <br>{@see self::isWithType()} В типе данных, есть указанный тип или нет
 * <br>{@see self::isWithNull()} В типе данных, есть NULL или нет
 * <br>{@see self::isWithBool()} В типе данных, есть булевы варианты (bool, true, false)
 * <br>{@see self::isWithNumber()} В типе данных, есть числа
 * <br>--- Проверка типов
 * <br>{@see self::getIterator()} Позволит перебрать все типы
 * <br>{@see self::getType()} Вернет все типы ввиде массива
 *
 * @method static DocTypeObject createFromPhp(string|string[] $type) Создает с переданными PHP типами
 * @method static DocTypeObject createFromSql(string $type, bool $isNull) Создает с переданным SQL типом
 * @method static DocTypeObject createFromDocBlock(string|string[] $type) Создает с переданными DocBlock типами
 *
 * Test cases for class {@see CodeTypeObjectTest}
 *
 * @deprecated Устарел с 1.4.0, будет удален не ранее v2.0.0 Используйте {@see \DraculAid\PhpTools\Code\Objects\DocBlockTypeStorage}
 *
 * @since 0.3.0
 * @since 1.4.0 Объявлен устаревшим
 */
class DocTypeObject extends DocBlockTypeStorage {}
