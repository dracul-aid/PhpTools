<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Strings;

use DraculAid\PhpTools\Strings\StringTools;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see StringTools}
 *
 * @run php tests/run.php tests/Strings/StringToolsTest.php
 */
class StringToolsTest extends TestCase
{
    /**
     * Test for {@see StringTools::lengthTrim()}
     *
     * @return void
     */
    public function testLengthTrim(): void
    {
        self::assertEquals(3, StringTools::lengthTrim('  обж  '));
        self::assertEquals(3, StringTools::lengthTrim('  abc  '));
        self::assertEquals(3, StringTools::lengthTrim('-abc-', '-'));
    }

    /**
     * Test for {@see StringTools::ipFilenameEncode()}
     * Test for {@see StringTools::ipFilenameDecode()}
     *
     * @return void
     */
    public function testIpFilename(): void
    {
        self::assertEquals('128p200p100p150', StringTools::ipFilenameEncode('128.200.100.150'));
        self::assertEquals('128x200x100xx', StringTools::ipFilenameEncode('128:200:100::'));

        self::assertEquals('128.200.100.150', StringTools::ipFilenameDecode('128p200p100p150'));
        self::assertEquals('128:200:100::', StringTools::ipFilenameDecode('128x200x100xx'));
    }
}
