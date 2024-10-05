<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Classes;

use DraculAid\PhpTools\Classes\ClassNotPublicManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see ClassNotPublicManager}
 *
 * @run php tests/run.php tests/Classes/ClassNotPublicManagerTest.php
 */
class ClassNotPublicManagerTest extends TestCase
{
    public function testCreate(): void
    {
        $manager1 = ClassNotPublicManager::getInstanceFor(\stdClass::class);
        $manager2 = ClassNotPublicManager::getInstanceFor(\stdClass::class);

        self::assertTrue($manager1 === $manager2);

        // * * *

        $manager1 = ClassNotPublicManager::getInstanceFor(new \stdClass);
        $manager2 = ClassNotPublicManager::getInstanceFor(new \stdClass);

        self::assertFalse($manager1 === $manager2);
    }

    /**
     * Test for {@see ClassNotPublicManager::constant()}
     * Test for {@see ClassNotPublicManager::get()}
     * Test for {@see ClassNotPublicManager::getStatic()}
     * Test for {@see ClassNotPublicManager::set()}
     * Test for {@see ClassNotPublicManager::setStatic()}
     * Test for {@see ClassNotPublicManager::call()}
     * Test for {@see ClassNotPublicManager::callStatic()}
     */
    public function testObject(): void
    {
        // * * * Чтение из объекта

        $testNotPublic = ClassNotPublicManager::getInstanceFor($this->createObject());

        self::assertEquals('private_const_value', $testNotPublic->constant('PRIVATE_CONST'));

        self::assertEquals('private_var_value', $testNotPublic->get('private_var'));
        self::assertEquals('private_static_var_value', $testNotPublic->getStatic('private_static_var'));

        $testNotPublic->set('private_var', '111')->setStatic('private_static_var', '222');
        self::assertEquals('111', $testNotPublic->get('private_var'));
        self::assertEquals('222', $testNotPublic->getStatic('private_static_var'));

        $testNotPublic->set(['private_var' => 'AAA'])->setStatic(['private_static_var' => 'BBB']);
        self::assertEquals('AAA', $testNotPublic->get('private_var'));
        self::assertEquals('BBB', $testNotPublic->getStatic('private_static_var'));

        $f_t1 = 'A111';
        self::assertEquals("private_function_return_[A111]_B222", $testNotPublic->call('private_function', [&$f_t1, 'B222']));
        self::assertEquals("[A111]", $f_t1);
        $f_t1 = 'С111';
        self::assertEquals("private_static_function_return_[С111]_D222", $testNotPublic->callStatic('private_static_function', [&$f_t1, 'D222']));
        self::assertEquals('[С111]', $f_t1);

        // * * * Чтение из класса

        $testClass = $this->createStaticClass();

        $testNotPublic = ClassNotPublicManager::getInstanceFor($testClass);

        self::assertEquals('private_const_value', $testNotPublic->constant('PRIVATE_CONST'));

        self::assertEquals('private_static_var_value', $testNotPublic->getStatic('private_static_var'));
        $testNotPublic->setStatic('private_static_var', '222');
        self::assertEquals('222', $testNotPublic->getStatic('private_static_var'));

        $f_t1 = 'С111';
        self::assertEquals("private_static_function_return_[С111]_D222", $testNotPublic->callStatic('private_static_function', [&$f_t1, 'D222']));
        self::assertEquals('[С111]', $f_t1);
    }

    /**
     * Test for {@see ClassNotPublicManager::readConstant()}
     * Test for {@see ClassNotPublicManager::readProperty()}
     * Test for {@see ClassNotPublicManager::writeProperty()}
     * Test for {@see ClassNotPublicManager::callMethod()}
     */
    public function testEasyObject(): void
    {
        // * * * Чтение из объекта

        $testObject = $this->createObject();
        $testClass = get_class($testObject);

        self::assertEquals('private_const_value', ClassNotPublicManager::readConstant($testObject, 'PRIVATE_CONST'));
        self::assertEquals('private_const_value', ClassNotPublicManager::readConstant($testClass, 'PRIVATE_CONST'));

        self::assertEquals('private_var_value', ClassNotPublicManager::readProperty($testObject,'private_var'));
        self::assertEquals('private_static_var_value', ClassNotPublicManager::readProperty($testClass, 'private_static_var'));

        ClassNotPublicManager::writeProperty($testObject, 'private_var', '111');
        ClassNotPublicManager::writeProperty($testClass, 'private_static_var', '222');
        self::assertEquals('111', ClassNotPublicManager::readProperty($testObject,'private_var'));
        self::assertEquals('222', ClassNotPublicManager::readProperty($testClass, 'private_static_var'));

        ClassNotPublicManager::writeProperty($testObject, ['private_var' => 'AAA']);
        ClassNotPublicManager::writeProperty($testClass, ['private_static_var' => 'BBB']);
        self::assertEquals('AAA', ClassNotPublicManager::readProperty($testObject,'private_var'));
        self::assertEquals('BBB', ClassNotPublicManager::readProperty($testClass, 'private_static_var'));

        $f_t1 = 'A111';
        self::assertEquals("private_function_return_[A111]_B222", ClassNotPublicManager::callMethod([$testObject, 'private_function'], [&$f_t1, 'B222']));
        self::assertEquals("[A111]", $f_t1);
        $f_t1 = 'С111';
        self::assertEquals("private_static_function_return_[С111]_D222", ClassNotPublicManager::callMethod([$testClass, 'private_static_function'], [&$f_t1, 'D222']));
        self::assertEquals('[С111]', $f_t1);

        // * * * Чтение из класса

        $testClass = $this->createStaticClass();

        self::assertEquals('private_const_value', ClassNotPublicManager::readConstant($testClass, 'PRIVATE_CONST'));

        self::assertEquals('private_static_var_value', ClassNotPublicManager::readProperty($testClass, 'private_static_var'));
        ClassNotPublicManager::writeProperty($testClass, 'private_static_var', '222');
        self::assertEquals('222', ClassNotPublicManager::readProperty($testClass, 'private_static_var'));

        $f_t1 = 'С111';
        self::assertEquals("private_static_function_return_[С111]_D222", ClassNotPublicManager::callMethod([$testClass, 'private_static_function'], [&$f_t1, 'D222']));
        self::assertEquals('[С111]', $f_t1);
    }

    /**
     * Создает объект для тестирования взаимодействия с непубличными элементами
     *
     * @return object
     *
     * @psalm-suppress MoreSpecificReturnType Псалм не понимает, что мы возвращаем имя класса, так как мы класс создаем динамически
     * @psalm-suppress LessSpecificReturnStatement Псалм не понимает, что мы возвращаем имя класса, так как мы класс создаем динамически
     */
    private function createObject(string $set_var = 'null'): object
    {
        $className = '___Test_Class_Name_' . uniqid() . '___';

        $classInner = <<<'CODE'
            private const PRIVATE_CONST = 'private_const_value';

            public string $public_var = 'public_var_value';
            public string $construct_var = 'construct_var_not_set';
            public string $construct_argument_var = 'construct_argument_var_not_set';

            private string $private_var = 'private_var_value';
            private static string $private_static_var = 'private_static_var_value';

            public function __construct(string $set_var = 'null')
            {
                $this->construct_var = 'construct_var_set_ok';
                $this->construct_argument_var = "construct_argument_var_set_{$set_var}";
            }

            private function private_function(string &$t1, string $t2): string
            {
                $t1 = "[{$t1}]";
                return "private_function_return_{$t1}_{$t2}";
            }
            private static function private_static_function(string &$t1, string $t2): string
            {
                $t1 = "[{$t1}]";
                return "private_static_function_return_{$t1}_{$t2}";
            }
        CODE;

        eval("class {$className} {{$classInner}}");

        return new $className($set_var);
    }

    /**
     * Создаст класс с статическими элементами
     *
     * @return class-string Вернет имя созданного класса
     *
     * @psalm-suppress MoreSpecificReturnType Псалм не понимает, что мы возвращаем имя класса, так как мы класс создаем динамически
     * @psalm-suppress LessSpecificReturnStatement Псалм не понимает, что мы возвращаем имя класса, так как мы класс создаем динамически
     */
    private function createStaticClass(): string
    {
        $className = '___Test_Class_Name_' . uniqid() . '___';

        $classInner = <<<'CODE'
            private const PRIVATE_CONST = 'private_const_value';

            private static string $private_static_var = 'private_static_var_value';

            private static function private_static_function(string &$t1, string $t2): string
            {
                $t1 = "[{$t1}]";
                return "private_static_function_return_{$t1}_{$t2}";
            }
            
            public function __construct()
            {
                throw new \RuntimeException('TEST FAIL');            
            }
        CODE;

        eval("final class {$className} {{$classInner}}");

        return $className;
    }
}
