<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\DateTime\Types;

/**
 * Интерфейс для классов, которые могут возвращать таймштамп и строковое представление даты времени
 *
 * Оглавление:
 * <br>{@see self::getTimestamp()} Преобразует объект в таймштамп (в секундах)
 * <br>{@see self::format()} Преобразует объект в строку, по указанному формату (аналогично {@see date()})
 */
interface GetTimestampInterface
{
    /**
     * Вернет таймштамп (в секундах)
     *
     * @return int
     */
    public function getTimestamp(): int;

    /**
     * Преобразует таймштам в строку
     *
     * @link https://www.php.net/manual/ru/datetime.format.php Формат преобразования таймштампа в строку
     *
     * @param   string   $format   Формат преобразования
     *
     * @return  string
     */
    public function format(string $format = ''): string;
}
