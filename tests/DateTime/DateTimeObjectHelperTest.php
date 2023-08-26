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
 * Test for {@see DateTimeObjectHelper}
 *
 * @run php tests/run.php tests/DateTime/DateTimeObjectHelperTest.php
 */
class DateTimeObjectHelperTest extends AbstractProjectTestCase
{
    /**
     * Test for {@see DateTimeObjectHelper::getDateObject()}
     *
     * @return void
     */
    public function testGetDateObject(): void
    {
        $testTimestamp = new \DateTime('2018-09-05 1:02:08.123456');

        // * * * Аргументы, приводящие к созданию объекта

        self::assertTimestamp(
            time(),
            DateTimeObjectHelper::getDateObject()->getTimestamp()
        );
        self::assertTimestamp(
            time(),
            DateTimeObjectHelper::getDateObject(null)->getTimestamp()
        );
        self::assertTimestamp(
            $testTimestamp->getTimestamp(),
            DateTimeObjectHelper::getDateObject($testTimestamp->getTimestamp())->getTimestamp()
        );
        self::assertEquals(
            $testTimestamp->format(DateTimeFormats::TIMESTAMP_WITH_MICROSECONDS),
            DateTimeObjectHelper::getDateObject((float)$testTimestamp->format(DateTimeFormats::TIMESTAMP_WITH_MICROSECONDS))->format(DateTimeFormats::TIMESTAMP_WITH_MICROSECONDS)
        );
        self::assertTimestamp(
            $testTimestamp->getTimestamp(),
            DateTimeObjectHelper::getDateObject('2018-09-05 1:02:08')->getTimestamp()
        );
        self::assertTimestamp(
            $testTimestamp->getTimestamp(),
            DateTimeObjectHelper::getDateObject(['year' => 2018, 'mon' => 9, 'mday' => 5, 'hours' => 1, 'minutes' => 2, 'seconds' => 8])->getTimestamp()
        );

        // * * * Аргумент-объект, который будет возвращен в неизменном виде

        $testObject = new DateTimeExtendedType('2018-09-05 1:02:08.123456');

        self::assertTimestamp(
            $testObject->getTimestamp(),
            DateTimeObjectHelper::getDateObject($testObject)->getTimestamp()
        );
        self::assertTrue($testObject === DateTimeObjectHelper::getDateObject($testObject));

        self::assertTimestamp(
            $testObject->getTimestamp(),
            DateTimeObjectHelper::getDateObject($testObject, DateTimeExtendedType::class)->getTimestamp()
        );
        self::assertTrue($testObject === DateTimeObjectHelper::getDateObject($testObject));


        // * * * Аргументы-даты ввиде объектов, приводящих к созданию новых объектов

        self::assertTimestamp(
            $testTimestamp->getTimestamp(),
            DateTimeObjectHelper::getDateObject(new \DateTime('2018-09-05 1:02:08.123456'))->getTimestamp()
        );

        self::assertTimestamp(
            $testTimestamp->getTimestamp(),
            DateTimeObjectHelper::getDateObject(new TimestampType('2018-09-05 1:02:08.123456'))->getTimestamp()
        );

        self::assertEquals(
            DateTimeExtendedType::class,
            get_class(DateTimeObjectHelper::getDateObject(new \DateTimeImmutable('2018-09-05 1:02:08.123456')))
        );
    }

    /**
     * Test for {@see DateTimeObjectHelper::copyDateTimeObject()}
     *
     * @return void
     */
    public function testCopyDateTimeObject(): void
    {
        self::assertEquals(
            TimestampType::class,
            get_class(DateTimeObjectHelper::copyDateTimeObject(new TimestampType()))
        );

        self::assertEquals(
            TimestampType::class,
            get_class(DateTimeObjectHelper::copyDateTimeObject(new \DateTime(), TimestampType::class))
        );

        $testObject = new TimestampType();
        self::assertTrue(
            TimestampType::class !== DateTimeObjectHelper::copyDateTimeObject($testObject)
        );

        self::assertEquals(
            strtotime('2022-06-15 12:30:30'),
            DateTimeObjectHelper::copyDateTimeObject(new \DateTime('2022-06-15 12:30:30'), TimestampType::class)->getTimestamp()
        );
    }

    /**
     * Test for {@see DateTimeObjectHelper::isGetTimestamp()}
     *
     * @return void
     */
    public function testIsGetTimestamp(): void
    {
        self::assertFalse(DateTimeObjectHelper::isGetTimestamp(new \stdClass()));

        self::assertTrue(DateTimeObjectHelper::isGetTimestamp(new \DateTime()));
        self::assertTrue(DateTimeObjectHelper::isGetTimestamp(new \DateTimeImmutable()));
        self::assertTrue(DateTimeObjectHelper::isGetTimestamp(new TimestampType()));

        // * * * Проверка функции, возвращающей таймштамп

        $objectWithFunction = new class(){public function getTimestamp(){return 123123;}};
        $objectWithoutFunction = new class(){};

        self::assertFalse(DateTimeObjectHelper::isGetTimestamp($objectWithFunction));
        self::assertFalse(DateTimeObjectHelper::isGetTimestamp($objectWithFunction, false));
        self::assertTrue(DateTimeObjectHelper::isGetTimestamp($objectWithFunction, true));

        self::assertFalse(DateTimeObjectHelper::isGetTimestamp($objectWithoutFunction, true));
    }
}
