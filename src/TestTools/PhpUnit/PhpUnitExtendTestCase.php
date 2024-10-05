<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\TestTools\PhpUnit;

use DraculAid\PhpTools\Classes\ClassNotPublicManager;
use PHPUnit\Framework\TestCase;

/**
 * Расширяет стандартный тест-класс PhpUnit
 *
 * Оглавление:
 * <br>{@see PhpUnitExtendTestCase::assertPropertyEquals()}  Проводит проверку для указанного свойства
 * <br>{@see PhpUnitExtendTestCase::assertMethodEquals()} Проводит проверку результатов работы метода
 */
class PhpUnitExtendTestCase extends TestCase
{
    /**
     * Проводит проверку для указанного свойства (в том числе и не публичного)
     *
     * @param   string|object   $classOrObject   Класс (для статического свойства) или объект
     * @param   string          $property        Имя свойства
     * @param   mixed           $equalValue      Ожидаемое значение свойства
     * @param   string          $message         Дополнительное сообщение, будет сгенерировано в случае возникновения ошибки
     *
     * @return void
     *
     * @psalm-param class-string|object $classOrObject
     *
     * @todo PHP8 Типизация аргументов
     */
    public static function assertPropertyEquals($classOrObject, string $property, $equalValue, string $message = ''): void
    {
        self::assertEquals(
            ClassNotPublicManager::readProperty($classOrObject, $property),
            $equalValue,
            $message
        );
    }

    /**
     * Проводит проверку результатов работы метода (в том числе и не публичного)
     *
     * @param   array    $methodAsArray   Вызываемый метод в формате массива [$objectOrClass, $method]
     * @param   array    $arguments       Массив аргументов
     * @param   mixed    $equalValue      Ожидаемое значение свойства
     * @param   string   $message         Дополнительное сообщение, будет сгенерировано в случае возникновения ошибки
     *
     * @return void
     *
     * @psalm-param array{class-string|object, string} $methodAsArray
     *
     * @todo PHP8 Типизация аргументов
     */
    public static function assertMethodEquals(array $methodAsArray, array $arguments, $equalValue, string $message = ''): void
    {
        self::assertEquals(
            ClassNotPublicManager::callMethod($methodAsArray, $arguments),
            $equalValue,
            $message
        );
    }
}
