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
 * <br> {@see DateTimeRangeType} Диапазон основанный на объектах {@see \DateTime}
 * <br> {@see DateTimeExtendedRangeType} Диапазон основанный на расширении объекта даты время PHP, см {@see DateTimeExtendedType}
 * <br> {@see TimestampRangeType} Временные диапазоны на основе таймштампов (в секундах)
 *
 * Оглавление:
 * <br>{@see self::create()} Создаст заполненный диапазон
 * <br>{@see self::createAsTmp()} Создает временной диапазон, начало и конец которого указывают на "сейчас"
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
     * @inheritdoc
     *
     * @todo Вынести тестирование их конкретных классов в тест абстрактного класса
     */
    public static function create($start = null, $finish = null): self
    {
        return (new static())->startSet($start)->finishSet($finish);
    }

    /**
     * @inheritdoc
     *
     * @todo Вынести тестирование их конкретных классов в тест абстрактного класса
     */
    public static function createAsTmp(): self
    {
        return (new static())->startSet(null)->finishSet(null);
    }

    /**
     * Очистит стартовую точку диапазона
     *
     * @return  $this
     *
     * @todo Вынести тестирование их конкретных классов в тест абстрактного класса
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
     *
     * @todo Вынести тестирование их конкретных классов в тест абстрактного класса
     */
    public function finishClear(): self
    {
        $this->finish = null;

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @todo PHP8 match()
     *
     * @todo Вынести тестирование их конкретных классов в тест абстрактного класса
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
     * @todo PHP8 match()
     *
     * @todo Вынести тестирование их конкретных классов в тест абстрактного класса
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
     *
     * @todo Вынести тестирование их конкретных классов в тест абстрактного класса
     */
    public function getLenght(bool $withMs = false)
    {
        if ($this->isSet() !== true) return 0;

        return abs($this->finishGetTimestamp($withMs) - $this->startGetTimestamp($withMs));
    }

    /**
     * @inheritdoc
     *
     * @todo Вынести тестирование их конкретных классов в тест абстрактного класса
     */
    public function getString($format = DateTimeFormats::VIEW_FOR_PEOPLE, string $separator = ' - '): string
    {
        if ($format === null) $format = DateTimeFormats::VIEW_FOR_PEOPLE;
        elseif ($format === false) $format = DateTimeFormats::VIEW_FOR_PEOPLE_TIME;
        elseif ($format === true) $format = DateTimeFormats::VIEW_FOR_PEOPLE_DATE;

        return "{$this->startGetString($format)}{$separator}{$this->finishGetString($format)}";
    }
}
