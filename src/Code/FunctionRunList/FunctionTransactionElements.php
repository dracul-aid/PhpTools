<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Code\FunctionRunList;

/**
 * Структура для хранения свойств объекта "списка функций" ({@see AbstractFunctionRunList})
 *
 * Оглавление:
 * <br>- {@see self::$functionList} - Набор функций
 * <br>- {@see self::$failList} - Список исключений пойманных в ходе выполнения списка функций
 * <br>- {@see self::$failRollbackList} - Список исключений пойманных в ходе выполнения роллбек-функций
 *
 * Test cases for class - Не требуется, в классе только свойства
 */
class FunctionTransactionElements
{
    /**
     * Набор функций
     *
     * @see AbstractFunctionRunList::addFunction() Используется для добавления функций в список
     *
     * @var FunctionElement[]
     */
    public array $functionList = [];

    /**
     * Список исключений пойманных в ходе выполнения списка функций ({@see self::$functionList}). Ключ - "позиция" функции,
     * значение - пойманное исключение
     *
     * @var array<int<0, max>, \Throwable>
     */
    public array $failList = [];

    /**
     * Список исключений пойманных в ходе выполнения роллбек-функций ({@see self::$functionList}). Ключ - "позиция" функции,
     * значение - пойманное исключение
     *
     * @var array<int<0, max>, \Throwable>
     */
    public array $failRollbackList = [];
}
