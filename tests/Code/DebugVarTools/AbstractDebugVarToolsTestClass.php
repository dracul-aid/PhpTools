<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Code\DebugVarTools;

use DraculAid\PhpTools\Code\DebugVarTools;
use PHPUnit\Framework\TestCase;

/**
 * Абстрактный класс для реализации тестов классов-генераторов отладочной информации
 */
abstract class AbstractDebugVarToolsTestClass extends TestCase
{
    /**
     * Вернет кейсы тестирования функций, возвращающих минимальную дебаг информацию по значениям переменных
     *
     * @return \Generator
     */
    protected function minDebugValueCases(): \Generator
    {
        yield ['NULL', null];
        yield ['FALSE', false];
        yield ['TRUE', true];

        yield ['float(NAN)', NAN];
        yield ['float(+INF)', INF];
        yield ['float(-INF)', -INF];

        yield ['""', ''];
        yield ['"abc"(3 chars)', 'abc'];
        $tmp = str_repeat('abcde', 15);
        yield ['"' . $tmp . '..."(87 chars)', "$tmp-blallalllla"];

        yield ['[]', []];
        yield ['list(1)', [1]];
        yield ['list(2)', [1, 'abc']];
        yield ['array(1)', ['x' => 1]];
        yield ['array(2)', ['y' => 1, 123]];

        yield [\stdClass::class, new \stdClass()];

        yield ['0', 0];
        yield ['float: 0', 0.0];
        yield ['1', 1];
        yield ['float: 1', 1.0];
        yield ['999', 999];
        yield ['1_000', 1_000];
        yield ['1_123_456', 1_123_456];
        yield ['float: 123.456', 123.456];

        yield ['callable strlen()', 'strlen'];
        yield [
            'callable ' . DebugVarTools::class . '::minDebugValue()',
            DebugVarTools::class . '::minDebugValue',
        ];
        yield [
            'callable-array-class ' . DebugVarTools::class . '::minDebugValue()',
            [DebugVarTools::class, 'minDebugValue'],
        ];
        yield [
            'callable-array-object ' . static::class . '::callableMethod()',
            [$this, 'callableMethod'],
        ];
        yield [
            'callable \Closure',
            static function (): void {},
        ];
        yield ['callable-function()', $this];
    }

    /**
     * Метод для проверки callable в виде массива с объектом
     *
     * @return void
     *
     * @todo эти методы нужно выкинуть из тест-класса, им тут не место, можно спокойно заменить на какой-то другой класс
     */
    public function callableMethod(): void {}

    /**
     * Позволит использовать объект теста как callable
     *
     * @return void
     *
     * @todo эти методы нужно выкинуть из тест-класса, им тут не место, можно спокойно заменить на какой-то другой класс
     */
    public function __invoke(): void {}
}
