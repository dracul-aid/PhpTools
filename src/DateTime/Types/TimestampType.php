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

use DraculAid\PhpTools\DateTime\TimestampHelper;
use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;

/**
 * Работа с таймштампами, как с объектами (таймштамп в формате секунд)
 *
 * Оглавление:
 * <br>{@see self::setTimestamp()} Установит таймштамп (в секундах)
 * <br>{@see self::getTimestamp()} Вернет таймштамп (в секундах)
 * <br>{@see self::format()} Вернет строковое представление даты-времени
 */
class TimestampType implements GetTimestampInterface
{
    /** Таймштамп в секундах */
    protected int $timestamp;

    /**
     * Создаст объект для работы с таймштампами (в секундах), как с объектами
     *
     * @param   mixed   $dateTime   Дата-время, см {@see TimestampHelper::getTimestamp()}
     */
    public function __construct($dateTime = null)
    {
        $this->setTimestamp($dateTime);
    }

    public function __toString()
    {
        return $this->format(DateTimeFormats::FUNCTIONS);
    }

    /**
     * Установит таймштамп (в секундах)
     *
     * @param   mixed   $dateTime   Дата-время, см {@see TimestampHelper::getTimestamp()}
     *
     * @return  $this
     */
    public function setTimestamp($dateTime): self
    {
        $this->timestamp = TimestampHelper::getTimestamp($dateTime);

        return $this;
    }

    /** @inheritdoc */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /** @inheritdoc */
    public function format(string $format): string
    {
        return date($format, $this->timestamp);
    }
}
