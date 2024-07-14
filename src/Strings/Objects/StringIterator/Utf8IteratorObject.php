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

use DraculAid\PhpTools\tests\Strings\Objects\StringIterator\Utf8IteratorObjectTest;

/**
 * Класс для итерирования строк UTF8 (посимвольного обхода строк с помощью `foreach`)
 *
 * (!) Замещает устаревший {@see \DraculAid\PhpTools\Strings\Utf8Iterator}
 * (!) Каждый символ в UTF8 занимает от 1 до 4 байт
 *
 * Оглавление:
 * <br>{@see Utf8IteratorObject::calculationCharLen()} Проводит вычисление длины в байтах для символа UTF-8
 * <br>{@see self::setString()} Установит новую строку для перебора (сбросив курсор)
 * <br>{@see self::getString()} Вернет всю перебираемую строку
 * <br>{@see self::getCharLen()} Вернет размер текущего символа в байтах
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
 *
 * Test cases for class {@see Utf8IteratorObjectTest}
 */
class Utf8IteratorObject extends AbstractStringIterator
{
    /** Указывает, что размер текущего символа не вычислен (обслуживает {@see self::$tmpCharLen}) */
    protected const TMP_CHAR_LEN_UNDEFINED = -1;

    /**
     * Размер в байтах текущего символа
     * ({@see self::TMP_CHAR_LEN_UNDEFINED}: размер не вычислен)
     */
    protected int $tmpCharLen = self::TMP_CHAR_LEN_UNDEFINED;

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

        if ($charCode < 128) return 1;
        elseif ($charCode < 224) return 2;
        elseif ($charCode < 240) return 3;
        else return 4;

        /* TODO PHP8
        return match (true) {
            $charCode < 128 => 1,
            $charCode < 224 => 2,
            $charCode < 240 => 3,
            default => 4,
        };
        */
    }

    /**
     * Вернет размер текущего символа в байтах
     * (0 - курсор находится вне строки)
     *
     * @return int
     */
    public function getCharLen(): int
    {
        // если размер символа неизвестен - проведем вычисление
        if ($this->tmpCharLen === self::TMP_CHAR_LEN_UNDEFINED)
        {
            $this->tmpCharLen = static::calculationCharLen($this->stringForIterator[$this->cursorByte]);
        }

        return $this->tmpCharLen;
    }

    /** @inheritdoc */
    public function current(): string
    {
        return substr($this->stringForIterator, $this->cursorByte, $this->getCharLen());
    }

    /** @inheritdoc */
    public function move(int $moveStep = 1): self
    {
        // перемещение вперед
        if ($moveStep > 0)
        {
            while ($moveStep > 0)
            {
                $this->cursorByte += $this->getCharLen();
                $this->cursorChar++;
                $this->tmpCharLen = self::TMP_CHAR_LEN_UNDEFINED;
                $moveStep--;
            }
        }
        // перемещение назад
        elseif ($moveStep < 0)
        {
            $this->move($this->cursorChar + $moveStep);
        }

        return $this;
    }
}
