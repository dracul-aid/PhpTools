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

use DraculAid\PhpTools\tests\Code\FunctionAsPropertyObjectTest;

/**
 * Позволяет использовать функцию в качестве свойства классов
 *
 * Позволяет типизировать свойства классов, используя их для хранения анонимных функций ({@see \Closure}), обычных функций,
 * методов классов, а также языковых конструкций PHP
 *
 * Оглавление:
 * <br>- {@see FunctionAsPropertyObject::getOrCreate()} - Вернет (если надо создаст) объект, хранящий функцию
 * <br>- {@see self::setFunction()} - Заменит установленную функцию
 * <br>- {@see self::call()} - Произведет вызов
 * <br>- {@see self::callSafe()} - Произведет защищенный вызов
 * <br>- {@see self::getFunction()} - Вернет установленную функцию
 *
 * Test cases for class {@see FunctionAsPropertyObjectTest}
 *
 * @deprecated Устарел с 1.4.0, будет удален не ранее v2.0.0 Используйте {@see \DraculAid\PhpTools\Code\Objects\FunctionAsPropertyObject}
 * @since 0.4.0
 * @since 1.4.0 Объявлен устаревшим
 */
class FunctionAsPropertyObject extends \DraculAid\PhpTools\Code\Objects\FunctionAsPropertyObject {}
