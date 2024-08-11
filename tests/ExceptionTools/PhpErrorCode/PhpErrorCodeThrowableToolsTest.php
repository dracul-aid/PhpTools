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
use DraculAid\PhpTools\ExceptionTools\PhpErrorCode\PhpErrorCodeThrowableTools;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@coversDefaultClass PhpErrorCodeThrowableTools}
 *
 * @run php tests/run.php tests/ExceptionTools/PhpErrorCode/PhpErrorCodeThrowableToolsTest.php
 */
class PhpErrorCodeThrowableToolsTest extends TestCase
{
    /**
     * Test for {@covers PhpErrorCodeThrowableTools::TYPE_AND_ERROR_CLASSES}
     * Test for {@covers PhpErrorCodeThrowableTools::getBasicErrorObject()}
     * Test for {@covers PhpErrorCodeThrowableTools::getErrorObject()}
     *
     * @return void
     */
    public function testRun(): void
    {
        self::assertEquals(PhpErrorCodeConstants::ALL, array_keys(PhpErrorCodeThrowableTools::TYPE_AND_ERROR_CLASSES));

        foreach (PhpErrorCodeThrowableTools::TYPE_AND_ERROR_CLASSES as $errCode => $errClass)
        {
            self::assertEquals($errCode, $errClass::getPhpErrorCode());
            self::assertEquals($errClass, get_class(PhpErrorCodeThrowableTools::getErrorObject($errCode)));
        }

        foreach (PhpErrorCodeThrowableTools::TYPE_AND_BASIC_ERROR_CLASSES as $errCode => $errClass)
        {
            self::assertEquals($errClass, get_class(PhpErrorCodeThrowableTools::getBasicErrorObject($errCode)));
        }
    }
}
