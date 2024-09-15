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
use DraculAid\PhpTools\Classes\Patterns\Iterator\IteratorTrait;

// @todo PHP8 удалить
LoaderPhp8Lib::loadInterfaces();

/**
 * Абстрактный класс для итераторов строк, основанных на типе данных `string`
 *
 * (!) Поддерживает преобразование в строку, возвращая разом всю перебираемую строку
 *
 * Основные реализации:
 * <br>{@see StringIteratorObject} Для перебора строк с явно указанным размером символа (1, 2.. байтовые кодировки)
 * <br>{@see Utf8IteratorObject} Для перебора UTF-8 строк

 * Оглавление:
 * <br>- {@see self::setString()} Установит новую строку для перебора (сбросив курсор)
 * <br>- {@see self::getString()} Вернет всю перебираемую строку
 * <br>- {@see self::getIterator()} Переберет посимвольно строку, без изменения позиции "курсора"
 * <br>--- Функции перебора
 * <br>- {@see self::key()} Вернет номер текущего читаемого символа или положение курсора чтения в байтах
 * <br>- {@see self::current()} Вернет "Текущий символ"
 * <br>- {@see self::next()} Переместит к следующему символу
 * <br>- {@see self::toPosition()} Переместит к указанному символу (возможно на указанное кол-во шагов, в том числе и назад)
 * <br>- {@see self::valid()} Проверит текущий элемент на валидность
 * <br>- {@see self::rewind()} Осуществит перемещение в начало итерируемой строки
 * <br>- {@see self::currentValueAndNext()} Вернет текущее значение и сдвинет "курсор", в случае достижения "конца", вернет NULL
 * <br>- {@see self::currentElementAndNext()} Вернет текущий ключ и значение и сдвинет "курсор"
 */
abstract class AbstractStringIterator implements StringIteratorInterface, \Stringable
{
    use IteratorTrait;

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
    public function getIterator(bool $bytes = false): \Generator
    {
        $startByte = $this->cursorByte;
        $startChar = $this->cursorChar;

        yield from $this->getIteratorRun();

        $this->cursorByte = $startByte;
        $this->cursorChar = $startChar;
    }

    /** @inheritdoc */
    public function key(bool $bytes = false): int
    {
        if ($bytes) return $this->cursorByte;

        return $this->cursorChar;
    }

    /** @inheritdoc */
    public function valid(): bool
    {
        return strlen($this->stringForIterator) > $this->cursorByte;
    }

    /** @inheritdoc */
    public function rewind()
    {
        $this->cursorChar = 0;
        $this->cursorByte = 0;

        return $this;
    }

    /** @inheritdoc */
    public function toPosition(int $positionNumber): self
    {
        // если нет смещения
        if ($positionNumber === $this->cursorChar) return $this;

        // если смещение "вперед"
        if ($positionNumber > $this->cursorChar) return $this->next($this->cursorChar - $positionNumber);
        // если смещение "назад"
        else return $this->next($positionNumber - $this->cursorChar);
    }

    /**
     * @inheritdoc
     *
     * @deprecated Будет удален начиная с 0.7
     */
    public function toStart(): self
    {
        $this->rewind();

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @deprecated Будет удален начиная с 0.7
     */
    public function move(int $moveStep = 1): self
    {
        return $this->next($moveStep);
    }

    /**
     * Перебирает строку для {@see static::getIterator()}
     *
     * @param   bool   $bytes
     *
     * @return \Generator
     */
    protected function getIteratorRun(bool $bytes = false): \Generator
    {
        $this->rewind();

        if (!$this->valid()) return;

        do {
            yield $this->key($bytes) => $this->current();
            $this->next();
        } while ($this->valid());
    }
}
