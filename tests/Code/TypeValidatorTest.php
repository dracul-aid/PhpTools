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
 * Test for {@see TypeValidator}
 *
 * @run php tests/run.php tests/Code/TypeValidatorTest.php
 *
 * @psalm-suppress UndefinedClass В тесте используются несуществующие классы, это нормально
 */
class TypeValidatorTest extends TestCase
{
    /**
     * Test for {@see TypeValidator::validateOr()}
     */
    public function testValidateOr(): void
    {
        // Проверки для NULL
        self::assertTrue(TypeValidator::validateOr(null, ['null'], false));
        self::assertFalse(TypeValidator::validateOr(null, ['bool'], false));

        // Проверки для BOOLEAN
        self::assertTrue(TypeValidator::validateOr(false, ['bool'], false));
        self::assertTrue(TypeValidator::validateOr(true, ['bool'], false));
        self::assertFalse(TypeValidator::validateOr(false, ['int'], false));
        self::assertFalse(TypeValidator::validateOr(true, ['int'], false));

        // Проверки для псевдотипов BOOLEAN
        self::assertTrue(TypeValidator::validateOr(false, ['false'], false));
        self::assertTrue(TypeValidator::validateOr(true, ['true'], false));
        self::assertFalse(TypeValidator::validateOr(false, ['true'], false));
        self::assertFalse(TypeValidator::validateOr(true, ['false'], false));

        // Проверки INT
        self::assertTrue(TypeValidator::validateOr(123, ['int'], false));
        self::assertFalse(TypeValidator::validateOr(123, ['float'], false));

        // Проверки FLOAT
        self::assertTrue(TypeValidator::validateOr(123.0, ['float'], false));
        self::assertTrue(TypeValidator::validateOr(123.123, ['float'], false));
        self::assertFalse(TypeValidator::validateOr(123, ['float'], false));

        // Названия типов "похожие" на типы PHP, но таковыми не являющиеся
        self::assertFalse(TypeValidator::validateOr(123, ['integer'], false));
        self::assertFalse(TypeValidator::validateOr(123, ['number'], false));
        self::assertFalse(TypeValidator::validateOr(123.123, ['double'], false));

        // Строки STRING
        self::assertTrue(TypeValidator::validateOr('', ['string'], false));
        self::assertTrue(TypeValidator::validateOr('123', ['string'], false));

        // Проверка ARRAY
        self::assertTrue(TypeValidator::validateOr([], ['array'], false));
        self::assertTrue(TypeValidator::validateOr([1, 2, 3], ['array'], false));
        self::assertFalse(TypeValidator::validateOr(new \stdClass(), ['array'], false));

        // Базовая проверка OBJECT
        self::assertTrue(TypeValidator::validateOr(new \stdClass(), ['object'], false));
        self::assertFalse(TypeValidator::validateOr(['a' => 1, 'b' => 2], ['object'], false));

        // Строгое соответствие имени класса
        self::assertTrue(TypeValidator::validateOr(new \stdClass(), [\stdClass::class], false));
        self::assertTrue(TypeValidator::validateOr($this, [TypeValidatorTest::class], false));
        self::assertFalse(TypeValidator::validateOr(new \stdClass(), [TypeValidatorTest::class], false));
        self::assertFalse(TypeValidator::validateOr($this, [\stdClass::class], false));

        // Проверки для "перечисляемого"
        self::assertTrue(TypeValidator::validateOr([], ['iterable'], false));
        self::assertTrue(TypeValidator::validateOr(['123'], ['iterable'], false));
        self::assertTrue(TypeValidator::validateOr((function (): \Generator {yield 123;})(), ['iterable'], false));
        self::assertFalse(TypeValidator::validateOr(new \stdClass(), ['iterable'], false));

        // Проверки для "вызываемого"
        self::assertTrue(TypeValidator::validateOr('in_array', ['callable'], false));
        self::assertTrue(TypeValidator::validateOr([new \DateTime(), 'format'], ['callable'], false));
        self::assertTrue(TypeValidator::validateOr(function(){}, ['callable'], false));
        self::assertFalse(TypeValidator::validateOr('is_is_string_not_function', ['callable'], false));
        self::assertFalse(TypeValidator::validateOr([new \DateTime(), 'format', 'r'], ['callable'], false));

        // Проверки для имен классов и интерфейсов
        self::assertTrue(TypeValidator::validateOr(new \DateTime(), [\DateTimeInterface::class], false));
        self::assertTrue(TypeValidator::validateOr(new \DateTime(), ['DateTimeInterface'], false));
        self::assertTrue(TypeValidator::validateOr(new \DateTime(), ['DateTime'], false));
        self::assertTrue(TypeValidator::validateOr(new \DateTime(), ['\DateTime'], false));
        self::assertTrue(TypeValidator::validateOr(new \DateTime(), [\DateTimeInterface::class, _____notClass1231312314244_____::class], false));
        self::assertTrue(TypeValidator::validateOr(new \DateTime(), ['DateTimeInterface', '_____notClass1231312314244_____'], false));
        self::assertFalse(TypeValidator::validateOr(new \DateTime(), [\Throwable::class], false));
        self::assertFalse(TypeValidator::validateOr(new \DateTime(), ['Throwable'], false));

        // * * * Проверка срабатывания, если указано несколько типов

        self::assertTrue(TypeValidator::validateOr(false, ['null', 'false'], false));
        self::assertTrue(TypeValidator::validateOr(true, ['int', 'true'], false));

        self::assertTrue(TypeValidator::validateOr(new \stdClass(), ['array', 'object'], false));
        self::assertTrue(TypeValidator::validateOr(new \stdClass(), ['array', \stdClass::class], false));
        self::assertFalse(TypeValidator::validateOr(new \stdClass(), ['int', 'string'], false));
    }

    /**
     * Test for {@see TypeValidator::validateAnd()}
     */
    public function testValidateAnd(): void
    {
        self::assertTrue(TypeValidator::validateAnd(new \stdClass(), [\stdClass::class], false));
        self::assertFalse(TypeValidator::validateAnd(new \Exception(), [\stdClass::class], false));

        self::assertTrue(TypeValidator::validateAnd(new \Exception(), [\Exception::class], false));
        self::assertTrue(TypeValidator::validateAnd(new \Exception(), [\Exception::class, \Throwable::class], false));
        self::assertTrue(TypeValidator::validateAnd(new \Exception(), ['\Exception', '\Throwable'], false));
        self::assertTrue(TypeValidator::validateAnd(new \Exception(), ['Exception', 'Throwable'], false));

        self::assertFalse(TypeValidator::validateAnd(new \stdClass(), [_____notClass1231312314244_____::class], false));
        self::assertFalse(TypeValidator::validateAnd(new \stdClass(), [\stdClass::class, _____notClass1231312314244_____::class], false));
    }

    /**
     * Test for {@see TypeValidator::validate()}
     * Test for {@see TypeValidator::validateOrError()}
     */
    public function testRunValidate(): void
    {
        // * * * Отправка на проверку объединений типов (A|B|C)

        self::assertTrue(TypeValidator::validate('xyz', 'string'));
        self::assertTrue(TypeValidator::validate('xyz', 'string|int'));
        self::assertTrue(TypeValidator::validate(123, 'string|int'));

        self::assertTrue(TypeValidator::validate(new \Exception(), '\Exception|\Throwable'));
        self::assertTrue(TypeValidator::validate(new \Exception(), '\Exception|\Countable'));

        self::assertFalse(TypeValidator::validate(new \Exception(), 'string'));
        self::assertFalse(TypeValidator::validate(123, '\Exception|\Throwable'));

        // * * * Отправка на проверку пересечений типов (A&B&C)

        self::assertTrue(TypeValidator::validate(new \Exception(), '\Exception&\Throwable'));

        self::assertFalse(TypeValidator::validate(new \Exception(), 'string'));
        self::assertFalse(TypeValidator::validate(new \Exception(), '\Exception&\Countable'));
        self::assertFalse(TypeValidator::validate(new \Exception(), 'Exception&Countable'));
        self::assertFalse(TypeValidator::validate(123, '\Exception&\Throwable'));

        // * * * Смесь составных типов не поддерживается

        self::assertTrue(
            ExceptionTools::wasCalledWithException(
                [TypeValidator::class, 'validate'],
                ['xyz', 'A|(B&C)'],
                \LogicException::class
            )
        );

        // * * * Валидация с выбрасыванием исключений

        self::assertTrue(
            ExceptionTools::wasCalledWithException(
                [TypeValidator::class, 'validateOrError'],
                ['xyz', 'A|B'],
                \TypeError::class
            )
        );
        
        self::assertTrue(
            ExceptionTools::wasCalledWithException(
                [TypeValidator::class, 'validateOrError'],
                ['xyz', 'A|B', \Exception::class],
                \Exception::class
            )
        );
    }
}
