<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\DateTime\Clock\Abstraction;

use DraculAid\PhpTools\DateTime\Clock\BigClock;
use DraculAid\PhpTools\DateTime\Clock\BigClockProxy;
use DraculAid\PhpTools\DateTime\Clock\FrozenBigClock;
use DraculAid\PhpTools\DateTime\Clock\RealBigClock;
use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;
use DraculAid\PhpTools\DateTime\TimestampHelper;
use DraculAid\PhpTools\tests\DateTime\Clock\AbstractBigClockTestClass;

/**
 * Абстрактный класс для получения часов текущего времени
 *
 * Основные реализации
 * <br>+ {@see BigClock} - Часы с возможностью установить поправку в секундах
 * <br>+ {@see FrozenBigClock} - Часы с возможностью "заморозить" время (идеально подходят для тестов)
 * <br>+ {@see RealBigClock} - Часы, без возможности повлиять на возвращаемое время
 * <br>+ {@see BigClockProxy} - Прокси-класс для использования {@see \Psr\Clock\ClockInterface} которые уже есть на проекте
 *
 * Оглавление:
 * <br>+{@see self::now()} - Вернет немутабельный объект даты времени ({@see \DateTimeImmutable})
 * <br>+{@see self::timestamp()} - Вернет таймштамп текущего времени
 * <br>+{@see self::timestampJs()} - Вернет таймштамп текущего времени в JS форматера (1 сек = 1000 мс)
 * <br>+{@see self::microtime()} - Вернет кол-во секунд и долей секунд
 * <br>+{@see self::format()} - Вернет текущую дату-время в указанном формате
 * <br>+{@see self::dateTime()} - Вернет мутабельный объект даты времени ({@see \DateTime})
 * <br>+{@see self::object()} - Вернет дату-время ввиде указанного объекта {@see \DateTimeInterface}
 *
 * Test cases for class {@see AbstractBigClockTestClass}
 *
 * @since 1.3.0
 */
abstract class AbstractBigClock implements BigClockInterface
{
    /** @inheritdoc */
    abstract public function microtime(): float;

    /** @inheritdoc */
    public function object(string $className): \DateTimeInterface
    {
        return new $className(
            TimestampHelper::toString($this->microtime(),DateTimeFormats::FUNCTIONS)
        );
    }

    /** @inheritdoc */
    public function now(): \DateTimeImmutable
    {
        return $this->object(\DateTimeImmutable::class);
    }

    /**
     * @inheritdoc
     *
     * @psalm-suppress LessSpecificReturnStatement По факту тут всегда вернется положительное число, т.е. тип ответа будет сохранен
     * @psalm-suppress MoreSpecificReturnType По факту тут всегда вернется положительное число, т.е. тип ответа будет сохранен
     */
    public function timestampJs(): int
    {
        return TimestampHelper::toJsTimestamp($this->microtime());
    }

    /**
     * @inheritdoc
     *
     * @psalm-suppress LessSpecificReturnStatement По факту тут всегда вернется положительное число, т.е. тип ответа будет сохранен
     * @psalm-suppress MoreSpecificReturnType По факту тут всегда вернется положительное число, т.е. тип ответа будет сохранен
     */
    public function timestamp(): int
    {
        return (int)$this->microtime();
    }

    /** @inheritdoc */
    public function format(string $format): string
    {
        return date($format, $this->timestamp());
    }

    /** @inheritdoc */
    public function dateTime(): \DateTime
    {
        return $this->object(\DateTime::class);
    }
}
