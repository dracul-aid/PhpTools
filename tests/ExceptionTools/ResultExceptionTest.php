<?php

namespace DraculAid\PhpTools\tests\ExceptionTools;

use DraculAid\PhpTools\ExceptionTools\ResultException;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see ResultException}
 *
 * @run php tests/run.php tests/ExceptionTools/ResultExceptionTest.php
 */
class ResultExceptionTest extends TestCase
{
    /**
     * Test for {@see ResultException::$result}
     * Test for {@see ResultException::__invoke()}
     */
    public function testReadResult(): void
    {
        try
        {
            throw new ResultException([11, 22, 33]);
        }
        catch (ResultException $exception)
        {
            self::assertEquals([11, 22, 33], $exception->result);
            self::assertEquals([11, 22, 33], $exception());
        }
    }

    /**
     * Test for {@see ResultException::call()}
     */
    public function testCall(): void
    {
        self::assertEquals(
            'ABC',
            ResultException::call(function (){return 'ABC';})
        );

        self::assertEquals(
            'CBA',
            ResultException::call(function (){throw new ResultException('CBA');})
        );

        self::assertEquals(
            3,
            ResultException::call(function (int $a, int $b){return $a + $b;}, [1, 2], $callWithException)
        );
        self::assertFalse($callWithException);

        self::assertEquals(
            5,
            ResultException::call(function (int $a, int $b){throw new ResultException($a + $b);}, [3, 2], $callWithException)
        );
        self::assertTrue($callWithException);
    }
}
