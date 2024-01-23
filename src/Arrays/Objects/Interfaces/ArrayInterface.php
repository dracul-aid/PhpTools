<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Arrays\Objects\Interfaces;

use DraculAid\PhpTools\Classes\ClassTools;

/**
 * Объекты похожие на массивы
 *
 * (!) Интерфейс добавлен как "сахар", для ссылок в докблоках, и на случай создания в библиотеке "массивоподобных классов"
 * (!) Классы реализующие интерфейс, должны также реализовывать одну из реализаций {@see \Traversable}: {@see \Iterator} или {@see \IteratorAggregate}
 * Пример подобного объекта: {@see \ArrayObject} или {@see \SplFixedArray}
 *
 * @see ArrayHelper::isAsArray() Проверит, является ли объект похожим на массив
 * @see ClassTools::isAsArray() Проверит, похож ли объект или класс на массив
 */
interface ArrayInterface extends \Countable, \ArrayAccess, \Traversable {}
