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
use DraculAid\PhpTools\Code\FunctionRunList\FunctionElement;
use DraculAid\PhpTools\Code\FunctionRunList\FunctionTransactionElements;
use DraculAid\PhpTools\tests\Code\FunctionRunList\resources\TestClassForTestFunctionRunList;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see AbstractFunctionRunList}, функционал по выполнению списка функций см в {@see AbstractFunctionRunExecuteTest}
 *
 * @run php tests/run.php tests/Code/FunctionRunList/AbstractFunctionRunListTest.php
 */
class AbstractFunctionRunListTest extends TestCase
{
    /**
     * @covers AbstractFunctionRunList::addFunction()
     * @covers \DraculAid\PhpTools\Code\FunctionRunList\FunctionElement
     *
     * @return void
     */
    public function testAddFunction(): void
    {
        /** @var callable[] $function_list Возможные примеры функций */
        $function_list = [
            0 => 'in_array',
            1 => TestClassForTestFunctionRunList::class . '::staticFunction',
            2 => [TestClassForTestFunctionRunList::class,'staticFunction'],
            3 => TestClassForTestFunctionRunList::staticFunction(...),
            4 => [new TestClassForTestFunctionRunList, 'staticFunction'],
            5 => (new TestClassForTestFunctionRunList)->staticFunction(...),
            6 => fn() => 33,
            7 => new class() {
                public function __invoke() {return 22;}
            },
        ];

        $testObject = $this->getFunctionRunListObject();

        // Установка функций
        foreach ($function_list as $function) $testObject->addFunction($function);

        // Установка функций "отката"
        foreach ($function_list as $function) $testObject->addFunction('in_array', $function);

        self::assertEquals(16, count($testObject->for_test___get_elements()->functionList));
    }

    /**
     * @covers AbstractFunctionRunList::run()
     * @covers AbstractFunctionRunList::afterRun()
     * @covers AbstractFunctionRunList::beforeRun()
     *
     * @return void
     */
    public function testBeforeAndAfterActionInRun(): void
    {
        // * * * Когда нет списка функций

        $testObject = $this->getFunctionRunListObject();

        // beforeRun - пропускает
        $testObject->beforeRun_return = true;
        $testObject->run();
        self::assertEquals(1, $testObject->beforeRun_counter);
        self::assertEquals(1, $testObject->afterRun_counter);

        // beforeRun - останавливает
        $testObject->beforeRun_return = false;
        $testObject->run();
        self::assertEquals(2, $testObject->beforeRun_counter);
        self::assertEquals(1, $testObject->afterRun_counter);

        // * * * Когда есть список функций (убеждаемся, что вызывается 1 раз)

        $testObject = $this->getFunctionRunListObject();

        $testObject->addFunction('time');
        $testObject->addFunction(fn() => '');

        // beforeRun - пропускает
        $testObject->beforeRun_return = true;
        self::assertEquals(0, $testObject->run());
        self::assertEquals(1, $testObject->beforeRun_counter);
        self::assertEquals(1, $testObject->afterRun_counter);

        // beforeRun - останавливает
        $testObject->beforeRun_return = false;
        self::assertEquals(0, $testObject->run());
        self::assertEquals(2, $testObject->beforeRun_counter);
        self::assertEquals(1, $testObject->afterRun_counter);

        // * * * afterRun будет вызвана, даже если какая-то функция в списке функций упала

        $testObject = $this->getFunctionRunListObject();
        $testObject->beforeRun_return = true;
        $testObject->addFunction(fn() => throw new \RuntimeException('Если видно ошибку, то провал теста'));
        self::assertEquals(1, $testObject->run());
        self::assertEquals(1, $testObject->beforeRun_counter);
        self::assertEquals(1, $testObject->afterRun_counter);
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
