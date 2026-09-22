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

use DraculAid\PhpTools\Code\TypeValidator;
use DraculAid\PhpTools\ExceptionTools\ExceptionTools;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@coversDefaultClass TypeValidator}
 *
 * @run php tests/run.php tests/Code/TypeValidatorTest.php
 *
 * @psalm-suppress UndefinedClass В тесте используются несуществующие классы, это нормально
 */
class TypeValidatorTest extends TestCase
{
    /**
     * Test for {@covers TypeValidator::validateOr()}
     */
    public function testValidateOr(): void
    {
        $testFunction = TypeValidator::validateOr(...);

        // Проверки для NULL
        self::assertTrue($testFunction(null, ['null'], false));
        self::assertFalse($testFunction(null, ['bool'], false));

        // Проверки для BOOLEAN
        self::assertTrue($testFunction(false, ['bool'], false));
        self::assertTrue($testFunction(true, ['bool'], false));
        self::assertFalse($testFunction(false, ['int'], false));
        self::assertFalse($testFunction(true, ['int'], false));

        // Проверки для псевдотипов BOOLEAN
        self::assertTrue($testFunction(false, ['false'], false));
        self::assertTrue($testFunction(true, ['true'], false));
        self::assertFalse($testFunction(false, ['true'], false));
        self::assertFalse($testFunction(true, ['false'], false));

        // Проверки INT
        self::assertTrue($testFunction(123, ['int'], false));
        self::assertFalse($testFunction(123, ['float'], false));

        // Проверки FLOAT
        self::assertTrue($testFunction(123.0, ['float'], false));
        self::assertTrue($testFunction(123.123, ['float'], false));
        self::assertFalse($testFunction(123, ['float'], false));

        // Названия типов "похожие" на типы PHP, но таковыми не являющиеся
        self::assertFalse($testFunction(123, ['integer'], false));
        self::assertFalse($testFunction(123, ['number'], false));
        self::assertFalse($testFunction(123.123, ['double'], false));

        // Строки STRING
        self::assertTrue($testFunction('', ['string'], false));
        self::assertTrue($testFunction('123', ['string'], false));

        // Проверка ARRAY
        self::assertTrue($testFunction([], ['array'], false));
        self::assertTrue($testFunction([1, 2, 3], ['array'], false));
        self::assertFalse($testFunction(new \stdClass(), ['array'], false));

        // Базовая проверка OBJECT
        self::assertTrue($testFunction(new \stdClass(), ['object'], false));
        self::assertFalse($testFunction(['a' => 1, 'b' => 2], ['object'], false));

        // Строгое соответствие имени класса
        self::assertTrue($testFunction(new \stdClass(), [\stdClass::class], false));
        self::assertTrue($testFunction($this, [TypeValidatorTest::class], false));
        self::assertFalse($testFunction(new \stdClass(), [TypeValidatorTest::class], false));
        self::assertFalse($testFunction($this, [\stdClass::class], false));

        // Проверки для "перечисляемого"
        self::assertTrue($testFunction([], ['iterable'], false));
        self::assertTrue($testFunction(['123'], ['iterable'], false));
        self::assertTrue($testFunction((function (): \Generator {yield 123;})(), ['iterable'], false));
        self::assertFalse($testFunction(new \stdClass(), ['iterable'], false));

        // Проверки для "вызываемого"
        self::assertTrue($testFunction('in_array', ['callable'], false));
        self::assertTrue($testFunction([new \DateTime(), 'format'], ['callable'], false));
        self::assertTrue($testFunction(function(){}, ['callable'], false));
        self::assertFalse($testFunction('is_is_string_not_function', ['callable'], false));
        self::assertFalse($testFunction([new \DateTime(), 'format', 'r'], ['callable'], false));

        // Проверки для имен классов и интерфейсов
        self::assertTrue($testFunction(new \DateTime(), [\DateTimeInterface::class], false));
        self::assertTrue($testFunction(new \DateTime(), ['DateTimeInterface'], false));
        self::assertTrue($testFunction(new \DateTime(), ['DateTime'], false));
        self::assertTrue($testFunction(new \DateTime(), ['\DateTime'], false));
        self::assertTrue($testFunction(new \DateTime(), [\DateTimeInterface::class, _____notClass1231312314244_____::class], false));
        self::assertTrue($testFunction(new \DateTime(), ['DateTimeInterface', '_____notClass1231312314244_____'], false));
        self::assertFalse($testFunction(new \DateTime(), [\Throwable::class], false));
        self::assertFalse($testFunction(new \DateTime(), ['Throwable'], false));

        // * * * Проверка срабатывания, если указано несколько типов

        self::assertTrue($testFunction(false, ['null', 'false'], false));
        self::assertTrue($testFunction(true, ['int', 'true'], false));

        self::assertTrue($testFunction(new \stdClass(), ['array', 'object'], false));
        self::assertTrue($testFunction(new \stdClass(), ['array', \stdClass::class], false));
        self::assertFalse($testFunction(new \stdClass(), ['int', 'string'], false));
    }

    /**
     * Test for {@covers TypeValidator::validateAnd()}
     */
    public function testValidateAnd(): void
    {
        $testFunction = TypeValidator::validateAnd(...);

        self::assertTrue($testFunction(new \stdClass(), [\stdClass::class], false));
        self::assertFalse($testFunction(new \Exception(), [\stdClass::class], false));

        self::assertTrue($testFunction(new \Exception(), [\Exception::class], false));
        self::assertTrue($testFunction(new \Exception(), [\Exception::class, \Throwable::class], false));
        self::assertTrue($testFunction(new \Exception(), ['\Exception', '\Throwable'], false));
        self::assertTrue($testFunction(new \Exception(), ['Exception', 'Throwable'], false));

        self::assertFalse($testFunction(new \stdClass(), [_____notClass1231312314244_____::class], false));
        self::assertFalse($testFunction(new \stdClass(), [\stdClass::class, _____notClass1231312314244_____::class], false));
    }

    /**
     * Test for {@covers TypeValidator::validate()}
     * Test for {@covers TypeValidator::validateOrError()}
     */
    public function testRunValidate(): void
    {
        $testFunctionValidate = TypeValidator::validate(...);
        $testFunctionValidateOrError = TypeValidator::validateOrError(...);

        // * * * Отправка на проверку объединений типов (A|B|C)

        self::assertTrue($testFunctionValidate('xyz', 'string'));
        self::assertTrue($testFunctionValidate('xyz', 'string|int'));
        self::assertTrue($testFunctionValidate(123, 'string|int'));

        self::assertTrue($testFunctionValidate(new \Exception(), '\Exception|\Throwable'));
        self::assertTrue($testFunctionValidate(new \Exception(), '\Exception|\Countable'));

        self::assertFalse($testFunctionValidate(new \Exception(), 'string'));
        self::assertFalse($testFunctionValidate(123, '\Exception|\Throwable'));

        // * * * Отправка на проверку пересечений типов (A&B&C)

        self::assertTrue($testFunctionValidate(new \Exception(), '\Exception&\Throwable'));

        self::assertFalse($testFunctionValidate(new \Exception(), 'string'));
        self::assertFalse($testFunctionValidate(new \Exception(), '\Exception&\Countable'));
        self::assertFalse($testFunctionValidate(new \Exception(), 'Exception&Countable'));
        self::assertFalse($testFunctionValidate(123, '\Exception&\Throwable'));

        // * * * Смесь составных типов не поддерживается

        self::assertTrue(
            ExceptionTools::wasCalledWithException(
                $testFunctionValidate,
                ['xyz', 'A|(B&C)'],
                \LogicException::class
            )
        );

        // * * * Валидация с выбрасыванием исключений

        self::assertTrue(
            ExceptionTools::wasCalledWithException(
                $testFunctionValidateOrError,
                ['xyz', 'A|B'],
                \TypeError::class
            )
        );
        
        self::assertTrue(
            ExceptionTools::wasCalledWithException(
                $testFunctionValidateOrError,
                ['xyz', 'A|B', \Exception::class],
                \Exception::class
            )
        );
    }
}
