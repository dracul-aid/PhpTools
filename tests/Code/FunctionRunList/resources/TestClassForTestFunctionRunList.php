<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Code\FunctionRunList\resources;

/**
 * Класс, с функциями для тестирования в {@see AbstractFunctionRunListTest}
 */
class TestClassForTestFunctionRunList
{
    static int $staticFunctionCallCounter = 0;
    static array $staticFunctionCallCounterWithArgument = [];

    public static function staticFunction(): string
    {
        self::$staticFunctionCallCounter++;
        return 'STATIC OK';
    }

    public function function(): string
    {
        return 'OK';
    }

    public static function staticFunctionWithArgument(mixed $argumentValue): void
    {
        self::$staticFunctionCallCounter++;
        self::$staticFunctionCallCounterWithArgument[] = $argumentValue;
    }
}
