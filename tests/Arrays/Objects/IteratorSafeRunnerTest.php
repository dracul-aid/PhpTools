<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Arrays\Objects;

use DraculAid\PhpTools\Arrays\Objects\IteratorSafeRunner;
use DraculAid\PhpTools\Classes\ClassNotPublicManager;
use DraculAid\PhpTools\tests\Arrays\Objects\_resources\IteratorSafeRunnerTestClass;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@coversDefaultClass IteratorSafeRunner}
 *
 * @run php tests/run.php tests/Arrays/Objects/IteratorSafeRunnerTest.php
 *
 * @see IteratorSafeRunnerTestClass Тестовый итератор, для проведения тестов
 */
class IteratorSafeRunnerTest extends TestCase
{
    /**
     * Test for {@covers IteratorSafeRunner::runNoSafe()}
     *
     * @return void
     */
    public function testRunNoSafe(): void
    {
        $testArray = [0, 1, 2, 3, 4, 5];

        $testIterator = new IteratorSafeRunnerTestClass();
        $testIterator->array = $testArray;
        $testIterator->cursor = 3;

        $testRunner = new IteratorSafeRunner($testIterator, $testIterator->cursor);

        self::assertEquals(
            $testArray,
            iterator_to_array(ClassNotPublicManager::callMethod([$testRunner, 'runNoSafe']))
        );

        self::assertEquals(count($testArray), $testIterator->cursor);
    }

    /**
     * Test for {@covers IteratorSafeRunner::getIterator()}
     *
     * @return void
     */
    public function testGetIterator(): void
    {
        $testArray = [0, 1, 2, 3, 4, 5];

        $testIterator = new IteratorSafeRunnerTestClass();
        $testIterator->array = $testArray;
        $testIterator->cursor = 3;

        $testRunner = new IteratorSafeRunner($testIterator, $testIterator->cursor);

        self::assertEquals(
            $testArray,
            iterator_to_array($testRunner)
        );

        self::assertEquals(3, $testIterator->cursor);
    }

    /**
     * Test for {@covers IteratorSafeRunner::runSafe()}
     *
     * @return void
     */
    public function testRunSafe(): void
    {
        $testArray = [0, 1, 2, 3, 4, 5];

        $testIterator = new IteratorSafeRunnerTestClass();
        $testIterator->array = $testArray;
        $testIterator->cursor = 3;

        $testRunner = new IteratorSafeRunner($testIterator, $testIterator->cursor);

        self::assertEquals(
            $testArray,
            iterator_to_array(ClassNotPublicManager::callMethod([$testRunner, 'runSafe']))
        );

        self::assertEquals(count($testArray), $testIterator->cursor);
    }
}
