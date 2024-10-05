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
use DraculAid\PhpTools\DateTime\Types\PhpExtended\DateTimeExtendedType;

/**
 * Класс для временных диапазонов, точка "начала" и "конца" - Объекты для работы с расширенным PHP объектом даты-времени ({@see DateTimeExtendedType})
 *
 * @see DateTimeRangeType Диапазон основанный на объектах {@see \DateTime}
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
class DateTimeExtendedRangeType extends AbstractDateTimeRange
{
    /**
     * Начало Диапазона (NULL - диапазон еще не установлен)
     *
     * @var null|DateTimeExtendedType
     *
     * @todo PHP8 Типизация
     */
    public $start = null;

    /**
     * Конец Диапазона (NULL - диапазон еще не установлен)
     *
     * @var null|DateTimeExtendedType
     *
     * @todo PHP8 Типизация
     */
    public $finish = null;

    /**
     * Создает пустой временной диапазон на основе объектов даты-времени
     */
    public function __construct() {}

    /**
     * @inheritdoc
     */
    public function startSet($start): self
    {
        /** @psalm-suppress PropertyTypeCoercion мы тут явно указываем, какой класс вернет функция, так что пласм зря переживает */
        $this->start = DateTimeObjectHelper::getDateObject($start, DateTimeExtendedType::class);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function finishSet($finish): self
    {
        /** @psalm-suppress PropertyTypeCoercion мы тут явно указываем, какой класс вернет функция, так что пласм зря переживает */
        $this->finish = DateTimeObjectHelper::getDateObject($finish, DateTimeExtendedType::class);

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
