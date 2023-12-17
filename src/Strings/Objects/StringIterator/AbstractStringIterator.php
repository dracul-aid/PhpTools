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

use DraculAid\Php8forPhp7\LoaderPhp8Lib;

// @todo PHP8 удалить
LoaderPhp8Lib::loadInterfaces();

/**
 * Абстрактный класс для итераторов строк, основанных на типе данных `string`
 *
 * (!) Поддерживает преобразование в строку, возвращая разом всю перебираемую строку
 *
 * Основные реализации:
 * <br>{@see StringIteratorObject}
 * <br>{@see Utf8IteratorObject}
 *
 * Оглавление:
 * <br>{@see self::setString()} Установит новую строку для перебора (сбросив курсор)
 * <br>{@see self::getString()} Вернет всю перебираемую строку
 * <br>--- Функции перебора
 * <br>{@see self::current()} Вернет "Текущий символ"
 * <br>{@see self::key()} Вернет номер текущего читаемого символа или положение курсора чтения в байтах
 * <br>{@see self::move()} Переместит к следующему символу (возможно на указанное кол-во шагов, в том числе и назад)
 * <br>{@see self::toStart()} Перемотает в начало строки
 * <br>{@see self::toPosition()} Переместит к указанному символу (возможно на указанное кол-во шагов, в том числе и назад)
 * <br>--- Функции перебора (для поддержки итератора)
 * <br>{@see self::next()} Переместит к следующему символу
 * <br>{@see self::valid()} Проверит текущий элемент на валидность
 * <br>{@see self::rewind()} Осуществит перемещение в начало итерируемой строки
 */
abstract class AbstractStringIterator implements StringIteratorInterface, \Stringable
{
    /** Строка для перебора */
    protected string $stringForIterator = '';

    /** Положение курсора чтения (в символах) */
    protected int $cursorChar = 0;

    /** Положение курсора чтения (в байтах) */
    protected int $cursorByte = 0;

    /**
     * Базовый конструктор объектов-интераторов строк, основанных на типе string
     *
     * @param   string   $stringForIterator   Строка для перебора
     */
    public function __construct(string $stringForIterator)
    {
        $this->setString($stringForIterator);
    }

    public function __toString(): string
    {
        return $this->stringForIterator;
    }

    /**
     * Установит новую строку для перебора (сбросив курсор)
     *
     * @param   string   $stringForIterator   Строка для перебора
     *
     * @return  $this
     */
    public function setString(string $stringForIterator): self
    {
        $this->stringForIterator = $stringForIterator;

        $this->rewind();

        return $this;
    }

    /**
     * Вернет всю перебираемую строку
     *
     * @return string
     */
    public function getString(): string
    {
        return $this->stringForIterator;
    }

    /** @inheritdoc */
    public function next(): void
    {
        $this->move();
    }

    /** @inheritdoc */
    public function key(bool $bites = false): int
    {
        if ($bites) return $this->cursorByte;

        return $this->cursorChar;
    }

    /** @inheritdoc */
    public function valid(): bool
    {
        return strlen($this->stringForIterator) > $this->cursorByte;
    }

    /** @inheritdoc */
    public function rewind(): void
    {
        $this->cursorChar = 0;
        $this->cursorByte = 0;
    }

    /** @inheritdoc */
    public function toStart(): self
    {
        $this->rewind();

        return $this;
    }

    /** @inheritdoc */
    public function toPosition(int $positionNumber): self
    {
        // если нет смещения
        if ($positionNumber === $this->cursorChar) return $this;

        // если смещение "вперед"
        if ($positionNumber > $this->cursorChar) return $this->move($this->cursorChar - $positionNumber);
        // если смещение "назад"
        else return $this->move($positionNumber - $this->cursorChar);
    }
}
