<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Classes\Patterns\Singleton;

use DraculAid\PhpTools\Classes\Patterns\Singleton\SingletonTrait;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see SingletonTrait}
 *
 * @run php tests/run.php tests/Classes/Patterns/Singleton/SingletonTraitTest.php
 */
class SingletonTraitTest extends TestCase
{
    public function testRun(): void
    {
        $singletonClass1 = $this->createClassWithSingletonTrait();
        $singletonClass2 = $this->createClassWithSingletonTrait();

        self::assertTrue($singletonClass1::getInstance() === $singletonClass1::getInstance());
        self::assertTrue($singletonClass2::getInstance() === $singletonClass2::getInstance());
        self::assertFALSE($singletonClass1::getInstance() === $singletonClass2::getInstance());
    }

    /**
     * @return class-string
     *
     * @psalm-suppress MoreSpecificReturnType Псалм не верит, что тут вернется имя класса, но оно так)
     * @psalm-suppress LessSpecificReturnStatement Псалм не верит, что тут вернется имя класса, но оно так)
     */
    private function createClassWithSingletonTrait(): string
    {
        $testClassName = '___createClassWithSingletoneTrait_' . uniqid() . '___';

        eval("class {$testClassName} {use \\" . SingletonTrait::class . ";}");

        return $testClassName;
    }
}
