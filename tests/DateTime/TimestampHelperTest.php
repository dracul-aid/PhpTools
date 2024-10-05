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

use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;
use DraculAid\PhpTools\DateTime\NowTimeGetter;
use DraculAid\PhpTools\DateTime\TimestampHelper;
use DraculAid\PhpTools\DateTime\Types\TimestampType;
use DraculAid\PhpTools\ExceptionTools\ExceptionTools;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see TimestampHelper}
 *
 * @run php tests/run.php tests/DateTime/TimestampHelperTest.php
 */
class TimestampHelperTest extends TestCase
{
    /**
     * Test for {@see TimestampHelper::toJsTimestamp()}
     *
     * @return void
     */
    public function testToJsTimestamp(): void
    {
        $timestamp = time();
        self::assertEquals((string)($timestamp * 1000), TimestampHelper::toJsTimestamp($timestamp));
        self::assertEquals((string)($timestamp * 1000), TimestampHelper::toJsTimestamp($timestamp, false));

        $timeObject = new \DateTime('2018-09-05 1:02:08.123456');
        self::assertEquals((string)($timeObject->getTimestamp() * 1000 + 123), TimestampHelper::toJsTimestamp($timeObject));
        self::assertEquals((string)($timeObject->getTimestamp() * 1000 + 123), TimestampHelper::toJsTimestamp($timeObject, false));
    }

    /**
     * Test for {@see TimestampHelper::toString()}
     *
     * @return void
     */
    public function testToString(): void
    {
        $trueDateString = new \DateTime('2018-09-05 1:02:08.000000');
        self::assertEquals(
            $trueDateString->format(DateTimeFormats::FUNCTIONS),
            TimestampHelper::toString($trueDateString->getTimestamp())
        );

        $trueDateString = new \DateTime('2018-09-05 1:02:08.123456');
        self::assertEquals(
            $trueDateString->format(DateTimeFormats::FUNCTIONS),
            TimestampHelper::toString($trueDateString->getTimestamp() + 0.123456)
        );

        $trueDateString = new \DateTime('2018-09-05 1:02:08.000000');
        self::assertEquals(
            $trueDateString->format(DateTimeFormats::VIEW_FOR_PEOPLE),
            TimestampHelper::toString($trueDateString->getTimestamp(), DateTimeFormats::VIEW_FOR_PEOPLE)
        );
    }

    /**
     * Test for {@see TimestampHelper::getTimestamp()}
     *
     * Не проверяет $dateTime = array(), так как, он проверяется в {@see self::testGetdateArrayToTimestamp()}
     *
     * @return void
     *
     * @todo Доработь тест
     */
    public function testGetTimestamp(): void
    {
        $testTimestamp = strtotime('2018-09-05 1:02:08');

        // * * * Преобразование в таймштамп

        $testCases = [
            0 => [time(), [null]],
            1 => [$testTimestamp, [$testTimestamp]],
            2 => [$testTimestamp, [$testTimestamp + 0.123456]],
            3 => [$testTimestamp, ['2018-09-05 1:02:08.123456']],
            4 => [$testTimestamp, [['year' => 2018, 'mon' => 9, 'mday' => 5, 'hours' => 1, 'minutes' => 2, 'seconds' => 8]]],
            5 => [$testTimestamp, [new \DateTime('2018-09-05 1:02:08.123456')]],
            6 => [$testTimestamp, [new \DateTimeImmutable('2018-09-05 1:02:08.123456')]],
            7 => [$testTimestamp, [new TimestampType('2018-09-05 1:02:08.123456')]],
            8 => [123123, [new class(){public function getTimestamp(): int {return 123123;}}]],
        ];

        $this->callTestFunctionList([TimestampHelper::class, 'getTimestamp'], $testCases);

        // * * * Проверка аргументов неверного типа

        // TODO реализовать проверку неверного типа
    }

    /**
     * Test for {@see TimestampHelper::getdateArrayToTimestamp()}
     *
     * @return void
     */
    public function testGetdateArrayToTimestamp(): void
    {
        // Если все нужные данные есть

        $testTimestamp = time();
        $arrDate = getdate($testTimestamp);

        $testCases = [
            0 => [$testTimestamp, [$arrDate]],
            1 => [$testTimestamp, [['year' => $arrDate['year'], 'yday' => $arrDate['yday']]]],
            2 => [$testTimestamp, [['year' => $arrDate['year'], 'mon' => $arrDate['mon'], 'mday' => $arrDate['mday']]]],
            3 => [time(), [['year' => null, 'yday' => null]]],
            4 => [time(), [['year' => null, 'mon' => null, 'mday' => null]]],
        ];

        $this->callTestFunctionList([TimestampHelper::class, 'getdateArrayToTimestamp'], $testCases);

        // * * * Если нет необходимых данных

        self::assertTrue(ExceptionTools::wasCalledWithException(
            [TimestampHelper::class, 'getdateArrayToTimestamp'],
            [],
            \TypeError::class,
        ));

        self::assertTrue(ExceptionTools::wasCalledWithException(
            [TimestampHelper::class, 'getdateArrayToTimestamp'],
            [['year' => 123]],
            \TypeError::class,
        ));

        self::assertTrue(ExceptionTools::wasCalledWithException(
            [TimestampHelper::class, 'getdateArrayToTimestamp'],
            [['year' => 123, 'mon' => 55]],
            \TypeError::class,
        ));

        self::assertTrue(ExceptionTools::wasCalledWithException(
            [TimestampHelper::class, 'getdateArrayToTimestamp'],
            [['year' => 123, 'mday' => 55]],
            \TypeError::class,
        ));
    }

    /**
     * Test for {@see TimestampHelper::getYearDay()}
     *
     * @return void
     */
    public function testGetYearDay(): void
    {
        $nowYear = NowTimeGetter::getYear();
        $nowYearDay = NowTimeGetter::getYearDay();

        $testCases = [
            0 => [strtotime('2022-01-10 0:00:00'), [2022, 10]],
            1 => [strtotime('2022-01-10 0:00:00'), [2022, 10, false]],
            2 => [strtotime('2022-01-10 23:59:59'), [2022, 10, true]],
            3 => [strtotime('2022-01-10 12:30:30'), [2022, 10, '12:30:30']],
            4 => [strtotime('2021-12-31 0:00:00'), [2022, 0]],
            5 => [strtotime('2023-01-01 0:00:00'), [2022, 366]],
            6 => [strtotime("{$nowYear}-01-00 0:00:00 + {$nowYearDay} day"), [null, null]],
        ];

        $this->callTestFunctionList([TimestampHelper::class, 'getYearDay'], $testCases);
    }

    /**
     * Test for {@see TimestampHelper::getMonDay()}
     *
     * @return void
     */
    public function testGetMonDay(): void
    {
        $nowDate = new \DateTime();

        $testCases = [
            0 => [strtotime('2022-05-10 0:00:00'), [2022, 5, 10]],
            1 => [strtotime('2022-05-10 23:59:59'), [2022, 5, 10, true]],
            2 => [strtotime('2022-05-10 0:00:00'), [2022, 5, 10, false]],
            3 => [strtotime('2022-05-10 12:30:30'), [2022, 5, 10, '12:30:30']],
            // Проверки с NULL (замещаются текущими значениями даты)
            4 => [strtotime($nowDate->format('Y-05-10 0:00:00')), [null, 5, 10]],
            5 => [strtotime($nowDate->format('2022-m-10 0:00:00')), [2022, null, 10]],
            6 => [strtotime($nowDate->format('2022-5-d 0:00:00')), [2022, 5, null]],
        ];

        $this->callTestFunctionList([TimestampHelper::class, 'getMonDay'], $testCases);

        // * * * Проверка передачи невалидной даты

        self::assertTrue(ExceptionTools::wasCalledWithException(
            [TimestampHelper::class, 'getMonDay'],
            [2022, 13, 10],
            \LogicException::class
        ));

        self::assertTrue(ExceptionTools::wasCalledWithException(
            [TimestampHelper::class, 'getMonDay'],
            [2022, 2, 30],
            \LogicException::class
        ));
    }

    /**
     * Test for {@see TimestampHelper::getFirstWeek()}
     * Test for {@see TimestampHelper::getWeekDay()}
     *
     * @return void
     */
    public function testGetFirstWeek(): void
    {
        $testCases = [
            0 => [strtotime('2023-01-02'), [2023]], // 1-января 2023: воскресенье
            1 => [strtotime('2022-01-03'), [2022]], // 1-января 2022: суббота
            2 => [strtotime('2021-01-04'), [2021]], // 1-января 2021: пятница
            3 => [strtotime('2020-01-01'), [2020]], // 1-января 2020: среда
            4 => [strtotime('2019-01-01'), [2019]], // 1-января 2019: вторник
            5 => [strtotime('2018-01-01'), [2018]], // 1-января 2018: понедельник
            6 => [strtotime('2026-01-01'), [2026]], // 1-января 2026: четверг
        ];

        $this->callTestFunctionList([TimestampHelper::class, 'getFirstWeek'], $testCases);

        // * * *

        $nowDate = new \DateTime();

        $testCases = [
            0 => [strtotime('2023-01-02 0:00:00'), [2023, 1, 1]],
            1 => [strtotime('2023-01-03 0:00:00'), [2023, 1, 2]],
            2 => [strtotime('2023-01-02 0:00:00'), [2023, 1, 1, false]],
            3 => [strtotime('2023-01-02 23:59:59'), [2023, 1, 1, true]],
            4 => [strtotime('2023-01-02 02:10:21'), [2023, 1, 1, 2*3600 + 10*60 + 21]],
            // Значения по умолчанию
            // ВНИМАНИЕ: эту строчку надо править, так как в ней берется "год по умолчанию", т.е. каждый год, юнит-тест
            // будет падать, так как год меняется, а значит меняется и начало первой недели
            5 => [strtotime($nowDate->format('Y-01-01 0:00:00')), [null, 1, 1]],
        ];

        $this->callTestFunctionList([TimestampHelper::class, 'getWeekDay'], $testCases);
    }

    /**
     * Вызов тестовых кейсов
     *
     * @param callable $function Вызываемая функция
     * @param array $testCases Список кейсов (каждый кейс - массив [ожидаемое значение при проверке, аргументы функции]
     * @return void
     */
    private function callTestFunctionList(callable $function, array $testCases): void
    {
        foreach ($testCases as $case => [$trueValue, $arguments])
        {
            $this->callTestFunction($function, $case, $trueValue, $arguments);
        }
    }

    /**
     * Вызов тестируемого метода
     *
     * @param callable $function Вызываемая функция
     * @param int $case Номер тест-кейса
     * @param mixed $trueValue Ожидаемое значение при проверке
     * @param array $arguments Aргументы функции
     * @return void
     */
    private function callTestFunction(callable $function, int $case, $trueValue, array $arguments): void
    {
        $functionResult = $function(...$arguments);

        self::assertEquals(
            $trueValue,
            $functionResult,
            "#{$case} Return " . TimestampHelper::toString($functionResult)
            . ", but will be " . TimestampHelper::toString($trueValue)
        );
    }
}
