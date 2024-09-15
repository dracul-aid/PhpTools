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

/**
 * Интерфейс для итераторов строк (объектов позволяющих посимвольно обойти строку с помощью `foreach`)
 *
 * Основные реализации:
 * <br>{@see StringIteratorObject} Для перебора строк с явно указанным размером символа (1, 2.. байтовые кодировки)
 * <br>{@see Utf8IteratorObject} Для перебора UTF-8 строк
 *
 * Оглавление:
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
interface StringIteratorInterface extends \Iterator
{
    /**
     * Вернет номер текущего читаемого символа (отсчет от 0)
     *
     * @param   bool   $bytes   TRUE если нужно вернуть текущую позицию в байтах. FALSE в символах (По умолчанию).
     *
     * @return  int
     */
    public function key(bool $bytes = false): int;

    /**
     * Вернет "Текущий символ"
     *
     * (!) Если "курсор" чтения строки находится за пределами конца строки - вернет пустую строку
     *
     * @return string
     */
    public function current(): string;

    /**
     * Переместит к следующему символу
     *
     * @return void
     */
    public function next(): void;

    /**
     * Проверит текущий элемент на валидность
     *
     * @return bool TRUE если чтение возможно, FALSE если чтение вышло за размеры строки
     */
    public function valid(): bool;

    /**
     * Осуществит перемещение в начало итерируемой строки
     *
     * @return void
     */
    public function rewind(): void;

    /**
     * Смещение на указанное кол-во позиций (в символах), может перемещать как вперед, так и назад
     *
     * - Особенности работы:
     * <br>(!) В случае итерирования строк, с переменным размером символа (например UFT8) перемещение назад может быть "дорогой операцией"
     * (так как потребует перемотать строку с самого начала)
     * <br>(!) В результате перемещения "курсор" может оказаться за строкой, функция никак не проверяет корректность перемещения
     *
     * @see self::toPosition() Перемещает в указанную позицию
     *
     * @param   int   $moveStep   Шаг перемещения
     *
     * @return  $this
     */
    public function move(int $moveStep = 1): self;

    /**
     * Осуществит перемещение в начало итерируемой строки
     *
     * @return $this
     *
     * @deprecated Будет удален начиная с 0.7
     */
    public function toStart(): self;

    /**
     * Перемещает курсор в указанную позицию (т.е. к указанному символу)
     *
     * - Особенности работы:
     * <br>(!) В случае итерирования строк, с переменным размером символа (например UFT8) перемещение назад может быть "дорогой операцией"
     * (так как потребует перемотать строку с самого начала)
     * <br>(!) В результате перемещения "курсор" может оказаться за строкой, функция никак не проверяет корректность перемещения
     *
     * @see self::move() Смещение на указанное кол-во позиций (в символах)
     *
     * @param   int   $positionNumber
     *
     * @return  $this
     *
     * @deprecated Будет удален начиная с 0.7
     */
    public function toPosition(int $positionNumber): self;
}
