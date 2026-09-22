<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\ExceptionTools;

use DraculAid\PhpTools\Classes\ClassNotPublicManager;
use DraculAid\PhpTools\ExceptionTools\ExceptionTools;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@coversDefaultClass ExceptionTools}
 *
 * @run php tests/run.php tests/ExceptionTools/ExceptionToolsTest.php
 */
class ExceptionToolsTest extends TestCase
{
    /**
     * Test for {@covers ExceptionTools::safeCallWithResult()}
     */
    public function testSafeCallWithResult(): void
    {
        $testFunctionSafeCallWithResult = ExceptionTools::safeCallWithResult(...);

        self::assertEquals(
            'ABC',
            $testFunctionSafeCallWithResult(function (): string {return 'ABC';})
        );

        self::assertEquals(
            3,
            $testFunctionSafeCallWithResult(function (int $a, int $b): int {return $a + $b;}, [1, 2])
        );

        self::assertEquals(
            null,
            $testFunctionSafeCallWithResult(function (): never {throw new \Exception();}, [])
        );

        self::assertEquals(
            100,
            $testFunctionSafeCallWithResult(function (): never {throw new \Exception();}, [], 100)
        );
    }

    /**
     * Test for {@covers ExceptionTools::safeCallWithCallable()}
     *
     * @psalm-suppress UndefinedVariable Пслам имеет проблемы с переменными-ссылками
     */
    public function testSafeCallWithCallable(): void
    {
        $testFunctionSafeCallWithCallable = ExceptionTools::safeCallWithCallable(...);

        $callableFunction = function () use (&$callableCall): int {$callableCall = true; return 777;};

        $callableCall = false;
        self::assertEquals(
            3,
            $testFunctionSafeCallWithCallable(function (int $a, int $b): int {return $a + $b;}, [1, 2], $callableFunction)
        );
        /** @psalm-suppress  RedundantCondition Псалм просто не понимает, что переменная передается в функцию по ссылке и может измениться */
        self::assertFalse($callableCall);

        $callableCall = false;
        self::assertEquals(
            777,
            $testFunctionSafeCallWithCallable(function (): never {throw new \Exception();}, [], $callableFunction)
        );
        self::assertTrue($callableCall);
    }

    /**
     * Test for {@covers ExceptionTools::safeCallFunctions()}
     *
     * @psalm-suppress UndefinedVariable Пслам имеет проблемы с переменными-ссылками
     */
    public function testSafeCallFunctions(): void
    {
        $testFunctionSafeCallFunctions = ExceptionTools::safeCallFunctions(...);

        $functionList = [
            function (): int {return 123;},
            function (): never {throw new \Exception();},
            function () use (&$callableCall): int {$callableCall = true; return 777;},
        ];

        // * * *

        $callableCall = false;
        $testFunctionSafeCallFunctions($functionList);
        self::assertTrue($callableCall);

        // * * *

        $functionGenerator = function () use ($functionList): \Generator {
            foreach ($functionList as $function) yield $function;
        };

        $callableCall = false;
        $testFunctionSafeCallFunctions($functionGenerator());
        self::assertTrue($callableCall);
    }

    /**
     * Test for {@covers ExceptionTools::callAndReturnException()}
     */
    public function testCallAndReturnException(): void
    {
        $testFunctionCallAndReturnException = ExceptionTools::callAndReturnException(...);

        $testException = new \Exception();

        self::assertNull(
            $testFunctionCallAndReturnException(function () {return 123;})
        );

        $resultException = $testFunctionCallAndReturnException(function () use ($testException) {throw $testException;});
        self::assertTrue($testException === $resultException);

        self::assertNull(
            $testFunctionCallAndReturnException(function (int $a, int $b) {return $a + $b;}, [1, 2], $result)
        );
        self::assertEquals(3, $result);
    }

    /**
     * Test for {@covers ExceptionTools::wasCalledWithException()}
     */
    public function testWasCalledWithException(): void
    {
        $testFunctionWasCalledWithException = ExceptionTools::wasCalledWithException(...);

        $testException = new \Exception();

        $result = null;
        self::assertFalse(
            $testFunctionWasCalledWithException(function () use (&$result) {$result = true;}, [], \Exception::class)
        );
        self::assertTrue($result);

        self::assertFalse(
            $testFunctionWasCalledWithException(
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
            $testFunctionWasCalledWithException(function () use ($testException) {throw $testException;}, [], \Exception::class)
        );

        self::assertTrue(
            $testFunctionWasCalledWithException(
                function () {throw new \Exception('AAA');}, [], \Exception::class, 'AAA'
            )
        );
        self::assertFalse(
            $testFunctionWasCalledWithException(
                function () {throw new \Exception('BBB');}, [], \Exception::class, 'AAA'
            )
        );

        self::assertTrue(
            $testFunctionWasCalledWithException(
                function () {throw new \Exception('AAA', 100);}, [], \Exception::class, null, 100
            )
        );
        self::assertFalse(
            $testFunctionWasCalledWithException(
                function () {throw new \Exception('AAA', 100);}, [], \Exception::class, null, 200
            )
        );
        self::assertFalse(
            $testFunctionWasCalledWithException(
                function () {throw new \Exception('AAA', 100);}, [], \Exception::class, 'BBB', 100
            )
        );

        self::assertFalse(
            $testFunctionWasCalledWithException(function () {return 123;}, [], \Exception::class, null, null, $result)
        );
        self::assertEquals(123, $result);
    }

    /**
     * Test for {@covers ExceptionTools::functionCall()}
     */
    public function testFunctionCall(): void
    {
        $testObject = $this->createTestObjectForTestFunctionCall();

        self::assertEquals(
            'public-123',
            ClassNotPublicManager::callMethod(
                [ExceptionTools::class, 'functionCall'],
                [
                    [$testObject, 'f_public'],
                    ['123'],
                ]
            )
        );
        self::assertEquals(
            'private-321',
            ClassNotPublicManager::callMethod(
                [ExceptionTools::class, 'functionCall'],
                [
                    [$testObject, 'f_private'],
                    ['321'],
                ]
            )
        );

        // * * *

        $callWithError = [
            'array, size 1' => [$testObject],
            'array, size 3' => [$testObject, 'f_private', 'value'],
        ];

        foreach ($callWithError as $testName => $notCallable)
        {
            if (ExceptionTools::callAndReturnException($notCallable) === null)
            {
                $this->fail("Fail test call not callable: {$testName}");
            }
        }
    }

    /**
     * Test for {@covers ExceptionTools::callAndResendException()}
     */
    public function testCallAndResendException(): void
    {
        $function = ExceptionTools::callAndResendException(...);

        // этот вызов не должен упасть
        $function(fn () => 1 + 1, [], \RuntimeException::class);

        // перехватываем ошибку
        try {
            $function(
                function () {
                    throw new \LogicException('AAA', 123);
                },
                [],
                \RuntimeException::class
            );
        } catch (\RuntimeException $err) {
            self::assertEquals('AAA', $err->getMessage());
            self::assertEquals(123, $err->getCode());
            self::assertEquals(get_class($err), \RuntimeException::class);
        }

        // переопределение текста ошибки
        try {
            $function(
                function () {
                    throw new \LogicException('AAA', 123);
                },
                [],
                \Error::class,
                'BBBB'
            );
        } catch (\Error $err) {
            self::assertEquals('BBBB. Original Message: AAA', $err->getMessage());
            self::assertEquals(123, $err->getCode());
            self::assertEquals(get_class($err), \Error::class);
        }
    }

    private function createTestObjectForTestFunctionCall(): object
    {
        return new class() {
            public function f_public(string $a): string {
                return "public-{$a}";
            }
            public function f_private(string $a): string {
                return "private-{$a}";
            }
        };
    }
}
