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

use DraculAid\PhpTools\DateTime\DateTimeObjectHelper;
use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;

/**
 * Класс для временных диапазонов, точка "начала" и "конца" - Объекты для работы с датой-временем PHP ({@see \DateTimeInterface})
 *
 * @see DateTimeExtendedRangeType Диапазон основанный на расширении объекта даты время PHP, см {@see DateTimeExtendedType}
 * @see TimestampRangeType Временные диапазоны на основе таймштампов (в секундах)
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
class DateTimeRangeType extends AbstractDateTimeRange
{
    /** Начало Диапазона (NULL - диапазон еще не установлен) */
    public null|\DateTimeInterface $start = null;

    /** Конец Диапазона (NULL - диапазон еще не установлен) */
    public null|\DateTimeInterface $finish = null;

    /** @inheritdoc */
    public function startSet($start): self
    {
        $this->start = DateTimeObjectHelper::getDateObject($start, \DateTime::class);

        return $this;
    }

    /** @inheritdoc */
    public function finishSet($finish): self
    {
        $this->finish = DateTimeObjectHelper::getDateObject($finish, \DateTime::class);

        return $this;
    }

    /** @inheritdoc */
    public function startGetTimestamp(bool $withMs = false): null|int|float
    {
        if ($this->start === null) return null;

        if (!$withMs) return $this->start->getTimestamp();
        else return (float)$this->start->format(DateTimeFormats::TIMESTAMP_WITH_MICROSECONDS);
    }

    /** @inheritdoc */
    public function finishGetTimestamp(bool $withMs = false): null|int|float
    {
        if ($this->finish === null) return null;

        if (!$withMs) return $this->finish->getTimestamp();
        else return (float)$this->finish->format(DateTimeFormats::TIMESTAMP_WITH_MICROSECONDS);
    }

    /** @inheritdoc */
    public function startGetString(string $format = DateTimeFormats::FUNCTIONS): null|string
    {
        if ($this->start === null) return null;

        return $this->start->format($format);
    }

    /** @inheritdoc */
    public function finishGetString(string $format = DateTimeFormats::FUNCTIONS): null|string
    {
        if ($this->finish === null) return null;

        return $this->finish->format($format);
    }
}
