<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Arrays\Objects\Components\ArrayObjectTools;

use DraculAid\PhpTools\Arrays\Objects\IteratorSafeRunner;

/**
 * Структура для хранения пойманных исключений в ходе выполнения {@see IteratorSafeRunner::exe()}
 */
final class IteratorSafeRunThrowableStructure
{
    /** Позиция "перебора" ({@see self::$position}) указывает на "Перемотку" до начала перебора */
    public const POSITION_UNDEFINED = -1;

    /** Имя функции {@see \Iterator} которая выбросила исключение */
    public string $functionName;

    /** Пойманный объект исключения (или ошибка) */
    public \Throwable $throwable;

    /** Позиция элемента при котором было выброшено исключение */
    public int $position;

    /**
     * Возможный ключ, который был возвращен при переборе
     *
     * @var mixed
     *
     * @todo PHP8 установить тип свойства
     */
    public $key;

    /**
     * Возможное значение, которое было возвращен при переборе
     *
     * @var mixed
     *
     * @todo PHP8 установить тип свойства
     */
    public $value;
}
