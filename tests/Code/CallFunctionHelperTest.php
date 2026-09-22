<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Code;

use DraculAid\PhpTools\Code\CallFunctionHelper;
use DraculAid\PhpTools\ExceptionTools\ExceptionTools;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@coversDefaultClass CallFunctionHelper}
 *
 * @run php tests/run.php tests/Code/CallFunctionHelperTest.php
 */
class CallFunctionHelperTest extends TestCase
{
    /**
     * Test for {@covers CallFunctionHelper::STRUCTURES}
     * Test for {@covers CallFunctionHelper::isStructures()}
     * Test for {@covers CallFunctionHelper::isCallable()}
     *
     * @return void
     */
    public function testIsStructures(): void
    {
        $testFunctionIsStructures = CallFunctionHelper::isStructures(...);
        $testFunctionIsCallable = CallFunctionHelper::isCallable(...);

        foreach (CallFunctionHelper::STRUCTURES as $name)
        {
            self::assertTrue($testFunctionIsStructures($name), "isStructures: Error for {$name}");
            self::assertTrue($testFunctionIsCallable($name), "isCallable: Error for {$name}");
        }

        self::assertFalse($testFunctionIsStructures('is_int'));
        self::assertTrue($testFunctionIsCallable('is_int'));
        self::assertFalse($testFunctionIsStructures(CallFunctionHelper::class . '::isStructures'));
        self::assertTrue($testFunctionIsCallable(CallFunctionHelper::class . '::isStructures'));

        self::assertTrue($testFunctionIsCallable([CallFunctionHelper::class, 'isStructures']));
        self::assertTrue($testFunctionIsCallable([$this, 'testIsStructures']));
    }

    /**
     * Test for {@covers CallFunctionHelper::exe()}
     * Test for {@covers CallFunctionHelper::exeCallable()}
     * Test for {@covers CallFunctionHelper::isClassCallable()} in {@covers CallFunctionHelper::exeCallable()}
     * Test for {@covers CallFunctionHelper::getReflectionForCallable()} in {@covers CallFunctionHelper::exeCallable()}
     *
     * @return void
     *
     * @psalm-suppress RedundantConditionGivenDocblockType да, мы в курсе, что ожидаемый тип и возвращается, но это тест
     */
    public function testExe(): void
    {
        $testFunction = CallFunctionHelper::exe(...);

        // создание объекта
        $t = $testFunction('new ' . \stdClass::class);
        self::assertTrue($t instanceof \stdClass);
        /** @var \ArrayObject $t */
        $t = $testFunction('new ' . \ArrayObject::class, [0, 1, 2, 3]);
        self::assertTrue($t instanceof \ArrayObject);
        self::assertEquals([0, 1, 2, 3], $t->getArrayCopy());

        // isset()
        $t = null;
        self::assertFalse($testFunction('isset', $t));
        $t = false;
        self::assertTrue($testFunction('isset', $t));
        $t = false; $a = null;
        self::assertTrue($testFunction('isset', $t, $a));

        // empty()
        $t = null; $a = null;
        self::assertTrue($testFunction('empty', $t, $a));
        $t = false;
        self::assertTrue($testFunction('empty', $t, $a));
        $t = '123';
        self::assertFalse($testFunction('empty', $t, $a));

        // echo()
        ob_start();
        self::assertNull($testFunction('echo', ''));
        self::assertEquals('', ob_get_contents());
        ob_clean();
        self::assertNull($testFunction('echo', 'ABC'));
        self::assertEquals('ABC', ob_get_contents());
        ob_clean();
        self::assertNull($testFunction('echo', 'ABC', 278));
        self::assertEquals('ABC278', ob_get_contents());
        ob_end_clean();

        // print()
        ob_start();
        self::assertEquals(1, $testFunction('print', ''));
        self::assertEquals('', ob_get_contents());
        ob_clean();
        self::assertEquals(1, $testFunction('print', 'ABC'));
        self::assertEquals('ABC', ob_get_contents());
        ob_clean();
        self::assertEquals(1, $testFunction('print', 'ЯФЖ'));
        self::assertEquals('ЯФЖ', ob_get_contents());
        ob_end_clean();


        // * * * Вызов функций
        $t = 'XXX';
        self::assertTrue((bool)$testFunction('time', $t));
        $t = 'XXX';
        self::assertFalse($testFunction('is_int', $t));
        $t = 0;
        self::assertTrue($testFunction('is_int', $t));
        $t = 1; $a = 'XYZ';
        self::assertTrue($testFunction('is_int', $t, $a));

        // вызов методов
        $testObject = $this->getTestObject();
        self::assertEquals(3, $testFunction([$testObject, 'f2'], 5, 2));
        self::assertEquals(4, $testFunction($testObject->f2(...), 6, 2));
        self::assertEquals(9, $testFunction($testObject::f1(...), 8, 1));
    }

    /**
     * Test for {@covers CallFunctionHelper::callMethodFromEmptyObject()}
     *
     * @return void
     */
    public function testCallMethodFromEmptyObject(): void
    {
        $testFunction = CallFunctionHelper::callMethodFromEmptyObject(...);

        $testClass = get_class(new class ('default-protected') {
            public string $varPublic = 'default-public';
            protected string $varProtected;
            private string $varPrivate;
            public function __construct(string $var)
            {
                $this->varProtected = $var;
                $this->varPrivate = 'default-private';
            }
            public function getVarPublic(string $a = 'AA'): string
            {
                return "{$this->varPublic}-{$a}";
            }
            public function getVarProtected(string $a = 'AA'): string
            {
                return "{$this->varProtected}-{$a}";
            }
            public function getVarPrivate(): string
            {
                return "{$this->varPrivate}";
            }
        });

        self::assertEquals(
            'default-public-AA',
            $testFunction([$testClass, 'getVarPublic'])
        );
        self::assertEquals(
            'default-public-BB',
            $testFunction([$testClass, 'getVarPublic'], ['BB'])
        );
        self::assertEquals(
            'XYZ-ZZ',
            $testFunction([$testClass, 'getVarProtected'], ['ZZ'], ['varProtected' => 'XYZ'])
        );
        self::assertEquals(
            'ABC',
            $testFunction([$testClass, 'getVarPrivate'], [], ['varPrivate' => 'ABC'])
        );

        self::assertTrue(
            ExceptionTools::wasCalledWithException(
                $testFunction,
                [[1, 'method']],
                \TypeError::class
            )
        );
        self::assertTrue(
            ExceptionTools::wasCalledWithException(
                $testFunction,
                [['class', 1]],
                \TypeError::class
            )
        );
    }

    private function getTestObject(): object
    {
        return new class() {
            public static function f1(int $a, int $b): int
            {
                return $a + $b;
            }
            public function f2(int $a, int $b): int
            {
                return $a - $b;
            }
        };
    }
}
