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

use DraculAid\PhpTools\Classes\ClassNotPublicManager;
use DraculAid\PhpTools\Code\CodeTypeObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see CodeTypeObject}
 *
 * @run php tests/run.php tests/Code/CodeTypeObjectTest.php
 */
class CodeTypeObjectTest extends TestCase
{
    public function testRun(): void
    {
        // * * * PHP типы

        $testObj = CodeTypeObject::createFromPhp('int|float');
        self::assertCount(2, array_keys(ClassNotPublicManager::readProperty($testObj, 'types')));
        self::assertTrue($testObj->isWithType('int'));
        self::assertTrue($testObj->isWithType('float'));
        self::assertEquals('int|float', (string)$testObj);

        // * * * DocBlock типы

        $testObj = CodeTypeObject::createFromDocBlock('integer|double|str|boolean');
        self::assertCount(4, array_keys(ClassNotPublicManager::readProperty($testObj, 'types')));
        self::assertTrue($testObj->isWithType('int'));
        self::assertTrue($testObj->isWithType('float'));
        self::assertTrue($testObj->isWithType('bool'));
        self::assertTrue($testObj->isWithType('string'));
        self::assertFalse($testObj->isWithNull());
        self::assertTrue($testObj->isWithBool());

        // * * * SQL типы

        $testObj = CodeTypeObject::createFromSql('tinyint', false);
        self::assertCount(1, array_keys(ClassNotPublicManager::readProperty($testObj, 'types')));
        self::assertTrue($testObj->isWithType('int'));

        $testObj = CodeTypeObject::createFromSql('smallint', true);
        self::assertCount(2, array_keys(ClassNotPublicManager::readProperty($testObj, 'types')));
        self::assertTrue($testObj->isWithType('int'));
        self::assertTrue($testObj->isWithType('null'));
        self::assertTrue($testObj->isWithNull());
        self::assertFalse($testObj->isWithBool());

        // * * * Проверки типов

        $testObj = CodeTypeObject::createFromPhp('string');
        self::assertFalse($testObj->isWithBool());
        self::assertFalse($testObj->isWithNull());
        self::assertFalse($testObj->isWithNumber());
        self::assertFalse($testObj->isWithType('int'));
        self::assertTrue($testObj->isWithType('string'));

        $testObj = CodeTypeObject::createFromPhp('bool');
        self::assertTrue($testObj->isWithBool());
        self::assertFalse($testObj->isWithNull());
        self::assertFalse($testObj->isWithNumber());

        $testObj = CodeTypeObject::createFromPhp('false');
        self::assertTrue($testObj->isWithBool());
        self::assertFalse($testObj->isWithNull());
        self::assertFalse($testObj->isWithNumber());

        $testObj = CodeTypeObject::createFromPhp('true');
        self::assertTrue($testObj->isWithBool());
        self::assertFalse($testObj->isWithNull());
        self::assertFalse($testObj->isWithNumber());

        $testObj = CodeTypeObject::createFromPhp('int');
        self::assertFalse($testObj->isWithBool());
        self::assertFalse($testObj->isWithNull());
        self::assertTrue($testObj->isWithNumber());

        $testObj = CodeTypeObject::createFromPhp('float');
        self::assertFalse($testObj->isWithBool());
        self::assertFalse($testObj->isWithNull());
        self::assertTrue($testObj->isWithNumber());
    }
}
