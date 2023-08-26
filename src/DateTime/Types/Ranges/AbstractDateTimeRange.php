<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\DateTime\Types\Ranges;

use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;

/**
 * Абстрактный класс для работы с Диапазонами даты-времени
 *
 * Основные реализации
 * <br> {@see DateTimeRangeType} Диапазон основанный на объектах, поддерживающих возврат таймштампов
 * <br> {@see TimestampRangeType} Временные диапазоны на основе таймштампов (в секундах)
 *
 * Оглавление:
 * <br>{@see self::create()} Создаст заполненный диапазон
 * <br>--- Начало диапазона
 * <br>{@see self::$start} Начало диапазона (NULL - не установлен)
 * <br>{@see self::startSet()} Устанавливает стартовую точку диапазона
 * <br>{@see self::startClear()} Очистит стартовую точку диапазона
 * <br>{@see self::startGetTimestamp()} Вернет начало диапазона ввиде таймштампа
 * <br>{@see self::startGetString()} Вернет начало диапазона ввиде строки
 * <br>--- Конец диапазона
 * <br>{@see self::$finish} Конец диапазона (NULL - не установлен)
 * <br>{@see self::finishSet()} Устанавливает конечную точку диапазона
 * <br>{@see self::finishClear()} Очистит конечную точку диапазона
 * <br>{@see self::finishGetTimestamp()} Вернет конец диапазона ввиде таймштампа
 * <br>{@see self::finishGetString()} Вернет конец диапазона ввиде строки
 * <br>--- Взаимодействие с диапазоном
 * <br>{@see self::isSet()} Вернет указание, установлен диапазон, его часть или нет
 * <br>{@see self::getSqlDateTime} Сгенерирует строку пригодную для использования в качестве части SQL запроса для проверки поля на диапазон даты-времени
 * <br>{@see self::getLenght()} Вернет длину диапазону в секундах, возможно с микросекундами
 */
abstract class AbstractDateTimeRange implements DateTimeRangeInterface
{
    /**
     * Очистит стартовую точку диапазона
     *
     * @return  $this
     */
    public function startClear(): self
    {
        $this->start = null;

        return $this;
    }

    /**
     * Очистит конечную точку диапазона
     *
     * @return  $this
     */
    public function finishClear(): self
    {
        $this->finish = null;

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @todo PHP8 math()
     */
    public function isSet()
    {
        if ($this->start === null && $this->finish === null) return false;

        if ($this->start !== null && $this->finish !== null) return true;

        if ($this->start === null && $this->finish !== null) return -1;

        if ($this->start !== null && $this->finish === null) return 1;

        return false;
    }

    /**
     * @inheritdoc
     *
     * @todo PHP8 math()
     */
    public function getSqlDateTime(string $column, string $format = DateTimeFormats::SQL_DATETIME, string $quote = "'"): string
    {
        if ($this->isSet() === false) return '';

        if ($this->isSet() === true) return " {$column} BETWEEN {$quote}{$this->startGetString($format)}{$quote} AND {$quote}{$this->finishGetString($format)}{$quote} ";

        if ($this->isSet() === -1) return " {$column} <= {$quote}{$this->finishGetString($format)}{$quote} ";
        if ($this->isSet() === 1) return " {$column} >= {$quote}{$this->startGetString($format)}{$quote} ";

        return '';
    }

    /**
     * @inheritdoc
     */
    public function getLenght(bool $withMs = false)
    {
        if ($this->isSet() !== true) return 0;

        return abs($this->finishGetTimestamp($withMs) - $this->startGetTimestamp($withMs));
    }
}
