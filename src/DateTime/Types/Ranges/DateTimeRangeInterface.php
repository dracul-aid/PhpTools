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
use DraculAid\PhpTools\DateTime\Types\GetTimestampInterface;
use DraculAid\PhpTools\DateTime\Types\PhpExtended\DateTimeExtendedType;

/**
 * Интерфейс с временным диапазоном
 *
 * Основные реализации:
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
 *
 * @property $start Начало диапазона (NULL - не установлен)
 * @property $finish Конец диапазона (NULL - не установлен)
 */
interface DateTimeRangeInterface
{
    /**
     * Создает пустой диапазон
     */
    public function __construct();

    /**
     * Создает заполненный временной диапазон
     *
     * @param   mixed   $start     Начало Диапазона, см {@see TimestampHelper::getTimestamp()} и аналоги
     * @param   mixed   $finish    Конец Диапазона, см {@see TimestampHelper::getTimestamp()} и аналоги
     *
     * @return static
     */
    public static function create($start = null, $finish = null): self;

    /**
     * Создает временной диапазон, начало и конец которого указывают на "сейчас" (см {@see time()})
     *
     * (!) Этот метод подходит, для создания диапазона с объектами-заготовкам начала и конца, которым позже указывается
     * реальное начало и конец (с помощью редактирования этих объектов)
     *
     * @return static
     */
    public static function createAsTmp(): self;

    /**
     * Устанавливает стартовую точку диапазона
     *
     * @param   mixed   $start   Любой формат представления даты-времени, см {@see TimestampHelper::getTimestamp()} и аналоги
     *
     * @return  $this
     */
    public function startSet($start): self;

    /**
     * Устанавливает конечную точку диапазона
     *
     * @param   mixed   $start   Любой формат представления даты-времени, см {@see TimestampHelper::getTimestamp()} и аналоги
     *
     * @return  $this
     */
    public function finishSet($finish): self;

    /**
     * Очистит конечную точку диапазона
     *
     * @return  $this
     */
    public function finishClear(): self;

    /**
     * Очистит стартовую точку диапазона
     *
     * @return  $this
     */
    public function startClear(): self;

    /**
     * Вернет указание, установлен диапазон, его часть или нет
     *
     * @return   bool|int    Вернет один из вариантов:
     *                       <br> FALSE: Диапазон не установлен
     *                       <br> -1: Установлено только начало, см {@see self::$start}
     *                       <br>  1: Установлен только конец, см {@see self::$finish}
     *                       <br> TRUE: Диапазон полностью установлен (и начало, и конец)
     *
     * @todo PHP8 Типизация ответа функции
     */
    public function isSet();

    /**
     * Вернет начало диапазона ({@see self::$start}) ввиде таймштампа, возможно с микросекундами. Вернет NULL - если "начало" не установлено
     *
     * @param   bool   $withMs   Нужно ли вернуть с микросекундами
     *
     * @return  null|int|float  Вернет целое число или FLOAT, если возвращает кол-во секунд.микросекунд
     *
     * @todo PHP8 Типизация ответа функции
     */
    public function startGetTimestamp(bool $withMs = false);

    /**
     * Вернет конец диапазона ({@see self::$finish}) ввиде таймштампа, возможно с микросекундами. Вернет NULL - если "конец" не установлен
     *
     * @param   bool   $withMs   Нужно ли вернуть с микросекундами
     *
     * @return  null|int|float  Вернет целое число или FLOAT, если возвращает кол-во секунд.микросекунд
     *
     * @todo PHP8 Типизация ответа функции
     */
    public function finishGetTimestamp(bool $withMs = false);

    /**
     * Вернет начало диапазона ({@see self::$start}) ввиде строки, если начало не установлено - вернет NULL
     *
     * @param   string   $format
     *
     * @return  string
     *
     * @todo PHP8 Типизация ответа функции
     */
    public function startGetString(string $format = DateTimeFormats::FUNCTIONS): ?string;

    /**
     * Вернет конец диапазона ({@see self::$finish}) ввиде строки, если начало не установлено - вернет NULL
     *
     * @param   string   $format
     *
     * @return  string
     *
     * @todo PHP8 Типизация ответа функции
     */
    public function finishGetString(string $format = DateTimeFormats::FUNCTIONS): ?string;

    /**
     * Сгенерирует строку пригодную для использования в качестве части SQL запроса для проверки поля на диапазон даты-времени
     *
     * (!) Генерируемый запрос будет формата `$column >= $start AND $column <= $finish`
     *
     * @param   string   $column    Имя столбца
     * @param   string   $format    Формат преобразования даты в строку
     * @param   string   $quote     Кавычки используемые для окружения даты-времени (по умолчанию одинарные кавычки)
     *
     * @return  string   Если начало ({@see self::$start}) и конец ({@see self::$finish}) не установлены - вернет пустую строку
     */
    public function getSqlDateTime(string $column, string $format = DateTimeFormats::SQL_DATETIME, string $quote = "'"): string;

    /**
     * Вернет длину диапазону в секундах, возможно с микросекундами
     *
     * (!) Если не установлено начало ({@see self::$start}) и конец ({@see self::$finish}) диапазона - вернет 0
     * (!) Не важно, начало больше конца, или наоборот, функция всегда вернет положительное число
     *
     * @param   bool   $withMs   Нужно ли вернуть с микросекундами
     *
     * @return  int|float   Вернет целое число или FLOAT, если возвращает кол-во секунд.микросекунд
     *
     * @todo PHP8 Типизация ответа функции
     */
    public function getLenght(bool $withMs = false);

    /**
     * Преобразование диапазона в строку (обычно для отображения пользователям)
     *
     * @param   null|bool|string   $format      Формат вывода точек начала и конца (аналогично формату {@see date()}
     *                                          <br> NULL: Дата и время (см {@see DateTimeFormats::VIEW_FOR_PEOPLE})
     *                                          <br> FALSE: Только время (см {@see DateTimeFormats::VIEW_FOR_PEOPLE_TIME})
     *                                          <br> TRUE: Только дата (см {@see DateTimeFormats::VIEW_FOR_PEOPLE_DATE})
     *                                          <br> string: любая строка для форматирования даты-времени
     * @param   string             $separator   Разделитель точки начала от точки конца
     *
     * @return  string
     *
     * @todo PHP8 Типизация аргументов
     */
    public function getString($format = DateTimeFormats::VIEW_FOR_PEOPLE, string $separator = ' - '): string;
}
