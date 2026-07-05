<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Code\Objects;

use DraculAid\PhpTools\Classes\ClassNotPublicManager;
use DraculAid\PhpTools\Code\Objects\DocBlockTypeStorage;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@covers DocBlockTypeStorage}
 *
 * @run php tests/run.php tests/Code/Objects/DocBlockTypeStorageTest.php
 */
class DocBlockTypeStorageTest extends TestCase
{
    public function testRun(): void
    {
        $testFunctionCreateFromPhp = DocBlockTypeStorage::createFromPhp(...);
        $testFunctionCreateFromSql = DocBlockTypeStorage::createFromSql(...);
        $testFunctionCreateFromDocBlock = DocBlockTypeStorage::createFromDocBlock(...);

        // * * * PHP типы

        $testObj = $testFunctionCreateFromPhp('int|float');
        self::assertCount(2, array_keys(ClassNotPublicManager::readProperty($testObj, 'types')));
        self::assertTrue($testObj->isWithType('int'));
        self::assertTrue($testObj->isWithType('float'));
        self::assertEquals('int|float', (string)$testObj);

        // * * * DocBlock типы

        $testObj = $testFunctionCreateFromDocBlock('integer|double|str|boolean');
        self::assertCount(4, array_keys(ClassNotPublicManager::readProperty($testObj, 'types')));
        self::assertTrue($testObj->isWithType('int'));
        self::assertTrue($testObj->isWithType('float'));
        self::assertTrue($testObj->isWithType('bool'));
        self::assertTrue($testObj->isWithType('string'));
        self::assertFalse($testObj->isWithNull());
        self::assertTrue($testObj->isWithBool());

        // * * * SQL типы

        $testObj = $testFunctionCreateFromSql('tinyint', false);
        self::assertCount(1, array_keys(ClassNotPublicManager::readProperty($testObj, 'types')));
        self::assertTrue($testObj->isWithType('int'));

        $testObj = $testFunctionCreateFromSql('smallint', true);
        self::assertCount(2, array_keys(ClassNotPublicManager::readProperty($testObj, 'types')));
        self::assertTrue($testObj->isWithType('int'));
        self::assertTrue($testObj->isWithType('null'));
        self::assertTrue($testObj->isWithNull());
        self::assertFalse($testObj->isWithBool());

        // * * * Проверки типов

        $testObj = $testFunctionCreateFromPhp('string');
        self::assertFalse($testObj->isWithBool());
        self::assertFalse($testObj->isWithNull());
        self::assertFalse($testObj->isWithNumber());
        self::assertFalse($testObj->isWithType('int'));
        self::assertTrue($testObj->isWithType('string'));

        $testObj = $testFunctionCreateFromPhp('bool');
        self::assertTrue($testObj->isWithBool());
        self::assertFalse($testObj->isWithNull());
        self::assertFalse($testObj->isWithNumber());

        $testObj = $testFunctionCreateFromPhp('false');
        self::assertTrue($testObj->isWithBool());
        self::assertFalse($testObj->isWithNull());
        self::assertFalse($testObj->isWithNumber());

        $testObj = $testFunctionCreateFromPhp('true');
        self::assertTrue($testObj->isWithBool());
        self::assertFalse($testObj->isWithNull());
        self::assertFalse($testObj->isWithNumber());

        $testObj = $testFunctionCreateFromPhp('int');
        self::assertFalse($testObj->isWithBool());
        self::assertFalse($testObj->isWithNull());
        self::assertTrue($testObj->isWithNumber());

        $testObj = $testFunctionCreateFromPhp('float');
        self::assertFalse($testObj->isWithBool());
        self::assertFalse($testObj->isWithNull());
        self::assertTrue($testObj->isWithNumber());
    }
}