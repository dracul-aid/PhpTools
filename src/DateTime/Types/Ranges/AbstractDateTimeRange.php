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
     * Создает пустой временной диапазон на основе объектов даты-времени
     */
    final public function __construct() {}

    /**
     * @inheritdoc
     *
     * @psalm-suppress UnsafeInstantiation вызов конструктора тут безопасен, так как конструктор определен в абстрактном классе и он финальный
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
     * @psalm-suppress UnsafeInstantiation вызов конструктора тут безопасен, так как конструктор определен в абстрактном классе и он финальный
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
        /** @psalm-suppress UndefinedThisPropertyAssignment Псалм не умеет корректно читать докблоки интерфейсов */
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
        /** @psalm-suppress UndefinedThisPropertyAssignment Псалм не умеет корректно читать докблоки интерфейсов */
        $this->finish = null;

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @todo Вынести юнит-тесты из тестов конкретных классов в тест абстрактного класса
    */
    public function isSet(): bool|int
    {
        // (!) В функции специально используется isset(), в качестве "защиты от дурака", изначально это помогало и от
        //     PSALM, но с новыми версиями он так и не начал видеть "свойства описанные в докблоках интерфейсов" + усвоил, что "динамические свойства - это плохо"
        return match (true) {
            !isset($this->start) && !isset($this->finish) => false,
            isset($this->start) && isset($this->finish)   => true,
            !isset($this->start) && isset($this->finish)  => -1,
            isset($this->start) && !isset($this->finish)  => 1,
            default                                       => false
        };
    }

    /**
     * @inheritdoc
     *
     * @todo Вынести тестирование их конкретных классов в тест абстрактного класса
     */
    public function getSqlDateTime(string $column, string $format = DateTimeFormats::SQL_DATETIME, string $quote = "'"): string
    {
        return match ($this->isSet()) {
            true    => " {$column} BETWEEN {$quote}{$this->startGetString($format)}{$quote} AND {$quote}{$this->finishGetString($format)}{$quote} ",
            -1      => " {$column} <= {$quote}{$this->finishGetString($format)}{$quote} ",
            1       => " {$column} >= {$quote}{$this->startGetString($format)}{$quote} ",
            default => '' // Этот же вариант срабатывает при false - но нет смысла его отдельно выделять
        };
    }

    /**
     * @inheritdoc
     *
     * @todo Вынести тестирование их конкретных классов в тест абстрактного класса
     */
    public function getLenght(bool $withMs = false): int|float
    {
        if ($this->isSet() !== true) return 0;

        return abs((double)$this->finishGetTimestamp($withMs) - (double)$this->startGetTimestamp($withMs));
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
