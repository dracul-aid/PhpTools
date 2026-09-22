<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\DateTime;

use DraculAid\PhpTools\DateTime\DateTimeObjectHelper;
use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;
use DraculAid\PhpTools\DateTime\Types\PhpExtended\DateTimeExtendedType;
use DraculAid\PhpTools\DateTime\Types\TimestampType;
use DraculAid\PhpTools\tests\AbstractProjectTestCase;

/**
 * Test for {@coversDefaultClass DateTimeObjectHelper}
 *
 * @run php tests/run.php tests/DateTime/DateTimeObjectHelperTest.php
 */
class DateTimeObjectHelperTest extends AbstractProjectTestCase
{
    /**
     * Test for {@covers DateTimeObjectHelper::getDateObject()}
     *
     * @return void
     */
    public function testGetDateObject(): void
    {
        $testFunction = DateTimeObjectHelper::getDateObject(...);

        $testTimestamp = new \DateTime('2018-09-05 1:02:08.123456');

        // * * * Аргументы, приводящие к созданию объекта

        self::assertTimestamp(
            time(),
            $testFunction()->getTimestamp()
        );
        self::assertTimestamp(
            time(),
            $testFunction(null)
                ->getTimestamp()
        );
        self::assertTimestamp(
            $testTimestamp->getTimestamp(),
            $testFunction($testTimestamp->getTimestamp())
                ->getTimestamp()
        );
        self::assertEquals(
            $testTimestamp->format(DateTimeFormats::TIMESTAMP_WITH_MICROSECONDS),
            $testFunction(
                    (float)$testTimestamp->format(DateTimeFormats::TIMESTAMP_WITH_MICROSECONDS)
                )->format(DateTimeFormats::TIMESTAMP_WITH_MICROSECONDS)
        );
        self::assertTimestamp(
            $testTimestamp->getTimestamp(),
            $testFunction('2018-09-05 1:02:08')
                ->getTimestamp()
        );
        self::assertTimestamp(
            $testTimestamp->getTimestamp(),
            $testFunction(
                    ['year' => 2018, 'mon' => 9, 'mday' => 5, 'hours' => 1, 'minutes' => 2, 'seconds' => 8]
                )
                ->getTimestamp()
        );

        // * * * Аргумент-объект, который будет возвращен в неизменном виде

        $testObject = new DateTimeExtendedType('2018-09-05 1:02:08.123456');

        self::assertTimestamp(
            $testObject->getTimestamp(),
            $testFunction($testObject)
                ->getTimestamp()
        );
        self::assertTrue($testObject === $testFunction($testObject));

        self::assertTimestamp(
            $testObject->getTimestamp(),
            $testFunction($testObject, DateTimeExtendedType::class)
                ->getTimestamp()
        );
        self::assertTrue($testObject === $testFunction($testObject));


        // * * * Аргументы-даты ввиде объектов, приводящих к созданию новых объектов

        self::assertTimestamp(
            $testTimestamp->getTimestamp(),
            $testFunction(new \DateTime('2018-09-05 1:02:08.123456'))
                ->getTimestamp()
        );

        self::assertTimestamp(
            $testTimestamp->getTimestamp(),
            $testFunction(new TimestampType('2018-09-05 1:02:08.123456'))
                ->getTimestamp()
        );

        self::assertEquals(
            DateTimeExtendedType::class,
            get_class($testFunction(new \DateTimeImmutable('2018-09-05 1:02:08.123456')))
        );
    }

    /**
     * Test for {@covers DateTimeObjectHelper::copyDateTimeObject()}
     *
     * @return void
     */
    public function testCopyDateTimeObject(): void
    {
        $testFunction = DateTimeObjectHelper::copyDateTimeObject(...);

        self::assertEquals(
            TimestampType::class,
            get_class($testFunction(new TimestampType()))
        );

        self::assertEquals(
            TimestampType::class,
            get_class($testFunction(new \DateTime(), TimestampType::class))
        );

        $testObject = new TimestampType();
        self::assertTrue(
            TimestampType::class !== $testFunction($testObject)
        );

        self::assertEquals(
            strtotime('2022-06-15 12:30:30'),
            $testFunction(new \DateTime('2022-06-15 12:30:30'), TimestampType::class)
                ->getTimestamp()
        );
    }

    /**
     * Test for {@covers DateTimeObjectHelper::isGetTimestamp()}
     *
     * @return void
     */
    public function testIsGetTimestamp(): void
    {
        $testFunction = DateTimeObjectHelper::isGetTimestamp(...);

        self::assertFalse($testFunction(new \stdClass()));

        self::assertTrue($testFunction(new \DateTime()));
        self::assertTrue($testFunction(new \DateTimeImmutable()));
        self::assertTrue($testFunction(new TimestampType()));

        // * * * Проверка функции, возвращающей таймштамп

        $objectWithFunction = new class(){public function getTimestamp(): int {return 123123;}};
        $objectWithoutFunction = new class(){};

        self::assertFalse($testFunction($objectWithFunction));
        self::assertFalse($testFunction($objectWithFunction, false));
        self::assertTrue($testFunction($objectWithFunction, true));

        self::assertFalse($testFunction($objectWithoutFunction, true));
    }
}
