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

use DraculAid\PhpTools\DateTime\Types\GetTimestampInterface;
use DraculAid\PhpTools\DateTime\DateTimeObjectHelper;
use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;

/**
 * Класс для временных диапазонов, точка "начала" и "конца" - Объекты для работы с датой-временем
 *
 * Поддерживает как PHP объекты ({@see \DateTimeInterface}), так и объекты возвращающие таймштампы ({@see GetTimestampInterface})
 *
 * @see TimestampRangeType Временные диапазоны на основе таймштампов (в секундах)
 *
 * Оглавление:
 * <br>{@see DateTimeRangeType::create()} Создаст заполненный диапазон
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
    /**
     * Начало Диапазона (NULL - диапазон еще не установлен)
     *
     * @var null|\DateTimeInterface|GetTimestampInterface
     *
     * @todo PHP8 Типизация
     */
    public $start = null;

    /**
     * Конец Диапазона (NULL - диапазон еще не установлен)
     *
     * @var null|\DateTimeInterface|GetTimestampInterface
     *
     * @todo PHP8 Типизация
     */
    public $finish = null;

    /**
     * Создает пустой временной диапазон на основе объектов даты-времени
     */
    public function __construct() {}

    /**
     * Создает заполненный временной диапазон на объектов даты-времени
     *
     * @param   mixed   $start     Начало Диапазона, см {@see DateTimeObjectHelper::getDateObject()}
     * @param   mixed   $finish    Конец Диапазона, см {@see DateTimeObjectHelper::getDateObject()}
     */
    public static function create($start = null, $finish = null): self
    {
        return (new static())->startSet($start)->finishSet($finish);
    }

    /**
     * @inheritdoc
     */
    public function startSet($start): self
    {
        $this->start = DateTimeObjectHelper::getDateObject($start);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function finishSet($finish): self
    {
        $this->finish = DateTimeObjectHelper::getDateObject($finish);

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @todo PHP8 Типизация ответа функции
     */
    public function startGetTimestamp(bool $withMs = false)
    {
        if ($this->start === null) return null;

        if (!$withMs) return $this->start->getTimestamp();
        else return (float)$this->start->format(DateTimeFormats::TIMESTAMP_WITH_MICROSECONDS);
    }

    /**
     * @inheritdoc
     *
     * @todo PHP8 Типизация ответа функции
     */
    public function finishGetTimestamp(bool $withMs = false)
    {
        if ($this->finish === null) return null;

        if (!$withMs) return $this->finish->getTimestamp();
        else return (float)$this->finish->format(DateTimeFormats::TIMESTAMP_WITH_MICROSECONDS);
    }

    /**
     * @inheritdoc
     *
     * @todo PHP8 Типизация ответа функции
     */
    public function startGetString(string $format = DateTimeFormats::FUNCTIONS): ?string
    {
        if ($this->start === null) return null;

        return $this->start->format($format);
    }

    /**
     * @inheritdoc
     *
     * @todo PHP8 Типизация ответа функции
     */
    public function finishGetString(string $format = DateTimeFormats::FUNCTIONS): ?string
    {
        if ($this->finish === null) return null;

        return $this->finish->format($format);
    }
}