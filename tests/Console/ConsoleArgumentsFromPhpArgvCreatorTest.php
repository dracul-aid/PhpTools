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

use DraculAid\PhpTools\Classes\ClassNotPublicManager;
use DraculAid\PhpTools\Console\ConsoleArgumentsFromPhpArgvCreator;
use DraculAid\PhpTools\Console\ConsoleArgumentsObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@coversDefaultClass ConsoleArgumentsFromPhpArgvCreator}
 *
 * @run php tests/run.php tests/Console/ConsoleArgumentsFromPhpArgvCreatorTest.php
 */
class ConsoleArgumentsFromPhpArgvCreatorTest extends TestCase
{
    public function testRun(): void
    {
        $this->testParseParamRowValue();
        $this->testExe();
    }

    /**
     * Test for {@covers ConsoleArgumentsFromPhpArgvCreator::parseParamRowValue()}
     *
     * @return void
     */
    private function testParseParamRowValue(): void
    {
        // * * * "анализ по первому символ" - значение без имени для цифр

        $testResult = $this->callParseParamRowValue('123');

        self::assertEquals(1, $testResult->count());
        self::assertEquals(0, $testResult->countNames());
        self::assertEquals('123', $testResult[0]);

        // * * * "анализ по первому символ" - значение без имени для знака препинания

        $testResult = $this->callParseParamRowValue('!ggg');

        self::assertEquals(1, $testResult->count());
        self::assertEquals(0, $testResult->countNames());
        self::assertEquals('!ggg', $testResult[0]);

        // * * * флаг без значения

        $testResult = $this->callParseParamRowValue('-f');

        self::assertEquals(1, $testResult->count());
        self::assertEquals(1, $testResult->countNames());
        self::assertEquals(true, $testResult->getByName('-f'));
        self::assertEquals(true, $testResult->getByPosition(0));
        self::assertEquals(0, $testResult->getPositionByName('-f'));
        self::assertEquals('-f', $testResult->getNameByPosition(0));

        // * * * флаг с значением

        $testResult = $this->callParseParamRowValue('-f=123');

        self::assertEquals(1, $testResult->count());
        self::assertEquals(1, $testResult->countNames());
        self::assertEquals('123', $testResult->getByName('-f'));
        self::assertEquals('123', $testResult->getByPosition(0));
        self::assertEquals(0, $testResult->getPositionByName('-f'));
        self::assertEquals('-f', $testResult->getNameByPosition(0));

        // * * * именованный аргумент со значением

        $testResult = $this->callParseParamRowValue('abc12=123');

        self::assertEquals(1, $testResult->count());
        self::assertEquals(1, $testResult->countNames());
        self::assertEquals('123', $testResult->getByName('abc12'));
        self::assertEquals('123', $testResult->getByPosition(0));
        self::assertEquals(0, $testResult->getPositionByName('abc12'));
        self::assertEquals('abc12', $testResult->getNameByPosition(0));
    }

    /**
     * Test for {@covers ConsoleArgumentsFromPhpArgvCreator::exe()}
     *
     * @return void
     */
    private function testExe(): void
    {
        $SERVER_ARGV = $_SERVER['argv'] ?? [];

        $_SERVER['argv'] = [
            'script.php',
            '*abc*=ddd',
            '-h',
            '--help',
            '-x=ggg',
            'abc=def',
            '-x=zzz',
        ];

        $testResult = ConsoleArgumentsFromPhpArgvCreator::exe();

        self::assertEquals('script.php', $testResult->script);

        self::assertEquals(6, $testResult->count());
        self::assertEquals(4, $testResult->countNames());

        self::assertEquals(['*abc*=ddd', true, true, 'ggg', 'def', 'zzz'], iterator_to_array($testResult->getIterator()));
        self::assertEquals(['*abc*=ddd', true, true, 'ggg', 'def', 'zzz'], iterator_to_array($testResult->getIterator(false)));

        self::assertEquals(['-h' => true, '--help' => true, 'abc' => 'def', '-x' => 'zzz'], iterator_to_array($testResult->getIterator(true)));

        $_SERVER['argv'] = $SERVER_ARGV;
    }

    /**
     * Вызовет {@see ConsoleArgumentsFromPhpArgvCreator::parseParamRowValue()} для проведения теста
     *
     * @param   string   $testArgumentValue   Строка с значением аргумента
     *
     * @return ConsoleArgumentsObject  Вернет объект "список аргументов консольной команды"
     */
    private function callParseParamRowValue(string $testArgumentValue): ConsoleArgumentsObject
    {
        $consoleArgument = new ConsoleArgumentsObject();

        ClassNotPublicManager::callMethod(
            [ConsoleArgumentsFromPhpArgvCreator::class, 'parseParamRowValue'],
            [0, $testArgumentValue, $consoleArgument]
        );

        return $consoleArgument;
    }
}
