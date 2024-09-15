<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Strings\Objects\StringIterator;

use DraculAid\PhpTools\tests\Strings\Objects\StringIterator\StringIteratorObjectTest;

/**
 * Итератор для перебора строки с явно указанным размером символа в байтах (посимвольного обхода строк с помощью `foreach`)
 *
 * (!) Подходит для перебора строки в котировках, символы которых всегда занимают 1, 2, 3, 4... байта на символ
 *
 * Оглавление:
 * <br>{@see self::setString()} Установит новую строку для перебора (сбросив курсор)
 * <br>{@see self::getString()} Вернет всю перебираемую строку
 * <br>--- Функции перебора
 * <br>{@see self::current()} Вернет "Текущий символ"
 * <br>{@see self::key()} Вернет номер текущего читаемого символа или положение курсора чтения в байтах
 * <br>{@see self::toPosition()} Переместит к указанному символу (возможно на указанное кол-во шагов, в том числе и назад)
 * <br>--- Функции перебора (для поддержки итератора)
 * <br>{@see self::next()} Переместит к следующему символу
 * <br>{@see self::valid()} Проверит текущий элемент на валидность
 * <br>{@see self::rewind()} Осуществит перемещение в начало итерируемой строки
 *
 * Test cases for class {@see StringIteratorObjectTest}
 */
class StringIteratorObject extends AbstractStringIterator
{
    /**
     * Длина каждого читаемого символа (в байтах)
     *
     * @todo PHP8 сделать `readonly public`
     *
     * @readonly
     */
    protected int $charLen = 0;

    /**
     * Создаст итератор для перебора строки с явно указанным размером символа (в байтах)
     *
     * @param   string   $stringForIterator   Строка для перебора
     * @param   int      $charLen             Длина каждого читаемого символа (в байтах)
     */
    public function __construct(string $stringForIterator, int $charLen)
    {
        parent::__construct($stringForIterator);

        $this->charLen = $charLen;
    }

    /** @inheritdoc */
    public function current(): string
    {
        return substr($this->stringForIterator, $this->cursorByte, $this->charLen);
    }

    /** @inheritdoc */
    public function next(int $position = 1)
    {
        $this->cursorChar += $position;
        $this->cursorByte += $this->charLen * $position;

        return $this;
    }
}
