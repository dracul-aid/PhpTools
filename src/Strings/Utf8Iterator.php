<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Strings;

/**
 * Позволяет перебирать символы UTF-8 строки пропуская значения
 *
 * (!) Если нет потребности изменения позиции курсора чтения, при переборе строки, лучше подойдет {@see Utf8Iterator::utf8Iterator()}
 *
 * Оглавление:
 * --- Упрощенная работа
 * <br>{@see Utf8Iterator::utf8Generator()} Позволяет пройти по всем символам UTF-8 строки
 * <br>{@see Utf8Iterator::calculationCharLen()} Вернет кол-во байт для символа UTF-8
 * --- Перебираемая строка
 * <br>{@see self::set()} - Установит новую строку для перебора (сбросив курсор)
 * <br>{@see self::get()} - Вернет текущую UTF-8 строку
 * <br>{@see self::length()} - Вернет кол-во символов в строке (или кол-во байт)
 * --- Прочитает символ или подстроку
 * <br>{@see self::readChar()} - Прочитает текущий символ (если надо, сдвинет курсор)
 * <br>{@see self::readString()} - Вернет подстроку, начиная с текущего положения курсора (и сдвинет курсор на конец чтения)
 * --- Изменение позиции курсора
 * <br>{@see self::cursorGet()} - Вернет текущее положение курсора
 * <br>{@see self::cursorSet()} - Установит текущее положение курсора
 * <br>{@see self::cursorTo()} - Сдвинет курсор на указанное кол-во символов вперед
 *
 * @deprecated Будет удалено начиная с 0.6 версии. Стоит использовать {@see \DraculAid\PhpTools\Strings\Objects\StringIterator\Utf8IteratorObject}
 */
class Utf8Iterator implements \Countable, \IteratorAggregate
{
    /**
     * UTF-8 строка для перебора
     */
    protected string $utf8String = '';

    /**
     * Положение курсора чтения (в символах)
     */
    protected int $cursorChar = 0;

    /**
     * Положение курсора чтения (в байтах)
     */
    protected int $cursorByte = 0;

    /**
     * Длина текущего символа (в байтах)
     */
    protected int $cursorLen = 0;

    /** Кеш для хранения кол-ва символов в строке (NULL - кеш не установлен, подсчет кол-ва символов еще не проводился) */
    protected null|int $countCache = null;

    /**
     * @param   string   $utf8String   UTF-8 строка для перебора
     */
    public function __construct(string $utf8String)
    {
        $this->set($utf8String);
    }

    /**
     * Позволяет пройти по всем символам UTF-8 строки
     *
     * @param   string   $string       Строка по которой нужно пройти
     * @param   int      $charStart    Позиция начала (в символах)
     * @param   int      $charCount    Сколько символов надо вернуть (0 - все символы)
     *
     * @return  \Generator
     */
    public static function utf8Generator(string $string, int $charStart = 0, int $charCount = 0): \Generator
    {
        for ($bytePosition = 0, $charReadCount = 0, $charYieldCount = 0; $bytePosition < strlen($string);)
        {
            $charLen = self::calculationCharLen($string[$bytePosition]);

            if ($charCount > 0 && $charCount > $charYieldCount) return;
            elseif ($charReadCount >= $charStart)
            {
                yield substr($string, $bytePosition, $charLen);
                $charYieldCount++;
            }

            $bytePosition += $charLen;
            $charReadCount++;
        }
    }

    /**
     * Проводит вычисление длины в байтах читаемого символа UTF-8 строки
     *
     * @param   string   $firstByte   Первый байт потенциального символа
     *
     * @return  int  Для пустой строки длина всегда будет 0
     */
    public static function calculationCharLen(string $firstByte): int
    {
        if (strlen($firstByte) === 0) return 0;

        $charCode = ord($firstByte[0]);

        return match (true) {
            $charCode < 128 => 1,
            $charCode < 224 => 2,
            $charCode < 240 => 3,
            default => 4,
        };
    }

    /**
     * Установит новую строку для перебора (сбросив курсор)
     *
     * @param   string   $utf8String   UTF-8 строка для перебора
     *
     * @return  $this
     */
    public function set(string $utf8String): self
    {
        $this->utf8String = $utf8String;
        $this->cursorSet(0);
        $this->countCache = null;

        return $this;
    }

    /**
     * Вернет текущую UTF-8 строку
     *
     * @return string
     */
    public function get(): string
    {
        return $this->utf8String;
    }

    /**
     * Вернет текущее положение курсора
     *
     * @return int
     */
    public function cursorGet(): int
    {
        return $this->cursorChar;
    }

    /**
     * Установит текущее положение курсора
     *
     * (!) Для вычисления позиции необходимо прочитать всю строку (в случае смещения "назад", от начала строки, в случае
     *     смещения "вперед" от текущего положения курсора)
     *
     * @param  int   $position   Положительное число (новое положение курсора)
     *
     * @return  $this
     */
    public function cursorSet(int $position): self
    {
        if ($this->utf8String === '')
        {
            $this->cursorChar = 0;
            $this->cursorByte = 0;
            $this->cursorLen = 0;

            return $this;
        }
        // при первом вызове "текущая позиция" и "новая позиция" равны (0=0), но при этом все равно необходимо
        // провести вычисление "длины первого символа"
        elseif ($this->cursorLen !== 0 && $position === $this->cursorChar)
        {
            return $this;
        }
        elseif ($position <= 0)
        {
            $this->cursorChar = 0;
            $this->cursorByte = 0;
            $this->cursorLen = self::calculationCharLen($this->utf8String[0]);

            return $this;
        }
        elseif ($position > $this->cursorChar)
        {
            $this->cursorChar++;
            $this->cursorByte = $this->cursorByte + $this->cursorLen;
        }
        else
        {
            $this->cursorChar = 0;
            $this->cursorByte = 0;
        }

        // * * *

        while (true)
        {
            if ($this->cursorByte >= strlen($this->utf8String))
            {
                $this->cursorLen = 0;
                return $this;
            }
            else
            {
                $this->cursorLen = self::calculationCharLen($this->utf8String[$this->cursorByte]);

                if ($this->cursorChar === $position)
                {
                    return $this;
                }
                else
                {
                    $this->cursorChar++;
                    $this->cursorByte += $this->cursorLen;
                }
            }
        }
    }

    /**
     * Сдвинет курсор на указанное кол-во символов вперед
     *
     * (!) Для вычисления позиции необходимо прочитать всю строку (в случае смещения "назад", от начала строки, в случае
     *     смещения "вперед" от текущего положения курсора)
     *
     * @param   int   $offset   Насколько нужно сдвинуть курсор (Только положительное число)
     *
     * @return  $this
     *
     * @throws  \TypeError   Если в качестве смещения курсора было передано отрицательное число
     */
    public function cursorTo(int $offset): self
    {
        return $this->cursorSet($this->cursorChar + $offset);
    }

    /**
     * Вернет очередной прочитанный символ и сдвинет курсор
     *
     * @param   int   $cursorAddPosition   Насколько нужно сдвинуть курсор после чтения
     *
     * @return  string
     */
    public function readChar(int $cursorAddPosition = 0): string
    {
        $char = substr($this->utf8String, $this->cursorByte, $this->cursorLen);

        $this->cursorTo($cursorAddPosition);

        return $char;
    }

    /**
     * Вернет подстроку, начиная с текущего положения курсора и сдвинет курсор на конец прочитанной подстроки
     *
     * @param  int    $len            Длина (в символах) читаемого блока
     * @param  bool   $movePosition   Нужно ли сдвигать курсор на конец прочитанной подстроки
     *
     * @return  string
     */
    public function readString(int $len, bool $movePosition = false): string
    {
        $tmpCursorChar = $this->cursorChar;
        $tmpCursorByte = $this->cursorByte;
        $tmpCursorlen = $this->cursorLen;

        $readLenByte = $this->cursorTo($len)->cursorByte - $tmpCursorByte;

        $_return = substr(
            $this->utf8String,
            $tmpCursorByte,
            $readLenByte,
        );

        if (!$movePosition)
        {
            $this->cursorChar = $tmpCursorChar;
            $this->cursorByte = $tmpCursorByte;
            $this->cursorLen = $tmpCursorlen;
        }

        return $_return;
    }

    /**
     * Вернет кол-во символов в строке (или кол-во байт)
     *
     * @param   bool   $lenByte   TRUE - если нужно вернуть кол-во байт
     *
     * @return  int   Пустая строка всегда вернет 0
     */
    public function length(bool $lenByte = false): int
    {
        if ($lenByte) return strlen($this->utf8String);
        else
        {
            if ($this->countCache === null) $this->countCache = mb_strlen($this->utf8String);

            return $this->countCache;
        }
    }

    public function count(): int
    {
        return $this->length(false);
    }

    public function getIterator(): \Generator
    {
        while ($this->cursorByte < strlen($this->utf8String))
        {
            yield $this->readChar(1);
        }
    }
}
