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
 * DTO с описанием даты-времени (Год, Месяц, Число и Время)
 */
class DateTimeLightDto
{
    /** Год (например, 2018) */
    public int $year;

    /** Номер месяца (1 - 12) */
    public int $mon;

    /** Номер дня месяца (1 - 31) */
    public int $day;

    /** Час (0 - 23) */
    public int $hour;

    /** Минута (0 - 60) */
    public int $minute;

    /** Секунда (0 - 60) */
    public int $second;
}
