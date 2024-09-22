<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\DateTime\Types;

use DraculAid\Php8forPhp7\LoaderPhp8Lib;
use DraculAid\PhpTools\DateTime\DateTimeHelper;
use DraculAid\PhpTools\DateTime\DateTimeValidator;
use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;

/** @todo PHP8 убрать загрузку функции */
LoaderPhp8Lib::loadFunction('str_starts_with');

/**
 * Минималистичный объект для хранения даты-времени
 *
 * Оглавление:
 * <br> {@see DateTimeLightType::getTimestamp()} Вернет таймштамп установленной даты-времени
 * <br> {@see DateTimeLightType::format()} Вернет форматированную строку с представлением даты-времени
 * <br>--- Доступ через свойства (запись и чтение)
 * <br> {@see DateTimeLightType::$year} Год (например, 2018)
 * <br> {@see DateTimeLightType::$mon} Номер месяца (1 - 12)
 * <br> {@see DateTimeLightType::$day} Номер дня месяца (1 - 31)
 * <br> {@see DateTimeLightType::$hour} Час (0 - 23)
 * <br> {@see DateTimeLightType::$minute} Минута (0 - 60)
 * <br> {@see DateTimeLightType::$second} Секунда (0 - 60)
 * <br>--- Смена даты-времени
 * <br> {@see DateTimeLightType::setDate()} Установит новую дату-время
 * <br> {@see DateTimeLightType::setYear()} Год (например, 2018)
 * <br> {@see DateTimeLightType::setMon()} Номер месяца (1 - 12)
 * <br> {@see DateTimeLightType::setDay()} Номер дня месяца (1 - 31)
 * <br> {@see DateTimeLightType::setHour()} Час (0 - 23)
 * <br> {@see DateTimeLightType::setMinutes()} Минута (0 - 60)
 * <br> {@see DateTimeLightType::setSecond()} Секунда (0 - 60)
 *
 * @property int $year Год (например, 2018)
 * @property int $mon Номер месяца (1 - 12)
 * @property int $day Номер дня месяца (1 - 31)
 * @property int $hour Час (0 - 23)
 * @property int $minute Минута (0 - 60)
 * @property int $second Секунда (0 - 60)
 *
 * @method self setYear(int $value) Установит год (например, 2018)
 * @method self setMon(int $value) Установит месяц (1 - 12)
 * @method self setDay(int $value) Установит день месяца (1 - 31)
 * @method self setHour(int $value) Установит час (0 - 23)
 * @method self setMinute(int $value) Установит минуту (0 - 60)
 * @method self setSecond(int $value) Установит секунду (0 - 60)
 *
 * @todo TEST тебует покрытия теста
 * @todo PHP8 добавить интерфейс {@see \Stringable}
 */
class DateTimeLightType implements GetTimestampInterface
{
    /** DTO с частями времени */
    protected DateTimeLightDto $dateTimeDto;

    /**
     * Создает минималистичный объект для хранения даты-времени
     *
     * @param   mixed   $dateTime   Дата-Время в любом формате, см {@see DateTimeHelper::getDateArray}
     *
     * @throws  \TypeError  Если переданная дата-временя не прошел валидацию
     *
     * @todo PHP8 типизация аргументов (null|int|float|string|array|\DateTimeInterface)
     */
    public function __construct($dateTime = null)
    {
        $this->dateTimeDto = new DateTimeLightDto;

        $this->setDate($dateTime);
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

    public function __set(string $name, $value)
    {
        if (!isset($this->dateTimeDto->{$name}))
        {
            throw new \LogicException("Class do not have property {$name}");
        }

        // валидация значений даты
        if (
            ![DateTimeValidator::class, $name]($value)
            || ($name === 'mon' && !checkdate($value, $this->dateTimeDto->day, $this->dateTimeDto->year))
            || ($name === 'day' && !checkdate($this->dateTimeDto->mon, $value, $this->dateTimeDto->year))
        ) {
            throw new \TypeError("Value {$name} is not valid");
        }

        // * * *

        $this->dateTimeDto->{$name} = $value;
    }

    public function __get(string $name): int
    {
        if (!isset($this->dateTimeDto->{$name}))
        {
            throw new \LogicException("Class do not have property {$name}");
        }

        return $this->dateTimeDto->{$name};
    }

    public function __call(string $name, array $arguments): self
    {
        $name = lcfirst(substr($name, 3));

        if (!isset($this->dateTimeDto->{$name}))
        {
            throw new \LogicException('Class do not have method set' . ucfirst($name));
        }

        // * * *

        $this->__set($name, $arguments[0]);

        return $this;
    }

    /**
     * Запишет новую дату-время
     *
     * @param   mixed   $dateTime   Дата-Время в любом формате, см {@see DateTimeHelper::getDateArray}
     *
     * @return  $this
     *
     * @throws  \TypeError  Если переданная дата-временя не прошел валидацию
     *
     * @todo PHP8 типизация аргументов (null|int|float|string|array|\DateTimeInterface)
     */
    public function setDate($dateTime): self
    {
        $dateArray = DateTimeHelper::getDateArray($dateTime);

        if (!DateTimeValidator::isValidDateAndTime($dateArray['year'], $dateArray['mon'], $dateArray['mday'], $dateArray['hours'], $dateArray['minutes'], $dateArray['seconds']))
        {
            throw new \TypeError(
                '$dateTime is not correct date time values: '
                    . "({$dateArray['year']}-{$dateArray['mon']}-{$dateArray['mday']}"
                    . "{$dateArray['hours']}:{$dateArray['minutes']}:{$dateArray['seconds']})"
            );
        }

        $this->dateTimeDto->year = $dateArray['year'];
        $this->dateTimeDto->mon = $dateArray['mon'];
        $this->dateTimeDto->day = $dateArray['mday'];
        $this->dateTimeDto->hour = $dateArray['hours'];
        $this->dateTimeDto->minute = $dateArray['minutes'];
        $this->dateTimeDto->second = $dateArray['seconds'];

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTimestamp(): int
    {
        return mktime(
            $this->dateTimeDto->hour,
            $this->dateTimeDto->minute,
            $this->dateTimeDto->second,
            $this->dateTimeDto->mon,
            $this->dateTimeDto->day,
            $this->dateTimeDto->year
        );
    }

    /**
     * @inheritdoc
     */
    public function format(string $format): string
    {
        return date($format, $this->getTimestamp());
    }
}
