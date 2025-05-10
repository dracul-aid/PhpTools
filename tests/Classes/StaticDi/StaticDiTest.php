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
use DraculAid\PhpTools\Classes\StaticDi\StaticDiEvents;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@coversDefaultClass StaticDi}
 *
 * @run php tests/run.php tests/Classes/StaticDi/StaticDiTest.php
 *
 * @runTestsInSeparateProcesses Все тест-методы в данном классе будут вызваться в отдельном потоке
 *
 * @psalm-suppress UndefinedClass В качестве имен классов (для упрощения написания теста) используются всякие мусорные значения
 */
class StaticDiTest extends TestCase
{
    /**
     * Test for {@covers StaticDi::getDefaultInstance()} без событий
     *
     * @return void
     */
    public function testGetDefaultInstanceWithoutEvents(): void
    {
        $staticDiFirst = StaticDi::getDefaultInstance();
        $staticDiSecond = StaticDi::getDefaultInstance();

        self::assertTrue($staticDiFirst === $staticDiSecond);
    }

    /**
     * Test for {@covers StaticDi::getDefaultInstance()} с событиями
     *
     * @return void
     */
    public function testGetDefaultInstanceWithEvents(): void
    {
        $f1Var = 0;
        $f2Var = 0;

        StaticDiEvents::$eventDefaultCreate = [
            static function () use (&$f1Var): void {
                static $a = 0;
                $f1Var += $a + 10;
            },
            static function () use (&$f2Var): void {
                static $a = 0;
                $f2Var += $a + 100;
            },
        ];

        // * * * проводим тест

        $staticDiFirst = StaticDi::getDefaultInstance();
        self::assertEquals(10, $f1Var);
        self::assertEquals(100, $f2Var);

        $staticDiSecond = StaticDi::getDefaultInstance();
        self::assertEquals(10, $f1Var);
        self::assertEquals(100, $f2Var);

        self::assertTrue($staticDiFirst === $staticDiSecond);

        // * * * на создание контейнера через конструктор, события срабатывать не должны

        new StaticDi();
        self::assertEquals(10, $f1Var);
        self::assertEquals(100, $f2Var);

        // * * * убеждаемся, что функции события работают корректно

        foreach (StaticDiEvents::$eventDefaultCreate as $f) $f();
        self::assertEquals(20, $f1Var);
        self::assertEquals(200, $f2Var);
    }

    /**
     * Тестируем получение классов из контейнеров
     *
     * (!) Выделено в отдельный метод, для ускорения прохождения теста (так как все тесты этого тест-класса проходят в отдельном потоке)
     *
     * Test for {@covers StaticDi::get()}
     * Test for {@covers StaticDi::getClass()}
     * Test for {@covers StaticDi::keyGetClass()}
     *
     * @return void
     */
    public function testGetRun(): void
    {
        $this->testGetClass();
        $this->testGetRunWithoutEvents();
        $this->testGetRunWithEvents();
    }

    /**
     * Test for {@covers StaticDi::get()}
     *
     * @return void
     */
    private function testGetClass(): void
    {
        $staticDiTest = StaticDi::getDefaultInstance();

        self::assertTrue($staticDiTest->getClass(\DateTime::class) === StaticDi::get(\DateTime::class));

        $staticDiTest->rules = [\DateTime::class => 'aaaa'];
        self::assertEquals('aaaa', StaticDi::get(\DateTime::class));
        self::assertTrue($staticDiTest->getClass(\DateTime::class) === StaticDi::get(\DateTime::class));
    }

    /**
     * Тестируем без событий
     *
     * @return void
     */
    private function testGetRunWithoutEvents(): void
    {
        // * * * Вернем какой-то класс

        $testDi = new StaticDi();

        self::assertEquals(\stdClass::class, $testDi->keyGetClass('-rule-', \stdClass::class));
        self::assertEquals(\DateTime::class, $testDi->keyGetClass('-rule-', \DateTime::class));
        self::assertEquals(\DateTime::class, $testDi->getClass(\DateTime::class));
        self::assertEquals(\stdClass::class, $testDi->getClass(\stdClass::class));

        $testDi->rules = ['x' => \stdClass::class, '-rule-' => \Exception::class, 'z' => \DateTime::class];
        self::assertEquals(\Exception::class, $testDi->keyGetClass('-rule-', \DateTime::class));
        self::assertEquals(\DateTime::class, $testDi->getClass(\DateTime::class));
        self::assertEquals(\stdClass::class, $testDi->getClass(\stdClass::class));

        self::assertEquals(\DateTime::class, $testDi->getClass(\DateTime::class));
        $testDi->rules[\DateTime::class] = \Error::class;
        self::assertEquals(\Error::class, $testDi->getClass(\DateTime::class));
    }

    /**
     * Тестируем с событиями
     *
     * @return void
     *
     * @psalm-suppress UnusedParam Функции с неиспользуемыми аргументами нужны, для проверки работы событий
     */
    private function testGetRunWithEvents(): void
    {
        // * * * Событие "до поиска" - не остановит поиск

        $testDi = new StaticDi();

        $testDi->rules = ['not-rule' => 'www'];

        /** @psalm-suppress PropertyTypeCoercion тут нет смысла добавлять докблоки для анонимных функций (все таки это тест), а без них псалм ругается */
        $testDi->events->eventSearchBefore = [
            static function (StaticDi $di, string $key, string $defaultClass): string {
                return '';
            },
        ];

        self::assertEquals('www', $testDi->keyGetClass('not-rule', 'xxx'));

        // * * * Событие "до поиска" - остановит поиск

        $testDi = new StaticDi();

        $testDi->rules = ['not-rule' => 'www'];

        /** @psalm-suppress PropertyTypeCoercion тут нет смысла добавлять докблоки для анонимных функций (все таки это тест), а без них псалм ругается */
        $testDi->events->eventSearchBefore = [
            static function (StaticDi $di, string $key, string $defaultClass): string {
                return '';
            },
            static function (StaticDi $di, string $key, string $defaultClass): string {
                /** @psalm-suppress PossiblyInvalidCast $di->rules могут содержать не только строками, но и функциями */
                return "zzz:{$key}:{$defaultClass}:{$di->rules['not-rule']}";
            },
            static function (StaticDi $di, string $key, string $defaultClass): string {
                return \ArrayObject::class;
            },
        ];

        self::assertEquals('zzz:not-rule:xxx:www', $testDi->keyGetClass('not-rule', 'xxx'));

        // * * * Событие "после поиска" - вернет найденное

        $testDi = new StaticDi();

        $testDi->rules = ['not-rule' => 'www'];

        /** @psalm-suppress PropertyTypeCoercion тут нет смысла добавлять докблоки для анонимных функций (все таки это тест), а без них псалм ругается */
        $testDi->events->eventSearchAfter = [
            static function (StaticDi $di, string $key, string $defaultClass, string $resultClass): string {
                return '';
            },
        ];

        self::assertEquals('www', $testDi->keyGetClass('not-rule', 'xxx'));


        // * * * Событие "после поиска" - вернет новый результат

        $testDi = new StaticDi();

        $testDi->rules = ['not-rule' => 'www'];

        /** @psalm-suppress PropertyTypeCoercion тут нет смысла добавлять докблоки для анонимных функций (все таки это тест), а без них псалм ругается */
        $testDi->events->eventSearchAfter = [
            static function (StaticDi $di, string $key, string $defaultClass, string $resultClass): string {
                return '';
            },
            static function (StaticDi $di, string $key, string $defaultClass, string $resultClass): string {
                /** @psalm-suppress PossiblyInvalidCast $di->rules могут содержать не только строками, но и функциями */
                return "zzz:{$key}:{$defaultClass}:{$di->rules['not-rule']}:www";
            },
            static function (StaticDi $di, string $key, string $defaultClass, string $resultClass): string {
                return \ArrayObject::class;
            },
        ];

        self::assertEquals('zzz:not-rule:xxx:www:www', $testDi->keyGetClass('not-rule', 'xxx'));
    }
}
