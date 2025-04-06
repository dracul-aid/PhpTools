<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\DateTime\Types\PhpExtended;

use DraculAid\PhpTools\DateTime\DateTimeHelper;
use DraculAid\PhpTools\DateTime\DateTimeObjectHelper;
use DraculAid\PhpTools\DateTime\DateTimeValidator;
use DraculAid\PhpTools\DateTime\Dictionary\DateConstants;
use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;
use DraculAid\PhpTools\DateTime\TimestampHelper;
use DraculAid\PhpTools\DateTime\Types\GetTimestampInterface;

/**
 * Расширение для стандартного PHP класса для работы с датой-временем {@see \DateTime}
 *
 * Оглавление:
 * <br>--- Получение данных
 * <br> {@see self::getSqlString()} Вернет таймштамп с поправкой на часовой пояс
 * <br> {@see self::getTimezoneOffsetSec()} Вернет смещение часового пояса в секундах
 * <br>--- Получение части даты-времени
 * <br> {@see self::getYear()} Вернет год в формате 4-ех цифр (2023, 1867)
 * <br> {@see self::getMon()} Вернет номер месяца (1 - 12)
 * <br> {@see self::getWeek()} Вернет номер недели (1-52)
 * <br> {@see self::getMonDay()} Вернет число, т.е. номер для в месяце (1-31)
 * <br> {@see self::getHours()} Вернет час (0-23)
 * <br> {@see self::getMinutes()} Вернет минуты (0-59)
 * <br> {@see self::getSeconds()} Вернет секунды (0-59)
 * <br> {@see self::getYearDay()} Вернет номер дня в году (1-366)
 * <br> {@see self::getWeekDayUsa()} Вернет номер дня недели в формате США (1 - Воскресенье, 2 - Понедельник ... 7 - Суббота)
 * <br> {@see self::getWeekDay()} Вернет номер для недели в Европейском формате (1 - Понедельник ... 7 - Воскресенье)
 * <br> {@see self::getMS()} Вернет дробную часть секунды (милисекунды, микросекнуды)
 * <br>--- Изменение даты-времени
 * <br> {@see self::set()} Сменит дату-время любым представлением
 * <br> {@see self::setDateValues()} Сменит дату (или часть даты, например день или месяц)
 * <br> {@see self::setWeekDay()} Сменит неделю (и день недели)
 * <br> {@see self::moveMon()} Переместит на указанное кол-во месяцев (если надо, также сменит день месяца)
 * <br> {@see self::moveWeek()} Переместится на указанное кол-во недель (если надо, также сменит день недели)
 *
 * @todo PHP8 добавить интерфейс {@see \Stringable}
 */
class DateTimeExtendedType extends \DateTime implements GetTimestampInterface
{
    /**
     * Создание расширенного объекта для работы с датой-временем (расширяет {@see \DateTime})
     *
     * @param   mixed                $datetime    Дата-время в любом представлении, см {@see DateTimeObjectHelper::getDateObject()}
     * @param   null|\DateTimeZone   $timezone    Объект-таймзона
     *
     * @throws  \Exception   В случае провала создания даны в {@see \DateTime::__construct()}
     *
     * @todo PHP8 добавить возможность создания объекта через передачу {@see GetTimestampInterface} {@see DateTimeObjectHelper::getDateObject} уже поддерживает это (т.е. дело только в юнит-тестах)
     */
    public function __construct(null|int|float|string|array|\DateTimeInterface $datetime = null, \DateTimeZone $timezone = null)
    {
        parent::__construct(
            DateTimeObjectHelper::getDateObject($datetime, \DateTime::class)->format(DateTimeFormats::FUNCTIONS),
            $timezone
        );
    }

    /**
     * Сменит дату времени, используя любое представление
     *
     * @param   mixed   $datetime   Дата-время в любом представлении, см {@see DateTimeObjectHelper::getDateObject()}
     *
     * @return  $this
     *
     * @throws \DateMalformedStringException Если невозможно распарсить строку даты-времени
     */
    public function set(null|int|float|string|array|\DateTimeInterface|GetTimestampInterface $datetime): self
    {
        $this->modify(
            DateTimeObjectHelper::getDateObject($datetime, \DateTime::class)->format(DateTimeFormats::FUNCTIONS)
        );

        return $this;
    }

    /**
     * Преобразование объекта-даты в строку
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->format(DateTimeFormats::FUNCTIONS);
    }

    /**
     * Вернет год в формате 4-ех цифр (2023, 1867)
     *
     * @return int
     */
    public function getYear(): int
    {
        return getdate($this->getTimestamp())['year'];
    }

    /**
     * Вернет номер месяца (1-12)
     *
     * @param   int   $start   Начало отсчета (0 или 1).
     *
     * @return  int
     */
    public function getMon(int $start = 1): int
    {
        return getdate($this->getTimestamp())['mon'] - 1 + $start;
    }

    /**
     * Вернет номер недели (1-52)
     *
     * Вернет номер недели по стандарту ISO 8601, по которому первая неделя года:
     * <br> Неделя, содержащая 4 января
     * <br> Неделя, в которой 1 января это понедельник, вторник, среда или четверг
     * <br> Неделя, которая содержит как минимум четыре дня нового года
     * <br>Т.е. 52 неделя года может оказаться уже в "новом году" (например 1 января суббота, это будет 52 неделя и она будет относиться к предыдущему году)
     *
     * @param   int   $start   Начало отсчета (0 или 1).
     *
     * @return  int
     */
    public function getWeek(int $start = 1): int
    {
        return (int)$this->format('W') - 1  + $start;
    }

    /**
     * Вернет число, т.е. номер для в месяце (1-31)
     *
     * @param   int   $start   Начало отсчета (0 или 1).
     *
     * @return  int
     */
    public function getMonDay(int $start = 1): int
    {
        return getdate($this->getTimestamp())['mday'] - 1 + $start;
    }

    /**
     * Вернет кол-во часов (0-23)
     *
     * @param   int   $start   Начало отсчета (0 или 1).
     *
     * @return  int
     */
    public function getHours(int $start = 0): int
    {
        return getdate($this->getTimestamp())['hours'] + $start;
    }

    /**
     * Вернет кол-во минут (0-59)
     *
     * @param   int   $start   Начало отсчета (0 или 1).
     *
     * @return  int
     */
    public function getMinutes(int $start = 0): int
    {
        return getdate($this->getTimestamp())['minutes'] + $start;
    }

    /**
     * Вернет кол-во секунд (0-59)
     *
     * @param   int   $start   Начало отсчета (0 или 1).
     *
     * @return  int
     */
    public function getSeconds(int $start = 0): int
    {
        return getdate($this->getTimestamp())['seconds'] + $start;
    }

    /**
     * Вернет номер дня в году (1-366)
     *
     * @param   int   $start   Начало отсчета (0 или 1). Позволяет получать номер дня недели совместимый с {@see getdate()}['yday']
     *
     * @return  int
     */
    public function getYearDay(int $start = 1): int
    {
        return getdate($this->getTimestamp())['yday'] + $start;
    }

    /**
     * Вернет номер дня недели в формате США (1 - Воскресенье, 2 - Понедельник ... 7 - Суббота)
     *
     * @param   int   $start   Начало отсчета (0 или 1). Позволяет получать номер дня недели совместимый с {@see getdate()}['wday']
     *
     * @return  int
     */
    public function getWeekDayUsa(int $start = 1): int
    {
        return getdate($this->getTimestamp())['wday'] + $start;
    }

    /**
     * Вернет номер дня недели в европейском формате (1 - Понедельник ... 7 - Воскресенье)
     *
     * @param   int   $start   Начало отсчета (0 или 1)
     *
     * @return  int
     */
    public function getWeekDay(int $start = 1): int
    {
        $usaWeekDay = getdate($this->getTimestamp())['wday'];

        if ($usaWeekDay === 0) return 6 + $start;
        else return $usaWeekDay - 1 + $start;
    }

    /**
     * Вернет кол-во секунд с начала текущих суток
     *
     * @return int
     */
    public function getTimeSecondFormat(): int
    {
        return DateTimeHelper::getDaySecFromDateTime($this);
    }

    /**
     * Вернет дробную часть секунды (милисекунды, микросекнуды)
     *
     * @param   bool|int   $size           Что нужно вернуть:
     *                                     <br> TRUE: микросекунды (т.е. 0.123456)
     *                                     <br> FALSE: милисекунды (т.е. 0.123)
     *                                     <br> int: кол-во знаков
     * @param   bool       $returnFloat    Нужно ли вернуть в FLOAT
     *
     * @return  int|float
     */
    public function getMS(bool|int $size = true, bool $returnFloat = false): int|float
    {
        // получение микросекунд (вернет ввиде строки)
        $ms = $this->format('u');

        // * * *

        if ($size === TRUE) $size = 6;
        elseif ($size === FALSE) $size = 3;
        elseif ($size < 1) return 0;

        if ($size < 6) $ms = substr($ms, 0, $size);

        // * * *

        if ($returnFloat) return floatval("0.{$ms}");
        else return intval($ms);
    }

    /**
     * Вернет смещение часового пояса в секундах
     *
     * Вернет отрицательное число для запада (Америка) и положительное для востока (Европа, Азия)
     *
     * @return int
     */
    public function getTimezoneOffsetSec(): int
    {
        return (int)$this->format('Z');
    }

    /**
     * Сгенерирует строку для применения даты-времени в SQL запросе
     *
     * @param   bool|string   $format    Формат преобразования
     *                                   <br> по умолчанию: Дата и время (ГГГГ-ММ-ДД ЧЧ:ММ:CC)
     *                                   <br> FALSE: Только время (ЧЧ:ММ:CC)
     *                                   <br> TRUE: Только дата (ГГГГ-ММ-ДД)
     *                                   <br> string: Строка с форматом ({@see Date()})
     *
     * @return  string
     */
    public function getSqlString(bool|string $format = DateTimeFormats::SQL_DATETIME): string
    {
        if ($format === true) $format = DateTimeFormats::SQL_DATE;
        elseif ($format === false) $format = DateTimeFormats::SQL_TIME;

        return $this->format($format);
    }

    /**
     * Сменит дату (или часть даты, например день или месяц)
     *
     * (!) При передаче значениям NULL (значение не меняется), надо быть готовым, что все таки поменяется. Например,
     * ткущая дата "2022-01-31", проводится установка даты "NULL-02-NULL", будет установлено "2022-02-28", так как 31 февраля не существует
     *
     * @param   null|int   $mday           Новое число (NULL - если не меняется), формат 1-31
     * @param   null|int   $year           Новый год (NULL - если не меняется), формат 4 цифры
     * @param   null|int   $mon            Новый месяц (NULL - если не меняется), формат 1-12
     * @param   bool       $withOutRange   Разрешить расширенные диапазоны, будет работать аналогично {@see mktime} (к примеру
     *                                     при попытке установить 13 месяц, установит +1 год и январь)
     *
     * @return  $this
     */
    public function setDateValues(null|int $mday = null, null|int $mon = null, null|int $year = null, bool $withOutRange = false): self
    {
        // если для каких-то параметров нужно взять текущие значения
        if ($year === null) $year = $this->getYear();
        if ($mon === null) $mon = $this->getMon();
        if ($mday === null) $mday = $this->getMonDay();

        // Если надо проверить корректность месяца или числа
        if (!$withOutRange) DateTimeValidator::validMonAndDay($year, $mon, $mday);

        // * * *

        $this->setDate($year, $mon, $mday);

        return $this;
    }

    /**
     * Установит неделю, и, если надо, номер дня недели (неделя: 1-52, номер дня недели: 1-7)
     *
     * Вернет номер недели по стандарту ISO 8601, по которому первая неделя года:
     * <br> Неделя, содержащая 4 января
     * <br> Неделя, в которой 1 января это понедельник, вторник, среда или четверг
     * <br> Неделя, которая содержит как минимум четыре дня нового года
     * <br>Т.е. 52 неделя года может оказаться уже в "новом году" (например 1 января суббота, это будет 52 неделя и она будет относиться к предыдущему году)
     *
     * @see  TimestampHelper::getWeekDay() Вернет таймштамп для указанной недели
     *
     * @see  DateTime::setISODate() Аналогичная встроенная функция. Требует указывать год, неделю и день недели
     *
     * @param   null|int   $year     Номер года (NULL - не меняется)
     * @param   null|int   $week     Номер недели (NULL - не меняется), Отсчет от 1
     * @param   null|int   $day      Номер для дня недели (NULL - не меняется), 1 понедельник ... 7 воскресенье
     * @param   mixed      $endDay   Указание времени, см {@see DateTimeHelper::getTimeString}, за исключением NULL - не меняется
     *
     * @return  $this
     *
     * @throws  \DateMalformedStringException  Если была передана строка, которую невозможно конвертировать в дату-время
     *
     * @todo Если время не меняется, не должны сбрасываться микросекнуды
     */
    public function setWeekDay(null|int $year, null|int $week, null|int $day, mixed $endDay = null): self
    {
        if ($year === null) $year = $this->getYear();
        if ($week === null) $week = $this->getWeek();
        if ($day === null) $day = $this->getWeekDay();
        if ($endDay === null) $endDay = $this->getTimeSecondFormat();

        $this->modify(
            date(DateTimeFormats::FUNCTIONS, TimestampHelper::getWeekDay($year, $week, $day, $endDay))
        );

        return $this;
    }

    /**
     * Переместит на указанное кол-во месяцев (если надо, также сменит день месяца)
     *
     * @param   int        $mon       Перемещение месяца
     * @param   null|int   $day       Номер для месяца (1-31), если в месяце нет указанного дня, будет установлен ближайший
     *                                день (например 31 февраля будет 28 или 29 февраля). NULL - по возможности оставит
     *                                текущий день месяца. Что бы гарантированно указывать на "последний" день любого месяца
     *                                следует установить параметр в 31
     * @param   mixed      $endDay    Указание времени, см {@see DateTimeHelper::getTimeString}. NULL - время не будет меняться.
     *
     * @return  $this
     *
     * @throws  \DateMalformedStringException  Если была передана строка, которую невозможно конвертировать в дату-время
     *
     * @todo Отработать "0" значения (т.е. без возможных перемещений)
     */
    public function moveMon(int $mon, null|int $day = null, mixed $endDay = null): self
    {
        if ($day === null) $day = $this->getMonDay();

        $this->modify("first day of {$mon} month");

        if ($day > 1)
        {
            $nowMon = $this->getMon();
            if ($day > DateConstants::MON_DAY_COUNT_LIST[$nowMon]) $day = DateConstants::MON_DAY_COUNT_LIST[$nowMon];
            $this->modify($day - 1 . 'day');
        }

        if ($endDay !== null)
        {
            $this->modify(DateTimeHelper::getTimeString($endDay));
        }

        return $this;
    }

    /**
     * Переместится на указанное кол-во недель (если надо, также сменит день недели)
     *
     * Номер недели по стандарту ISO 8601, по которому первая неделя года:
     * <br> Неделя, содержащая 4 января
     * <br> Неделя, в которой 1 января это понедельник, вторник, среда или четверг
     * <br> Неделя, которая содержит как минимум четыре дня нового года
     * <br>Т.е. 52 неделя года может оказаться уже в "новом году" (например 1 января суббота, это будет 52 неделя и она будет относиться к предыдущему году)
     *
     * @param   int        $week     Смещение в неделях
     * @param   null|int   $day      Номер для недели (NULL - если ненужно менять), 1 понедельник ... 7 воскресенье
     * @param   mixed      $endDay   Указание времени, см {@see DateTimeHelper::getTimeString}. NULL - время не будет меняться.
     *
     * @return  $this
     *
     * @throws  \DateMalformedStringException  Если была передана строка, которую невозможно конвертировать в дату-время
     *
     * @todo Отработать "0" значения (т.е. без возможных перемещений)
     */
    public function moveWeek(int $week, null|int $day = null, mixed $endDay = null): self
    {
        if ($day === null) $diffDay = 0;
        else $diffDay = $this->getWeekDay() - $day;

        $modifyString = ($week * 7 - $diffDay) . ' day';
        if ($endDay !== null) $modifyString .= ' ' . DateTimeHelper::getTimeString($endDay);

        $this->modify($modifyString);

        return $this;
    }
}
