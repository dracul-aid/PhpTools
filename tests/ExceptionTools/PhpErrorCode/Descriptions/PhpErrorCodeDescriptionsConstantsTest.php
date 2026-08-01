<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\ExceptionTools\PhpErrorCode\Descriptions;

use DraculAid\PhpTools\ExceptionTools\PhpErrorCode\Descriptions\PhpErrorCodeEnDescriptionsConstants;
use DraculAid\PhpTools\ExceptionTools\PhpErrorCode\Descriptions\PhpErrorCodeRuDescriptionsConstants;
use DraculAid\PhpTools\ExceptionTools\PhpErrorCode\PhpErrorCodeConstants;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@coversDefaultClass PhpErrorCodeEnDescriptionsConstants}
 * Test for {@coversDefaultClass PhpErrorCodeRuDescriptionsConstants}
 *
 * @run php tests/run.php tests/ExceptionTools/PhpErrorCode/Descriptions/PhpErrorCodeDescriptionsConstantsTest.php
 */
class PhpErrorCodeDescriptionsConstantsTest extends TestCase
{
    /**
     * @covers PhpErrorCodeEnDescriptionsConstants::TITLES
     * @covers PhpErrorCodeEnDescriptionsConstants::TITLES
     * @covers PhpErrorCodeRuDescriptionsConstants::DESCRIPTIONS
     * @covers PhpErrorCodeRuDescriptionsConstants::DESCRIPTIONS
     *
     * @return void
     */
    public function testRun(): void
    {
        foreach ([PhpErrorCodeEnDescriptionsConstants::class, PhpErrorCodeRuDescriptionsConstants::class] as $className)
        {
            self::assertEquals(PhpErrorCodeConstants::ALL, array_keys($className::TITLES));
            self::assertEquals(PhpErrorCodeConstants::ALL, array_keys($className::DESCRIPTIONS));
        }
    }
}
