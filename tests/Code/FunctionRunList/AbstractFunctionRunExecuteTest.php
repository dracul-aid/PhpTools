<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Code\FunctionRunList;

use DraculAid\PhpTools\Code\FunctionRunList\AbstractFunctionRunList;
use DraculAid\PhpTools\Code\FunctionRunList\FunctionTransactionElements;
use DraculAid\PhpTools\tests\Code\FunctionRunList\resources\TestClassForTestFunctionRunList;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see AbstractFunctionRunList} и {@see FunctionElement}
 *
 * @run php tests/run.php tests/Code/FunctionRunList/AbstractFunctionRunExecuteTest.php
 */
class AbstractFunctionRunExecuteTest extends TestCase
{
    /**
     * @covers AbstractFunctionRunList::run()
     * @covers FunctionElement::run()
     *
     * @return void
     */
    public function testRun(): void
    {
        // * * * Все функции успешно выполнены (функции переданы без аргументов)

        $testObject = $this->getFunctionRunListObject();
        TestClassForTestFunctionRunList::$staticFunctionCallCounter = 0;

        // успешная функция
        $testObject->addFunction(TestClassForTestFunctionRunList::staticFunction(...));
        // успешная функция, с роллбеком
        $testObject->addFunction(TestClassForTestFunctionRunList::staticFunction(...), fn() => throw new \RuntimeException('Провал выполнения теста'));
        // успешная функция
        $testObject->addFunction(TestClassForTestFunctionRunList::staticFunction(...), null);

        self::assertEquals(0, $testObject->run());
        self::assertEquals(3, TestClassForTestFunctionRunList::$staticFunctionCallCounter);

        // * * * Все функции успешно выполнены и получили верные аргументы

        $testObject = $this->getFunctionRunListObject();
        TestClassForTestFunctionRunList::$staticFunctionCallCounter = 0;
        TestClassForTestFunctionRunList::$staticFunctionCallCounterWithArgument = [];

        // успешная функция
        $testObject->addFunction(TestClassForTestFunctionRunList::staticFunctionWithArgument(...));
        // провальная функция, с роллбеком
        $testObject->addFunction(fn(bool $arg) => TestClassForTestFunctionRunList::staticFunctionWithArgument($arg) || throw new \RuntimeException('Провал выполнения теста #1'));
        // успешная функция
        $testObject->addFunction(TestClassForTestFunctionRunList::staticFunctionWithArgument(...));
        // успешная функция
        $testObject->addFunction(TestClassForTestFunctionRunList::staticFunctionWithArgument(...));
        // провальная функция, с роллбеком
        $testObject->addFunction(fn(bool $arg) => TestClassForTestFunctionRunList::staticFunctionWithArgument($arg) || throw new \RuntimeException('Провал выполнения теста #2'));
        // успешная функция
        $testObject->addFunction(TestClassForTestFunctionRunList::staticFunctionWithArgument(...));

        self::assertEquals(2, $testObject->run());
        self::assertEquals(6, TestClassForTestFunctionRunList::$staticFunctionCallCounter);
        self::assertEquals([true, true, false, false, false, false], TestClassForTestFunctionRunList::$staticFunctionCallCounterWithArgument);

        // * * * Проверяем отслеживание ошибок

        $testObject = $this->getFunctionRunListObject();
        TestClassForTestFunctionRunList::$staticFunctionCallCounter = 0;

        $testObject->addFunction('time');
        $testObject->addFunction(fn() => throw new \RuntimeException('Провал выполнения теста'));
        $testObject->addFunction('time');
        $testObject->addFunction(fn() => throw new \LogicException('Провал выполнения теста'), fn() => throw new \TypeError('Провал выполнения теста'));
        $testObject->addFunction('time');

        self::assertEquals(3, $testObject->run());
        self::assertEquals(2, count($testObject->getFailList()));
        self::assertEquals(\RuntimeException::class, get_class($testObject->getFailList()[1]));
        self::assertEquals(\LogicException::class, get_class($testObject->getFailList()[3]));
        self::assertEquals(1, count($testObject->getFailRollbackList()));
        self::assertEquals(\TypeError::class, get_class($testObject->getFailRollbackList()[3]));
    }

    private function getFunctionRunListObject(): AbstractFunctionRunList
    {
        return new class() extends AbstractFunctionRunList {

            public bool $beforeRun_return = true;
            public int $beforeRun_counter = 0;
            public int $afterRun_counter = 0;

            /**
             * Вернет - Внутренние компоненты "Объекта-Транзакции"
             *
             * (!) Функция создана для проверки работы класса
             *
             * @return FunctionTransactionElements
             */
            public function for_test___get_elements(): FunctionTransactionElements
            {
                return $this->elements;
            }

            /** @inheritdoc */
            protected function beforeRun(): bool
            {
                $this->beforeRun_counter++;

                return $this->beforeRun_return;
            }

            /** @inheritdoc */
            protected function afterRun(): void
            {
                $this->afterRun_counter++;
            }
        };
    }
}
