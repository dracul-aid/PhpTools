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

use DraculAid\PhpTools\DateTime\TimestampHelper;
use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;

/**
 * Класс для временных диапазонов, точка "начала" и "конца" - таймштампы в формате Секунды
 *
 * @see DateTimeRangeType Временные диапазоны на основе объектов даты-времени
 *
 * Оглавление:
 * <br>{@see TimestampRangeType::create()} Создаст заполненный диапазон
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
class TimestampRangeType extends AbstractDateTimeRange
{
    /**
     * Начало Диапазона (NULL - диапазон еще не установлен)
     *
     * @todo PHP8 Типизация
     */
    public ?int $start = null;

    /**
     * Конец Диапазона (NULL - диапазон еще не установлен)
     *
     * @todo PHP8 Типизация
     */
    public ?int $finish = null;

    /**
     * Создает пустой временной диапазон на основе таймштампов
     */
    public function __construct() {}

    /**
     * Создает заполненный временной диапазон на основе таймштампов
     *
     * @param   mixed   $start     Начало Диапазона, см {@see TimestampHelper::getTimestamp()}
     * @param   mixed   $finish    Конец Диапазона, см {@see TimestampHelper::getTimestamp()}
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
        $this->start = TimestampHelper::getTimestamp($start);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function finishSet($finish): self
    {
        $this->finish = TimestampHelper::getTimestamp($finish);

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @todo PHP8 Типизация ответа функции
     */
    public function startGetTimestamp(bool $withMs = false): ?int
    {
        if ($this->start === null) return null;

        return $this->start;
    }

    /**
     * @inheritdoc
     *
     * @todo PHP8 Типизация ответа функции
     */
    public function finishGetTimestamp(bool $withMs = false): ?int
    {
        if ($this->finish === null) return null;

        return $this->finish;
    }

    /**
     * @inheritdoc
     *
     * @todo PHP8 Типизация ответа функции
     */
    public function startGetString(string $format = DateTimeFormats::FUNCTIONS): ?string
    {
        if ($this->start === null) return null;

        return date($format, $this->start);
    }

    /**
     * @inheritdoc
     *
     * @todo PHP8 Типизация ответа функции
     */
    public function finishGetString(string $format = DateTimeFormats::FUNCTIONS): ?string
    {
        if ($this->finish === null) return null;

        return date($format, $this->finish);
    }
}
