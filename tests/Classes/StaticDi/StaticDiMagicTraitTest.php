<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Classes\StaticDi;

use DraculAid\PhpTools\Classes\StaticDi\StaticDi;
use DraculAid\PhpTools\Classes\StaticDi\StaticDiMagicTrait;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@covers StaticDiMagicTrait}
 *
 * @run php tests/run.php tests/Classes/StaticDi/StaticDiMagicTraitTest.php
 *
 * @runTestsInSeparateProcesses Все тест-методы в данном классе будут вызваться в отдельном потоке
 *
 * @psalm-suppress UndefinedClass В качестве имен классов (для упрощения написания теста) используются всякие мусорные значения
 */
class StaticDiMagicTraitTest extends TestCase
{
    /** @var class-string|'' $classWithTrait Класс внутри которого будет тестируемый трейт */
    private string $classWithTrait = '';

    /** @var class-string|'' $classForDiRule Класс "для замены" */
    private string $classForDiRule = '';

    public function testRun(): void
    {
        $this->createTestClasses();

        StaticDi::getDefaultInstance()->rules = [$this->classWithTrait => $this->classForDiRule];
        self::assertEquals('fake', $this->classWithTrait::f());
    }

    /**
     * Создаем классы для тестирования функционала трейта
     *
     * @return void
     */
    private function createTestClasses(): void
    {
        // * * * Класс внутри которого будет тестируемый трейт

        $testObjectForTrait = new class() {
            use StaticDiMagicTrait;
        };

        $this->classWithTrait = get_class($testObjectForTrait);

        // * * * Класс "для замены"

        $testObjectForDiRule = new class() {
            public static function f(): string
            {
                return 'fake';
            }
        };

        $this->classForDiRule = get_class($testObjectForDiRule);
    }
}
