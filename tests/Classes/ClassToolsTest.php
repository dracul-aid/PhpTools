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
use DraculAid\PhpTools\Classes\ClassTools;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see ClassTools}
 *
 * @run php tests/run.php tests/Classes/ClassToolsTest.php
 */
class ClassToolsTest extends TestCase
{
    /**
     * Test for {@see ClassTools::isLoad()}
     */
    public function testIsLoad(): void
    {
        $this->createTestTraitAndEnum($traitName, $enumName);

        // * * *

        self::assertTrue(ClassTools::isLoad(\stdClass::class));
        self::assertTrue(ClassTools::isLoad(static::class));

        self::assertTrue(ClassTools::isLoad(\Throwable::class));
        self::assertTrue(ClassTools::isLoad(Test::class));

        /**
         * @psalm-suppress PossiblyNullArgument Пслам не умеет нормально работать с "ссылками" (а эта переменная получает значение ппо ссылке выше
         * @psalm-suppress ArgumentTypeCoercion Пслам не умеет нормально работать с "ссылками" (а эта переменная получает значение ппо ссылке выше
         */
        self::assertTrue(ClassTools::isLoad($traitName));
        self::assertTrue(ClassTools::isLoad($enumName));

        /** @psalm-suppress UndefinedClass Мы знаем что такой класс не существует, проверяем, как функция отреагирует на такой кейс */
        self::assertFalse(ClassTools::isInternal(_______NoClassName_______::class));
    }

    /**
     * Test for {@see ClassTools::isInternal()}
     */
    public function testIsInternal(): void
    {
        self::assertTrue(ClassTools::isInternal(\stdClass::class));
        self::assertFalse(ClassTools::isInternal(static::class));
    }

    /**
     * Test for {@see ClassTools::isAsArray()}
     */
    public function testIsAsArray(): void
    {
        self::assertFalse(ClassTools::isAsArray(\stdClass::class));
        self::assertFalse(ClassTools::isAsArray(new \stdClass));

        self::assertTrue(ClassTools::isAsArray(\ArrayObject::class));
        self::assertTrue(ClassTools::isAsArray(new \ArrayObject([])));
    }

    /**
     * Test for {@see ClassTools::getNamespace()}
     * Test for {@see ClassTools::getNameWithoutNamespace()}
     * Test for {@see ClassTools::getNameAndNamespace()}
     */
    public function testGetNameOrNamespace(): void
    {
        self::assertEquals('catalog\\subcatalog', ClassTools::getNamespace('catalog\\subcatalog\\class'));
        self::assertEquals('catalog', ClassTools::getNamespace('catalog\\class'));
        self::assertEquals('', ClassTools::getNamespace('class'));

        // * * *

        self::assertEquals('class', ClassTools::getNameWithoutNamespace('catalog\\subcatalog\\class'));
        self::assertEquals('class', ClassTools::getNameWithoutNamespace('catalog\\class'));
        self::assertEquals('class', ClassTools::getNameWithoutNamespace('class'));

        // * * *

        [$namespace, $name] = ClassTools::getNameAndNamespace('catalog\\subcatalog\\class');
        self::assertEquals('catalog\\subcatalog', $namespace);
        self::assertEquals('class', $name);

        [$namespace, $name] = ClassTools::getNameAndNamespace('catalog\\class');
        self::assertEquals('catalog', $namespace);
        self::assertEquals('class', $name);

        [$namespace, $name] = ClassTools::getNameAndNamespace('class');
        self::assertEquals('', $namespace);
        self::assertEquals('class', $name);
    }

    /**
     * Test for {@see ClassTools::createObject()}
     */
    public function testCreateObjectWithoutConstructor(): void
    {
        $className = get_class($this->createObject());
        $testObject = ClassTools::createObject($className, false, ['public_var' => '123', 'private_var' => 'ABC']);

        self::assertEquals("123", $testObject->public_var);
        self::assertEquals("construct_var_not_set", $testObject->construct_var);
        self::assertEquals("construct_argument_var_not_set", $testObject->construct_argument_var);
        self::assertEquals("ABC", ClassNotPublicManager::getInstanceFor($testObject)->get('private_var'));
    }

    /**
     * Test for {@see ClassTools::createObject()}
     */
    public function testCreateObjectWithConstructor(): void
    {
        $className = get_class($this->createObject());
        $testObject = ClassTools::createObject($className, ['XXX'], ['public_var' => '123', 'private_var' => 'ABC']);

        self::assertEquals("123", $testObject->public_var);
        self::assertEquals("construct_var_set_ok", $testObject->construct_var);
        self::assertEquals("construct_argument_var_set_XXX", $testObject->construct_argument_var);
        self::assertEquals("ABC", ClassNotPublicManager::getInstanceFor($testObject)->get('private_var'));
    }

    /**
     * @param string|null $traitName
     * @param string|null $enumName
     * @return void
     */
    private function createTestTraitAndEnum(null|string &$traitName, null|string &$enumName): void
    {
        $traitName = '___test_trait_name_' . uniqid() . '___';
        $enumName = '___test_enum_name_' . uniqid() . '___';

        eval("trait {$traitName} {}");
        
        eval("enum {$enumName} {}");
    }

    /**
     * Создает объект для тестирования взаимодействия с непубличными элементами
     *
     * @return object
     */
    private function createObject(string $set_var = 'null'): object
    {
        $className = '___Test_Class_Name_' . uniqid() . '___';

        $classInner = <<<'CODE'
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
        CODE;

        eval("class {$className} {{$classInner}}");

        /** @psalm-suppress InvalidStringClass Тут и правда имя класса */
        return new $className($set_var);
    }
}
