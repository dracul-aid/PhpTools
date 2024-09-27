<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\ExceptionTools\PhpErrorCode;

use DraculAid\PhpTools\ExceptionTools\PhpErrorCode\PhpErrorCodeConstants;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@coversDefaultClass PhpErrorCodeConstants}
 *
 * @run php tests/run.php tests/ExceptionTools/PhpErrorCode/PhpErrorCodeConstantsTest.php
 */
class PhpErrorCodeConstantsTest extends TestCase
{
    /**
     * @covers PhpErrorCodeConstants::NAMES
     * @covers PhpErrorCodeConstants::ALL
     *
     * @return void
     */
    public function testRun(): void
    {
        // * * * Имена констант есть в списке всех констант ошибок

        /** @psalm-suppress RedundantFunctionCall Нам нужно гарантировать что мы берем значения из {@see PhpErrorCodeConstants::ALL} */
        self::assertEquals(array_values(PhpErrorCodeConstants::ALL), array_keys(PhpErrorCodeConstants::NAMES));
        foreach (PhpErrorCodeConstants::NAMES as $contValue => $constName)
        {
            self::assertEquals($contValue, constant($constName), "Error value for const `{$constName}`");
        }

        // * * * списки ошибок по типам присутствуют в списке всех констант ошибок

        $testLists = [
            'WARNINGS_AND_NOTICES' => PhpErrorCodeConstants::WARNINGS_AND_NOTICES,
            'FATAL_ERRORS' => PhpErrorCodeConstants::FATAL_ERRORS,
            'SCRIPT_ERRORS' => PhpErrorCodeConstants::SCRIPT_ERRORS,
            'CORE_ERRORS' => PhpErrorCodeConstants::CORE_ERRORS,
            'CODE_ERRORS' => PhpErrorCodeConstants::CODE_ERRORS,
            'DEPRECATED_ERRORS' => PhpErrorCodeConstants::DEPRECATED_ERRORS,
            'USER_ERRORS' => PhpErrorCodeConstants::USER_ERRORS,
        ];

        foreach ($testLists as $listName => $errorTypeList)
        {
            foreach ($errorTypeList as $errorType)
            {
                self::assertTrue(
                    in_array($errorType, PhpErrorCodeConstants::ALL),
                    "Value error {$errorType} for list {$listName}"
                );
            }
        }

        // * * * Проверяем, что все ошибки есть хотя бы в одном тематическом списке

        foreach (PhpErrorCodeConstants::NAMES as $errorType => $errorName)
        {
            foreach ($testLists as $errorTypeList)
            {
                if (in_array($errorType, $errorTypeList)) continue 2;
            }

            $this->fail("Const {$errorName} ({$errorType}) not found in type error constants");
        }
    }
}
