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
     * Тестирование режима работы "прокси-объекта"
     *
     * Test for {@see ClassNotPublicManager::constant()}
     * Test for {@see ClassNotPublicManager::get()}
     * Test for {@see ClassNotPublicManager::getStatic()}
     * Test for {@see ClassNotPublicManager::set()}
     * Test for {@see ClassNotPublicManager::setStatic()}
     * Test for {@see ClassNotPublicManager::call()}
     * Test for {@see ClassNotPublicManager::callStatic()}
     *
     * @psalm-suppress PossiblyNullFunctionCall
     */
    public function testObject(): void
    {
        // * * * Чтение из объекта

        [$testObject, $testClass, $testParent] = $this->createObject();

        $testNotPublic = ClassNotPublicManager::getInstanceFor($testObject);

        // выполнение произвольного кода
        // (ниже по коду свойства меняют значения, поэтому эту проверку лучше всего оставлять в начале)
        self::assertEquals('private_var_value---double_private_var_value', $testNotPublic->run(function () {return "{$this->private_var}---{$this->parent_private_var}";}));
        self::assertEquals('only_parent_var_value---parent_private_var_value', $testNotPublic->run(function () {return "{$this->only_parent_var}---{$this->parent_private_var}";}, $testParent));
        self::assertEquals('private_var_value---double_private_var_value', $testNotPublic->run(function () {return "{$this->private_var}---{$this->parent_private_var}";}, $testClass));

        // чтение константы
        self::assertEquals('private_const_value', $testNotPublic->constant('PRIVATE_CONST'));
        self::assertEquals('parent_private_const_value', $testNotPublic->constant('PARENT_PRIVATE_CONST', $testParent));
        self::assertEquals('double_private_const_value', $testNotPublic->constant('PARENT_PRIVATE_CONST', $testClass));

        // чтение свойств
        self::assertEquals('private_var_value', $testNotPublic->get('private_var'));
        self::assertEquals('parent_private_var_value', $testNotPublic->get('parent_private_var', $testParent));
        self::assertEquals('double_private_var_value', $testNotPublic->get('parent_private_var', $testClass));

        // чтение статических свойств
        self::assertEquals('private_static_var_value', $testNotPublic->getStatic('private_static_var'));
        self::assertEquals('parent_private_static_var_value', $testNotPublic->getStatic('parent_private_static_var', $testParent));
        self::assertEquals('double_private_static_var_value', $testNotPublic->getStatic('parent_private_static_var', $testClass));

        // запись свойств
        $testNotPublic->set('private_var', '111')->setStatic('private_static_var', '222');
        self::assertEquals('111', $testNotPublic->get('private_var'));
        self::assertEquals('222', $testNotPublic->getStatic('private_static_var'));
        // -
        $testNotPublic->set('parent_private_var', 'parent_111', $testParent)->setStatic('parent_private_static_var', 'parent_222', $testParent);
        self::assertEquals('parent_111', $testNotPublic->get('parent_private_var', $testParent));
        self::assertEquals('parent_222', $testNotPublic->getStatic('parent_private_static_var', $testParent));
        // -
        $testNotPublic->set('parent_private_var', 'double_111', $testClass)->setStatic('parent_private_static_var', 'double_222', $testClass);
        self::assertEquals('double_111', $testNotPublic->get('parent_private_var', $testClass));
        self::assertEquals('double_222', $testNotPublic->getStatic('parent_private_static_var', $testClass));

        // запись свойств (массовая)
        $testNotPublic->set(['private_var' => 'AAA'])->setStatic(['private_static_var' => 'BBB']);
        self::assertEquals('AAA', $testNotPublic->get('private_var'));
        self::assertEquals('BBB', $testNotPublic->getStatic('private_static_var'));

        // вызов метода
        $f_t1 = 'A111';
        self::assertEquals("private_function_return_[A111]_B222", $testNotPublic->call('private_function', [&$f_t1, 'B222']));
        self::assertEquals("[A111]", $f_t1);
        // -
        $f_t1 = 'С111';
        self::assertEquals("parent_private_function_return_[С111]_D222", $testNotPublic->call('parent_private_function', [&$f_t1, 'D222'], $testParent));
        self::assertEquals('[С111]', $f_t1);
        // -
        $f_t1 = 'С111';
        self::assertEquals("double_private_function_return_[С111]_D222", $testNotPublic->call('parent_private_function', [&$f_t1, 'D222'], $testClass));
        self::assertEquals('[С111]', $f_t1);

        // вызов статического метода
        $f_t1 = 'С111';
        self::assertEquals("private_static_function_return_[С111]_D222", $testNotPublic->callStatic('private_static_function', [&$f_t1, 'D222']));
        self::assertEquals('[С111]', $f_t1);
        // -
        $f_t1 = 'С111';
        self::assertEquals("parent_private_static_function_return_[С111]_D222", $testNotPublic->callStatic('parent_private_static_function', [&$f_t1, 'D222'], $testParent));
        self::assertEquals('[С111]', $f_t1);
        // -
        $f_t1 = 'С111';
        self::assertEquals("double_private_static_function_return_[С111]_D222", $testNotPublic->callStatic('parent_private_static_function', [&$f_t1, 'D222'], $testClass));
        self::assertEquals('[С111]', $f_t1);

        // * * * Чтение из класса

        [$testClass, $testParent] = $this->createStaticClass();

        $testNotPublic = ClassNotPublicManager::getInstanceFor($testClass);

        // выполнение произвольного кода
        // (ниже по коду свойства меняют значения, поэтому эту проверку лучше всего оставлять в начале)
        self::assertEquals('private_static_var_value---double_private_static_var_value', $testNotPublic->run(function () {return self::$private_static_var . '---' . self::$parent_private_static_var;}));
        self::assertEquals('only_parent_static_var_value---parent_private_static_var_value', $testNotPublic->run(function () {return self::$only_parent_static_var . '---' . self::$parent_private_static_var;}, $testParent));
        self::assertEquals('private_static_var_value---double_private_static_var_value', $testNotPublic->run(function () {return self::$private_static_var . '---' . self::$parent_private_static_var;}, $testClass));

        // чтение константы
        self::assertEquals('private_const_value', $testNotPublic->constant('PRIVATE_CONST'));
        self::assertEquals('parent_private_const_value', $testNotPublic->constant('PARENT_PRIVATE_CONST', $testParent));
        self::assertEquals('double_private_const_value', $testNotPublic->constant('PARENT_PRIVATE_CONST', $testClass));

        // чтение и запись свойств
        self::assertEquals('private_static_var_value', $testNotPublic->getStatic('private_static_var'));
        $testNotPublic->setStatic('private_static_var', '222');
        self::assertEquals('222', $testNotPublic->getStatic('private_static_var'));
        // -
        self::assertEquals('parent_private_static_var_value', $testNotPublic->getStatic('parent_private_static_var', $testParent));
        $testNotPublic->setStatic('parent_private_static_var', 'parent_222', $testParent);
        self::assertEquals('parent_222', $testNotPublic->getStatic('parent_private_static_var', $testParent));
        // -
        self::assertEquals('double_private_static_var_value', $testNotPublic->getStatic('parent_private_static_var', $testClass));
        $testNotPublic->setStatic('parent_private_static_var', 'double_222', $testClass);
        self::assertEquals('double_222', $testNotPublic->getStatic('parent_private_static_var', $testClass));

        // вызов статического метода
        $f_t1 = 'С111';
        self::assertEquals("private_static_function_return_[С111]_D222", $testNotPublic->callStatic('private_static_function', [&$f_t1, 'D222']));
        self::assertEquals('[С111]', $f_t1);
        // -
        $f_t1 = 'С111';
        self::assertEquals("parent_private_static_function_return_[С111]_D222", $testNotPublic->callStatic('parent_private_static_function', [&$f_t1, 'D222'], $testParent));
        self::assertEquals('[С111]', $f_t1);
        // -
        $f_t1 = 'С111';
        self::assertEquals("double_private_static_function_return_[С111]_D222", $testNotPublic->callStatic('parent_private_static_function', [&$f_t1, 'D222'], $testClass));
        self::assertEquals('[С111]', $f_t1);

    }

    /**
     * Тестирование упрощенного режима работы, без прокси-объекта
     *
     * Test for {@see ClassNotPublicManager::readConstant()}
     * Test for {@see ClassNotPublicManager::readProperty()}
     * Test for {@see ClassNotPublicManager::writeProperty()}
     * Test for {@see ClassNotPublicManager::callMethod()}
     *
     * @psalm-suppress UndefinedThisPropertyFetch
     * @psalm-suppress PossiblyNullFunctionCall
     */
    public function testEasyObject(): void
    {
        // * * * Чтение из объекта

        [$testObject, $testClass, $testParent] = $this->createObject();

        // выполнение произвольного кода
        // (ниже по коду свойства меняют значения, поэтому эту проверку лучше всего оставлять в начале)
        self::assertEquals('private_var_value---double_private_var_value', ClassNotPublicManager::execute($testObject, function () {return "{$this->private_var}---{$this->parent_private_var}";}));
        self::assertEquals('only_parent_var_value---parent_private_var_value', ClassNotPublicManager::execute($testObject, function () {return "{$this->only_parent_var}---{$this->parent_private_var}";}, $testParent));
        self::assertEquals('private_var_value---double_private_var_value', ClassNotPublicManager::execute($testObject, function () {return "{$this->private_var}---{$this->parent_private_var}";}, $testClass));

        // чтение констант класса
        self::assertEquals('private_const_value', ClassNotPublicManager::readConstant($testObject, 'PRIVATE_CONST'));

        // чтение констант объекта
        self::assertEquals('private_const_value', ClassNotPublicManager::readConstant($testObject, 'PRIVATE_CONST'));
        self::assertEquals('parent_private_const_value', ClassNotPublicManager::readConstant($testObject, 'PARENT_PRIVATE_CONST', $testParent));
        self::assertEquals('double_private_const_value', ClassNotPublicManager::readConstant($testObject, 'PARENT_PRIVATE_CONST', $testClass));

        // чтение статических свойств
        self::assertEquals('private_static_var_value', ClassNotPublicManager::readProperty($testClass, 'private_static_var'));
        self::assertEquals('parent_private_static_var_value', ClassNotPublicManager::readProperty($testClass, 'parent_private_static_var', $testParent));
        self::assertEquals('double_private_static_var_value', ClassNotPublicManager::readProperty($testClass, 'parent_private_static_var', $testClass));

        // чтение свойств
        self::assertEquals('private_var_value', ClassNotPublicManager::readProperty($testObject,'private_var'));
        self::assertEquals('parent_private_var_value', ClassNotPublicManager::readProperty($testObject, 'parent_private_var', $testParent));
        self::assertEquals('double_private_var_value', ClassNotPublicManager::readProperty($testObject, 'parent_private_var', $testClass));

        // запись статических свойств
        ClassNotPublicManager::writeProperty($testClass, 'private_static_var', '222');
        self::assertEquals('222', ClassNotPublicManager::readProperty($testClass, 'private_static_var'));
        ClassNotPublicManager::writeProperty($testClass, ['private_static_var' => 'BBB']);
        self::assertEquals('BBB', ClassNotPublicManager::readProperty($testClass, 'private_static_var'));
        ClassNotPublicManager::writeProperty($testClass, 'parent_private_static_var', 'parent_222', $testParent);
        self::assertEquals('parent_222', ClassNotPublicManager::readProperty($testClass, 'parent_private_static_var', $testParent));
        ClassNotPublicManager::writeProperty($testClass, 'parent_private_static_var', 'double_222', $testClass);
        self::assertEquals('double_222', ClassNotPublicManager::readProperty($testClass, 'parent_private_static_var', $testClass));

        // запись свойств
        ClassNotPublicManager::writeProperty($testObject, 'private_var', '111');
        self::assertEquals('111', ClassNotPublicManager::readProperty($testObject,'private_var'));
        ClassNotPublicManager::writeProperty($testObject, ['private_var' => 'AAA']);
        self::assertEquals('AAA', ClassNotPublicManager::readProperty($testObject,'private_var'));
        ClassNotPublicManager::writeProperty($testObject, 'parent_private_var', 'parent_111', $testParent);
        self::assertEquals('parent_111', ClassNotPublicManager::readProperty($testObject, 'parent_private_var', $testParent));
        ClassNotPublicManager::writeProperty($testObject, 'parent_private_var', 'double_111', $testClass);
        self::assertEquals('double_111', ClassNotPublicManager::readProperty($testObject, 'parent_private_var', $testClass));

        // Вызов статических методов
        $f_t1 = 'С111';
        self::assertEquals("private_static_function_return_[С111]_D222", ClassNotPublicManager::callMethod([$testObject, 'private_static_function'], [&$f_t1, 'D222']));
        self::assertEquals('[С111]', $f_t1);
        // -
        $f_t1 = 'С111';
        self::assertEquals("parent_private_static_function_return_[С111]_D222", ClassNotPublicManager::callMethod([$testObject, 'parent_private_static_function'], [&$f_t1, 'D222'], $testParent));
        self::assertEquals('[С111]', $f_t1);
        // -
        $f_t1 = 'С111';
        self::assertEquals("double_private_static_function_return_[С111]_D222", ClassNotPublicManager::callMethod([$testObject, 'parent_private_static_function'], [&$f_t1, 'D222'], $testClass));
        self::assertEquals('[С111]', $f_t1);

        // Вызов методов
        $f_t1 = 'A111';
        self::assertEquals("private_function_return_[A111]_B222", ClassNotPublicManager::callMethod([$testObject, 'private_function'], [&$f_t1, 'B222']));
        self::assertEquals("[A111]", $f_t1);
        // -
        $f_t1 = 'С111';
        self::assertEquals("parent_private_function_return_[С111]_D222", ClassNotPublicManager::callMethod([$testObject, 'parent_private_function'], [&$f_t1, 'D222'], $testParent));
        self::assertEquals('[С111]', $f_t1);
        // -
        $f_t1 = 'С111';
        self::assertEquals("double_private_function_return_[С111]_D222", ClassNotPublicManager::callMethod([$testObject, 'parent_private_function'], [&$f_t1, 'D222'], $testClass));
        self::assertEquals('[С111]', $f_t1);


        // * * * Чтение из класса

        [$testClass, $testParent] = $this->createStaticClass();

        // выполнение произвольного кода
        // (ниже по коду свойства меняют значения, поэтому эту проверку лучше всего оставлять в начале)
        self::assertEquals('private_static_var_value---double_private_static_var_value', ClassNotPublicManager::execute($testClass, function () {return self::$private_static_var . '---' . self::$parent_private_static_var;}));
        self::assertEquals('only_parent_static_var_value---parent_private_static_var_value', ClassNotPublicManager::execute($testClass, function () {return self::$only_parent_static_var . '---' . self::$parent_private_static_var;}, $testParent));
        self::assertEquals('private_static_var_value---double_private_static_var_value', ClassNotPublicManager::execute($testClass, function () {return self::$private_static_var . '---' . self::$parent_private_static_var;}, $testClass));

        // чтение констант
        self::assertEquals('private_const_value', ClassNotPublicManager::readConstant($testClass, 'PRIVATE_CONST'));
        self::assertEquals('parent_private_const_value', ClassNotPublicManager::readConstant($testClass, 'PARENT_PRIVATE_CONST', $testParent));
        self::assertEquals('double_private_const_value', ClassNotPublicManager::readConstant($testClass, 'PARENT_PRIVATE_CONST', $testClass));

        // чтение и запись статического свойства
        self::assertEquals('private_static_var_value', ClassNotPublicManager::readProperty($testClass, 'private_static_var'));
        ClassNotPublicManager::writeProperty($testClass, 'private_static_var', '222');
        self::assertEquals('222', ClassNotPublicManager::readProperty($testClass, 'private_static_var'));
        // -
        self::assertEquals('parent_private_static_var_value', ClassNotPublicManager::readProperty($testClass, 'parent_private_static_var', $testParent));
        ClassNotPublicManager::writeProperty($testClass, 'parent_private_static_var', 'parent_222', $testParent);
        self::assertEquals('parent_222', ClassNotPublicManager::readProperty($testClass, 'parent_private_static_var', $testParent));
        // -
        self::assertEquals('double_private_static_var_value', ClassNotPublicManager::readProperty($testClass, 'parent_private_static_var', $testClass));
        ClassNotPublicManager::writeProperty($testClass, 'parent_private_static_var', 'double_222', $testClass);
        self::assertEquals('double_222', ClassNotPublicManager::readProperty($testClass, 'parent_private_static_var', $testClass));

        $f_t1 = 'С111';
        self::assertEquals("private_static_function_return_[С111]_D222", ClassNotPublicManager::callMethod([$testClass, 'private_static_function'], [&$f_t1, 'D222']));
        self::assertEquals('[С111]', $f_t1);
        // -
        $f_t1 = 'С111';
        self::assertEquals("parent_private_static_function_return_[С111]_D222", ClassNotPublicManager::callMethod([$testClass, 'parent_private_static_function'], [&$f_t1, 'D222'], $testParent));
        self::assertEquals('[С111]', $f_t1);
        // -
        $f_t1 = 'С111';
        self::assertEquals("double_private_static_function_return_[С111]_D222", ClassNotPublicManager::callMethod([$testClass, 'parent_private_static_function'], [&$f_t1, 'D222'], $testClass));
        self::assertEquals('[С111]', $f_t1);
    }

    /**
     * Создает объект для тестирования взаимодействия с непубличными элементами
     *
     * @return array{0: object, 1: class-string, 2: class-string} Вернет массив c
     *                                                            - 0: тестовый объект
     *                                                            - 1: имя класса тестового объекта
     *                                                            - 2: имя класса-родителя тестового объекта
     *
     * @psalm-suppress MoreSpecificReturnType Псалм не понимает, что мы возвращаем имя класса, так как мы класс создаем динамически
     * @psalm-suppress LessSpecificReturnStatement Псалм не понимает, что мы возвращаем имя класса, так как мы класс создаем динамически
     */
    private function createObject(): array
    {
        // * * * Тестовый класс-родитель (нужен, для тестирования с явно указанной областью видимости)

        /** @var class-string $parentName Имя тестового класса */
        $parentName = '___Test_Parent_Name_' . uniqid() . '___';

        /** Код тестового класса-родителя */
        $parentInner = <<<'CODE'
        
            private const PARENT_PRIVATE_CONST = 'parent_private_const_value';
        
            private static string $only_parent_static_var = 'only_parent_var_static_value';
            private string $only_parent_var = 'only_parent_var_value';
            
            private string $parent_private_var = 'parent_private_var_value';
            private static string $parent_private_static_var = 'parent_private_static_var_value';
            
            private static function parent_private_function(string &$t1, string $t2): string
            {
                $t1 = "[{$t1}]";
                return "parent_private_function_return_{$t1}_{$t2}";
            }
            private static function parent_private_static_function(string &$t1, string $t2): string
            {
                $t1 = "[{$t1}]";
                return "parent_private_static_function_return_{$t1}_{$t2}";
            }
        CODE;

        eval("class {$parentName} {{$parentInner}}");

        // * * * Тестовый класс

        /** @var class-string $className Имя тестового класса */
        $className = '___Test_Class_Name_' . uniqid() . '___';

        /** Код тестового класса */
        $classInner = <<<'CODE'
            private const PRIVATE_CONST = 'private_const_value';
            private const PARENT_PRIVATE_CONST = 'double_private_const_value';

            public string $public_var = 'public_var_value';

            private string $private_var = 'private_var_value';
            private string $parent_private_var = 'double_private_var_value';
            
            private static string $private_static_var = 'private_static_var_value';
            private static string $parent_private_static_var = 'double_private_static_var_value';

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
            
            private static function parent_private_function(string &$t1, string $t2): string
            {
                $t1 = "[{$t1}]";
                return "double_private_function_return_{$t1}_{$t2}";
            }
            private static function parent_private_static_function(string &$t1, string $t2): string
            {
                $t1 = "[{$t1}]";
                return "double_private_static_function_return_{$t1}_{$t2}";
            }
        CODE;

        // * * * Создаем тестовый класс

        eval("class {$className} extends {$parentName} {{$classInner}}");

        /** @psalm-suppress InvalidStringClass тут строка и правда является именем класса */
        return [new $className(), $className, $parentName];
    }

    /**
     * Создаст класс с статическими элементами
     *
     * @return array{0:class-string, 1:class-string} Вернет имя созданного класса и имя класса родителя
     *
     * @psalm-suppress MoreSpecificReturnType Псалм не понимает, что мы возвращаем имя класса, так как мы класс создаем динамически
     * @psalm-suppress LessSpecificReturnStatement Псалм не понимает, что мы возвращаем имя класса, так как мы класс создаем динамически
     */
    private function createStaticClass(): array
    {
        // * * * Тестовый класс-родитель (нужен, для тестирования с явно указанной областью видимости)

        /** @var class-string $parentName Имя тестового класса */
        $parentName = '___Test_Parent_Name_' . uniqid() . '___';

        /** Код тестового класса-родителя */
        $parentInner = <<<'CODE'
            private const PARENT_PRIVATE_CONST = 'parent_private_const_value';
        
            private static string $only_parent_static_var = 'only_parent_static_var_value';
            private string $only_parent_var = 'only_parent_var_value';
            
            private static string $parent_private_static_var = 'parent_private_static_var_value';
            
            private static function parent_private_static_function(string &$t1, string $t2): string
            {
                $t1 = "[{$t1}]";
                return "parent_private_static_function_return_{$t1}_{$t2}";
            }
        CODE;

        eval("class {$parentName} {{$parentInner}}");

        // * * * Тестовый класс

        /** @var class-string $className Имя тестового класса */
        $className = '___Test_Class_Name_' . uniqid() . '___';

        /** Код тестового класса */
        $classInner = <<<'CODE'
            private const PRIVATE_CONST = 'private_const_value';
            private const PARENT_PRIVATE_CONST = 'double_private_const_value';

            private static string $private_static_var = 'private_static_var_value';
            private static string $parent_private_static_var = 'double_private_static_var_value';

            private static function private_static_function(string &$t1, string $t2): string
            {
                $t1 = "[{$t1}]";
                return "private_static_function_return_{$t1}_{$t2}";
            }
            
            private static function parent_private_static_function(string &$t1, string $t2): string
            {
                $t1 = "[{$t1}]";
                return "double_private_static_function_return_{$t1}_{$t2}";
            }
            
            public function __construct()
            {
                // нам важно убедится, что мы создаем объект для взаимодействия с элементами класса без вызова конструктора
                throw new \RuntimeException('TEST FAIL');            
            }
        CODE;

        // * * * Создаем тестовый класс

        eval("class {$className} extends {$parentName} {{$classInner}}");

        return [$className, $parentName];
    }
}
