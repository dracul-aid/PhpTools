<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Console;

use DraculAid\PhpTools\Console\ConsoleArgumentsFromPhpArgvCreator;
use DraculAid\PhpTools\Console\ConsoleArgumentsFromString;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@coversDefaultClass ConsoleArgumentsFromString}
 *
 * @run php tests/run.php tests/Console/ConsoleArgumentsFromStringTest.php
 */
class ConsoleArgumentsFromStringTest extends TestCase
{
    /**
     * @covers ConsoleArgumentsFromString::exe()
     *
     * @return void
     */
    public function testRun(): void
    {
        // смотрим, что ничего не падает с пустой строкой
        $argumentsObject = ConsoleArgumentsFromString::exe('');
        self::assertEquals([], iterator_to_array($argumentsObject->getIterator()));
        self::assertEquals([], iterator_to_array($argumentsObject->getIterator(true)));

        // * * * "Безымянные" команды

        // 1 "безымянная" команда
        $argumentsObject = ConsoleArgumentsFromString::exe('first');
        self::assertEquals(['first'], iterator_to_array($argumentsObject->getIterator()));
        self::assertEquals([], iterator_to_array($argumentsObject->getIterator(true)));

        // 1 "безымянная" команда + ложные пробелы
        $argumentsObject = ConsoleArgumentsFromString::exe('    second ');
        self::assertEquals(['second'], iterator_to_array($argumentsObject->getIterator()));
        self::assertEquals([], iterator_to_array($argumentsObject->getIterator(true)));

        // куча разных вариантов "безымянных" команд + ложные пробелы
        $argumentsObject = ConsoleArgumentsFromString::exe('aaa  "bb zz" \'ccc \'   `ddd`');
        self::assertEquals(['aaa', 'bb zz', 'ccc ', 'ddd'], iterator_to_array($argumentsObject->getIterator()));
        self::assertEquals([], iterator_to_array($argumentsObject->getIterator(true)));

        // безымянная команда с равенством
        $argumentsObject = ConsoleArgumentsFromString::exe('*abc*=ddd a=b=c');
        self::assertEquals(['*abc*=ddd', 'a=b=c'], iterator_to_array($argumentsObject->getIterator()));
        self::assertEquals([], iterator_to_array($argumentsObject->getIterator(true)));

        // * * * "Именованные" команды

        // Одна именованная команда
        $argumentsObject = ConsoleArgumentsFromString::exe('-f=123');
        self::assertEquals(['123'], iterator_to_array($argumentsObject->getIterator()));
        self::assertEquals(['-f' => '123'], iterator_to_array($argumentsObject->getIterator(true)));
        $argumentsObject = ConsoleArgumentsFromString::exe('--first="a b c"');
        self::assertEquals(['a b c'], iterator_to_array($argumentsObject->getIterator()));
        self::assertEquals(['--first' => 'a b c'], iterator_to_array($argumentsObject->getIterator(true)));

        // Несколько "именованных команд"
        $argumentsObject = ConsoleArgumentsFromString::exe('-f=`FFFFFFF`     -a=11 --abc= -k');
        self::assertEquals(['FFFFFFF', '11', '', true], iterator_to_array($argumentsObject->getIterator()));
        self::assertEquals(['-f' => 'FFFFFFF', '-a' => '11', '--abc' => '', '-k' => true], iterator_to_array($argumentsObject->getIterator(true)));

        // * * * "Безымянные" и "Именованные" команды

        $argumentsObject = ConsoleArgumentsFromString::exe('a1  b2== -c3== -d4=44=44  ---x=`123   123`');
        self::assertEquals(['a1', 'b2==', '=', '44=44', '123   123'], iterator_to_array($argumentsObject->getIterator()));
        self::assertEquals(['-c3' => '=', '-d4' => '44=44', '---x' => '123   123'], iterator_to_array($argumentsObject->getIterator(true)));
    }

    /**
     * @covers ConsoleArgumentsFromString::exe()
     *
     * @return void
     */
    public function testEqualsPhpArgv(): void
    {
        $SERVER_ARGV = $_SERVER['argv'] ?? [];

        $_SERVER['argv'] = [
            'script.php',
            '*abc*=ddd',
            '-h',
            '--help',
            '-x=gg',
            'abc=def',
            '-z=zzz',
            '--null=',
            '=',
        ];

        // * * *

        $testArgvResult = ConsoleArgumentsFromPhpArgvCreator::exe();
        $testStringResult = ConsoleArgumentsFromString::exe((string)$testArgvResult);

        self::assertEquals((string)$testArgvResult, (string)$testStringResult);

        // * * *

        $_SERVER['argv'] = $SERVER_ARGV;
    }
}
