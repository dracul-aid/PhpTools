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

use DraculAid\PhpTools\Code\ObHelper;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see ObHelper}
 *
 * @run php tests/run.php tests/Code/ObHelperTest.php
 */
class ObHelperTest extends TestCase
{
    /**
     * Test for {@see ObHelper::callFunction()}
     *
     * @return void
     */
    public function testCallFunction(): void
    {
        $this->standardFunctionTests([ObHelper::class, 'callFunction'], 'ObHelper::callFunction');
    }

    /**
     * Test for {@see ObHelper::callWithFunctionHelper()}
     *
     * @return void
     */
    public function testWithFunctionHelper(): void
    {
        $this->standardFunctionTests([ObHelper::class, 'callWithFunctionHelper'], 'ObHelper::callWithFunctionHelper');

        self::assertEquals(
            '==098==',
            ObHelper::callWithFunctionHelper(
                function () {echo "==098==";},
                ['aaa', 'bbb']
            )
        );

        self::assertEquals(
            '==555==',
            ObHelper::callWithFunctionHelper(
                'echo',
                ['==555==']
            )
        );
    }

    /**
     * Test for {@see ObHelper::callNotPublicMethod()}
     *
     * @return void
     */
    public function testCallNotPublicMethod(): void
    {
        $object = new class() {
            protected function notPublicPrint(): void
            {
                echo '===111===';
            }
            protected function notPublicPrintAndReturn(string $a = 'abc'): string
            {
                echo "==={$a}===";
                return '===333===';
            }
        };

        // * * *

        self::assertEquals(
            '===111===',
            ObHelper::callNotPublicMethod(
                [$object, 'notPublicPrint'],
            ),
        );

        $testReturn = null;
        self::assertEquals(
            '===xyz===',
            ObHelper::callNotPublicMethod(
                [$object, 'notPublicPrintAndReturn'],
                ['xyz'],
                $testReturn
            ),
        );
        self::assertEquals('===333===', $testReturn);
    }

    /**
     * Test for {@see ObHelper::callMethodFromEmptyObject()}
     *
     * @return void
     */
    public function testCallMethodFromEmptyObject(): void
    {
        $object = new class() {
            protected string $str = '===';
            public function __construct() {
                $this->str = '---';
            }
            protected function notPublicPrint(): void
            {
                echo "{$this->str}111{$this->str}";
            }
            protected function notPublicPrintAndReturn(string $a = 'abc'): string
            {
                echo "{$this->str}{$a}{$this->str}";
                return "{$this->str}333{$this->str}";
            }
        };
        $testClass = get_class($object);

        // * * *

        self::assertEquals(
            '===111===',
            ObHelper::callMethodFromEmptyObject(
                [$testClass, 'notPublicPrint'],
            ),
        );

        $testReturn = null;
        self::assertEquals(
            '===xyz===',
            ObHelper::callMethodFromEmptyObject(
                [$testClass, 'notPublicPrintAndReturn'],
                ['xyz'],
                [],
                $testReturn
            ),
        );
        self::assertEquals('===333===', $testReturn);
    }

    /**
     * Стандартные тесты с анонимными функциями
     *
     * @param   string|callable   $testFunction    Функция или языковая конструкция
     * @param   string            $label           Подпись, что за элемент тестируется
     *
     * @return void
     *
     * @todo PHP8 Типизация аргументов функции
     *
     * @psalm-suppress UnusedVariable Псалм не может узнать, что $testFunction принимает переменные по ссылки, и считает, что переменные никогда не меняют значения
     */
    private function standardFunctionTests($testFunction, string $label): void
    {
        self::assertEquals(
            '==123==',
            $testFunction(
                function (): void {echo '==123==';},
            ),
            "Error for {$label}"
        );

        self::assertEquals(
            '==123==',
            $testFunction(
                function (string $a = '123'): void {echo "=={$a}==";},
            ),
            "Error for {$label}"
        );

        self::assertEquals(
            '==aaabbbccc==',
            $testFunction(
                function (string $a, string $b, string $c = 'ccc'): void {echo "=={$a}{$b}{$c}==";},
                ['aaa', 'bbb']
            ),
            "Error for {$label}"
        );

        $testResult = null;
        self::assertEquals(
            '==123==',
            $testFunction(
                function (): string {echo '==123=='; return 'XXX';},
                [],
                $testResult
            ),
            "Error for {$label}"
        );
        self::assertEquals('XXX', $testResult,  "Error for {$label}");
    }
}
