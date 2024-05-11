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

use DraculAid\PhpTools\Code\FunctionAsPropertyObject;
use DraculAid\PhpTools\ExceptionTools\ExceptionTools;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@coversDefaultClass FunctionAsPropertyObject}
 *
 * @run php tests/run.php tests/Code/FunctionAsPropertyObjectTest.php
 */
class FunctionAsPropertyObjectTest extends TestCase
{
    /**
     * Test for {@covers FunctionAsPropertyObject::__construct()}
     * Test for {@covers FunctionAsPropertyObject::setFunction()}
     * Test for {@covers FunctionAsPropertyObject::call()}
     * Test for {@covers FunctionAsPropertyObject::callSafe()}
     *
     * @return void
     */
    public function testCreateAndSet(): void
    {
        // * * * Обычные функции

        // создание для обычной функции
        $functionObject = new FunctionAsPropertyObject('array_sum');
        self::assertEquals(5, $functionObject->call([2, 3]));
        self::assertEquals(5, $functionObject([2, 3]));

        // защищенный вызов, array_sum() поддерживает только один аргумент, вызов с 2 аргументами приведет к ошибке, ошибка будет проигнорирована
        $functionObject = new FunctionAsPropertyObject('array_sum');
        self::assertEquals(4, $functionObject->call([1, 3], 123123));
        $functionObject = new FunctionAsPropertyObject('array_sum', true);
        self::assertEquals(7, $functionObject->call([3, 4], 123123));

        // вызов без защищенного режима приведет к ошибке, так как array_sum() может принять только 1 аргумент
        // @todo PHP8 до 8-ой версии PHP нет смысла в этом тесте, так как будет выброшено "предупреждение", которое будет воспринято PhpUnit как ошибка прохождения теста
        if (PHP_MAJOR_VERSION > 7)
        {
            $functionObject = new FunctionAsPropertyObject('array_sum', false);
            ExceptionTools::wasCalledWithException(
                [$functionObject, 'call'],
                [[3, 4], 123123],
                \ArgumentCountError::class
            );
        }
        // принудительный вызов в защищенном режиме
        self::assertEquals(7, $functionObject->callSafe([3, 4], 123123));


        // * * * Языковые конструкции

        // создание и вызов для языковой конструкции
        $functionObject = new FunctionAsPropertyObject('array');
        self::assertEquals([2, 4, 6], $functionObject->call(2, 4, 6));

        // даже если выключен защищенный режим, он будет применен, так как array() не функция
        $functionObject = new FunctionAsPropertyObject('array', false);
        self::assertEquals([2, 4, 6], $functionObject->call(2, 4, 6));
    }

    /**
     * Test for {@covers FunctionAsPropertyObject::getOrCreate()}
     *
     * @return void
     */
    public function testGetOrCreate(): void
    {
        $functionObject = FunctionAsPropertyObject::getOrCreate('array_sum');
        self::assertTrue($functionObject instanceof FunctionAsPropertyObject);

        $newFunctionObject = FunctionAsPropertyObject::getOrCreate($functionObject);
        self::assertTrue($functionObject === $newFunctionObject);
    }
}
