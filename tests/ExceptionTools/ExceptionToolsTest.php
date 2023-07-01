<?php

namespace DraculAid\PhpTools\tests\ExceptionTools;

use DraculAid\PhpTools\ExceptionTools\ExceptionTools;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see ExceptionTools}
 *
 * @run php tests/run.php tests/ExceptionTools/ExceptionToolsTest.php
 */
class ExceptionToolsTest extends TestCase
{
    /**
     * Test for {@see ExceptionTools::safeCallWithResult()}
     */
    public function testSafeCallWithResult(): void
    {
        self::assertEquals(
            'ABC',
            ExceptionTools::safeCallWithResult(function () {return 'ABC';})
        );

        self::assertEquals(
            3,
            ExceptionTools::safeCallWithResult(function (int $a, int $b){return $a + $b;}, [1, 2])
        );

        self::assertEquals(
            null,
            ExceptionTools::safeCallWithResult(function (){throw new \Exception();}, [])
        );

        self::assertEquals(
            100,
            ExceptionTools::safeCallWithResult(function (){throw new \Exception();}, [], 100)
        );
    }

    /**
     * Test for {@see ExceptionTools::safeCallWithCallable()}
     */
    public function testSafeCallWithCallable(): void
    {
        $callableFunction = function () use (&$callableCall) {$callableCall = true; return 777;};

        $callableCall = false;
        self::assertEquals(
            3,
            ExceptionTools::safeCallWithCallable(function (int $a, int $b){return $a + $b;}, [1, 2], $callableFunction)
        );
        self::assertFalse($callableCall);

        $callableCall = false;
        self::assertEquals(
            777,
            ExceptionTools::safeCallWithCallable(function (){throw new \Exception();}, [], $callableFunction)
        );
        self::assertTrue($callableCall);
    }

    /**
     * Test for {@see ExceptionTools::safeCallFunctions()}
     */
    public function testSafeCallFunctions(): void
    {
        $functionList = [
            function () {return 123;},
            function () {throw new \Exception();},
            function () use (&$callableCall) {$callableCall = true; return 777;},
        ];

        // * * *

        $callableCall = false;
        ExceptionTools::safeCallFunctions($functionList);
        self::assertTrue($callableCall);

        // * * *

        $functionGenerator = function () use ($functionList): \Generator {
            foreach ($functionList as $function) yield $function;
        };

        $callableCall = false;
        ExceptionTools::safeCallFunctions($functionGenerator());
        self::assertTrue($callableCall);
    }

    /**
     * Test for {@see ExceptionTools::callAndReturnException()}
     */
    public function testCallAndReturnException(): void
    {
        $testException = new \Exception();

        self::assertNull(
            ExceptionTools::callAndReturnException(function () {return 123;})
        );

        $resultException = ExceptionTools::callAndReturnException(function () use ($testException) {throw $testException;});
        self::assertTrue($testException === $resultException);

        self::assertNull(
            ExceptionTools::callAndReturnException(function (int $a, int $b) {return $a + $b;}, [1, 2], $result)
        );
        self::assertEquals(3, $result);
    }

    /**
     * Test for {@see ExceptionTools::wasCalledWithException()}
     */
    public function testWasCalledWithException(): void
    {
        $testException = new \Exception();

        $result = null;
        self::assertFalse(
            ExceptionTools::wasCalledWithException(function () use (&$result) {$result = true;}, [], \Exception::class)
        );
        self::assertTrue($result);

        self::assertFalse(
            ExceptionTools::wasCalledWithException(
                function (int $a, int $b) use (&$result) {$result = $a + $b;},
                [1, 2],
                \Exception::class
            )
        );
        self::assertEquals(3, $result);

        self::assertTrue(
            $testException
            ===
            ExceptionTools::callAndReturnException(
                [ExceptionTools::class, 'wasCalledWithException'],
                [function () use ($testException) {throw $testException;}, [], \stdClass::class]
            )
        );

        self::assertTrue(
            ExceptionTools::wasCalledWithException(function () use ($testException) {throw $testException;}, [], \Exception::class)
        );

        self::assertTrue(
            ExceptionTools::wasCalledWithException(
                function () {throw new \Exception('AAA');}, [], \Exception::class, 'AAA'
            )
        );
        self::assertFalse(
            ExceptionTools::wasCalledWithException(
                function () {throw new \Exception('BBB');}, [], \Exception::class, 'AAA'
            )
        );

        self::assertTrue(
            ExceptionTools::wasCalledWithException(
                function () {throw new \Exception('AAA', 100);}, [], \Exception::class, null, 100
            )
        );
        self::assertFalse(
            ExceptionTools::wasCalledWithException(
                function () {throw new \Exception('AAA', 100);}, [], \Exception::class, null, 200
            )
        );
        self::assertFalse(
            ExceptionTools::wasCalledWithException(
                function () {throw new \Exception('AAA', 100);}, [], \Exception::class, 'BBB', 100
            )
        );

        self::assertFalse(
            ExceptionTools::wasCalledWithException(function () {return 123;}, [], \Exception::class, null, null, $result)
        );
        self::assertEquals(123, $result);
    }
}
